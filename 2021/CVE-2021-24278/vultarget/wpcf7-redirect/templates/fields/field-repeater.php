<?php
/**
 * Render a repater field
 */

defined( 'ABSPATH' ) || exit;

$values                = isset( $field['value'] ) && ! empty( $field['value'] ) > 0 ? $field['value'] : array( 'default' );
$base_prefix           = $prefix;
$repeater_row_template = array();
?>

<?php ob_start(); ?>

<tr class="qs-repeater-row <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>" data-repeater-row-count="new_row">
	<?php $prefix = $base_prefix . '[' . $field['name'] . ']' . '[new_row]'; ?>
	<?php foreach ( $field['fields'] as $child_field ) : ?>
		<td>
			<?php
				$child_field['value'] = isset( $value[ $child_field['name'] ] ) ? $value[ $child_field['name'] ] : '';
				$child_field['label'] = '';
				WPCF7R_Html::render_field( $child_field, $prefix );
			?>
		</td>
	<?php endforeach; ?>
	<td>
		<div class="qs-repeater-action">
			<span class="dashicons dashicons-plus"></span>
			<span class="dashicons dashicons-minus"></span>
		</div>
	</td>
</tr>

<?php $repeater_single_row_template['template'] = ob_get_clean(); ?>

<div class="qs-row">
	<div class="qs-col qs-col-12">
		<?php if ( isset( $field['sub_title'] ) ) : ?>
			<h2><?php echo esc_html( $field['sub_title'] ); ?></h2>
		<?php endif; ?>
		<label for="wpcf7-redirect-<?php echo $field['name']; ?>">
			<h3><?php echo esc_html( $field['label'] ); ?> <?php echo isset( $field['tooltip'] ) ? cf7r_tooltip( $field['tooltip'] ) : ''; ?></h3>
		</label>
		<div>
			<div class="actions">
				<table class="wp-list-table widefat fixed striped pages repeater-table leads-list">
					<thead>
						<tr>
							<?php foreach ( $values as $value_row => $value ) : ?>
								<?php foreach ( $field['fields'] as $child_field ) : ?>
									<th class="manage-column column-primary sortable desc">
										<a href="#"><?php echo $child_field['label']; ?></a>
									</th>
								<?php endforeach; ?>
								<th><?php _e( 'Actions', 'wpcf7-redirect' ); ?></th>
								<?php break; ?>
							<?php endforeach; ?>
						</tr>
					</thead>

					<tbody id="the_list" data-repeater-template="<?php echo htmlspecialchars( wp_json_encode( $repeater_single_row_template ) ); ?>">
						<?php foreach ( $values as $value_row => $value ) : ?>
							<tr class="qs-repeater-row" data-repeater-row-count="<?php echo $value_row; ?>">
								<?php $prefix = $base_prefix . '[' . $field['name'] . ']' . '[' . $value_row . ']'; ?>
								<?php foreach ( $field['fields'] as $child_field ) : ?>
									<td>
										<?php
											$child_field['value'] = isset( $value[ $child_field['name'] ] ) ? $value[ $child_field['name'] ] : '';
											$child_field['label'] = '';
											WPCF7R_Html::render_field( $child_field, $prefix );
										?>
									</td>
								<?php endforeach; ?>
								<td>
									<div class="qs-repeater-action">
										<span class="dashicons dashicons-plus"></span>
										<span class="dashicons dashicons-minus"></span>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="field-footer">
			<?php echo isset( $field['footer'] ) ? $field['footer'] : ''; ?>
		</div>
	</div>
</div>
