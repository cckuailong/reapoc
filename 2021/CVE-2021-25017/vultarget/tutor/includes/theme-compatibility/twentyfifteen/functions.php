<?php
if ( ! defined( 'ABSPATH' ) )
	exit;


add_action('wp_enqueue_scripts', 'tutor_twentyfifteen_scripts');

if ( ! function_exists('tutor_twentyfifteen_scripts')){
	function tutor_twentyfifteen_scripts(){
		$dir_url = plugin_dir_url(__FILE__);
		wp_enqueue_style('tutor_twentyfifteen', $dir_url.'assets/css/style.css');
	}
}



