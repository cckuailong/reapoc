<?php
/**
 * Render json editor field
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo $field['name']; ?> <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>">
	<label for="wpcf7-redirect-<?php echo $field['name']; ?>">
		<strong><?php echo esc_html( $field['label'] ); ?></strong>
	</label>
	<?php if ( isset( $field['sub_title'] ) && $field['sub_title'] ) : ?>
		<div class="wpcf7-subtitle">
			<?php echo $field['sub_title']; ?>
		</div>
	<?php endif; ?>
	<textarea rows="10" class="json-container wpcf7-redirect-<?php echo $field['name']; ?>-fields" placeholder="<?php echo $field['placeholder']; ?>" name="wpcf7-redirect<?php echo $prefix; ?>[<?php echo $field['name']; ?>]"><?php echo esc_html( $field['value'] ); ?></textarea>
	<div class="field-footer">
		<?php echo isset( $field['footer'] ) ? $field['footer'] : ''; ?>
	</div>
</div>
