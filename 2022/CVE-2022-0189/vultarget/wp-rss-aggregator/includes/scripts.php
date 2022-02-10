<?php
/**
 * Scripts
 *
 * @package WPRSSAggregator
 */

use Aventura\Wprss\Core\Licensing\License\Status as License_Status;

add_action('init', function () {
    $version = wprss()->getVersion();

    // Add the Class library, the Xdn library, and the Aventura namespace and classes
    wp_register_script('wprss-xdn-class', wprss_get_script_url('class'), ['jquery'], $version);
    wp_register_script('wprss-xdn-lib', wprss_get_script_url('xdn'), ['wprss-xdn-class'], $version);
    wp_register_script('aventura', wprss_get_script_url('aventura'), ['wprss-xdn-lib'], $version);

    wp_register_script('wprss-admin-addon-ajax', WPRSS_JS . 'admin-addon-ajax.js', ['jquery'], $version);
    wp_localize_script('wprss-admin-addon-ajax', 'wprss_admin_addon_ajax', [
        'please_wait' => __('Please wait ...', 'wprss'),
        'nonce' => wp_create_nonce('wprss_admin_addon_ajax'),
    ]);

    // Prepare the URL for removing bulk from blacklist, with a nonce
    $blacklist_remove_url = admin_url('edit.php?wprss-bulk=1');
    $blacklist_remove_url = wp_nonce_url($blacklist_remove_url, 'blacklist-remove-selected', 'wprss_blacklist_trash');
    $blacklist_remove_url .= '&wprss-blacklist-remove=';
    wp_register_script('wprss-admin-custom', WPRSS_JS . 'admin-custom.js',
        ['jquery', 'jquery-ui-datepicker', 'jquery-ui-slider'], $version);
    wp_localize_script('wprss-admin-custom', 'wprss_admin_custom', [
        'failed_to_import' => __('Failed to import', 'wprss'),
        'items_are_importing' => __('Importing!', 'wprss'),
        'items_are_deleting' => __('Deleting!', 'wprss'),
        'please_wait' => __('Please wait ...', 'wprss'),
        'bulk_add' => __('Bulk Add', 'wprss'),
        'ok' => __('OK', 'wprss'),
        'cancel' => __('Cancel', 'wprss'),
        'blacklist_desc' => __('The feed items listed here will be disregarded when importing new items from your feed sources.',
            'wprss'),
        'blacklist_remove' => __('Remove selected from Blacklist', 'wprss'),
        'blacklist_remove_url' => $blacklist_remove_url,
    ]);
    // Creates the wprss_urls object in JS
    wp_localize_script('wprss-admin-custom', 'wprss_urls', [
        'import_export' => admin_url('edit.php?post_type=wprss_feed&page=wprss-import-export-settings'),
    ]);

    wp_register_script('jquery-ui-timepicker-addon', WPRSS_JS . 'jquery-ui-timepicker-addon.js',
        ['jquery', 'jquery-ui-datepicker'], $version);
    wp_register_script('wprss-custom-bulk-actions', WPRSS_JS . 'admin-custom-bulk-actions.js', ['jquery'], $version);
    wp_localize_script('wprss-custom-bulk-actions', 'wprss_admin_bulk', [
        'activate' => __('Activate', 'wprss'),
        'pause' => __('Pause', 'wprss'),
    ]);

    wp_register_script('wprss-feed-source-table-heartbeat', WPRSS_JS . 'heartbeat.js', [], $version);
    wp_localize_script('wprss-feed-source-table-heartbeat', 'wprss_admin_heartbeat', [
        'ago' => __('ago', 'wprss'),
    ]);
    wp_register_script('wprss-admin-license-manager', WPRSS_JS . 'admin-license-manager.js', [], $version);

    wp_register_script('wprss-admin-licensing', WPRSS_JS . 'admin-licensing.js', [], $version);
    wp_localize_script('wprss-admin-licensing', 'wprss_admin_licensing', [
        'activating' => __('Activating...', 'wprss'),
        'deactivating' => __('Deactivating...', 'wprss'),
    ]);

    wp_register_script('wprss-admin-help', WPRSS_JS . 'admin-help.js', [], $version);
    wp_localize_script('wprss-admin-help', 'wprss_admin_help', [
        'sending' => __('Sending...', 'wprss'),
        'sent-error' => sprintf(
            __(
                'There was an error sending the form. Please use the <a href="%s">contact form on our site.</a>',
                'wprss'
            ),
            esc_attr('https://www.wprssaggregator.com/contact/')
        ),
        'sent-ok' => __(
            'Your message has been sent and we\'ll send you a confirmation e-mail when we receive it.',
            'wprss'
        ),
    ]);

    wp_register_script('wprss-hs-beacon-js', WPRSS_JS . 'beacon.min.js', [], $version);
    wp_localize_script('wprss-hs-beacon-js', 'WprssHelpBeaconConfig', [
        'premiumSupport' => (wprss_licensing_get_manager()->licenseWithStatusExists(License_Status::VALID)),
    ]);

    wp_register_script('wprss-gallery-js', WPRSS_JS . 'gallery.js', ['jquery'], $version, true);

    wp_register_script('wpra-tools', WPRSS_JS . 'admin/tools/main.js', ['jquery'], $version, true);
    wp_register_script('wpra-logs-tool', WPRSS_JS . 'admin/tools/logs.js', ['jquery'], $version, true);
    wp_register_script('wpra-blacklist-tool', WPRSS_JS . 'admin/tools/blacklist.js', ['jquery'], $version, true);

    $wpSchedules = wp_get_schedules();
    $globSchedule = wprss_get_general_setting('cron_interval');
    $customSchedule = [
        'display' => __('Use Global Cron', 'wprss'),
        'interval' => $wpSchedules[$globSchedule]['interval'],
    ];
    $schedules = array_merge(['global' => $customSchedule], $wpSchedules);

    wp_register_script('wpra-crons-tool', WPRSS_JS . 'admin/tools/crons.js', ['jquery'], $version, true);
    wp_localize_script('wpra-crons-tool', 'WpraCronsTool', [
        'restUrl' => trailingslashit(rest_url()),
        'restApiNonce' => wp_create_nonce('wp_rest'),
        'globalInterval' => $globSchedule,
        'globalTime' => wprss_get_global_update_time(),
        'globalWord' => __('Global', 'wprss'),
        'perPage' => 30,
        'schedules' => $schedules,
    ]);

    wp_register_script('wpra-reset-tool', WPRSS_JS . 'admin/tools/reset.js', ['jquery'], $version, true);
    wp_localize_script('wpra-reset-tool', 'WpraResetTool', [
        'message' => __('Are you sure you want to do this? This operation cannot be undone.', 'wprss'),
    ]);
}, 9);

add_action('admin_enqueue_scripts', 'wprss_admin_scripts_styles');
/**
 * Insert required scripts, styles and filters on the admin side
 *
 * @since 2.0
 */
function wprss_admin_scripts_styles()
{
    $isWpraScreen = wprss_is_wprss_page();

    // On all admin screens
    wp_enqueue_style('wprss-admin-editor-styles');
    wp_enqueue_style('wprss-admin-tracking-styles');
    wp_enqueue_style('wprss-admin-general-styles');

    // Only on WPRA-related admin screens
    if ($isWpraScreen) {
        wprss_admin_exclusive_scripts_styles();
    }

    do_action('wprss_admin_scripts_styles');
} // end wprss_admin_scripts_styles

/**
 * Enqueues backend scripts on WPRA-related pages only
 *
 * @since 4.10
 */
function wprss_admin_exclusive_scripts_styles()
{
    $screen = get_current_screen();
    $pageBase = $screen->base;
    $postType = $screen->post_type;

    wp_enqueue_style('wprss-admin-styles');
    wp_enqueue_style('wprss-fa');
    wp_enqueue_style('wprss-admin-3.8-styles');

    wp_enqueue_script('wprss-xdn-class');
    wp_enqueue_script('wprss-xdn-lib');
    wp_enqueue_script('aventura');

    wp_enqueue_script('wprss-admin-addon-ajax');

    wp_enqueue_script('wprss-admin-custom');

    wp_enqueue_script('jquery-ui-timepicker-addon');
    wp_enqueue_style('jquery-style');

    if ($pageBase === 'post' && $postType = 'wprss_feed') {
        // Change text on post screen from 'Enter title here' to 'Enter feed name here'
        add_filter('enter_title_here', 'wprss_change_title_text');
        wp_enqueue_media();
        wp_enqueue_script('wprss-gallery-js');
    }
    if ('wprss_feed' === $postType) {
        wp_enqueue_script('wprss-custom-bulk-actions');
    }
    if ('wprss_feed_item' === $postType) {
        wp_enqueue_script('wprss-custom-bulk-actions-feed-item');
    }

    // Load Heartbeat script and set dependancy for Heartbeat to ensure Heartbeat is loaded
    if ($pageBase === 'edit' && $postType === 'wprss_feed' && apply_filters('wprss_ajax_polling', true) === true) {
        wp_enqueue_script('wprss-feed-source-table-heartbeat');
    }

    if ($pageBase === 'wprss_feed_page_wprss-aggregator-settings') {
        wp_enqueue_script('wprss-admin-license-manager');
        wp_enqueue_script('wprss-admin-licensing');
    }

    if ($pageBase === 'wprss_feed_page_wprss-help') {
        wp_enqueue_script('wprss-admin-help');
    }

    if ($pageBase === 'wprss_feed_page_wpra_tools') {
        wp_enqueue_script('wpra-tools');
        wp_enqueue_script('wpra-logs-tool');
        wp_enqueue_script('wpra-blacklist-tool');
        wp_enqueue_script('wpra-crons-tool');
        wp_enqueue_script('wpra-reset-tool');
    }

    if (wprss_is_help_beacon_enabled()) {
        wp_enqueue_script('wprss-hs-beacon-js');
        wp_enqueue_style('wprss-hs-beacon-css');
    }

    do_action('wprss_admin_exclusive_scripts_styles');
}

add_action('wp_enqueue_scripts', 'wprss_load_scripts');
/**
 * Enqueues the required scripts.
 *
 * @since 3.0
 */
function wprss_load_scripts()
{
    /*  wp_enqueue_script( 'jquery.colorbox-min', WPRSS_JS . 'jquery.colorbox-min.js', array( 'jquery' ) );
      wp_enqueue_script( 'custom', WPRSS_JS . 'custom.js', array( 'jquery', 'jquery.colorbox-min' ) );  */
    do_action('wprss_register_scripts');
} // end wprss_head_scripts_styles

/**
 * Returns the path to the WPRSS templates directory
 *
 * @since       3.0
 * @return      string
 */
function wprss_get_templates_dir()
{
    return WPRSS_DIR . 'templates';
}

/**
 * Returns the URL to the WPRSS templates directory
 *
 * @since       3.0
 * @return      string
 */
function wprss_get_templates_uri()
{
    return WPRSS_URI . 'templates';
}

add_action('init', 'wprss_register_styles');
/**
 * Registers all WPRA styles.
 *
 * Does not enqueue anything.
 *
 * @since 3.0
 */
function wprss_register_styles()
{
    $version = wprss()->getVersion();

    wp_register_style('wprss-admin-styles', WPRSS_CSS . 'admin-styles.css', [], $version);
    wp_register_style('wprss-fa', WPRSS_CSS . 'font-awesome.min.css', [], $version);
    wp_register_style('wprss-admin-3.8-styles', WPRSS_CSS . 'admin-3.8.css', [], $version);
    wp_register_style('wprss-admin-editor-styles', WPRSS_CSS . 'admin-editor.css', [], $version);
    wp_register_style('wprss-admin-tracking-styles', WPRSS_CSS . 'admin-tracking-styles.css', [], $version);
    wp_register_style('wprss-admin-general-styles', WPRSS_CSS . 'admin-general-styles.css', [], $version);
    wp_register_style('wprss-hs-beacon-css', WPRSS_CSS . 'beacon.css', [], $version);
    wp_register_style('jquery-style', WPRSS_CSS . 'jquery-ui-smoothness.css', [], $version);
}
