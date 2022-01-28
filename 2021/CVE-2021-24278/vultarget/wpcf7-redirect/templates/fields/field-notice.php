<?php
/**
 * Render notice block
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap">
	<div class="field-notice field-wrap-<?php echo $field['name']; ?> <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>">
		<strong>
			<?php echo $field['label']; ?>
		</strong>
		<?php echo $field['sub_title']; ?>
	</div>

</div>
