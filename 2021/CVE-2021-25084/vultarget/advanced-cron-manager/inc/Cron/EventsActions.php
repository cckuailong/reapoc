<?php
/**
 * Events Actions class
 * Handles actions on events
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager\Cron;

use underDEV\Utils;

/**
 * EventsActions class
 */
class EventsActions {

	/**
	 * Ajax class
	 *
	 * @var instance of underDEV\AdvancedCronManage\Utils\Ajax
	 */
	private $ajax;

	/**
	 * Events class
	 *
	 * @var instance of underDEV\AdvancedCronManage\Cron\Events
	 */
	private $events;

	/**
	 * EventsLibrary class
	 *
	 * @var instance of underDEV\AdvancedCronManage\Cron\EventsLibrary
	 */
	private $library;

	/**
	 * Schedules class
	 *
	 * @var instance of underDEV\AdvancedCronManage\Cron\Schedules
	 */
	private $schedules;

	/**
	 * Constructor
	 *
	 * @param Utils\Ajax    $ajax      Ajax object.
	 * @param Events        $events    Events object.
	 * @param EventsLibrary $library   EventsLibrary object.
	 * @param Schedules     $schedules Schedules object.
	 */
	public function __construct( Utils\Ajax $ajax, Events $events, EventsLibrary $library, Schedules $schedules ) {

		$this->ajax      = $ajax;
		$this->events    = $events;
		$this->library   = $library;
		$this->schedules = $schedules;

	}

	/**
	 * Insert event
	 *
	 * @return void
	 */
	public function insert() {

		$this->ajax->verify_nonce( 'acm/event/insert' );

		// phpcs:ignore
		$data = wp_parse_args( $_REQUEST['data'], array() );

		$execution = strtotime( $data['execution'] ) ? strtotime( $data['execution'] ) + ( HOUR_IN_SECONDS * $data['execution_offset'] ) : time() + ( HOUR_IN_SECONDS * $data['execution_offset'] );

		$args = array();
		foreach ( $data['arguments'] as $arg_raw ) {
			if ( ! empty( $arg_raw ) ) {
				$args[] = $arg_raw;
			}
		}

		$hook = trim( wp_strip_all_tags( $data['hook'] ) );

		$result = $this->library->insert( $hook, $execution, $data['schedule'], $args );

		if ( is_array( $result ) ) {
			$errors = $result;
		} else {
			$errors = array();
		}

		$schedule = $this->schedules->get_schedule( $data['schedule'] );

		$arg_num = count( $args );

		$success = sprintf(
			// Translators: event hook name, # of args, schedule name.
			esc_html( _n( 'Event "%1$s" with %2$d argument has been scheduled (%3$s)', 'Event "%1$s" with %2$d arguments has been scheduled (%3$s)', $arg_num, 'advanced-cron-manager' ) ),
			$hook, $arg_num, $schedule->label
		);

		$this->ajax->response( $success, $errors );

	}

	/**
	 * Run event
	 *
	 * @return void
	 */
	public function run() {

		global $acm_current_event;

		// phpcs:ignore
		$event = $this->events->get_event_by_hash( $_REQUEST['event'] );

		if ( ! $event ) {
			$this->ajax->response( false, array(
				__( 'This event doesn\'t seem to exist anymore', 'advanced-cron-manager' ),
			) );
		}

		$this->ajax->verify_nonce( 'acm/event/run/' . $event->hash );

		$acm_current_event = $event;

		if ( ! defined( 'DOING_CRON' ) ) {
			define( 'DOING_CRON', true );
		}

		do_action_ref_array( $event->hook, $event->args );

		// Translators: event hook.
		$success = sprintf( __( 'Event "%s" has been executed', 'advanced-cron-manager' ), $event->hook );

		$this->ajax->response( $success, array() );

	}

	/**
	 * Remove event
	 *
	 * @return void
	 */
	public function remove() {

		// phpcs:ignore
		$event  = $this->events->get_event_by_hash( $_REQUEST['event'] );
		$errors = array();

		$this->ajax->verify_nonce( 'acm/event/remove/' . $event->hash );

		if ( $event->protected ) {
			// Translators: event hook.
			$errors = array( sprintf( __( 'Event "%s" is protected and you cannot remove it', 'advanced-cron-manager' ), $event->hook ) );
		}

		$this->library->unschedule( $event );
		$this->library->remove_from_paused( $event );

		// Translators: event hook.
		$success = sprintf( __( 'Event "%s" has been removed', 'advanced-cron-manager' ), $event->hook );

		$this->ajax->response( $success, $errors );

	}

	/**
	 * Pause event
	 *
	 * @return void
	 */
	public function pause() {

		// phpcs:ignore
		$event = $this->events->get_event_by_hash( $_REQUEST['event'] );

		$this->ajax->verify_nonce( 'acm/event/pause/' . $event->hash );

		$result = $this->library->pause( $event );

		if ( is_array( $result ) ) {
			$errors = $result;
		} else {
			$errors = array();
		}

		// Translators: event hook.
		$success = sprintf( __( 'Event "%s" has been paused', 'advanced-cron-manager' ), $event->hook );

		$this->ajax->response( $success, $errors );

	}

	/**
	 * Unpause event
	 *
	 * @return void
	 */
	public function unpause() {

		// phpcs:ignore
		$event = $this->events->get_event_by_hash( $_REQUEST['event'] );

		$this->ajax->verify_nonce( 'acm/event/unpause/' . $event->hash );

		$result = $this->library->unpause( $event );

		if ( is_array( $result ) ) {
			$errors = $result;
		} else {
			$errors = array();
		}

		// Translators: event hook.
		$success = sprintf( __( 'Event "%s" has been unpaused', 'advanced-cron-manager' ), $event->hook );

		$this->ajax->response( $success, $errors );

	}

}
