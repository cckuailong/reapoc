<?php

class WPML_PP_Info_List extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'list_items';
	}

	public function get_fields() {
		return array( 
			'text',
			'description',
			'icon_text',
			'link' => array( 'url' ),
			'button_text',
	 );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'text':
				return esc_html__( 'Info List - Title', 'powerpack' );
			case 'description':
				return esc_html__( 'Info List - Description', 'powerpack' );
			case 'icon_text':
				return esc_html__( 'Info List - Icon Text', 'powerpack' );
			case 'url':
				return esc_html__( 'Info List - Link', 'powerpack' );
			case 'button_text':
				return esc_html__( 'Info List - Button Text', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'text':
				return 'LINE';
			case 'description':
				return 'AREA';
			case 'icon_text':
				return 'LINE';
			case 'url':
				return 'LINK';
			case 'button_text':
				return 'LINE';
			default:
				return '';
		}
	}

}
