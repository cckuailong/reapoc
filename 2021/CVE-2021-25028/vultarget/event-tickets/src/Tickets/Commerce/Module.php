<?php

namespace TEC\Tickets\Commerce;

use TEC\Tickets\Commerce;
use TEC\Tickets\Commerce\Status\Completed;
use Tribe__Utils__Array as Arr;

/**
 * Class Tickets Provider class for Tickets Commerce
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */
class Module extends \Tribe__Tickets__Tickets {

	public function __construct() {
		// This needs to happen before parent construct.
		$this->plugin_name = __( 'Tickets Commerce', 'event-tickets' );

		parent::__construct();

		$this->attendee_object = Attendee::POSTTYPE;

		$this->attendee_ticket_sent = '_tribe_tpp_attendee_ticket_sent';

		$this->attendee_optout_key = Attendee::$optout_meta_key;

		$this->ticket_object = Ticket::POSTTYPE;

		$this->event_key = Attendee::$event_relation_meta_key;

		$this->checkin_key = Attendee::$checked_in_meta_key;

		$this->order_key = Attendee::$order_relation_meta_key;

		$this->refund_order_key = '_tribe_tpp_refund_order';

		$this->security_code = Attendee::$security_code_meta_key;

		$this->full_name = '_tribe_tpp_full_name';

		$this->email = Attendee::$purchaser_email_meta_key;
	}

	/**
	 * {@inheritdoc}
	 */
	public $orm_provider = \TEC\Tickets\Commerce::PROVIDER;

	/**
	 * Name of the CPT that holds Attendees (tickets holders).
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const ATTENDEE_OBJECT = Attendee::POSTTYPE;

	/**
	 * Name of the CPT that holds Orders
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const ORDER_OBJECT = Order::POSTTYPE;

	/**
	 * Meta key that relates Attendees and Events.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const ATTENDEE_EVENT_KEY = '_tec_tickets_commerce_event';

	/**
	 * Meta key that relates Attendees and Products.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const ATTENDEE_PRODUCT_KEY = '_tec_tickets_commerce_product';

	/**
	 * Meta key that relates Attendees and Orders.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const ATTENDEE_ORDER_KEY = '_tec_tickets_commerce_order';

	/**
	 * Indicates if a ticket for this attendee was sent out via email.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $attendee_ticket_sent;

	/**
	 * Meta key that if this attendee wants to show on the attendee list
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $attendee_optout_key;

	/**
	 * Meta key that if this attendee PayPal status
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $attendee_tpp_key;

	/**
	 * Name of the CPT that holds Tickets
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $ticket_object;

	/**
	 * Meta key that relates Products and Events
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $event_key;

	/**
	 * Meta key that stores if an attendee has checked in to an event
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $checkin_key;

	/**
	 * Meta key that ties attendees together by order
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $order_key;

	/**
	 * Meta key that ties attendees together by refunded order
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $refund_order_key;

	/**
	 * Meta key that holds the security code that's printed in the tickets
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $security_code;

	/**
	 * Meta key that holds the full name of the tickets PayPal "buyer"
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $full_name;

	/**
	 * Meta key that holds the email of the tickets PayPal "buyer"
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $email;

	/**
	 * Meta key that holds the name of a ticket to be used in reports if the Product is deleted
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public $deleted_product = '_tribe_deleted_product_name';

	/**
	 * A variable holder if PayPal is loaded
	 *
	 * @since 5.1.9
	 *
	 * @var boolean
	 */
	protected $is_loaded = false;

	/**
	 * This method is required for the module to properly load.
	 *
	 * @since 5.1.9
	 * @return static
	 */
	public static function get_instance() {
		return tribe( static::class );
	}

	/**
	 * Registers all actions/filters
	 *
	 * @since 5.1.9
	 */
	public function hooks() {
		// if the hooks have already been bound, don't do it again
		if ( $this->is_loaded ) {
			return false;
		}

//		add_filter( 'post_updated_messages', [ $this, 'updated_messages' ] );

//		add_action( 'init', tribe_callback( 'tickets.commerce.paypal.orders.report', 'hook' ) );
//		add_action( 'tribe_tickets_attendees_page_inside', tribe_callback( 'tickets.commerce.paypal.orders.tabbed-view', 'render' ) );
//		add_filter( 'tribe_tickets_stock_message_available_quantity', tribe_callback( 'tickets.commerce.paypal.orders.sales', 'filter_available' ), 10, 4 );
//		add_action( 'admin_init', tribe_callback( 'tickets.commerce.paypal.oversell.request', 'handle' ) );```
	}

	/**
	 * Send tickets email for attendees.
	 *
	 * @since 5.1.9
	 *
	 * @param array       $attendees   List of attendees.
	 * @param array       $args        {
	 *                                 The list of arguments to use for sending ticket emails.
	 *
	 * @type string       $subject     The email subject.
	 * @type string       $content     The email content.
	 * @type string       $from_name   The name to send tickets from.
	 * @type string       $from_email  The email to send tickets from.
	 * @type array|string $headers     The list of headers to send.
	 * @type array        $attachments The list of attachments to send.
	 * @type string       $provider    The provider slug (rsvp, tpp, woo, edd).
	 * @type int          $post_id     The post/event ID to send the emails for.
	 * @type string|int   $order_id    The order ID to send the emails for.
	 * }
	 *
	 * @return int The number of emails sent successfully.
	 */
	public function send_tickets_email_for_attendees( $attendees, $args = [] ) {
		$args = array_merge(
			[
				'subject'    => tribe_get_option( Settings::$option_confirmation_email_subject, false ),
				'from_name'  => tribe_get_option( Settings::$option_confirmation_email_sender_name, false ),
				'from_email' => tribe_get_option( Settings::$option_confirmation_email_sender_email, false ),
				'provider'   => Commerce::ABBR,
			],
			$args
		);

		return parent::send_tickets_email_for_attendees( $attendees, $args );
	}

	/**
	 * Shows the tickets form in the front end
	 *
	 * @since 5.1.9
	 *
	 * @param $content
	 *
	 * @return void
	 */
	public function front_end_tickets_form( $content ) {

		$post    = $GLOBALS['post'];
		$tickets = $this->get_tickets( $post->ID );

		foreach ( $tickets as $index => $ticket ) {
			if ( __CLASS__ !== $ticket->provider_class ) {
				unset( $tickets[ $index ] );
			}
		}

		if ( empty( $tickets ) ) {
			return;
		}

		tribe( Tickets_View::class )->get_tickets_block( $post->ID );
	}

	/**
	 * Indicates if we currently require users to be logged in before they can obtain
	 * tickets.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function login_required() {
		$requirements = (array) tribe_get_option( 'ticket-authentication-requirements', array() );

		return in_array( 'event-tickets_all', $requirements, true );
	}

	/**
	 * Get attendees by id and associated post type
	 * or default to using $post_id
	 *
	 * @since 5.1.9
	 *
	 * @param      $post_id
	 * @param null $post_type
	 *
	 * @return array|mixed
	 */
	public function get_attendees_by_id( $post_id, $post_type = null ) {
		if ( ! $post_type ) {
			$post_type = get_post_type( $post_id );
		}

		switch ( $post_type ) {
			case $this->attendee_object:
				return $this->get_attendees_by_attendee_id( $post_id );

				break;
			case 'tpp_order_hash':
				return $this->get_attendees_by_order_id( $post_id );

				break;
			case $this->ticket_object:
				return $this->get_attendees_by_ticket_id( $post_id );

				break;
			default:
				return $this->get_attendees_by_post_id( $post_id );

				break;
		}

	}

	/**
	 * Get attendees for a ticket by order ID, optionally by ticket ID.
	 *
	 * This overrides the parent method because Tickets Commerce stores the order ID in the post_parent.
	 *
	 * @since 5.2.0
	 *
	 * @param int|string $order_id  Order ID.
	 * @param null|int   $ticket_id (optional) Ticket ID.
	 *
	 * @return array List of attendees.
	 */
	public function get_attendees_by_order_id( $order_id ) {
		$ticket_id = null;

		// Support an optional second argument while not causing warnings from other ticket provider classes.
		if ( 1 < func_num_args() ) {
			$ticket_id = func_get_arg( 1 );
		}

		/** @var Tribe__Tickets__Attendee_Repository $repository */
		$repository = tribe_attendees( $this->orm_provider );

		$repository->by( 'parent', $order_id );

		if ( $ticket_id ) {
			$repository->by( 'ticket', $ticket_id );
		}

		return $this->get_attendees_from_module( $repository->all() );
	}

	/**
	 * Returns the value of a key defined by the class.
	 *
	 * @since 5.1.9
	 *
	 * @param string $key
	 *
	 * @return string The key value or an empty string if not defined.
	 */
	public static function get_key( $key ) {
		$instance = self::get_instance();
		$key      = strtolower( $key );

		$constant_map = [
			'attendee_event_key'   => $instance->attendee_event_key,
			'attendee_product_key' => $instance->attendee_product_key,
			'attendee_order_key'   => $instance->order_key,
			'attendee_optout_key'  => $instance->attendee_optout_key,
			'event_key'            => $instance->get_event_key(),
			'checkin_key'          => $instance->checkin_key,
			'order_key'            => $instance->order_key,
		];

		return \Tribe__Utils__Array::get( $constant_map, $key, '' );
	}

	/**
	 * Indicates if global stock support is enabled for this provider.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function supports_global_stock() {
		/**
		 * Allows the declaration of global stock support for Tribe Commerce tickets
		 * to be overridden.
		 *
		 * @param bool $enable_global_stock_support
		 */
		return (bool) apply_filters( 'tec_tickets_commerce_enable_global_stock', true );
	}

	/**
	 * All the methods below here were created merely as a backwards compatibility piece for our old Code that
	 * depends so much on the concept of a Main class handling all kinds of integration pieces.
	 *
	 * ! DO NOT INTRODUCE MORE LOGIC OR COMPLEXITY ON THESE METHODS !
	 *
	 * The methods are all focused on routing functionality to their correct handlers.
	 */

	/**
	 * Get's the product price html
	 *
	 * @since 5.1.9
	 *
	 * @param int|object    $product
	 * @param array|boolean $attendee
	 *
	 * @return string
	 */
	public function get_price_html( $product, $attendee = false ) {
		return tribe( Ticket::class )->get_price_html( $product, $attendee );
	}

	/**
	 * Gets the product price value
	 *
	 * @since 5.1.9
	 *
	 * @param int|\WP_Post $product
	 *
	 * @return string
	 */
	public function get_price_value( $product ) {
		return tribe( Ticket::class )->get_price_value( $product );
	}

	/**
	 * Whether a specific attendee is valid toward inventory decrease or not.
	 *
	 * By default only attendees generated as part of a Completed order will count toward
	 * an inventory decrease but, if the option to reserve stock for Pending Orders is activated,
	 * then those attendees generated as part of a Pending Order will, for a limited time after the
	 * order creation, cause the inventory to be decreased.
	 *
	 * @since 5.1.9
	 *
	 * @param array $attendee
	 *
	 * @return bool
	 */
	public function attendee_decreases_inventory( array $attendee ) {
		return tribe( Attendee::class )->decreases_inventory( $attendee );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_attendee( $attendee, $post_id = 0 ) {
		return tec_tc_get_attendee( $attendee, ARRAY_A );
	}

	/**
	 * Event Tickets Plus Admin Reports page will use this data from this method.
	 *
	 * @since 5.1.9
	 *
	 *
	 * @param string|int $order_id
	 *
	 * @return array
	 */
	public function get_order_data( $order_id ) {
		return tec_tc_get_order( $order_id, ARRAY_A );
	}

	/**
	 * Renders the advanced fields in the new/edit ticket form.
	 * Using the method, providers can add as many fields as
	 * they want, specific to their implementation.
	 *
	 * @since 5.1.9
	 *
	 * @param int $post_id
	 * @param int $ticket_id
	 */
	public function do_metabox_capacity_options( $post_id, $ticket_id ) {
		tribe( Editor\Metabox::class )->do_metabox_capacity_options( $post_id, $ticket_id );
	}

	/**
	 * Maps to the Cart Class method to get the cart.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_cart_url() {
		return tribe( Cart::class )->get_url();
	}

	/**
	 * Maps to the Checkout Class method to get the checkout.
	 *
	 * @since 5.2.0
	 *
	 * @return string
	 */
	public function get_checkout_url() {
		return tribe( Checkout::class )->get_url();
	}

	/**
	 * Generate and store all the attendees information for a new order.
	 *
	 * @since      5.1.9
	 * @deprecated 5.2.0
	 *
	 * @param string $payment_status The tickets payment status, defaults to completed.
	 * @param bool   $redirect       Whether the client should be redirected or not.
	 */
	public function generate_tickets( $payment_status = 'completed', $redirect = true ) {
		_deprecated_function( __METHOD__, '5.2.0' );
	}

	/**
	 * Gets an individual ticket.
	 *
	 * @since 5.1.9
	 *
	 * @param int|\WP_Post $post_id
	 * @param int|\WP_Post $ticket_id
	 *
	 * @return null|\Tribe__Tickets__Ticket_Object
	 */
	public function get_ticket( $post_id, $ticket_id ) {
		return tribe( Ticket::class )->get_ticket( $ticket_id );
	}

	/**
	 * Saves a Tickets Commerce ticket.
	 *
	 * @since 5.1.9
	 *
	 * @param int                            $post_id  Post ID.
	 * @param \Tribe__Tickets__Ticket_Object $ticket   Ticket object.
	 * @param array                          $raw_data Ticket data.
	 *
	 * @return int|false The updated/created ticket post ID or false if no ticket ID.
	 */
	public function save_ticket( $post_id, $ticket, $raw_data = [] ) {
		// Run anything we might need on parent method.
		parent::save_ticket( $post_id, $ticket, $raw_data );

		/**
		 * Important, do not add anything above this method.
		 * Our goal is to reduce the amount of load on the `Module`, relegate these behaviors to the correct models.
		 */
		return tribe( Ticket::class )->save( $post_id, $ticket, $raw_data );
	}

	/**
	 * Deletes a ticket.
	 *
	 * @since 5.1.9
	 *
	 * @param $event_id
	 * @param $ticket_id
	 *
	 * @return bool
	 */
	public function delete_ticket( $event_id, $ticket_id ) {
		/**
		 * Important, do not add anything above this method.
		 * Our goal is to reduce the amount of load on the `Module`, relegate these behaviors to the correct models.
		 */
		$deleted = tribe( Ticket::class )->delete( $event_id, $ticket_id );

		if ( ! $deleted ) {
			return $deleted;
		}

		// Run anything we might need on parent method.
		parent::delete_ticket( $event_id, $ticket_id );

		return $deleted;
	}

	/**
	 * Return whether we're currently on the checkout page for Tickets Commerce.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function is_checkout_page() {
		return tribe( Checkout::class )->is_current_page();
	}

	/**
	 * Links to sales report for all tickets for this event.
	 *
	 * @since 5.1.9
	 *
	 * @param int  $event_id
	 * @param bool $url_only
	 *
	 * @return string
	 */
	public function get_event_reports_link( $event_id, $url_only = false ) {
		return tribe( Commerce\Reports\Orders::class )->get_event_link( $event_id, $url_only );
	}

	/**
	 * Links to the sales report for this product.
	 *
	 * @since 5.1.9
	 *
	 * @param $event_id
	 * @param $ticket_id
	 *
	 * @return string
	 */
	public function get_ticket_reports_link( $event_id, $ticket_id ) {
		return tribe( Commerce\Reports\Orders::class )->get_ticket_link( $event_id, $ticket_id );
	}

	/**
	 * Create an attendee for the Commerce provider from a ticket.
	 *
	 * @since 5.1.0
	 *
	 * @param \Tribe__Tickets__Ticket_Object|int $ticket        Ticket object or ID to create the attendee for.
	 * @param array                              $attendee_data Attendee data to create from.
	 *
	 * @return \WP_Post|\WP_Error|false The new post object or false if we can't resolve to a Ticket object. WP_Error if modifying status fails
	 */
	public function create_attendee( $ticket, $attendee_data ) {
		// Get the ticket object from the ID.
		if ( is_numeric( $ticket ) ) {
			$ticket = $this->get_ticket( 0, (int) $ticket );
		}

		// If the ticket is not valid, stop creating the attendee.
		if ( ! $ticket instanceof \Tribe__Tickets__Ticket_Object ) {
			return false;
		}

		$extra              = [];
		$extra['attendees'] = [
			1 => [
				'meta' => Arr::get( $attendee_data, 'attendee_meta', [] )
			]
		];
		$extra['optout']    = ! Arr::get( $attendee_data, 'send_ticket_email', true );
		$extra['iac']       = false;

		// The Manual Order takes the same format as the cart items.
		$items = [
			$ticket->ID => [
				'ticket_id' => $ticket->ID,
				'quantity'  => 1,
				'extra'     => $extra,
			]
		];

		$purchaser = [
			'full_name' => $attendee_data['full_name'],
			'email'     => $attendee_data['email'],

			// By default user ID is zero here.
			'user_id'   => 0,
		];

		$order = tribe( Gateways\Manual\Order::class )->create( $items, $purchaser );

		/**
		 * For now we need to make sure we move to pending before completed.
		 *
		 * @todo @backend when an order is moved into completed they need to update to pending first.
		 *       likely we should have this be developed by implementing a dependent status.
		 */
		$updated = tribe( Order::class )->modify_status( $order->ID, 'pending' );
		if ( is_wp_error( $updated ) ) {
			return $updated;
		}

		$updated = tribe( Order::class )->modify_status( $order->ID, 'completed' );
		if ( is_wp_error( $updated ) ) {
			return $updated;
		}

		$attendee = tec_tc_attendees()->by( 'order_id', $order->ID )->first();

		return $attendee;
	}

	/**
	 * Update an attendee for the Commerce provider.
	 *
	 * @since 5.2.0
	 *
	 * @todo TribeLegacyCommerce We need to move this into the Attendee class.
	 *
	 * @param array|int $attendee      The attendee data or ID for the attendee to update.
	 * @param array     $attendee_data The attendee data to update to.
	 *
	 * @return \WP_Post|false The updated post object or false if unsuccessful.
	 */
	public function update_attendee( $attendee, $attendee_data ) {
		if ( is_numeric( $attendee ) ) {
			$attendee_id = (int) $attendee;
		} elseif ( is_array( $attendee ) && isset( $attendee['attendee_id'] ) ) {
			$attendee_id = (int) $attendee['attendee_id'];
		} else {
			return false;
		}

		$attendee = tec_tc_attendees( $this->orm_provider )
			->where( 'ID', $attendee_id );

		try {
			if ( ! empty( $attendee_data['attendee_meta'] ) ) {
				$attendee->set( 'fields', $attendee_data['attendee_meta'] );
			}

			if ( ! empty( $attendee_data['full_name'] ) ) {
				$attendee->set( 'full_name', $attendee_data['full_name'] );
			}

			if ( ! empty( $attendee_data['email'] ) && filter_var( $attendee_data['email'], FILTER_VALIDATE_EMAIL ) ) {
				$attendee->set( 'email', $attendee_data['email'] );
			}

			$attendee->save();

			// Send attendee email.
			$send_ticket_email      = (bool) Arr::get( $attendee_data, 'send_ticket_email', false );
			$send_ticket_email_args = (array) Arr::get( $attendee_data, 'send_ticket_email_args', [] );

			// Check if we need to send the ticket email.
			if ( $send_ticket_email ) {
				$attendee_tickets = [
					$attendee_id,
				];

				// Maybe send the attendee email.
				$this->send_tickets_email_for_attendees( $attendee_tickets, $send_ticket_email_args );
			}
		} catch ( \Tribe__Repository__Usage_Error $e ) {
			do_action( 'tribe_log', 'error', __CLASS__, [ 'message' => $e->getMessage() ] );

			return false;
		}

		return $attendee;
	}

}
