<?php
/**
 * Models an Tickets Commerce Ticket.
 *
 * @since    5.1.9
 *
 * @package  TEC\Tickets\Commerce\Models
 */

namespace TEC\Tickets\Commerce\Models;

use DateInterval;
use DatePeriod;
use DateTimeZone;
use Tribe\Models\Post_Types\Base;
use TEC\Tickets\Commerce\Ticket;
use Tribe\Utils\Lazy_Collection;
use Tribe\Utils\Lazy_String;
use Tribe\Utils\Post_Thumbnail;
use Tribe__Date_Utils as Dates;

/**
 * Class Attendee.
 *
 * @since    5.1.9
 *
 * @package  TEC\Tickets\Commerce\Models
 */
class Ticket_Model extends Base {
	/**
	 * {@inheritDoc}
	 */
	protected function build_properties( $filter ) {
		try {
			$cache_this = $this->get_caching_callback( $filter );

			$post_id = $this->post->ID;

			$post_meta = get_post_meta( $post_id );

//			$total_value = isset( $post_meta[ Ticket::$total_value_meta_key ][0] ) ? $post_meta[ Ticket::$total_value_meta_key ][0] : null;

			$properties = [

			];
		} catch ( \Exception $e ) {
			return [];
		}

		return $properties;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_cache_slug() {
		return 'tc_tickets';
	}
}
