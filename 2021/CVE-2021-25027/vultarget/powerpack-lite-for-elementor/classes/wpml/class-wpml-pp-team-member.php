<?php

class WPML_PP_Team_Member extends WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'team_member_social';
	}

	public function get_fields() {
		return array( 
			'social_link' => array( 'url' )
	 );
	}

	protected function get_title( $field ) {
		switch( $field ) {
			case 'url':
				return esc_html__( 'Team Member - Social Link', 'powerpack' );
			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch( $field ) {
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}

}
