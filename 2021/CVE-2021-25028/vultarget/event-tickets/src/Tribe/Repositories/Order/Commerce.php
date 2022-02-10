<?php

namespace Tribe\Tickets\Repositories\Order;

use Tribe__Utils__Array as Arr;
use Tribe__Tickets__Commerce__PayPal__Main;
use Tribe__Tickets__Commerce__PayPal__Stati as PayPal__Stati;
use Tribe\Tickets\Repositories\Order;
use Usage_Error;

/**
 * The ORM/Repository class for Tribe Commerce (PayPal) orders.
 *
 * @since 5.1.6
 *
 * @property Tribe__Tickets__Commerce__PayPal__Main $attendee_provider
 */
class Commerce extends Order {

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
		/*
		 * Hook into the status filtering before parent::__construct() runs.
		 *
		 * These can be moved in the future into each commerce provider class
		 * when we add order status filters to the Attendees repository.
		 */
		add_filter( 'tribe_tickets_repositories_order_statuses', [ $this, 'register_statuses' ] );
		add_filter( 'tribe_tickets_repositories_order_public_statuses', [ $this, 'register_public_statuses' ] );

		parent::__construct();

		$this->attendee_provider = tribe( 'tickets.commerce.paypal' );

		// Set the order post type.
		$this->default_args['post_type'] = $this->attendee_provider->order_object;
	}

	/**
	 * Filtering the list of all order statuses to add ET+ provider order statuses.
	 *
	 * This can be moved in the future into each commerce provider class
	 * when we add order status filters to the Attendees repository.
	 *
	 * @since 5.1.6
	 *
	 * @param array $statuses List of all order statuses.
	 *
	 * @return array List of all order statuses.
	 */
	public function register_statuses( $statuses ) {
		/** @var Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		$statuses = array_merge( $statuses, $status_mgr->get_statuses_by_action( 'all', 'tpp' ) );

		// Enforce lowercase for comparison purposes.
		$statuses = array_map( 'strtolower', $statuses );

		// Prevent unnecessary duplicates.
		return array_unique( $statuses );
	}

	/**
	 * Filtering the list of public order statuses to add provider public order statuses.
	 *
	 * This can be moved in the future into each commerce provider class
	 * when we add order status filters to the Attendees repository.
	 *
	 * @since 5.1.6
	 *
	 * @param array $public_order_statuses List of public order statuses.
	 *
	 * @return array List of public order statuses.
	 */
	public function register_public_statuses( $public_order_statuses ) {
		$public_order_statuses[] = 'completed';

		// Prevent unnecessary duplicates.
		return array_unique( $public_order_statuses );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 5.1.6
	 *
	 * @return array|null|WP_Post|false The new post object or false if unsuccessful.
	 *
	 * @throws Usage_Error
	 */
	public function create() {
		// @todo Replace the logic here.
		$order_data = $this->updates;

		$required_details = [
			'full_name',
			'email',
			'tickets',
		];

		foreach ( $required_details as $required_detail ) {
			// Detail is not set.
			if ( ! isset( $order_data[ $required_detail ] ) ) {
				throw new Usage_Error( sprintf( 'You must provide "%s" to create a new order.', $required_detail ) );
			}

			// Detail is empty.
			if ( empty( $order_data[ $required_detail ] ) ) {
				throw new Usage_Error( sprintf( 'Order field "%s" is empty.', $required_detail ) );
			}
		}

		$full_name         = $order_data['full_name'];
		$email             = $order_data['email'];
		$tickets           = $order_data['tickets'];
		$first_name        = Arr::get( $order_data, 'first_name' );
		$last_name         = Arr::get( $order_data, 'last_name' );
		$user_id           = (int) Arr::get( $order_data, 'user_id', 0 );
		$create_user       = (bool) Arr::get( $order_data, 'create_user', false );
		$use_existing_user = (bool) Arr::get( $order_data, 'use_existing_user', true );
		$send_emails       = (bool) Arr::get( $order_data, 'send_emails', false );
		$order_status      = Arr::get( $order_data, 'order_status', 'completed' );

		$order_status = strtolower( trim( $order_status ) );

		// Maybe set the first / last name.
		if ( null === $first_name || null === $last_name ) {
			$first_name = $full_name;
			$last_name  = '';

			// Get first name and last name.
			if ( false !== strpos( $full_name, ' ' ) ) {
				$name_parts = explode( ' ', $full_name );

				// First name is first text.
				$first_name = array_shift( $name_parts );

				// Last name is everything the first text.
				$last_name = implode( ' ', $name_parts );
			}
		}

		if ( 0 === $user_id ) {
			$user_args = [
				'use_existing_user' => $use_existing_user,
				'create_user'       => $create_user,
				'send_email'        => $send_emails,
				'display_name'      => $full_name,
				'first_name'        => $first_name,
				'last_name'         => $last_name,
			];

			$user_id = (int) $this->attendee_provider->maybe_setup_attendee_user_from_email( $email, $user_args );
		}

		$cart_items = [];

		// Build list of downloads and cart items to use.
		foreach ( $tickets as $ticket ) {
			$cart_item = [
				'id'       => 0,
				'quantity' => 0,
			];

			$cart_item = array_merge( $cart_item, $ticket );

			if ( $cart_item['id'] < 1 ) {
				throw new Usage_Error( 'Every ticket must have a valid id set to be added to an order.' );
			}

			// Skip empty quantities.
			if ( $cart_item['quantity'] < 1 ) {
				continue;
			}

			$cart_items[] = $cart_item;
		}

		// Create the order.
		// @todo Create the order with $order_status.
		$order_id = 0;

		// Add items to the order.
		foreach ( $cart_items as $cart_item ) {
			// $cart_item['id'];
			// $cart_item['quantity'];
		}

		return get_post( $order_id );
	}
}
