<?php

class WPML_PP_Business_Hours extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'business_hours_custom';
	}

	public function get_fields() {
		return array(
			'day',
			'time',
			'closed_text'
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'day':
				return esc_html__( 'Business Hours - Day', 'powerpack' );

			case 'time':
				return esc_html__( 'Business Hours - Time', 'powerpack' );

			case 'closed_text':
				return esc_html__( 'Business Hours - Closed Text', 'powerpack' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'day':
				return 'LINE';

			case 'time':
				return 'LINE';

			case 'closed_text':
				return 'LINE';

			default:
				return '';
		}
	}

}
