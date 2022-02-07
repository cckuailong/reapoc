<?php
if ( ! defined('ABSPATH')) exit; // if direct access 

class class_post_grid_notices{

    public function __construct(){

        //add_action('admin_notices', array( $this, 'import_layouts' ));

    }

    public function import_layouts(){

        $post_grid_info = get_option('post_grid_info');
        $import_layouts = isset($post_grid_info['import_layouts']) ? $post_grid_info['import_layouts'] : '';


        //delete_option('post_grid_info');

        ob_start();

        if($import_layouts != 'done'):
            ?>
            <div class="update-nag">
                <?php
                echo sprintf(__('Post grid require import free layouts, please <a href="%s">click here</a> to go import page', 'post-grid-pro'), admin_url().'edit.php?post_type=post_grid&page=post-grid-settings&tab=help_support')
                ?>
            </div>
        <?php
        endif;


        echo ob_get_clean();
    }

}

new class_post_grid_notices();