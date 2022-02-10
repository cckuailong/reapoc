<?php
namespace PowerpackElementsLite\Modules\QueryControl\Types;

use PowerpackElementsLite\Modules\QueryControl\Types\Type_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Types\Terms
 *
 * @since  1.2.9
 */
class Terms extends Type_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.2.9
	 * @return string
	 */
	public function get_name() {
		return 'terms';
	}

	/**
	 * Gets autocomplete values
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function get_autocomplete_values( array $data ) {
		$results = [];

		$taxonomies = get_object_taxonomies('');

		$query_params = [
			'taxonomy' 		=> $taxonomies,
			'search' 		=> $data['q'],
			'hide_empty' 	=> false,
		];

		$terms = get_terms( $query_params );

		foreach ( $terms as $term ) {
			$taxonomy = get_taxonomy( $term->taxonomy );

			$results[] = [
				'id' 	=> $term->term_id,
				'text' 	=> $taxonomy->labels->singular_name . ': ' . $term->name,
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

		$query_params = [
			'include' 		=> $ids,
		];

		$terms = get_terms( $query_params );

		foreach ( $terms as $term ) {
			$results[ $term->term_id ] = $term->name;
		}

		return $results;
	}
}
