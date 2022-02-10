<?php


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// display activation notice
add_action('admin_notices', 'wpedon_admin_notices');
function wpedon_admin_notices() {
	if (!get_option('wpedon_notice_shown')) {
		echo "<div class='updated'><p><a href='admin.php?page=wpedon_settings'>Click here to view the plugin settings</a>.</p></div>";
		update_option("wpedon_notice_shown", "true");
	}
}



// add paypal menu
add_action("admin_menu", "wpedon_plugin_menu");
function wpedon_plugin_menu() {
	global $plugin_dir_url;
	
	add_menu_page("PayPal Donations", "PayPal Donations", "manage_options", "wpedon_menu", "wpedon_plugin_orders",'dashicons-cart','28.5');
	
	add_submenu_page("wpedon_menu", "Donations", "Donations", "manage_options", "wpedon_menu", "wpedon_plugin_orders");
	
	add_submenu_page("wpedon_menu", "Buttons", "Buttons", "manage_options", "wpedon_buttons", "wpedon_plugin_buttons");
	
	add_submenu_page("wpedon_menu", "Settings", "Settings", "manage_options", "wpedon_settings", "wpedon_plugin_options");
}



function wpedon_action_links($links) {

global $support_link, $edit_link, $settings_link;
   $links[] = '<a href="https://wordpress.org/support/plugin/easy-paypal-donation" target="_blank">Support</a>';
   $links[] = '<a href="admin.php?page=wpedon_settings">Settings</a>';
   return $links;
}

add_filter( 'plugin_action_links_' . $plugin_basename, 'wpedon_action_links' );

