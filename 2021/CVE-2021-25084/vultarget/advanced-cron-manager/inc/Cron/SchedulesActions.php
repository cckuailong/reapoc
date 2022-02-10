<?php
/**
 * Schedules Actions class
 * Handles actions on schedules
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager\Cron;

use underDEV\Utils;

/**
 * SchedulesActions class
 */
class SchedulesActions {

	/**
	 * Ajax class
	 *
	 * @var instance of underDEV\AdvancedCronManage\Utils\Ajax
	 */
	private $ajax;

	/**
	 * SchedulesLibrary class
	 *
	 * @var instance of underDEV\AdvancedCronManage\Cron\SchedulesLibrary
	 */
	private $library;

	/**
	 * Constructor
	 *
	 * @since 2.4.0
	 * @param Utils\Ajax       $ajax    Ajax object.
	 * @param SchedulesLibrary $library SchedulesLibrary object.
	 */
	public function __construct( Utils\Ajax $ajax, SchedulesLibrary $library ) {
		$this->ajax    = $ajax;
		$this->library = $library;
	}

	/**
	 * Insert schedule
	 *
	 * @return void
	 */
	public function insert() {

		$this->ajax->verify_nonce( 'acm/schedule/insert' );

		// phpcs:ignore
		$data = wp_parse_args( $_REQUEST['data'], array() );

		$slug = sanitize_title_with_dashes( $data['slug'], null, 'save' );
		$slug = str_replace( '-', '_', $slug );

		$result = $this->library->insert( $slug, $data['name'], $data['interval'] );

		if ( is_array( $result ) ) {
			$errors = $result;
		} else {
			$errors = array();
		}

		// Translators: schedule slug.
		$success = sprintf( __( 'Schedule "%s" has been added', 'advanced-cron-manager' ), $data['name'] );

		$this->ajax->response( $success, $errors );

	}

	/**
	 * Edit schedule
	 *
	 * @return void
	 */
	public function edit() {

		$this->ajax->verify_nonce( 'acm/schedule/edit' );

		// phpcs:ignore
		$data = wp_parse_args( $_REQUEST['data'], array() );

		$result = $this->library->insert( $data['slug'], $data['name'], $data['interval'], true );

		if ( is_array( $result ) ) {
			$errors = $result;
		} else {
			$errors = array();
		}

		// Translators: schedule slug.
		$success = sprintf( __( 'Schedule "%s" has been edited', 'advanced-cron-manager' ), $data['name'] );

		$this->ajax->response( $success, $errors );

	}

	/**
	 * Remove schedule
	 *
	 * @return void
	 */
	public function remove() {

		// phpcs:ignore
		$schedule_slug = $_REQUEST['schedule'];

		$this->ajax->verify_nonce( 'acm/schedule/remove/' . $schedule_slug );

		$result = $this->library->remove( $schedule_slug );

		if ( is_array( $result ) ) {
			$errors = $result;
		} else {
			$errors = array();
		}

		// Translators: schedule slug.
		$success = sprintf( __( 'Schedule "%s" has been removed', 'advanced-cron-manager' ), $schedule_slug );

		$this->ajax->response( $success, $errors );

	}

}
