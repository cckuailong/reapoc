<?php
/**
 * Widget displayed errors on plugin settings page.
 *
 * @var string[][] $errors_messages .
 * @var string[]   $errors_codes    .
 * @package WebP Converter for Media
 */

?>
<?php if ( $errors_messages ) : ?>
	<div class="webpPage__widget">
		<h3 class="webpPage__widgetTitle webpPage__widgetTitle--error">
			<?php echo esc_html( __( 'Server configuration error', 'webp-converter-for-media' ) ); ?>
		</h3>
		<div class="webpContent webpContent--wide">
			<?php foreach ( $errors_messages as $error_index => $error_lines ) : ?>
				<?php if ( $error_index > 0 ) : ?>
					<p>---</p>
				<?php endif; ?>
				<?php foreach ( $error_lines as $error_line ) : ?>
					<p><?php echo wp_kses_post( $error_line ); ?></p>
				<?php endforeach; ?>
			<?php endforeach; ?>
			<p>---</p>
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
					/* translators: %1$s: open strong tag, %2$s: close strong tag, %3$s: errors codes */
						__( '%1$sError codes:%2$s %3$s', 'webp-converter-for-media' ),
						'<strong>',
						'</strong>',
						implode( ', ', $errors_codes )
					)
				);
				?>
			</p>
		</div>
	</div>
<?php endif; ?>
