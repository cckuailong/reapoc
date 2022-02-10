<?php

class WPML_PP_Advanced_Accordion extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'tabs';
	}

	public function get_fields() {
		return array( 
			'tab_title',
			'accordion_content',
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'tab_title':
				return esc_html__( 'Advanced Accordion - Item Title', 'powerpack' );
			case 'accordion_content':
				return esc_html__( 'Advanced Accordion - Item Content', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'tab_title':
				return 'LINE';
			case 'accordion_content':
				return 'VISUAL';
			default:
				return '';
		}
	}

}
