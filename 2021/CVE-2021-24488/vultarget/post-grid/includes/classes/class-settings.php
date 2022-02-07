<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access

class class_post_grid_settings{

	public function __construct(){
		
		add_action('admin_menu', array( $this, 'post_grid_menu_init' ));
		
		}


    public function post_grid_menu_init() {

        $post_grid_info = get_option('post_grid_info');


        $data_update_status = isset($post_grid_info['data_update_status']) ? $post_grid_info['data_update_status'] : 'pending';


//        add_submenu_page('edit.php?post_type=post_grid', __('Layout Editor(Old)', 'post-grid'), __('Layout Editor(Old)', 'post-grid'), 'manage_options', 'layout_editor', array( $this, 'layout_editor' ));

        add_submenu_page('edit.php?post_type=post_grid', __('Settings', 'post-grid'), __('Settings', 'post-grid'), 'manage_options', 'post-grid-settings', array( $this, 'settings' ));
        add_submenu_page('edit.php?post_type=post_grid', __('Addons', 'post-grid'), __('Addons', 'post-grid'), 'manage_options', 'post-grid-addons', array( $this, 'addons' ));

        add_submenu_page( 'edit.php?post_type=post_grid', __( 'Layouts library', 'post-grid' ), __( 'Layouts library', 'post-grid' ), 'manage_options', 'import_layouts', array( $this, 'import_layouts' ) );


        if($data_update_status == 'pending'):
            add_submenu_page('edit.php?post_type=post_grid', __('Data Update', 'post-grid'), __('Data Update', 'post-grid'), 'manage_options', 'data-update', array( $this, 'data_update' ));

        endif;




    }

	public function settings(){
		include(post_grid_plugin_dir.'includes/menu/settings.php');
	}

    public function addons(){
        include(post_grid_plugin_dir.'includes/menu/addons.php');
    }
	public function layout_editor(){
		include(post_grid_plugin_dir.'includes/menu/layout-editor.php');
	}

    public function data_update(){
        include(post_grid_plugin_dir.'includes/menu/data-update.php');
    }



    public function import_layouts(){
        include(post_grid_plugin_dir.'includes/menu/import-layouts.php');


    }




	
}
	
new class_post_grid_settings();