<?php
$attrs = array();
$attr = ' ';

$profile = $settings->profile;
$hashtag = $settings->hashtag_url;
$recipient_id = $settings->recipient_id;
$default_text = ( isset( $settings->default_text ) && ! empty( $settings->default_text ) ) ? rawurlencode( $settings->default_text ) : '';

$attrs['data-size'] 		= ( 'yes' == $settings->large_button ) ? 'large' : '';
if ( 'share' == $settings->button_type || 'mention' == $settings->button_type || 'hashtag' == $settings->button_type ) {
	$attrs['data-via'] 			= $settings->via;
	$attrs['data-text'] 		= $settings->share_text;
	$attrs['data-url'] 			= $settings->share_url;
}
$attrs['data-lang'] 		= get_locale();

if ( 'mention' == $settings->button_type ) {
	$attrs['data-show-count'] 	= ( 'yes' == $settings->show_count ) ? 'true' : 'false';
}

if ( 'message' == $settings->button_type ) {
	$attrs['data-screen-name'] 	= $profile;
}

foreach ( $attrs as $key => $value ) {
	$attr .= $key;
	if ( ! empty( $value ) ) {
		$attr .= '="' . $value . '"';
	}

	$attr .= ' ';
}

?>
<div class="pp-twitter-buttons" <?php echo $attr; ?>>
	<?php if ( 'share' == $settings->button_type ) { ?>
		<a href="https://twitter.com/share" class="twitter-share-button" <?php echo $attr; ?>>Tweet</a>
	<?php } elseif ( 'follow' == $settings->button_type ) { ?>
		<a href="https://twitter.com/<?php echo $profile; ?>" class="twitter-follow-button" <?php echo $attr; ?>>Tweet</a>
	<?php } elseif ( 'mention' == $settings->button_type ) { ?>
		<a href="https://twitter.com/intent/tweet?screen_name=<?php echo $profile; ?>" class="twitter-mention-button" <?php echo $attr; ?>>Tweet</a>
	<?php } elseif ( 'hashtag' == $settings->button_type ) { ?>
		<a href="https://twitter.com/intent/tweet?button_hashtag=<?php echo $hashtag; ?>" class="twitter-hashtag-button" <?php echo $attr; ?>>Tweet</a>
	<?php } else { ?>
		<a href="https://twitter.com/messages/compose?recipient_id=<?php echo $recipient_id; ?><?php echo ! empty( $default_text ) ? '&text=' . $default_text : ''; ?>" class="twitter-dm-button" <?php echo $attr; ?>>Message</a>
	<?php } ?>
</div>
