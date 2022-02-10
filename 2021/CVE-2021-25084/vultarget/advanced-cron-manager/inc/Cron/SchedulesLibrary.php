<?php
/**
 * Schedules Library class
 * Handles DB operations on schedules
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager\Cron;

use underDEV\Utils;

/**
 * Schedules Library class
 */
class SchedulesLibrary {

	/**
	 * Ajax class
	 *
	 * @var instance of underDEV\AdvancedCronManage\Utils\Ajax
	 */
	public $ajax;

	/**
	 * Option name
	 *
	 * @var string
	 */
	private $option_name;

	/**
	 * Saved schedules
	 * Format: schedule_slug => array( 'interval' => $interval, 'display' => $display )
	 *
	 * @var array
	 */
	private $schedules = array();

	/**
	 * Constructor
	 *
	 * @param Utils\Ajax $ajax Ajax object.
	 */
	public function __construct( Utils\Ajax $ajax ) {

		$this->ajax        = $ajax;
		$this->option_name = 'acm_schedules';

	}

	/**
	 * Gets all saved schedules
	 * Supports lazy loading
	 *
	 * @param  boolean $force if refresh stored schedules.
	 * @return array          saved schedules
	 */
	public function get_schedules( $force = false ) {

		if ( empty( $this->schedules ) || $force ) {

			$this->schedules = array();

			$schedules = get_option( $this->option_name, array() );

			foreach ( $schedules as $schedule_slug => $params ) {
				$this->schedules[ $schedule_slug ] = new Element\Schedule( $schedule_slug, $params['interval'], $params['display'], false );
			}
		}

		return $this->schedules;

	}

	/**
	 * Gets single schedule
	 *
	 * @param  string $slug Schedule slug.
	 * @return mixed        Schedule object on success or false
	 */
	public function get_schedule( $slug = '' ) {

		if ( empty( $slug ) ) {
			trigger_error( 'Schedule slug cannot be empty' );
		}

		$schedules = $this->get_schedules();

		return isset( $schedules[ $slug ] ) ? $schedules[ $slug ] : false;

	}

	/**
	 * Check if schedule is saved by ACM
	 *
	 * @param  string $schedule_slug schedule slug.
	 * @return boolean                true if yes
	 */
	public function has( $schedule_slug ) {
		$schedules = $this->get_schedules();
		return isset( $schedules[ $schedule_slug ] );
	}

	/**
	 * Registers all schedules
	 *
	 * @param  array $schedules Schedules already registered in WP.
	 * @return array            all Schedules
	 */
	public function register( $schedules ) {

		$acm_schedules = $this->get_schedules();

		foreach ( $acm_schedules as $schedule ) {

			$schedules[ $schedule->slug ] = array(
				'interval' => $schedule->interval,
				'display'  => $schedule->label,
			);

		}

		return $schedules;

	}

	/**
	 * Inserts new schedule in the database
	 * It also refreshed the current schedules
	 *
	 * @param  string $slug     Schedule slug.
	 * @param  string $name     Schedule name.
	 * @param  int    $interval Schedule interval in seconds.
	 * @param  bool   $edit     if this an edit action.
	 * @return mixed            true on success or array with errors
	 */
	public function insert( $slug, $name, $interval = 0, $edit = false ) {

		$errors = array();

		if ( empty( $name ) ) {
			$errors[] = __( 'Please, provide a name for your Schedule', 'advanced-cron-manager' );
		}

		if ( empty( $slug ) ) {
			$errors[] = __( 'Please, provide a slug for your Schedule', 'advanced-cron-manager' );
		}

		if ( $interval < 1 ) {
			$errors[] = __( 'Interval cannot be shorter than 1 second', 'advanced-cron-manager' );
		}

		if ( ! $edit && $this->has( $slug ) ) {
			// Translators: schedule slug.
			$errors[] = sprintf( __( 'Schedule with slug "%s" already exists', 'advanced-cron-manager' ), $slug );
		}

		if ( $edit ) {

			if ( ! $this->has( $slug ) ) {
				// Translators: schedule slug.
				$errors[] = sprintf( __( 'Schedule with slug "%s" doesn\'t exists', 'advanced-cron-manager' ), $slug );
			}

			if ( $this->get_schedule( $slug )->protected ) {
				// Translators: schedule slug.
				$errors[] = sprintf( __( 'Schedule "%s" is protected and you cannot edit it', 'advanced-cron-manager' ), $slug );
			}
		}

		if ( ! empty( $errors ) ) {
			return $errors;
		}

		$schedules = get_option( $this->option_name, array() );

		$schedules[ $slug ] = array(
			'interval' => $interval,
			'display'  => $name,
		);

		update_option( $this->option_name, $schedules );

		$this->schedules[ $slug ] = new Element\Schedule( $slug, $interval, $name, false );

		return true;

	}

	/**
	 * Inserts new schedule in the database
	 * It also refreshed the current schedules
	 *
	 * @param  string $slug Schedule slug.
	 * @return mixed        true on success or array with errors
	 */
	public function remove( $slug ) {

		$errors = array();

		if ( ! $this->has( $slug ) ) {
			// Translators: schedule slug.
			$errors[] = sprintf( __( 'Schedule with slug "%s" cannot be removed because it doesn\'t exists', 'advanced-cron-manager' ), $slug );
		}

		if ( ! empty( $errors ) ) {
			return $errors;
		}

		$schedules = get_option( $this->option_name, array() );
		unset( $schedules[ $slug ] );
		update_option( $this->option_name, $schedules );

		unset( $this->schedules[ $slug ] );

		return true;

	}

}
