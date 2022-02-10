<?php

use Tribe__Utils__Array as Arr;
use Tribe__Tickets__Commerce__PayPal__Stati as PayPal__Stati;

/**
 * The ORM/Repository class for Tribe Commerce (PayPal) attendees.
 *
 * @since 4.10.6
 *
 * @property Tribe__Tickets__Commerce__PayPal__Main $attendee_provider
 */
class Tribe__Tickets__Repositories__Attendee__Commerce extends Tribe__Tickets__Attendee_Repository {

	/**
	 * Key name to use when limiting lists of keys.
	 *
	 * @var string
	 */
	protected $key_name = 'tribe-commerce';

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct();

		$this->attendee_provider = tribe( 'tickets.commerce.paypal' );

		$this->create_args['post_type'] = $this->attendee_provider->attendee_object;

		// Use a regular variable so we can get constants from it in a PHP <7.0 compatible way.
		$attendee_provider = $this->attendee_provider;

		// Add object specific aliases.
		$this->update_fields_aliases = array_merge(
			$this->update_fields_aliases,
			[
				'ticket_id'       => $attendee_provider::ATTENDEE_PRODUCT_KEY,
				'event_id'        => $attendee_provider::ATTENDEE_EVENT_KEY,
				'post_id'         => $attendee_provider::ATTENDEE_EVENT_KEY,
				'security_code'   => $attendee_provider->security_code,
				'order_id'        => $attendee_provider->order_key,
				'optout'          => $attendee_provider->attendee_optout_key,
				'user_id'         => $attendee_provider->attendee_user_id,
				'price_paid'      => $attendee_provider->price_paid,
				'price_currency'  => $attendee_provider->price_currency,
				'full_name'       => $attendee_provider->full_name,
				'email'           => $attendee_provider->email,
				'attendee_status' => $attendee_provider->attendee_tpp_key,
				'refund_order_id' => $attendee_provider->refund_order_key,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function attendee_types() {
		return $this->limit_list( $this->key_name, parent::attendee_types() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function attendee_to_event_keys() {
		return $this->limit_list( $this->key_name, parent::attendee_to_event_keys() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function attendee_to_ticket_keys() {
		return $this->limit_list( $this->key_name, parent::attendee_to_ticket_keys() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function attendee_to_order_keys() {
		return $this->limit_list( $this->key_name, parent::attendee_to_order_keys() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function purchaser_name_keys() {
		return $this->limit_list( $this->key_name, parent::purchaser_name_keys() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function purchaser_email_keys() {
		return $this->limit_list( $this->key_name, parent::purchaser_email_keys() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function security_code_keys() {
		return $this->limit_list( $this->key_name, parent::security_code_keys() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function attendee_optout_keys() {
		return $this->limit_list( $this->key_name, parent::attendee_optout_keys() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function checked_in_keys() {
		return $this->limit_list( $this->key_name, parent::checked_in_keys() );
	}

	/**
	 * Set up the arguments to set for the attendee for this provider.
	 *
	 * @since 5.1.0
	 *
	 * @param array                         $args          List of arguments to set for the attendee.
	 * @param array                         $attendee_data List of additional attendee data.
	 * @param Tribe__Tickets__Ticket_Object $ticket        The ticket object or null if not relying on it.
	 *
	 * @return array List of arguments to set for the attendee.
	 */
	public function setup_attendee_args( $args, $attendee_data, $ticket = null ) {
		// Set default attendee status.
		if ( ! isset( $args['attendee_status'] ) ) {
			$args['attendee_status'] = PayPal__Stati::$completed;
		}

		// Set default currency symbol.
		if ( ! isset( $args['price_currency'] ) && $ticket ) {
			/** @var Tribe__Tickets__Commerce__Currency $currency */
			$currency        = tribe( 'tickets.commerce.currency' );
			$currency_symbol = $currency->get_currency_symbol( $ticket->ID, true );

			$args['price_currency'] = $currency_symbol;
		}

		return $args;
	}

	/**
	 * Handle backwards compatible actions for Tribe Commerce.
	 *
	 * @since 5.1.0
	 *
	 * @param WP_Post                       $attendee      The attendee object.
	 * @param array                         $attendee_data List of additional attendee data.
	 * @param Tribe__Tickets__Ticket_Object $ticket        The ticket object.
	 */
	public function trigger_create_actions( $attendee, $attendee_data, $ticket ) {
		$attendee_id           = $attendee->ID;
		$post_id               = Arr::get( $attendee_data, 'post_id' );
		$order_id              = Arr::get( $attendee_data, 'order_id' );
		$product_id            = $ticket->ID;
		$order_attendee_id     = Arr::get( $attendee_data, 'order_attendee_id' );
		$attendee_order_status = $attendee_data['attendee_status'];

		/**
		 * Action fired when an PayPal attendee ticket is created
		 *
		 * @since 4.7
		 *
		 * @param int    $attendee_id           Attendee post ID
		 * @param string $order_id              PayPal Order ID
		 * @param int    $product_id            PayPal ticket post ID
		 * @param int    $order_attendee_id     Attendee number in submitted order
		 * @param string $attendee_order_status The order status for the attendee.
		 */
		do_action( 'event_tickets_tpp_attendee_created', $attendee_id, $order_id, $product_id, $order_attendee_id, $attendee_order_status );

		/**
		 * Action fired when an PayPal attendee ticket is updated.
		 *
		 * This action will fire both when the attendee is created and
		 * when the attendee is updated.
		 * Hook into the `event_tickets_tpp_attendee_created` action to
		 * only act on the attendee creation.
		 *
		 * @since 4.7
		 *
		 * @param int    $attendee_id           Attendee post ID
		 * @param string $order_id              PayPal Order ID
		 * @param int    $product_id            PayPal ticket post ID
		 * @param int    $order_attendee_id     Attendee number in submitted order
		 * @param string $attendee_order_status The order status for the attendee.
		 */
		do_action( 'event_tickets_tpp_attendee_updated', $attendee_id, $order_id, $product_id, $order_attendee_id, $attendee_order_status );

		// Update the ticket sales numbers.
		if ( $post_id ) {
			$global_stock    = new Tribe__Tickets__Global_Stock( $post_id );
			$shared_capacity = false;

			if ( $global_stock->is_enabled() ) {
				$shared_capacity = true;
			}

			if ( Tribe__Tickets__Commerce__PayPal__Stati::$completed === $attendee_order_status ) {
				$this->attendee_provider->increase_ticket_sales_by( $product_id, 1, $shared_capacity, $global_stock );
			} elseif ( Tribe__Tickets__Commerce__PayPal__Stati::$refunded === $attendee_order_status ) {
				$this->attendee_provider->decrease_ticket_sales_by( $product_id, 1, $shared_capacity, $global_stock );
			}
		}

		parent::trigger_create_actions( $attendee, $attendee_data, $ticket );
	}

	/**
	 * Handle backwards compatible update actions for RSVPs.
	 *
	 * @since 5.1.0
	 *
	 * @param array $attendee_data List of attendee data to be saved.
	 */
	public function trigger_update_actions( $attendee_data ) {
		parent::trigger_update_actions( $attendee_data );

		$attendee_id = (int) Arr::get( $attendee_data, 'attendee_id' );

		if ( ! $attendee_id ) {
			return;
		}

		$attendee = $this->attendee_provider->get_attendee( $attendee_id );

		if ( ! $attendee ) {
			return;
		}

		$order_id              = $attendee['order_id'];
		$product_id            = $attendee['product_id'];
		$order_attendee_id     = 0;
		$attendee_order_status = $attendee['order_status'];

		/**
		 * Action fired when an PayPal attendee ticket is updated.
		 *
		 * This action will fire both when the attendee is created and
		 * when the attendee is updated.
		 * Hook into the `event_tickets_tpp_attendee_created` action to
		 * only act on the attendee creation.
		 *
		 * @since 4.7
		 *
		 * @param int    $attendee_id           Attendee post ID
		 * @param string $order_id              PayPal Order ID
		 * @param int    $product_id            PayPal ticket post ID
		 * @param int    $order_attendee_id     Attendee number in submitted order
		 * @param string $attendee_order_status The order status for the attendee.
		 */
		do_action( 'event_tickets_tpp_attendee_updated', $attendee_id, $order_id, $product_id, $order_attendee_id, $attendee_order_status );
	}
}
