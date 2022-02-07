<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access

class class_post_grid_data_update{
	
	public function __construct(){
		
		add_action('admin_notices', array( $this, 'admin_notices_data_update' ));
        //add_action('post_grid_action_install', array( $this, 'post_grid_info' ));
		
		}

	
	
	
	public function admin_notices_data_update(){

        $post_grid_info = get_option('post_grid_info');
        $data_update_status = isset($post_grid_info['data_update_status']) ? $post_grid_info['data_update_status'] : 'pending';

        $admin_url = get_admin_url();


		ob_start();

		if($data_update_status == 'pending'):
            ?>
            <div class="update-nag">
                <?php
                echo sprintf(__('Data update required for  <b>%s &raquo; <a href="%sedit.php?post_type=post_grid&page=data-update">Update</a></b>', 'post-grid'), post_grid_plugin_name, $admin_url)
                ?>
            </div>
        <?php
        endif;


		echo ob_get_clean();
		}
	}
	
new class_post_grid_data_update();