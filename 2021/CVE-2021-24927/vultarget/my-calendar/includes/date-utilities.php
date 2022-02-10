<?php
/**
 * Date Utilities file
 *
 * @category Utilities
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate classes for a given date
 *
 * @param string $current timestamp.
 *
 * @return string classes
 */
function mc_dateclass( $current ) {
	$dayclass = sanitize_html_class( strtolower( date_i18n( 'l', $current ) ) ) . ' ' . sanitize_html_class( strtolower( date_i18n( 'D', $current ) ) );
	if ( current_time( 'Ymd' ) === mc_date( 'Ymd', $current, false ) ) {
		$dateclass = 'current-day';
	} elseif ( my_calendar_date_comp( current_time( 'Y-m-d' ), mc_date( 'Y-m-d', $current, false ) ) ) {
		$dateclass = 'future-day';
	} else {
		$dateclass = 'past-day past-date'; // stupid legacy classes.
	}

	return esc_attr( $dayclass . ' ' . $dateclass );
}

/**
 * Given a date and a quantity of time to add, produce new date
 *
 * @param string $givendate A time string.
 * @param int    $day Number of days to add.
 * @param int    $mth Number of months to add.
 * @param int    $yr number of years to add.
 *
 * @return timestamp
 */
function my_calendar_add_date( $givendate, $day = 0, $mth = 0, $yr = 0 ) {
	$cd      = strtotime( $givendate );
	$newdate = mktime( mc_date( 'H', $cd, false ), mc_date( 'i', $cd, false ), mc_date( 's', $cd, false ), mc_date( 'm', $cd, false ) + $mth, mc_date( 'd', $cd, false ) + $day, mc_date( 'Y', $cd, false ) + $yr );

	return $newdate;
}

/**
 * Test if the date is before or equal to second date with time precision
 *
 * @param string $early date string.
 * @param string $late date string.
 *
 * @return boolean true if first date earlier or equal
 */
function my_calendar_date_comp( $early, $late ) {
	$firstdate = strtotime( $early );
	$lastdate  = strtotime( $late );
	if ( $firstdate <= $lastdate ) {

		return true;
	} else {

		return false;
	}
}

/**
 * Test if first date before second date with time precision
 *
 * @param string $early date string.
 * @param string $late date string.
 *
 * @return boolean true if first date earlier
 */
function my_calendar_date_xcomp( $early, $late ) {
	$firstdate = strtotime( $early );
	$lastdate  = strtotime( $late );
	if ( $firstdate < $lastdate ) {

		return true;
	} else {

		return false;
	}
}

/**
 *  Test if dates are the same with day precision
 *
 * @param string $early date string in current time zone.
 * @param string $late date string in current time zone.
 *
 * @return boolean true if first date equal to second
 */
function my_calendar_date_equal( $early, $late ) {
	// convert full datetime to date only.
	$firstdate = strtotime( mc_date( 'Y-m-d', strtotime( $early ), false ) );
	$lastdate  = strtotime( mc_date( 'Y-m-d', strtotime( $late ), false ) );
	if ( $firstdate === $lastdate ) {

		return true;
	} else {

		return false;
	}
}

/**
 * Function to compare time in event objects for sorting
 *
 * @param object $a event object.
 * @param object $b event object.
 *
 * @return int (ternary value)
 */
function mc_time_cmp( $a, $b ) {
	if ( $a->occur_begin === $b->occur_begin ) {

		return 0;
	}

	return ( $a->occur_begin < $b->occur_begin ) ? - 1 : 1;
}

/**
 * Function to compare datetime in event objects & sort by string
 *
 * @param object $a event object.
 * @param object $b event object.
 *
 * @return integer (ternary value)
 */
function mc_datetime_cmp( $a, $b ) {
	$event_dt_a = strtotime( $a->occur_begin );
	$event_dt_b = strtotime( $b->occur_begin );
	if ( $event_dt_a === $event_dt_b ) {
		// this should sub-sort by title if date is the same. But it doesn't seem to.
		$ta = $a->event_title;
		$tb = $b->event_title;

		return strcmp( $ta, $tb );
	}

	return ( $event_dt_a < $event_dt_b ) ? - 1 : 1;
}

/**
 * Compare two event dates with time precision
 *
 * @param object $a event object.
 * @param object $b event object.
 *
 * @return integer (ternary value)
 */
function mc_timediff_cmp( $a, $b ) {
	$a          = $a . current_time( ' H:i:s' );
	$b          = $b . current_time( ' H:i:s' );
	$event_dt_a = strtotime( $a );
	$event_dt_b = strtotime( $b );
	$diff_a     = mc_date_diff_precise( $event_dt_a );
	$diff_b     = mc_date_diff_precise( $event_dt_b );

	if ( $diff_a === $diff_b ) {
		return 0;
	}

	return ( $diff_a < $diff_b ) ? - 1 : 1;
}

/**
 * Compare two dates for diff with high precision
 *
 * @param int              $start timestamp.
 * @param mixed int/string $end timestamp or 'now'.
 *
 * @return absolute time diff
 */
function mc_date_diff_precise( $start, $end = 'NOW' ) {
	if ( 'NOW' === $end ) {
		$end = strtotime( 'NOW' );
	}
	$sdate = $start;
	$edate = $end;

	$time = $edate - $sdate;

	return abs( $time );
}

/**
 * Get the week of the month a given date falls on.
 *
 * @param integer $date_of_event  current month's date.
 *
 * @return integer $week_of_event The week of the month this date falls in;
 */
function mc_week_of_month( $date_of_event ) {
	$week_of_event = 0;
	switch ( $date_of_event ) {
		case ( $date_of_event >= 1 && $date_of_event < 8 ):
			$week_of_event = 0;
			break;
		case ( $date_of_event >= 8 && $date_of_event < 15 ):
			$week_of_event = 1;
			break;
		case ( $date_of_event >= 15 && $date_of_event < 22 ):
			$week_of_event = 2;
			break;
		case ( $date_of_event >= 22 && $date_of_event < 29 ):
			$week_of_event = 3;
			break;
		case ( $date_of_event >= 29 ):
			$week_of_event = 4;
			break;
	}

	return $week_of_event;
}

/**
 * Validate that a string is a valid date.
 *
 * @param string $date date string.
 *
 * @return boolean true if verified date
 */
function mc_checkdate( $date ) {
	$time = strtotime( $date );
	$m    = mc_date( 'n', $time );
	$d    = mc_date( 'j', $time );
	$y    = mc_date( 'Y', $time );

	return checkdate( $m, $d, $y );
}

/**
 * Get the first day value of the current week.
 *
 * @param mixed int/boolean $timestamp timestamp + offset or false if now.
 *
 * @return array day and month
 */
function mc_first_day_of_week( $timestamp = false ) {
	$start_of_week = ( get_option( 'start_of_week' ) === '1' || get_option( 'start_of_week' ) === '0' ) ? absint( get_option( 'start_of_week' ) ) : 0;
	if ( $timestamp ) {
		$today = mc_date( 'w', $timestamp, false );
		$now   = mc_date( 'Y-m-d', $timestamp, false );
	} else {
		$today = current_time( 'w' );
		$now   = current_time( 'Y-m-d' );
	}
	$month = 0;
	$sub   = 0; // don't change month.
	switch ( $today ) {
		case 1:
			$sub = ( 1 === $start_of_week ) ? 0 : 1;
			break; // mon.
		case 2:
			$sub = ( 1 === $start_of_week ) ? 1 : 2;
			break; // tues.
		case 3:
			$sub = ( 1 === $start_of_week ) ? 2 : 3;
			break; // wed.
		case 4:
			$sub = ( 1 === $start_of_week ) ? 3 : 4;
			break; // thu.
		case 5:
			$sub = ( 1 === $start_of_week ) ? 4 : 5;
			break; // fri.
		case 6:
			$sub = ( 1 === $start_of_week ) ? 5 : 6;
			break; // sat.
		case 0:
			$sub = ( 1 === $start_of_week ) ? 6 : 0;
			break; // sun.
	}
	$day = mc_date( 'j', strtotime( $now . ' -' . $sub . ' day' ), false );
	if ( 0 !== $sub ) {
		if ( mc_date( 'n', strtotime( $now . ' -' . $sub . ' day' ), false ) !== mc_date( 'n', strtotime( $now ), false ) ) {
			$month = - 1;
		} else {
			$month = 0;
		}
	}

	return array( $day, $month );
}

/**
 * Generate an ordinal string in English for numeric values
 *
 * @param int $number Any integer value.
 *
 * @return string number plus ordinal value
 */
function mc_ordinal( $number ) {
	// when fed a number, adds the English ordinal suffix. Works for any number.
	if ( $number % 100 > 10 && $number % 100 < 14 ) {
		$suffix = 'th';
	} else {
		switch ( $number % 10 ) {
			case 0:
				$suffix = 'th';
				break;
			case 1:
				$suffix = 'st';
				break;
			case 2:
				$suffix = 'nd';
				break;
			case 3:
				$suffix = 'rd';
				break;
			default:
				$suffix = 'th';
				break;
		}
	}

	return apply_filters( 'mc_ordinal', "${number}$suffix", $number, $suffix );
}

/**
 * Generate abbreviations & code used for HTML output of calendar headings.
 *
 * @param string $format 'mini', 'list', 'grid'.
 *
 * @return array HTML for each day in an array.
 */
function mc_name_days( $format ) {
	$name_days = array(
		'<abbr title="' . date_i18n( 'l', strtotime( 'Sunday' ) ) . '" aria-hidden="true">' . date_i18n( 'D', strtotime( 'Sunday' ) ) . '</abbr><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Sunday' ) ) . '</span>',
		'<abbr title="' . date_i18n( 'l', strtotime( 'Monday' ) ) . '" aria-hidden="true">' . date_i18n( 'D', strtotime( 'Monday' ) ) . '</abbr><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Monday' ) ) . '</span>',
		'<abbr title="' . date_i18n( 'l', strtotime( 'Tuesday' ) ) . '" aria-hidden="true">' . date_i18n( 'D', strtotime( 'Tuesday' ) ) . '</abbr><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Tuesday' ) ) . '</span>',
		'<abbr title="' . date_i18n( 'l', strtotime( 'Wednesday' ) ) . '" aria-hidden="true">' . date_i18n( 'D', strtotime( 'Wednesday' ) ) . '</abbr><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Wednesday' ) ) . '</span>',
		'<abbr title="' . date_i18n( 'l', strtotime( 'Thursday' ) ) . '" aria-hidden="true">' . date_i18n( 'D', strtotime( 'Thursday' ) ) . '</abbr><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Thursday' ) ) . '</span>',
		'<abbr title="' . date_i18n( 'l', strtotime( 'Friday' ) ) . '" aria-hidden="true">' . date_i18n( 'D', strtotime( 'Friday' ) ) . '</abbr><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Friday' ) ) . '</span>',
		'<abbr title="' . date_i18n( 'l', strtotime( 'Saturday' ) ) . '" aria-hidden="true">' . date_i18n( 'D', strtotime( 'Saturday' ) ) . '</abbr><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Saturday' ) ) . '</span>',
	);
	if ( 'mini' === $format ) {
		// PHP doesn't have a single letter abbreviation, so this has to be a translatable.
		$name_days = array(
			'<span aria-hidden="true">' . __( '<abbr title="Sunday">S</abbr>', 'my-calendar' ) . '</span><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Sunday' ) ) . '</span>', // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
			'<span aria-hidden="true">' . __( '<abbr title="Monday">M</abbr>', 'my-calendar' ) . '</span><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Monday' ) ) . '</span>', // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
			'<span aria-hidden="true">' . __( '<abbr title="Tuesday">T</abbr>', 'my-calendar' ) . '</span><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Tuesday' ) ) . '</span>', // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
			'<span aria-hidden="true">' . __( '<abbr title="Wednesday">W</abbr>', 'my-calendar' ) . '</span><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Wednesday' ) ) . '</span>', // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
			'<span aria-hidden="true">' . __( '<abbr title="Thursday">T</abbr>', 'my-calendar' ) . '</span><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Thursday' ) ) . '</span>', // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
			'<span aria-hidden="true">' . __( '<abbr title="Friday">F</abbr>', 'my-calendar' ) . '</span><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Friday' ) ) . '</span>', // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
			'<span aria-hidden="true">' . __( '<abbr title="Saturday">S</abbr>', 'my-calendar' ) . '</span><span class="screen-reader-text">' . date_i18n( 'l', strtotime( 'Saturday' ) ) . '</span>', // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
		);
	}

	return $name_days;
}

/**
 * Handles all cases for exiting processing early: private events, drafts, etc.
 *
 * @param object $event Event object.
 * @param string $process_date Current date being articulated.
 *
 * @return boolean true if early exit is qualified.
 */
function mc_exit_early( $event, $process_date ) {
	// if event is not approved, return without processing.
	if ( 1 !== (int) $event->event_approved && ! mc_is_preview() ) {
		return true;
	}

	$hide_days = apply_filters( 'mc_hide_additional_days', false, $event );
	$today     = mc_date( 'Y-m-d', strtotime( $event->occur_begin ) );
	$current   = mc_date( 'Y-m-d', strtotime( $process_date ) );
	$end       = mc_date( 'Y-m-d', strtotime( $event->occur_end ) );
	// if event ends at midnight today (e.g., very first thing of the day), exit without re-drawing.
	// or if event started yesterday & has event_hide_end checked.
	$ends_at_midnight = ( '00:00:00' === $event->event_endtime && $end === $process_date && $current !== $today ) ? true : false;

	// hides events if hiding end time & not first day.
	$hide_day_two = ( $hide_days && ( $today !== $current ) ) ? true : false;

	if ( $ends_at_midnight || $hide_day_two ) {
		return true;
	}

	if ( mc_private_event( $event ) ) {
		return true;
	}

	return false;
}

/**
 * Check whether an object with a category_private property is private
 *
 * @param object $event [can be a category object].
 *
 * @return boolean
 */
function mc_private_event( $event ) {
	$status = ( 1 === absint( $event->category_private ) && ! is_user_logged_in() ) ? true : false;
	// custom filter to grant custom reasons for exiting.
	// $event may not be an event object; in some cases it's a category object.
	$status = apply_filters( 'mc_private_event', $status, $event );

	return $status;
}

/**
 * Wrapper for mc_date()
 *
 * @param string $format Format to use.
 * @param int    $timestamp Timestamp.
 * @param bool   $offset false to not add offset; if already a true timestamp.
 *
 * @return string Formatted date.
 */
function mc_date( $format, $timestamp = false, $offset = true ) {
	if ( ! $timestamp ) {
		// Timestamp is UTC.
		$timestamp = time();
	}
	if ( $offset ) {
		$offset = intval( get_option( 'gmt_offset', 0 ) ) * 60 * 60;
	} else {
		$offset = 0;
	}
	$timestamp = $timestamp + $offset;

	return gmdate( $format, $timestamp );
}
