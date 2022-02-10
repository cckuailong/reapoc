<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

function acf_photo_gallery_image_fields( $args, $attachment_id, $field){
	return array(
		'url' => array(
			'type' => 'text', 
			'label' => 'URL', 
			'name' => 'url', 
			'value' => ($args['url'])? esc_url($args['url']):null
		),
		'target' => array(
			'type' => 'checkbox', 
			'label' => 'Open in new tab', 
			'name' => 'target', 
			'value' => ($args['target'])? esc_attr($args['target']):null
		),
		'title' => array(
			'type' => 'text', 
			'label' => 'Title', 
			'name' => 'title', 
			'value' => ($args['title'])? esc_attr($args['title']):null
		),
		'caption' => array(
			'type' => 'textarea', 
			'label' => 'Caption', 
			'name' => 'caption', 
			'value' => ($args['caption'])? esc_attr($args['caption']):null
		)
	);
}
add_filter( 'acf_photo_gallery_image_fields', 'acf_photo_gallery_image_fields', 10, 3 );
