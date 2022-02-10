<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>


<?php

	/**
	 * Hook to add extra fields at the top of the General Tab
	 *
	 * @param array $settings
	 *
	 */
	do_action( 'wpbs_submenu_page_settings_tab_general_top', $settings );

?>



<!-- Booking Selection Hover -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline">

	<label class="wpbs-settings-field-label"><?php echo __( 'Booking Hover Color', 'wp-booking-system' ); ?> <?php echo wpbs_get_output_tooltip(__('The color of the calendar dates when hovering over them.', 'wp-booking-system')) ?></label>

	<div class="wpbs-settings-field-inner">
		<input name="wpbs_settings[booking_selection_hover_color]" type="text" class="wpbs-colorpicker" value="<?php echo ( ! empty( $settings['booking_selection_hover_color'] ) ? esc_attr( $settings['booking_selection_hover_color'] ) : '' ); ?>" />
	</div>
	
</div>

<!-- Booking Selection Hover -->
<div class="wpbs-settings-field-wrapper wpbs-settings-field-inline">

	<label class="wpbs-settings-field-label"><?php echo __( 'Booking Selection Color', 'wp-booking-system' ); ?> <?php echo wpbs_get_output_tooltip(__('The color of the calendar dates after they have been selected.', 'wp-booking-system')) ?></label>

	<div class="wpbs-settings-field-inner">
		<input name="wpbs_settings[booking_selection_selected_color]" type="text" class="wpbs-colorpicker" value="<?php echo ( ! empty( $settings['booking_selection_selected_color'] ) ? esc_attr( $settings['booking_selection_selected_color'] ) : '' ); ?>" />
	</div>
	
</div>



<?php

	/**
	 * Hook to add extra fields at the bottom of the General Tab
	 *
	 * @param array $settings
	 *
	 */
	do_action( 'wpbs_submenu_page_settings_tab_general_bottom', $settings );

?>

<!-- Submit button -->
<input type="submit" class="button-primary" value="<?php echo __( 'Save Settings', 'wp-booking-system' ); ?>" />