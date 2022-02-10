<?php
/**
 * Queue to handle Attendees Block REST migration.
 *
 * @since 4.12.0
 */

namespace Tribe\Tickets\Migration;

use Tribe\Tickets\Events\Attendees_List;
use Tribe__Tickets__Main;
use WP_Query;

/**
 * Class Queue_4_12
 *
 * @package Tribe\Tickets\Migration
 */
class Queue_4_12 extends Queue {

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.12.0
	 *
	 * @var string
	 */
	public $queue_id = '4_12';

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.12.0
	 *
	 * @param WP_Post|int $post The post object or post ID, depending on what $this->get_query() is set to return.
	 *
	 * @return bool Whether the post was processed.
	 */
	public function process_post( $post ) {
		$has_attendee_list_shortcode = has_shortcode( $post->post_content, 'tribe_attendees_list' );
		$has_attendee_list_block     = function_exists( 'has_block' ) ? has_block( 'tribe/attendees', $post ) : false;

		$has_attendee_list = $has_attendee_list_shortcode || $has_attendee_list_block;

		update_post_meta( $post->ID, Attendees_List::HIDE_META_KEY, (int) $has_attendee_list );

		return true;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 4.12.0
	 *
	 * @param int $current_offset The current offset being used.
	 *
	 * @return int[]|WP_Post[] List of post IDs or post objects.
	 */
	public function get_posts( $current_offset ) {
		$supported_post_types = Tribe__Tickets__Main::instance()->post_types();

		if ( empty( $supported_post_types ) ) {
			return [];
		}

		$hide_meta_key = Attendees_List::HIDE_META_KEY;

		$searches = [
			'[tribe_attendees_list',
			'<!-- wp:tribe/attendees ',
		];

		$args = [
			'post_type'      => $supported_post_types,
			'post_status'    => 'any',
			'posts_per_page' => $this->batch_size,
			'offset'         => $current_offset,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'meta_query'     => [
				'relation' => 'OR',
				[
					'key'     => $hide_meta_key,
					'compare' => 'NOT EXISTS',
				],
				[
					'key'   => $hide_meta_key,
					'value' => '',
				],
			],
			'sentence'       => true,
		];

		$posts = [];

		// This gets set in the loop.
		/** @var WP_Query $query */

		// Search posts that have a block or shortcode.
		foreach ( $searches as $search ) {
			$args['s'] = $search;

			$query = new WP_Query( $args );

			$posts[] = $query->posts;
		}

		// Return a full list of posts found across all searches.
		return array_merge( [], ...$posts );
	}
}
