<?php
/**
 * Notice displayed in admin panel.
 *
 * @var string $ajax_url URL of admin-ajax.
 * @package WebP Converter for Media
 */

?>
<div class="notice notice-success is-dismissible"
	data-notice="webp-converter-for-media"
	data-url="<?php echo esc_url( $ajax_url ); ?>"
>
	<div class="webpContent webpContent--notice">
		<h4>
			<?php echo esc_html( __( 'Thank you for using our plugin WebP Converter for Media!', 'webp-converter-for-media' ) ); ?>
		</h4>
		<p>
			<?php
			echo wp_kses_post(
				__( 'We are glad that you are using our plugin and we hope you are satisfied with it. If you want, you can support us in the development of the plugin by providing us a coffee and adding a plugin review. This is very important and gives us the opportunity to create even better tools for you. Thank you to everyone.', 'webp-converter-for-media' )
			);
			?>
		</p>
		<div class="webpContent__buttons">
			<a href="https://ko-fi.com/gbiorczyk/?utm_source=webp-converter-for-media&utm_medium=notice-thanks"
				target="_blank"
				class="webpContent__button webpButton webpButton--green dashicons-heart"
			>
				<?php echo esc_html( __( 'Provide us a coffee', 'webp-converter-for-media' ) ); ?>
			</a>
			<a href="https://wordpress.org/support/plugin/webp-converter-for-media/reviews/?rate=5#new-post"
				target="_blank"
				class="webpContent__button webpButton webpButton--green"
			>
				<?php echo esc_html( __( 'Add plugin review', 'webp-converter-for-media' ) ); ?>
			</a>
			<button type="button" data-permanently
				class="webpContent__button webpButton webpButton--blue"
			>
				<?php echo esc_html( __( 'Hide, do not show again', 'webp-converter-for-media' ) ); ?>
			</button>
		</div>
	</div>
</div>
