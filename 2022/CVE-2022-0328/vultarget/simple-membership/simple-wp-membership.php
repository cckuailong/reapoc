<?php
/*
Plugin Name: Simple WordPress Membership
Version: 4.0.7
Plugin URI: https://simple-membership-plugin.com/
Author: smp7, wp.insider
Author URI: https://simple-membership-plugin.com/
Description: A flexible, well-supported, and easy-to-use WordPress membership plugin for offering free and premium content from your WordPress site.
Text Domain: simple-membership
Domain Path: /languages/
Requires PHP: 5.6
*/

//Direct access to this file is not permitted
if (!defined('ABSPATH')){
    exit("Do not access this file directly.");
}

include_once('classes/class.simple-wp-membership.php');
include_once('classes/class.swpm-cronjob.php');
include_once('swpm-compat.php');

define('SIMPLE_WP_MEMBERSHIP_VER', '4.0.7');
define('SIMPLE_WP_MEMBERSHIP_DB_VER', '1.3');
define('SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL', home_url());
define('SIMPLE_WP_MEMBERSHIP_PATH', dirname(__FILE__) . '/');
define('SIMPLE_WP_MEMBERSHIP_URL', plugins_url('', __FILE__));
define('SIMPLE_WP_MEMBERSHIP_DIRNAME', dirname(plugin_basename(__FILE__)));
define('SIMPLE_WP_MEMBERSHIP_TEMPLATE_PATH', 'simple-membership');
if (!defined('COOKIEHASH')) {
    define('COOKIEHASH', md5(get_site_option('siteurl')));
}
define('SIMPLE_WP_MEMBERSHIP_AUTH', 'simple_wp_membership_' . COOKIEHASH);
define('SIMPLE_WP_MEMBERSHIP_SEC_AUTH', 'simple_wp_membership_sec_' . COOKIEHASH);
define('SIMPLE_WP_MEMBERSHIP_STRIPE_ZERO_CENTS',serialize(array('JPY', 'MGA', 'VND', 'KRW')));

SwpmUtils::do_misc_initial_plugin_setup_tasks();

register_activation_hook(SIMPLE_WP_MEMBERSHIP_PATH . 'simple-wp-membership.php', 'SimpleWpMembership::activate');
register_deactivation_hook(SIMPLE_WP_MEMBERSHIP_PATH . 'simple-wp-membership.php', 'SimpleWpMembership::deactivate');

add_action('swpm_login', 'SimpleWpMembership::swpm_login', 10, 3);

$simple_membership = new SimpleWpMembership();
$simple_membership_cron = new SwpmCronJob();

//Add settings link in plugins listing page
function swpm_add_settings_link($links, $file) {
    if ($file == plugin_basename(__FILE__)) {
        $settings_link = '<a href="admin.php?page=simple_wp_membership_settings">Settings</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

add_filter('plugin_action_links', 'swpm_add_settings_link', 10, 2);
