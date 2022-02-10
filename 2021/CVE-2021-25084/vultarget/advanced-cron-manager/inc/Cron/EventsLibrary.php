<?php
/**
 * Events Library class
 * Handles DB operations on events
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager\Cron;

use underDEV\Utils;

/**
 * EventsLibrary class
 */
class EventsLibrary {

	/**
	 * Schedules class
	 *
	 * @var instance of underDEV\AdvancedCronManage\Cron\Schedules
	 */
	public $schedules;

	/**
	 * Events class
	 *
	 * @var instance of underDEV\AdvancedCronManage\Cron\Events
	 */
	private $events;

	/**
	 * Option name
	 *
	 * @var string
	 */
	private $paused_option_name;

	/**
	 * Constructor
	 *
	 * @param Schedules $schedules Schedules object.
	 * @param Events    $events    Events object.
	 */
	public function __construct( Schedules $schedules, Events $events ) {

		$this->schedules = $schedules;
		$this->events    = $events;

		$this->paused_option_name = 'acm_paused_events';

	}

	/**
	 * Registers paused events
	 *
	 * @param  array $events array of registered events.
	 * @return array         array of both registered and paused events
	 */
	public function register_paused( $events ) {

		$paused_events = get_option( $this->paused_option_name, array() );

		foreach ( $paused_events as $paused_event ) {
			$events[] = new Element\Event( $paused_event['hook'], $paused_event['schedule_slug'], $paused_event['interval'], $paused_event['args'], $paused_event['execution_timestamp'], $this->events->is_protected( $paused_event['hook'] ), true );
		}

		return $events;

	}

	/**
	 * Inserts new event
	 *
	 * @param  string  $hook                action hook name.
	 * @param  int     $execution_timestamp UTC timestamp for first execution.
	 * @param  string  $schedule_slug       Schedule slug.
	 * @param  array   $args                arguments.
	 * @param  boolean $new                 if event is new.
	 * @return mixed                        array with errors on error or true
	 */
	public function insert( $hook, $execution_timestamp, $schedule_slug, $args, $new = true ) {

		$errors = array();

		if ( empty( $hook ) ) {
			$errors[] = __( 'Please, provide a hook for your Event', 'advanced-cron-manager' );
		}

		$schedule = $this->schedules->get_schedule( $schedule_slug );

		if ( $schedule->slug !== $schedule_slug ) {
			// Translators: schedule slug.
			$errors[] = sprintf( __( 'Schedule "%s" cannot be found', 'advanced-cron-manager' ), $schedule_slug );
		}

		if ( ! empty( $errors ) ) {
			return $errors;
		}

		if ( $schedule->slug === $this->schedules->get_single_event_schedule()->slug ) {
			wp_schedule_single_event( $execution_timestamp, $hook, $args );
		} else {
			wp_schedule_event( $execution_timestamp, $schedule->slug, $hook, $args );
		}

		if ( $new ) {
			do_action( 'advanced-cron-manager/event/scheduled', $hook, $execution_timestamp, $schedule, $args );
		}

		return true;

	}

	/**
	 * Removes (unschedules) the event
	 *
	 * @param  mixed   $thing       event hash or Event object.
	 * @param  boolean $permanently if unschedule is permanent.
	 * @return mixed                array with errors on error or true
	 */
	public function unschedule( $thing, $permanently = true ) {

		$errors = array();

		if ( is_string( $thing ) ) {
			$event = $this->events->get_event_by_hash( $thing );
		} else {
			$event = $thing;
		}

		if ( false === $event ) {
			$errors[] = __( 'Event not found and cannot be unscheduled', 'advanced-cron-manager' );
			return $errors;
		}

		wp_unschedule_event( $event->next_call, $event->hook, $event->args );

		if ( $permanently ) {
			do_action( 'advanced-cron-manager/event/unscheduled', $event );
		}

		return true;

	}

	/**
	 * Pauses the event
	 *
	 * @param  mixed $thing event hash or Event object.
	 * @return mixed        array with errors on error or true
	 */
	public function pause( $thing ) {

		$errors = array();

		if ( is_string( $thing ) ) {
			$event = $this->events->get_event_by_hash( $thing );
		} else {
			$event = $thing;
		}

		if ( false === $event ) {
			$errors[] = __( 'Event not found and cannot be paused', 'advanced-cron-manager' );
		}

		if ( $event->protected ) {
			$errors[] = __( 'Event is protected and cannot be paused', 'advanced-cron-manager' );
		}

		if ( ! empty( $errors ) ) {
			return $errors;
		}

		// add to paused option.
		$this->add_to_paused( $event );

		// unschedule.
		$this->unschedule( $event, false );

		return true;

	}

	/**
	 * Unpauses the event
	 *
	 * @param  mixed $thing event hash or Event object.
	 * @return mixed        array with errors on error or true
	 */
	public function unpause( $thing ) {

		$errors = array();

		if ( is_string( $thing ) ) {
			$event = $this->events->get_event_by_hash( $thing );
		} else {
			$event = $thing;
		}

		if ( false === $event ) {
			$errors[] = __( 'Event not found and cannot be unpaused', 'advanced-cron-manager' );
		}

		if ( ! empty( $errors ) ) {
			return $errors;
		}

		// remove from paused option.
		$this->remove_from_paused( $event );

		// schedule.
		$result = $this->insert( $event->hook, $event->next_call, $event->schedule, $event->args, false );

		return $result;

	}

	/**
	 * Adds an event to paused events option
	 *
	 * @param object $event Event object.
	 */
	public function add_to_paused( $event ) {

		$paused_events = get_option( $this->paused_option_name, array() );

		$paused_events[ $event->hash ] = array(
			'hook'                => $event->hook,
			'interval'            => $event->interval,
			'execution_timestamp' => $event->next_call,
			'schedule_slug'       => $event->schedule,
			'args'                => $event->args,
		);

		update_option( $this->paused_option_name, $paused_events );

	}

	/**
	 * Removes an event from paused events option
	 *
	 * @param object $event Event object.
	 */
	public function remove_from_paused( $event ) {

		$paused_events = get_option( $this->paused_option_name, array() );

		if ( isset( $paused_events[ $event->hash ] ) ) {

			unset( $paused_events[ $event->hash ] );
			update_option( $this->paused_option_name, $paused_events );

		}

	}

}
