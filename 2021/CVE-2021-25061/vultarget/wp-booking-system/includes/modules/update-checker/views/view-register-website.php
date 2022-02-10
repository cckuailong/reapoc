<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$serial_key = get_option( 'wpbs_serial_key', '' );
$website_id = get_option( 'wpbs_registered_website_id', '' );

?>

<!-- Serial Key -->
<div id="wpbs-wpbs-settings-field-register-website" class="wpbs-settings-field-wrapper wpbs-settings-field-inline">

	<label class="wpbs-settings-field-label"><?php echo __( 'Serial Key', 'wp-booking-system' ); ?><?php echo wpbs_get_output_tooltip( sprintf( __( 'You can find your serial key in your WP Booking System account. %sClick here to go to your account.%s', 'wp-booking-system' ), '<a href="https://www.wpbookingsystem.com/account/" target="_blank">', '</a>' ) ); ?></label>

	<div class="wpbs-settings-field-inner">

		<input type="text" name="serial_key" <?php echo ( ! empty( $serial_key ) ? 'disabled' : '' ); ?> value="<?php echo esc_attr( $serial_key ); ?>" />

		<?php if( empty( $website_id ) ): ?>
			<a id="wpbs-register-website-button" class="button-primary" href="<?php echo add_query_arg( array( 'tab' => 'register_website', 'wpbs_action' => 'register_website', 'wpbs_token' => wp_create_nonce( 'wpbs_register_website' ) ) ); ?>"><?php echo __( 'Register Website', 'wp-booking-system' ); ?></a>
		<?php else: ?>
			<a id="wpbs-deregister-website-button" class="button-secondary" href="<?php echo add_query_arg( array( 'tab' => 'register_website', 'wpbs_action' => 'deregister_website', 'wpbs_token' => wp_create_nonce( 'wpbs_deregister_website' ) ) ); ?>"><?php echo __( 'Deregister Website', 'wp-booking-system' ); ?></a>
		<?php endif; ?>

	</div>
	
</div>

<!-- Check for Updates -->
<div id="wpbs-wpbs-settings-field-register-website" class="wpbs-settings-field-wrapper wpbs-settings-field-inline">

	<label class="wpbs-settings-field-label"><?php echo __( 'Manual Update Check', 'wp-booking-system' ); ?><?php echo wpbs_get_output_tooltip( sprintf( __( 'The plugin by default checks once a day if there is an update available for it and displays if there is one in the %1$sPlugins page%2$s. If you want to do a manual update check and not wait for the automatic one, press the Check for Updates button and then check the %1$sPlugins page%2$s.', 'wp-booking-system' ), '<a href="' . admin_url( 'plugins.php' ) . '">', '</a>' ) ); ?></label>

	<div class="wpbs-settings-field-inner">

		<a id="wpbs-check-for-updates-button" class="button-secondary" <?php echo ( empty( $serial_key ) || empty( $website_id ) ? 'disabled' : '' ); ?> href="<?php echo add_query_arg( array( 'tab' => 'register_website', 'wpbs_action' => 'check_for_updates', 'wpbs_token' => wp_create_nonce( 'wpbs_check_for_updates' ) ) ); ?>"><?php echo __( 'Check for Updates', 'wp-booking-system' ); ?></a>

	</div>
	
</div>

<?php if( !empty( $website_id ) ): ?>
<div class="wpbs-page-notice notice-info wpbs-form-changed-notice"> 
    <p><?php echo __( '<strong>IMPORTANT</strong>: After updating, you will need to refresh the Plugins page and manually activate the premium plugin.', 'wp-booking-system' ); ?></p>
</div>
<?php endif; ?>