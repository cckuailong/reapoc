<?php
/**
 * Some of the code in this library was borrowed from the TGM Updater class by Thomas Griffin. (https://github.com/thomasgriffin/TGM-Updater)
 */

/**
 * Setup plugins api filters
 *
 * @since 1.8.5
 */
function pmpro_setupAddonUpdateInfo() {
	add_filter( 'plugins_api', 'pmpro_plugins_api', 10, 3 );
	add_filter( 'pre_set_site_transient_update_plugins', 'pmpro_update_plugins_filter' );
	add_filter( 'http_request_args', 'pmpro_http_request_args_for_addons', 10, 2 );
	add_action( 'update_option_pmpro_license_key', 'pmpro_reset_update_plugins_cache', 10, 2 );
}
add_action( 'init', 'pmpro_setupAddonUpdateInfo' );

/**
 * Get addon information from PMPro server.
 *
 * @since  1.8.5
 */
function pmpro_getAddons() {
	// check if forcing a pull from the server
	$addons = get_option( 'pmpro_addons', array() );
	$addons_timestamp = get_option( 'pmpro_addons_timestamp', 0 );

	// if no addons locally, we need to hit the server
	if ( empty( $addons ) || ! empty( $_REQUEST['force-check'] ) || current_time( 'timestamp' ) > $addons_timestamp + 86400 ) {
		/**
		 * Filter to change the timeout for this wp_remote_get() request.
		 *
		 * @since 1.8.5.1
		 *
		 * @param int $timeout The number of seconds before the request times out
		 */
		$timeout = apply_filters( 'pmpro_get_addons_timeout', 5 );

		// get em
		$remote_addons = wp_remote_get( PMPRO_LICENSE_SERVER . 'addons/', $timeout );

		// make sure we have at least an array to pass back
		if ( empty( $addons ) ) {
			$addons = array();
		}

		// test response
		if ( is_wp_error( $remote_addons ) ) {
			// error
			pmpro_setMessage( 'Could not connect to the PMPro License Server to update addon information. Try again later.', 'error' );
		} elseif ( ! empty( $remote_addons ) && $remote_addons['response']['code'] == 200 ) {
			// update addons in cache
			$addons = json_decode( wp_remote_retrieve_body( $remote_addons ), true );
			delete_option( 'pmpro_addons' );
			add_option( 'pmpro_addons', $addons, null, 'no' );
		}

		// save timestamp of last update
		delete_option( 'pmpro_addons_timestamp' );
		add_option( 'pmpro_addons_timestamp', current_time( 'timestamp' ), null, 'no' );
	}

	return $addons;
}

/**
 * Find a PMPro addon by slug.
 *
 * @since 1.8.5
 *
 * @param object $slug  The identifying slug for the addon (typically the directory name)
 * @return object $addon containing plugin information or false if not found
 */
function pmpro_getAddonBySlug( $slug ) {
	$addons = pmpro_getAddons();

	if ( empty( $addons ) ) {
		return false;
	}

	foreach ( $addons as $addon ) {
		if ( $addon['Slug'] == $slug ) {
			return $addon;
		}
	}

	return false;
}

/**
 * Infuse plugin update details when WordPress runs its update checker.
 *
 * @since 1.8.5
 *
 * @param object $value  The WordPress update object.
 * @return object $value Amended WordPress update object on success, default if object is empty.
 */
function pmpro_update_plugins_filter( $value ) {

	// If no update object exists, return early.
	if ( empty( $value ) ) {
		return $value;
	}

	// get addon information
	$addons = pmpro_getAddons();

	// no addons?
	if ( empty( $addons ) ) {
		return $value;
	}

	// check addons
	foreach ( $addons as $addon ) {
		// skip wordpress.org plugins
		if ( empty( $addon['License'] ) || $addon['License'] == 'wordpress.org' ) {
			continue;
		}

		// get data for plugin
		$plugin_file = $addon['Slug'] . '/' . $addon['Slug'] . '.php';
		$plugin_file_abs = WP_PLUGIN_DIR . '/' . $plugin_file;

		// couldn't find plugin, skip
		if ( ! file_exists( $plugin_file_abs ) ) {
			continue;
		} else {
			$plugin_data = get_plugin_data( $plugin_file_abs, false, true );
		}

		// compare versions
		if ( version_compare( $plugin_data['Version'], $addon['Version'], '<' ) ) {
			$value->response[ $plugin_file ] = pmpro_getPluginAPIObjectFromAddon( $addon );
			$value->response[ $plugin_file ]->new_version = $addon['Version'];
		} else {
			$value->no_update[ $plugin_file ] = pmpro_getPluginAPIObjectFromAddon( $addon );
		}
	}

	// Return the update object.
	return $value;
}

/**
 * Disables SSL verification to prevent download package failures.
 *
 * @since 1.8.5
 *
 * @param array  $args  Array of request args.
 * @param string $url  The URL to be pinged.
 * @return array $args Amended array of request args.
 */
function pmpro_http_request_args_for_addons( $args, $url ) {
	// If this is an SSL request and we are performing an upgrade routine, disable SSL verification.
	if ( strpos( $url, 'https://' ) !== false && strpos( $url, PMPRO_LICENSE_SERVER ) !== false && strpos( $url, 'download' ) !== false ) {
		$args['sslverify'] = false;
	}

	return $args;
}

/**
 * Setup plugin updaters
 *
 * @since  1.8.5
 */
function pmpro_plugins_api( $api, $action = '', $args = null ) {
	// Not even looking for plugin information? Or not given slug?
	if ( 'plugin_information' != $action || empty( $args->slug ) ) {
		return $api;
	}

	// get addon information
	$addon = pmpro_getAddonBySlug( $args->slug );

	// no addons?
	if ( empty( $addon ) ) {
		return $api;
	}

	// handled by wordpress.org?
	if ( empty( $addon['License'] ) || $addon['License'] == 'wordpress.org' ) {
		return $api;
	}

	// Create a new stdClass object and populate it with our plugin information.
	$api = pmpro_getPluginAPIObjectFromAddon( $addon );
	return $api;
}

/**
 * Convert the format from the pmpro_getAddons function to that needed for plugins_api
 *
 * @since  1.8.5
 */
function pmpro_getPluginAPIObjectFromAddon( $addon ) {
	$api                        = new stdClass();

	if ( empty( $addon ) ) {
		return $api;
	}

	// add info
	$api->name                  = isset( $addon['Name'] ) ? $addon['Name'] : '';
	$api->slug                  = isset( $addon['Slug'] ) ? $addon['Slug'] : '';
	$api->plugin                = isset( $addon['plugin'] ) ? $addon['plugin'] : '';
	$api->version               = isset( $addon['Version'] ) ? $addon['Version'] : '';
	$api->author                = isset( $addon['Author'] ) ? $addon['Author'] : '';
	$api->author_profile        = isset( $addon['AuthorURI'] ) ? $addon['AuthorURI'] : '';
	$api->requires              = isset( $addon['Requires'] ) ? $addon['Requires'] : '';
	$api->tested                = isset( $addon['Tested'] ) ? $addon['Tested'] : '';
	$api->last_updated          = isset( $addon['LastUpdated'] ) ? $addon['LastUpdated'] : '';
	$api->homepage              = isset( $addon['URI'] ) ? $addon['URI'] : '';
	$api->download_link         = isset( $addon['Download'] ) ? $addon['Download'] : '';
	$api->package               = isset( $addon['Download'] ) ? $addon['Download'] : '';

	// add sections
	if ( !empty( $addon['Description'] ) ) {
		$api->sections['description'] = $addon['Description'];
	}
	if ( !empty( $addon['Installation'] ) ) {
		$api->sections['installation'] = $addon['Installation'];
	}
	if ( !empty( $addon['FAQ'] ) ) {
		$api->sections['faq'] = $addon['FAQ'];
	}
	if ( !empty( $addon['Changelog'] ) ) {
		$api->sections['changelog'] = $addon['Changelog'];
	}

	// get license key if one is available
	$key = get_option( 'pmpro_license_key', '' );
	if ( ! empty( $key ) && ! empty( $api->download_link ) ) {
		$api->download_link = add_query_arg( 'key', $key, $api->download_link );
	}
	if ( ! empty( $key ) && ! empty( $api->package ) ) {
		$api->package = add_query_arg( 'key', $key, $api->package );
	}
	if ( empty( $api->upgrade_notice ) && ! pmpro_license_isValid( null, 'plus' ) ) {
		$api->upgrade_notice = __( 'Important: This plugin requires a valid PMPro Plus license key to update.', 'paid-memberships-pro' );
	}

	return $api;
}

/**
 * Force update of plugin update data when the PMPro License key is updated
 *
 * @since 1.8
 *
 * @param array  $args  Array of request args.
 * @param string $url  The URL to be pinged.
 * @return array $args Amended array of request args.
 */
function pmpro_reset_update_plugins_cache( $old_value, $value ) {
	delete_option( 'pmpro_addons_timestamp' );
	delete_site_transient( 'update_themes' );
}

/**
 * Detect when trying to update a PMPro Plus plugin without a valid license key.
 *
 * @since 1.9
 */
function pmpro_admin_init_updating_plugins() {
	// if user can't edit plugins, then WP will catch this later
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	// updating one or more plugins via Dashboard -> Upgrade
	if ( basename( $_SERVER['SCRIPT_NAME'] ) == 'update.php' && ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update-selected' && ! empty( $_REQUEST['plugins'] ) ) {
		// figure out which plugin we are updating
		$plugins = explode( ',', stripslashes( $_GET['plugins'] ) );
		$plugins = array_map( 'urldecode', $plugins );

		// look for addons
		$plus_addons = array();
		$plus_plugins = array();
		foreach ( $plugins as $plugin ) {
			$slug = str_replace( '.php', '', basename( $plugin ) );
			$addon = pmpro_getAddonBySlug( $slug );
			if ( ! empty( $addon ) && $addon['License'] == 'plus' ) {
				$plus_addons[] = $addon['Name'];
				$plus_plugins[] = $plugin;
			}
		}
		unset( $plugin );

		// if Plus addons found, check license key
		if ( ! empty( $plus_plugins ) && ! pmpro_license_isValid( null, 'plus' ) ) {
			// show error
			$msg = __( 'You must have a <a href="https://www.paidmembershipspro.com/pricing/?utm_source=wp-admin&utm_pluginlink=bulkupdate">valid PMPro Plus License Key</a> to update PMPro Plus add ons. The following plugins will not be updated:', 'paid-memberships-pro' );
			echo '<div class="error"><p>' . $msg . ' <strong>' . implode( ', ', $plus_addons ) . '</strong></p></div>';
		}

		// can exit out of this function now
		return;
	}

	// upgrading just one or plugin via an update.php link
	if ( basename( $_SERVER['SCRIPT_NAME'] ) == 'update.php' && ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'upgrade-plugin' && ! empty( $_REQUEST['plugin'] ) ) {
		// figure out which plugin we are updating
		$plugin = urldecode( trim( $_REQUEST['plugin'] ) );

		$slug = str_replace( '.php', '', basename( $plugin ) );
		$addon = pmpro_getAddonBySlug( $slug );
		if ( ! empty( $addon ) && $addon->License == 'plus' && ! pmpro_license_isValid( null, 'plus' ) ) {
			require_once( ABSPATH . 'wp-admin/admin-header.php' );

			echo '<div class="wrap"><h2>' . __( 'Update Plugin' ) . '</h2>';

			$msg = __( 'You must have a <a href="https://www.paidmembershipspro.com/pricing/?utm_source=wp-admin&utm_pluginlink=addon_update">valid PMPro Plus License Key</a> to update PMPro Plus add ons.', 'paid-memberships-pro' );
			echo '<div class="error"><p>' . $msg . '</p></div>';

			echo '<p><a href="' . admin_url( 'admin.php?page=pmpro-addons' ) . '" target="_parent">' . __( 'Return to the PMPro Add Ons page', 'paid-memberships-pro' ) . '</a></p>';

			echo '</div>';

			include( ABSPATH . 'wp-admin/admin-footer.php' );

			// can exit WP now
			exit;
		}
	}

	// updating via AJAX on the plugins page
	if ( basename( $_SERVER['SCRIPT_NAME'] ) == 'admin-ajax.php' && ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update-plugin' && ! empty( $_REQUEST['plugin'] ) ) {
		// figure out which plugin we are updating
		$plugin = urldecode( trim( $_REQUEST['plugin'] ) );

		$slug = str_replace( '.php', '', basename( $plugin ) );
		$addon = pmpro_getAddonBySlug( $slug );
		if ( ! empty( $addon ) && $addon->License == 'plus' && ! pmpro_license_isValid( null, 'plus' ) ) {
			$msg = __( 'You must enter a valid PMPro Plus License Key under Settings > PMPro License to update this add on.', 'paid-memberships-pro' );
			echo '<div class="error"><p>' . $msg . '</p></div>';

			// can exit WP now
			exit;
		}
	}

	/*
        TODO:
		* Check for PMPro Plug plugins
		* If a plus plugin is found, check the PMPro license key
		* If the key is missing or invalid, throw an error
		* Show appropriate footer and exit... maybe do something else to keep plugin update from happening
	*/
}
add_action( 'admin_init', 'pmpro_admin_init_updating_plugins' );
