<?php

class WPML_PP_Business_Hours extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->business_hours_rows;
	}

	public function get_fields() {
		return array( 'status_text' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'status_text':
				return esc_html__( 'Business Hours - Status Text', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'status_text':
				return 'LINE';

			default:
				return '';
		}
	}

}
