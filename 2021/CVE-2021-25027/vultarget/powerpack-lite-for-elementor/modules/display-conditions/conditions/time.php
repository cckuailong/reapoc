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
 * \Modules\DisplayConditions\Conditions\Time
 *
 * @since  1.2.7
 */
class Time extends Condition {

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
		return 'time';
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
		return __( 'Time of Day', 'powerpack' );
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
		return [
			'label'     => __( 'Before', 'powerpack' ),
			'type'      => \Elementor\Controls_Manager::DATE_TIME,
			'picker_options' => [
				'dateFormat'    => 'H:i',
				'enableTime'    => true,
				'noCalendar'    => true,
			],
			'label_block'   => true,
			'default'       => '',
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
		// Split control valur into two dates
		$time   = date( 'H:i', strtotime( preg_replace( '/\s+/', '', $value ) ) );
		$now    = date( 'H:i', strtotime( 'now' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );

		// Default returned bool to false
		$show   = false;

		// Check vars
		if ( \DateTime::createFromFormat( 'H:i', $time ) === false ) { // Make sure it's a valid DateTime format
			return;
		}

		// Convert to timestamp
		$time_ts    = strtotime( $time );
		$now_ts     = strtotime( $now );

		// Check that user date is between start & end
		$show = ( $now_ts < $time_ts );

		return $this->compare( $show, true, $operator );
	}
}
