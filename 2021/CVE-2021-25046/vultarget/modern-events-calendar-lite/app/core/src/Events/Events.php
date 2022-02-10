<?php

namespace MEC\Events;

/**
 * Not completed
 */
class Events {

	public function save_event( $event ) {

		$d_args = array(
			'title'   => '',
			'content' => '',
			'status'  => 'publish',
		);

		$event = wp_parse_args( $event, $d_args );

		$event_arg = array(

			'post_title'   => $event['title'],
			'post_content' => $event['content'],
			'post_status'  => $event['status'],
		);

		$event_id = wp_insert_post( $event_arg );

		do_action( 'mec_saved_event', $event_id, $event );

		$event_mata = array(
			'mec_location_id',
			'mec_dont_show_map',
			'mec_organizer_id',
			'mec_read_more',
			'mec_more_info',
			'mec_more_info_title',
			'mec_more_info_target',
			'mec_cost',

			'mec_additional_organizer_ids',
			'mec_additional_location_ids',

			'mec_date',
			'mec_repeat',
			'mec_certain_weekdays',
			'mec_allday',
			'one_occurrence',
			'mec_hide_time',
			'mec_hide_end_time',
			'mec_comment',
			'mec_timezone',
			'mec_countdown_method',
			'mec_public',

			'mec_start_date',
			'mec_start_time_hour',
			'mec_start_time_minutes',
			'mec_start_time_ampm',
			'mec_start_day_seconds',

			'mec_end_date',
			'mec_end_time_hour',
			'mec_end_time_minutes',
			'mec_end_time_ampm',
			'mec_end_day_seconds',

			'mec_repeat_status',
			'mec_repeat_type',
			'mec_repeat_interval',
			'mec_repeat_end',
			'mec_repeat_end_at_occurrences',
			'mec_repeat_end_at_date',
			'mec_advanced_days',

			'mec_event_date_submit',

			'mec_in_days',
			'mec_not_in_days',
			'mec_hourly_schedules',
			'mec_booking',

			'mec_tickets',
			'mec_fees_global_inheritance',
			'mec_fees',

			'mec_ticket_variations_global_inheritance',
			'mec_ticket_variations',
			'mec_reg_fields_global_inheritance',

			'mec_reg_fields',
			'mec_bfixed_fields',
			'mec_op',

			'mec_fields',
			'mec_notifications',
		);

	}
}
