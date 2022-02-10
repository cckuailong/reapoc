<?php

namespace TEC\Tickets\Commerce\Flag_Actions;

use TEC\Tickets\Commerce\Order;
use TEC\Tickets\Commerce\Status\Not_Completed;
use TEC\Tickets\Commerce\Status\Pending;
use TEC\Tickets\Commerce\Status\Status_Interface;
use Tribe__Utils__Array as Arr;

/**
 * Class End_Duplicated_Pending_Orders, normally triggered when a given order is completed it will modify the status of all the
 * other orders created with the same Hash cart key, which prevents leaving pending orders open when one was completed.
 *
 * @since    5.2.0
 *
 * @package  TEC\Tickets\Commerce\Flag_Actions
 */
class End_Duplicated_Pending_Orders extends Flag_Action_Abstract {

	/**
	 * {@inheritDoc}
	 *
	 * @since 5.2.0
	 */
	protected $flags = [
		'end_duplicated_pending_orders',
	];

	/**
	 * {@inheritDoc}
	 *
	 * @since 5.2.0
	 */
	protected $post_types = [
		Order::POSTTYPE,
	];

	/**
	 * {@inheritDoc}
	 *
	 * @since 5.2.0
	 */
	public function handle( Status_Interface $new_status, $old_status, \WP_Post $post ) {
		if ( empty( $post->hash ) ) {
			return;
		}

		$orders_query = tec_tc_orders()->by_args( [
			'status'   => tribe( Pending::class )->get_wp_slug(),
			'hash'     => $post->hash,
			'per_page' => - 1,
		] );

		if ( ! $orders_query->found() ) {
			return;
		}

		$duplicated_orders = $orders_query->all();
		foreach ( $duplicated_orders as $order ) {
			tribe( Order::class )->modify_status( $order->ID, Not_Completed::SLUG );
		}
	}
}