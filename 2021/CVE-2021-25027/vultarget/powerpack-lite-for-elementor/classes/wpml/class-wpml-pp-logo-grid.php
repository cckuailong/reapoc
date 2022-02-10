<?php

class WPML_PP_Logo_Grid extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'pp_logos';
	}

	public function get_fields() {
		return array( 
			'title',
			'link' => array( 'url' ),
	 );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'title':
				return esc_html__( 'Logo Carousel - Logo Title', 'powerpack' );
			case 'url':
				return esc_html__( 'Logo Carousel - Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
				return 'LINE';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}

}
