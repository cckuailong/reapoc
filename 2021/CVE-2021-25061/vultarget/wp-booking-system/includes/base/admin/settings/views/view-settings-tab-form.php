<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>


<?php

	/**
	 * Hook to add extra fields at the top of the Form Tab
	 *
	 * @param array $settings
	 *
	 */
	do_action( 'wpbs_submenu_page_settings_tab_form_top', $settings );

?>

<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-medium">
	<label class="wpbs-settings-field-label" for="form_styling">
		<?php echo __( 'Form Styling', 'wp-booking-system' ); ?>
		<?php echo wpbs_get_output_tooltip(__("By default we use our own styling to make sure everything looks fine. You can select 'Theme Styling' to disable the output of our custom CSS.", 'wp-booking-system'));?>
	</label>

	<div class="wpbs-settings-field-inner">
		<select name="wpbs_settings[form_styling]" id="form_styling">
			<option value="default" <?php echo isset($settings['form_styling']) ? selected($settings['form_styling'], 'default', false) : '';?> ><?php echo __('Plugin Styling','wp-booking-system') ?></option>
			<option value="theme" <?php echo isset($settings['form_styling']) ? selected($settings['form_styling'], 'theme', false) : '';?>><?php echo __('Theme Styling','wp-booking-system') ?></option>
		</select>
		
	</div>
</div>

<!-- reCAPTCHA Keys -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-heading wpbs-settings-field-large">
	<label class="wpbs-settings-field-label"><?php echo __( 'reCAPTCHA v2 Tickbox', 'wp-booking-system' ); ?></label>
    <div class="wpbs-settings-field-inner">&nbsp;</div>
</div>

<!-- reCAPTCHA Keys -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline  wpbs-settings-field-large">

	<label class="wpbs-settings-field-label" for="recaptcha_v2_site_key"><?php echo __( 'Site Key', 'wp-booking-system' ); ?></label>

	<div class="wpbs-settings-field-inner">
		<input name="wpbs_settings[recaptcha_v2_site_key]" id="recaptcha_v2_site_key" type="text" value="<?php echo ( ! empty( $settings['recaptcha_v2_site_key'] ) ? esc_attr( $settings['recaptcha_v2_site_key'] ) : '' ); ?>" />
	</div>
	
</div>

<!-- reCAPTCHA Keys -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline wpbs-settings-field-large">

	<label class="wpbs-settings-field-label" for="recaptcha_v2_secret_key"><?php echo __( 'Secret Key', 'wp-booking-system' ); ?></label>

	<div class="wpbs-settings-field-inner">
		<input name="wpbs_settings[recaptcha_v2_secret_key]" id="recaptcha_v2_secret_key" type="text" value="<?php echo ( ! empty( $settings['recaptcha_v2_secret_key'] ) ? esc_attr( $settings['recaptcha_v2_secret_key'] ) : '' ); ?>" />
	</div>
	
</div>


<?php

	/**
	 * Hook to add extra fields at the bottom of the Form Tab
	 *
	 * @param array $settings
	 *
	 */
	do_action( 'wpbs_submenu_page_settings_tab_form_bottom', $settings );

?>

<!-- Submit button -->
<input type="submit" class="button-primary" value="<?php echo __( 'Save Settings', 'wp-booking-system' ); ?>" />