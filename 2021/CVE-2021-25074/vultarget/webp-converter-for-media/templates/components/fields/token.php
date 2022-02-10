<?php
/**
 * Token field displayed in plugin settings form.
 *
 * @var string  $submit_activate_token   .
 * @var string  $submit_deactivate_token .
 * @var bool    $token_valid_status      .
 * @var string  $api_calculate_url       URL of REST API endpoint.
 * @var mixed[] $option                  Data of field.
 * @var string  $index                   Index of field.
 * @package WebP Converter for Media
 */

?>
<?php if ( $option['info'] ) : ?>
	<p><?php echo wp_kses_post( $option['info'] ); ?></p>
<?php endif; ?>
<div class="webpInput">
	<input type="text"
		name="<?php echo esc_attr( $option['name'] ); ?>"
		value="<?php echo esc_attr( $option['value'] ); ?>"
		id="<?php echo esc_attr( $option['name'] ); ?>"
		class="webpInput__field"
		<?php echo ( $token_valid_status ) ? 'readonly' : ''; ?>
	>
	<?php if ( ! $token_valid_status ) : ?>
		<button type="submit"
			name="<?php echo esc_attr( $submit_activate_token ); ?>"
			class="webpInput__button webpButton webpButton--green">
			<?php echo esc_html( __( 'Activate Token', 'webp-converter-for-media' ) ); ?>
		</button>
	<?php else : ?>
		<button type="submit"
			name="<?php echo esc_attr( $submit_deactivate_token ); ?>"
			class="webpInput__button webpButton webpButton--red">
			<?php echo esc_html( __( 'Deactivate Token', 'webp-converter-for-media' ) ); ?>
		</button>
	<?php endif; ?>
</div>
<p data-calculate-widget data-calculate-widget-api="<?php echo esc_url( $api_calculate_url ); ?>">
	<?php
	echo esc_html( __( 'How many maximum images to convert are on my website?', 'webp-converter-for-media' ) );
	echo ' ';
	echo wp_kses_post(
		sprintf(
		/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
			__( '%1$sCalculate%2$s', 'webp-converter-for-media' ),
			'<a href="#" data-calculate-widget-button>',
			'</a>'
		)
	);
	?>
	<strong data-calculate-widget-loading hidden>
		<?php echo esc_html( __( 'Please wait...', 'webp-converter-for-media' ) ); ?>
	</strong>
	<strong data-calculate-widget-output hidden></strong>
</p>
