<?php
/**
 * Widget displayed information about plugin operation on plugin settings page.
 *
 * @package WebP Converter for Media
 */

?>
<div class="webpPage__widget">
	<h3 class="webpPage__widgetTitle webpPage__widgetTitle--second">
		<?php echo esc_html( __( 'How does this work?', 'webp-converter-for-media' ) ); ?>
	</h3>
	<div class="webpContent">
		<p>
			<?php
			echo wp_kses_post(
				__( 'By adding images to your media library, they are automatically converted and saved in a separate directory. Images are converted using GD or Imagick native extension for PHP.', 'webp-converter-for-media' )
			);
			?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				__( 'When the browser tries to download an image file, the server verifies if it supports image/webp files and if the file exists. If everything is OK, instead of the original image, the browser will receive its equivalent in WebP format.', 'webp-converter-for-media' )
			);
			?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				__( 'The plugin in default loading mode (via .htaccess) does not change file URLs, so there are no problems with saving the page to the cache and the page generation time does not increase.', 'webp-converter-for-media' )
			);
			?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				__( 'Image URLs are modified using the module mod_rewrite on the server, i.e. the same, thanks to which we can use friendly links in WordPress. Additionally, the MIME type of the sent file is modified to image/webp.', 'webp-converter-for-media' )
			);
			?>
		</p>
	</div>
</div>
