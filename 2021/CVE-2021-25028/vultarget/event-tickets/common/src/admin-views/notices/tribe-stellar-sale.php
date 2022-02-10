<?php
/**
 * The Stellar Sale admin notice.
 *
 * @since 4.14.2
 *
 * @var string $icon_url The local URL for the notice's image.
 * @var string $cta_url The short URL for black friday.
 */
?>
<div class="tribe-marketing-notice">
	<div class="tribe-marketing-notice__icon">
		<img src="<?php echo esc_url( $icon_url ); ?>"/>
	</div>
	<div class="tribe-marketing-notice__content">
		<h3>Save 40% on all our plugins, and all StellarWP brand products.</h3>
		<p>
			Now through August 4.
			<span class="tribe-marketing-notice__cta"><a target="_blank" href="<?php echo esc_url( $cta_url ); ?>">Shop now</a></span>
		</p>
	</div>
</div>
