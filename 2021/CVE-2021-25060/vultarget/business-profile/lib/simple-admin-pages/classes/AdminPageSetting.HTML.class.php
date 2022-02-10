<?php

/**
 * Register and save an arbitrary HTML chunk in the admin menu
 *
 * This allows you to easily add in a dummy "setting" with any arbitrary HTML
 * code. It's good for displaying a link to documentation, upgrades or anything
 * else you can think of.
 *
 * Data in this field will not be saved or passed. It's purely for presenting
 * information.
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingHTML_2_6_3 extends sapAdminPageSetting_2_6_3 {

	public $sanitize_callback = 'sanitize_text_field';

	/**
	 * Display this setting
	 * @since 1.0
	 */
	public function display_setting() {

		?>

        <fieldset <?php $this->print_conditional_data(); ?>>
            <?php echo wp_kses_post( $this->html ); ?>
        </fieldset>

        <?php

		$this->display_description();

	}

}
