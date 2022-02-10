<?php

/**
 * Register, display and save an option with multiple checkboxes.
 *
 * This setting accepts the following arguments in its constructor function.
 *
 * $args = array(
 *		'id'			=> 'setting_id', 	// Unique id
 *		'title'			=> 'My Setting', 	// Title or label for the setting
 *		'description'	=> 'Description', 	// Help text description
 *		'options'		=> array(
 *							   'value' => 'Name'
 *						   ), 		// The checkbox values and text
 *		);
 * );
 *
 * @since 2.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingCheckbox_2_6_3 extends sapAdminPageSetting_2_6_3 {

	//public $sanitize_callback = 'sanitize_text_field';

	/**
	 * Display this setting
	 * @since 2.0
	 */
	public function display_setting() {
	
		$input_name = $this->get_input_name();
		$values = ( is_array( $this->value ) ? $this->value : array() );

		?>
		<fieldset <?php echo ( isset( $this->columns ) ? 'class="sap-setting-columns-' . $this->columns . '"' : '' ); ?> <?php $this->print_conditional_data(); ?>>
			<?php foreach ( $this->options as $id => $title  ) : ?> 
				<label title="<?php echo ( strpos( $title, '<' ) === false ? esc_attr( $title ) : ''); ?>" class="sap-admin-input-container">
					<input type="checkbox" name="<?php echo esc_attr( $input_name ); ?>[]" id="<?php echo esc_attr( $input_name . "-" . $id ); ?>" value="<?php echo esc_attr( $id ); ?>" <?php echo ( in_array($id, $values) ?  'checked="checked"' : '' ) ?> <?php echo ( $this->disabled ? 'disabled' : ''); ?> />
					<span class='sap-admin-checkbox'></span> <span><?php echo esc_html( $title ); ?></span>
				</label>
			<?php endforeach; ?>
			<?php $this->display_disabled(); ?>
		</fieldset>
		<?php

		$this->display_description();

	}

	public function sanitize_callback_wrapper( $values ) {

		return is_array( $values ) ? array_map( $this->sanitize_callback, $values ) : array();
	}

	/**
	 * Escape the value to display it safely HTML textarea fields
	 */
	public function esc_value( $values ) {

		$return = is_array( $values ) ? array_map( 'esc_attr', $values ) : $values;
		$return = is_string( $return ) ? esc_attr( $return ) : $return;

		return $return;
	}

}
