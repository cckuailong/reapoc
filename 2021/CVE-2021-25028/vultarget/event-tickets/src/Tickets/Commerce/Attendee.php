<?php

namespace TEC\Tickets\Commerce;

use TEC\Tickets\Commerce;
use TEC\Tickets\Commerce\Communications\Email;
use TEC\Tickets\Commerce\Status\Status_Handler;
use \Tribe__Tickets__Ticket_Object as Ticket_Object;
use Tribe__Utils__Array as Arr;
use Tribe__Date_Utils;
use WP_Post;

/**
 * Class Attendee
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce
 */
class Attendee {

	/**
	 * Tickets Commerce Attendee Post Type slug.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const POSTTYPE = 'tec_tc_attendee';

	/**
	 * Which meta holds the Relation ship between an attendee and which user it's registered to.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $user_relation_meta_key = '_tribe_tickets_attendee_user_id';

	/**
	 * Which meta holds the Relation ship between an attendee and which event it's registered to.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $event_relation_meta_key = '_tec_tickets_commerce_event';

	/**
	 * Which meta holds the Relation ship between an attendee and which ticket it was created from.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $ticket_relation_meta_key = '_tec_tickets_commerce_ticket';

	/**
	 * Which meta holds the Relation ship between an attendee and which order it belongs to.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $order_relation_meta_key = '_tec_tickets_commerce_order';

	/**
	 * Which meta holds the purchaser name for an attendee.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $purchaser_name_meta_key = '_tec_tickets_commerce_purchaser_name';

	/**
	 * Which meta holds the purchaser email for an attendee.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $purchaser_email_meta_key = '_tec_tickets_commerce_purchaser_email';

	/**
	 * Which meta holds the security code for an attendee.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $security_code_meta_key = '_tec_tickets_commerce_security_code';

	/**
	 * Which meta holds the status value for an attendee.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $status_meta_key = '_tec_tickets_commerce_status';

	/**
	 * Which meta holds the optout value for an attendee.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $optout_meta_key = '_tec_tickets_commerce_optout';

	/**
	 * Which meta holds the checked in status for an attendee.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $checked_in_meta_key = '_tec_tickets_commerce_checked_in';

	/**
	 * Which meta holds the checked in status for an attendee.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $deleted_ticket_meta_key = '_tribe_deleted_product_name';

	/**
	 * Indicates if a ticket for this attendee was sent out via email.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $ticket_sent_meta_key = '_tec_tickets_commerce_attendee_ticket_sent';

	/**
	 * Meta key holding an indication if this attendee was subscribed.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $subscribed_meta_key = '_tribe_tickets_subscribed';

	/**
	 * Meta key holding the first name for the attendee. (not purchaser)
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $full_name_meta_key = '_tec_tickets_commerce_full_name';

	/**
	 * Meta key holding the email for the attendee. (not purchaser)
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $email_meta_key = '_tec_tickets_commerce_email';

	/**
	 * Meta key holding price paid for this attendee.
	 *
	 * @since 5.1.10
	 *
	 * @var string
	 */
	public static $price_paid_meta_key = '_tec_tickets_commerce_price_paid';

	/**
	 * Meta key holding currency which the price was paid in.
	 *
	 * @since 5.1.10
	 *
	 * @var string
	 */
	public static $currency_meta_key = '_tec_tickets_commerce_currency';

	/**
	 * Meta key holding the attendee's unique id
	 *
	 * @since 5.2.0
	 *
	 * @var string
	 */
	public static $unique_id_meta_key = '_unique_id';

	/**
	 * Register this Class post type into WP.
	 *
	 * @since 5.1.9
	 */
	public function register_post_type() {
		$post_type_args = [
			'label'           => __( 'Attendees', 'event-tickets' ),
			'public'          => false,
			'show_ui'         => false,
			'show_in_menu'    => false,
			'query_var'       => false,
			'rewrite'         => false,
			'capability_type' => 'post',
			'has_archive'     => false,
			'hierarchical'    => false,
		];

		/**
		 * Filter the arguments that craft the attendee post type.
		 *
		 * @see   register_post_type
		 *
		 * @since 5.1.9
		 *
		 * @param array $post_type_args Post type arguments, passed to register_post_type()
		 */
		$post_type_args = apply_filters( 'tec_tickets_commerce_attendee_post_type_args', $post_type_args );

		register_post_type( static::POSTTYPE, $post_type_args );
	}

	/**
	 * Archives an attendee. In WordPress this means the attendee post will have `trash` status, but it won't be
	 * deleted.
	 *
	 * @since 5.2.1
	 *
	 * @param int $attendee_id The Attendee ID.
	 */
	public function archive( $attendee_id ) {
		/**
		 * Allows filtering the Attendee ID for archival.
		 *
		 * @since 5.2.1
		 *
		 * @param int $attendee_id The Attendee ID.
		 */
		$attendee_id = apply_filters( 'tec_tickets_commerce_attendee_to_archive', $attendee_id );

		/**
		 * Allows actions to run right before archiving an attendee.
		 *
		 * @since 5.2.1
		 *
		 * @param int $attendee_id The Attendee ID.
		 */
		do_action( 'tec_tickets_commerce_attendee_before_archive', $attendee_id );

		$result = wp_trash_post( $attendee_id );

		/**
		 * Allows actions to run right after archiving an attendee.
		 *
		 * @since 5.2.1
		 *
		 * @param int                $attendee_id The Attendee ID.
		 * @param WP_Post|false|null $result      Attendee post data on success, false or null on failure.
		 */
		do_action( 'tec_tickets_commerce_attendee_after_archive', $attendee_id, $result );

		/**
		 * Allows filtering of the return from the `wp_trash_post`.
		 *
		 * @since 5.2.1
		 *
		 * @param WP_Post|false|null $result      Attendee post data on success, false or null on failure.
		 * @param int                $attendee_id The Attendee ID.
		 */
		return apply_filters( 'tec_tickets_commerce_attendee_archived', $result, $attendee_id );
	}

	/**
	 * Permanently deletes an attendee.
	 *
	 * @since 5.2.1
	 *
	 * @param int     $attendee_id The Attendee ID.
	 * @param boolean $force       Force the deletion.
	 */
	public function delete( $attendee_id, $force = true ) {
		/**
		 * Allows filtering the Attendee ID for deletion.
		 *
		 * @since 5.2.1
		 *
		 * @param int     $attendee_id The Attendee ID
		 * @param boolean $force       Force the deletion.
		 */
		$attendee_id = apply_filters( 'tec_tickets_commerce_attendee_to_delete', $attendee_id, $force );

		/**
		 * Allows actions to run right before deleting an attendee.
		 *
		 * @since 5.2.1
		 *
		 * @param int     $attendee_id The Attendee ID.
		 * @param boolean $force       Force the deletion.
		 */
		do_action( 'tec_tickets_commerce_attendee_before_delete', $attendee_id, $force );

		$result = wp_delete_post( $attendee_id, true );

		/**
		 * Allows actions to run right after deleting an attendee.
		 *
		 * @since 5.2.1
		 *
		 * @param int                $attendee_id The Attendee ID.
		 * @param WP_Post|false|null $result      Attendee post data on success, false or null on failure.
		 * @param boolean            $force       Force the deletion.
		 */
		do_action( 'tec_tickets_commerce_attendee_after_delete', $attendee_id, $result, $force );

		/**
		 * Allows filtering of the return from the `wp_delete_post`.
		 *
		 * @since 5.2.1
		 *
		 * @param WP_Post|false|null $result      Attendee post data on success, false or null on failure.
		 * @param int                $attendee_id The Attendee ID.
		 * @param boolean            $force       Force the deletion.
		 */
		return apply_filters( 'tec_tickets_commerce_attendee_deleted', $result, $attendee_id, $force );
	}

	/**
	 * Creates an individual attendee given an Order and Ticket.
	 *
	 * @since 5.1.10
	 *
	 * @param \WP_Post      $order  Which order generated this attendee.
	 * @param Ticket_Object $ticket Which ticket generated this Attendee.
	 * @param array         $args   Set of extra arguments used to populate the data for the attendee.
	 *
	 * @return \WP_Error|\WP_Post
	 */
	public function create( \WP_Post $order, $ticket, array $args = [] ) {
		$create_args = [
			'order_id'      => $order->ID,
			'ticket_id'     => $ticket->ID,
			'event_id'      => $ticket->get_event_id(),
			'security_code' => Arr::get( $args, 'security_code' ),
			'opt_out'       => Arr::get( $args, 'opt_out' ),
			'price_paid'    => Arr::get( $args, 'price_paid' ),
			'currency'      => Arr::get( $args, 'currency' ),
		];

		if ( ! empty( $order->purchaser['user_id'] ) ) {
			$create_args['user_id'] = $order->purchaser['user_id'];
		}

		if ( ! empty( $args['email'] ) ) {
			$create_args['email'] = $args['email'];
		}

		if (
			empty( $args['email'] )
			&& ! empty( $order->purchaser['email'] )
		) {
			$create_args['email'] = $order->purchaser['email'];
		}

		if ( ! empty( $args['full_name'] ) ) {
			$create_args['full_name'] = $args['full_name'];
		}

		if (
			empty( $args['full_name'] )
			&& ! empty( $order->purchaser['full_name'] )
		) {
			$create_args['full_name'] = $order->purchaser['full_name'];
		}

		$fields = Arr::get( $args, 'fields', [] );
		if ( ! empty( $fields ) ) {
			$create_args['fields'] = $fields;
		}

		/**
		 * Allow the filtering of the create arguments for attendee.
		 *
		 * @since 5.1.10
		 *
		 * @param array         $create_args Which arguments we are going to use to create the attendee.
		 * @param \WP_Post      $order       Which order generated this attendee.
		 * @param Ticket_Object $ticket      Which ticket generated this Attendee.
		 * @param array         $args        Set of extra arguments used to populate the data for the attendee.
		 */
		$create_args = apply_filters( 'tec_tickets_commerce_attendee_create_args', $create_args, $order, $ticket, $args );

		/**
		 * Allow the actions before creating the attendee.
		 *
		 * @since 5.1.10
		 *
		 * @param array         $create_args Which arguments we are going to use to create the attendee.
		 * @param \WP_Post      $order       Which order generated this attendee.
		 * @param Ticket_Object $ticket      Which ticket generated this Attendee.
		 * @param array         $args        Set of extra arguments used to populate the data for the attendee.
		 */
		do_action( 'tec_tickets_commerce_attendee_before_create', $create_args, $order, $ticket, $args );

		$attendee = tec_tc_attendees()->set_args( $create_args )->create();

		/**
		 * Allow the actions after creating the attendee.
		 *
		 * @since 5.1.10
		 *
		 * @param \WP_Post      $attendee Post object for the attendee.
		 * @param \WP_Post      $order    Which order generated this attendee.
		 * @param Ticket_Object $ticket   Which ticket generated this Attendee.
		 * @param array         $args     Set of extra arguments used to populate the data for the attendee.
		 */
		do_action( 'tec_tickets_commerce_attendee_after_create', $attendee, $order, $ticket, $args );

		/**
		 * Allow the filtering of the attendee WP_Post after creating attendee.
		 *
		 * @since 5.1.10
		 *
		 * @param \WP_Post      $attendee Post object for the attendee.
		 * @param \WP_Post      $order    Which order generated this attendee.
		 * @param Ticket_Object $ticket   Which ticket generated this Attendee.
		 * @param array         $args     Set of extra arguments used to populate the data for the attendee.
		 */
		return apply_filters( 'tec_tickets_commerce_attendee_create', $attendee, $order, $ticket, $args );
	}

	/**
	 * If the post that was moved to the trash was an PayPal Ticket attendee post type, redirect to
	 * the Attendees Report rather than the PayPal Ticket attendees post list (because that's kind of
	 * confusing)
	 *
	 * @todo  @backend this should probably be moved to the Archive Attendees flag action and handled from there.
	 *
	 * @since 5.1.9
	 *
	 * @param int $post_id WP_Post ID.
	 */
	public function maybe_redirect_to_attendees_report( $post_id ) {
		$post = get_post( $post_id );

		if ( static::POSTTYPE !== $post->post_type ) {
			return;
		}

		// Do not redirect if this status change is being handled by a Flag Action.
		if ( did_action( 'tec_tickets_commerce_order_status_flag_archive_attendees' ) ) {
			return;
		}

		$args = [
			'post_type' => 'tribe_events',
			'page'      => \Tribe__Tickets__Tickets_Handler::$attendees_slug,
			'event_id'  => get_post_meta( $post_id, static::$event_relation_meta_key, true ),
		];

		$url = add_query_arg( $args, admin_url( 'edit.php' ) );
		$url = esc_url_raw( $url );

		wp_redirect( $url );
		tribe_exit();
	}

	/**
	 * Update the Ticket Commerce values for this user.
	 *
	 * Note that, within this method, $order_id refers to the attendee or ticket ID
	 * (it does not refer to an "order" in the sense of a transaction that may include
	 * multiple tickets, as is the case in some other methods for instance).
	 *
	 * @todo  Adjust to the Ticket Commerce data.
	 *
	 * @since 5.1.9
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

		$ticket_attendees    = tribe( Module::class )->get_attendees_by_user_id( $user_id, $post_id );
		$ticket_attendee_ids = wp_list_pluck( $ticket_attendees, 'attendee_id' );

		// This makes sure we don't save attendees for attendees that are not from this current user and event.
		if ( ! in_array( $attendee_id, $ticket_attendee_ids, true ) ) {
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

		// Only update if there's data to set.
		if ( empty( $attendee_data_to_save ) ) {
			return;
		}

		tribe( Module::class )->update_attendee( $attendee_id, $attendee_data_to_save );
	}

	/**
	 * Triggers the sending of ticket emails after PayPal Ticket information is updated.
	 *
	 * This is useful if a user initially suggests they will not be attending
	 * an event (in which case we do not send tickets out) but where they
	 * incrementally amend the status of one or more of those tickets to
	 * attending, at which point we should send tickets out for any of those
	 * newly attending persons.
	 *
	 * @since 5.1.9
	 *
	 * @param int $event_id the event id.
	 */
	public function maybe_send_tickets_after_status_change( $event_id ) {
		$transaction_ids = [];

		foreach ( tribe( Module::class )->get_event_attendees( $event_id ) as $attendee ) {
			$transaction = get_post_meta( $attendee['attendee_id'], static::$order_relation_meta_key, true );

			if ( ! empty( $transaction ) ) {
				$transaction_ids[ $transaction ] = $transaction;
			}
		}

		foreach ( $transaction_ids as $transaction ) {
			// This method takes care of intelligently sending out emails only when
			// required, for attendees that have not yet received their tickets
			tribe( Email::class )->send_tickets_email( $transaction, $event_id );
		}
	}

	/**
	 * Add our class to the list of classes for the attendee registration form
	 *
	 * @since 5.2.0
	 *
	 * @param array $classes existing array of classes.
	 *
	 * @return array $classes with our class added
	 */
	public function registration_form_class( $classes ) {
		$classes[ static::POSTTYPE ] = \TEC\Tickets\Commerce::ABBR;

		return $classes;
	}

	/**
	 * Filter the provider object to return this class if tickets are for this provider.
	 *
	 * @since 5.1.9
	 *
	 * @param object $provider_obj
	 * @param string $provider
	 *
	 * @return object
	 */
	public function registration_cart_provider( $provider_obj, $provider ) {
		$options = [
			\TEC\Tickets\Commerce::ABBR,
			static::POSTTYPE,
			\TEC\Tickets\Commerce::PROVIDER,
			static::class,
		];

		if ( in_array( $provider, $options, true ) ) {
			return tribe( Module::class );
		}

		return $provider_obj;
	}

	/**
	 * Whether a specific attendee is valid toward inventory decrease or not.
	 *
	 * By default only attendees generated as part of a Completed order will count toward
	 * an inventory decrease but, if the option to reserve stock for Pending Orders is activated,
	 * then those attendees generated as part of a Pending Order will, for a limited time after the
	 * order creation, cause the inventory to be decreased.
	 *
	 * @todo  TribeCommerceLegacy: Move this method a Flag action.
	 * @todo  For some forsaken reason the calculation of inventory is happening on the fly instead of when orders
	 *        are modified we need to address that for performance reasons.
	 *
	 * @since 5.1.9
	 *
	 * @param array $attendee array of attendee information.
	 *
	 * @return bool
	 */
	public function decreases_inventory( $attendee ) {
		$attendee = tec_tc_get_attendee( $attendee['ID'] );
		$order    = tec_tc_get_order( $attendee->post_parent );
		$statuses = array_unique(
			[
				tribe( Status_Handler::class )->get_inventory_decrease_status()->get_wp_slug(),
				tribe( Commerce\Status\Completed::class )->get_wp_slug(),
			]
		);

		return in_array( $order->post_status, $statuses, true );
	}

	/**
	 * Hydrate attendee object with ticket data
	 *
	 * @todo  We should not be using this particular piece of the code until it's using `tec_tc_get_attendee`.
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return \WP_Post
	 */
	public function get_attendee( \WP_Post $attendee ) {

		if ( static::POSTTYPE !== $attendee->post_type ) {
			$attendee->is_legacy_attendee = true;
			$legacy_provider              = tribe_tickets_get_ticket_provider( $attendee->ID );
			$attendee_data                = (array) $legacy_provider->get_attendee( $attendee );

			foreach ( $attendee_data as $key => $value ) {
				$attendee->{$key} = $value;
			}
		} else {
			$attendee = $this->load_attendee_data( $attendee );
		}

		return $attendee;
	}

	/**
	 * Loads event, ticket, order and other data into an attendee object
	 *
	 * @todo  We should not be using this particular piece of the code until it's using `tec_tc_get_attendee`.
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return \WP_Post
	 */
	public function load_attendee_data( \WP_Post $attendee ) {
		$attendee->attendee_id        = $attendee->ID;
		$attendee->attendee_meta      = null;
		$attendee->check_in           = $this->get_check_in_status( $attendee );
		$attendee->event_id           = $this->get_event_id( $attendee );
		$attendee->holder_email       = $this->get_holder_email( $attendee );
		$attendee->holder_name        = $this->get_holder_name( $attendee );
		$attendee->is_legacy_attendee = false;
		$attendee->is_purchaser       = true;
		$attendee->is_subscribed      = null;
		$attendee->optout             = null;
		$attendee->order_id           = 0;
		$attendee->product            = $this->get_product( $attendee );
		$attendee->product_id         = $this->get_product_id( $attendee );
		$attendee->provider           = Commerce::ABBR;
		$attendee->provider_slug      = Commerce::ABBR;
		$attendee->purchase_time      = get_post_time( Tribe__Date_Utils::DBDATETIMEFORMAT, false, $attendee->order_id );
		$attendee->qr_ticket_id       = null;
		$attendee->security           = $this->get_security_code( $attendee );
		$attendee->security_code      = $this->get_security_code( $attendee );
		$attendee->ticket             = $this->get_product_title( $attendee );
		$attendee->ticket_id          = $this->get_unique_id( $attendee );
		$attendee->ticket_name        = $this->get_product_title( $attendee );
		$attendee->ticket_sent        = null;
		$attendee->user_id            = null;

		$order = $this->get_order( $attendee );

		if ( $order ) {
			$attendee->order_id           = $order->ID;
			$attendee->order_status       = $order->post_status;
			$attendee->order_status_label = tribe( Tickets_View::class )->get_rsvp_options( $attendee->order_status );
			$attendee->purchaser_name     = get_post_meta( $order->ID, Order::$purchaser_full_name_meta_key, true );
			$attendee->purchaser_email    = get_post_meta( $order->ID, Order::$purchaser_email_meta_key, true );
		}

		if ( empty( $attendee->ticket_id ) ) {
			$attendee->ticket_id = $attendee->ID;
		}

		return $attendee;
	}

	/**
	 * Returns the product object related to an attendee
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return \WP_Post|\stdClass
	 */
	public function get_product( \WP_Post $attendee ) {
		$product = get_post_meta( $attendee->ID, Module::ATTENDEE_PRODUCT_KEY, true );

		if ( $product ) {
			return get_post( $product );
		}

		return (object) [];
	}

	/**
	 * Returns the product id related to an attendee
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return string
	 */
	public function get_product_id( \WP_Post $attendee ) {
		if ( empty( $attendee->product->ID ) ) {
			return '';
		}

		return (string) $attendee->product->ID;
	}

	/**
	 * Returns the product title related to an attendee
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return string
	 */
	public function get_product_title( \WP_Post $attendee ) {
		$ticket = get_post( $attendee->ticket_id );

		return ! empty( $ticket->post_title ) ?
			esc_html( $this->post_title ) :
			get_post_meta( $attendee->ID, static::$deleted_ticket_meta_key, true );
	}

	/**
	 * Returns the event id related to an attendee
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return string
	 */
	public function get_event_id( \WP_Post $attendee ) {
		return get_post_meta( $attendee->ID, static::$event_relation_meta_key, true );
	}

	/**
	 * Returns the ticket unique id related to an attendee
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return string
	 */
	public function get_unique_id( \WP_Post $attendee ) {
		$id = ! empty( $attendee->attendee_id ) ? $attendee->attendee_id : $attendee->ID;

		return get_post_meta( $id, static::$unique_id_meta_key, true );
	}

	/**
	 * Returns the ticket id related to an attendee
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return string
	 */
	public function get_ticket_id( \WP_Post $attendee ) {
		if ( $attendee->is_legacy_attendee ) {
			return $attendee->product_id;
		}

		return get_post_meta( $attendee->ID, static::$ticket_relation_meta_key, true );
	}

	/**
	 * Returns the security code for an attendee
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return string
	 */
	public function get_security_code( \WP_Post $attendee ) {
		if ( $attendee->is_legacy_attendee ) {
			return $attendee->security_code;
		}

		return get_post_meta( $attendee->ID, static::$security_code_meta_key, true );
	}

	/**
	 * Returns the check in status of an attendee
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return string
	 */
	public function get_check_in_status( \WP_Post $attendee ) {
		if ( $attendee->is_legacy_attendee ) {
			return $attendee->check_in;
		}

		return get_post_meta( $attendee->ID, static::$checked_in_meta_key, true );
	}

	/**
	 * Returns the status label used in the Status column
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return string
	 */
	public function get_status_label( \WP_Post $attendee ) {
		if ( $attendee->is_legacy_attendee ) {
			return $attendee->order_status_label;
		}

		$checked_in = $this->get_check_in_status( $attendee );

		if ( empty( $checked_in ) ) {
			return '';
		}

		return tribe( Tickets_View::class )->get_rsvp_options( '1' === $checked_in ? 'yes' : 'no' );
	}

	/**
	 * Returns the order object related to an attendee
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return array|WP_Post|null    The Order post object or array, `null` if not found.
	 */
	public function get_order( \WP_Post $attendee ) {
		return tec_tc_get_order(
			get_post_meta( $attendee->ID, static::$order_relation_meta_key, true )
		);
	}

	/**
	 * Returns the purchaser name if available
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return string
	 */
	public function get_holder_name( \WP_Post $attendee ) {
		$name = get_post_meta( $attendee->ID, static::$purchaser_name_meta_key, true );

		if ( $name ) {
			return esc_html( $name );
		}

		return esc_html__( 'Name not available', 'event-tickets' );
	}

	/**
	 * Returns the purchaser email if available
	 *
	 * @since 5.2.0
	 *
	 * @param \WP_Post $attendee the attendee object.
	 *
	 * @return string
	 */
	public function get_holder_email( \WP_Post $attendee ) {
		$email = get_post_meta( $attendee->ID, static::$purchaser_email_meta_key, true );

		if ( $email ) {
			return esc_html( $email );
		}

		return esc_html__( 'Email not available', 'event-tickets' );
	}

	/**
	 * Check if the attendee is of valid type.
	 *
	 * @since 5.2.0
	 *
	 * @param int|\WP_Post $attendee The attendee object to check.
	 *
	 * @return bool
	 */
	public static function is_valid( $attendee ) {
		$attendee = get_post( $attendee );

		if ( ! $attendee ) {
			return false;
		}

		return static::POSTTYPE === $attendee->post_type;
	}
}
