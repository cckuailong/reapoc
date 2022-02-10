<?php
namespace PowerpackElementsLite\Modules\QueryControl\Types;

use PowerpackElementsLite\Modules\QueryControl\Types\Meta_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * \Modules\QueryControl\Types\Toolset
 *
 * @since  1.2.9
 */
class Toolset extends Meta_Base {

	/**
	 * Get Name
	 * 
	 * Get the name of the module
	 *
	 * @since  1.2.9
	 * @return string
	 */
	public function get_name() {
		return 'toolset';
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
		return __( 'Toolset', 'powerpack' );
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

		$toolset_groups = wpcf_admin_fields_get_groups();

		foreach ( $toolset_groups as $group ) {
			$fields = wpcf_admin_fields_get_fields_by_group( $group['id'] );

			if ( ! is_array( $fields ) ) {
				continue;
			}

			foreach ( $fields as $field_key => $field ) {
				if ( strpos( strtolower( $field['name'] ), strtolower( $data['q'] ) ) === false || strpos( strtolower( $field['slug'] ), strtolower( $data['q'] ) ) ) {
					continue;
				}

				if ( ! is_array( $field ) || empty( $field['type'] ) ) {
					continue;
				}

				if ( ! $this->is_valid_field_type( $options['field_type'], $field['type'] ) ) {
					continue;
				}

				$display 			= $field['name'];
				$display_type 		= ( $options['show_type'] ) ? $this->get_title() : '';
				$display_field_type = ( $options['show_field_type'] ) ? $field['slug'] : '';
				$display 			= ( $options['show_type'] || $options['show_field_type'] ) ? ': ' . $display : $display;

				$results[] = [
					'id' 	=> $group['slug'] . ':' . $field['slug'],
					'text' 	=> sprintf( '%1$s %2$s %3$s', $display_type, $display_field_type, $display ),
				];
			}
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
		$keys 		= (array)$request['id'];
		$results 	= [];
		$options 	= $request['query_options'];

		foreach ( $keys as $key ) {
			list( $field_group, $field_key ) = explode( ':', $key );

			$field = wpcf_admin_fields_get_field( $field_key );

			if ( ! is_array( $field ) || empty( $field['type'] ) ) {
				continue;
			}

			if ( ! $this->is_valid_field_type( $options['field_type'], $field['type'] ) ) {
				continue;
			}

			$display 			= $field['name'];
			$display_type 		= ( $options['show_type'] ) ? $this->get_title() : '';
			$display_field_type = ( $options['show_field_type'] ) ? $field['slug'] : '';
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
				'date',
			],
		];
	}
}
