<?php
/**
 * FormProvider class
 * Provides forms for all plugin actions
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager;

use underDEV\Utils;
use underDEV\AdvancedCronManager\Cron;

/**
 * FormProvider class
 */
class FormProvider {

	/**
	 * View class
	 *
	 * @var instance of underDEV\AdvancedCronManager\Utils\View
	 */
	public $view;

	/**
	 * Ajax class
	 *
	 * @var instance of underDEV\AdvancedCronManager\Utils\Ajax
	 */
	public $ajax;

	/**
	 * SchedulesLibrary class
	 *
	 * @var instance of underDEV\AdvancedCronManager\Cron\SchedulesLibrary
	 */
	public $schedules_library;

	/**
	 * Schedules class
	 *
	 * @var instance of underDEV\AdvancedCronManager\Cron\Schedules
	 */
	public $schedules;

	/**
	 * Contructor
	 *
	 * @param Utils\View            $view              View class.
	 * @param Utils\Ajax            $ajax              Ajax class.
	 * @param Cron\SchedulesLibrary $schedules_library SchedulesLibrary class.
	 * @param Cron\Schedules        $schedules         Schedules class.
	 */
	public function __construct( Utils\View $view, Utils\Ajax $ajax, Cron\SchedulesLibrary $schedules_library, Cron\Schedules $schedules ) {
		$this->view              = $view;
		$this->ajax              = $ajax;
		$this->schedules_library = $schedules_library;
		$this->schedules         = $schedules;
	}

	/**
	 * Gets specified form
	 *
	 * @param  string $form_name  form slug which will be passed to View.
	 * @param  string $form_title heading for form.
	 * @param  string $cta        send button label.
	 * @return void               prints and dies form html
	 */
	public function get_form( $form_name = null, $form_title = '', $cta = '' ) {

		if ( null === $form_name ) {
			trigger_error( 'Form name cannot be empty' );
		}

		ob_start();

		$this->view->set_var( 'heading', $form_title );
		$this->view->set_var( 'cta', $cta );
		$this->view->set_var( 'form_class', str_replace( '/', '-', $form_name ) );

		$this->view->get_view( 'forms/' . $form_name );

		$form_html = ob_get_clean();

		$this->ajax->response( $form_html );

	}

	/**
	 * Add schedule form
	 */
	public function add_schedule() {

		$this->ajax->verify_nonce( 'acm/schedule/add' );

		$this->get_form( 'schedule/add', __( 'New schedule', 'advanced-cron-manager' ), __( 'Add schedule', 'advanced-cron-manager' ) );

	}

	/**
	 * Edit schedule form
	 */
	public function edit_schedule() {

		// phpcs:ignore
		$schedule_slug = $_REQUEST['schedule'];

		$this->ajax->verify_nonce( 'acm/schedule/edit/' . $schedule_slug );

		$schedule = $this->schedules_library->get_schedule( $schedule_slug );

		$this->view->set_var( 'schedule', $schedule );

		// Translators: schedule slug.
		$this->get_form( 'schedule/edit', sprintf( __( 'Edit "%s" schedule', 'advanced-cron-manager' ), $schedule->slug ), __( 'Edit schedule', 'advanced-cron-manager' ) );

	}

	/**
	 * Add event form
	 */
	public function add_event() {

		$this->ajax->verify_nonce( 'acm/event/add' );

		$this->view->set_var( 'schedules', $this->schedules->get_schedules() );
		$this->view->set_var( 'single_schedule', $this->schedules->get_single_event_schedule() );

		$this->get_form( 'event/add', __( 'New event', 'advanced-cron-manager' ), __( 'Add event', 'advanced-cron-manager' ) );

	}

}
