<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * Set analytics as suggested stats tool in the wizard
 */
add_filter( 'cmplz_default_value', 'cmplz_beehive_set_default', 20, 2 );
function cmplz_beehive_set_default( $value, $fieldname ) {
	if ( $fieldname == 'compile_statistics' ) {
		return "google-analytics";
	}

	return $value;
}

/**
 * Remove notice
 *
 * */

function cmplz_beehive_remove_actions() {
	remove_action( 'cmplz_notice_compile_statistics',
		'cmplz_show_compile_statistics_notice', 10 );
}

add_action( 'init', 'cmplz_beehive_remove_actions' );


/**
 * We remove some actions to integrate fully
 * */
function cmplz_beehive_remove_scripts_others() {
	remove_action( 'cmplz_statistics_script',
		array( COMPLIANZ::$cookie_admin, 'get_statistics_script' ), 10 );
}

add_action( 'after_setup_theme', 'cmplz_beehive_remove_scripts_others' );

/**
 * Add notice to tell a user to choose Analytics
 *
 * @param $args
 */
function cmplz_beehive_show_compile_statistics_notice( $args ) {
	cmplz_sidebar_notice( sprintf( __( "You use %s, which means the answer to this question should be Google Analytics.",
		'complianz-gdpr' ), 'Beehive' ) );
}

add_action( 'cmplz_notice_compile_statistics',
	'cmplz_beehive_show_compile_statistics_notice', 10, 1 );


add_filter( 'beehive_get_options', 'cmplz_beehive_options', 10, 2 );
function cmplz_beehive_options( $options, $network ) {
	//handle anonymization
	if ( cmplz_no_ip_addresses() ) {
		$options['general']['anonymize'] = true;
	}

	//handle sharing of data
	if ( cmplz_statistics_no_sharing_allowed() ) {
		$options['general']['advertising'] = false;
	}

	return $options;
}

/**
 * Make sure there's no warning about configuring GA anymore
 *
 * @param $warnings
 *
 * @return mixed
 */

function cmplz_beehive_filter_warnings( $warnings ) {
	unset( $warnings[ 'ga-needs-configuring' ] );
	return $warnings;
}

add_filter( 'cmplz_warning_types', 'cmplz_beehive_filter_warnings' );

/**
 * Hide the stats configuration options when Beehive is enabled.
 *
 * @param $fields
 *
 * @return mixed
 */

function cmplz_beehive_filter_fields( $fields ) {
	unset( $fields['configuration_by_complianz'] );
	unset( $fields['UA_code'] );
	return $fields;
}

add_filter( 'cmplz_fields', 'cmplz_beehive_filter_fields', 20, 1 );

/**
 * Tell the user the consequences of choices made
 */
function cmplz_beehive_compile_statistics_more_info_notice() {
	if ( cmplz_no_ip_addresses() ) {
		cmplz_sidebar_notice( __( "You have selected you anonymize IP addresses. This setting is now enabled in Beehive.",
			'complianz-gdpr' ) );
	}
	if ( cmplz_statistics_no_sharing_allowed() ) {
		cmplz_sidebar_notice( __( "You have selected you do not share data with third-party networks. Display advertising is now disabled in Beehive.",
			'complianz-gdpr' ) );
	}
}

add_action( 'cmplz_notice_compile_statistics_more_info',
	'cmplz_beehive_compile_statistics_more_info_notice' );
