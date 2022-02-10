<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap wpbs-wrap wpbs-wrap-upgrader">
		
	<!-- Icon -->
	<div id="wpbs-add-new-calendar-icon">
		<div class="wpbs-icon-wrap">
			<span class="dashicons dashicons-calendar-alt"></span>
		</div>
	</div>

	<!-- Heading -->
	<h1 id="wpbs-add-new-calendar-heading"><?php echo __( 'Welcome to WP Booking System v.2', 'wp-booking-system' ); ?></h1>

	<div id="wpbs-upgrader-content">

		<!-- Welcome Text Content -->	
		<div id="wpbs-upgrader-content-inner">
			<p><?php echo __( "This new version was written from the ground up to make the plugin easier to use, more flexible and more stable.", 'wp-booking-system' ); ?></p>
			<p><?php echo __( "To start using the new version we will have to do a quick setup to migrate your existing data to the new format.", 'wp-booking-system' ); ?></p>
			<p><?php echo __( "The setup should take just a few moments. For safe keeping, your old data will not be removed.", 'wp-booking-system' ); ?></p>
		</div>

		<!-- Upgrade Loading Bar -->
		<div id="wpbs-upgrader-loading-bar-wrapper" data-step="1">

			<div id="wpbs-upgrader-loading-bar">

				<!-- Loading Bar Steps -->
				<div id="wpbs-upgrader-loading-bar-step-1" class="wpbs-upgrader-loading-bar-step"></div>
				<div id="wpbs-upgrader-loading-bar-step-2" class="wpbs-upgrader-loading-bar-step"></div>
				<div id="wpbs-upgrader-loading-bar-step-3" class="wpbs-upgrader-loading-bar-step"></div>
				<div id="wpbs-upgrader-loading-bar-step-4" class="wpbs-upgrader-loading-bar-step"></div>
				<div id="wpbs-upgrader-loading-bar-step-5" class="wpbs-upgrader-loading-bar-step"></div>
				<div id="wpbs-upgrader-loading-bar-step-6" class="wpbs-upgrader-loading-bar-step"></div>

				<!-- Loading Messages - Doing -->
				<p id="wpbs-upgrader-message-doing-step-1" class="wpbs-upgrader-message-doing-step description"><?php echo __( 'Migrating calendars...', 'wp-booking-system' ); ?></p>
				<p id="wpbs-upgrader-message-doing-step-2" class="wpbs-upgrader-message-doing-step description"><?php echo __( 'Migrating calendar events...', 'wp-booking-system' ); ?></p>
				<p id="wpbs-upgrader-message-doing-step-3" class="wpbs-upgrader-message-doing-step description"><?php echo __( 'Migrating forms...', 'wp-booking-system' ); ?></p>
				<p id="wpbs-upgrader-message-doing-step-4" class="wpbs-upgrader-message-doing-step description"><?php echo __( 'Migrating bookings...', 'wp-booking-system' ); ?></p>
				<p id="wpbs-upgrader-message-doing-step-5" class="wpbs-upgrader-message-doing-step description"><?php echo __( 'Migrating general settings...', 'wp-booking-system' ); ?></p>
				<p id="wpbs-upgrader-message-doing-step-6" class="wpbs-upgrader-message-doing-step description"><?php echo __( 'Finishing up...', 'wp-booking-system' ); ?></p>

				<!-- Loading Messages - To-do/Done -->
				<p id="wpbs-upgrader-message-step-1" class="wpbs-upgrader-message-step description"><?php echo __( 'Calendars', 'wp-booking-system' ); ?></p>
				<p id="wpbs-upgrader-message-step-2" class="wpbs-upgrader-message-step description"><?php echo __( 'Calendar Events', 'wp-booking-system' ); ?></p>
				<p id="wpbs-upgrader-message-step-3" class="wpbs-upgrader-message-step description"><?php echo __( 'Forms', 'wp-booking-system' ); ?></p>
				<p id="wpbs-upgrader-message-step-4" class="wpbs-upgrader-message-step description"><?php echo __( 'Bookings', 'wp-booking-system' ); ?></p>
				<p id="wpbs-upgrader-message-step-5" class="wpbs-upgrader-message-step description"><?php echo __( 'General settings', 'wp-booking-system' ); ?></p>
				<p id="wpbs-upgrader-message-step-6" class="wpbs-upgrader-message-step description"><?php echo __( 'Finished', 'wp-booking-system' ); ?></p>

				<!-- Loader -->
				<div class="spinner"><!-- --></div>

			</div>

		</div>

		<!-- Action Buttons -->
		<div id="wpbs-upgrader-button-wrapper">

			<?php wp_nonce_field( 'wpbs_upgrader', 'wpbs_token', false ); ?>

			<a id="wpbs-upgrader-button-start-upgrade" href="#" class="button-primary wpbs-button-large">
				<span><?php echo __( "Let's start!", 'wp-booking-system' ); ?></span>
				<span><?php echo __( "Please wait...", 'wp-booking-system' ); ?></span>
			</a>

			<a id="wpbs-upgrader-button-continue" href="<?php echo add_query_arg( array( 'page' => 'wpbs-calendars' ), admin_url( 'admin.php' ) ); ?>" class="button-primary wpbs-button-large"><?php echo __( "Continue to plugin", 'wp-booking-system' ); ?></a>

			<div class="spinner"><!-- --></div>
		</div>

		<!-- Skip Action -->
		<div id="wpbs-upgrader-skip-wrapper">
			<p class="description"><?php echo sprintf( __( "If you wish to start fresh and skip the migration of your calendars and settings, from the old version, %sclick here%s.", 'wp-booking-system' ), '<a href="' . wp_nonce_url( add_query_arg( array( 'wpbs_action' => 'skip_upgrade_process' ) ), 'wpbs_skip_upgrade_process', 'wpbs_token' ) . '">', '</a>' ); ?></p>
		</div>

	</div>

</div>