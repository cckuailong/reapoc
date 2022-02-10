<?php
/**
 * Checkbox field displayed in plugin settings form.
 *
 * @var mixed[] $option Data of field.
 * @var string  $index  Index of field.
 * @package WebP Converter for Media
 */

?>
<?php if ( $option['info'] ) : ?>
	<p><?php echo wp_kses_post( $option['info'] ); ?></p>
<?php endif; ?>
<?php foreach ( $option['values'] as $value => $label ) : ?>
	<div class="webpField">
		<input type="checkbox"
			name="<?php echo esc_attr( $option['name'] ); ?>[]"
			value="<?php echo esc_attr( $value ); ?>"
			id="<?php echo esc_attr( $option['name'] . '-' . $value ); ?>"
			class="webpField__input webpField__input--checkbox"
			<?php echo ( in_array( $value, $option['disabled'] ) ) ? 'disabled' : ''; ?>
			<?php echo ( in_array( $value, $option['value'] ) ) ? 'checked' : ''; ?>
		>
		<label for="<?php echo esc_attr( $option['name'] . '-' . $value ); ?>"></label>
		<span class="webpField__label"><?php echo wp_kses_post( $label ); ?></span>
	</div>
<?php endforeach; ?>
