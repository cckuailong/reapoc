<?php

class WPML_PP_Testimonials extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->testimonials;
	}

	public function get_fields() {
		return array( 'title', 'subtitle', 'testimonial' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'title':
				return esc_html__( 'Testimonial - Client Name', 'bb-powerpack-lite' );

			case 'subtitle':
				return esc_html__( 'Testimonial - Client Profile', 'bb-powerpack-lite' );

			case 'testimonial':
				return esc_html__( 'Testimonial - Content', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
			case 'subtitle':
				return 'LINE';

			case 'testimonial':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
