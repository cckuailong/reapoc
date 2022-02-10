<?php

define('WPRSS_UPDATE_PAGE_SLUG', 'wpra-update');
define('WPRSS_UPDATE_PAGE_PREV_VERSION_OPTION', 'wprss_prev_update_page_version');

/**
 * Registers the update page.
 *
 * @since 4.12.1
 */
add_action('admin_menu', function () {
    add_submenu_page(
        null,
        __('Thank you for updating WP RSS Aggregator', 'wprss'),
        __('Thank you for updating WP RSS Aggregator', 'wprss'),
        'manage_options',
        WPRSS_UPDATE_PAGE_SLUG,
        'wprss_render_update_page'
    );
});

/**
 * Renders the update page.
 *
 * @since 4.12.1
 *
 * @throws Twig_Error_Loader
 * @throws Twig_Error_Runtime
 * @throws Twig_Error_Syntax
 */
function wprss_render_update_page()
{
    wprss_update_previous_update_page_version();

    wp_enqueue_style('update-page', WPRSS_APP_CSS . 'update.min.css');
    wp_enqueue_script('wpra-colorbox', WPRSS_JS . 'jquery.colorbox-min.js', ['jquery'], WPRSS_VERSION);
    wp_enqueue_style('colorbox', WPRSS_CSS . 'colorbox.css', [], '1.4.33');

    $changelog = wpra_get('core/changelog_dataset');
    $parsedown = wpra_get('parsedown');

    echo wprss_render_template('admin/update-page.twig', array(
        'title' => __('What\'s new in WP RSS Aggregator', 'wprss'),
        'version' => WPRSS_VERSION,
        'url' => array(
            'main' => admin_url('edit.php?post_type=wprss_feed'),
            'website' => 'https://www.wprssaggregator.com/',
            'support' => 'https://www.wprssaggregator.com/contact/',
            'kb' => 'https://kb.wprssaggregator.com/',
        ),
        'path' => array(
            'images' => WPRSS_IMG,
        ),
        'changelog' => $parsedown->text($changelog[WPRSS_VERSION]['raw'])
    ));
}

/**
 * Retrieves the URL of the update page.
 *
 * @since 4.12.1
 *
 * @return string
 */
function wprss_get_update_page_url()
{
    return menu_page_url(WPRSS_UPDATE_PAGE_SLUG, false);
}

/**
 * Checks whether the update should be shown or not, based on whether the user is new and previously had an older
 * version of the plugin.
 *
 * @since 4.12.1
 *
 * @return bool True if the update page should be shown, false if not.
 */
function wprss_should_do_update_page()
{
    // Temporarily disabled
    return false && !wprss_is_new_user() && current_user_can('manage_options') && wprss_user_had_previous_version();
}

/**
 * Checks whether the user had a previous version of WP RSS Aggregator.
 *
 * @since 4.12.1
 *
 * @return mixed
 */
function wprss_user_had_previous_version()
{
    $previous = wprss_get_previous_update_page_version();
    $is_newer = version_compare(WPRSS_VERSION, $previous, '>');

    return $is_newer;
}

/**
 * Updates the previous update page version to the current plugin version.
 *
 * @since 4.12.1
 */
function wprss_update_previous_update_page_version()
{
    update_option(WPRSS_UPDATE_PAGE_PREV_VERSION_OPTION, WPRSS_VERSION);
}

/**
 * Retrieves the previous update page version seen by the user, if at all.
 *
 * @since 4.12.1
 *
 * @return string The version string, or '0.0.0' if the user is new nad has not used WPRA before.
 */
function wprss_get_previous_update_page_version()
{
    wprss_migrate_welcome_page_to_update_page();

    return get_option(WPRSS_UPDATE_PAGE_PREV_VERSION_OPTION, '0.0.0');
}

/**
 * Migrates the previously used "welcome screen" version DB option.
 *
 * @since 4.12.1
 */
function wprss_migrate_welcome_page_to_update_page()
{
    // Get the previous welcome screen version - for when the page was called the "welcome screen"
    $pwsv = get_option('wprss_pwsv', null);

    // If the option exists, move it to the new option
    if ($pwsv !== null) {
        update_option(WPRSS_UPDATE_PAGE_PREV_VERSION_OPTION, $pwsv);
        delete_option('wprss_pwsv');
    }
}
