<?php
/**
 * Template for generate withdraw options
 */


$withdraw_methods = $this->withdraw_methods;


?>

<div class="withdraw-admin-options-wrap">

	<ul class="withdraw-method-nav">
		<?php
		foreach ($withdraw_methods as $method_id => $method) {
			?>
			<li><a href="javascript:;" data-target-id="withdraw-method-<?php esc_attr_e( $method_id ); ?>-form"> <?php esc_html_e( $method['method_name'] ); ?> </a> </li>
			<?php
		}
		?>
	</ul>


	<?php
	$method_i = 0;

	foreach ($withdraw_methods as $method_id => $method){
		$method_i++;

		$is_enable = tutor_utils()->avalue_dot($method_id.".enabled", $this->get_options);
		?>

		<div id="withdraw-method-<?php esc_attr_e( $method_id ); ?>-form" class="withdraw-method-form-wrap" style="display: <?php esc_attr_e( $method_i ) == 1 ? 'block' : 'none'; ?>;">

			<div class="tutor-option-field-row">
				<div class="tutor-option-field-label">
					<label for=""><?php esc_html_e('Enable/Disable', 'tutor'); ?></label>
				</div>
				<div class="tutor-option-field">
					<label>
						<input type="checkbox" name="tutor_withdraw_options[<?php esc_attr_e( $method_id ); ?>][enabled]" value="1" <?php checked('1', $is_enable) ?> >
						<?php esc_html_e('Enable ', 'tutor'); ?> <?php esc_html_e( $method['method_name'] ); ?>
					</label>
				</div>
			</div>

			<?php

			if ( ! empty($method['admin_form_fields']) && tutor_utils()->count($method['admin_form_fields'])){
				$form_fields = $method['admin_form_fields'];

				foreach ($form_fields as $field_name => $field){
					$saved_value = tutor_utils()->avalue_dot($method_id.".".$field_name, $this->get_options);
					?>
					<div class="tutor-option-field-row">
						<?php
						if (isset($field['label'])){
							?>
							<div class="tutor-option-field-label">
								<label for=""><?php esc_html_e( $field['label'] ); ?></label>
							</div>
							<?php
						}
						?>
						<div class="tutor-option-field">
							<?php
							include tutor()->path."views/options/withdraw/{$field['type']}.php";

							if (isset($field['desc'])){
								echo '<p class="desc">' . esc_html( $field['desc'] ) . '</p>';
							}
							?>
						</div>
					</div>
					<?php
				}
			}
			?>


		</div>

		<?php
	}
	?>

</div>