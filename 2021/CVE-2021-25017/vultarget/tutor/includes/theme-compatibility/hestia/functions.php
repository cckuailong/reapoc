<?php
if ( ! defined( 'ABSPATH' ) )
    exit;


add_action('wp_enqueue_scripts', 'tutor_hestia_scripts');

if ( ! function_exists('tutor_hestia_scripts')){
    function tutor_hestia_scripts(){
        $dir_url = plugin_dir_url(__FILE__);
        wp_enqueue_style('tutor_hestia', $dir_url.'assets/css/style.css');
    }
}



