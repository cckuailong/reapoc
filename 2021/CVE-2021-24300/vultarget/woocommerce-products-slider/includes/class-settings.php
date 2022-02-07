<?php
if ( ! defined('ABSPATH')) exit;  // if direct access 	


class wcps_class_settings{
	
	
    public function __construct(){

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );

    }
	
	
	public function admin_menu() {

        $wcps_plugin_info = get_option('wcps_plugin_info');
        $wcps_upgrade = isset($wcps_plugin_info['wcps_upgrade']) ? $wcps_plugin_info['wcps_upgrade'] : '';

        add_submenu_page( 'edit.php?post_type=wcps', __( 'Settings', 'woocommerce-products-slider' ), __( 'Settings', 'woocommerce-products-slider' ), 'manage_options', 'settings', array( $this, 'settings' ) );
        add_submenu_page( 'edit.php?post_type=wcps', __( 'Import layouts', 'woocommerce-products-slider' ), __( 'Import layouts', 'woocommerce-products-slider' ), 'manage_options', 'import_layouts', array( $this, 'import_layouts' ) );

       if($wcps_upgrade != 'done')
        add_submenu_page( 'edit.php?post_type=wcps', __( 'Upgrade status', 'woocommerce-products-slider' ), __( 'Upgrade status', 'woocommerce-products-slider' ), 'manage_options', 'upgrade_status', array( $this, 'upgrade_status' ) );







	}
	
	public function settings(){
		
		//include( 'menu/settings-old.php' );
        include( 'menu/settings.php' );

    }


    public function upgrade_status(){
        include( 'menu/upgrade-status.php' );

    }

    public function import_layouts(){
        include( 'menu/import-layouts.php' );

    }
	

}


new wcps_class_settings();

