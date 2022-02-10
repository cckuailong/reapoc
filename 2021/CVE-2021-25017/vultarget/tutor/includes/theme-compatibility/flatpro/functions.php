<?php
if ( ! defined( 'ABSPATH' ) )
    exit;


add_action('wp_enqueue_scripts', 'tutor_flatpro_scripts');

if ( ! function_exists('tutor_flatpro_scripts')){
    function tutor_flatpro_scripts(){
        $dir_url = plugin_dir_url(__FILE__);
        wp_enqueue_style('tutor_flatpro', $dir_url.'assets/css/style.css');
    }
}



