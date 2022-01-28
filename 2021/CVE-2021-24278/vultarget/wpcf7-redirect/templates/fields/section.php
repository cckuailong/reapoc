<?php
/**
 * Wrapper for each field
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo $field['name']; ?> <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?> qs-section clearfix" <?php echo isset( $field['attr'] ) ? $field['attr'] : ''; ?>>
	<?php if ( isset( $field['title'] ) && $field['title'] ) : ?>
		<label for="wpcf7-redirect-<?php echo $field['name']; ?>">
			<h3 class="" data-tab-target="section-inner-wrap-<?php echo $field['name']; ?>"><span class="dashicons dashicons-plus-alt"></span> <?php echo esc_html( $field['title'] ); ?></h3>
		</label>
	<?php endif; ?>

	<div class="section-inner-wrap" data-tab="section-inner-wrap-<?php echo $field['name']; ?>">
		<?php if ( isset( $field['sub_title'] ) && $field['sub_title'] ) : ?>
			<label for="wpcf7-redirect-<?php echo $field['name']; ?>">
				<h4><?php echo $field['sub_title']; ?></h4>
			</label>
		<?php endif; ?>
		<div>&nbsp;</div>

		<?php foreach ( $field['fields'] as $child_field ) : ?>
			<?php WPCF7R_Html::render_field( $child_field, $prefix ); ?>
		<?php endforeach; ?>

		<div class="field-footer">
			<?php echo isset( $field['footer'] ) ? $field['footer'] : ''; ?>
		</div>
	</div>
</div>
