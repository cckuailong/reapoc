<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

function cmplz_happyforms_initform() {
	if(!wp_script_is('jquery', 'done')) {
		wp_enqueue_script('jquery');
	}
	ob_start();
	?>
	<script>
		jQuery(document).ready(function ($) {
			$(document).on("cmplzRunAfterAllScripts", cmplzRunHappyFormsScript);
			function cmplzRunHappyFormsScript() {
				if ($('.happyforms-form').length) $('.happyforms-form').happyForm();
			}
		});
	</script>
	<?php
	$script = ob_get_clean();
	$script = str_replace(array('<script>', '</script>'), '', $script);
	wp_add_inline_script( 'jquery', $script );

}
add_action( 'wp_enqueue_scripts', 'cmplz_happyforms_initform' );


/**
 * Add happyforms as formtype to the list
 *
 * @param $formtypes
 *
 * @return mixed
 */
function cmplz_happyforms_form_types( $formtypes ) {
	$formtypes['hf_'] = 'happyforms';

	return $formtypes;
}

add_filter( 'cmplz_form_types', 'cmplz_happyforms_form_types' );

/**
 * Get list of happyforms forms
 *
 * @param array $input_forms
 *
 * @return array $input_forms
 */

function cmplz_happyforms_get_plugin_forms( $input_forms ) {

	$forms = get_posts( array(
		'post_type'   => 'happyform',
		'post_status' => 'publish',
		'numberposts' => - 1,
	) );
	$forms = wp_list_pluck( $forms, "post_title", "ID" );
	foreach ( $forms as $id => $title ) {
		$input_forms[ 'hf_' . $id ] = $title . " " . '(Happy Forms)';
	}

	return $input_forms;
}

add_filter( 'cmplz_get_forms', 'cmplz_happyforms_get_plugin_forms' );


/**
 * Adds a consent box the a happyforms form
 *
 * @param $form_id
 */
function cmplz_happyforms_add_consent_checkbox( $form_id ) {
	$form_id = str_replace( 'hf_', '', $form_id );

	if ( !file_exists(happyforms_get_include_folder() . '/core/classes/class-form-controller.php')) {
		return;
	}

	require_once( happyforms_get_include_folder()
	              . '/core/classes/class-form-controller.php' );
	require_once( happyforms_get_include_folder()
	              . '/core/classes/class-form-part-library.php' );
	require_once( happyforms_get_include_folder()
	              . '/core/classes/class-form-styles.php' );
	require_once( happyforms_get_include_folder()
	              . '/core/classes/class-session.php' );
	require_once( happyforms_get_include_folder()
	              . '/core/helpers/helper-form-templates.php' );
	require_once( happyforms_get_include_folder()
	              . '/core/helpers/helper-validation.php' );

	$form_controller = happyforms_get_form_controller();

	// Get the new form default data
	$form_data    = $form_controller->get( $form_id );
	$count        = count( $form_data['parts'] ) + 1;
	$has_checkbox = false;
	if ( is_array( $form_data['parts'] ) ) {
		foreach ( $form_data['parts'] as $part ) {
			if ( $part['type'] === 'legal' ) {
				$has_checkbox = true;
			}
		}
	}

	if ( ! $has_checkbox ) {
		$part_data            = array(
			'type'       => 'legal',
			'legal_text' => sprintf( __( "Yes, I agree with the %sprivacy statement%s",
				"complianz-gdpr" ), '<a href="'
			                        . COMPLIANZ::$document->get_permalink( 'privacy-statement',
					'eu', true ) . '">', '</a>' ),
			'width'      => 'full',
			'required'   => 1,
			'label'      => __( 'Privacy', "complianz-gdpr" ),
			'id'         => 'legal_' . $count,
		);
		$form_data['parts'][] = $part_data;
		$form_controller->update( $form_data );
	}
}

add_action( "cmplz_add_consent_box_happyforms",
	'cmplz_happyforms_add_consent_checkbox' );
