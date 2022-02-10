<?php
if ( ! defined( 'ABSPATH' ) )
	exit;


add_action('wp_enqueue_scripts', 'tutor_twentyseventeen_scripts');

if ( ! function_exists('tutor_twentyseventeen_scripts')){
	function tutor_twentyseventeen_scripts(){
		$dir_url = plugin_dir_url(__FILE__);
		wp_enqueue_style('tutor_twentyseventeen', $dir_url.'assets/css/style.css');
	}
}



