<?php
    /**
     * Contains all the functions related to updating the plugin from
     * one version to another
     *
     * @package WP RSS Aggregator
     */

    add_action( 'init', 'wprss_version_check' );
	/**
	 * Checks the version number and runs install or update functions if needed.
	 *
	 * @since 2.0
	 */
	function wprss_version_check() {

		// Get the old database version.
		$old_db_version = get_option( 'wprss_db_version' );

		// Get the plugin settings.
		$settings = get_option( 'wprss_settings' );

		// Get the plugin options
		$options = get_option( 'wprss_options' );

		// For fresh installs
		// If there is no old database version and no settings, run the install.
		if ( empty( $old_db_version ) && false === $settings && false === $options ) {
			wprss_install();
		}

		// For version 1.0 to 3.0
		// If there is no old database version and no settings, but only options
		elseif ( empty( $old_db_version ) && false === $settings && !empty( $options ) ) {
			wp_clear_scheduled_hook( 'wprss_generate_hook' );
			wprss_install();
			wprss_migrate();
			wprss_fetch_insert_all_feed_items();
		}

		// For version 1.1 to 3.0
		// If there is no old database version, but only settings and options
		elseif ( empty( $old_db_version ) && !empty( $settings ) && !empty( $options ) ) {
			wp_clear_scheduled_hook( 'wprss_generate_hook' );
			wprss_update();
			wprss_migrate();
			wprss_fetch_insert_all_feed_items();
		}

		// For version 2+ to 3.0
		// We check if wprss_settings option exists, as this only exists prior to version 3.0
		// Settings field changed, and another added
		elseif ( intval( $old_db_version ) < intval( WPRSS_DB_VERSION ) && ( FALSE != get_option( 'wprss_settings' ) ) ) {
			wprss_upgrade_30();
			wprss_update();
			wprss_fetch_insert_all_feed_items();
		}

		// For any future versions where DB changes
		// If the old version is less than the new version, run the update.
		elseif ( intval( $old_db_version ) < intval( WPRSS_DB_VERSION ) ) {
			wprss_update();
			wprss_fetch_insert_all_feed_items();

			// NO FOLLOW CHANGE FIX
			$options = get_option( 'wprss_settings_general' );
			if ( $options['follow_dd'] === __( "No Follow", 'wprss' ) ) {
				$options['follow_dd'] = 'no_follow';
			} elseif ( $options['follow_dd'] === __( "Follow", 'wprss' ) ) {
				$options['follow_dd'] = 'follow';
			}
		}

		// Update to 4.14
		if ( !empty($old_db_version) && intval($old_db_version) < 16 ) {
			// Copy the default template's meta to its settings options
			// In 4.13.x, a bug caused corruption such that the meta data was more up-to-date than the settings
			// This also meant that the options users saw in the Templates edit page did not match what was stored in
			// the database, and what was used by the templates during rendering.
			// This was fixed in 4.14. Since the options that users saw in the Templates Edit page are what they most
			// likely wanted to have be saved in the database, we are here copying the meta over to the settings.
			try {
				$templates = wpra_container()->get('wpra/feeds/templates/collection');
				$default = $templates['default'];
				$id = $default['id'];
				$options = $default['options'];

				$meta = get_post_meta($id, 'wprss_template_options',true);
				foreach ($meta as $k => $v) {
					$options[$k] = $v;
				}

				$default['options'] = $options;
			} catch (Exception $exception) {
				// Fail silently
			}
		}
	}


	/**
	 * Adds the plugin settings on install.
	 *
	 * @since 2.0
	 */
	function wprss_install() {

		// Add the database version setting.
		add_option( 'wprss_db_version', WPRSS_DB_VERSION );

		// Add the default plugin settings.
		add_option( 'wprss_settings_general', wprss_get_default_settings_general() );
	}


	/**
	 * Update settings of plugin to reflect new version
	 *
	 * @since 2.0
	 */
	function wprss_update() {

		// Update the database version setting.
		update_option( 'wprss_db_version', WPRSS_DB_VERSION );
		// Initialize settings
		wprss_settings_initialize();

                // Open Link Behavior Name Fix
                $settings = get_option( 'wprss_settings_general' );

                if( $settings['open_dd'] === 'New window' || $settings['open_dd'] === __( 'New window', 'wprss' ) ){
                    $settings['open_dd'] = 'blank';
                }else if( $settings['open_dd'] === 'Lightbox' || $settings['open_dd'] === __( 'Lightbox', 'wprss' ) ){
                    $settings['open_dd'] = 'lightbox';
                }else if( $settings['open_dd'] === 'Self' || $settings['open_dd'] === __( 'Self', 'wprss' ) ){
                    $settings['open_dd'] = 'self';
                }

		// At version 4.7.5 tracking was disabled
		$settings['tracking'] = '0';
		update_option( 'wprss_settings_general', $settings );
	}

	/**
	 * Initialize settings to default ones if they are not yet set
	 *
	 * @since 3.0
	 */
	function wprss_settings_initialize() {
		// Get the settings from the new field in the database
		$settings = get_option( 'wprss_settings_general' );

		// Get the default plugin settings.
		$default_settings = wprss_get_default_settings_general();

		// Loop through each of the default plugin settings.
		foreach ( $default_settings as $setting_key => $setting_value ) {

			// If the setting didn't previously exist, add the default value to the $settings array.
			if ( ! isset( $settings[ $setting_key ] ) )
				$settings[ $setting_key ] = $setting_value;
		}

		// Update the plugin settings.
		update_option( 'wprss_settings_general', $settings );
	}


	/**
	 * Takes care of cron and DB changes between versions 2+ and 3
	 *
	 * @since 3.0
	 */
	function wprss_upgrade_30() {
		wp_clear_scheduled_hook( 'wprss_fetch_feeds_hook' );

		// Get the settings from the database.
		$settings = get_option( 'wprss_settings' );

		// Put them into our new field
		update_option( 'wprss_settings_general', $settings );

		// Remove old options field, we are now using wprss_settings_general
		delete_option( 'wprss_settings' );
	}


	/**
	 * Migrates the feed sources from the wprss_options field to the wp_posts table (for older versions)
	 *
	 * @since 2.0
	 */
	function wprss_migrate() {

		// Get the plugin options
		$options = get_option( 'wprss_options' );

        $feed_sources = array_chunk( $options, 2 );

        foreach ( $feed_sources as $feed_source ) {
            $feed_title = $feed_source[0];
            $feed_url = $feed_source[1];

            // Create post object
            $feed_item = array(
                'post_title' 	=> $feed_title,
                'post_content' 	=> '',
                'post_status' 	=> 'publish',
                'post_type' 	=> 'wprss_feed'
            );

            $inserted_ID = wp_insert_post( $feed_item, $wp_error );
            // insert post meta
            update_post_meta( $inserted_ID, 'wprss_url', $feed_url );
        }
        // delete unneeded option
        delete_option( 'wprss_options' );
	}


	/**
	 * Returns an array of the default plugin settings. These are only used on initial setup.
	 *
	 * @since 2.0
	 */
	function wprss_get_default_settings_general() {

		// Set up the default plugin settings
		$settings = apply_filters(
			'wprss_default_settings_general',
			array(
				// from version 1.1
				'open_dd' 					=> 'blank',
				'follow_dd' 				=> 'no_follow',

				// from version 2.0
				'feed_limit'				=> 15,

				// from version 3.0
				'date_format'				=> 'Y-m-d',
				'limit_feed_items_db' 		=> 200,
				'cron_interval' 			=> 'hourly',
				'styles_disable'    		=> 0,
				'title_link'				=> 1,
				'title_limit'				=> '',
				'source_enable'     		=> 1,
				'text_preceding_source' 	=> 'Source:',
				'date_enable'				=> 1,
				'text_preceding_date' 		=> 'Published on',

				// from version 3.1
				'limit_feed_items_imported' => 0,

				// from version 3.3
				'custom_feed_url'			=> 'wprss',
				'custom_feed_limit'			=> '',
				'source_link'				=> 0,

				// from version 3.4
				'video_link'				=> 'false',

				// from version 3.8
				'limit_feed_items_age'		=> '',
				'limit_feed_items_age_unit'	=> 'days',

				// from version 4.0.5
				'time_ago_format_enable'    => 0,

				// tracking
				'tracking'					=> 0,

				// from version 4.1.2
				'custom_feed_title'			=> 'Latest imported feed items on ' . get_bloginfo('name'),

				// From version 4.2.3
				'pagination'				=>	'default',

				// From 4.2.4
				'authors_enable'			=>	0,

				// From 4.7.2
				'unique_titles'				=> 0,

                // From 4.7.8
                'expiration_notice_period'  => '2 weeks',

                // From 4.8.2
                'feed_request_useragent'    => 'Mozilla/5.0 (Linux 10.0; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.77 Safari/537.36',
                'certificate-path' => implode( '/', array( WPINC, 'certificates', 'ca-bundle.crt' ) ),

                // From 4.11.2
                'limit_feed_items_per_import'   => null,
                'feed_items_import_order'       => 'latest',

                // From 4.13
                'custom_css' => '',
                'html_classes' => '',

                // From 4.14.1
                'feed_cache_enabled' => 0,

                // From 4.17
                'schedule_future_items' => 0,
			)
		);

		// Return the default settings
		return $settings;
	}



	/**
	 * Returns the default tracking settings.
	 *
	 * @since 3.6
	 */
	function wprss_get_default_tracking_settings() {
		return apply_filters(
			'wprss_default_tracking_settings',
			array(
				'use_presstrends'				=>	'false',
				'tracking_notice'				=>	''
			)
		);
	}
