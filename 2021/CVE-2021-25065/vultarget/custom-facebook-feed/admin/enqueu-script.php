<?php
require_once trailingslashit( CFF_PLUGIN_DIR ) . 'admin/addon-functions.php';

require_once trailingslashit( CFF_PLUGIN_DIR ) . 'inc/Helpers/PluginSilentUpgrader.php';
require_once trailingslashit( CFF_PLUGIN_DIR ) . 'inc/Helpers/PluginSilentUpgraderSkin.php';
require_once trailingslashit( CFF_PLUGIN_DIR ) . 'inc/Admin/CFF_Install_Skin.php';


function cff_ppca_token_check_flag() {
	check_ajax_referer( 'cff_nonce' , 'cff_nonce');

	$cap = current_user_can( 'manage_custom_facebook_feed_options' ) ? 'manage_custom_facebook_feed_options' : 'manage_options';
	$cap = apply_filters( 'cff_settings_pages_capability', $cap );
	if ( ! current_user_can( $cap ) ) {
		wp_send_json_error(); // This auto-dies.
	}

    if( get_transient('cff_ppca_access_token_invalid') ){
        print_r(true);
    } else {
        print_r(false);
    }

    die();
}
add_action( 'wp_ajax_cff_ppca_token_check_flag', 'cff_ppca_token_check_flag' );

add_action( 'admin_enqueue_scripts' , 'enqueue_admin_scripts_assets' );
function enqueue_admin_scripts_assets(){
	wp_register_style(
		'custom_wp_admin_css',
		CFF_PLUGIN_URL . 'admin/assets/css/cff-admin-style.css',
		false,
		CFFVER
	);
	wp_enqueue_style( 'custom_wp_admin_css' );
	wp_enqueue_style(
		'cff-font-awesome',
		'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css',
		array(),
		'4.5.0'
	);

	wp_enqueue_script(
		'cff_admin_script',
		CFF_PLUGIN_URL . 'admin/assets/js/cff-admin-scripts.js',
		false,
		CFFVER
	);

	wp_localize_script( 'cff_admin_script', 'cffA', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'cff_nonce' => wp_create_nonce( 'cff_nonce' )
	)
);
	$strings = array(
		'addon_activate'                  => esc_html__( 'Activate', 'custom-facebook-feed' ),
		'addon_activated'                 => esc_html__( 'Activated', 'custom-facebook-feed' ),
		'addon_active'                    => esc_html__( 'Active', 'custom-facebook-feed' ),
		'addon_deactivate'                => esc_html__( 'Deactivate', 'custom-facebook-feed' ),
		'addon_inactive'                  => esc_html__( 'Inactive', 'custom-facebook-feed' ),
		'addon_install'                   => esc_html__( 'Install Addon', 'custom-facebook-feed' ),
		'addon_error'                     => esc_html__( 'Could not install addon. Please download from smashballoon.com and install manually.', 'custom-facebook-feed' ),
		'plugin_error'                    => esc_html__( 'Could not install a plugin. Please download from WordPress.org and install manually.', 'custom-facebook-feed' ),
		'addon_search'                    => esc_html__( 'Searching Addons', 'custom-facebook-feed' ),
		'ajax_url'                        => admin_url( 'admin-ajax.php' ),
		'cancel'                          => esc_html__( 'Cancel', 'custom-facebook-feed' ),
		'close'                           => esc_html__( 'Close', 'custom-facebook-feed' ),
		'nonce'                           => wp_create_nonce( 'cff-admin' ),
		'almost_done'                     => esc_html__( 'Almost Done', 'custom-facebook-feed' ),
		'oops'                            => esc_html__( 'Oops!', 'custom-facebook-feed' ),
		'ok'                              => esc_html__( 'OK', 'custom-facebook-feed' ),
		'plugin_install_activate_btn'     => esc_html__( 'Install and Activate', 'custom-facebook-feed' ),
		'plugin_install_activate_confirm' => esc_html__( 'needs to be installed and activated to import its forms. Would you like us to install and activate it for you?', 'custom-facebook-feed' ),
		'plugin_activate_btn'             => esc_html__( 'Activate', 'custom-facebook-feed' ),
	);
	$strings = apply_filters( 'cff_admin_strings', $strings );
	wp_localize_script(
		'cff_admin_script',
		'cff_admin',
		$strings
	);
}