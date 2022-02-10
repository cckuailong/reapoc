<?php

namespace TEC\Tickets\Commerce\Admin_Tables;

use TEC\Tickets\Commerce\Gateways\Manager;
use TEC\Tickets\Commerce\Status\Completed;
use TEC\Tickets\Commerce\Status\Refunded;
use TEC\Tickets\Commerce\Status\Status_Handler;
use \Tribe__Utils__Array as Arr;

use \WP_List_Table;
use \WP_Post;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/screen.php' );
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Class Admin Tables for Orders.
 *
 * @since 5.2.0
 *
 */
class Orders extends WP_List_Table {

	/**
	 * The user option that will be used to store the number of orders per page to show.
	 *
	 * @var string
	 */
	public $per_page_option = 20;

	/**
	 * The current post ID
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Orders Table constructor.
	 *
	 * @since 5.2.0
	 */
	public function __construct() {
		$args = [
			'singular' => 'order',
			'plural'   => 'orders',
			'ajax'     => true,
		];

		parent::__construct( $args );
	}

	/**
	 * Overrides the list of CSS classes for the WP_List_Table table tag.
	 * This function is not hookable in core, so it needs to be overridden!
	 *
	 * @since 5.2.0
	 *
	 * @return array List of CSS classes for the table tag.
	 */
	protected function get_table_classes() {
		$classes = [ 'widefat', 'striped', 'tribe-tickets-commerce-report-orders' ];

		if ( is_admin() ) {
			$classes[] = 'fixed';
		}

		/**
		 * Filters the default classes added to the Tickets Commerce order report `WP_List_Table`.
		 *
		 * @since 5.2.0
		 *
		 * @param array $classes The array of classes to be applied.
		 */
		return apply_filters( 'tec_tickets_commerce_reports_orders_table_classes', $classes );
	}

	/**
	 * Checks the current user's permissions
	 *
	 * @since 5.2.0
	 */
	public function ajax_user_can() {
		$post_type = get_post_type_object( $this->screen->post_type );

		return ! empty( $post_type->cap->edit_posts ) && current_user_can( $post_type->cap->edit_posts );
	}

	/**
	 * Returns the  list of columns.
	 *
	 * @since 5.2.0
	 *
	 * @return array An associative array in the format [ <slug> => <title> ]
	 */
	public function get_columns() {
		$columns = [
			'order'            => __( 'Order', 'event-tickets' ),
			'purchaser'        => __( 'Purchaser', 'event-tickets' ),
			'email'            => __( 'Email', 'event-tickets' ),
			'purchased'        => __( 'Purchased', 'event-tickets' ),
			'date'             => __( 'Date', 'event-tickets' ),
			'gateway'          => __( 'Gateway', 'event-tickets' ),
			'gateway_order_id' => __( 'Gateway ID', 'event-tickets' ),
			'status'           => __( 'Status', 'event-tickets' ),
			'total'            => __( 'Total', 'event-tickets' ),
		];

		return $columns;
	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @since 5.2.0
	 *
	 * @param WP_Post $item The current item
	 */
	public function single_row( $item ) {
		echo '<tr class="' . esc_attr( $item->post_status ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @since 5.2.0
	 */
	public function prepare_items() {
		$post_id = tribe_get_request_var( 'post_id', 0 );
		$post_id = tribe_get_request_var( 'event_id', $post_id );

		$this->post_id = $post_id;
		$product_ids   = tribe_get_request_var( 'product_ids' );
		$product_ids   = ! empty( $product_ids ) ? explode( ',', $product_ids ) : null;

		$search    = tribe_get_request_var( 's' );
		$page      = absint( tribe_get_request_var( 'paged', 0 ) );
		$arguments = [
			'status'         => 'any',
			'paged'          => $page,
			'posts_per_page' => $this->per_page_option,
		];

		if ( $search ) {
			$arguments['search'] = $search;
		}

		if ( ! empty( $post_id ) ) {
			$arguments['events'] = $post_id;
		}
		if ( ! empty( $product_ids ) ) {
			$arguments['tickets'] = $product_ids;
		}

		$orders_repository = tec_tc_orders()->by_args( $arguments );

		$total_items = $orders_repository->found();

		$this->items = $orders_repository->all();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $this->per_page_option,
		] );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 5.2.0
	 */
	public function no_items() {
		_e( 'No matching orders found.', 'event-tickets' );
	}

	/**
	 * Handler for the columns that don't have a specific column_{name} handler function.
	 *
	 * @since 5.2.0
	 *
	 * @param WP_Post $item
	 * @param         $column
	 *
	 * @return string
	 */
	public function column_default( $item, $column ) {
		return empty( $item->$column ) ? '??' : $item->$column;
	}

	/**
	 * Returns the customer name.
	 *
	 * @since 5.2.0
	 *
	 * @param WP_Post $item The current item.
	 *
	 * @return string
	 */
	public function column_purchaser( $item ) {
		return esc_html( $item->purchaser['full_name'] );
	}

	/**
	 * Returns the customer email.
	 *
	 * @since 5.2.0
	 *
	 * @param WP_Post $item The current item.
	 *
	 * @return string
	 */
	public function column_email( $item ) {
		return esc_html( $item->purchaser['email'] );
	}

	/**
	 * Returns the order status.
	 *
	 * @since 5.2.0
	 *
	 * @param WP_Post $item
	 *
	 * @return string
	 */
	public function column_status( $item ) {
		$status = tribe( Status_Handler::class )->get_by_wp_slug( $item->post_status );

		return esc_html( $status->get_name() );
	}

	/**
	 * Handler for the date column
	 *
	 * @since 5.2.0
	 *
	 * @param WP_Post $item
	 *
	 * @return string
	 */
	public function column_date( $item ) {
		return esc_html( \Tribe__Date_Utils::reformat( $item->post_modified, \Tribe__Date_Utils::DATEONLYFORMAT ) );
	}

	/**
	 * Handler for the purchased column
	 *
	 * @since 5.2.0
	 *
	 * @param WP_Post $item
	 *
	 * @return string
	 */
	public function column_purchased( $item ) {
		$output = '';

		foreach ( $item->items as $cart_item ) {
			$ticket   = \Tribe__Tickets__Tickets::load_ticket_object( $cart_item['ticket_id'] );
			$name     = esc_html( $ticket->name );
			$quantity = esc_html( (int) $cart_item['quantity'] );
			$output   .= "<div class='tribe-line-item'>{$quantity} - {$name}</div>";
		}

		return $output;
	}

	/**
	 * Handler for the order column
	 *
	 * @since 5.2.0
	 *
	 * @param WP_Post $item
	 *
	 * @return string
	 */
	public function column_order( $item ) {
		$output = sprintf( esc_html__( '%1$s', 'event-tickets' ), $item->ID );
		$status = tribe( Status_Handler::class )->get_by_wp_slug( $item->post_status );

		switch ( $status->get_slug() ) {
			default:
				$output .= '<div class="order-status order-status-' . esc_attr( $status->get_slug() ) . '">';
				$output .= esc_html( ucwords( $status->get_name() ) );
				$output .= '</div>';
				break;
		}

		return $output;
	}

	/**
	 * Handler for the total column
	 *
	 * @since 5.2.0
	 *
	 * @param WP_Post $item
	 *
	 * @return string
	 */
	public function column_total( $item ) {
		return tribe_format_currency( $item->total_value, $this->post_id );
	}

	/**
	 * Handler for gateway order id.
	 *
	 * @since 5.2.0
	 *
	 * @param WP_Post $item
	 *
	 * @return string
	 */
	public function column_gateway_order_id( $item ) {
		return $item->gateway_order_id;
	}

	/**
	 * Handler for gateway column
	 *
	 * @since 5.2.0
	 *
	 * @param WP_Post $item
	 *
	 * @return string
	 */
	public function column_gateway( $item ) {
		$gateway = tribe( Manager::class )->get_gateway_by_key( $item->gateway );
		if ( ! $gateway ) {
			return $item->gateway;
		}
		return $gateway::get_label();
	}
}
