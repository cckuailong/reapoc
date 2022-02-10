<?php

namespace TEC\Tickets\Commerce\Repositories;

use TEC\Tickets\Commerce;
use TEC\Tickets\Commerce\Module;
use \Tribe__Repository;
use TEC\Tickets\Commerce\Ticket;
use Tribe__Repository__Usage_Error as Usage_Error;

use Tribe__Utils__Array as Arr;
use Tribe__Date_Utils as Dates;

/**
 * Class Tickets Repository.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Repositories
 */
class Tickets_Repository extends Tribe__Repository {
	/**
	 * The unique fragment that will be used to identify this repository filters.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $filter_name = 'tc_tickets';

	/**
	 * Key name to use when limiting lists of keys.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $key_name = \TEC\Tickets\Commerce::ABBR;

	/**
	 * {@inheritdoc}
	 */
	public function __construct() {
		parent::__construct();

		// Set the order post type.
		$this->default_args['post_type']   = Ticket::POSTTYPE;
		$this->default_args['post_status'] = 'publish';
		$this->create_args['post_status']  = 'publish';
		$this->create_args['post_type']    = Ticket::POSTTYPE;

		// Add event specific aliases.
		$this->update_fields_aliases = array_merge(
			$this->update_fields_aliases,
			[
				'event'            => Ticket::$event_relation_meta_key,
				'show_description' => Ticket::$show_description_meta_key,
				'price'            => Ticket::$price_meta_key,
			]
		);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function format_item( $id ) {
		$formatted = null === $this->formatter
			? tec_tc_get_ticket( $id )
			: $this->formatter->format_item( $id );

		/**
		 * Filters a single formatted ticket result.
		 *
		 * @since 5.1.9
		 *
		 * @param mixed|\WP_Post                $formatted The formatted event result, usually a post object.
		 * @param int                           $id        The formatted post ID.
		 * @param \Tribe__Repository__Interface $this      The current repository object.
		 */
		$formatted = apply_filters( 'tec_tickets_commerce_repository_ticket_format', $formatted, $id, $this );

		return $formatted;
	}

	/**
	 * {@inheritdoc}
	 */
	public function filter_postarr_for_create( array $postarr ) {
		if ( isset( $postarr['meta_input'] ) ) {
			$postarr = $this->filter_meta_input( $postarr );
		}

		return parent::filter_postarr_for_create( $postarr );
	}

	/**
	 * {@inheritdoc}
	 */
	public function filter_postarr_for_update( array $postarr, $post_id ) {
		if ( isset( $postarr['meta_input'] ) ) {
			$postarr = $this->filter_meta_input( $postarr, $post_id );
		}

		return parent::filter_postarr_for_update( $postarr, $post_id );
	}

	/**
	 * Filters and updates the order meta to make sure it makes sense.
	 *
	 * @since 5.1.9
	 *
	 * @param array $postarr The update post array, passed entirely for context purposes.
	 * @param int   $post_id The ID of the event that's being updated.
	 *
	 * @return array The filtered postarr array.
	 */
	protected function filter_meta_input( array $postarr, $post_id = null ) {
//		if ( ! empty( $postarr['meta_input']['purchaser'] ) ) {
//			$postarr = $this->filter_purchaser_input( $postarr, $post_id );
//		}

		return $postarr;
	}
}