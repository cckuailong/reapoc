<?php
namespace PowerpackElementsLite\Modules\QueryControl\Types;

use PowerpackElementsLite\Modules\QueryControl\Types\Meta_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Types\Pods
 *
 * @since  1.2.9
 */
class Pods extends Meta_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.2.9
	 * @return string
	 */
	public function get_name() {
		return 'pods';
	}

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.2.9
	 * @return string
	 */
	public function get_title() {
		return __( 'Pods', 'powerpack' );
	}

	/**
	 * Gets autocomplete values
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function get_autocomplete_values( array $data ) {
		$results 	= [];
		$options 	= $data['query_options'];

		$query_params = [
			'post_type' 		=> '_pods_field',
			'post_status'		=> 'publish',
			'search_title_name' => $data['q'],
			'posts_per_page' 	=> -1,
		];

		add_filter( 'posts_where', 'pp_posts_where_by_title_name', 10, 2 );

		$query = new \WP_Query( $query_params );

		foreach ( $query->posts as $field_post ) {
			$pod 	= get_post( $field_post->post_parent );
			$field 	= pods_api()->load_field( [
				'pod' 			=> $pod->post_name,
				'pod_id'		=> $pod->ID,
				'name' 			=> $field_post->post_name,
				'id' 			=> $field_post->ID,
				'table_info' 	=> false,
			] );

			if ( ! is_array( $field ) || empty( $field['type'] ) ) {
				continue;
			}

			if ( ! $this->is_valid_field_type( $options['field_type'], $field['type'] ) ) {
				continue;
			}

			$display 			= $field['label'];
			$display_type 		= ( $options['show_type'] ) ? $this->get_title() : '';
			$display_field_type = ( $options['show_field_type'] ) ? $field['type'] : '';
			$display 			= ( $options['show_type'] || $options['show_field_type'] ) ? ': ' . $display : $display;

			$results[] = [
				'id' 	=> $pod->post_name . ':' . $pod->ID . ':' . $field['name'] . ':' . $field['id'],
				'text' 	=> sprintf( '%1$s %2$s %3$s', $display_type, $display_field_type, $display ),
			];
		}

		remove_filter( 'posts_where', 'pp_posts_where_by_title_name', 10, 2 );

		return $results;
	}

	/**
	 * Gets control values titles
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function get_value_titles( array $request ) {
		$keys 		= (array)$request['id'];
		$results 	= [];
		$options 	= $request['query_options'];

		foreach ( $keys as $key ) {
			list( $pod_name, $pod_id, $field_name, $field_id ) = explode( ':', $key );

			$field = pods_api()->load_field( [
				'pod' 			=> $pod_name,
				'pod_id'		=> $pod_id,
				'name' 			=> $field_name,
				'id' 			=> $field_id,
				'table_info' 	=> false,
			] );

			if ( ! is_array( $field ) || empty( $field['type'] ) ) {
				continue;
			}

			if ( ! $this->is_valid_field_type( $options['field_type'], $field['type'] ) ) {
				continue;
			}

			$display 			= $field['label'];
			$display_type 		= ( $options['show_type'] ) ? $this->get_title() : '';
			$display_field_type = ( $options['show_field_type'] ) ? $field['type'] : '';
			$display 			= ( $options['show_type'] || $options['show_field_type'] ) ? ': ' . $display : $display;
			$results[ $key ] 	= sprintf( '%1$s %2$s %3$s', $display_type, $display_field_type, $display );
		}

		return $results;
	}

	/**
	 * Returns array of pods field types organized
	 * by category
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function get_field_types() {
		return [
			'date' => [
				'datetime',
				'date',
			],
		];
	}
}
