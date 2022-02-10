<?php

/**
 * Register, display and save a warning/tip in the admin menu
 *
 * @since 2.3.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingWarningTip_2_6_1 extends sapAdminPageSetting_2_6_1 {

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

		<fieldset class="fdm-warning-tip" <?php $this->print_conditional_data(); ?>>
			<div class="fdm-shortcode-reminder">
				<?php echo '<strong>' . $this->title . '</strong> ' . $this->placeholder; ?>
			</div>

			<?php $this->display_disabled(); ?>		
		</fieldset>
		
		<?php
		
		$this->display_description();
		
	}

}
