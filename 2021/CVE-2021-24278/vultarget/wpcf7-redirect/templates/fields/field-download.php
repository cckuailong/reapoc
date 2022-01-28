<?php
/**
 * Display download field
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

	<a href="data:application/octet-stream;base64,<?php echo esc_html( $field['value'] ); ?>" download="<?php echo $field['filename']; ?>"><?php echo $field['filename']; ?></a>

	<div class="field-footer">
		<?php echo isset( $field['footer'] ) ? $field['footer'] : ''; ?>
	</div>
</div>
