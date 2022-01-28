<?php
/**
 * Render debug log by requested fields
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo $field['name']; ?> <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>">
	<div class="debug-log-wrap">
		<?php foreach ( $field['fields'] as $debug_field_name => $debug_field_value ) : ?>
			<div class="debug_log">
				<h4><?php echo $debug_field_name; ?>:</h4>
				<textarea rows="10">
				<?php
					$debug_field_value = maybe_unserialize( $debug_field_value );

				if ( is_array( $debug_field_value ) || is_object( $debug_field_value ) ) {
					print_r( $debug_field_value );
				} else {
					echo trim( esc_attr( $debug_field_value ) );
				}
				?>
				</textarea>
			</div>
		<?php endforeach; ?>
	</div>
</div>
