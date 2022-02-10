<?php
/**
 * Schedules class
 * Used to handle collection of schedules
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager\Cron;

/**
 * Schedules class
 */
class Schedules {

	/**
	 * Registered schedules
	 *
	 * @var array
	 */
	private $schedules = array();

	/**
	 * Schedules library
	 *
	 * @var object
	 */
	private $library = array();

	/**
	 * Constructor
	 *
	 * @param SchedulesLibrary $library SchedulesLibrary object.
	 */
	public function __construct( SchedulesLibrary $library ) {

		$this->library = $library;

	}

	/**
	 * Gets all registered schedules
	 * Supports lazy loading
	 *
	 * @param  boolean $force if refresh stored schedules.
	 * @return array          registered schedules
	 */
	public function get_schedules( $force = false ) {

		if ( empty( $this->schedules ) || $force ) {

			$this->schedules = array();

			foreach ( wp_get_schedules() as $slug => $params ) {

				if ( empty( $slug ) ) {
					continue;
				}

				if ( $this->library->has( $slug ) ) {
					$protected = false;
				} else {
					$protected = true;
				}

				$this->schedules[ $slug ] = new Element\Schedule( $slug, $params['interval'], $params['display'], $protected );

			}
		}

		return $this->schedules;

	}

	/**
	 * Counts the total number of schedules
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->get_schedules() );
	}

	/**
	 * Gets single schedule object
	 *
	 * @param  string $slug schedule slug name.
	 * @return mixed        schedule object or null
	 */
	public function get_schedule( $slug ) {

		$schedules = $this->get_schedules();

		return isset( $schedules[ $slug ] ) ? $schedules[ $slug ] : $this->get_single_event_schedule();

	}

	/**
	 * Gets single event fake schedule
	 *
	 * @return object
	 */
	public function get_single_event_schedule() {

		if ( ! isset( $this->single_event_schedule ) ) {
			$this->single_event_schedule = new Element\Schedule( 'single_event', 1, __( 'Single event', 'advanced-cron-manager' ), true );
		}

		return $this->single_event_schedule;

	}

}
