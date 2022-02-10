<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

//Helper function that resizes the images from the specified args
function acf_photo_gallery_resize_image( $img_url, $width = 150, $height = 150){
	if( !function_exists('aq_resize') ){
		require_once( dirname(__FILE__) . '/aq_resizer.php');
	}
	$extension = explode('.', $img_url);
	$extension = strtolower(end($extension));
	if( $extension != 'svg' ){
		$img_url = aq_resize( $img_url, $width, $height, true, true, true);
	}
	return $img_url;
}