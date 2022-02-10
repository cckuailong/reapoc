<?php
if ( ! defined( 'ABSPATH' ) )
    exit;


add_action('wp_enqueue_scripts', 'tutor_oceanwp_scripts');

if ( ! function_exists('tutor_oceanwp_scripts')){
    function tutor_oceanwp_scripts(){
        $dir_url = plugin_dir_url(__FILE__);
        wp_enqueue_style('tutor_oceanwp', $dir_url.'assets/css/style.css');
    }
}



