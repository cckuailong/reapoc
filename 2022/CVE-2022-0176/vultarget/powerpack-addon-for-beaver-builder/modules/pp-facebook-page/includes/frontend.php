<?php
if ( ! empty( $settings->page_url ) ) {

	$attrs = array();
	$attr = ' ';
	$style = array( 'min-height:1px;' );

	$attrs['data-href'] 			= $settings->page_url;
	$attrs['data-tabs'] 			= implode( ',', $settings->layout );
	$attrs['data-width'] 			= $settings->width;
	$attrs['data-height'] 			= $settings->height;
	$attrs['data-small-header'] 	= ( 'yes' == $settings->small_header ) ? 'true' : 'false';
	$attrs['data-hide-cover'] 		= ( 'yes' == $settings->cover ) ? 'false' : 'true';
	$attrs['data-show-facepile'] 	= ( 'yes' == $settings->profile_photos ) ? 'true' : 'false';
	$attrs['data-hide-cta'] 		= ( 'yes' == $settings->cta ) ? 'false' : 'true';

	$style[] = 'height:' . $settings->height . 'px';

	foreach ( $attrs as $key => $value ) {
		$attr .= $key;
		if ( ! empty( $value ) ) {
			$attr .= '=' . $value;
		}

		$attr .= ' ';
	}

	?>

	<div class="pp-facebook-widget fb-page" <?php echo $attr; ?> style="<?php echo implode( ' ', $style ); ?>">
		<blockquote cite="<?php echo $settings->page_url; ?>" class="fb-xfbml-parse-ignore"><a href="<?php echo $settings->page_url; ?>"></a></blockquote>
	</div>

<?php } ?>