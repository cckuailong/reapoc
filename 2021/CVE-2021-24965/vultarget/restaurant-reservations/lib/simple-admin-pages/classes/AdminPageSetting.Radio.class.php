<?php

/**
 * Register, display and save an option with radio buttons.
 *
 * This setting accepts the following arguments in its constructor function.
 *
 * $args = array(
 *		'id'			=> 'setting_id', 	// Unique id
 *		'title'			=> 'My Setting', 	// Title or label for the setting
 *		'description'	=> 'Description', 	// Help text description
 *		'options'		=> array(
 *							   'value' => 'Name'
 *						   ), 		// The radio buttons values and text
 *		);
 * );
 *
 * @since 2.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingRadio_2_6_1 extends sapAdminPageSetting_2_6_1 {

	public $sanitize_callback = 'sanitize_text_field';

	/**
	 * Display this setting
	 * @since 2.0
	 */
	public function display_setting() {
	
		$input_name = $this->get_input_name();
		
		if ( empty( $this->value ) ) { $this->value = $this->get_default_setting(); }

		?>
		<fieldset <?php echo ( isset( $this->columns ) ? 'class="sap-setting-columns-' . $this->columns . '"' : '' ); ?> <?php $this->print_conditional_data(); ?>>
			<?php foreach ( $this->options as $id => $title  ) : ?>
				<label title="<?php echo ( strpos( $title, '<' ) === false ? $title : ''); ?>" class="sap-admin-input-container">
					<input type="radio" name="<?php echo $input_name; ?>" id="<?php echo $input_name . "-" . $id; ?>" value="<?php echo $id; ?>" <?php echo ( $id == $this->value ?  'checked="checked"' : '' ) ?> <?php echo ( $this->disabled ? 'disabled' : ''); ?> />
					<span class='sap-admin-radio-button'></span> <span><?php echo $title; ?></span>
				</label>
			<?php endforeach; ?>
			
			<?php $this->display_disabled(); ?>		
		</fieldset>
		<?php

		$this->display_description();

	}

}
