<?php

class DomainCheckCron {

	private static $cronsAllowed = array(
		'domain_check_cron_email',
		'domain_check_cron_watch',
		'domain_check_cron_coupons',
	);

	private static $schedulesAllowed = array(
		'daily' => 86400,
		'weekly'  => 604800,
		'monthly' => 2635200,
		'off' => 0
	);


	public static function cron_schedule($cron_name, $cron_schedule) {

		//cron is not allowed
		if ( !self::cron_allowed( $cron_name ) ) {
			return null; //'Cron not allowed.';
		}
		if ( !self::schedule_allowed( $cron_schedule ) ) {
			return null; //'Schedule not allowed.';
		}

		//clear the cron
		wp_clear_scheduled_hook( $cron_name );
		wp_clear_scheduled_hook( $cron_name );

		if ( !wp_get_schedule( $cron_name ) ) {

			//turning cron off
			if ( $cron_schedule === 'off' ) {
				return true; //'Success! Turned off!';
			}

			//turning cron on
			$res = wp_schedule_event( time() + self::$cronsAllowed[$cron_name], $cron_schedule, $cron_name );
			return res;
		}

	}

	public static function cron_allowed($cron_name) {
		if ( isset( self::$cronsAllowed[$cron_name] ) ) {
			return true;
		}
		return false;
	}

	public static function schedule_allowed($cron_schedule) {
		if ( in_array( $cron_schedule, self::$schedulesAllowed ) ) {
			return true;
		}
		return false;
	}

	public static function add_intervals($schedules) {

		// add a 'weekly' interval
		if ( !isset( $schedules['weekly'] ) ) {
			$schedules['weekly'] = array(
				'interval' => 604800,
				'display' => __('Once Weekly')
			);
		}
		if ( !isset( $schedules['monthly'] ) ) {
			$schedules['monthly'] = array(
				'interval' => 2635200,
				'display' => __('Once Monthly')
			);
		}
		return $schedules;
	}

	public static function get_schedule( $schedule ) {
		if ( self::schedule_allowed( $schedule ) ) {
			return self::$schedulesAllowed[$schedule];
		}
	}
}