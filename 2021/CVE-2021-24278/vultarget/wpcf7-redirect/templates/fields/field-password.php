<?php
/**
 * Render a password field
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo $field['name']; ?> <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>">
	<?php if ( isset( $field['label'] ) && $field['label'] ) : ?>
		<label for="wpcf7-redirect-<?php echo $field['name']; ?>">
			<strong><?php echo esc_html( $field['label'] ); ?></strong>
			<?php echo isset( $field['tooltip'] ) ? cf7r_tooltip( $field['tooltip'] ) : ''; ?>
		</label>
	<?php endif; ?>

	<?php if ( isset( $field['sub_title'] ) && $field['sub_title'] ) : ?>
		<div class="wpcf7-subtitle">
			<?php echo $field['sub_title']; ?>
		</div>
	<?php endif; ?>

	<input type="password" class="wpcf7-redirect-<?php echo $field['name']; ?>-fields <?php echo isset( $field['input_class'] ) ? $field['input_class'] : ''; ?>" placeholder="<?php echo esc_html( $field['placeholder'] ); ?>" name="wpcf7-redirect<?php echo $prefix; ?>[<?php echo $field['name']; ?>]" value="<?php echo esc_html( $field['value'] ); ?>" <?php echo isset( $field['input_attr'] ) ? $field['input_attr'] : ''; ?>>

	<div class="field-footer">
		<?php echo isset( $field['footer'] ) ? $field['footer'] : ''; ?>
	</div>
</div>
