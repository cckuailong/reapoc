<?php
/**
 * Widget displayed information about about donation on plugin settings page.
 *
 * @package WebP Converter for Media
 */

?>
<div class="webpPage__widget">
	<h3 class="webpPage__widgetTitle webpPage__widgetTitle--second">
		<?php echo esc_html( __( 'I love what I do!', 'webp-converter-for-media' ) ); ?>
	</h3>
	<div class="webpContent">
		<p>
			<?php
			echo wp_kses_post(
				__( 'However, working on plugins and technical support requires many hours of work. If you want to appreciate it, you can provide me a coffee.', 'webp-converter-for-media' )
			);
			?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				__( 'If every plugin user did it, I could devote myself fully to working on this plugin. Thanks everyone!', 'webp-converter-for-media' )
			);
			?>
		</p>
		<p class="center">
			<a href="https://ko-fi.com/gbiorczyk/?utm_source=webp-converter-for-media&utm_medium=widget-donate"
				target="_blank"
				class="webpButton webpButton--blue dashicons-heart"
			>
				<?php echo esc_html( __( 'Provide me a coffee', 'webp-converter-for-media' ) ); ?>
			</a>
		</p>
	</div>
</div>
