<?php
/**
 * Render a select field
 */

defined( 'ABSPATH' ) || exit;

$selected = selected( '1', '1', false );
$toggler  = isset( $field['toggler'] ) ? $field['name'] : false;
?>

<div class="field-wrap field-wrap-<?php echo $field['name']; ?> <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>" >
	<?php if ( $field['label'] ) : ?>
		<label for="wpcf7-redirect-<?php echo $field['name']; ?>">
			<strong><?php echo esc_html( $field['label'] ); ?></strong>
			<?php echo isset( $field['tooltip'] ) ? cf7r_tooltip( $field['tooltip'] ) : ''; ?>
		</label>
	<?php endif; ?>
	<select class="" <?php echo $toggler ? "data-toggler-name='{$toggler}'" : ''; ?> name="wpcf7-redirect<?php echo $prefix; ?>[<?php echo $field['name']; ?>]" >
		<?php foreach ( $field['options'] as $option_key => $option_label ) : ?>
			<?php
				$selected = isset( $field['value'] ) && $field['value'] ? selected( $field['value'], $option_key, false ) : $selected;
			?>
			<option value="<?php echo $option_key; ?>" <?php echo $selected; ?>><?php echo $option_label; ?></option>
			<?php $selected = ''; ?>
		<?php endforeach; ?>
	</select>
	<div class="field-footer">
		<?php echo isset( $field['footer'] ) ? $field['footer'] : ''; ?>
	</div>
</div>
