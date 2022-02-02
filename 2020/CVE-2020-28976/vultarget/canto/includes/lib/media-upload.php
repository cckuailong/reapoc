<?php
/**
 * @package Netword_Shared_Media
 * @version 0.10.1
 */
define('WP_ADMIN', FALSE);
define('WP_LOAD_IMPORTERS', FALSE);

require_once( dirname( dirname( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) ) ) . '/wp-admin/admin.php' );

if (!current_user_can('upload_files'))
	wp_die(__('You do not have permission to upload files.'));

// $blog_id is global var in WP

if( isset( $_POST['send'] ) ) {
	$nsm_blog_id = (int) $_GET['blog_id'];
	reset( $_POST['send'] );
	$nsm_send_id = (int) key( $_POST['send'] );
}

/* copied from wp-admin/inculdes/ajax-actions.php wp_ajax_send_attachment_to_editor() */
if ( isset( $nsm_blog_id ) && isset( $nsm_send_id ) ) {
	//switch_to_blog( $nsm_blog_id );
	if (!current_user_can('upload_files')) {
		$current_blog_name = get_bloginfo('name');
		restore_current_blog();
		wp_die(__('You do not have permission to upload files to site: ')  . $current_blog_name );
	}

	global $post;

	$attachment = wp_unslash( $_POST['attachments'][$nsm_send_id] );
	$id = $nsm_send_id;

	if ( ! $post = get_post( $id ) )
		wp_send_json_error();

	if ( 'attachment' != $post->post_type )
		wp_send_json_error();

	$rel = $url = '';
	$html = $title = isset( $attachment['post_title'] ) ? $attachment['post_title'] : '';
	if ( ! empty( $attachment['url'] ) ) {
		$url = $attachment['url'];
		if ( strpos( $url, 'attachment_id') || get_attachment_link( $id ) == $url )
			$rel = ' rel="attachment wp-att-' . $id . '"';
		$html = '<a href="' . esc_url( $url ) . '"' . $rel . '>' . $html . '</a>';
	}

	if ( 'image' === substr( $post->post_mime_type, 0, 5 ) ) {
		$align = isset( $attachment['align'] ) ? $attachment['align'] : 'none';
		$size = isset( $attachment['image-size'] ) ? $attachment['image-size'] : 'medium';
		$alt = isset( $attachment['image_alt'] ) ? $attachment['image_alt'] : '';
		$caption = isset( $attachment['post_excerpt'] ) ? $attachment['post_excerpt'] : '';
		$title = ''; // We no longer insert title tags into <img> tags, as they are redundant.
		$html = get_image_send_to_editor( $id, $caption, $title, $align, $url, (bool) $rel, $size, $alt );
	} elseif ( 'video' === substr( $post->post_mime_type, 0, 5 ) || 'audio' === substr( $post->post_mime_type, 0, 5 ) ) {
		global $wp_embed;
		$meta = get_post_meta( $id, '_wp_attachment_metadata', true );
		$html = $wp_embed->shortcode( $meta, $url );
	}

	/** This filter is documented in wp-admin/includes/media.php */
	$html = apply_filters( 'media_send_to_editor', $html, $id, $attachment );

	// replace wp-image-<id>, wp-att-<id> and attachment_<id>
	$html = preg_replace(
		array(
			'#(caption id="attachment_)(\d+")#', // mind the quotes!
			'#(wp-image-|wp-att-)(\d+)#'
		),
		array(
			sprintf('${1}nsm_%s_${2}', esc_attr($nsm_blog_id)),
			sprintf('${1}nsm-%s-${2}', esc_attr($nsm_blog_id)),
		),
		$html
	);

	if( isset($_POST['chromeless']) && $_POST['chromeless'] ) {
		// WP3.5+ media browser is identified by the 'chromeless' parameter
		exit($html);
	} else {
		return media_send_to_editor($html);
	}
}
