<?php

class WPML_PP_Price_Menu extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'menu_items';
	}

	public function get_fields() {
		return array( 
			'menu_title',
			'menu_description',
			'menu_price',
			'original_price',
			'link' => array( 'url' ),
	 );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'menu_title':
				return esc_html__( 'Price Menu - Menu Title', 'powerpack' );
			case 'menu_description':
				return esc_html__( 'Price Menu - Menu Description', 'powerpack' );
			case 'menu_price':
				return esc_html__( 'Price Menu - Menu Price', 'powerpack' );
			case 'original_price':
				return esc_html__( 'Price Menu - Original Price', 'powerpack' );
			case 'url':
				return esc_html__( 'Price Menu - Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'menu_title':
				return 'LINE';
			case 'menu_description':
				return 'AREA';
			case 'menu_price':
				return 'LINE';
			case 'original_price':
				return 'LINE';
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}

}
