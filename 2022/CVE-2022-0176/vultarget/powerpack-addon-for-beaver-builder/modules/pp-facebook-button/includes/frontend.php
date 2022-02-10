<?php
$attrs = array();
$attr = ' ';
$style = 'min-height:1px';

$class = array( 'pp-facebook-widget' );

$attrs['data-layout'] 			= $settings->layout;
$attrs['data-colorscheme'] 		= $settings->color_scheme;
$attrs['data-size'] 			= $settings->size;
$attrs['data-show-faces'] 		= ( 'yes' == $settings->show_faces ) ? 'true' : 'false';

if ( 'like' == $settings->button_type || 'recommend' == $settings->button_type ) {
	if ( 'current_page' == $settings->url_type ) {
		$permalink			= get_permalink();
	} else {
		$permalink			= esc_url( $settings->url );
	}

	$attrs['data-href']	= $permalink;
	$attrs['data-share'] = ( 'yes' == $settings->show_share ) ? 'true' : 'false';
	$attrs['data-action'] = $settings->button_type;

	$class[] = 'fb-like';
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
</div>
