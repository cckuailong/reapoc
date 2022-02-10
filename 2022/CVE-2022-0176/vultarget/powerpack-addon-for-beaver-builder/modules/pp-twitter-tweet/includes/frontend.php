<?php
$attrs = array();
$attr = ' ';

$url = esc_url( $settings->tweet_url );

$attrs['data-theme'] 	= $settings->theme;
$attrs['data-align'] 	= $settings->alignment;
$attrs['data-lang'] 	= get_locale();

if ( ! empty( $settings->width ) ) {
	$attrs['data-width'] = $settings->width;
}

if ( 'no' == $settings->expanded ) {
	$attrs['data-cards'] = 'hidden';
}

if ( isset( $settings->link_color ) && ! empty( $settings->link_color ) ) {
	$attrs['data-link-color'] = '#' . $settings->link_color;
}

foreach ( $attrs as $key => $value ) {

	if ( ! empty( $value ) ) {
		$attr .= $key . '="' . $value . '"';
	}

	$attr .= ' ';
}
?>

<div class="pp-twitter-tweet">
	<blockquote class="twitter-tweet" <?php echo $attr; ?>><a href="<?php echo $url; ?>"></a></blockquote>
</div>
