<?php

class WPML_PP_Filterable_Gallery extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->gallery_filter;
	}

	public function get_fields() {
		return array( 'filter_label' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'filter_label':
				return esc_html__( 'Filterable Gallery - Filter Label', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'filter_label':
				return 'LINE';

			default:
				return '';
		}
	}

}
