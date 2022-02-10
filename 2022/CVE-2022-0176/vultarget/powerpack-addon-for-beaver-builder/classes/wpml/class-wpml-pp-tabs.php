<?php

class WPML_PP_Tabs extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->items;
	}

	public function get_fields() {
		return array( 'label', 'content' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'label':
				return esc_html__( 'Tabs - Item Label', 'bb-powerpack-lite' );

			case 'content':
				return esc_html__( 'Tabs - Item Content', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'label':
				return 'LINE';

			case 'content':
				return 'VISUAL';

			default:
				return '';
		}
	}

}
