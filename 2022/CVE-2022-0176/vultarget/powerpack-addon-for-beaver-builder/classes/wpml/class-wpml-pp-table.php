<?php

class WPML_PP_Table extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->rows;
	}

	public function get_fields() {
		return array( 'header', 'label', 'cell' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'header':
				return esc_html__( 'Table - Header', 'bb-powerpack-lite' );

			case 'label':
				return esc_html__( 'Table - Row Label', 'bb-powerpack-lite' );

			case 'cell':
				return esc_html__( 'Table - Cell', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'header':
			case 'label':
			case 'cell':
				return 'LINE';

			default:
				return '';
		}
	}

}
