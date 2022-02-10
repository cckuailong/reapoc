<?php

class WPML_PP_Image_Accordion extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'accordion_items';
	}

	public function get_fields() {
		return array( 
			'title',
			'description',
			'button_text',
			'link' => array( 'url' ),
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'title':
				return esc_html__( 'Image Accordion - Item Title', 'powerpack' );
			case 'description':
				return esc_html__( 'Image Accordion - Item Description', 'powerpack' );
			case 'button_text':
				return esc_html__( 'Image Accordion - Button Text', 'powerpack' );
			case 'url':
				return esc_html__( 'Image Accordion - Button Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
				return 'LINE';
			case 'description':
				return 'LINE';
			case 'button_text':
				return 'LINE';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}

}
