<?php

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * A JSON-LD class to hook and change how Orders work on Events
 * @todo rework this class to make it standalone from The Events Calendar
 */
class Tribe__Tickets__JSON_LD__Order {

	/**
	 * Get (and instantiate, if necessary) the instance of the class
	 *
	 * @static
	 * @return self
	 *
	 */
	public static function instance() {
		static $instance;

		if ( ! $instance instanceof self ) {
			$instance = new self;
		}

		return $instance;
	}

	/**
	 * Value indicating low stock availability for a specific ticket.
	 *
	 * This can be overridden with the tribe_tickets_json_ld_low_inventory_level filter.
	 *
	 * @var int
	 */
	protected $low_stock = 5;

	/**
	 * Setup Google Event Data for tickets.
	 */
	public static function hook() {
		$myself = self::instance();

		add_filter( 'tribe_json_ld_event_object', array( $myself, 'add_ticket_data' ), 10, 3 );

		$event_type = class_exists( 'Tribe__Events__Main' ) ? Tribe__Events__Main::POSTTYPE : 'tribe_events';
		$post_types = (array) tribe_get_option( 'ticket-enabled-post-types', array() );

		/**
		 * Other types can have tickets as well we might need to hook into each type to add tickets if any has tickets
		 */
		$filters = array();
		foreach ( $post_types as $post_type ) {
			if ( $event_type === $post_type ) {
				continue;
			}

			/**
			 * This will allow you to change the type for the Rich Snippet, by default it will use the type Product for
			 * any Post type or Page. If this is runs in a book post type the filter becomes something like.
			 *
			 * @example tribe_events_json_ld_book_type
			 *
			 * @see http://schema.org/Product
			 *
			 * @since 4.7.1
			 *
			 * @return string
			 */
			$filters[] = strtolower( (string) apply_filters( "tribe_tickets_json_ld_{$post_type}_type", 'Product' ) );
		}

		/**
		 * Avoid duplicates calls to add_filter
		 */
		$filters = array_unique( $filters );
		foreach ( $filters as $type ) {
			add_filter( "tribe_json_ld_{$type}_object", array( $myself, 'add_ticket_data' ), 10, 3 );
		}
	}

	/**
	 * Used to setup variables on this class
	 */
	protected function __construct() {
		/**
		 * Allow users to change the Low inventory mark
		 * @var int
		 */
		$this->low_stock = apply_filters( 'tribe_tickets_json_ld_low_inventory_level', $this->low_stock );
	}

	/**
	 * Adds the tickets data to the event Object
	 *
	 * @param array   $data
	 * @param array   $args
	 * @param WP_Post $post
	 *
	 * @return array
	 */
	public function add_ticket_data( $data, $args, $post ) {
		if ( ! tribe_events_has_tickets( $post->ID ) ) {
			return $data;
		}

		$tickets = Tribe__Tickets__Tickets::get_all_event_tickets( $post->ID );

		// Reset it
		$data->offers = array();

		foreach ( $tickets as $ticket ) {
			$data->offers[] = $this->get_offer( $ticket, $post );
		}

		return $data;
	}

	/**
	 * Builds an object representing a ticket offer.
	 *
	 * @param object  $ticket
	 * @param WP_Post $post
	 *
	 * @return object
	 */
	public function get_offer( $ticket, $post ) {
		$price = $ticket->price;
		// We use `the-events-calendar` domain to make sure it's translate-able the correct way
		$string_free = __( 'Free', 'the-events-calendar' );

		// JSON-LD can't have free as a price
		if ( strpos( strtolower( trim( $price ) ), $string_free ) !== false ) {
			$price = 0;
		}

		$offer = (object) array(
			'@type'         => 'Offer',
			'url'           => get_permalink( $post ),
			'price'         => $price,
			'category'      => 'primary',
			'availability'  => $this->get_ticket_availability( $ticket ),
			'priceCurrency' => $this->get_price_currency( $ticket ),
		);

		if ( ! empty( $ticket->start_date ) ) {
			$offer->validFrom = date( DateTime::ATOM, strtotime( $ticket->start_date ) );
		}

		if ( ! empty( $ticket->end_date ) ) {
			$offer->validThrough = date( DateTime::ATOM, strtotime( $ticket->end_date ) );
		}

		/**
		 * Allows modifications to be made to the offer object representing a specific
		 * event ticket.
		 *
		 * @param object                        $offer
		 * @param Tribe__Tickets__Ticket_Object $ticket
		 * @param object $post
		 */
		return (object) apply_filters( 'tribe_json_ld_offer_object', $offer, $ticket, $post );
	}

	/**
	 * Returns a string indicating current availability of the ticket.
	 *
	 * @param  object  $ticket
	 * @return string
	 */
	public function get_ticket_availability( $ticket ) {
		$stock = $ticket->stock();

		if ( 0 === $stock ) {
			return 'SoldOut';
		} elseif ( $stock >= 1 && $stock <= $this->low_stock ) {
			return 'LimitedAvailability';
		} else {
			return 'InStock';
		}
	}

	/**
	 * Return the price currency used on the Ticket
	 *
	 * @since 4.7.1
	 *
	 * @param $ticket
	 *
	 * @return mixed
	 */
	public function get_price_currency( $ticket ) {

		$currency = tribe_get_option( 'ticket-commerce-currency-code', 'USD' );

		if ( class_exists( $ticket->provider_class ) ) {
			$instance = call_user_func( array( $ticket->provider_class, 'get_instance' ) ) ;
			$currency = $instance->get_currency();
		}

		return $currency;
	}
}
