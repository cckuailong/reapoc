<?php
/**
 * The Tickets abstraction objece, used to add tickets-related properties to the event object crated by the
 * `trib_get_event` function.
 *
 * @todo  @sc0ttkclark This model class needs to move into `src/Tribe` when Tickets model is implemented by Green Team
 *
 * @since   4.10.9
 *
 * @package Tribe\Tickets\Events\Views\V2\Models
 */

namespace Tribe\Tickets\Events\Views\V2\Models;

use Tribe\Utils\Lazy_Events;

/**
 * Class Tickets
 *
 * @since   4.10.9
 *
 * @package Tribe\Tickets\Events\Views\V2\Models
 */
class Tickets implements \ArrayAccess, \Serializable {
	use Lazy_Events;

	/**
	 * The post ID this tickets model is for.
	 *
	 * @since 4.10.9
	 *
	 * @var int
	 */
	protected $post_id;

	/**
	 * The tickets data.
	 *
	 * @since 4.10.9
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * A flag property indicating whether tickets for the post exists or not.
	 *
	 * @since 4.10.9
	 *
	 * @var bool
	 */
	protected $exists;

	/**
	 * An array of all the tickets for this event.
	 *
	 * @since 4.10.9
	 *
	 * @var array
	 */
	 protected $all_tickets;

	/**
	 * Tickets constructor.
	 *
	 * @param int $post_id The post ID.
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function __get( $property ) {
		if ( 'exist' === $property ) {
			return $this->exist();
		}

		return $this->offsetGet( $property );
	}

	/**
	 * {@inheritDoc}
	 */
	public function __set( $property, $value ) {
		throw new \InvalidArgumentException( "The `Tickets::{$property}` property cannot be set." );
	}

	/**
	 * {@inheritDoc}
	 */
	public function __isset( $property ) {
		return $this->offsetExists( $property );
	}

	/**
	 * Returns the data about the event tickets, if any.
	 *
	 * @since 4.10.9
	 *
	 * @return array An array of objects containing the post thumbnail data.
	 */
	public function fetch_data() {
		if ( ! $this->exist() ) {
			return [];
		}

		if ( null !== $this->data ) {
			return $this->data;
		}

		$num_ticket_types_available = 0;
		foreach ( $this->all_tickets as $ticket ) {
			if ( ! tribe_events_ticket_is_on_sale( $ticket ) ) {
				continue;
			}

			$num_ticket_types_available++;
		}

		if ( ! $num_ticket_types_available ) {
			return [];
		}

		// Get an array for ticket and rsvp counts.
		$types = \Tribe__Tickets__Tickets::get_ticket_counts( $this->post_id );

		// If no rsvp or tickets return.
		if ( ! $types ) {
			return [];
		}

		$html        = [];
		$parts       = [];
		$stock_html  = '';
		$sold_out    = '';
		$link_label  = '';
		$link_anchor = '';

		// If we have tickets or RSVP, but everything is Sold Out then display the Sold Out message.
		foreach ( $types as $type => $data ) {

			if ( ! $data['count'] ) {
				continue;
			}

			if ( ! $data['available'] ) {
				if ( 'rsvp' === $type ) {
					$parts[ $type . '_stock' ] = esc_html_x( 'Currently full', 'events rsvp full (v2)', 'event-tickets' );
				} else {
					$parts[ $type . '_stock' ] = esc_html_x( 'Sold Out', 'events stock sold out (v2)', 'event-tickets' );
				}

				// Only re-apply if we don't have a stock yet.
				if ( empty( $html['stock'] ) ) {
					$html['stock'] = $parts[ $type . '_stock' ];
					$sold_out      = $parts[ $type . '_stock' ];
				}
			} else {
				$stock = $data['stock'];
				if ( $data['unlimited'] || ! $data['stock'] ) {
					// If unlimited tickets, tickets with no stock and rsvp, or no tickets and rsvp unlimited - hide the remaining count.
					$stock = false;
				}

				if ( $stock ) {
					/** @var Tribe__Settings_Manager $settings_manager */
					$settings_manager = tribe( 'settings.manager' );

					$threshold = $settings_manager::get_option( 'ticket-display-tickets-left-threshold', 0 );

					/**
					 * Overwrites the threshold to display "# tickets left".
					 *
					 * @param int   $threshold Stock threshold to trigger display of "# tickets left"
					 * @param array $data      Ticket data.
					 * @param int   $event_id  Event ID.
					 *
					 * @since 4.10.1
					 */
					$threshold = absint( apply_filters( 'tribe_display_tickets_left_threshold', $threshold, $data, $this->post_id ) );

					if ( ! $threshold || $stock <= $threshold ) {

						$number = number_format_i18n( $stock );

						$ticket_label_singular = tribe_get_ticket_label_singular_lowercase( 'event-tickets' );
						$ticket_label_plural   = tribe_get_ticket_label_plural_lowercase( 'event-tickets' );

						if ( 'rsvp' === $type ) {
							/* translators: %1$s: Number of stock */
							$text = _n( '%1$s spot left', '%1$s spots left', $stock, 'event-tickets' );
						} else {
							/* translators: %1$s: Number of stock, %2$s: Ticket label, %3$s: Tickets label */
							$text = _n( '%1$s %2$s left', '%1$s %3$s left', $stock, 'event-tickets' );
						}

						$stock_html = esc_html( sprintf( $text, $number, $ticket_label_singular, $ticket_label_plural ) );
					}
				}

				$parts[ $type . '_stock' ] = $html['stock'] = $stock_html;

				if ( 'rsvp' === $type ) {
					/* Translators: RSVP singular label. */
					$link_label  = esc_html( sprintf( _x( '%s Now', 'list view rsvp now ticket button', 'event-tickets' ), tribe_get_rsvp_label_singular( 'list_view_rsvp_now_button' ) ) );
					$link_anchor = '#rsvp-now';
				} else {
					/* Translators: Tickets plural label. */
					$link_label  = esc_html( sprintf( _x( 'Get %s', 'list view buy now ticket button', 'event-tickets' ), tribe_get_ticket_label_plural( 'list_view_buy_now_button' ) ) );
					$link_anchor = '#tribe-tickets';
				}
			}
		}

		$this->data['link'] = (object) [
			'anchor' => get_permalink( $this->post_id ) . $link_anchor,
			'label'  => $link_label,
		];

		$this->data['stock'] = (object) [
			'available' => $stock_html,
			'sold_out'  => $sold_out,
		];

		return $this->data;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetExists( $offset ) {
		$this->data = $this->fetch_data();

		return isset( $this->data[ $offset ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetGet( $offset ) {
		$this->data = $this->fetch_data();

		return isset( $this->data[ $offset ] )
			? $this->data[ $offset ]
			: null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetSet( $offset, $value ) {
		$this->data = $this->fetch_data();

		$this->data[ $offset ] = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function offsetUnset( $offset ) {
		$this->data = $this->fetch_data();

		unset( $this->data[ $offset ] );
	}

	/**
	 * Returns an array representation of the event tickets data.
	 *
	 * @since 4.10.9
	 *
	 * @return array An array representation of the event tickets data.
	 */
	public function to_array() {
		$this->data = $this->fetch_data();

		return json_decode( json_encode( $this->data ), true );
	}

	/**
	 * {@inheritDoc}
	 */
	public function serialize() {
		$data            = $this->fetch_data();
		$data['post_id'] = $this->post_id;

		return serialize( $data );
	}

	/**
	 * {@inheritDoc}
	 */
	public function unserialize( $serialized ) {
		$data          = unserialize( $serialized );
		$this->post_id = $data['post_id'];
		unset( $data['post_id'] );
		$this->data = $data;
	}

	/**
	 * Returns whether an event has tickets at all or not.
	 *
	 * @since 4.10.9
	 *
	 * @return bool Whether an event has tickets at all or not.
	 */
	public function exist() {
		if ( null !== $this->exists ) {
			return $this->exists;
		}

		$this->all_tickets = \Tribe__Tickets__Tickets::get_all_event_tickets( $this->post_id );

		$this->exists = ! empty( $this->all_tickets );

		return $this->exists;
	}

	/**
	 * Returns whether an event has tickets in date range.
	 *
	 * @since 4.12.0
	 *
	 * @return bool Whether an event has tickets in date range
	 */
	public function in_date_range() {
		if ( ! $this->post_id ) {
			return false;
		}

		return tribe_tickets_is_current_time_in_date_window( $this->post_id );
	}

	/**
	 * Returns whether an event has its tickets sold out.
	 *
	 * @since 4.12.0
	 *
	 * @return bool Whether an event has its tickets sold out.
	 */
	public function sold_out() {
		$data = $this->fetch_data();

		return ! empty( $data['stock']->sold_out );
	}
}
