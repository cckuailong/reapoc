<?php

/**
 * Register, display and save an option with multiple checkboxes.
 *
 * This setting accepts the following arguments in its constructor function.
 *
 * $args = array(
 *		'id'			=> 'setting_id', 	// Unique id
 *		'title'			=> 'My Setting', 	// Title or label for the setting
 *		'add_label'		=> 'Add Row', 		// Text for the "Add Row" button
 *		'description'	=> 'Description', 	// Help text description
 *		'fields'		=> array(
 *		   'field' => array(
 * 				'type' => 'text' //text, select
 * 				'label' => 'Name'
 * 				'required' => false,
 *				'options' => array()
 * 			)
 *		) 		// The attributes and labels for the fields
 * );
 *
 * @since 2.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingInfiniteTable_2_6_3 extends sapAdminPageSetting_2_6_3 {

	public $sanitize_callback = 'sanitize_textarea_field';

	/**
	 * Add in the JS requried for rows to be added and the values to be stored
	 * @since 2.0
	 */
	public $scripts = array(
		'sap-infinite-table' => array(
			'path'			=> 'js/infinite_table.js',
			'dependencies'	=> array( 'jquery' ),
			'version'		=> SAP_VERSION,
			'footer'		=> true,
		),
	);

	/**
	 * Add in the CSS requried for rows to be displayed correctly
	 * @since 2.0
	 */
	public $styles = array(
		'sap-infinite-table' => array(
			'path'			=> 'css/infinite_table.css',
			'dependencies'	=> array( ),
			'version'		=> SAP_VERSION,
			'media'			=> 'all',
		),
	);

	/**
	 * Display this setting
	 * @since 2.0
	 */
	public function display_setting() {

		$input_name = $this->get_input_name();
		$values = json_decode( html_entity_decode( $this->value ) );

		if ( ! is_array( $values ) )
			$values = array();

		$fields = '';
		foreach ($this->fields as $field_id => $field) {
			$fields .= $field_id . ",";
		}
		$fields = trim($fields, ',');
		
		?>

		<fieldset <?php $this->print_conditional_data(); ?>>
			<div class='sap-infinite-table <?php echo ( $this->disabled ? 'disabled' : ''); ?>' data-fieldids='<?php echo esc_attr( $fields ); ?>'>
				<input type='hidden' id="sap-infinite-table-main-input" name='<?php echo esc_attr( $input_name ); ?>' value='<?php echo esc_attr( $this->value ); ?>' />
				<table>
					<thead>
						<tr>
							<?php foreach ($this->fields as $field) { ?>
								<th><?php echo esc_html( $field['label'] ); ?></th>
							<?php } ?>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($values as $row) { ?>
							<tr class='sap-infinite-table-row'>
								<?php foreach ($this->fields as $field_id => $field) { ?>
									<td data-field-type="<?php echo esc_attr( $field['type'] ); ?>" >
										<?php if ($field['type'] == 'id') : ?>
											<span class='sap-infinite-table-id-html'><?php echo esc_html( $row->$field_id ); ?></span>
											<input type='hidden' data-name='<?php echo esc_attr( $field_id ); ?>' value='<?php echo esc_attr( $row->$field_id ); ?>' />
										<?php endif; ?>
										<?php if ($field['type'] == 'text') : ?>
											<input type='text' data-name='<?php echo esc_attr( $field_id ); ?>' value='<?php echo esc_attr( $row->$field_id ); ?>' />
										<?php endif; ?>
										<?php if ($field['type'] == 'textarea') : ?>
											<textarea data-name='<?php echo esc_attr( $field_id ); ?>'><?php echo esc_textarea( $row->$field_id ); ?></textarea>
										<?php endif; ?>
										<?php if ($field['type'] == 'number') : ?>
											<input type='number' data-name='<?php echo esc_attr( $field_id ); ?>' value='<?php echo esc_attr( $row->$field_id ); ?>' />
										<?php endif; ?>
										<?php if ($field['type'] == 'hidden') : ?>
											<span class='sap-infinite-table-hidden-value'><?php echo esc_html( $row->$field_id ); ?></span>
											<input type='hidden' data-name='<?php echo esc_attr( $field_id ); ?>' value='<?php echo esc_attr( $row->$field_id ); ?>' />
										<?php endif; ?>
										<?php if ($field['type'] == 'select') : ?>
											<select data-name='<?php echo esc_attr( $field_id ); ?>'>
												<?php if ( ! empty( $field['blank_option'] ) ) { ?><option></option><?php } ?>
												<?php $this->print_options( $field['options'], $row, $field_id ); ?>
											</select>
										<?php endif; ?>
										<?php if ($field['type'] == 'toggle') : ?>
											<label class="sap-admin-switch">
												<input type="checkbox" class="sap-admin-option-toggle" data-name="<?php echo esc_attr( $field_id ); ?>" <?php if( $row->$field_id == '1' ) {echo "checked='checked'";} ?> >
												<span class="sap-admin-switch-slider round"></span>
											</label>
										<?php endif; ?>
									</td>
								<?php } ?>
								<td class='sap-infinite-table-row-delete'><?php echo esc_html( $this->del_label ); ?></td>
							</tr>
						<?php } ?>
					</tbody>
					<tfoot>
						<tr class='sap-infinite-table-row-template sap-hidden'>
							<?php foreach ($this->fields as $field_id => $field) { ?>
								<td data-field-type="<?php echo esc_attr( $field['type'] ); ?>" >
									<?php if ($field['type'] == 'id') : ?>
										<span class='sap-infinite-table-id-html'></span>
										<input type='hidden' data-name='<?php echo esc_attr( $field_id ); ?>' value='' />
									<?php endif; ?>
									<?php if ($field['type'] == 'text') : ?>
										<input type='text' data-name='<?php echo esc_attr( $field_id ); ?>' value='' />
									<?php endif; ?>
									<?php if ($field['type'] == 'textarea') : ?>
										<textarea data-name='<?php echo esc_attr( $field_id ); ?>'></textarea>
									<?php endif; ?>
									<?php if ($field['type'] == 'number') : ?>
										<input type='number' data-name='<?php echo esc_attr( $field_id ); ?>' value='' />
									<?php endif; ?>
									<?php if ($field['type'] == 'hidden') : ?>
										<span class='sap-infinite-table-hidden-value'></span>
										<input type='hidden' data-name='<?php echo esc_attr( $field_id ); ?>' value='' />
									<?php endif; ?>
									<?php if ($field['type'] == 'select') : ?>
										<select data-name='<?php echo esc_attr( $field_id ); ?>'>
											<?php if ( ! empty( $field['blank_option'] ) ) { ?><option></option><?php } ?>
											<?php $this->print_options( $field['options'] ); ?>
										</select>
									<?php endif; ?>
									<?php if ($field['type'] == 'toggle') : ?>
										<label class="sap-admin-switch">
											<input type="checkbox" class="sap-admin-option-toggle" data-name="<?php echo esc_attr( $field_id ); ?>" checked >
											<span class="sap-admin-switch-slider round"></span>
										</label>
									<?php endif; ?>
								</td>
							<?php } ?>
							<td class='sap-infinite-table-row-delete'>
								<?php echo wp_kses_post( $this->del_label ); ?>
							</td>
						</tr>
						<tr class='sap-infinite-table-add-row'>
							<td colspan="<?php echo count( $this->fields ) ?>">
								<a class="sap-new-admin-add-button">
									<?php echo wp_kses_post( $this->add_label ); ?>
								</a>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>

			<?php $this->display_disabled(); ?>
		</fieldset>

		<?php

		$this->display_description();

	}

	/**
	 * Recursively print out select options
	 * @since 2.5.3
	 */
	public function print_options( $options, $row = false, $field_id = 0 ) {

		foreach ( $options as $option_value => $option_name ) {

			if ( is_array( $option_name ) ) { ?>

				<optgroup label='<?php echo esc_attr( $option_value ); ?>'>
					<?php $this->print_options( $option_name, $row, $field_id ); ?>
				</optgroup>

				<?php

				continue;
			}

			$selected_value = $row ? $row->$field_id : false;

			?>

			<option value='<?php echo esc_attr( $option_value ); ?>' <?php echo ($selected_value == $option_value ? 'selected="selected"' : ''); ?>>
				<?php echo esc_html( $option_name ); ?>
			</option>
		
			<?php 
		}
	}

}
