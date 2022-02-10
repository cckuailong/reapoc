<?php

namespace TEC\Tickets\Commerce;

use TEC\Tickets\Commerce\Status\Denied;
use TEC\Tickets\Commerce\Status\Pending;
use TEC\Tickets\Commerce\Status\Status_Handler;
use TEC\Tickets\Commerce\Status\Status_Interface;
use TEC\Tickets\Commerce\Utils\Price;
use TEC\Tickets\Event;

use Tribe__Utils__Array as Arr;
use Tribe__Tickets__Global_Stock as Event_Stock;

/**
 * Class Ticket.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce
 */
class Ticket {
	/**
	 * Tickets Commerce Ticket Post Type slug.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const POSTTYPE = 'tec_tc_ticket';

	/**
	 * Which meta holds the Relation ship between an ticket and which event it's registered to.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $event_relation_meta_key = '_tec_tickets_commerce_event';

	/**
	 * Which meta holds the data for showing the ticket description.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $show_description_meta_key = '_tribe_ticket_show_description';

	/**
	 * Which meta holds the data for the ticket sku.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $sku_meta_key = '_sku';

	/**
	 * Which meta holds the data for the ticket price.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $price_meta_key = '_price';

	/**
	 * Which meta holds the data for the ticket sales.
	 *
	 * @since 5.2.0
	 *
	 * @var string
	 */
	public static $sales_meta_key = 'total_sales';

	/**
	 * Which meta holds the data for the ticket stock mode.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $stock_mode_meta_key = Event_Stock::TICKET_STOCK_MODE;

	/**
	 * Which meta holds the data for the ticket stock.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $stock_meta_key = '_stock';

	/**
	 * Which meta holds the data for the ticket stock status.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $stock_status_meta_key = '_stock_status';

	/**
	 * Which meta holds the data for the ticket allows backorders.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $allow_backorders_meta_key = '_backorders';

	/**
	 * Which meta holds the data for the ticket is managing stock.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $should_manage_stock_meta_key = '_manage_stock';

	/**
	 * Prefix for the counter for a given status..
	 *
	 * @since 5.2.0
	 *
	 * @var string
	 */
	public static $status_count_meta_key_prefix = '_tec_tc_ticket_status_count';

	/**
	 * Register this Class post type into WP.
	 *
	 * @since 5.1.9
	 */
	public function register_post_type() {

		$post_type_args = [
			'label'           => __( 'Tickets', 'event-tickets' ),
			'labels'          => [
				'name'          => __( 'Tickets Commerce Tickets', 'event-tickets' ),
				'singular_name' => __( 'Tickets Commerce Ticket', 'event-tickets' ),
			],
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
		 * Filter the arguments that craft the ticket post type.
		 *
		 * @see   register_post_type
		 *
		 * @since 5.1.9
		 *
		 * @param array $post_type_args Post type arguments, passed to register_post_type()
		 */
		$post_type_args = apply_filters( 'tec_tickets_commerce_ticket_post_type_args', $post_type_args );

		register_post_type( static::POSTTYPE, $post_type_args );
	}

	/**
	 * Gets the meta Key for a given status count on a ticket.
	 *
	 * @since 5.2.0
	 *
	 * @param Status_Interface $status
	 *
	 * @return string
	 */
	public static function get_status_count_meta_key( Status_Interface $status ) {
		return static::$status_count_meta_key_prefix . ':' . $status->get_slug();
	}

	/**
	 * Modify the counters for all the tickets involved on this particular order.
	 *
	 * @since 5.2.0
	 *
	 * @param Status_Interface      $new_status New post status.
	 * @param Status_Interface|null $old_status Old post status.
	 * @param \WP_Post              $post       Post object.
	 */
	public function modify_counters_by_status( $new_status, $old_status, $post ) {
		$order = tec_tc_get_order( $post );

		// This should never be the case, but lets be safe.
		if ( empty( $order->items ) ) {
			return;
		}

		foreach ( $order->items as $item ) {
			$ticket_id              = $item['ticket_id'];
			$new_status_meta_key    = static::get_status_count_meta_key( $new_status );
			$current_new_status_qty = get_post_meta( $ticket_id, $new_status_meta_key, true );
			if ( ! $current_new_status_qty ) {
				$current_new_status_qty = 0;
			}
			update_post_meta( $ticket_id, $new_status_meta_key, (int) $current_new_status_qty + $item['quantity'] );

			if ( $old_status ) {
				$old_status_meta_key    = static::get_status_count_meta_key( $old_status );
				$current_old_status_qty = get_post_meta( $ticket_id, $old_status_meta_key, true );
				if ( ! $current_old_status_qty ) {
					$current_old_status_qty = 0;
				}
				update_post_meta( $ticket_id, $old_status_meta_key, max( 0, (int) $current_old_status_qty - $item['quantity'] ) );
			}
		}
	}

	/**
	 * Given a valid ticket will fetch the quantity of orders on each one of the registered status based on the counting
	 * that is handled by the Order status transitions system.
	 *
	 * @since 5.2.0
	 *
	 * @param int|string|\WP_Post $ticket_id Which ticket we are fetching the count for.
	 *
	 * @return array<string,int>|\WP_Error
	 */
	public function get_status_quantity( $ticket_id ) {
		$ticket = get_post( $ticket_id );

		if ( ! $ticket ) {
			return new \WP_Error( 'tec-tickets-commerce-non-existent-ticket' );
		}

		$all_statuses = tribe( Status_Handler::class )->get_all();
		$status_qty   = [];

		foreach ( $all_statuses as $status ) {
			$value = get_post_meta( $ticket->ID, static::get_status_count_meta_key( $status ), true );
			if ( empty( $value ) ) {
				$value = 0;
			}

			$status_qty[ $status->get_slug() ] = (int) $value;
		}

		return $status_qty;
	}

	/**
	 * Gets an individual ticket.
	 *
	 * @todo  TribeCommerceLegacy: This method needs to make use of the Ticket Model.
	 *
	 * @since 5.1.9
	 *
	 * @param int|\WP_Post $ticket_id
	 *
	 * @return null|\Tribe__Tickets__Ticket_Object
	 */
	public function get_ticket( $ticket_id ) {
		$product = get_post( $ticket_id );

		if ( ! $product ) {
			return null;
		}

		if ( static::POSTTYPE !== get_post_type( $product ) ) {
			return null;
		}

		/** @todo TribeCommerceLegacy: Remove the usage of Currency from Tribe Commerce totally, leave that behind. */
		add_filter( 'tribe_get_option_ticket-commerce-currency-code', [ tribe( Legacy_Compat::class ), 'maybe_load_currency_code_from_tribe_commerce' ] );

		$event_id = get_post_meta( $ticket_id, static::$event_relation_meta_key, true );

		$return = new \Tribe__Tickets__Ticket_Object();

		$return->description      = $product->post_excerpt;
		$return->ID               = $ticket_id;
		$return->name             = $product->post_title;
		$return->menu_order       = $product->menu_order;
		$return->post_type        = $product->post_type;
		$return->price            = get_post_meta( $ticket_id, '_price', true );
		$return->provider_class   = Module::class;
		$return->admin_link       = '';
		$return->show_description = $return->show_description();
		$return->start_date       = get_post_meta( $ticket_id, '_ticket_start_date', true );
		$return->end_date         = get_post_meta( $ticket_id, '_ticket_end_date', true );
		$return->start_time       = get_post_meta( $ticket_id, '_ticket_start_time', true );
		$return->end_time         = get_post_meta( $ticket_id, '_ticket_end_time', true );
		$return->sku              = get_post_meta( $ticket_id, '_sku', true );

		$qty_sold = get_post_meta( $ticket_id,  static::$sales_meta_key, true );

		// If the quantity sold wasn't set, default to zero
		$qty_sold = $qty_sold ? $qty_sold : 0;

		// Ticket stock is a simple reflection of remaining inventory for this item...
		$stock = (int) get_post_meta( $ticket_id, '_stock', true );

		// If we don't have a stock value, then stock should be considered 'unlimited'
		if ( null === $stock ) {
			$stock = - 1;
		}

		$return->manage_stock( 'yes' === get_post_meta( $ticket_id, '_manage_stock', true ) );
		$return->stock( $stock );
		$return->global_stock_mode( get_post_meta( $ticket_id, \Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE, true ) );
		$capped = get_post_meta( $ticket_id, \Tribe__Tickets__Global_Stock::TICKET_STOCK_CAP, true );

		if ( '' !== $capped ) {
			$return->global_stock_cap( $capped );
		}

		$qty_cancelled = $this->get_cancelled( $ticket_id );

		// Manually add cancelled to sold so that we can remove it correctly later when calculating.
		$return->qty_sold( $qty_sold + $qty_cancelled );

		$return->qty_cancelled( $qty_cancelled );

		$pending = $this->get_qty_pending( $ticket_id );

		$return->qty_pending( $pending );

		/**
		 * Use this Filter to change any information you want about this ticket
		 *
		 * @since 5.1.9
		 *
		 * @param object $ticket
		 * @param int    $post_id
		 * @param int    $ticket_id
		 */
		$return = apply_filters( 'tec_tickets_commerce_get_ticket_legacy', $return, $event_id, $ticket_id );

		return $return;
	}

	/**
	 * Returns the total number of cancelled tickets.
	 *
	 * @todo  TribeCommerceLegacy: Move this method into the another place.
	 *
	 * @since 5.1.9
	 *
	 * @param int $ticket_id The ticket post ID.
	 *
	 * @return int
	 */
	protected function get_cancelled( $ticket_id ) {
		$denied_orders = \Tribe__Tickets__Commerce__PayPal__Order::find_by( array(
			'ticket_id'      => $ticket_id,
			'post_status'    => Denied::SLUG,
			'posts_per_page' => - 1,
		), [
			'items',
		] );

		$denied = 0;
		foreach ( $denied_orders as $denied_order ) {
			$denied += $denied_order->get_item_quantity( $ticket_id );
		}

		return max( 0, $denied );
	}

	/**
	 * Returns the number of pending attendees by ticket.
	 *
	 * @todo  TribeCommerceLegacy: Move this method into the another place.
	 *
	 * @since 5.1.9
	 *
	 * @param int  $ticket_id The ticket post ID
	 * @param bool $refresh   Whether to try and use the cached value or not.
	 *
	 * @return int
	 */
	public function get_qty_pending( $ticket_id, $refresh = false ) {
		static $pending_attendees_by_ticket = [];

		if ( $refresh || empty( $pending_attendees_by_ticket[ $ticket_id ] ) ) {
			$pending_query = new \WP_Query( [
				'fields'     => 'ids',
				'per_page'   => 1,
				'post_type'  => Attendee::POSTTYPE,
				'meta_query' => [
					[
						'key'   => Attendee::$event_relation_meta_key,
						'value' => $ticket_id,
					],
					'relation' => 'AND',
					[
						'key'   => Attendee::$status_meta_key,
						'value' => tribe( Pending::class )->get_wp_slug(),
					],
				],
			] );

			$pending_attendees_by_ticket[ $ticket_id ] = $pending_query->found_posts;
		}

		return $pending_attendees_by_ticket[ $ticket_id ];
	}

	/**
	 * Legacy method ported from Tribe Commerce (TPP), we are specifically avoiding refactoring anything on the first
	 * stage of Tickets Commerce
	 *
	 * @todo  TribeCommerceLegacy: This method needs to be split into `create` and `update`
	 *
	 * @since 5.1.9
	 *
	 * @param       $post_id
	 * @param       $ticket
	 * @param array $raw_data
	 *
	 * @return false|int|\WP_Error
	 */
	public function save( $post_id, $ticket, $raw_data = [] ) {
		$save_type = 'update';

		if ( empty( $ticket->ID ) ) {
			$save_type = 'create';

			/* Create main product post */
			$args = array(
				'post_status'  => 'publish',
				'post_type'    => static::POSTTYPE,
				'post_author'  => get_current_user_id(),
				'post_excerpt' => $ticket->description,
				'post_title'   => $ticket->name,
				'menu_order'   => tribe_get_request_var( 'menu_order', - 1 ),
			);

			$ticket->ID = wp_insert_post( $args );

			// Relate event <---> ticket
			add_post_meta( $ticket->ID, static::$event_relation_meta_key, $post_id );

		} else {
			$args = array(
				'ID'           => $ticket->ID,
				'post_excerpt' => $ticket->description,
				'post_title'   => $ticket->name,
				'menu_order'   => $ticket->menu_order,
			);

			$ticket->ID = wp_update_post( $args );
		}

		if ( ! $ticket->ID ) {
			return false;
		}

		/** @var \Tribe__Tickets__Tickets_Handler $tickets_handler */
		$tickets_handler = tribe( 'tickets.handler' );

		// Updates if we should show Description.
		$ticket->show_description = isset( $ticket->show_description ) && tribe_is_truthy( $ticket->show_description ) ? 'yes' : 'no';
		update_post_meta( $ticket->ID, $tickets_handler->key_show_description, $ticket->show_description );

		// let's make sure float price values are formatted to "0.xyz"
		if ( is_numeric( $ticket->price ) ) {
			$ticket->price = (string) (int) $ticket->price === $ticket->price
				? (int) $ticket->price
				: (float) $ticket->price;
		}

		update_post_meta( $ticket->ID, '_price', $ticket->price );

		$ticket_data = \Tribe__Utils__Array::get( $raw_data, 'tribe-ticket', array() );
		tribe( Module::class )->update_capacity( $ticket, $ticket_data, $save_type );

		foreach ( [ 'start_date', 'start_time', 'end_date', 'end_time' ] as $time_key ) {
			if ( isset( $ticket->{$time_key} ) ) {
				update_post_meta( $ticket->ID, "_ticket_{$time_key}", $ticket->{$time_key} );
			} else {
				delete_post_meta( $ticket->ID, "_ticket_{$time_key}" );
			}
		}

		/**
		 * Toggle filter to allow skipping the automatic SKU generation.
		 *
		 * @param bool $should_default_ticket_sku
		 */
		$should_default_ticket_sku = apply_filters( 'tribe_tickets_should_default_ticket_sku', true );
		if ( $should_default_ticket_sku ) {
			// make sure the SKU is set to the correct value
			if ( ! empty( $raw_data['ticket_sku'] ) ) {
				$sku = $raw_data['ticket_sku'];
			} else {
				$post_author            = get_post( $ticket->ID )->post_author;
				$str                    = $raw_data['ticket_name'];
				$str                    = tribe_strtoupper( $str );
				$sku                    = "{$ticket->ID}-{$post_author}-" . str_replace( ' ', '-', $str );
				$raw_data['ticket_sku'] = $sku;
			}
			update_post_meta( $ticket->ID, '_sku', $sku );
		}

		// Fetches all Ticket Form data
		$data = \Tribe__Utils__Array::get( $raw_data, 'tribe-ticket', array() );

		// Fetch the Global stock Instance for this Event
		$event_stock = new \Tribe__Tickets__Global_Stock( $post_id );

		// Only need to do this if we haven't already set one - they shouldn't be able to edit it from here otherwise
		if ( ! $event_stock->is_enabled() ) {
			if ( isset( $data['event_capacity'] ) ) {
				$data['event_capacity'] = trim( filter_var( $data['event_capacity'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH ) );

				// If empty we need to modify to -1
				if ( '' === $data['event_capacity'] ) {
					$data['event_capacity'] = - 1;
				}

				// Makes sure it's an Int after this point
				$data['event_capacity'] = (int) $data['event_capacity'];

				$tickets_handler->remove_hooks();

				// We need to update event post meta - if we've set a global stock
				$event_stock->enable();
				$event_stock->set_stock_level( $data['event_capacity'], true );

				// Update Event capacity
				update_post_meta( $post_id, $tickets_handler->key_capacity, $data['event_capacity'] );
				update_post_meta( $post_id, $event_stock::GLOBAL_STOCK_ENABLED, 1 );

				$tickets_handler->add_hooks();
			}
		} else {
			// If the Global Stock is configured we pull it from the Event
			$global_capacity        = (int) tribe_tickets_get_capacity( $post_id );
			$data['event_capacity'] = (int) \Tribe__Utils__Array::get( 'event_capacity', $data, 0 );

			if ( ! empty( $data['event_capacity'] ) && $data['event_capacity'] !== $global_capacity ) {
				// Update stock level with $data['event_capacity'].
				$event_stock->set_stock_level( $data['event_capacity'], true );
			} else {
				// Set $data['event_capacity'] with what we know.
				$data['event_capacity'] = $global_capacity;
			}
		}

		// Default Capacity will be 0
		$default_capacity   = 0;
		$is_capacity_passed = true;

		// If we have Event Global stock we fetch that Stock
		if ( $event_stock->is_enabled() ) {
			$default_capacity = $data['event_capacity'];
		}

		// Fetch capacity field, if we don't have it use default (defined above)
		$data['capacity'] = trim( \Tribe__Utils__Array::get( $data, 'capacity', $default_capacity ) );

		// If empty we need to modify to the default
		if ( '' !== $data['capacity'] ) {
			// Makes sure it's an Int after this point
			$data['capacity'] = (int) $data['capacity'];

			// The only available value lower than zero is -1 which is unlimited
			if ( 0 > $data['capacity'] ) {
				$data['capacity'] = - 1;
			}

			$default_capacity = $data['capacity'];
		}

		// Fetch the stock if defined, otherwise use Capacity field
		$data['stock'] = trim( \Tribe__Utils__Array::get( $data, 'stock', $default_capacity ) );

		// If empty we need to modify to what every capacity was
		if ( '' === $data['stock'] ) {
			$data['stock'] = $default_capacity;
		}

		// Makes sure it's an Int after this point
		$data['stock'] = (int) $data['stock'];

		// The only available value lower than zero is -1 which is unlimited.
		if ( 0 > $data['stock'] ) {
			$data['stock'] = - 1;
		}

		$mode = isset( $data['mode'] ) ? $data['mode'] : 'own';

		if ( '' !== $mode ) {
			if ( 'update' === $save_type ) {
				$totals        = $tickets_handler->get_ticket_totals( $ticket->ID );
				$data['stock'] -= $totals['pending'] + $totals['sold'];
			}

			// In here is safe to check because we don't have unlimited = -1
			$status = ( 0 < $data['stock'] ) ? 'instock' : 'outofstock';

			update_post_meta( $ticket->ID, \Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE, $mode );
			update_post_meta( $ticket->ID, '_stock', $data['stock'] );
			update_post_meta( $ticket->ID, '_stock_status', $status );
			update_post_meta( $ticket->ID, '_backorders', 'no' );
			update_post_meta( $ticket->ID, '_manage_stock', 'yes' );

			// Prevent Ticket Capacity from going higher then Event Capacity
			if (
				$event_stock->is_enabled()
				&& \Tribe__Tickets__Global_Stock::OWN_STOCK_MODE !== $mode
				&& (
					'' === $data['capacity']
					|| $data['event_capacity'] < $data['capacity']
				)
			) {
				$data['capacity'] = $data['event_capacity'];
			}
		} else {
			// Unlimited Tickets
			// Besides setting _manage_stock to "no" we should remove the associated stock fields if set previously
			update_post_meta( $ticket->ID, '_manage_stock', 'no' );
			delete_post_meta( $ticket->ID, '_stock_status' );
			delete_post_meta( $ticket->ID, '_stock' );
			delete_post_meta( $ticket->ID, \Tribe__Tickets__Global_Stock::TICKET_STOCK_CAP );
			delete_post_meta( $ticket->ID, \Tribe__Tickets__Global_Stock::TICKET_STOCK_MODE );

			// Set Capacity -1 when we don't have a stock mode, which means unlimited
			$data['capacity'] = - 1;
		}

		if ( '' !== $data['capacity'] ) {
			// Update Ticket capacity
			update_post_meta( $ticket->ID, $tickets_handler->key_capacity, $data['capacity'] );
		}

		/**
		 * Generic action fired after saving a ticket (by type)
		 *
		 * @since 5.2.0
		 *
		 * @param int                            $post_id  Post ID of post the ticket is tied to
		 * @param \Tribe__Tickets__Ticket_Object $ticket   Ticket that was just saved
		 * @param array                          $raw_data Ticket data
		 * @param string                         $class    Commerce engine class
		 */
		do_action( "tec_tickets_commerce_after_{$save_type}_ticket", $post_id, $ticket, $raw_data, static::class );

		/**
		 * Generic action fired after saving a ticket.
		 *
		 * @since 5.2.0
		 *
		 * @param int                            $post_id  Post ID of post the ticket is tied to
		 * @param \Tribe__Tickets__Ticket_Object $ticket   Ticket that was just saved
		 * @param array                          $raw_data Ticket data
		 * @param string                         $class    Commerce engine class
		 */
		do_action( 'tec_tickets_commerce_after_save_ticket', $post_id, $ticket, $raw_data, static::class );

		/**
		 * Generic action fired after saving a ticket (by type)
		 *
		 * @todo  TribeCommerceLegacy
		 *
		 * @since 5.2.0
		 *
		 * @param int                            $post_id  Post ID of post the ticket is tied to
		 * @param \Tribe__Tickets__Ticket_Object $ticket   Ticket that was just saved
		 * @param array                          $raw_data Ticket data
		 * @param string                         $class    Commerce engine class
		 */
		do_action( "event_tickets_after_{$save_type}_ticket", $post_id, $ticket, $raw_data, static::class );

		/**
		 * Generic action fired after saving a ticket.
		 *
		 * @todo  TribeCommerceLegacy
		 *
		 * @since 5.2.0
		 *
		 * @param int                            $post_id  Post ID of post the ticket is tied to
		 * @param \Tribe__Tickets__Ticket_Object $ticket   Ticket that was just saved
		 * @param array                          $raw_data Ticket data
		 * @param string                         $class    Commerce engine class
		 */
		do_action( 'event_tickets_after_save_ticket', $post_id, $ticket, $raw_data, static::class );

		return $ticket->ID;
	}

	/**
	 * Deletes a given ticket.
	 *
	 * @todo  TribeCommerceLegacy: This method needs to be refactored to Tickets Commerce standards.
	 *
	 * @since 5.1.9
	 *
	 * @param $event_id
	 * @param $ticket_id
	 *
	 * @return bool
	 */
	public function delete( $event_id, $ticket_id ) {
		// Ensure we know the event and product IDs (the event ID may not have been passed in)
		if ( empty( $event_id ) ) {
			$event_id = get_post_meta( $ticket_id, Attendee::$event_relation_meta_key, true );
		}

		// Additional check (in case we were passed an invalid ticket ID and still can't determine the event)
		if ( empty( $event_id ) ) {
			return false;
		}

		$product_id = get_post_meta( $ticket_id, Attendee::$event_relation_meta_key, true );

		// @todo: should deleting an attendee replenish a ticket stock?

		// Store name so we can still show it in the attendee list
		$attendees      = tribe( Module::class )->get_attendees_by_id( $event_id );
		$post_to_delete = get_post( $ticket_id );

		foreach ( (array) $attendees as $attendee ) {
			if ( $attendee['product_id'] == $ticket_id ) {
				update_post_meta( $attendee['attendee_id'], Attendee::$deleted_ticket_meta_key, esc_html( $post_to_delete->post_title ) );
			}
		}

		// Try to kill the actual ticket/attendee post
		$delete = wp_delete_post( $ticket_id, true );
		if ( is_wp_error( $delete ) || ! isset( $delete->ID ) ) {
			return false;
		}

		\Tribe__Tickets__Attendance::instance( $event_id )->increment_deleted_attendees_count();
		do_action( 'tec_tickets_commerce_ticket_deleted', $ticket_id, $event_id, $product_id );
		\Tribe__Post_Transient::instance()->delete( $event_id, \Tribe__Tickets__Tickets::ATTENDEES_CACHE );

		return true;
	}

	/**
	 * Update Stock and Global Stock when deleting an Attendee
	 *
	 * @todo  TribeCommerceLegacy: This should be moved into using a Flag Action.
	 *
	 * @since 5.1.9
	 *
	 * @param int $ticket_id  the attendee id being deleted
	 * @param int $post_id    the post or event id for the attendee
	 * @param int $product_id the ticket-product id in Tribe Commerce
	 */
	public function update_stock_after_deletion( $ticket_id, $post_id, $product_id ) {

		$global_stock    = new \Tribe__Tickets__Global_Stock( $post_id );
		$shared_capacity = false;
		if ( $global_stock->is_enabled() ) {
			$shared_capacity = true;
		}

		tribe( Module::class )->decrease_ticket_sales_by( $product_id, 1, $shared_capacity, $global_stock );
	}

	/**
	 * Update Global Stock.
	 *
	 * @todo  TribeCommerceLegacy: Not sure where this method fits, might just need to integrate it it into the
	 *        create/update methods and delete this.
	 *
	 * @since 5.1.9
	 *
	 * @param \Tribe__Tickets__Global_Stock $global_stock The global stock object.
	 * @param int                           $qty          The quantity to modify stock.
	 * @param bool                          $increase     Whether to increase stock, default is false.
	 */
	public function update_global_stock( $global_stock, $qty = 1, $increase = false ) {
		$level = $global_stock->get_stock_level();

		if ( $increase ) {
			$new_level = (int) $level + (int) $qty;
		} else {
			$new_level = (int) $level - (int) $qty;
		}

		$global_stock->set_stock_level( $new_level );
	}

	/**
	 * Increase the sales for a ticket by a specific quantity.
	 *
	 * @todo  TribeCommerceLegacy: This should be moved into using a Flag Action.
	 *
	 * @since 5.1.9
	 *
	 * @param int                                $ticket_id       The ticket post ID.
	 * @param int                                $quantity        The quantity to increase the ticket sales by.
	 * @param bool                               $shared_capacity Whether the ticket is using shared capacity.
	 * @param \Tribe__Tickets__Global_Stock|null $global_stock    The stock object or null.
	 *
	 * @return int The new sales amount.
	 */
	public function increase_ticket_sales_by( $ticket_id, $quantity = 1, $shared_capacity = false, $global_stock = null ) {
		// Adjust sales.
		$sales = (int) get_post_meta( $ticket_id,  static::$sales_meta_key, true ) + $quantity;

		update_post_meta( $ticket_id,  static::$sales_meta_key, $sales );

		if (  'own' !== $shared_capacity && $global_stock instanceof \Tribe__Tickets__Global_Stock ) {
			$this->update_global_stock( $global_stock, $quantity );
		}

		return $sales;
	}

	/**
	 * Decrease the sales for a ticket by a specific quantity.
	 *
	 * @todo  TribeCommerceLegacy: This should be moved into using a Flag Action.
	 *
	 * @since 5.1.9
	 *
	 * @param int                                $ticket_id       The ticket post ID.
	 * @param int                                $quantity        The quantity to increase the ticket sales by.
	 * @param bool                               $shared_capacity Whether the ticket is using shared capacity.
	 * @param \Tribe__Tickets__Global_Stock|null $global_stock    The stock object or null.
	 *
	 * @return int The new sales amount.
	 */
	public function decrease_ticket_sales_by( $ticket_id, $quantity = 1, $shared_capacity = false, $global_stock = null ) {
		// Adjust sales.
		$sales = (int) get_post_meta( $ticket_id,  static::$sales_meta_key, true ) - $quantity;

		// Prevent negatives.
		$sales = max( $sales, 0 );

		update_post_meta( $ticket_id,  static::$sales_meta_key, $sales );

		if ( $shared_capacity && $global_stock instanceof \Tribe__Tickets__Global_Stock ) {
			$this->update_global_stock( $global_stock, $quantity, true );
		}

		return $sales;
	}

	/**
	 * Gets the product price value.
	 *
	 * @todo  TribeCommerceLegacy: This should not be used, the model should be used.
	 *
	 * @since 5.1.9
	 *
	 * @param int|\WP_Post $product
	 *
	 * @return string
	 */
	public function get_price_value( $product ) {
		$product = get_post( $product );

		if ( ! $product instanceof \WP_Post ) {
			return false;
		}

		return get_post_meta( $product->ID, static::$price_meta_key, true );
	}

	/**
	 * Get's the product price html
	 *
	 * @todo  TribeCommerceLegacy: This should not be used, the model and a template should be used.
	 *
	 * @since 5.1.9
	 *
	 * @param int|object    $product
	 * @param array|boolean $attendee
	 *
	 * @return string
	 */
	public function get_price_html( $product, $attendee = false ) {
		$product_id = $product;

		if ( $product instanceof \WP_Post ) {
			$product_id = $product->ID;
		} elseif ( is_numeric( $product_id ) ) {
			$product = get_post( $product_id );
		} else {
			return '';
		}

		$price = Price::to_currency( Price::to_string( $this->get_price_value( $product ) ) );

		$price_html = '<span class="tribe-tickets-price-amount amount">' . esc_html( $price ) . '</span>';

		/**
		 * Allow filtering of the Price HTML
		 *
		 * @since 5.1.9
		 *
		 * @param string $price_html
		 * @param mixed  $product
		 * @param mixed  $attendee
		 *
		 */
		return apply_filters( 'tec_tickets_commerce_ticket_price_html', $price_html, $product, $attendee );
	}
}