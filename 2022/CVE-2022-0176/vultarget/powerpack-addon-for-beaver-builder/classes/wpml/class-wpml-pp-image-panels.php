<?php

class WPML_PP_Image_Panels extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->image_panels;
	}

	public function get_fields() {
		return array( 'title', 'link' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'title':
				return esc_html__( 'Image Panel - Title', 'bb-powerpack-lite' );

			case 'link':
				return esc_html__( 'Image Panel - Link', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
				return 'LINE';

			case 'link':
				return 'LINK';

			default:
				return '';
		}
	}

}
