<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
function cmplz_wpforms_form_types( $formtypes ) {
	$formtypes['wpf_'] = 'wpforms';

	return $formtypes;
}

add_filter( 'cmplz_form_types', 'cmplz_wpforms_form_types' );
/**
 * Add WP forms to the forms array
 *
 * @param array $input_forms
 *
 * @return array
 */
function cmplz_wpforms_get_plugin_forms( $input_forms ) {
	$forms = wpforms()->form->get();

	if ( is_array( $forms ) ) {
		$forms = wp_list_pluck( $forms, "post_title", "ID" );
		foreach ( $forms as $id => $title ) {
			$input_forms[ 'wpf_' . $id ] = $title . " " . __( '(WP Forms)', 'complianz-gdpr' );
		}
	}

	return $input_forms;
}

add_filter( 'cmplz_get_forms', 'cmplz_wpforms_get_plugin_forms', 10, 1 );


/**
 * Conditionally add the dependency from the CF 7 inline script to the .js file
 */

add_filter( 'cmplz_dependencies', 'cmplz_wpforms_dependencies' );
function cmplz_wpforms_dependencies( $tags ) {
	if (cmplz_get_value('block_recaptcha_service') === 'yes'){
		$site_key   = wpforms_setting( 'recaptcha-site-key', '' );
		$secret_key = wpforms_setting( 'recaptcha-secret-key', '' );

		if ( ! empty( $site_key ) && ! empty( $secret_key ) ) {
			$tags['recaptcha/api.js'] = 'grecaptcha';
		}
	}
	return $tags;
}

function cmplz_wpforms_add_consent_checkbox( $form_id ) {
	$form_id = str_replace( 'wpf_', '', $form_id );

	$form = wpforms()->form->get( $form_id, array(
		'content_only' => true,
	) );
	//enable GDPR settings
	$wpforms_settings         = get_option( 'wpforms_settings', array() );
	$wpforms_settings['gdpr'] = true;
	update_option( 'wpforms_settings', $wpforms_settings );
	$label
		= sprintf( __( 'To submit this form, you need to accept our %sPrivacy Statement%s',
		'complianz-gdpr' ),
		'<a href="' . COMPLIANZ::$document->get_permalink( 'privacy-statement',
			'eu', true ) . '">', '</a>' );

	if ( ! wpforms_has_field_type( 'gdpr-checkbox', $form ) ) {
		$field_id = wpforms()->form->next_field_id( $form_id );

		$fields         = $form['fields'];
		$fields[]       = array(
			'id'       => $field_id,
			'type'     => 'gdpr-checkbox',
			'required' => 1,
			'choices'  => array(
				array(
					'label' => $label,
					'value' => '',
					'image' => '',
				),
			),
		);
		$form['fields'] = $fields;
		wpforms()->form->update( $form_id, $form );

	}
}

add_action( "cmplz_add_consent_box_wpforms", 'cmplz_wpforms_add_consent_checkbox' );


add_action( 'wp_footer', 'cmplz_wpforms_css' );
function cmplz_wpforms_css() {
	?>
	<style>
		.wpforms-recaptcha-container {
			position: relative !important;
		}

		div.wpforms-container-full .wpforms-form .cmplz-accept-marketing {
			background: grey;
		}
	</style>
	<?php
}
;
