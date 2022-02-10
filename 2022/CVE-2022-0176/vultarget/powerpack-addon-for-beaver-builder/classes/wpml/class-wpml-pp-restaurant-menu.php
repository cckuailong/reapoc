<?php

class WPML_PP_Restaurant_Menu extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->menu_items;
	}

	public function get_fields() {
		return array( 'menu_items_title', 'menu_items_link', 'menu_item_description', 'menu_items_price', 'menu_items_unit' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'menu_items_title':
				return esc_html__( 'Restaurant / Services Menu - Title', 'bb-powerpack-lite' );

			case 'menu_items_link':
				return esc_html__( 'Restaurant / Services Menu - Link To', 'bb-powerpack-lite' );

			case 'menu_item_description':
				return esc_html__( 'Restaurant / Services Menu - Item Description', 'bb-powerpack-lite' );

			case 'menu_items_price':
				return esc_html__( 'Restaurant / Services Menu - Price', 'bb-powerpack-lite' );

			case 'menu_items_unit':
				return esc_html__( 'Restaurant / Services Menu - Unit', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'menu_items_title':
			case 'menu_items_link':
			case 'menu_items_price':
			case 'menu_items_unit':
				return 'LINE';

			case 'menu_item_description':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
