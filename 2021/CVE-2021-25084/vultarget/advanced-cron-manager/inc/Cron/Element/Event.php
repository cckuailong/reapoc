<?php
/**
 * Event class
 * Single instance of an event
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager\Cron\Element;

/**
 * Event
 */
class Event {

	/**
	 * Event hook
	 *
	 * @var string
	 */
	private $hook;

	/**
	 * Event's schedule interval
	 *
	 * @var int
	 */
	private $interval;

	/**
	 * Event's schedule slug
	 *
	 * @var string
	 */
	private $schedule;

	/**
	 * Event's arguments
	 *
	 * @var array
	 */
	private $args = array();

	/**
	 * Event's next call timestamp
	 *
	 * @var int
	 */
	private $next_call;

	/**
	 * Protected
	 *
	 * @var bool
	 */
	private $protected;

	/**
	 * Paused
	 *
	 * @var bool
	 */
	private $paused;

	/**
	 * Constructor
	 *
	 * @since 2.4.0
	 * @param string  $hook      Hook name.
	 * @param string  $schedule  Schedule name.
	 * @param integer $interval  Interval.
	 * @param array   $args      Arguments.
	 * @param integer $next_call Next call timestamp.
	 * @param boolean $protected If protected.
	 * @param boolean $paused    If paused.
	 */
	public function __construct( $hook = null, $schedule = '', $interval = 0, $args = array(), $next_call = 0, $protected = false, $paused = false ) {

		if ( empty( $hook ) ) {
			trigger_error( 'Hook cannot be empty', E_USER_ERROR );
		}

		$this->hook      = $hook;
		$this->schedule  = $schedule;
		$this->interval  = $interval;
		$this->args      = $args;
		$this->next_call = $next_call;
		$this->protected = $protected;
		$this->paused    = $paused;

		// phpcs:ignore
		$this->hash = substr( md5( $this->hook . $this->schedule . $this->next_call . serialize( $this->args ) ), 0, 8 );

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
	 * Gets implementation code for event
	 *
	 * @return string
	 */
	public function get_implementation() {

		$arguments = array();
		foreach ( $this->args as $n => $arg ) {
			$arguments[] = '$arg' . (string) $n;
		}
		$arguments = empty( $arguments ) ? '' : ' ' . implode( ', ', $arguments ) . ' ';

		$function_name = 'cron_' . $this->hook . '_' . $this->hash;

		$imp = '';

		$imp .= 'function ' . $function_name . '(' . $arguments . ') {<br>';
		$imp .= '&nbsp;&nbsp;&nbsp;&nbsp;// do stuff<br>';
		$imp .= '}<br>';
		$imp .= '<br>';
		$imp .= "add_action( '" . $this->hook . "',  '" . $function_name . "', 10, " . count( $this->args ) . ' );';

		return $imp;

	}

	/**
	 * Gets the nonce hash for event action
	 *
	 * @param  string $action action name.
	 * @return string         nonce hash
	 */
	public function nonce( $action = '' ) {
		return esc_attr( wp_create_nonce( 'acm/event/' . $action . '/' . $this->hash ) );
	}

}
