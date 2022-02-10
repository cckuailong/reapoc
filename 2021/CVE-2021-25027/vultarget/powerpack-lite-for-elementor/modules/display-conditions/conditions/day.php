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
 * \Modules\DisplayConditions\Conditions\Day
 *
 * @since  1.2.7
 */
class Day extends Condition {

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
		return 'day';
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
		return __( 'Day of Week', 'powerpack' );
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
			'label'             => __( 'Day(s)', 'powerpack' ),
			'type'              => \Elementor\Controls_Manager::SELECT2,
			'options' => [
				'1' => __( 'Monday', 'powerpack' ),
				'2' => __( 'Tuesday', 'powerpack' ),
				'3' => __( 'Wednesday', 'powerpack' ),
				'4' => __( 'Thursday', 'powerpack' ),
				'5' => __( 'Friday', 'powerpack' ),
				'6' => __( 'Saturday', 'powerpack' ),
				'0' => __( 'Sunday', 'powerpack' ),
			],
			'multiple'          => true,
			'label_block'       => true,
			'default'           => '1',
		];
	}

	/**
	 * Check day of week
	 *
	 * Checks wether today falls inside a
	 * specified day of the week
	 *
	 * @since 1.2.7
	 *
	 * @access protected
	 *
	 * @param string    $name       The control name to check
	 * @param mixed     $value      The control value to check
	 * @param string    $operator   Comparison operator.
	 */
	public function check( $name, $operator, $value ) {
		// Default returned bool to false
		$show  = false;
		$today = new \DateTime();

		if ( function_exists( 'wp_timezone' ) ) {
			$timezone = wp_timezone();

			// Set timezone
			$today->setTimeZone( $timezone );
		}

		$day = $today->format( 'w' );

		$show = is_array( $value ) && ! empty( $value ) ? in_array( $day, $value ) : $value === $day;

		return self::compare( $show, true, $operator );
	}
}
