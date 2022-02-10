<?php

/**
 * Register, display and save a selection option with a drop-down menu.
 *
 * This setting accepts the following arguments in its constructor function.
 *
 * $args = array(
 *		'id'			=> 'setting_id', 	// Unique id
 *		'title'			=> 'My Setting', 	// Title or label for the setting
 *		'description'	=> 'Description', 	// Help text description
 *		'blank_option'	=> true, 			// Whether or not to show a blank option
 *		'options'		=> array(			// An array of key/value pairs which
 *			'option1'	=> 'Option 1',		//	define the options.
 *			'option2'	=> 'Option 2',
 *			...
 *		);
 * );
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingSelect_2_6_3 extends sapAdminPageSetting_2_6_3 {

	public $sanitize_callback = 'sanitize_text_field';

	// Whether or not to display a blank option
	public $blank_option = true;

	// An array of options for this select field, accepted as a key/value pair.
	public $options = array();

	/**
	 * Display this setting
	 * @since 1.0
	 */
	public function display_setting() {

		?>

		<fieldset <?php $this->print_conditional_data(); ?>>

			<select name="<?php echo esc_attr( $this->get_input_name() ); ?>" id="<?php echo esc_attr( $this->id ); ?>" <?php echo ( $this->disabled ? 'disabled' : ''); ?>>

				<?php if ( $this->blank_option === true ) : ?>
					<option></option>
				<?php endif; ?>

				<?php foreach ( $this->options as $id => $title  ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>"<?php if( $this->value == $id ) : ?> selected="selected"<?php endif; ?>><?php echo esc_html( $title ); ?></option>
				<?php endforeach; ?>

			</select>
			<?php $this->display_disabled(); ?>	

		</fieldset>

		<?php

		$this->display_description();

	}

}
