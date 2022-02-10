<?php

class WPML_PP_Timeline extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->timeline;
	}

	public function get_fields() {
		return array( 'title', 'content', 'button_text', 'button_link' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'title':
                return esc_html__( 'Timeline - Title', 'bb-powerpack-lite' );

            case 'content':
                return esc_html__( 'Timeline - Content', 'bb-powerpack-lite' );

            case 'button_text':
                return esc_html__( 'Timeline - Button Text', 'bb-powerpack-lite' );

			case 'button_link':
				return esc_html__( 'Timeline - Link', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
                return 'LINE';

            case 'content':
                return 'VISUAL';

            case 'button_text':
                return 'LINE';

			case 'button_link':
				return 'LINK';

			default:
				return '';
		}
	}

}
