<?php
/**
 * Widget displayed information about support forum on plugin settings page.
 *
 * @var string $settings_debug_url URL of plugin settings page (debug view).
 * @package WebP Converter for Media
 */

?>
<div class="webpPage__widget">
	<h3 class="webpPage__widgetTitle webpPage__widgetTitle--second">
		<?php echo esc_html( __( 'We are waiting for your message', 'webp-converter-for-media' ) ); ?>
	</h3>
	<div class="webpContent">
		<p>
			<?php
			echo wp_kses_post(
				__( 'Do you have a technical problem? Please contact us. We will be happy to help you. Or maybe you have an idea for a new feature? Please let us know about it by filling the support form. We will try to add it!', 'webp-converter-for-media' )
			);
			?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
				/* translators: %1$s: open anchor tag, %2$s: close anchor tag, %3$s: open anchor tag, %4$s: close anchor tag */
					__( 'Please %1$scheck our FAQ%2$s before adding a thread with technical problem. If you do not find help there, %3$scheck support forum%4$s for similar problems. Before you contact us check the configuration of your server and attach it in your message, e.g. as a screenshot.', 'webp-converter-for-media' ),
					'<a href="https://wordpress.org/plugins/webp-converter-for-media/#faq" target="_blank">',
					'</a>',
					'<a href="https://wordpress.org/support/plugin/webp-converter-for-media/" target="_blank">',
					'</a>'
				)
			);
			?>
		</p>
		<p class="center">
			<a href="<?php echo esc_url( $settings_debug_url ); ?>"
				class="webpButton webpButton--blue dashicons-admin-tools"
			>
				<?php echo esc_html( __( 'Server configuration', 'webp-converter-for-media' ) ); ?>
			</a>
			<br>
			<a href="https://wordpress.org/support/plugin/webp-converter-for-media/" target="_blank"
				class="webpButton webpButton--blue"
			>
				<?php echo esc_html( __( 'Get help', 'webp-converter-for-media' ) ); ?>
			</a>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				__( 'Do you like our plugin? Could you rate him? Please let us know what you think about our plugin. It is important that we can develop this tool. Thank you for all the ratings, reviews and donates.', 'webp-converter-for-media' )
			);
			?>
		</p>
		<p class="center">
			<a href="https://wordpress.org/support/plugin/webp-converter-for-media/reviews/?rate=5#new-post"
				target="_blank" class="webpButton webpButton--blue"
			>
				<?php echo esc_html( __( 'Add review', 'webp-converter-for-media' ) ); ?>
			</a>
		</p>
	</div>
</div>
