<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * Override the forminator captcha message so users understand it doesn't work because recaptcha is blocked.
 *
 */

add_filter( 'forminator_invalid_captcha_message',
	'cmplz_forminator_override_recaptcha_error', 10, 4 );
function cmplz_forminator_override_recaptcha_error(
	$invalid_captcha_message, $element_id, $field, $verify
) {
	$invalid_captcha_message
		= __( "Please accept cookies so we can validate your request with reCaptcha, and submit this form",
		"complianz-gdpr" );

	return $invalid_captcha_message;
}

/**
 * Add forminator as formtype to the list
 *
 * @param $formtypes
 *
 * @return mixed
 */
function cmplz_forminator_form_types( $formtypes ) {
	$formtypes['fn_'] = 'forminator';

	return $formtypes;
}

add_filter( 'cmplz_form_types', 'cmplz_forminator_form_types' );

/**
 * Get list of Forminator forms
 *
 * @param array $input_forms
 *
 * @return array $input_forms
 */

function cmplz_forminator_get_plugin_forms( $input_forms ) {
	$forms = Forminator_API::get_forms();
	if ( is_array( $forms ) ) {
		$forms = wp_list_pluck( $forms, "name", "id" );
		foreach ( $forms as $id => $title ) {
			$input_forms[ 'fn_' . $id ] = $title . " " . '(Forminator)';
		}
	}

	return $input_forms;
}

add_filter( 'cmplz_get_forms', 'cmplz_forminator_get_plugin_forms' );

/**
 * Adds a consent box the a Forminator form
 *
 * @param $form_id
 */
function cmplz_forminator_add_consent_checkbox( $form_id ) {

	$form_id = str_replace( 'fn_', '', $form_id );
	$gdpr_field = Forminator_API::get_form_fields_by_type( $form_id,
		'gdprcheckbox' );
	if ( is_wp_error( $gdpr_field ) ) {
		$data = array(
			'condition_action' => 'show',
			'condition_rule'   => 'any',
			'type'             => 'gdprcheckbox',
			"required"         => true,
			'cols'             => '12',
			'validation'       => '',
			'gdpr_description' => sprintf( __( "Yes, I agree with the %sPrivacy Statement%s",
				"complianz-gdpr" ), '<a href="'
			                        . COMPLIANZ::$document->get_permalink( 'privacy-statement',
					'eu', true ) . '">', '</a>' ),
			'field_label'      => __( 'Privacy', "complianz-gdpr" ),
			'description'      => '',
			'validation_text'  => '',
			'custom-class'     => ''
		);

		Forminator_API::add_form_field( $form_id, 'gdprcheckbox', $data );
	}
}

add_action( "cmplz_add_consent_box_forminator",
	'cmplz_forminator_add_consent_checkbox' );


add_filter( 'cmplz_placeholder_markers', 'cmplz_forminator_placeholder' );
function cmplz_forminator_placeholder( $tags ) {
	$tags['google-recaptcha'][] = 'forminator-g-recaptcha';

	return $tags;
}


/**
 * Add some custom css for the placeholder
 */

add_action( 'wp_footer', 'cmplz_forminator_css' );
function cmplz_forminator_css() {
	?>
	<style>
		.cmplz-blocked-content-container.forminator-g-recaptcha {
			max-width: initial !important;
			height: 70px !important
		}

		@media only screen and (max-width: 400px) {
			.cmplz-blocked-content-container.forminator-g-recaptcha {
				height: 100px !important
			}
		}

		.cmplz-blocked-content-container.forminator-g-recaptcha .cmplz-blocked-content-notice {
			top: 2px
		}
	</style>
	<?php
}

;
