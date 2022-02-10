<?php
/**
 * Events class
 * Used to handle collection of events
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager\Cron;

/**
 * Events object
 */
class Events {

	/**
	 * Schedules class
	 *
	 * @var instance of underDEV\AdvancedCronManage\Cron\Schedules
	 */
	private $schedules;

	/**
	 * Registered events
	 *
	 * @var array
	 */
	private $events = array();

	/**
	 * Protected events slugs
	 *
	 * @var array
	 */
	private $protected_events = array();

	/**
	 * Constructor
	 *
	 * @param Schedules $schedules Schedules object.
	 */
	public function __construct( Schedules $schedules ) {

		$this->schedules = $schedules;

		// protected events registered by WordPress' core.
		$this->protected_events = array(
			'wp_privacy_delete_old_export_files',
			'wp_version_check',
			'wp_update_plugins',
			'wp_update_themes',
			'wp_site_health_scheduled_check',
			'recovery_mode_clean_expired_keys',
			'wp_scheduled_delete',
			'delete_expired_transients',
			'wp_scheduled_auto_draft_delete',
			'recovery_mode_clean_expired_keys',
		);
	}

	/**
	 * Gets all registered events
	 * Supports lazy loading
	 *
	 * @param  boolean $force if refresh stored events.
	 * @return array          registered events
	 */
	public function get_events( $force = false ) {

		if ( empty( $this->events ) || $force ) {

			$events_array = array();

			foreach ( _get_cron_array() as $timestamp => $events ) {

				foreach ( $events as $event_hook => $event_args ) {

					if ( $this->is_protected( $event_hook ) ) {
						$protected = true;
					} else {
						$protected = false;
					}

					foreach ( $event_args as $event ) {

						$interval       = isset( $event['interval'] ) ? $event['interval'] : 0;
						$schedule       = empty( $event['schedule'] ) ? $this->schedules->get_single_event_schedule()->slug : $event['schedule'];
						$events_array[] = new Element\Event( $event_hook, $schedule, $interval, $event['args'], $timestamp, $protected );

					}
				}
			}

			$events_array = apply_filters( 'advanced-cron-manager/events/array', $events_array );

			usort( $events_array, array( $this, 'compare_event_next_calls' ) );

			// add event's hashes to the array.
			foreach ( $events_array as $event ) {
				$this->events[ $event->hash ] = $event;
			}
		}

		return $this->events;

	}

	/**
	 * Gets event by it's hash
	 *
	 * @param  string $hash hash.
	 * @return mixed        Event object or false
	 */
	public function get_event_by_hash( $hash ) {

		$events = $this->get_events();
		return isset( $events[ $hash ] ) ? $events[ $hash ] : false;

	}

	/**
	 * Checks if event hook is default WP Hook, thus protected
	 *
	 * @param  string $event_hook hook slug.
	 * @return boolean             true if protected
	 */
	public function is_protected( $event_hook ) {
		return in_array( $event_hook, $this->protected_events, true );
	}

	/**
	 * Counts the total number of events
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->get_events() );
	}

	/**
	 * Compares the event's next execution times
	 * Used by usort function
	 *
	 * @param  object $e1 Event 1.
	 * @param  object $e2 Event 2.
	 * @return int        -1 or 1 or 0, depends on the comparsion result
	 */
	public function compare_event_next_calls( $e1, $e2 ) {

		// phpcs:ignore
		if ( $e1->next_call == $e2->next_call ) {
			return 0;
		}

		return ( $e1->next_call < $e2->next_call ) ? -1 : 1;

	}

}
