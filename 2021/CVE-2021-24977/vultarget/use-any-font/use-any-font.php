<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
/*
Plugin Name: Use Any Font
Plugin URI: https://dineshkarki.com.np/use-any-font
Description: Embed any font in your website
Author: Dnesscarkey
Version: 6.2
Author URI: https://dineshkarki.com.np/use-any-font
*/

define ( 'UAF_FILE_PATH', plugin_dir_path( __FILE__ ) );

include UAF_FILE_PATH.'includes/uaf_config.php';
include UAF_FILE_PATH.'includes/functions/uaf_admin_functions.php';
include UAF_FILE_PATH.'includes/functions/uaf_client_functions.php';
include UAF_FILE_PATH.'includes/functions/uaf_font_functions.php';
include UAF_FILE_PATH.'includes/functions/uaf_cache_functions.php';


add_action('init', 'uaf_plugin_initialize');
add_action('admin_menu', 'uaf_create_menu');
add_action('admin_enqueue_scripts', 'uaf_admin_assets');
add_action('wp_enqueue_scripts', 'uaf_client_assets');
add_action('admin_notices', 'uaf_admin_notices');

add_action( 'wp_ajax_uaf_predefined_font_interface', 'uaf_predefined_font_interface' );

register_activation_hook( __FILE__, 'uaf_plugin_activated' );