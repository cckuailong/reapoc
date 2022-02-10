<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
add_filter( 'cmplz_fields_load_types', 'cmplz_filter_field_types', 10, 1 );
function cmplz_filter_field_types( $fields ) {
	/**
	 * Add dynamic purposes
	 *
	 * */
	if ( cmplz_has_region( 'us' )
	     || ( cmplz_has_region( 'ca' )
	          && cmplz_get_value( 'privacy-statement' ) === 'generated' )
	     || ( cmplz_has_region( 'au' )
	          && cmplz_get_value( 'privacy-statement' ) === 'generated' )
	) {
		foreach ( COMPLIANZ::$config->purposes as $key => $label ) {

			if ( ! empty( COMPLIANZ::$config->details_per_purpose_us ) ) {
				$fields = $fields + array(
						$key . '_data_purpose_us' => array(
							'master_label' => __( "Purpose:", 'complianz-gdpr' ) . " " . $label,
							'step' => STEP_COMPANY,
							'section' => 8,
							'source' => 'wizard',
							'type' => 'multicheckbox',
							'default' => '',
							'label' => __( "What data do you collect for this purpose?", 'complianz-gdpr' ),
							'required' => true,
							'callback_condition' => array(
								'purpose_personaldata' => $key
							),
							'options' => COMPLIANZ::$config->details_per_purpose_us,
						),

					);

			}

		}

	}

	return $fields;

}

add_filter( 'cmplz_fields', 'cmplz_filter_fields', 10, 1 );
function cmplz_filter_fields( $fields ) {
	if (!cmplz_uses_preferences_cookies()) {
		unset( $fields['category_prefs'] );
	}

	return $fields;

}
