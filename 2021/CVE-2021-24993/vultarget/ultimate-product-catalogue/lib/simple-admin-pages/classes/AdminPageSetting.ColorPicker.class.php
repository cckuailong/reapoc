<?php

/**
 * Register, display and save a color picker field setting in the admin menu
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingColorPicker_2_6_1 extends sapAdminPageSetting_2_6_1 {

	public $sanitize_callback = 'sanitize_text_field';

	/**
	 * Placeholder string for the input field
	 * @since 2.0
	 */
	public $placeholder = '';

	/**
	 * Display this setting
	 * @since 1.0
	 */
	public function display_setting() {
		?>

		<fieldset class="sap-colorpicker" <?php $this->print_conditional_data(); ?>>
			<input class="sap-spectrum" name="<?php echo $this->get_input_name(); ?>" type="text" id="<?php echo $this->get_input_name(); ?>" value="<?php echo $this->value; ?>"<?php echo !empty( $this->placeholder ) ? ' placeholder="' . esc_attr( $this->placeholder ) . '"' : ''; ?> class="regular-text" <?php echo ( $this->disabled ? 'disabled' : ''); ?> />
			
			<?php $this->display_disabled(); ?>		
		</fieldset>
		
		<?php
		
		$this->display_description();
		
	}

}
