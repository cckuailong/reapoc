<?php
if ( ! defined( 'ABSPATH' ) )
    exit;


add_action('wp_enqueue_scripts', 'tutor_astra_scripts');

if ( ! function_exists('tutor_astra_scripts')){
    function tutor_astra_scripts(){
        $dir_url = plugin_dir_url(__FILE__);
        wp_enqueue_style('tutor_astra', $dir_url.'assets/css/style.css');
    }
}



