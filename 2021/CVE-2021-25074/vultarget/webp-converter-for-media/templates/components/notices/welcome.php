<?php
/**
 * Notice displayed in admin panel.
 *
 * @var string $settings_url URL of plugin settings page (default view).
 * @package WebP Converter for Media
 */

?>
<div class="notice notice-success">
	<div class="webpContent webpContent--notice">
		<h4>
			<?php echo esc_html( __( 'Thank you for installing our plugin WebP Converter for Media!', 'webp-converter-for-media' ) ); ?>
		</h4>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
				/* translators: %1$s: br tag, %2$s: icon heart */
					__( 'Would you like to speed up your website using our plugin? %1$sGo to plugin settings and convert all your images to WebP with one click! Thank you for being with us! %2$s', 'webp-converter-for-media' ),
					'<br>',
					'<span class="dashicons dashicons-heart"></span>'
				)
			);
			?>
		</p>
		<div class="webpContent__buttons">
			<a href="<?php echo esc_url( $settings_url ); ?>"
				class="webpContent__button webpButton webpButton--green"
			>
				<?php echo esc_html( __( 'Speed up my website', 'webp-converter-for-media' ) ); ?>
			</a>
		</div>
	</div>
</div>
