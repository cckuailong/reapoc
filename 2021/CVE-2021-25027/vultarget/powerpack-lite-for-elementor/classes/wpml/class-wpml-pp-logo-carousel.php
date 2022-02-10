<?php

class WPML_PP_Logo_Carousel extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'carousel_slides';
	}

	public function get_fields() {
		return array( 
			'logo_title',
			'link' => array( 'url' ),
	 );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'logo_title':
				return esc_html__( 'Logo Carousel - Logo Title', 'powerpack' );
			case 'url':
				return esc_html__( 'Logo Carousel - Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'logo_title':
				return 'LINE';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}

}
