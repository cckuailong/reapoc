<?php
$attrs = array();
$attr = ' ';

$user = $settings->username;

$attrs['data-theme'] 			= $settings->theme;
$attrs['data-show-replies'] 	= ( 'yes' == $settings->show_replies ) ? 'true' : 'false';

if ( ! empty( $settings->width ) ) {
	$attrs['data-width'] = $settings->width;
}
if ( ! empty( $settings->height ) ) {
	$attrs['data-height'] = $settings->height;
}
if ( isset( $settings->layout ) && ! empty( $settings->layout ) ) {
	$attrs['data-chrome'] = implode( ' ', $settings->layout );
}
if ( ! empty( $settings->tweet_limit ) && absint( $settings->tweet_limit ) ) {
	$attrs['data-tweet-limit'] = absint( $settings->tweet_limit );
}
if ( ! empty( $settings->link_color ) ) {
	$attrs['data-link-color'] 		= '#' . $settings->link_color;
}
if ( ! empty( $settings->border_color ) ) {
	$attrs['data-border-color'] 	= '#' . $settings->border_color;
}

foreach ( $attrs as $key => $value ) {

	if ( ! empty( $value ) ) {
		$attr .= $key . '="' . $value . '"';
	}

	$attr .= ' ';
}

?>
<div class="pp-twitter-timeline" <?php echo $attr; ?>>
	<a class="twitter-timeline" href="https://twitter.com/<?php echo $user; ?>" <?php echo $attr; ?>>Tweets by <?php echo $user; ?></a>
</div>
