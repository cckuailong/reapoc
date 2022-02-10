<?php

class WPML_PP_Infolist extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->list_items;
	}

	public function get_fields() {
		return array( 'title', 'description', 'read_more_text', 'link' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'title':
				return esc_html__( 'InfoList - Title', 'bb-powerpack-lite' );

			case 'description':
				return esc_html__( 'InfoList - Description', 'bb-powerpack-lite' );

			case 'read_more_text':
				return esc_html__( 'InfoList - Button Text', 'bb-powerpack-lite' );

			case 'link':
				return esc_html__( 'InfoList - Link', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
			case 'read_more_text':
				return 'LINE';

			case 'description':
				return 'VISUAL';

			case 'link':
				return 'LINK';

			default:
				return '';
		}
	}

}
