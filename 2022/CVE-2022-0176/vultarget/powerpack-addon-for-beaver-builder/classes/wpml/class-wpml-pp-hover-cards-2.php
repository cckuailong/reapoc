<?php

class WPML_PP_Hover_Cards_2 extends WPML_Beaver_Builder_Module_With_Items {

	public function &get_items( $settings ) {
		return $settings->card_content;
	}

	public function get_fields() {
		return array( 'title', 'hover_content', 'box_link' );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'title':
                return esc_html__( 'Hover Cards 2 - Title', 'bb-powerpack-lite' );
                
            case 'hover_content':
                return esc_html__( 'Hover Cards 2 - Content', 'bb-powerpack-lite' );

			case 'box_link':
				return esc_html__( 'Hover Cards 2 - Link', 'bb-powerpack-lite' );

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

			case 'box_link':
				return 'LINK';

			default:
				return '';
		}
	}

}
