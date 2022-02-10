<?php

$attrs = array();
$attr = ' ';
$style = 'min-height:1px;';
$class = array( 'pp-facebook-widget' );

if ( 'comment' == $settings->embed_type ) {
	$class[] 						= 'fb-comment-embed';
	$attrs['data-href'] 			= $settings->comment_url;
	$attrs['data-include-parent'] 	= ( 'yes' == $settings->include_parent ) ? 'true' : 'false';
}

if ( 'post' == $settings->embed_type ) {
	$class[] 						= 'fb-post';
	$attrs['data-href'] 			= $settings->post_url;
	$attrs['data-show-text'] 		= ( 'yes' == $settings->show_text ) ? 'true' : 'false';
}

if ( 'video' == $settings->embed_type ) {
	$class[] 						= 'fb-video';
	$attrs['data-href'] 			= $settings->video_url;
	$attrs['data-show-text'] 		= ( 'yes' == $settings->show_text ) ? 'true' : 'false';
	$attrs['data-allowfullscreen'] 	= ( 'yes' == $settings->video_allowfullscreen ) ? 'true' : 'false';
	$attrs['data-autoplay'] 		= ( 'yes' == $settings->video_autoplay ) ? 'true' : 'false';
	$attrs['data-show-captions'] 	= ( 'yes' == $settings->show_captions ) ? 'true' : 'false';
}

if ( '' != $settings->width ) {
	$attrs['data-width'] 	= $settings->width;
}

foreach ( $attrs as $key => $value ) {
	$attr .= $key;
	if ( ! empty( $value ) ) {
		$attr .= '=' . $value;
	}

	$attr .= ' ';
}

?>

<div class="<?php echo implode( ' ', $class ); ?>" <?php echo $attr; ?> style="<?php echo $style; ?>">
	<blockquote class="fb-xfbml-parse-ignore"></blockquote>
</div>
