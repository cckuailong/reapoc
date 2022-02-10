<?php
namespace PowerpackElementsLite\Modules\QueryControl\Types;

use PowerpackElementsLite\Modules\QueryControl\Types\Type_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Types\Posts
 *
 * @since  1.2.9
 */
class Posts extends Type_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.2.9
	 * @return string
	 */
	public function get_name() {
		return 'posts';
	}

	/**
	 * Gets autocomplete values
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function get_autocomplete_values( array $data ) {
		$results = [];

		$query_params = [
			'post_type' 		=> $data['object_type'],
			's' 				=> $data['q'],
			'posts_per_page' 	=> -1,
		];

		if ( 'attachment' === $query_params['post_type'] ) {
			$query_params['post_status'] = 'inherit';
		}

		$query = new \WP_Query( $query_params );

		foreach ( $query->posts as $post ) {
			$results[] = [
				'id' 	=> $post->ID,
				'text' 	=> $post->post_title,
			];
		}

		return $results;
	}

	/**
	 * Gets control values titles
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function get_value_titles( array $request ) {
		$ids = (array) $request['id'];
		$results = [];

		$query = new \WP_Query( [
			'post_type' 		=> 'any',
			'post__in' 			=> $ids,
			'posts_per_page' 	=> -1,
		] );

		foreach ( $query->posts as $post ) {
			$results[ $post->ID ] = $post->post_title;
		}

		return $results;
	}
}
