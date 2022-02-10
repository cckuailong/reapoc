<?php

class Tribe__Tickets__RSVP extends Tribe__Tickets__Tickets {
	/**
	 * {@inheritdoc}
	 */
	public $orm_provider = 'rsvp';

	/**
	 * Name of the CPT that holds Attendees (tickets holders).
	 *
	 * @var string
	 */
	const ATTENDEE_OBJECT = 'tribe_rsvp_attendees';

	/**
	 * Name of the CPT that holds Attendees (tickets holders).
	 *
	 * @var string
	 */
	public $attendee_object = 'tribe_rsvp_attendees';

	/**
	 * Name of the CPT that holds Orders
	 */
	const ORDER_OBJECT = 'tribe_rsvp_attendees';

	/**
	 * Meta key that relates Attendees and Events.
	 *
	 * @var string
	 */
	const ATTENDEE_EVENT_KEY = '_tribe_rsvp_event';

	/**
	 * Meta key that relates Attendees and Products.
	 *
	 * @var string
	 */
	const ATTENDEE_PRODUCT_KEY = '_tribe_rsvp_product';

	/**
	 * Currently unused for this provider, but defined per the Tribe__Tickets__Tickets spec.
	 *
	 * @var string
	 */
	const ATTENDEE_ORDER_KEY = '';

	/**
	 * Indicates if a ticket for this attendee was sent out via email.
	 *
	 * @var boolean
	 */
	const ATTENDEE_TICKET_SENT = '_tribe_rsvp_attendee_ticket_sent';

	/**
	 * Holds the number of times a ticket for this attendee was sent out via email.
	 *
	 * @var boolean
	 */
	public $attendee_ticket_sent = '_tribe_rsvp_attendee_ticket_sent';

	/**
	 * Name of the CPT that holds Tickets
	 *
	 * @var string
	 */
	public $ticket_object = 'tribe_rsvp_tickets';

	/**
	 * Meta key that relates Products and Events
	 * @var string
	 */
	public $event_key = '_tribe_rsvp_for_event';

	/**
	 * Meta key that stores if an attendee has checked in to an event
	 * @var string
	 */
	public $checkin_key = '_tribe_rsvp_checkedin';

	/**
	 * Meta key that ties attendees together by order
	 * @var string
	 */
	public $order_key = '_tribe_rsvp_order';

	/**
	 * Meta key that holds the security code that's printed in the tickets
	 * @var string
	 */
	public $security_code = '_tribe_rsvp_security_code';

	/**
	 * Meta key that if this attendee wants to show on the attendee list
	 *
	 * @var string
	 */
	const ATTENDEE_OPTOUT_KEY = '_tribe_rsvp_attendee_optout';

	/**
	 * Meta key that if this attendee rsvp status
	 *
	 * @var string
	 */
	const ATTENDEE_RSVP_KEY = '_tribe_rsvp_status';

	/**
	 * Meta key that holds the full name of the tickets RSVP "buyer"
	 *
	 * @var string
	 */
	public $full_name = '_tribe_rsvp_full_name';

	/**
	 * Meta key that holds the email of the tickets RSVP "buyer"
	 *
	 * @var string
	 */
	public $email = '_tribe_rsvp_email';

	/**
	 * Meta key that holds the name of a ticket to be used in reports if the Product is deleted
	 * @var string
	 */
	public $deleted_product = '_tribe_deleted_product_name';

	/**
	 * Meta key that holds the "not going" option visibility status.
	 *
	 * @var string
	 */
	public $show_not_going = '_tribe_ticket_show_not_going';

	/**
	 * @var Tribe__Tickets__RSVP__Attendance_Totals
	 */
	protected $attendance_totals;

	/**
	 * Messages for submission
	 */
	protected static $messages = array();

	/**
	 * @var Tribe__Tickets__Tickets_View
	 */
	protected $tickets_view;

	/**
	 * Array of not going statuses
	 */
	protected $not_going_statuses = array();

	/**
	 * Creates a Variable to prevent Double FE forms
	 * @var boolean
	 */
	private $is_frontend_tickets_form_done = false;

	/**
	 * Instance of this class for use as singleton
	 */
	private static $instance;

	/**
	 * Creates the instance of the class
	 *
	 * @static
	 * @return void
	 */
	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 *
	 * @static
	 * @return Tribe__Tickets__RSVP
	 */
	public static function get_instance() {
		return tribe( 'tickets.rsvp' );
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		$main = Tribe__Tickets__Main::instance();
		$this->tickets_view = Tribe__Tickets__Tickets_View::instance();
		/* Set up parent vars */
		$this->plugin_name = $this->pluginName = esc_html( tribe_get_rsvp_label_plural( 'provider_plugin_name' ) );
		$this->plugin_path = $this->pluginPath = $main->plugin_path;
		$this->plugin_url  = $this->pluginUrl  = $main->plugin_url;

		parent::__construct();

		$this->hooks();

		add_action( 'init', array( $this, 'init' ) );

		/**
		 * Whenever we are dealing with Redirects we cannot do stuff on `init`
		 * Use: `template_redirect`
		 *
		 * Was running into an issue of `get_permalink( $event_id )` returning
		 * the wrong url because it was too early on the execution
		 */
		add_action( 'template_redirect', array( $this, 'maybe_generate_tickets' ), 10, 0 );
		add_action( 'event_tickets_attendee_update', array( $this, 'update_attendee_data' ), 10, 3 );
		add_action( 'event_tickets_after_attendees_update', array( $this, 'maybe_send_tickets_after_status_change' ) );
	}

	/**
	 * Registers all actions/filters
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_resources' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_resources' ), 11 );
		add_action( 'trashed_post', array( $this, 'maybe_redirect_to_attendees_report' ) );
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
		add_action( 'rsvp_checkin', array( $this, 'purge_attendees_transient' ) );
		add_action( 'rsvp_uncheckin', array( $this, 'purge_attendees_transient' ) );
		add_action( 'tribe_events_tickets_attendees_event_details_top', array( $this, 'setup_attendance_totals' ) );
		add_filter( 'tribe_get_cost', [ $this, 'trigger_get_cost' ], 10, 3 );
		add_filter(
			'event_tickets_attendees_rsvp_checkin_stati',
			array( $this, 'filter_event_tickets_attendees_rsvp_checkin_stati' )
		);

		if ( is_user_logged_in() ) {
			add_filter( 'tribe_tickets_rsvp_form_full_name', array( $this, 'rsvp_form_add_full_name' ) );
			add_filter( 'tribe_tickets_rsvp_form_email', array( $this, 'rsvp_form_add_email' ) );
		}

		// Has to be run on before_delete_post to be sure the meta is still available (and we don't want it to run again after the post is deleted)
		// See https://codex.wordpress.org/Plugin_API/Action_Reference/delete_post
		add_action( 'before_delete_post', array( $this, 'update_stock_from_attendees_page' ) );

		// Handle RSVP AJAX.
		add_action( 'wp_ajax_nopriv_tribe_tickets_rsvp_handle', [ $this, 'ajax_handle_rsvp' ] );
		add_action( 'wp_ajax_tribe_tickets_rsvp_handle', [ $this, 'ajax_handle_rsvp' ] );
	}

	/**
	 * Handle RSVP processing for the RSVP forms.
	 *
	 * @since 4.12.3
	 */
	public function ajax_handle_rsvp() {
		$response = [
			'html' => '',
		];

		$ticket_id = absint( tribe_get_request_var( 'ticket_id', 0 ) );
		$step      = tribe_get_request_var( 'step', null );

		$render_response = $this->render_rsvp_step( $ticket_id, $step );

		if ( is_string( $render_response ) && '' !== $render_response ) {
			// Return the HTML if it's a string.
			$response['html'] = $render_response;

			wp_send_json_success( $response );
		} elseif ( is_array( $render_response ) && ! empty( $render_response['errors'] ) ) {
			$response['html'] = $this->render_rsvp_error( $render_response['errors'] );

			wp_send_json_error( $response );
		}

		$response['html'] = $this->render_rsvp_error( __( 'Something happened here.', 'event-tickets' ) );

		wp_send_json_error( $response );
	}

	/**
	 * Handle RSVP processing for the RSVP forms.
	 *
	 * @since 4.12.3
	 *
	 * @param int         $ticket_id The ticket ID.
	 * @param null|string $step      Which step to render.
	 *
	 * @return string The step template HTML.
	 */
	public function render_rsvp_step( $ticket_id, $step = null ) {
		// No ticket.
		if ( 0 === $ticket_id ) {
			return '';
		}

		$post_id = (int) get_post_meta( $ticket_id, $this->get_event_key(), true );

		// No post found, something went wrong.
		if ( 0 === $post_id ) {
			return '';
		}

		/** @var \Tribe__Tickets__Editor__Blocks__Rsvp $blocks_rsvp */
		$blocks_rsvp = tribe( 'tickets.editor.blocks.rsvp' );

		/** @var \Tribe__Tickets__Editor__Template $template */
		$template = tribe( 'tickets.editor.template' );

		$ticket = $this->get_ticket( $post_id, $ticket_id );

		// No ticket found.
		if ( null === $ticket ) {
			return '';
		}

		// Set required template globals.
		$args = [
			'rsvp_id'    => $ticket_id,
			'post_id'    => $post_id,
			'rsvp'       => $ticket,
			'step'       => $step,
			'must_login' => ! is_user_logged_in() && $this->login_required(),
			'login_url'  => self::get_login_url( $post_id ),
			'threshold'  => $blocks_rsvp->get_threshold( $post_id ),
		];

		/**
		 * Allow filtering of the template arguments used prior to processing.
		 *
		 * @since 5.0.0
		 *
		 * @param array $args {
		 *      The list of step template arguments.
		 *
		 *      @type int                           $rsvp_id    The RSVP ticket ID.
		 *      @type int                           $post_id    The ticket ID.
		 *      @type Tribe__Tickets__Ticket_Object $rsvp       The RSVP ticket object.
		 *      @type null|string                   $step       Which step being rendered.
		 *      @type boolean                       $must_login Whether login is required to register.
		 *      @type string                        $login_url  The site login URL.
		 *      @type int                           $threshold  The RSVP ticket threshold.
		 * }
		 */
		$args = apply_filters( 'tribe_tickets_rsvp_render_step_template_args_pre_process', $args );

		$args['process_result'] = $this->process_rsvp_step( $args );

		/**
		 * Allow filtering of the template arguments used.
		 *
		 * @since 4.12.3
		 *
		 * @param array $args {
		 *      The list of step template arguments.
		 *
		 *      @type int                           $rsvp_id        The RSVP ticket ID.
		 *      @type int                           $post_id        The ticket ID.
		 *      @type Tribe__Tickets__Ticket_Object $rsvp           The RSVP ticket object.
		 *      @type null|string                   $step           Which step being rendered.
		 *      @type boolean                       $must_login     Whether login is required to register.
		 *      @type string                        $login_url      The site login URL.
		 *      @type int                           $threshold      The RSVP ticket threshold.
		 *      @type array                         $process_result The processing result.
		 * }
		 */
		$args = apply_filters( 'tribe_tickets_rsvp_render_step_template_args', $args );

		// Return the process result for opt-in.
		if ( false === $args['process_result']['success'] ) {
			return $args['process_result'];
		}

		$args['opt_in_checked']      = false;
		$args['opt_in_attendee_ids'] = '';
		$args['opt_in_nonce']        = '';
		$args['is_going']            = null;

		if ( ! empty( $args['process_result']['opt_in_args'] ) ) {
			// Refresh ticket.
			$args['rsvp'] = $this->get_ticket( $post_id, $ticket_id );

			$args['is_going']            = $args['process_result']['opt_in_args']['is_going'];
			$args['opt_in_checked']      = $args['process_result']['opt_in_args']['checked'];
			$args['opt_in_attendee_ids'] = $args['process_result']['opt_in_args']['attendee_ids'];
			$args['opt_in_nonce']        = $args['process_result']['opt_in_args']['opt_in_nonce'];
		}

		// Check to see if attendees list is being hidden or not.
		if ( class_exists( 'Tribe__Tickets_Plus__Attendees_List' ) ) {
			// Handle Event Tickets Plus compatible logic.
			$hide_attendee_list_optout = Tribe__Tickets_Plus__Attendees_List::is_hidden_on( $post_id );
		} else {
			// Handle Event Tickets logic.
			$hide_attendee_list_optout = \Tribe\Tickets\Events\Attendees_List::is_hidden_on( $post_id );
		}

		/**
		 * Allow filtering of whether to show the opt-in option for attendees.
		 *
		 * @since 4.5.2
		 * @since 5.0.0 Added $post_id and $ticket_id parameters.
		 *
		 * @param bool $hide_attendee_list_optout Whether to hide attendees list opt-out.
		 * @param int  $post_id                   The post ID that the ticket belongs to.
		 * @param int  $ticket_id                 The ticket ID.
		 */
		$hide_attendee_list_optout = apply_filters( 'tribe_tickets_hide_attendees_list_optout', $hide_attendee_list_optout, $post_id, $ticket_id );

		if ( false === $args['is_going'] ) {
			$hide_attendee_list_optout = true;
		}

		$args['opt_in_toggle_hidden'] = $hide_attendee_list_optout;

		// Add the rendering attributes into global context.
		$template->add_template_globals( $args );

		$html  = $template->template( 'v2/components/loader/loader', [ 'classes' => [] ], false );
		$html .= $template->template( 'v2/rsvp/content', $args, false );

		return $html;
	}

	/**
	 * Handle RSVP error rendering.
	 *
	 * @since 5.0.0
	 *
	 * @param string|array $error_message The error message(s).
	 *
	 * @return string The error template HTML.
	 */
	public function render_rsvp_error( $error_message ) {
		// Set required template globals.
		$args = [
			'error_message' => $error_message,
		];

		/** @var \Tribe__Tickets__Editor__Template $template */
		$template = tribe( 'tickets.editor.template' );

		// Add the rendering attributes into global context.
		$template->add_template_globals( $args );

		return $template->template( 'v2/rsvp/messages/error', $args, false );
	}

	/**
	 * Handle processing the RSVP step based on current arguments.
	 *
	 * @since 4.12.3
	 *
	 * @param array $args {
	 *      The list of step template arguments.
	 *
	 *      @type int                           $rsvp_id    The RSVP ticket ID.
	 *      @type int                           $post_id    The ticket ID.
	 *      @type Tribe__Tickets__Ticket_Object $rsvp       The RSVP ticket object.
	 *      @type null|string                   $step       Which step being rendered.
	 *      @type boolean                       $must_login Whether login is required to register.
	 *      @type string                        $login_url  The site login URL.
	 *      @type int                           $threshold  The RSVP ticket threshold.
	 * }
	 *
	 * @return array The process result.
	 */
	public function process_rsvp_step( array $args ) {
		$result = [
			'success' => null,
			'errors'  => [],
		];

		// Process the attendee.
		if ( 'success' === $args['step'] ) {
			$first_attendee = $this->parse_attendee_details();

			/**
			 * These are the inputs we should be seeing:
			 *
			 * tribe_tickets[{$ticket_id}][ticket_id] (previously product_id[])
			 * tribe_tickets[{$ticket_id}][quantity] (previously quantity_{$ticket_id})
			 * tribe_tickets[{$ticket_id}][attendees][0][order_status] (previously attendee[order_status])
			 * tribe_tickets[{$ticket_id}][attendees][0][full_name] (previously attendee[full_name])
			 * tribe_tickets[{$ticket_id}][attendees][0][email] (previously attendee[email])
			 * tribe_tickets[{$ticket_id}][attendees][0][meta][{$field_slug}] (previously tribe-tickets-meta[{$ticket_id}][0][{$field_slug}])
			 * tribe_tickets[{$ticket_id}][attendees][1][full_name] (new for IAC)
			 * tribe_tickets[{$ticket_id}][attendees][1][email] (new for IAC)
			 * tribe_tickets[{$ticket_id}][attendees][1][meta][{$field_slug}] (previously tribe-tickets-meta[{$ticket_id}][1][{$field_slug}])
			 */
			$attendee_ids = $this->generate_tickets( $args['post_id'], false );

			if ( false === $attendee_ids ) {
				$result['success']  = false;
				$result['errors'][] = __( 'Your RSVP was unsuccessful, please try again.', 'event-tickets' );

				return $result;
			}

			$attendee_ids = implode( ',', $attendee_ids );

			$nonce_action = 'tribe-tickets-rsvp-opt-in-' . md5( $attendee_ids );

			$result['success']     = true;
			$result['opt_in_args'] = [
				'is_going'     => ! empty( $first_attendee['order_status'] ) ? 'yes' === $first_attendee['order_status'] : false,
				'checked'      => false,
				'attendee_ids' => $attendee_ids,
				'opt_in_nonce' => wp_create_nonce( $nonce_action ),
			];
		} elseif ( 'opt-in' === $args['step'] ) {
			/**
			 * These are the inputs we should be seeing:
			 *
			 * opt_in
			 * attendee_ids
			 * opt_in_nonce
			 */
			// @todo Handle opt-in setting for each attendee in order.
			$optout = ! tribe_is_truthy( tribe_get_request_var( 'opt_in', true ) );

			$attendee_ids = Tribe__Utils__Array::list_to_array( tribe_get_request_var( 'attendee_ids', [] ) );
			$attendee_ids = array_map( 'absint', $attendee_ids );

			$attendee_ids_flat = implode( ',', $attendee_ids );

			$nonce_value  = tribe_get_request_var( 'opt_in_nonce', '' );
			$nonce_action = 'tribe-tickets-rsvp-opt-in-' . md5( $attendee_ids_flat );

			if ( false === wp_verify_nonce( $nonce_value, $nonce_action ) ) {
				$result['success']  = false;
				$result['errors'][] = __( 'Unable to verify your opt-in request, please try again.', 'event-tickets' );

				return $result;
			}

			foreach ( $attendee_ids as $attendee_id ) {
				// @todo This class is not setting $this->attendee_optout_key.
				update_post_meta( $attendee_id, self::ATTENDEE_OPTOUT_KEY, (int) $optout );
			}

			$result['success']     = true;
			$result['opt_in_args'] = [
				'is_going'     => true,
				'checked'      => ! $optout,
				'attendee_ids' => $attendee_ids_flat,
				'opt_in_nonce' => $nonce_value,
			];
		}

		return $result;
	}

	/**
	 * Hooks into the filter `tribe_tickets_rsvp_form_full_name` to add the user full name if user is logged in
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function rsvp_form_add_full_name( $name = '' ) {
		$current_user = wp_get_current_user();
		$name_parts = array( $current_user->first_name, $current_user->last_name );
		$name = implode( ' ', array_filter( $name_parts ) );
		if ( empty( $name ) ) {
			$name = $current_user->display_name;
		}
		return $name;
	}

	/**
	 * Hook into the filter `tribe_tickets_rsvp_form_email` to add the user default email.
	 *
	 * @since 4.7.1
	 *
	 * @param string $default_email
	 *
	 * @return string
	 */
	public function rsvp_form_add_email() {
		$current_user = wp_get_current_user();
		return $current_user->user_email;
	}

	/**
	 * Generates an Order ID.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public static function generate_order_id() {
		return md5( time() . rand() );
	}

	/**
	 * Hooked to the init action
	 */
	public function init() {
		$this->register_types();
	}

	/**
	 * registers resources
	 */
	public function register_resources() {
		$main = Tribe__Tickets__Main::instance();

		$stylesheet_url = $main->plugin_url . 'src/resources/css/rsvp.css';
		$stylesheet_url = Tribe__Template_Factory::getMinFile( $stylesheet_url, true );

		// apply filters
		$stylesheet_url = apply_filters( 'tribe_tickets_rsvp_stylesheet_url', $stylesheet_url );

		wp_register_style(
			'event-tickets-rsvp',
			$stylesheet_url,
			array(),
			apply_filters( 'tribe_tickets_rsvp_css_version', Tribe__Tickets__Main::VERSION )
		);

		$js_url = $main->plugin_url . 'src/resources/js/rsvp.js';
		$js_url = Tribe__Template_Factory::getMinFile( $js_url, true );
		$js_url = apply_filters( 'tribe_tickets_rsvp_js_url', $js_url );

		wp_register_script(
			'event-tickets-rsvp',
			$js_url,
			array( 'jquery' ),
			apply_filters( 'tribe_tickets_rsvp_js_version', Tribe__Tickets__Main::VERSION ),
			true
		);

		wp_localize_script( 'event-tickets-rsvp', 'tribe_tickets_rsvp_strings', array(
			'attendee' => _x( 'Attendee %1$s', 'Attendee number', 'event-tickets' ),
		) );
	}

	/**
	 * Enqueue the plugin stylesheet(s).
	 *
	 * @author caseypicker
	 * @since  3.9
	 * @return void
	 */
	public function enqueue_resources() {
		$post_types = Tribe__Tickets__Main::instance()->post_types();

		if ( ! is_singular( $post_types ) ) {
			return;
		}

		wp_enqueue_style( 'event-tickets-rsvp' );
		wp_enqueue_script( 'event-tickets-rsvp' );

		// Check for override stylesheet
		$user_stylesheet_url = Tribe__Templates::locate_stylesheet( 'tribe-events/tickets/rsvp.css' );

		// If override stylesheet exists, then enqueue it
		if ( $user_stylesheet_url ) {
			wp_enqueue_style( 'tribe-events-tickets-rsvp-override-style', $user_stylesheet_url );
		}
	}

	/**
	 * Register our custom post type
	 */
	public function register_types() {

		$ticket_post_args = array(
			'label'           => 'Tickets',
			'labels'          => array(
				'name'          => __( 'RSVP Tickets', 'event-tickets' ),
				'singular_name' => __( 'RSVP Ticket', 'event-tickets' ),
			),
			'public'          => false,
			'show_ui'         => false,
			'show_in_menu'    => false,
			'query_var'       => false,
			'rewrite'         => false,
			'capability_type' => 'post',
			'has_archive'     => false,
			'hierarchical'    => true,
		);

		$attendee_post_args = array(
			'label'           => 'Attendees',
			'public'          => false,
			'show_ui'         => false,
			'show_in_menu'    => false,
			'query_var'       => false,
			'rewrite'         => false,
			'capability_type' => 'post',
			'has_archive'     => false,
			'hierarchical'    => true,
		);

		/**
		 * Filter the arguments that craft the ticket post type.
		 *
		 * @since 4.4.6
		 *
		 * @see register_post_type
		 *
		 * @param array $ticket_post_args Post type arguments, passed to register_post_type()
		 */
		$ticket_post_args = apply_filters( 'tribe_tickets_register_ticket_post_type_args', $ticket_post_args );

		register_post_type( $this->ticket_object, $ticket_post_args );

		/**
		 * Filter the arguments that craft the attendee post type.
		 *
		 * @since 4.4.6
		 *
		 * @see register_post_type
		 *
		 * @param array $attendee_post_args Post type arguments, passed to register_post_type()
		 */
		$attendee_post_args = apply_filters( 'tribe_tickets_register_attendee_post_type_args', $attendee_post_args );

		register_post_type( self::ATTENDEE_OBJECT, $attendee_post_args );
	}

	/**
	 * Adds RSVP attendance totals to the summary box of the attendance
	 * screen.
	 *
	 * Expects to fire during 'tribe_tickets_attendees_page_inside', ie
	 * before the attendee screen is rendered.
	 */
	public function setup_attendance_totals() {
		$this->attendance_totals()->integrate_with_attendee_screen();
	}

	/**
	 * @return Tribe__Tickets__RSVP__Attendance_Totals
	 */
	public function attendance_totals() {
		if ( empty( $this->attendance_totals ) ) {
			$this->attendance_totals = new Tribe__Tickets__RSVP__Attendance_Totals;
		}

		return $this->attendance_totals;
	}

	/**
	 * Get Status by Action from Status Manager
	 *
	 * @since 4.10
	 *
	 * @param $action string|array a string or array of actions that a status includes
	 *
	 * @return array an array of statuses
	 */
	public function get_statuses_by_action( $action ) {
		/** @var Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		return $status_mgr->get_statuses_by_action( $action, 'rsvp' );
	}

	/**
	 * Create an attendee for a RSVP ticket.
	 *
	 * @since 5.0.0
	 *
	 * @param \Tribe__Tickets__Ticket_Object $ticket        Ticket object.
	 * @param array                          $attendee_data Attendee data.
	 *
	 * @return int Attendee ID.
	 *
	 * @throws Exception The exception if attendee failed to be created.
	 */
	public function create_attendee_for_ticket( $ticket, $attendee_data ) {
		$rsvp_options = \Tribe__Tickets__Tickets_View::instance()->get_rsvp_options( null, false );

		$required_details = [
			'full_name',
			'email',
		];

		foreach ( $required_details as $required_detail ) {
			// Detail is not set.
			if ( ! isset( $attendee_data[ $required_detail ] ) ) {
				/* translators: %s is the attendee field name. */
				throw new Exception( sprintf( __( 'Attendee field "%s" is not set.', 'event-tickets' ), $required_detail ) );
			}

			// Detail is empty.
			if ( empty( $attendee_data[ $required_detail ] ) ) {
				/* translators: %s is the attendee field name. */
				throw new Exception( sprintf( __( 'Attendee field "%s" is empty.', 'event-tickets' ), $required_detail ) );
			}
		}

		$order_status = 'yes';

		if ( isset( $attendee_data['order_status'] ) ) {
			$order_status = $attendee_data['order_status'];
		} elseif ( isset( $attendee_data['attendee_status'] ) ) {
			$order_status = $attendee_data['attendee_status'];
		}

		$full_name         = $attendee_data['full_name'];
		$email             = $attendee_data['email'];
		$optout            = true;
		$user_id           = isset( $attendee_data['user_id'] ) ? (int) $attendee_data['user_id'] : 0;
		$order_id          = ! empty( $attendee_data['order_id'] ) ? $attendee_data['order_id'] : $this->generate_order_id();
		$product_id        = $ticket->ID;
		$order_attendee_id = isset( $attendee_data['order_attendee_id'] ) ? $attendee_data['order_attendee_id'] : null;

		if ( isset( $attendee_data['optout'] ) && '' !== $attendee_data['optout'] ) {
			$optout = tribe_is_truthy( $attendee_data['optout'] );
		}

		if ( 'going' === $order_status ) {
			$order_status = 'yes';
		} elseif ( 'not-going' === $order_status ) {
			$order_status = 'no';
		}

		if ( ! isset( $rsvp_options[ $order_status ] ) ) {
			$order_status = 'yes';
		}

		// Get the event this ticket is for.
		$post_id = (int) get_post_meta( $product_id, $this->event_key, true );

		if ( empty( $post_id ) ) {
			throw new Exception( __( 'Unable to process your request, invalid content resource.', 'event-tickets' ) );
		}

		/**
		 * Allow filtering the individual attendee name used when creating a new attendee.
		 *
		 * @since 5.0.3
		 *
		 * @param string                  $individual_attendee_name The attendee full name.
		 * @param int|null                $attendee_number          The attendee number index value from the order, starting with zero.
		 * @param int                     $order_id                 The order ID.
		 * @param int                     $ticket_id                The ticket ID.
		 * @param int                     $post_id                  The ID of the post associated to the ticket.
		 * @param Tribe__Tickets__Tickets $provider                 The current ticket provider object.
		 */
		$individual_attendee_name = apply_filters( 'tribe_tickets_attendee_create_individual_name', $full_name, $order_attendee_id, $order_id, $product_id, $post_id, $this );

		/**
		 * Allow filtering the individual attendee email used when creating a new attendee.
		 *
		 * @since 5.0.3
		 *
		 * @param string                  $individual_attendee_email The attendee email.
		 * @param int|null                $attendee_number           The attendee number index value from the order, starting with zero.
		 * @param int                     $order_id                 The order ID.
		 * @param int                     $ticket_id                 The ticket ID.
		 * @param int                     $post_id                   The ID of the post associated to the ticket.
		 * @param Tribe__Tickets__Tickets $provider                  The current ticket provider object.
		 */
		$individual_attendee_email = apply_filters( 'tribe_tickets_attendee_create_individual_email', $email, $order_attendee_id, $order_id, $product_id, $post_id, $this );

		$data = [
			'full_name'         => $individual_attendee_name,
			'email'             => $individual_attendee_email,
			'ticket_id'         => $product_id,
			'post_id'           => $post_id,
			'order_id'          => $order_id,
			'order_attendee_id' => $order_attendee_id,
			'user_id'           => $user_id,
			'optout'            => (int) $optout,
			'attendee_status'   => $order_status,
			'price_paid'        => 0,
		];

		/** @var Tribe__Tickets__Attendees $attendees */
		$attendees = tribe( 'tickets.attendees' );

		$attendee_object = $attendees->create_attendee( $ticket, $data );

		if ( ! $attendee_object ) {
			throw new Exception( __( 'Unable to process your request, attendee creation failed.', 'event-tickets' ) );
		}

		return $attendee_object->ID;
	}

	/**
	 * Update the RSVP values for this user.
	 *
	 * Note that, within this method, $attendee_id refers to the attendee or ticket ID
	 * (it does not refer to an "order" in the sense of a transaction that may include
	 * multiple tickets, as is the case in some other methods for instance).
	 *
	 * @param array $attendee_data Information that we are trying to save.
	 * @param int   $attendee_id   The attendee ID.
	 * @param int   $post_id       The event/post ID.
	 */
	public function update_attendee_data( $attendee_data, $attendee_id, $post_id ) {
		// Bail if the user is not logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();

		$rsvp_attendees    = $this->tickets_view->get_event_rsvp_attendees( $post_id, $user_id );
		$rsvp_attendee_ids = array_map( 'absint', wp_list_pluck( $rsvp_attendees, 'attendee_id' ) );

		// This makes sure we don't save attendees for orders that are not from this current user and event
		if ( ! in_array( $attendee_id, $rsvp_attendee_ids, true ) ) {
			return;
		}

		$attendee = array();

		// Get the attendee data for the attendee we are updating.
		foreach ( $rsvp_attendees as $test_attendee ) {
			if ( $attendee_id !== $test_attendee['attendee_id'] ) {
				continue;
			}

			$attendee = $test_attendee;
		}

		// Don't try to Save if it's restricted.
		if (
			! isset( $attendee['product_id'] )
			|| $this->tickets_view->is_rsvp_restricted( $post_id, $attendee['product_id'] )
		) {
			return;
		}

		$attendee_data_to_save = [];

		// Only update full name if set.
		if ( ! empty( $attendee_data['full_name'] ) ) {
			$attendee_data_to_save['full_name'] = sanitize_text_field( $attendee_data['full_name'] );
		}

		// Only update email if set.
		if ( ! empty( $attendee_data['email'] ) ) {
			$attendee_data['email'] = sanitize_email( $attendee_data['email'] );

			// Only update email if valid.
			if ( is_email( $attendee_data['email'] ) ) {
				$attendee_data_to_save['email'] = $attendee_data['email'];
			}
		}

		// Only update optout if set.
		if ( isset( $attendee_data['optout'] ) ) {
			$attendee_data_to_save['optout'] = (int) tribe_is_truthy( $attendee_data['optout'] );
		}

		if ( empty( $attendee_data['order_status'] ) || ! $this->tickets_view->is_valid_rsvp_option( $attendee_data['order_status'] ) ) {
			$attendee_status = null;
		} else {
			$attendee_status = $attendee_data['order_status'];
		}

		$ticket_id = $attendee['product_id'];

		//check if changing status will cause rsvp to go over capacity
		$previous_order_status = get_post_meta( $attendee_id, self::ATTENDEE_RSVP_KEY, true );

		// The status changed from "not going" to "going", check if we have the capacity to support it.
		if ( tribe_is_truthy( $attendee_status ) && in_array( $previous_order_status, $this->get_statuses_by_action( 'count_not_going' ), true ) ) {
			$capacity  = tribe_tickets_get_capacity( $ticket_id );
			$sales     = (int) get_post_meta( $ticket_id, 'total_sales', true );
			$unlimited = - 1;

			// If capacity is not unlimited and there is no capacity for switching, remove attendee status from being saved.
			if ( $unlimited !== $capacity && $sales + 1 > $capacity ) {
				// Set to null so it won't be changed.
				$attendee_status = null;
			}
		}

		$this->update_sales_and_stock_by_order_status( $attendee_id, $attendee_status, $ticket_id );

		if ( null !== $attendee_status ) {
			$attendee_data_to_save['attendee_status'] = $attendee_status;
		}

		// Only update if there's data to set.
		if ( empty( $attendee_data_to_save ) ) {
			return;
		}

		$this->update_attendee( $attendee_id, $attendee_data_to_save );
	}

	/**
	 * Triggers the sending of ticket emails after RSVP information is updated.
	 *
	 * This is useful if a user initially suggests they will not be attending
	 * an event (in which case we do not send tickets out) but where they
	 * incrementally amend the status of one or more of those tickets to
	 * attending, at which point we should send tickets out for any of those
	 * newly attending persons.
	 *
	 * @param $event_id
	 */
	public function maybe_send_tickets_after_status_change( $event_id ) {
		$transaction_ids = array();

		foreach ( $this->get_event_attendees( $event_id ) as $attendee ) {
			$transaction = get_post_meta( $attendee['attendee_id'], $this->order_key, true );

			if ( ! empty( $transaction ) ) {
				$transaction_ids[ $transaction ] = $transaction;
			}
		}

		foreach ( $transaction_ids as $transaction ) {
			// This method takes care of intelligently sending out emails only when
			// required, for attendees that have not yet received their tickets
			$this->send_tickets_email( $transaction, $event_id );
		}
	}

	/**
	 * Generate and store all the attendees information for a new order.
	 */
	public function maybe_generate_tickets() {
		$has_ticket_attendees = ! empty( $_POST['tribe_tickets'] );

		if (
			empty( $_POST['tickets_process'] )
			|| (
				! $has_ticket_attendees
				&& empty( $_POST['attendee'] )
			)
			|| (
				! $has_ticket_attendees
				&& empty( $_POST['product_id'] )
			)
		) {
			return;
		}

		$this->generate_tickets( get_the_ID() );
	}

	/**
	 * Generate and store all the attendees information for a new order.
	 *
	 * @param int|null $post_id  Post ID for ticket, null to use current post ID.
	 * @param boolean  $redirect Whether to redirect on error.
	 *
	 * @return array|false List of attendee ID(s) generated, or false if there was a problem.
	 */
	public function generate_tickets( $post_id = null, $redirect = true ) {
		$has_tickets = false;

		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		/**
		 * RSVP specific action fired just before a RSVP-driven attendee tickets for an order are generated
		 *
		 * @param $data $_POST Parameters comes from RSVP Form
		 */
		do_action( 'tribe_tickets_rsvp_before_order_processing', $_POST );

		// Parse the details submitted for the RSVP
		$attendee_details = $this->parse_attendee_details();

		// If there are details missing, we return to the event page with the rsvp_error
		if ( false === $attendee_details ) {
			if ( $redirect ) {
				$url = get_permalink();
				$url = add_query_arg( 'rsvp_error', 1, $url );

				wp_redirect( esc_url_raw( $url ) );
				tribe_exit();
			}

			return false;
		}

		$product_ids = [];

		if ( isset( $_POST['tribe_tickets'] ) ) {
			$product_ids = wp_list_pluck( $_POST['tribe_tickets'], 'ticket_id' );
		} elseif ( isset( $_POST['product_id'] ) ) {
			$product_ids = (array) $_POST['product_id'];
		}

		$product_ids = array_map( 'absint', $product_ids );
		$product_ids = array_filter( $product_ids );

		$attendee_ids = [];

		// Iterate over each product
		foreach ( $product_ids as $product_id ) {
			$ticket_qty = $this->parse_ticket_quantity( $product_id );

			if ( 0 === $ticket_qty ) {
				// If there were no RSVP tickets for the product added to the cart, continue.
				continue;
			}

			$tickets_generated = $this->generate_tickets_for( $product_id, $ticket_qty, $attendee_details, $redirect );

			if ( $tickets_generated ) {
				if ( is_array( $tickets_generated ) ) {
					$attendee_ids[] = $tickets_generated;
				}

				$has_tickets = true;
			}
		}

		if ( ! empty( $attendee_ids ) ) {
			$attendee_ids = array_merge( ...$attendee_ids );
		}

		$order_id              = $attendee_details['order_id'];
		$attendee_order_status = $attendee_details['order_status'];

		/**
		 * Fires when an RSVP attendee tickets have been generated.
		 *
		 * @param int    $order_id              ID of the RSVP order
		 * @param int    $post_id               ID of the post the order was placed for
		 * @param string $attendee_order_status status if the user indicated they will attend
		 */
		do_action( 'event_tickets_rsvp_tickets_generated', $order_id, $post_id, $attendee_order_status );

		/** @var Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		$send_mail_stati = $status_mgr->get_statuses_by_action( 'attendee_dispatch', 'rsvp' );

		/**
		 * Filters whether a confirmation email should be sent or not for RSVP tickets.
		 *
		 * This applies to attendance and non attendance emails.
		 *
		 * @param bool $send_mail Defaults to `true`.
		 */
		$send_mail = apply_filters( 'tribe_tickets_rsvp_send_mail', true );

		if ( $send_mail ) {
			/**
			 * Filters the attendee order stati that should trigger an attendance confirmation.
			 *
			 * Any attendee order status not listed here will trigger a non attendance email.
			 *
			 * @param array  $send_mail_stati       An array of default stati triggering an attendance email.
			 * @param int    $order_id              ID of the RSVP order
			 * @param int    $post_id               ID of the post the order was placed for
			 * @param string $attendee_order_status status if the user indicated they will attend
			 */
			$send_mail_stati = apply_filters(
				'tribe_tickets_rsvp_send_mail_stati', $send_mail_stati, $order_id, $post_id, $attendee_order_status
			);

			// No point sending tickets if their current intention is not to attend
			if ( $has_tickets && in_array( $attendee_order_status, $send_mail_stati, true ) ) {
				$this->send_tickets_email( $order_id, $post_id );
			} elseif ( $has_tickets ) {
				$this->send_non_attendance_confirmation( $order_id, $post_id );
			}
		}

		// Redirect to the same page to prevent double purchase on refresh
		if ( $redirect && ! empty( $post_id ) ) {
			$url = get_permalink( $post_id );
			$url = add_query_arg( 'rsvp_sent', 1, $url );

			wp_redirect( esc_url_raw( $url ) );
			tribe_exit();
		}

		return $attendee_ids;
	}

	/**
	 * Dispatches a confirmation email that acknowledges the user has RSVP'd
	 * including the tickets.
	 *
	 * @since 4.5.2 added $event_id parameter
	 *
	 * @param int $order_id
	 * @param int $event_id
	 */
	public function send_tickets_email( $order_id, $event_id = null ) {
		$all_attendees = $this->get_attendees_by_order_id( $order_id );

		$to_send = array();

		if ( empty( $all_attendees ) ) {
			return;
		}

		// Look at each attendee and check if a ticket was sent: in each case where a ticket
		// has not yet been sent we should a) send the ticket out by email and b) record the
		// fact it was sent
		foreach ( $all_attendees as $single_attendee ) {
			// Do not add those attendees/tickets marked as not attending (note that despite the name
			// 'qr_ticket_id', this key is not QR code specific, it's simply the attendee post ID)
			$going_status = get_post_meta( $single_attendee['qr_ticket_id'], self::ATTENDEE_RSVP_KEY, true );
			if ( in_array( $going_status, $this->get_statuses_by_action( 'count_not_going' ), true ) ) {
				continue;
			}

			// Only add those attendees/tickets that haven't already been sent
			if ( empty( $single_attendee['ticket_sent'] ) ) {
				$to_send[] = $single_attendee;
			}
		}

		/**
		 * Controls the list of tickets which will be emailed out.
		 *
		 * @param array $to_send        list of tickets to be sent out by email
		 * @param array $all_attendees  list of all attendees/tickets, including those already sent out
		 * @param int   $order_id
		 *
		 */
		$to_send = (array) apply_filters( 'tribe_tickets_rsvp_tickets_to_send', $to_send, $all_attendees, $order_id );

		if ( empty( $to_send ) ) {
			return;
		}

		// For now all ticket holders in an order share the same email
		$to = $all_attendees['0']['holder_email'];

		if ( ! is_email( $to ) ) {
			return;
		}

		/**
		 * Filters the RSVP tickets email headers
		 *
		 * @since 4.5.2 added new parameters $event_id and $order_id
		 *
		 * @param array  email headers
		 * @param int    $event_id
		 * @param int    $order_id
		 */
		$headers = apply_filters( 'tribe_rsvp_email_headers', array( 'Content-type: text/html' ), $event_id, $order_id );

		/**
		 * Filters the RSVP tickets email attachments
		 *
		 * @since 4.5.2 added new parameters $event_id and $order_id
		 *
		 * @param array  attachments
		 * @param int    $event_id
		 * @param int    $order_id
		 */
		$attachments = apply_filters( 'tribe_rsvp_email_attachments', array(), $event_id, $order_id );

		/**
		 * Filters the RSVP tickets email recipient
		 *
		 * @since 4.5.2 added new parameters $event_id and $order_id
		 *
		 * @param string  $to
		 * @param int     $event_id
		 * @param int     $order_id
		 */
		$to = apply_filters( 'tribe_rsvp_email_recipient', $to, $event_id, $order_id );

		/**
		 * Filters the RSVP tickets email subject
		 *
		 * @since 4.5.2 added new parameters $event_id and $order_id
		 * @since 4.10.9 Use customizable ticket name functions.
		 *
		 * @param string
		 * @param int    $event_id
		 * @param int    $order_id
		 */
		$subject = apply_filters( 'tribe_rsvp_email_subject',
			esc_html( sprintf(
				__( 'Your %1$s from %2$s', 'event-tickets' ),
				tribe_get_ticket_label_plural_lowercase( 'tribe_rsvp_email_subject' ),
				stripslashes_deep( html_entity_decode( get_bloginfo( 'name' ), ENT_QUOTES ) )
			) ),
			$event_id,
			$order_id
		);

		/**
		 * Filters the RSVP tickets email content
		 *
		 * @since 4.5.2 added new parameters $event_id and $order_id
		 *
		 * @param string  email content
		 * @param int     $event_id
		 * @param int     $order_id
		 */
		$content = apply_filters( 'tribe_rsvp_email_content', $this->generate_tickets_email_content( $to_send ), $event_id, $order_id );

		$sent = wp_mail( $to, $subject, $content, $headers, $attachments );

		if ( $sent ) {
			foreach ( $all_attendees as $attendee ) {
				$this->update_ticket_sent_counter( $attendee['qr_ticket_id'] );

				$this->update_attendee_activity_log(
					$attendee['attendee_id'],
					[
						'type'  => 'email',
						'name'  => $attendee['holder_name'],
						'email' => $attendee['holder_email'],
					]
				);
			}
		}
	}

	/**
	 * Dispatches a confirmation email that acknowledges the user has RSVP'd
	 * in cases where they have indicated that they will *not* be attending.
	 *
	 * @param int $order_id
	 * @param int $event_id
	 */
	public function send_non_attendance_confirmation( $order_id, $event_id ) {

		$attendees = $this->get_attendees_by_order_id( $order_id );

		if ( empty( $attendees ) ) {
			return;
		}

		// For now all ticket holders in an order share the same email
		$to = $attendees['0']['holder_email'];

		if ( ! is_email( $to ) ) {
			return;
		}

		/**
		 * Filters the non attending RSVP tickets email headers
		 *
		 * @since 4.5.2 added new parameters $event_id and $order_id
		 * @since 4.5.5 changed filter name to be unique to non attendance emails
		 *
		 * @param array  email headers
		 * @param int    $event_id
		 * @param int    $order_id
		 */
		$headers = apply_filters( 'tribe_rsvp_non_attendance_email_headers', array( 'Content-type: text/html' ), $event_id, $order_id );

		/**
		 * Filters the non attending RSVP tickets email attachments
		 *
		 * @since 4.5.2 added new parameters $event_id and $order_id
		 * @since 4.5.5 changed filter name to be unique to non attendance emails
		 *
		 * @param array  attachments
		 * @param int    $event_id
		 * @param int    $order_id
		 */
		$attachments = apply_filters( 'tribe_rsvp_non_attendance_email_attachments', array(), $event_id, $order_id );

		/**
		 * Filters the non attending RSVP tickets email recepient
		 *
		 * @since 4.5.2 added new parameters $event_id and $order_id
		 * @since 4.5.5 changed filter name to be unique to non attendance emails
		 *
		 * @param string  $to
		 * @param int     $event_id
		 * @param int     $order_id
		 */
		$to = apply_filters( 'tribe_rsvp_non_attendance_email_recipient', $to, $event_id, $order_id );

		/**
		 * Filters the non attending RSVP tickets email subject
		 *
		 * @since 4.5.2 added new parameters $event_id and $order_id
		 * @since 4.5.5 changed filter name to be unique to non attendance emails
		 *
		 * @param string
		 * @param int     $event_id
		 * @param int     $order_id
		 */
		$subject = apply_filters( 'tribe_rsvp_non_attendance_email_subject',
			sprintf( __( 'You confirmed you will not be attending %s', 'event-tickets' ), get_the_title( $event_id ) ),
			$event_id,
			$order_id
		);

		$template_data = array( 'event_id' => $event_id, 'order_id' => $order_id, 'attendees' => $attendees );

		/**
		 * Filters the non attending RSVP tickets email content
		 *
		 * @since 4.5.2 added new parameters $event_id and $order_id
		 * @since 4.5.5 changed filter name to be unique to non attendance emails
		 *
		 * @param string  email content
		 * @param int     $event_id
		 * @param int     $order_id
		 */
		$content = apply_filters( 'tribe_rsvp_non_attendance_email_content',
			tribe_tickets_get_template_part( 'tickets/email-non-attendance', null, $template_data, false ),
			$event_id,
			$order_id
		);

		wp_mail( $to, $subject, $content, $headers, $attachments );
	}

	/**
	 * Saves an RSVP ticket.
	 *
	 * @param int                           $post_id  Post ID.
	 * @param Tribe__Tickets__Ticket_Object $ticket   Ticket object.
	 * @param array                         $raw_data Ticket data.
	 *
	 * @return int The updated/created ticket post ID.
	 */
	public function save_ticket( $post_id, $ticket, $raw_data = array() ) {
		// Run anything we might need on parent method.
		parent::save_ticket( $post_id, $ticket, $raw_data );

		// assume we are updating until we find out otherwise
		$save_type = 'update';

		if ( empty( $ticket->ID ) ) {
			$save_type = 'create';

			/* Create main product post */
			$args = array(
				'post_status'  => 'publish',
				'post_type'    => $this->ticket_object,
				'post_author'  => get_current_user_id(),
				'post_excerpt' => $ticket->description,
				'post_title'   => $ticket->name,
				'menu_order'   => tribe_get_request_var( 'menu_order', -1 ),
			);

			$ticket->ID = wp_insert_post( $args );

			// Relate event <---> ticket
			add_post_meta( $ticket->ID, $this->get_event_key(), $post_id );

		} else {
			$args = array(
				'ID'           => $ticket->ID,
				'post_excerpt' => $ticket->description,
				'post_title'   => $ticket->name,
			);

			$ticket->ID = wp_update_post( $args );
		}

		if ( ! $ticket->ID ) {
			return false;
		}

		// Updates if we should show Description
		$ticket->show_description = isset( $ticket->show_description ) && tribe_is_truthy( $ticket->show_description ) ? 'yes' : 'no';
		update_post_meta( $ticket->ID, tribe( 'tickets.handler' )->key_show_description, $ticket->show_description );

		// Adds RSVP price
		update_post_meta( $ticket->ID, '_price', $ticket->price );

		$ticket_data = Tribe__Utils__Array::get( $raw_data, 'tribe-ticket', array() );
		$this->update_capacity( $ticket, $ticket_data, $save_type );

		if ( tribe_tickets_rsvp_new_views_is_enabled() ) {
			$show_not_going = 'no';

			if ( isset( $ticket_data['not_going'] ) ) {
				$show_not_going = $ticket_data['not_going'];
			}

			$show_not_going = tribe_is_truthy( $show_not_going ) ? 'yes' : 'no';
			update_post_meta( $ticket->ID, $this->show_not_going, $show_not_going );
		}

		if ( ! empty( $raw_data['ticket_start_date'] ) ) {
			$start_date = Tribe__Date_Utils::maybe_format_from_datepicker( $raw_data['ticket_start_date'] );

			if ( ! empty( $raw_data['ticket_start_time'] ) ) {
				$start_date .= ' ' . $raw_data['ticket_start_time'];
			}

			$ticket->start_date = date( Tribe__Date_Utils::DBDATETIMEFORMAT, strtotime( $start_date ) );
			$previous_start_date = get_post_meta( $ticket->ID, tribe( 'tickets.handler' )->key_start_date, true );

			// Only update when we are modifying
			if ( $ticket->start_date !== $previous_start_date ) {
				update_post_meta( $ticket->ID, tribe( 'tickets.handler' )->key_start_date, $ticket->start_date );
			}
		} else {
			delete_post_meta( $ticket->ID, '_ticket_start_date' );
		}

		if ( ! empty( $raw_data['ticket_end_date'] ) ) {
			$end_date = Tribe__Date_Utils::maybe_format_from_datepicker( $raw_data['ticket_end_date'] );

			if ( ! empty( $raw_data['ticket_end_time'] ) ) {
				$end_date .= ' ' . $raw_data['ticket_end_time'];
			}

			$end_date = strtotime( $end_date );

			$ticket->end_date = date( Tribe__Date_Utils::DBDATETIMEFORMAT, $end_date );
			$previous_end_date = get_post_meta( $ticket->ID, tribe( 'tickets.handler' )->key_end_date, true );

			// Only update when we are modifying
			if ( $ticket->end_date !== $previous_end_date ) {
				update_post_meta( $ticket->ID, tribe( 'tickets.handler' )->key_end_date, $ticket->end_date );
			}
		} else {
			delete_post_meta( $ticket->ID, '_ticket_end_date' );
		}

		/**
		 * Generic action fired after saving a ticket (by type)
		 *
		 * @var int Post ID of post the ticket is tied to
		 * @var Tribe__Tickets__Ticket_Object Ticket that was just saved
		 * @var array Ticket data
		 * @var string Commerce engine class
		 */
		do_action( 'event_tickets_after_' . $save_type . '_ticket', $post_id, $ticket, $raw_data, __CLASS__ );

		/**
		 * Generic action fired after saving a ticket
		 *
		 * @var int Post ID of post the ticket is tied to
		 * @var Tribe__Tickets__Ticket_Object Ticket that was just saved
		 * @var array Ticket data
		 * @var string Commerce engine class
		 */
		do_action( 'event_tickets_after_save_ticket', $post_id, $ticket, $raw_data, __CLASS__ );

		tribe( 'tickets.version' )->update( $ticket->ID );

		return $ticket->ID;
	}

	/**
	 * Deletes a ticket
	 *
	 * @param $event_id
	 * @param $ticket_id
	 *
	 * @return bool
	 */
	public function delete_ticket( $event_id, $ticket_id ) {
		// Run anything we might need on parent method.
		parent::delete_ticket( $event_id, $ticket_id );

		// Ensure we know the event and product IDs (the event ID may not have been passed in)
		if ( empty( $event_id ) ) {
			$event_id = get_post_meta( $ticket_id, self::ATTENDEE_EVENT_KEY, true );
		}

		// Additional check (in case we were passed an invalid ticket ID and still can't determine the event)
		if ( empty( $event_id ) ) {
			return false;
		}

		if ( ! tribe( 'tickets.attendees' )->user_can_manage_attendees( 0, $event_id ) ) {
			return false;
		}

		$product_id = get_post_meta( $ticket_id, self::ATTENDEE_PRODUCT_KEY, true );

		// Stock Adjustment handled by $this->update_stock_from_attendees_page()

		// Store name so we can still show it in the attendee list
		$attendees = [];

		if ( get_post_type( $ticket_id ) === $this->ticket_object ) {
			$attendees = $this->get_attendees_by_ticket_id( $ticket_id );
		}

		$post_to_delete = get_post( $ticket_id );

		// Loop through attendees of ticket (if deleting ticket and not a specific attendee).
		foreach ( $attendees as $attendee ) {
			update_post_meta( $attendee['attendee_id'], $this->deleted_product, esc_html( $post_to_delete->post_title ) );
		}

		// Try to kill the actual ticket/attendee post
		$delete = wp_delete_post( $ticket_id, true );
		if ( ! isset( $delete->ID ) || is_wp_error( $delete ) ) {
			return false;
		}

		Tribe__Tickets__Attendance::instance( $event_id )->increment_deleted_attendees_count();
		do_action( 'tickets_rsvp_ticket_deleted', $ticket_id, $event_id, $product_id );
		Tribe__Post_Transient::instance()->delete( $event_id, Tribe__Tickets__Tickets::ATTENDEES_CACHE );

		return true;
	}

	/**
	 * Trigger for tribe_get_cost if there are only RSVPs
	 *
	 * @since 4.10.2
	 *
	 * @param string $cost
	 * @param int $post_id
	 * @param boolean $unused_with_currency_symbol
	 *
	 * @return string $cost
	 */
	public function trigger_get_cost( $cost, $post_id, $unused_with_currency_symbol ) {
		if (
			empty( $cost )
			&& tribe_events_has_tickets( get_post( $post_id ) )
			&& tribe_tickets( 'rsvp' )->where( 'event', $post_id )->found()
		) {
			$cost = __( 'Free', 'event-tickets' );
		}

		return $cost;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.7
	 */
	public function get_tickets( $post_id ) {
		$ticket_ids = $this->get_tickets_ids( $post_id );
		if ( ! $ticket_ids ) {
			return [];
		}

		$tickets = [];

		foreach ( $ticket_ids as $post ) {
			$tickets[] = $this->get_ticket( $post_id, $post );
		}

		return $tickets;
	}

	/**
	 * Shows the tickets form in the front end
	 *
	 * @param $content
	 *
	 * @return void
	 */
	public function front_end_tickets_form( $content ) {

		// if password protected then do not display content
		if ( post_password_required() ) {
			return null;
		}

		if ( $this->is_frontend_tickets_form_done ) {
			return $content;
		}

		$post = $GLOBALS['post'];

		// For recurring events (child instances only), default to loading tickets for the parent event
		if ( ! empty( $post->post_parent ) && function_exists( 'tribe_is_recurring_event' ) && tribe_is_recurring_event( $post->ID ) ) {
			$post = get_post( $post->post_parent );
		}

		$tickets = $this->get_tickets( $post->ID );

		if ( empty( $tickets ) ) {
			return $content;
		}

		// test for blocks in content, but usually called after the blocks have been converted
		if (
			has_blocks( $content )
			|| false !== strpos( (string) $content, 'tribe-block ' )
		) {
			return $content;
		}

		// Check to see if all available tickets' end-sale dates have passed, in which case no form
		// should show on the front-end.
		$expired_tickets = 0;

		foreach ( $tickets as $ticket ) {
			if ( ! $ticket->date_in_range() ) {
				$expired_tickets++;
			}
		}

		$must_login = ! is_user_logged_in() && $this->login_required();

		if ( $expired_tickets >= count( $tickets ) ) {
			/**
			 * Allow to hook into the FE form of the tickets if tickets has already expired. If the action used the
			 * second value for tickets make sure to use a callback instead of an inline call to the method such as:
			 *
			 * Example:
			 *
			 * add_action( 'tribe_tickets_expired_front_end_ticket_form', function( $must_login, $tickets ) {
			 *  Tribe__Tickets_Plus__Attendees_List::instance()->render();
			 * }, 10, 2 );
			 *
			 * If the tickets are not required to be used on the view you an use instead.
			 *
			 * add_action( 'tribe_tickets_expired_front_end_ticket_form', array( Tribe__Tickets_Plus__Attendees_List::instance(), 'render' ) );
			 *
			 * @since 4.7.3
			 *
			 * @param boolean $must_login
			 * @param array $tickets
			 */
			do_action( 'tribe_tickets_expired_front_end_ticket_form', $must_login, $tickets );
		}

		// Maybe render the new views.
		if ( tribe_tickets_rsvp_new_views_is_enabled() ) {
			$this->tickets_view->get_rsvp_block( $post );

			return;
		}

		$rsvp_sent  = empty( $_GET['rsvp_sent'] ) ? false : true;
		$rsvp_error = empty( $_GET['rsvp_error'] ) ? false : intval( $_GET['rsvp_error'] );

		if ( $rsvp_sent ) {
			$this->add_message( esc_html( sprintf( __( 'Your %1$s has been received! Check your email for your %1$s confirmation.', 'event-tickets' ), tribe_get_rsvp_label_singular( basename( __FILE__ ) ) ) ), 'success' );
		}

		if ( $rsvp_error ) {
			switch ( $rsvp_error ) {
				case 2:
					$this->add_message( esc_html( sprintf(
						__( 'You can\'t %1$s more than the total remaining %2$s.', 'event-tickets' ),
						tribe_get_rsvp_label_singular( 'verb' ),
						tribe_get_ticket_label_plural_lowercase( 'rsvp_error_attempt_too_many' )
					) ), 'error' );
					break;

				case 1:
				default:
					$this->add_message( esc_html( sprintf( __( 'In order to %s, you must enter your name and a valid email address.', 'event-tickets' ), tribe_get_rsvp_label_singular( 'verb' ) ) ), 'error' );
					break;
			}
		}

		/**
		 * Allow for the addition of content (namely the "Who's Attending?" list) above the ticket form.
		 *
		 * @since 4.5.5
		 */
		do_action( 'tribe_tickets_before_front_end_ticket_form' );

		include $this->getTemplateHierarchy( 'tickets/rsvp' );

		// It's only done when it's included
		$this->is_frontend_tickets_form_done = true;
	}

	/**
	 * Indicates if we currently require users to be logged in before they can obtain
	 * tickets.
	 *
	 * @return bool
	 */
	public function login_required() {
		$requirements = (array) tribe_get_option( 'ticket-authentication-requirements', array() );
		return in_array( 'event-tickets_rsvp', $requirements, true );
	}

	/**
	 * Gets an individual ticket
	 *
	 * @param $event_id
	 * @param $ticket_id
	 *
	 * @return null|Tribe__Tickets__Ticket_Object
	 */
	public function get_ticket( $event_id, $ticket_id ) {
		$product = get_post( $ticket_id );

		if ( ! $product ) {
			return null;
		}

		$return            = new Tribe__Tickets__Ticket_Object();
		$qty               = (int) get_post_meta( $ticket_id, 'total_sales', true );
		$global_stock_mode = get_post_meta( $ticket_id, Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE, true );

		$return->description      = $product->post_excerpt;
		$return->ID               = $ticket_id;
		$return->name             = $product->post_title;
		$return->menu_order       = $product->menu_order;
		$return->post_type        = $product->post_type;
		$return->price            = get_post_meta( $ticket_id, '_price', true );
		$return->provider_class   = get_class( $this );
		$return->admin_link       = '';
		$return->report_link      = '';
		$return->show_description = $return->show_description();

		$start_date               = get_post_meta( $ticket_id, '_ticket_start_date', true );
		$end_date                 = get_post_meta( $ticket_id, '_ticket_end_date', true );

		if ( ! empty( $start_date ) ) {
			$start_date_unix    = strtotime( $start_date );
			$return->start_date = Tribe__Date_Utils::date_only( $start_date_unix, true );
			$return->start_time = Tribe__Date_Utils::time_only( $start_date_unix );
		}

		if ( ! empty( $end_date ) ) {
			$end_date_unix    = strtotime( $end_date );
			$return->end_date = Tribe__Date_Utils::date_only( $end_date_unix, true );
			$return->end_time = Tribe__Date_Utils::time_only( $end_date_unix );
		}

		$return->manage_stock( 'yes' === get_post_meta( $ticket_id, '_manage_stock', true ) );
		$return->global_stock_mode = ( Tribe__Tickets__Global_Stock::OWN_STOCK_MODE === $global_stock_mode ) ? Tribe__Tickets__Global_Stock::OWN_STOCK_MODE : '';

		$return->stock( (int) get_post_meta( $ticket_id, '_stock', true ) );
		$return->qty_sold( $qty );
		$return->capacity = tribe_tickets_get_capacity( $ticket_id );

		/**
		 * Allow filtering to change ticket data.
		 *
		 * @since 5.0.3
		 *
		 * @param Tribe__Tickets__Ticket_Object $ticket    The ticket object.
		 * @param int                           $post_id   The ticket parent post ID.
		 * @param int                           $ticket_id The ticket ID.
		 */
		return apply_filters( 'tribe_tickets_rsvp_get_ticket', $return, $event_id, $ticket_id );
	}

	/**
	 * Accepts a reference to a product (either an object or a numeric ID) and
	 * tests to see if it functions as a ticket: if so, the corresponding event
	 * object is returned. If not, boolean false is returned.
	 *
	 * @param WP_Post|int $ticket_product
	 *
	 * @return bool|WP_Post
	 */
	public function get_event_for_ticket( $ticket_product ) {
		if ( is_object( $ticket_product ) && isset( $ticket_product->ID ) ) {
			$ticket_product = $ticket_product->ID;
		}

		if ( null === get_post( $ticket_product ) ) {
			return false;
		}

		$event_id = (int) get_post_meta( $ticket_product, $this->get_event_key(), true );

		if ( 0 === $event_id ) {
			$event_id = (int) get_post_meta( $ticket_product, self::ATTENDEE_EVENT_KEY, true );
		}

		if ( 0 === $event_id ) {
			return false;
		}

		if ( in_array( get_post_type( $event_id ), Tribe__Tickets__Main::instance()->post_types(), true ) ) {
			return get_post( $event_id );
		}

		return false;
	}

	/**
	 * Get attendees by id and associated post type
	 * or default to using $post_id
	 *
	 * @param      $post_id
	 * @param null $post_type
	 *
	 * @return array|mixed
	 */
	public function get_attendees_by_id( $post_id, $post_type = null ) {

		// RSVP Orders are a unique hash
		if ( ! is_numeric( $post_id ) ) {
			$post_type = 'rsvp_order_hash';
		}

		if ( ! $post_type ) {
			$post_type = get_post_type( $post_id );
		}

		switch ( $post_type ) {
			case $this->ticket_object:
				return $this->get_attendees_by_product_id( $post_id );

				break;
			case self::ATTENDEE_OBJECT:
				return $this->get_attendees_by_attendee_id( $post_id );

				break;
			case 'rsvp_order_hash':
				return $this->get_attendees_by_order_id( $post_id );

				break;
			default:
				return $this->get_attendees_by_post_id( $post_id );

				break;
		}

	}

	/**
	 * Get total count of attendees marked as going for this provider.
	 *
	 * @since 4.10.6
	 *
	 * @param int $post_id Post or Event ID.
	 *
	 * @return int Total count of attendees marked as going.
	 */
	public function get_attendees_count_going( $post_id ) {
		/** @var Tribe__Tickets__Attendee_Repository $repository */
		$repository = tribe_attendees( $this->orm_provider );

		return $repository->by( 'event', $post_id )->by( 'rsvp_status', 'yes' )->found();
	}

	/**
	 * Get total count of attendees marked as not going for this provider.
	 *
	 * @since 4.10.6
	 *
	 * @param int $post_id Post or Event ID.
	 *
	 * @return int Total count of attendees marked as going.
	 */
	public function get_attendees_count_not_going( $post_id ) {
		/** @var Tribe__Tickets__Attendee_Repository $repository */
		$repository = tribe_attendees( $this->orm_provider );

		return $repository->by( 'event', $post_id )->by( 'rsvp_status', 'no' )->found();
	}

	/**
	 * Get total count of attendees marked as going for this provider and user.
	 *
	 * @since 4.11.3
	 *
	 * @param int $post_id Post or Event ID.
	 *
	 * @return int Total count of attendees marked as going.
	 */
	public function get_attendees_count_going_for_user( $post_id, $user_id ) {
		/** @var Tribe__Tickets__Attendee_Repository $repository */
		$repository = tribe_attendees( $this->orm_provider );

		return $repository->by( 'event', $post_id )->by( 'user', $user_id )->by( 'rsvp_status', 'yes' )->found();
	}

	/**
	 * Get total count of attendees marked as not going for this provider.
	 *
	 * @since 4.11.3
	 *
	 * @param int $post_id Post or Event ID.
	 *
	 * @return int Total count of attendees marked as going.
	 */
	public function get_attendees_count_not_going_for_user( $post_id, $user_id ) {
		/** @var Tribe__Tickets__Attendee_Repository $repository */
		$repository = tribe_attendees( $this->orm_provider );

		return $repository->by( 'event', $post_id )->by( 'user', $user_id )->by( 'rsvp_status', 'no' )->found();
	}

	/**
	 * {@inheritdoc}
	 *
	 * Get all the attendees for post type. It returns an array with the
	 * following fields:
	 *
	 *     order_id
	 *     order_status
	 *     purchaser_name
	 *     purchaser_email
	 *     optout
	 *     ticket
	 *     attendee_id
	 *     security
	 *     product_id
	 *     check_in
	 *     provider
	 */
	public function get_attendee( $attendee, $post_id = 0 ) {
		if ( is_numeric( $attendee ) ) {
			$attendee = get_post( $attendee );
		}

		if ( ! $attendee instanceof WP_Post || self::ATTENDEE_OBJECT !== $attendee->post_type ) {
			return false;
		}

		$checkin      = get_post_meta( $attendee->ID, $this->checkin_key, true );
		$security     = get_post_meta( $attendee->ID, $this->security_code, true );
		$product_id   = get_post_meta( $attendee->ID, self::ATTENDEE_PRODUCT_KEY, true );
		$optout       = get_post_meta( $attendee->ID, self::ATTENDEE_OPTOUT_KEY, true );
		$status       = get_post_meta( $attendee->ID, self::ATTENDEE_RSVP_KEY, true );
		$status_label = $this->tickets_view->get_rsvp_options( $status );
		$user_id      = get_post_meta( $attendee->ID, self::ATTENDEE_USER_ID, true );
		$ticket_sent  = (int) get_post_meta( $attendee->ID, $this->attendee_ticket_sent, true );

		if ( empty( $product_id ) ) {
			return false;
		}

		$optout = filter_var( $optout, FILTER_VALIDATE_BOOLEAN );

		$product       = get_post( $product_id );
		$product_title = ( ! empty( $product ) ) ? $product->post_title : get_post_meta( $attendee->ID, $this->deleted_product, true ) . ' ' . __( '(deleted)', 'event-tickets' );

		$ticket_unique_id = get_post_meta( $attendee->ID, '_unique_id', true );
		$ticket_unique_id = $ticket_unique_id === '' ? $attendee->ID : $ticket_unique_id;

		$meta = '';
		if ( class_exists( 'Tribe__Tickets_Plus__Meta', false ) ) {
			$meta = get_post_meta( $attendee->ID, Tribe__Tickets_Plus__Meta::META_KEY, true );

			// Process Meta to include value, slug, and label
			if ( ! empty( $meta ) ) {
				$meta = $this->process_attendee_meta( $product_id, $meta );
			}
		}

		$attendee_data = array_merge(
			$this->get_order_data( $attendee->ID ),
			[
				'optout'             => $optout,
				'ticket'             => $product_title,
				'attendee_id'        => $attendee->ID,
				'security'           => $security,
				'product_id'         => $product_id,
				'check_in'           => $checkin,
				'order_status'       => $status,
				'order_status_label' => $status_label,
				'user_id'            => $user_id,
				'ticket_sent'        => $ticket_sent,

				// Fields for Email Tickets.
				'event_id'           => get_post_meta( $attendee->ID, self::ATTENDEE_EVENT_KEY, true ),
				'ticket_name'        => ! empty( $product ) ? $product->post_title : false,
				'holder_name'        => get_post_meta( $attendee->ID, $this->full_name, true ),
				'holder_email'       => get_post_meta( $attendee->ID, $this->email, true ),
				'order_id'           => $attendee->ID,
				'ticket_id'          => $ticket_unique_id,
				'qr_ticket_id'       => $attendee->ID,
				'security_code'      => $security,

				// Attendee Meta.
				'attendee_meta'      => $meta,

				// Handle initial Attendee flags.
				'is_subscribed'      => tribe_is_truthy( get_post_meta( $attendee->ID, $this->attendee_subscribed, true ) ),
				'is_purchaser'       => true,
			]
		);

		/**
		 * Allow filtering the attendee information to return.
		 *
		 * @since 4.7
		 *
		 * @param array   $attendee_data The attendee information.
		 * @param string  $provider_slug The provider slug.
		 * @param WP_Post $attendee      The attendee post object.
		 * @param int     $post_id       The post ID of the attendee ID.
		 */
		return apply_filters( 'tribe_tickets_attendee_data', $attendee_data, $this->orm_provider, $attendee, $post_id );
	}

	/**
	 * Retrieve only order related information
	 * Important: On RSVP the order is the Attendee Object
	 *
	 *     order_id
	 *     purchaser_name
	 *     purchaser_email
	 *     provider
	 *     provider_slug
	 *
	 * @param int $order_id
	 * @return array
	 */
	public function get_order_data( $order_id ) {
		$name  = get_post_meta( $order_id, $this->full_name, true );
		$email = get_post_meta( $order_id, $this->email, true );

		$data = array(
			'order_id'        => $order_id,
			'purchaser_name'  => $name,
			'purchaser_email' => $email,
			'provider'        => __CLASS__,
			'provider_slug'   => $this->orm_provider,
			'purchase_time'   => get_post_time( Tribe__Date_Utils::DBDATETIMEFORMAT, false, $order_id ),
		);

		/**
		 * Allow users to filter the Order Data
		 *
		 * @var array An associative array with the Information of the Order
		 * @var string What Provider is been used
		 * @var int Order ID
		 *
		 */
		$data = apply_filters( 'tribe_tickets_order_data', $data, $this->orm_provider, $order_id );

		return $data;
	}

	/**
	 * Remove the Post Transients when a Shopp Ticket is bought
	 *
	 * @param  int $attendee_id
	 * @return void
	 */
	public function purge_attendees_transient( $attendee_id ) {

		$event_id = get_post_meta( $attendee_id, self::ATTENDEE_EVENT_KEY, true );

		if ( $event_id ) {
			Tribe__Post_Transient::instance()->delete( $event_id, Tribe__Tickets__Tickets::ATTENDEES_CACHE );
		}
	}

	/**
	 * Marks an attendee as checked in for an event
	 *
	 * Because we must still support our legacy ticket plugins, we cannot change the abstract
	 * checkin() method's signature. However, the QR checkin process needs to move forward
	 * so we get around that problem by leveraging func_get_arg() to pass a second argument.
	 *
	 * It is hacky, but we'll aim to resolve this issue when we end-of-life our legacy ticket plugins
	 * OR write around it in a future major release
	 *
	 * @param $attendee_id
	 * @param $qr true if from QR checkin process (NOTE: this is a param-less parameter for backward compatibility)
	 *
	 * @return bool
	 */
	public function checkin( $attendee_id ) {
		$qr = null;

		$args = func_get_args();
		if ( isset( $args[1] ) && $qr = (bool) $args[1] ) {
			update_post_meta( $attendee_id, '_tribe_qr_status', 1 );
		}

		$event_id = get_post_meta( $attendee_id, self::ATTENDEE_EVENT_KEY, true );

		if ( ! $qr && ! tribe( 'tickets.attendees' )->user_can_manage_attendees( 0, $event_id ) ) {
			return false;
		}

		update_post_meta( $attendee_id, $this->checkin_key, 1 );

		if ( func_num_args() > 1 && $qr = func_get_arg( 1 ) ) {
			update_post_meta( $attendee_id, '_tribe_qr_status', 1 );
		}

		$checkin_details = array(
			'date'   => current_time( 'mysql' ),
			'source' => null !== $qr ? 'app' : 'site',
			'author' => get_current_user_id(),
		);

		/**
		 * Filters the checkin details for this attendee checkin.
		 *
		 * @since 4.8
		 *
		 * @param array $checkin_details
		 * @param int   $attendee_id
		 * @param mixed $qr
		 */
		$checkin_details = apply_filters( 'rsvp_checkin_details', $checkin_details, $attendee_id, $qr );

		update_post_meta( $attendee_id, $this->checkin_key . '_details', $checkin_details );

		/**
		 * Fires a checkin action
		 *
		 * @var int $attendee_id
		 * @var bool|null $qr
		 */
		do_action( 'rsvp_checkin', $attendee_id, $qr );

		return true;
	}

	/**
	 * Marks an attendee as not checked in for an event
	 *
	 * @param $attendee_id
	 *
	 * @return bool
	 */
	public function uncheckin( $attendee_id ) {
		$event_id = get_post_meta( $attendee_id, self::ATTENDEE_EVENT_KEY, true );

		if ( ! tribe( 'tickets.attendees' )->user_can_manage_attendees( 0, $event_id ) ) {
			return false;
		}

		delete_post_meta( $attendee_id, $this->checkin_key );
		delete_post_meta( $attendee_id, '_tribe_qr_status' );
		do_action( 'rsvp_uncheckin', $attendee_id );

		return true;
	}

	/**
	 * Links to sales report for all tickets for this event.
	 *
	 * @param int $event_id
	 *
	 * @return string
	 */
	public function get_event_reports_link( $event_id ) {
		return '';
	}

	/**
	 * Links to the sales report for this product.
	 * As of 4.6 we reversed the params and deprecated $event_id as it was never used
	 *
	 * @param deprecated $event_id
	 * @param int $unused_ticket_id
	 *
	 * @return string
	 */
	public function get_ticket_reports_link( $unused_ticket_id, $event_id_deprecated = null ) {
		if ( ! empty( $event_id_deprecated ) ) {
			_deprecated_argument( __METHOD__, '4.6' );
		}

		return '';
	}

	/**
	 * Renders the advanced fields in the new/edit ticket form.
	 * Using the method, providers can add as many fields as
	 * they want, specific to their implementation.
	 *
	 * @deprecated 4.6
	 *
	 * @return void
	 */
	public function do_metabox_advanced_options() {
		_deprecated_function( __METHOD__, '4.6', 'Tribe__Tickets__RSVP::do_metabox_capacity_options' );
	}

	/**
	 * Renders the advanced fields in the new/edit ticket form.
	 * Using the method, providers can add as many fields as
	 * they want, specific to their implementation.
	 *
	 * @since 4.6
	 *
	 * @param int $event_id
	 * @param int $ticket_id
	 *
	 * @return mixed
	 */
	public function do_metabox_capacity_options( $event_id, $ticket_id ) {
		$capacity  = '';
		$not_going = false;

		// This returns the original stock
		if ( ! empty( $ticket_id ) ) {
			$ticket = $this->get_ticket( $event_id, $ticket_id );

			if ( ! empty( $ticket ) ) {
				$capacity  = $ticket->capacity();
				$not_going = tribe_is_truthy( get_post_meta( $ticket_id, $this->show_not_going, true ) );
			}
		}

		include Tribe__Tickets__Main::instance()->plugin_path . 'src/admin-views/rsvp-metabox-capacity.php';

		if ( tribe_tickets_rsvp_new_views_is_enabled() ) {
			include Tribe__Tickets__Main::instance()->plugin_path . 'src/admin-views/rsvp-metabox-not-going.php';
		}
	}

	public function get_messages() {
		return self::$messages;
	}

	public function add_message( $message, $type = 'update' ) {
		$message = apply_filters( 'tribe_rsvp_submission_message', $message, $type );
		self::$messages[] = (object) array( 'message' => $message, 'type' => $type );
	}

	/**
	 * If the post that was moved to the trash was an RSVP attendee post type, redirect to
	 * the Attendees Report rather than the RSVP attendees post list (because that's kind of
	 * confusing)
	 *
	 * @param int $post_id WP_Post ID
	 */
	public function maybe_redirect_to_attendees_report( $post_id ) {
		$post = get_post( $post_id );

		if ( self::ATTENDEE_OBJECT !== $post->post_type ) {
			return;
		}

		$args = array(
			'post_type' => 'tribe_events',
			'page' => tribe( 'tickets.attendees' )->slug(),
			'event_id' => get_post_meta( $post_id, self::ATTENDEE_EVENT_KEY, true ),
		);

		$url = add_query_arg( $args, admin_url( 'edit.php' ) );
		$url = esc_url_raw( $url );

		wp_redirect( $url );
		tribe_exit();
	}

	/**
	 * Filters the post_updated_messages array for attendees
	 *
	 * @param array $messages Array of update messages
	 */
	public function updated_messages( $messages ) {
		$ticket_post = get_post();

		if ( ! $ticket_post ) {
			return $messages;
		}

		$post_type = get_post_type( $ticket_post );

		if ( self::ATTENDEE_OBJECT !== $post_type ) {
			return $messages;
		}

		$event = $this->get_event_for_ticket( $ticket_post );

		$attendees_report_url = add_query_arg(
			array(
				'post_type' => $event->post_type,
				'page' => tribe( 'tickets.attendees' )->slug(),
				'event_id' => $event->ID,
			),
			admin_url( 'edit.php' )
		);

		$return_link = sprintf(
			esc_html__( 'Return to the %1$sAttendees Report%2$s.', 'event-tickets' ),
			"<a href='" . esc_url( $attendees_report_url ) . "'>",
			'</a>'
		);

		$messages[ self::ATTENDEE_OBJECT ] = $messages['post'];
		$messages[ self::ATTENDEE_OBJECT ][1] = sprintf(
			esc_html__( 'Post updated. %1$s', 'event-tickets' ),
			$return_link
		);
		$messages[ self::ATTENDEE_OBJECT ][6] = sprintf(
			esc_html__( 'Post published. %1$s', 'event-tickets' ),
			$return_link
		);
		$messages[ self::ATTENDEE_OBJECT ][8] = esc_html__( 'Post submitted.', 'event-tickets' );
		$messages[ self::ATTENDEE_OBJECT ][9] = esc_html__( 'Post scheduled.', 'event-tickets' );
		$messages[ self::ATTENDEE_OBJECT ][10] = esc_html__( 'Post draft updated.', 'event-tickets' );

		return $messages;
	}

	/**
	 * Determine if the order stati are different (and we need to update the meta)
	 * @since 4.7.4
	 *
	 * @param $order_id
	 * @param $attendee_order_status
	 *
	 * @return array|bool array of stock size values, false if no difference
	 */
	public function stati_are_different( $order_id, $attendee_order_status ) {
		$rsvp_options = $this->tickets_view->get_rsvp_options( null, false );

		$previous_order_status = get_post_meta( $order_id, self::ATTENDEE_RSVP_KEY, true );

		if (
			! isset( $rsvp_options[ $previous_order_status ] )
			|| ! isset( $rsvp_options[ $attendee_order_status ] )
		) {
			return false;
		}

		if ( $rsvp_options[ $previous_order_status ]['decrease_stock_by'] === $rsvp_options[ $attendee_order_status ]['decrease_stock_by'] ) {
			return false;
		}

		return array(
			'previous_stock_size' => $rsvp_options[ $previous_order_status ]['decrease_stock_by'],
			'attendee_stock_size' => $rsvp_options[ $attendee_order_status ]['decrease_stock_by'],
		);
	}

	/**
	 * Get updated value for stock or sales, based on order status
	 * @since 4.7.4
	 *
	 * @param $order_id
	 * @param $attendee_order_status
	 * @param $ticket_id
	 * @param $meta
	 *
	 * @return bool|int|mixed get updated value, return false if no need to update
	 */
	public function find_updated_sales_or_stock_value( $order_id, $attendee_order_status, $ticket_id, $meta ) {
		$rsvp_options = $this->tickets_view->get_rsvp_options( null, false );

		$status_stock_sizes = $this->stati_are_different( $order_id, $attendee_order_status );

		if ( empty( $status_stock_sizes ) ) {
			return false;
		}

		$diff = $status_stock_sizes['attendee_stock_size'] - $status_stock_sizes['previous_stock_size'];

		if ( 0 === $diff ) {
			return false;
		}

		$meta_value = (int) get_post_meta( $ticket_id, $meta, true );

		if ( 'total_sales' === $meta ) {
			$new_value = $meta_value + $diff;
		} else {
			// When we increase sales, we reduce stock
			$new_value = $meta_value - $diff;
			// stock can NEVER exceed capacity
			$capacity = get_post_meta( $ticket_id, '_tribe_ticket_capacity', true );
			$new_value = ( $new_value > $capacity ) ? $capacity : $new_value;
		}

		return $new_value;
	}

	/**
	 * Updates the product sales and stock if old and new order stati differ in stock size.
	 *
	 * @param int    $order_id
	 * @param string $attendee_order_status
	 * @param int    $ticket_id
	 */
	public function update_sales_and_stock_by_order_status( $order_id, $attendee_order_status, $ticket_id ) {
		$sales_diff = $this->find_updated_sales_or_stock_value( $order_id, $attendee_order_status, $ticket_id, 'total_sales' );

		// it's all or none here...
		if ( false === $sales_diff ) {
			return false;
		}

		$stock_diff = $this->find_updated_sales_or_stock_value( $order_id, $attendee_order_status, $ticket_id, '_stock' );

		// it's all or none here...
		if ( false === $stock_diff ) {
			return false;
		}

		// Prevent negatives.
		$sales_diff = max( $sales_diff, 0 );
		$stock_diff = max( $stock_diff, 0 );

		// these should NEVER be updated separately - if one goes up the other must go down and vice versa
		return update_post_meta( $ticket_id, 'total_sales', $sales_diff ) && update_post_meta( $ticket_id, '_stock', $stock_diff );
	}

	/**
	 * Updates the product sales if old and new order stati differ in stock size.
	 *
	 * @deprecated 4.7.4
	 *
	 * @return void
	 */
	public function update_sales_by_order_status( $order_id, $attendee_order_status, $ticket_id ) {
		_deprecated_function( __METHOD__, '4.6', 'Tribe__Tickets__RSVP::update_sales_and_stock_by_order_status' );
		return $this->update_sales_and_stock_by_order_status( $order_id, $attendee_order_status, $ticket_id );
	}

	/**
	 * @param Tribe__Tickets__Tickets_View $tickets_view
	 *
	 * @internal Used for dependency injection.
	 */
	public function set_tickets_view( Tribe__Tickets__Tickets_View $tickets_view ) {
		$this->tickets_view = $tickets_view;
	}

	/**
	 * Filters the array of stati that will mark an RSVP attendee as eligible for check-in.
	 *
	 * @param array $stati An array of stati that should mark an RSVP attendee as
	 *                     available for check-in.
	 *
	 * @return array The original array plus the 'yes' status.
	 */
	public function filter_event_tickets_attendees_rsvp_checkin_stati( array $stati = array() ) {
		/** @var Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		$merged_array = array_merge( $stati, ( $status_mgr->get_statuses_by_action( 'count_completed', 'rsvp' ) ) );

		return array_unique( $merged_array );
	}

	/**
	 * Generates a number of attendees for an RSVP ticket.
	 *
	 * @since 4.7
	 *
	 * @param int     $product_id       The ticket post ID.
	 * @param int     $ticket_qty       The number of attendees that should be generated.
	 * @param array   $attendee_details An array containing the details for the attendees
	 *                                  that should be generated.
	 * @param boolean $redirect         Whether to redirect on error.
	 *
	 * @return bool|array `true` if the attendees were successfully generated, `false` otherwise. If $redirect is set to false, upon success this method will return an array of attendee IDs generated.
	 */
	public function generate_tickets_for( $product_id, $ticket_qty, $attendee_details, $redirect = true ) {
		$rsvp_options = $this->tickets_view->get_rsvp_options( null, false );

		$required_details = array(
			'full_name',
			'email',
			'order_status',
			'optout',
			'order_id',
		);

		foreach ( $required_details as $required_detail ) {
			if ( ! isset( $attendee_details[ $required_detail ] ) ) {
				return false;
			}

			// Some details should not be empty.
			if ( 'optout' !== $required_detail && empty( $attendee_details[ $required_detail ] ) ) {
				return false;
			}
		}

		$attendee_full_name    = $attendee_details['full_name'];
		$attendee_email        = $attendee_details['email'];
		$attendee_order_status = $attendee_details['order_status'];
		$attendee_optout       = $attendee_details['optout'];
		$order_id              = $attendee_details['order_id'];

		if ( 'going' === $attendee_order_status ) {
			$attendee_order_status = 'yes';
		} elseif ( 'not-going' === $attendee_order_status ) {
			$attendee_order_status = 'no';
		}

		$attendee_optout = filter_var( $attendee_optout, FILTER_VALIDATE_BOOLEAN );
		$attendee_optout = (int) $attendee_optout;

		// Get the event this tickets is for
		$post_id = get_post_meta( $product_id, $this->get_event_key(), true );

		if ( empty( $post_id ) ) {
			return false;
		}

		/** @var Tribe__Tickets__Ticket_Object $ticket_type */
		$ticket_type = $this->get_ticket( $post_id, $product_id );

		// get the RSVP status `decrease_stock_by` value
		$status_stock_size     = $rsvp_options[ $attendee_order_status ]['decrease_stock_by'];

		// to avoid tickets from not being created on a status stock size of 0
		// let's take the status stock size into account and create a number of tickets
		// at least equal to the number of tickets the user requested
		$ticket_qty = $status_stock_size < 1 ? $ticket_qty : $status_stock_size * $ticket_qty;

		$qty = max( $ticket_qty, 0 );

		// Throw an error if Qty is bigger then Remaining
		if ( $ticket_type->managing_stock() && $ticket_type->inventory() < $qty ) {
			if ( $redirect ) {
				$url = add_query_arg( 'rsvp_error', 2, get_permalink( $post_id ) );
				wp_redirect( esc_url_raw( $url ) );
				tribe_exit();
			}

			return false;
		}

		/**
		 * RSVP specific action fired just before a RSVP-driven attendee ticket for an event is generated
		 *
		 * @param int $post_id ID of event
		 * @param Tribe__Tickets__Ticket_Object $ticket_type Ticket Type object for the product
		 * @param array $_POST Parameters coming from the RSVP Form
		 */
		do_action( 'tribe_tickets_rsvp_before_attendee_ticket_creation', $post_id, $ticket_type, $_POST );

		$attendee_ids = [];

		// Iterate over all the amount of tickets purchased (for this product)
		for ( $i = 0; $i < $qty; $i++ ) {
			try {
				$attendee_data = [
					'full_name'         => $attendee_full_name,
					'email'             => $attendee_email,
					'optout'            => $attendee_optout,
					'attendee_status'   => $attendee_order_status,
					'order_id'          => $order_id,
					'order_attendee_id' => $i + 1,
					'user_id'           => is_user_logged_in() ? get_current_user_id() : 0,
				];

				$attendee_ids[] = $this->create_attendee_for_ticket( $ticket_type, $attendee_data );
			} catch ( Exception $exception ) {
				// Stop processing and return false.
				return false;
			}
		}

		/**
		 * Action fired when an RSVP has had attendee tickets generated for it
		 *
		 * @param int    $product_id   RSVP ticket post ID
		 * @param string $order_id     ID (hash) of the RSVP order
		 * @param int    $qty          Quantity ordered
		 * @param array  $attendee_ids List of attendee IDs generated.
		 */
		do_action( 'event_tickets_rsvp_tickets_generated_for_product', $product_id, $order_id, $qty, $attendee_ids );

		// After Adding the Values we Update the Transient
		$this->clear_attendees_cache( $post_id );

		if ( ! $redirect ) {
			return $attendee_ids;
		}

		return true;
	}

	/**
	 * @param $post_id
	 *
	 * @return array|false
	 */
	public function parse_attendee_details() {
		$order_id = self::generate_order_id();

		$first_attendee = [];

		if ( ! empty( $_POST['tribe_tickets'] ) ) {
			$first_ticket = current( $_POST['tribe_tickets'] );

			if ( ! empty( $first_ticket['attendees'] ) ) {
				$first_attendee = current( $first_ticket['attendees'] );
			}
		} elseif ( isset( $_POST['attendee'] ) ) {
			$first_attendee = $_POST['attendee'];
		}

		$attendee_email        = empty( $first_attendee['email'] ) ? null : sanitize_email( $first_attendee['email'] );
		$attendee_email        = is_email( $attendee_email ) ? $attendee_email : null;
		$attendee_full_name    = empty( $first_attendee['full_name'] ) ? null : sanitize_text_field( $first_attendee['full_name'] );
		$attendee_optout       = empty( $first_attendee['optout'] ) ? 0 : $first_attendee['optout'];
		$attendee_order_status = empty( $first_attendee['order_status'] ) ? 'yes' : $first_attendee['order_status'];

		$attendee_optout = filter_var( $attendee_optout, FILTER_VALIDATE_BOOLEAN );

		if ( 'going' === $attendee_order_status ) {
			$attendee_order_status = 'yes';
		} elseif ( 'not-going' === $attendee_order_status ) {
			$attendee_order_status = 'no';
		}

		if ( ! $this->tickets_view->is_valid_rsvp_option( $attendee_order_status ) ) {
			$attendee_order_status = 'yes';
		}

		if ( ! $attendee_email || ! $attendee_full_name ) {
			return false;
		}

		$attendee_details = [
			'full_name'    => $attendee_full_name,
			'email'        => $attendee_email,
			'order_status' => $attendee_order_status,
			'optout'       => $attendee_optout,
			'order_id'     => $order_id,
		];

		return $attendee_details;
	}

	/**
	 * Parses the quantity of tickets requested for a product via the $_POST var.
	 *
	 * @since 4.7
	 *
	 * @param int $ticket_id The ticket ID.
	 *
	 * @return int Either the requested quantity of tickets or `0` in any other case.
	 */
	public function parse_ticket_quantity( $ticket_id ) {
		$quantity = 0;

		if ( isset( $_POST['tribe_tickets'][ $ticket_id ]['quantity'] ) ) {
			$quantity = absint( $_POST['tribe_tickets'][ $ticket_id ]['quantity'] );
		} elseif ( isset( $_POST["quantity_{$ticket_id}"] ) ) {
			$quantity = absint( $_POST["quantity_{$ticket_id}"] );
		}

		return $quantity;
	}

	/**
	 * Ensure we update the stock when deleting attendees from the admin side
	 * @since 4.7.4
	 *
	 * @param $attendee_id
	 *
	 * @return bool|void
	 */
	public function update_stock_from_attendees_page( $attendee_id ) {
		$attendee = get_post( $attendee_id );

		// Can't find the attendee post
		if ( empty( $attendee ) ) {
			return false;
		}

		// It's not an attendee post
		if ( self::ATTENDEE_OBJECT !== $attendee->post_type ) {
			return false;
		}

		$ticket_id = get_post_meta( $attendee->ID, self::ATTENDEE_PRODUCT_KEY, true );

		// Orphan attendees? No event to update.
		if ( empty( $ticket_id ) ) {
			return false;
		}

		return $this->update_sales_and_stock_by_order_status( $attendee->ID, 'no', $ticket_id );
	}

	/**
	 * Return the "Not Going" RSVPs number
	 * on an event basis.
	 *
	 * @since 4.8.2
	 *
	 * @param $event_id
	 *
	 * @return int
	 */
	public function get_total_not_going( $event_id ) {
		return $this->get_attendees_count_not_going( $event_id );
	}

	/**
	 * Increase the sales for a ticket by a specific quantity.
	 *
	 * @since 5.1.0
	 *
	 * @param int $ticket_id The ticket post ID.
	 * @param int $quantity  The quanitity to increase the ticket sales by.
	 *
	 * @return int The new sales amount.
	 */
	public function increase_ticket_sales_by( $ticket_id, $quantity = 1 ) {
		// Adjust sales.
		$sales = (int) get_post_meta( $ticket_id, 'total_sales', true ) + $quantity;

		update_post_meta( $ticket_id, 'total_sales', $sales );

		// Adjust stock.
		$stock = (int) get_post_meta( $ticket_id, '_stock', true ) - $quantity;

		// Prevent negatives.
		$stock = max( $stock, 0 );

		update_post_meta( $ticket_id, '_stock', $stock );

		return $sales;
	}

	/**
	 * Decrease the sales for a ticket by a specific quantity.
	 *
	 * @since 5.1.0
	 *
	 * @param int $ticket_id The ticket post ID.
	 * @param int $quantity  The quanitity to decrease the ticket sales by.
	 *
	 * @return int The new sales amount.
	 */
	public function decrease_ticket_sales_by( $ticket_id, $quantity = 1 ) {
		// Adjust sales.
		$sales = (int) get_post_meta( $ticket_id, 'total_sales', true ) - $quantity;

		// Prevent negatives.
		$sales = max( $sales, 0 );

		update_post_meta( $ticket_id, 'total_sales', $sales );

		// Adjust stock.
		$stock = (int) get_post_meta( $ticket_id, '_stock', true ) + $quantity;

		update_post_meta( $ticket_id, '_stock', $stock );

		return $sales;
	}
}
