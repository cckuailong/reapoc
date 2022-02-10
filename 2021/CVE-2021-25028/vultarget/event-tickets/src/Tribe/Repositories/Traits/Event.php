<?php
/**
 * Event trait that contains all of the ORM filters that can be used for any repository.
 *
 * @since 4.12.1
 *
 * @package Tribe\Tickets\Repositories\Traits
 */

namespace Tribe\Tickets\Repositories\Traits;

use DateTimeZone;
use Tribe__Date_Utils as Dates;
use Tribe__Timezones as Timezones;

/**
 * Class Event
 *
 * @since 4.12.1
 */
trait Event {

	/**
	 * Filters events whose end date occurs on or before the provided date; fetch is not inclusive.
	 *
	 * @since 4.9
	 *
	 * @param string|DateTime|int $datetime A `strtotime` parse-able string, a DateTime object or
	 *                                      a timestamp.
	 * @param string|DateTimeZone $timezone A timezone string, UTC offset or DateTimeZone object;
	 *                                      defaults to the site timezone; this parameter is ignored
	 *                                      if the `$datetime` parameter is a DatTime object.
	 *
	 * @return array An array of arguments that should be added to the WP_Query object.
	 */
	public function filter_by_ends_after( $datetime, $timezone = null ) {
		/**
		 * Depending on the setting used to present event on the site the timezone used to normalize
		 * events and the keys used to sort them will be different.
		 * This initial setting can be reverted on a per-instance base using the `use_utc` method.
		 *
		 * @see Tribe__Events__Repositories__Event::use_utc()
		 */
		if ( Timezones::is_mode( 'site' ) ) {
			$normal_timezone = new DateTimeZone( 'UTC' );
			$start_meta_key  = '_EventStartDateUTC';
			$end_meta_key    = '_EventEndDateUTC';
		} else {
			$normal_timezone = Timezones::build_timezone_object();
			$start_meta_key  = '_EventStartDate';
			$end_meta_key    = '_EventEndDate';
		}

		$date     = Dates::build_date_object( $datetime, $timezone )->setTimezone( $normal_timezone );
		$end_date = $date->format( Dates::DBDATETIMEFORMAT );

		global $wpdb;

		$alias_end_date = 'post_event_end_date';

		// Join to the meta that relates meta to attendees.
		$this->filter_query->join( $wpdb->prepare( "
				LEFT JOIN `{$wpdb->postmeta}` AS `{$alias_end_date}`
					ON (
						`{$alias_end_date}`.`post_id` = `$wpdb->posts`.`ID`
						AND `{$alias_end_date}`.`meta_key` = %s
					)
			", [ $end_meta_key ] ), $alias_end_date );

		$this->where_clause( $wpdb->prepare( "
				`{$alias_end_date}`.`post_id` IS NULL
				OR `{$alias_end_date}`.`meta_value` = ''
				OR CAST( `{$alias_end_date}`.`meta_value` AS DATETIME ) >= %s
			", $end_date ) );
	}
}
