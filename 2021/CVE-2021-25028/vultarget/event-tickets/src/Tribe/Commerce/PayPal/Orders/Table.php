<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Orders__Table
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Orders__Table extends WP_List_Table {

	/**
	 * @var string The user option that will be used to store the number of orders per page to show.
	 */
	public $per_page_option;

	/**
	 * @var int The current post ID
	 */
	public $post_id;

	/**
	 * @var Tribe__Tickets__Commerce__PayPal__Orders__Sales
	 */
	protected $sales;

	/**
	 * Tribe__Tickets__Commerce__PayPal__Orders__Table constructor.
	 *
	 * @since 4.7
	 */
	public function __construct() {
		$args = array(
			'singular' => 'order',
			'plural'   => 'orders',
			'ajax'     => true,
		);

		$this->per_page_option = Tribe__Tickets__Commerce__PayPal__Screen_Options::$per_page_user_option;

		$screen = get_current_screen();

		$screen->add_option( 'per_page', array(
			'label'  => __( 'Number of orders per page:', 'event-tickets' ),
			'option' => $this->per_page_option,
		) );

		$this->sales = tribe( 'tickets.commerce.paypal.orders.sales' );

		parent::__construct( $args );
	}

	/**
	 * Overrides the list of CSS classes for the WP_List_Table table tag.
	 * This function is not hookable in core, so it needs to be overridden!
	 *
	 * @since 4.10.7
	 *
	 * @return array List of CSS classes for the table tag.
	 */
	protected function get_table_classes() {
		$classes = [ 'widefat', 'striped', 'orders', 'tribe-commerce-orders' ];

		if ( is_admin() ) {
			$classes[] = 'fixed';
		}

		/**
		 * Filters the default classes added to the TCC order report `WP_List_Table`.
		 *
		 * @since 4.10.7
		 *
		 * @param array $classes The array of classes to be applied.
		 */
		$classes = apply_filters( 'tribe_tickets_commerce_order_table_classes', $classes );

		return $classes;
	}

	/**
	 * Checks the current user's permissions
	 *
	 * @since 4.7
	 */
	public function ajax_user_can() {
		$post_type = get_post_type_object( $this->screen->post_type );

		return ! empty( $post_type->cap->edit_posts ) && current_user_can( $post_type->cap->edit_posts );
	}

	/**
	 * Returns the  list of columns.
	 *
	 * @since 4.7
	 *
	 * @return array An associative array in the format [ <slug> => <title> ]
	 */
	public function get_columns() {
		$columns = array(
			'order'     => __( 'Order', 'event-tickets' ),
			'purchaser' => __( 'Purchaser', 'event-tickets' ),
			'email'     => __( 'Email', 'event-tickets' ),
			'purchased' => __( 'Purchased', 'event-tickets' ),
			'date'      => __( 'Date', 'event-tickets' ),
			'status'    => __( 'Status', 'event-tickets' ),
		);

		$columns['total'] = __( 'Total', 'event-tickets' );

		return $columns;
	}

	/**
	 * Handler for the columns that don't have a specific column_{name} handler function.
	 *
	 * @since 4.7
	 *
	 * @param array $item
	 * @param $column
	 *
	 * @return string
	 */
	public function column_default( $item, $column ) {
		$value = empty( $item->$column ) ? '' : $item->$column;

		return apply_filters( 'tribe_tickets_commerce_paypal_orders_table_column', $value, $item, $column );
	}

	/**
	 * Handler for the date column
	 *
	 * @since 4.7
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_date( $item ) {
		$date = $item['purchase_time'];

		return esc_html( Tribe__Date_Utils::reformat( $date, Tribe__Date_Utils::DATEONLYFORMAT ) );
	}

	/**
	 * Handler for the purchased column
	 *
	 * @since 4.7
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_purchased( $item ) {
		$output = '';

		foreach ( $item['items'] as $i ) {
			$name     = esc_html( $i['item_name'] );
			$quantity = esc_html( (int) $i['quantity'] );
			$output   .= "<div class='tribe-line-item'>{$quantity} - {$name}</div>";
		}

		return $output;
	}

	/**
	 * Handler for the order column
	 *
	 * @since 4.7
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_order( $item ) {
		$order_number = $item['number'];

		$order_number_link = '<a href="' . esc_url( $item['url'] ) . '" target="_blank">' . esc_html( $order_number ) . '</a>';

		$output = sprintf( esc_html__( '%1$s', 'event-tickets' ), $order_number_link );

		switch ( $item['status'] ) {
			case Tribe__Tickets__Commerce__PayPal__Stati::$refunded:
				$refund_order_number      = $item['refund_number'];
				$refund_order_number_link = '<a href="' . esc_url( $item['refund_url'] ) . '" target="_blank">' . esc_html( $refund_order_number ) . '</a>';
				$output                   .= '<div class="order-status order-status-' . esc_attr( $item['status'] ) . '">';
				$output                   .= sprintf( esc_html__( 'Refunded with %s', 'event-tickets' ), $refund_order_number_link );
				$output                   .= '</div>';
				break;
			case Tribe__Tickets__Commerce__PayPal__Stati::$completed:
				break;
			default:
				$output .= '<div class="order-status order-status-' . esc_attr( $item['status'] ) . '">';
				$output .= esc_html( ucwords( $item['status_label'] ) );
				$output .= '</div>';
				break;
		}

		return $output;
	}

	/**
	 * Handler for the total column
	 *
	 * @since 4.7
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_total( $item ) {
		$post_id = Tribe__Utils__Array::get( $_GET, 'post_id', null );

		return tribe_format_currency( number_format( $item['line_total'], 2 ), $post_id );
	}

	/**
	 * Generates content for a single row of the table
	 *
	 * @since 4.7
	 *
	 * @param array $item The current item
	 */
	public function single_row( $item ) {
		echo '<tr class="' . esc_attr( $item['status'] ) . '">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @since 4.7
	 */
	public function prepare_items() {
		$this->post_id = Tribe__Utils__Array::get( $_GET, 'event_id', Tribe__Utils__Array::get( $_GET, 'post_id', 0 ), 0 );

		/** @var \Tribe__Tickets__Commerce__PayPal__Orders__Sales $sales */
		$sales = tribe( 'tickets.commerce.paypal.orders.sales' );

		$product_ids = Tribe__Utils__Array::get( $_GET, 'product_ids', null );

		$product_ids = ! empty( $product_ids ) ? explode( ',', $product_ids ) : null;

		// in the context of this report some order statuses that normally don't should show a non 0 line total
		add_filter( 'tribe_tickets_commerce_paypal_revenue_generating_order_statuses', array( $this, 'filter_revenue_generating_order_statuses' ) );
		$items = $sales->get_orders_for_post( $this->post_id, $product_ids );
		remove_filter( 'tribe_tickets_commerce_paypal_revenue_generating_order_statuses', array( $this, 'filter_revenue_generating_order_statuses' ) );

		$search = isset( $_REQUEST['s'] ) ? esc_attr( trim($_REQUEST['s']) ) : false;
		if ( ! empty( $search ) ) {
			$items = $this->filter_orders_by_string( $search, $items );
		}

		$total_items = count( $items );

		$per_page = $this->get_items_per_page( $this->per_page_option );

		$current_page = $this->get_pagenum();

		$this->items = array_slice( $items, ( $current_page - 1 ) * $per_page, $per_page );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 4.7
	 */
	public function no_items() {
		_e( 'No matching orders found.', 'event-tickets' );
	}

	/**
	 * Returns the customer name.
	 *
	 * @since 4.7
	 *
	 * @param array $item The current item.
	 *
	 * @return string
	 */
	public function column_purchaser( $item ) {
		return esc_html( $item['purchaser_name'] );
	}

	/**
	 * Returns the customer email.
	 *
	 * @since 4.7
	 *
	 * @param array $item The current item.
	 *
	 * @return string
	 */
	public function column_email( $item ) {
		return esc_html( $item['purchaser_email'] );
	}

	/**
	 * Returns the order status.
	 *
	 * @since 4.7
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public function column_status( $item ) {
		return esc_html( $item['status_label'] );
	}

	/**
	 * Filters the items by a search string.
	 *
	 * @since 4.7
	 *
	 * @param array  $items  An array of candidate items.
	 * @param string $search The string to look for
	 *
	 * @return array An array of filtered items.
	 */
	protected function filter_orders_by_string( $search, array $items ) {
		if ( empty( $items ) ) {
			return $items;
		}

		$search_keys = [ 'number', 'status', 'status_label', 'purchaser_name', 'purchaser_email', 'purchase_time' ];

		/**
		 * Filters the item keys that should be used to filter orders while searching them.
		 *
		 * @since 4.7
		 *
		 * @param array  $search_keys The keys that should be used to search orders
		 * @param array  $items       The orders list
		 * @param string $search      The current search string.
		 */
		$search_keys = apply_filters( 'tribe_tickets_commerce_paypal_search_orders_by', $search_keys, $items, $search );

		$filtered = [];
		foreach ( $items as $order_number => $order_data ) {
			$keys = array_intersect( array_keys( $order_data ), $search_keys );
			foreach ( $keys as $key ) {
				if ( ! empty( $order_data[ $key ] ) && false !== stripos( $order_data[ $key ], $search ) ) {
					$filtered[ $order_number ] = $order_data;
					break;
				}
			}
		}

		return $filtered;
	}

	/**
	 * Filters the order statuses that are considered to generate revenue.
	 *
	 * While in other contexts some order statuses, like "pending", should correctly
	 * generate a line total of 0, in the context of this table the line total for
	 * some statuses should show as not 0.
	 *
	 * @since 4.7
	 *
	 * @param array $statuses
	 *
	 * @return array
	 */
	public function filter_revenue_generating_order_statuses( array $statuses = array() ) {
		/** @var Tribe__Tickets__Status__Manager $status_mgr */
		$status_mgr = tribe( 'tickets.status' );

		$statuses = array_merge(
			$statuses,
			$status_mgr->get_statuses_by_action( array( 'stock_reduced', 'tpp' ) )
		);

		return $statuses;
	}
}
