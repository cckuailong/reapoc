<?php
$event_meta = $event_obj->event_meta;

$form_disabled_att = '';
$form_disabled_class = '';
$users_only_disabled_att = '';
$users_only_disabled_class = '';
$limit_disabled_att = '';
$limit_disabled_class = '';
$max_disabled_att = '';
$max_disabled_class = '';
$deadline_disabled_att = '';
$deadline_disabled_class = '';
$deadline_other_disabled_class = '';
$attendee_who_disabled_att = '';
$attendee_who_disabled_class = '';
$notification_recipients_for_event = get_post_meta( $event_meta['post_id'], '_RTECnotificationEmailRecipient' );

if ( ! empty( $notification_recipients_for_event[0] ) ) {
	$notification_recipients = explode(',', str_replace( ' ', '', $notification_recipients_for_event[0] ) );
} else {
	$notification_recipients = array();
}

$notification_email = implode( ', ', $notification_recipients );
$conf_email = rtec_get_confirmation_from_address( $event_meta['post_id'], true );
$deadline_time = isset( $event_meta['deadline_other_timestamp'] ) ? $event_meta['deadline_other_timestamp'] : strtotime( $event_meta['start_date'] );
if ( $deadline_time == 0 ) {
	$deadline_time = strtotime( date( 'Y-m-d' ) ) + 28800;
}

if ( $event_meta['registrations_disabled'] ) {
	$form_disabled_att = ' disabled="true"';;
	$form_disabled_class = ' rtec-fade';
	$users_only_disabled_att = ' disabled="true"';;
	$users_only_disabled_class = ' rtec-fade';
	$limit_disabled_att = ' disabled="true"';
	$limit_disabled_class = ' rtec-fade';
	$deadline_disabled_att = ' disabled="true"';
	$deadline_disabled_class = ' rtec-fade';
}

if ( $event_meta['registrations_disabled'] || ! $event_meta['limit_registrations'] ) {
	$max_disabled_att = ' disabled="true"';
	$max_disabled_class = ' rtec-fade';
}

if ( $event_meta['deadline_type'] !== 'other' ) {
	$deadline_other_disabled_class = ' rtec-fade';
}

if ( ! $event_meta['show_registrants_data'] ) {
	$attendee_who_disabled_att = ' disabled="true"';
	$attendee_who_disabled_class = ' rtec-fade';
}
?>
<div class="rtec-event-options postbox closed">
	<button type="button" class="handlediv button-link" aria-expanded="false"><span class="screen-reader-text"><?php _e( 'Toggle panel: Information', 'registrations-for-the-events-calendar' ); ?></span><span class="toggle-indicator" aria-hidden="true"></span></button>
	<span class="hndle"><span><?php _e( 'Event Options', 'registrations-for-the-events-calendar' ); ?></span></span>
</div>
<div class="rtec-event-options rtec-hidden-options postbox">
	<form class="rtec-event-options-form" action="">
		<input type="hidden" name="rtec_event_id" value="<?php echo esc_attr( $event_meta['post_id'] ); ?>" />
		<h4>General</h4>
		<div class="rtec-hidden-option-wrap">
			<input type="checkbox" id="rtec-disable-<?php echo esc_attr( $event_meta['post_id'] ); ?>" name="_RTECregistrationsDisabled" <?php if ( $event_meta['registrations_disabled'] ) { echo 'checked'; } ?> value="1"/>
			<label for="rtec-disable-<?php echo esc_attr( $event_meta['post_id'] ); ?>"><?php _e( 'Disable registrations for this event', 'registrations-for-the-events-calendar' ); ?></label>
		</div>
        <div class="rtec-hidden-option-wrap<?php echo $users_only_disabled_class; ?>">
            <input type="checkbox" id="rtec-users-<?php echo esc_attr( $event_meta['post_id'] ); ?>" name="_RTECwhoCanRegister" value="users" <?php if( $event_meta['who_can_register'] === 'users' ) { echo 'checked'; } ?> <?php echo $users_only_disabled_att; ?>/>
            <label for="rtec-users-<?php echo esc_attr( $event_meta['post_id'] ); ?>"><?php _e( 'Logged in users only', 'registrations-for-the-events-calendar' ); ?></label>
        </div>
		<div class="rtec-hidden-option-wrap<?php echo $limit_disabled_class; ?>">
			<input type="checkbox" id="rtec-limit-<?php echo esc_attr( $event_meta['post_id'] ); ?>" name="_RTEClimitRegistrations" <?php if( $event_meta['limit_registrations'] ) { echo 'checked'; } ?> value="1"<?php echo $limit_disabled_att; ?>/>
			<label for="rtec-limit-<?php echo esc_attr( $event_meta['post_id'] ); ?>"><?php _e( 'Limit the number registrations allowed', 'registrations-for-the-events-calendar' ); ?></label>
		</div>
		<div class="rtec-hidden-option-wrap<?php echo $max_disabled_class; ?>">
			<input type="text" min="0" size="3" id="rtec-max-<?php echo esc_attr( $event_meta['post_id'] ); ?>" name="_RTECmaxRegistrations" value="<?php echo esc_attr( $event_meta['max_registrations'] ); ?>"<?php echo $max_disabled_att; ?>/>
			<label for="rtec-max-<?php echo esc_attr( $event_meta['post_id'] ); ?>"><?php _e( 'Maximum registrations', 'registrations-for-the-events-calendar' ); ?></label>
		</div>
		<div class="rtec-hidden-option-wrap<?php echo $deadline_disabled_class; ?>">
			<span style="margin-bottom: 5px; display: block;">Deadline type:</span>
			<div class="rtec-sameline">
				<input type="radio" id="rtec-start-<?php echo esc_attr( $event_meta['post_id'] ); ?>" name="_RTECdeadlineType" <?php if( $event_meta['deadline_type'] === 'start' ) { echo 'checked'; } ?> value="start"<?php echo $deadline_disabled_att; ?>/>
				<label for="rtec-start-<?php echo esc_attr( $event_meta['post_id'] ); ?>"><?php _e( 'Start Time', 'registrations-for-the-events-calendar' ); ?></label>
			</div>
			<div class="rtec-sameline">
				<input type="radio" id="rtec-end-<?php echo esc_attr( $event_meta['post_id'] ); ?>" name="_RTECdeadlineType" <?php if( $event_meta['deadline_type'] === 'end' ) { echo 'checked'; } ?> value="end"<?php echo $deadline_disabled_att; ?>/>
				<label for="rtec-end-<?php echo esc_attr( $event_meta['post_id'] ); ?>"><?php _e( 'End Time', 'registrations-for-the-events-calendar' ); ?></label>
			</div>
			<div class="rtec-sameline">
				<input type="radio" id="rtec-none-<?php echo esc_attr( $event_meta['post_id'] ); ?>" name="_RTECdeadlineType" <?php if( $event_meta['deadline_type'] === 'none' ) { echo 'checked'; } ?> value="none"<?php echo $deadline_disabled_att; ?>/>
				<label for="rtec-none-<?php echo esc_attr( $event_meta['post_id'] ); ?>"><?php _e( 'No deadline', 'registrations-for-the-events-calendar' ); ?></label>
			</div>
			<br />
			<input type="radio" id="rtec-other-<?php echo esc_attr( $event_meta['post_id'] ); ?>" name="_RTECdeadlineType" <?php if( $event_meta['deadline_type'] === 'other' ) { echo 'checked'; } ?> value="other"<?php echo $deadline_disabled_att;?>/>
			<label for="rtec-other-<?php echo esc_attr( $event_meta['post_id'] ); ?>"><?php _e( 'Other:', 'registrations-for-the-events-calendar' ); ?></label>
			<input type="text" id="rtec-date-picker-deadline-<?php echo esc_attr( $event_meta['post_id'] ); ?>" name="_RTECdeadlineDate" value="<?php echo date( 'Y-m-d', $deadline_time ); ?>" class="rtec-date-picker<?php echo $deadline_other_disabled_class; ?>" style="width: 100px;"/>
			<input autocomplete="off" tabindex="2001" type="text" class="rtec-time-picker ui-timepicker-input<?php echo $deadline_other_disabled_class; ?>" name="_RTECdeadlineTime" id="rtec-time-picker" data-step="30" data-round="" value="<?php echo date( "H:i:s", $deadline_time ); ?>" style="width: 80px;">

		</div>
		<div class="rtec-hidden-option-wrap">
			<h4><?php _e( 'Email', 'registrations-for-the-events-calendar' ); ?></h4>
			<div class="rtec-hidden-option-wrap">
				<label for="rtec-not-email-<?php echo esc_attr( $event_meta['post_id'] ); ?>"><?php _e( 'Notification email recipients', 'registrations-for-the-events-calendar' ); ?></label><br />
				<input type="text" size="50" id="rtec-not-email-<?php echo esc_attr( $event_meta['post_id'] ); ?>" name="_RTECnotificationEmailRecipient" value="<?php echo esc_attr( $notification_email ); ?>" placeholder="<?php _e( 'leave blank for default', 'registrations-for-the-events-calendar' ); ?>"/>
			</div>
			<div class="rtec-hidden-option-wrap">
				<label for="rtec-conf-email-<?php echo esc_attr( $event_meta['post_id'] ); ?>"><?php _e( 'Confirmation email from address', 'registrations-for-the-events-calendar' ); ?></label><br />
				<input type="text" size="50" id="rtec-conf-email-<?php echo esc_attr( $event_meta['post_id'] ); ?>" name="_RTECconfirmationEmailFrom" value="<?php echo esc_attr( $conf_email ); ?>" placeholder="<?php _e( 'leave blank for default', 'registrations-for-the-events-calendar' ); ?>"/>
			</div>
		</div>
		<button class="button action rtec-admin-secondary-button rtec-update-event-options"><?php _e( 'Update', 'registrations-for-the-events-calendar'  ); ?></button>
		<div class="rtec-clear"></div>
	</form>
</div>