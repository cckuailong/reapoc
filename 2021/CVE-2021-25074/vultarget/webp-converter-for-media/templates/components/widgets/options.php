<?php
/**
 * Widget displayed settings form on plugin settings page.
 *
 * @var mixed[] $options                 Options of plugin settings.
 * @var string  $submit_value            Value of submit button.
 * @var string  $submit_activate_token   .
 * @var string  $submit_deactivate_token .
 * @var bool    $token_valid_status      .
 * @var string  $settings_debug_url      URL of plugin settings page (debug view).
 * @var string  $api_calculate_url       URL of REST API endpoint.
 * @package WebP Converter for Media
 */

?>
<div class="webpPage__widget">
	<h3 class="webpPage__widgetTitle">
		<?php echo esc_html( __( 'Settings', 'webp-converter-for-media' ) ); ?>
	</h3>
	<div class="webpContent">
		<?php foreach ( $options as $index => $option ) : ?>
			<div class="webpPage__widgetRow">
				<ul class="webpPage__widgetColumns">
					<li class="webpPage__widgetColumn">
						<h4><?php echo esc_html( $option['label'] ); ?></h4>
						<?php include dirname( __DIR__ ) . '/fields/' . $option['type'] . '.php'; ?>
					</li>
					<?php if ( $option['notice_lines'] ) : ?>
						<li class="webpPage__widgetColumn">
							<div class="webpPage__widgetColumnNotice">
								<?php foreach ( $option['notice_lines'] as $line ) : ?>
									<p><?php echo wp_kses_post( $line ); ?></p>
								<?php endforeach; ?>
							</div>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		<?php endforeach; ?>
		<div class="webpPage__widgetRow">
			<button type="submit" name="<?php echo esc_attr( $submit_value ); ?>"
				class="webpButton webpButton--green"
			>
				<?php echo esc_html( __( 'Save Changes', 'webp-converter-for-media' ) ); ?>
			</button>
		</div>
		<div class="webpPage__widgetRow">
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
					/* translators: %1$s: open anchor tag, %2$s: close anchor tag, %3$s: open anchor tag, %4$s: close anchor tag, %5$s: open anchor tag, %6$s: close anchor tag */
						__( 'If you have a problem %1$scheck our FAQ%2$s first. If you did not find help there, please %3$scheck support forum%4$s for any similar problem or contact us. Before you contact us, %5$scheck the configuration%6$s of your server.', 'webp-converter-for-media' ),
						'<a href="https://wordpress.org/plugins/webp-converter-for-media/#faq" target="_blank">',
						'</a>',
						'<a href="https://wordpress.org/support/plugin/webp-converter-for-media/" target="_blank">',
						'</a>',
						'<a href="' . $settings_debug_url . '">',
						'</a>'
					)
				);
				?>
			</p>
		</div>
	</div>
</div>
