<?php
/**
 * Schedule class
 * Single instance of a schedule
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager\Cron\Element;

/**
 * Schedule class
 */
class Schedule {

	/**
	 * Unique slug
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * Schedule interval
	 *
	 * @var int
	 */
	private $interval;

	/**
	 * Label, default to slug
	 *
	 * @var string
	 */
	private $label;

	/**
	 * Protected
	 *
	 * @var bool
	 */
	private $protected;

	/**
	 * Constructor
	 *
	 * @since 2.4.0
	 * @param string  $slug      Slug.
	 * @param integer $interval  Interval.
	 * @param string  $label     Label.
	 * @param boolean $protected If protected.
	 */
	public function __construct( $slug = null, $interval = 0, $label = null, $protected = false ) {

		if ( empty( $label ) ) {
			$label = $slug;
		}

		$this->slug      = $slug;
		$this->interval  = $interval;
		$this->label     = $label;
		$this->protected = $protected;

	}

	/**
	 * Magic method
	 *
	 * @param  string $property Schedule property.
	 * @return mixed            property value
	 */
	public function __get( $property ) {
		return $this->$property;
	}

	/**
	 * Gets raw interval split by days, hours, minutes and seconds
	 *
	 * @return array
	 */
	public function get_raw_human_interval() {

		$interval = array();

		$seconds_in_minute = 60;
		$seconds_in_hour   = 60 * $seconds_in_minute;
		$seconds_in_day    = 24 * $seconds_in_hour;

		// extract days.
		$interval['days'] = floor( $this->interval / $seconds_in_day );

		// extract hours.
		$hour_seconds      = $this->interval % $seconds_in_day;
		$interval['hours'] = floor( $hour_seconds / $seconds_in_hour );

		// extract minutes.
		$minute_seconds      = $hour_seconds % $seconds_in_hour;
		$interval['minutes'] = floor( $minute_seconds / $seconds_in_minute );

		// extract the remaining seconds.
		$remaining_seconds   = $minute_seconds % $seconds_in_minute;
		$interval['seconds'] = ceil( $remaining_seconds );

		return $interval;

	}

	/**
	 * Gets interval in human readable format
	 *
	 * @return string
	 */
	public function get_human_interval() {

		$interval = $this->get_raw_human_interval();

		$human_time = '';

		if ( $interval['days'] > 0 ) {
			$human_time .= $interval['days'] . 'd ';
		}

		if ( $interval['hours'] > 0 ) {
			$human_time .= $interval['hours'] . 'h ';
		}

		if ( $interval['minutes'] > 0 ) {
			$human_time .= $interval['minutes'] . 'm ';
		}

		if ( $interval['seconds'] > 0 ) {
			$human_time .= $interval['seconds'] . 's ';
		}

		if ( empty( $human_time ) ) {
			$human_time = '0s';
		}

		return trim( $human_time );

	}

	/**
	 * Gets the nonce hash for schedule action
	 *
	 * @param  string $action action name.
	 * @return string         nonce hash
	 */
	public function nonce( $action = '' ) {
		return esc_attr( wp_create_nonce( 'acm/schedule/' . $action . '/' . $this->slug ) );
	}

}
