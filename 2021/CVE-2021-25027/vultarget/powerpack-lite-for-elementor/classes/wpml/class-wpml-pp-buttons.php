<?php

class WPML_PP_Buttons extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'buttons';
	}

	public function get_fields() {
		return array( 
			'text',
			'icon_text',
			'tooltip_content',
			'link' => array( 'url' ),
		);
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'text':
				return esc_html__( 'Buttons - Button Text', 'powerpack' );
			case 'icon_text':
				return esc_html__( 'Buttons - Button Icon Text', 'powerpack' );
			case 'tooltip_content':
				return esc_html__( 'Buttons - Button Tooltip Content', 'powerpack' );
			case 'url':
				return esc_html__( 'Buttons - Button Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'text':
				return 'LINE';
			case 'icon_text':
				return 'LINE';
			case 'tooltip_content':
				return 'AREA';
			case 'url':
				return 'LINE';
			default:
				return '';
		}
	}

}
