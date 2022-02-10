<?php

class WPML_PP_Logos_Grid extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->logos_grid;
	}

	public function get_fields() {
		return array( 'upload_logo_title', 'upload_logo_link' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'upload_logo_title':
                return esc_html__( 'Logos Grid - Item Title', 'bb-powerpack-lite' );

			case 'upload_logo_link':
				return esc_html__( 'Logos Grid - Item Link', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'upload_logo_title':
                return 'LINE';
                
			case 'upload_logo_link':
				return 'LINK';

			default:
				return '';
		}
	}

}
