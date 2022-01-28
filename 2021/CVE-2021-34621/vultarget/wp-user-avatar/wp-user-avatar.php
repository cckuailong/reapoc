<?php
/**
 * Plugin Name: ProfilePress
 * Plugin URI: https://profilepress.net
 * Description: The modern WordPress membership and user profile plugin.
 * Version: 3.1.3
 * Author: ProfilePress Team
 * Author URI: https://profilepress.net
 * Text Domain: wp-user-avatar
 * Domain Path: /languages
 *
 */

defined('ABSPATH') or die("No script kiddies please!");

define('PROFILEPRESS_SYSTEM_FILE_PATH', __FILE__);
define('PPRESS_VERSION_NUMBER', '3.1.3');

require __DIR__ . '/vendor/autoload.php';

add_action('init', function () {
    load_plugin_textdomain('wp-user-avatar', false, dirname(plugin_basename(PROFILEPRESS_SYSTEM_FILE_PATH)) . '/languages');
});

ProfilePress\Core\Base::get_instance();