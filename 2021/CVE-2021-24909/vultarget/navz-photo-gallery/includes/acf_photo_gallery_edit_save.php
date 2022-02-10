<?php

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

//Fires off when ediitn the details of the photo
function acf_photo_gallery_edit_save(){
	if( wp_verify_nonce( $_POST['acf-pg-hidden-nonce'], 'acf_photo_gallery_edit_save') and !empty($_POST['acf-pg-hidden-field']) and !empty($_POST['acf-pg-hidden-post'])  and !empty($_POST['acf-pg-hidden-attachment']) ){

		$request = $_POST;
		$field = sanitize_text_field($request['acf-pg-hidden-field']);
		$post = sanitize_text_field($request['acf-pg-hidden-post']);
		$attachment = sanitize_text_field($request['acf-pg-hidden-attachment']);
		$title = sanitize_text_field($request['title']);
		$caption = sanitize_textarea_field($request['caption']);

		unset( $request['acf-pg-hidden-field'] );
		unset( $request['acf-pg-hidden-post'] );
		unset( $request['acf-pg-hidden-attachment'] );
		unset( $request['action'] );
		unset( $request['acf-pg-hidden-nonce'] );
		unset( $request['title'] );
		unset( $request['caption'] );

		$acf_photo_gallery_editbox_caption_from_attachment = apply_filters( 'acf_photo_gallery_editbox_caption_from_attachment', $request);
		if( $acf_photo_gallery_editbox_caption_from_attachment == 1 ){
			$captionColumn = 'post_excerpt';
		} else {
			$captionColumn = 'post_content';
		}

		$post = array('ID' => $attachment, 'post_title' => $title, $captionColumn => $caption);
		wp_update_post( $post );

		foreach( $request as $name => $value ){
			$name = sanitize_text_field( $name );
			$value = sanitize_text_field( $value );

			if( !empty($value) ){
				update_post_meta( $attachment, $field . '_' . $name, $value);
			} else {
				delete_post_meta( $attachment, $field . '_' . $name);
			}
		}

	}
	die();
}
add_action( 'wp_ajax_acf_photo_gallery_edit_save', 'acf_photo_gallery_edit_save' );
