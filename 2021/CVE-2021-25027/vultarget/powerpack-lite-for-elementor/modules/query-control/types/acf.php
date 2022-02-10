<?php
namespace PowerpackElementsLite\Modules\QueryControl\Types;

use PowerpackElementsLite\Modules\QueryControl\Types\Meta_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Types\Acf
 *
 * @since  1.2.9
 */
class Acf extends Meta_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.2.9
	 * @return string
	 */
	public function get_name() {
		return 'acf';
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
		return __( 'ACF', 'powerpack' );
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
			'post_type' 		=> 'acf-field',
			'post_status'		=> 'publish',
			'search_title_name' => $data['q'],
			'posts_per_page' 	=> -1,
		];

		add_filter( 'posts_where', 'pp_posts_where_by_title_name', 10, 2 );

		$query = new \WP_Query( $query_params );

		foreach ( $query->posts as $post ) {

			$field_settings 	= unserialize( $post->post_content );
			$field_type 		= $field_settings['type'];

			if ( ! $this->is_valid_field_type( $options['field_type'], $field_type ) ) {
				continue;
			}

			$display 			= $post->post_title;
			$display_type 		= ( $options['show_type'] ) ? $this->get_title() : '';
			$display_field_type = ( $options['show_field_type'] ) ? $this->get_acf_field_type_label( $field_type ) : '';
			$display 			= ( $options['show_type'] || $options['show_field_type'] ) ? ': ' . $display : $display;

			$results[] = [
				'id' 	=> $post->post_name,
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
		$keys 		= (array) $request['id'];
		$results 	= [];
		$options 	= $request['query_options'];

		$query = new \WP_Query( [
			'post_type' 		=> 'acf-field',
			'post_name__in' 	=> $keys,
			'posts_per_page' 	=> -1,
		] );

		foreach ( $query->posts as $post ) {
			$field_settings 	= unserialize( $post->post_content );
			$field_type 		= $field_settings['type'];
			$display 			= $post->post_title;
			$display_type 		= ( $options['show_type'] ) ? $this->get_title() : '';
			$display_field_type = ( $options['show_field_type'] ) ? $this->get_acf_field_type_label( $field_type ) : '';
			$display 			= ( $options['show_type'] || $options['show_field_type'] ) ? ': ' . $display : $display;

			$results[ $post->post_name ] = sprintf( '%1$s %2$s %3$s', $display_type, $display_field_type, $display );
		}

		return $results;
	}

	/**
	 * Gets the acf control type label by field type
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function get_acf_field_type_label( $field_type ) {
		if ( ! function_exists( 'acf_get_field_type' ) )
			return;

		$field_type_object = acf_get_field_type( $field_type );

		if ( $field_type_object )
			return $field_type_object->label;

		return false;
	}

	/**
	 * Returns array of acf field types organized
	 * by category
	 *
	 * @since  1.2.9
	 * @return array
	 */
	public function get_field_types() {
		return [
			'textual' => [
				'text',
				'textarea',
				'number',
				'range',
				'email',
				'url',
				'password',
			],
			'date' => [
				'date_picker',
				'date_time_picker',
			],
			'option' => [
				'select',
				'checkbox',
				'radio',
			],
			'boolean' => [
				'true_false',
			],
			'post' => [
				'post_object',
				'relationship',
			],
			'taxonomy' => [
				'taxonomy',
			],
		];
	}
}
