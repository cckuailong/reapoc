<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

function cmplz_consent_box_required_on_form() {
	$contact = cmplz_forms_used_on_sites();
	$permission_needed = ( cmplz_get_value( 'contact_processing_data_lawfull' )
	                       === '1' ) ? true : false;

	return ( $contact && $permission_needed );
}

function cmplz_forms_used_on_sites() {
	$purpose = cmplz_get_value( 'purpose_personaldata' );
	if ( isset( $purpose['contact'] ) && $purpose['contact'] == 1 ) {
		return true;
	}

	return false;
}

function cmplz_site_uses_contact_forms() {
	if ( get_option( 'cmplz_detected_forms' )
	     && is_array( get_option( 'cmplz_detected_forms' ) )
	     && count( get_option( 'cmplz_detected_forms' ) ) > 0
	) {
		return true;
	}

	return false;
}


/**
 * Do stuff after a page from the wizard is saved.
 *
 * */

function cmplz_forms_maybe_add_consent_checkbox() {
	//preload form options. Otherwise we could get conflicts with custom form fields
	$preload_forms = apply_filters( 'cmplz_get_forms', array() );
	update_option( 'cmplz_detected_forms', $preload_forms );

	$forms = cmplz_get_value( 'add_consent_to_forms' );
	if ( ! $forms || ! is_array( $forms ) ) {
		return;
	}

	$forms = array_filter( $forms, function ( $el ) {
		return ( $el == 1 );
	} );
	foreach ( $forms as $form_id => $checked ) {
		$type = cmplz_get_form_type( $form_id );
		do_action( "cmplz_add_consent_box_$type", $form_id );
	}
}

add_action( 'cmplz_wizard_wizard', 'cmplz_forms_maybe_add_consent_checkbox', 10, 1 );


/**
 * Get the type of a saved form
 *
 * @param $form_id
 *
 * @return bool|int;
 */

function cmplz_get_form_type( $form_id ) {
	$form_types = apply_filters( 'cmplz_form_types', array() );
	foreach ( $form_types as $key => $type ) {
		if ( strpos( $form_id, $key ) !== false ) {
			return $type;
		}
	}

	return false;

}


