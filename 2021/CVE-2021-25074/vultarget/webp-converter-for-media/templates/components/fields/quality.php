<?php
/**
 * Quality steps field displayed in plugin settings form.
 *
 * @var mixed[] $option Data of field.
 * @var string  $index  Index of field.
 * @package WebP Converter for Media
 */

?>
<?php if ( $option['info'] ) : ?>
	<p><?php echo wp_kses_post( $option['info'] ); ?></p>
<?php endif; ?>
<div class="webpPage__quality">
	<?php foreach ( $option['values'] as $value => $label ) : ?>
		<div class="webpPage__qualityItem">
			<input type="radio"
				name="<?php echo esc_attr( $option['name'] ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				id="webpc-<?php echo esc_attr( $index ); ?>-<?php echo esc_attr( $value ); ?>"
				class="webpPage__qualityItemInput"
				<?php echo ( $value == $option['value'] ) ? 'checked' : ''; // phpcs:ignore  ?>
				<?php echo ( in_array( $value, $option['disabled'] ) ) ? 'disabled' : ''; ?>>
			<label for="webpc-<?php echo esc_attr( $index ); ?>-<?php echo esc_attr( $value ); ?>"
				class="webpPage__qualityItemLabel"
			>
				<?php echo wp_kses_post( $label ); ?>
			</label>
		</div>
	<?php endforeach; ?>
</div>
