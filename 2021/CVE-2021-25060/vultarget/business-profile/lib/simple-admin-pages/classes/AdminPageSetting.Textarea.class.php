<?php

/**
 * Register, display and save a textarea field setting in the admin menu
 *
 * @since 1.0
 * @package Simple Admin Pages
 *
 * @todo textareas should have an option to swap new lines for <br>s
 */

class sapAdminPageSettingTextarea_2_6_3 extends sapAdminPageSetting_2_6_3 {

	/*
	 * Size of this textarea
	 *
	 * This is put directly into a css class [size]-text,
	 * and setting this to 'large' will link into WordPress's existing textarea
	 * style for full-width textareas.
	 */
	public $size = 'small';

	public $sanitize_callback = 'wp_kses_post';

	/**
	 * Escape the value to display it safely HTML textarea fields
	 * @since 1.0
	 */
	public function esc_value( $val ) {
		return esc_textarea( $val );
	}

	/**
	 * Set the size of this textarea field
	 * @since 1.0
	 */
	public function set_size( $size ) {
		$this->size = esc_attr( $size );
	}

	/**
	 * Display this setting
	 * @since 1.0
	 */
	public function display_setting() {
		?>

		<fieldset <?php $this->print_conditional_data(); ?>>
			<textarea name="<?php echo esc_attr( $this->get_input_name() ); ?>" id="<?php echo esc_attr( $this->get_input_name() ); ?>" class="<?php echo esc_attr( $this->size ); ?>-text"<?php echo !empty( $this->placeholder ) ? ' placeholder="' . esc_attr( $this->placeholder ) . '"' : ''; ?> <?php echo ( $this->disabled ? 'disabled' : ''); ?> <?php $this->print_conditional_data(); ?>><?php echo esc_textarea( $this->value ); ?></textarea>

			<?php $this->display_disabled(); ?>
		</fieldset>

		<?php
		$this->display_description();
	}

}
