<?php
/**
 * Render editor field
 */

defined( 'ABSPATH' ) || exit;

$field_name = "wpcf7-redirect{$prefix}[{$field['name']}]";
$css_class  = "wpcf7-redirect-{$field['name']}-fields";
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

	<?php
		wp_editor(
			$field['value'],
			'editor-' . md5( $field_name ),
			$settings = array(
				'textarea_name' => $field_name,
				'editor_class'  => $css_class,
			)
		);
		?>

	<div class="field-footer">
		<?php echo isset( $field['footer'] ) ? $field['footer'] : ''; ?>
	</div>

</div>
