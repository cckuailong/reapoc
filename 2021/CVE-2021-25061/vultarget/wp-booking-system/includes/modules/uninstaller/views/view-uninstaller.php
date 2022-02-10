<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wpbs-notice-error">

	<p><strong><?php echo __( 'Important!', 'wp-booking-system' ) ?></strong></p>

	<p><?php echo __( 'The uninstaller will remove all WP Booking System information stored in the database for version 2 and higher.', 'wp-booking-system' ); ?></p>

	<p><?php echo __( 'This includes, but is not limited to, all calendars, all legend items, all bookings, all plugin settings.', 'wp-booking-system' ); ?></p>

	<p><?php echo __( 'Data related to versions lower than version 2 will not be removed.', 'wp-booking-system' ); ?></p>

	<p><?php echo __( 'After the uninstall process is complete the plugin will be automatically deactivated.', 'wp-booking-system' ); ?></p>

</div>

<div id="wpbs-uninstaller-confirmation" class="wpbs-notice-error">

	<p><?php echo __( 'To confirm that you really want to uninstall WP Booking System, please type REMOVE in the field below.', 'wp-booking-system' ); ?></p>

	<p><input id="wpbs-uninstaller-confirmation-field" type="text" /></p>

</div>

<a id="wpbs-uninstaller-button" class="button-primary" href="<?php echo add_query_arg( array( 'wpbs_action' => 'uninstall_plugin', 'wpbs_token' => wp_create_nonce( 'wpbs_uninstall_plugin' ) ), admin_url( 'admin.php' ) ); ?>"><?php echo __( 'Uninstall Plugin', 'wp-booking-system' ); ?></a>