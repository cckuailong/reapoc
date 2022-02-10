<?php
namespace PowerpackElementsLite\Modules\DisplayConditions\Conditions;

// Powerpack Elements Classes
use PowerpackElementsLite\Base\Condition;

// Elementor Classes
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * \Modules\DisplayConditions\Conditions\Date
 *
 * @since  1.2.7
 */
class Date extends Condition {

	/**
	 * Get Group
	 *
	 * Get the group of the condition
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_group() {
		return 'date_time';
	}

	/**
	 * Get Name
	 *
	 * Get the name of the module
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_name() {
		return 'date';
	}

	/**
	 * Get Title
	 *
	 * Get the title of the module
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_title() {
		return __( 'Current Date', 'powerpack' );
	}

	/**
	 * Get Value Control
	 *
	 * Get the settings for the value control
	 *
	 * @since  1.2.7
	 * @return string
	 */
	public function get_value_control() {
		$default_date_start = date( 'Y-m-d', strtotime( '-3 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		$default_date_end   = date( 'Y-m-d', strtotime( '+3 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		$default_interval   = $default_date_start . ' to ' . $default_date_end;

		return [
			'label'             => __( 'In interval', 'powerpack' ),
			'type'              => \Elementor\Controls_Manager::DATE_TIME,
			'picker_options'    => [
				'enableTime'    => false,
				'mode'          => 'range',
			],
			'label_block'       => true,
			'default'           => $default_interval,
		];
	}

	/**
	 * Check condition
	 *
	 * @since 1.2.7
	 *
	 * @access public
	 *
	 * @param string    $name       The control name to check
	 * @param string    $operator   Comparison operator
	 * @param mixed     $value      The control value to check
	 */
	public function check( $name, $operator, $value ) {
		// Default returned bool to false
		$show = false;

		// Split control valur into two dates
		$intervals = explode( 'to', preg_replace( '/\s+/', '', $value ) );

		// Make sure the explode return an array with exactly 2 indexes
		if ( ! is_array( $intervals ) || 2 !== count( $intervals ) ) {
			return;
		}

		// Set start and end dates
		$today = new \DateTime();
		$start = \DateTime::createFromFormat( 'Y-m-d', $intervals[0] );
		$end   = \DateTime::createFromFormat( 'Y-m-d', $intervals[1] );

		// Check vars
		if ( ! $start || ! $end ) { // Make sure it's a date
			return;
		}

		if ( function_exists( 'wp_timezone' ) ) {
			$timezone = wp_timezone();

			// Set timezone
			$today->setTimeZone( $timezone );
		}

		// Get tijmestamps for comparison
		$start_ts = $start->format( 'U' );
		$end_ts   = $end->format( 'U' );
		$today_ts = $today->format( 'U' ) + $today->getOffset(); // Adding the offset

		// Check that user date is between start & end
		$show = ( ( $today_ts >= $start_ts ) && ( $today_ts <= $end_ts ) );

		return $this->compare( $show, true, $operator );
	}
}
