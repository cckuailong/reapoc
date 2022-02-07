<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_post_grid_shortcodes{
	
	
    public function __construct(){
		
		add_shortcode( 'post_grid', array( $this, 'post_grid_new_display' ) );

    }


    public function post_grid_new_display($atts, $content = null ){

        $atts = shortcode_atts(
            array(
                'id' => "",
            ),
            $atts
        );

        $atts = apply_filters('post_grid_atts', $atts);

        $grid_id = $atts['id'];

        ob_start();

        if(empty($grid_id)){
            echo 'Please provide valid post grid id, ex: <code>[post_grid id="123"]</code>';
            return;
        }

        //include( post_grid_plugin_dir . 'templates/post-grid.php');

        do_action('post_grid_main', $atts);




        return ob_get_clean();


    }





}

new class_post_grid_shortcodes();