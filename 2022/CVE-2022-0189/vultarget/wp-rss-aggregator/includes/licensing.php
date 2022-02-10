<?php

/**
 * Gets the singleton instance of the Settings class, creating it if it doesn't exist.
 *
 * @since 4.7.8
 * @return Aventura\Wprss\Core\Licensing\Settings
 */
function wprss_licensing_get_settings_controller() {
	static $instance = null;
	return is_null( $instance )
            ? $instance = new Aventura\Wprss\Core\Licensing\Settings()
            : $instance;
}


/**
 * Gets the singleton instance of the Manager class, creating it if it doesn't exist.
 *
 * @since 4.7.8
 * @return Aventura\Wprss\Core\Licensing\Manager
 */
function wprss_licensing_get_manager() {
	static $manager = null;

	if ( is_null( $manager ) ) {
        $manager = new Aventura\Wprss\Core\Licensing\Manager();
        $manager->setExpirationNoticePeriod( wprss_get_general_setting( 'expiration_notice_period' ) );
        $manager->setDefaultAuthorName( 'Jean Galea' );
    }

    return $manager;
}

/**
 * Gets the singleton instance of the AjaxController class, creating it if it doesn't exist.
 *
 * @since 4.7.8
 * @return Aventura\Wprss\Core\Licensing\AjaxController
 */
function wprss_licensing_get_ajax_controller() {
	static $instance = null;
	return is_null( $instance )
            ? $instance = new Aventura\Wprss\Core\Licensing\AjaxController()
            : $instance;
}

/**
 * Returns all registered addons.
 *
 * @since 4.4.5
 * @param bool $noCache If true, the add-ons filter will be ran again; if false, it will only be ran if not ran before.
 * @return array Array of add-on codes.
 */
function wprss_get_addons($noCache = false) {
	static $addons = null;
	return is_null( $addons ) || $noCache
            ? $addons = apply_filters( 'wprss_register_addon', array() )
            : $addons;
}

/**
 * Finds all WPRA addons installed, even if they are deactivated.
 *
 * @since 4.12.1
 *
 * @return array An array of WordPress plugin info arrays.
 */
function wprss_find_installed_addons()
{
    $all_plugins = get_plugins();
    $addons = array();
    foreach ($all_plugins as $plugin_path => $plugin) {
        if ($plugin_path !== WPRSS_FILE_CONSTANT && strpos($plugin['Name'], 'WP RSS Aggregator - ') === 0) {
            $addons[$plugin_path] = $plugin;
        }
    }
    return $addons;
}

/**
 * Retrieves the names of the installed addons, even if they are deactivated.
 *
 * @since 4.12.1
 *
 * @return string[] A list of the names of installed addons.
 */
function wprss_find_installed_addon_names()
{
    return array_map(function ($plugin_info) {
        return substr($plugin_info['Name'], strlen('WP RSS Aggregator - '));
    }, wprss_find_installed_addons());
}

/**
 * Hooks the licensing system into WordPress.
 */
function wprss_licensing() {
    static $licensing = null;

    if ( is_null( $licensing ) ) {
        // Get licensing class instances
        $manager = wprss_licensing_get_manager();
        $settingsController = wprss_licensing_get_settings_controller();
        $ajaxController = wprss_licensing_get_ajax_controller();

        // Set up Ajax Controller pointers
        $ajaxController->setManager( $manager );
        $ajaxController->setSettingsController( $settingsController );

        // Licensing Ajax Controller hooks
        add_action( 'wp_ajax_wprss_ajax_manage_license', array( $ajaxController, 'handleAjaxManageLicense' ) );
        add_action( 'wp_ajax_wprss_ajax_fetch_license', array( $ajaxController, 'handleAjaxFetchLicense' ) );

        // Licensing Settings Controller hooks
        add_action( 'wprss_admin_init', array( $settingsController, 'registerSettings' ), 100 );
        add_action( 'admin_init', array( $settingsController, 'handleLicenseStatusChange' ), 10 );
        add_action( 'wprss_settings_license_key_is_valid', array( $settingsController, 'validateLicenseKeyForSave' ), 10, 2 );

        $licensing = (object) compact( 'manager', 'settingsController', 'ajaxController' );

        // Action for hooking after licensing has been initialized
        do_action( 'wprss_init_licensing' );

        // Backwards compatibility with old licensing lib
        // This ensures that, if an addon is loading an older version of the licensing library, the old method for initializing the updaters is called.
        if ( method_exists($manager, 'initUpdaterInstances') ) {
            add_action( 'admin_init', array($manager, 'initUpdaterInstances') );
        }
    }

    return $licensing;
}
