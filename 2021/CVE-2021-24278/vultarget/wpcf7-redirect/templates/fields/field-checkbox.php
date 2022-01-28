<?php
/**
 * Render checkbox field
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo $field['name']; ?> <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?> wpcf7r-checkbox" data-toggle="<?php echo isset( $field['show_selector'] ) ? $field['show_selector'] : ''; ?>">
	<label>
		<input type="checkbox" class="wpcf7-redirect-<?php echo $field['name']; ?>-fields"
		name="wpcf7-redirect<?php echo $prefix; ?>[<?php echo $field['name']; ?>]"
		<?php checked( $field['value'], 'on', true ); ?>
		data-toggle-label="<?php echo isset( $field['toggle-label'] ) ? esc_html( $field['toggle-label'] ) : ''; ?>"
		/>
		<span class="wpcf7r-on-off-button">
			<span class="wpcf7r-toggle-button">
			</span>
		</span>
		<strong class="checkbox-label"><?php echo esc_html( $field['label'] ); ?></strong>
		<?php echo isset( $field['tooltip'] ) ? cf7r_tooltip( $field['tooltip'] ) : ''; ?>
	</label>
</div>
