<?php
if ( ! defined( 'ABSPATH' ) )
	exit;


add_action('wp_enqueue_scripts', 'tutor_storefront_scripts');

if ( ! function_exists('tutor_storefront_scripts')){
	function tutor_storefront_scripts(){
		$dir_url = plugin_dir_url(__FILE__);
		wp_enqueue_style('tutor_storefront', $dir_url.'assets/css/style.css');
	}
}



