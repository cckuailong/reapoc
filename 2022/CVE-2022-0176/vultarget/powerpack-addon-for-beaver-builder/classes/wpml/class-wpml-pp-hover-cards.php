<?php

class WPML_PP_Hover_Cards extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->card_content;
	}

	public function get_fields() {
		return array( 'title', 'hover_content', 'button_text', 'button_link' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'title':
                return esc_html__( 'Hover Cards - Title', 'bb-powerpack-lite' );
                
            case 'hover_content':
                return esc_html__( 'Hover Cards - Content', 'bb-powerpack-lite' );

            case 'button_text':
                return esc_html__( 'Hover Cards - Button Text', 'bb-powerpack-lite' );

			case 'button_link':
				return esc_html__( 'Hover Cards - Link', 'bb-powerpack-lite' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'title':
                return 'LINE';
                
            case 'hover_content':
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
