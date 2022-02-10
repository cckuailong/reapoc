<?php

class WPML_PP_Icon_List extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->list_items;
	}

	public function get_fields() {
		return array( 'list_items' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'list_items':
				return esc_html__( 'Icon List - Item', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'list_items':
				return 'LINE';

			default:
				return '';
		}
	}

}
