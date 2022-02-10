<?php
if ( ! defined( 'ABSPATH' ) )
    exit;


add_action('wp_enqueue_scripts', 'tutor_gemeratepress_scripts');

if ( ! function_exists('tutor_gemeratepress_scripts')){
    function tutor_gemeratepress_scripts(){
        $dir_url = plugin_dir_url(__FILE__);
        wp_enqueue_style('tutor_gemeratepress', $dir_url.'assets/css/style.css');
    }
}



