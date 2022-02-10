<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * Set analytics as suggested stats tool in the wizard
 */

add_filter( 'cmplz_default_value', 'cmplz_monsterinsights_set_default', 20, 2 );
function cmplz_monsterinsights_set_default( $value, $fieldname ) {
	if ( $fieldname == 'compile_statistics' ) {
		return "google-analytics";
	}

	return $value;
}
/**
 * Add conditional classes to the monsterinsights statistics script
 *
 * */

function cmplz_monsterinsights_add_monsterinsights_attributes( $attr ) {
	$classes       = COMPLIANZ::$cookie_admin->get_statistics_script_classes();
	$attr['class'] = implode( ' ', $classes );
	return $attr;
}
add_filter( 'monsterinsights_tracking_analytics_script_attributes', 'cmplz_monsterinsights_add_monsterinsights_attributes', 10, 1 );

/**
 * Block all premium scripts as well
 *
 * */
function cmplz_monsterinsights_script( $tags ) {
	$tags[] = 'monsterinsights_scroll_tracking_load';
	$tags[] = 'google-analytics-premium/pro/assets/';
	$tags[] = 'mi_version';
	return $tags;
}
add_filter( 'cmplz_known_script_tags', 'cmplz_monsterinsights_script' );

/**
 * Remove stuff which is not necessary anymore
 *
 * */

function cmplz_monsterinsights_remove_actions() {
	remove_action( 'cmplz_notice_compile_statistics',
		'cmplz_show_compile_statistics_notice', 10 );
}

add_action( 'init', 'cmplz_monsterinsights_remove_actions' );

/**
 * Add notice to tell a user to choose Analytics
 *
 * @param $args
 */
function cmplz_monsterinsights_show_compile_statistics_notice( $args ) {
	cmplz_sidebar_notice( sprintf( __( "You use %s, which means the answer to this question should be Google Analytics.", 'complianz-gdpr' ), 'Monsterinsights' ) );
}

add_action( 'cmplz_notice_compile_statistics', 'cmplz_monsterinsights_show_compile_statistics_notice', 10, 1 );



function cmplz_monsterinsights_compile_statistics_notice() {
	if ( cmplz_no_ip_addresses() ) {
		cmplz_sidebar_notice( __( "You have selected you anonymize IP addresses. This setting is now enabled in MonsterInsights.", 'complianz-gdpr' ) );
	}

	if ( cmplz_statistics_no_sharing_allowed() ) {
		cmplz_sidebar_notice( __( "You have selected you do not share data with third-party networks. Demographics is now disabled in MonsterInsights.", 'complianz-gdpr' ) );
	}
}

add_action( 'cmplz_notice_compile_statistics_more_info',
	'cmplz_monsterinsights_compile_statistics_notice' );

/**
 * We remove some actions to integrate fully
 * */
function cmplz_monsterinsights_remove_scripts_others() {
	remove_action( 'wp_head', 'monsterinsights_tracking_script', 6 );
	remove_action( 'cmplz_statistics_script',
		array( COMPLIANZ::$cookie_admin, 'get_statistics_script' ), 10 );
}

add_action( 'after_setup_theme',
	'cmplz_monsterinsights_remove_scripts_others' );

/**
 * Execute the monsterinsights script at the right point
 */
add_action( 'cmplz_before_statistics_script', 'monsterinsights_tracking_script', 10, 1 );


/**
 * Hide the stats configuration options when monsterinsights is enabled.
 *
 * @param $fields
 *
 * @return mixed
 */

function cmplz_monsterinsights_filter_fields( $fields ) {
	unset( $fields['configuration_by_complianz'] );
	unset( $fields['UA_code'] );
	return $fields;
}

add_filter( 'cmplz_fields', 'cmplz_monsterinsights_filter_fields' );

/**
 * Make sure there's no warning about configuring GA anymore
 *
 * @param $warnings
 *
 * @return mixed
 */

function cmplz_monsterinsights_filter_warnings( $warnings ) {
	unset( $warnings[ 'ga-needs-configuring' ] );
	return $warnings;
}

add_filter( 'cmplz_warning_types', 'cmplz_monsterinsights_filter_warnings' );

/**
 * Make sure Monsterinsights returns true for anonymize IP's when this option is selected in the wizard
 *
 * @param $value
 * @param $key
 * @param $default
 *
 * @return bool
 */
function cmplz_monsterinsights_force_anonymize_ips( $value, $key, $default ) {
	if ( cmplz_no_ip_addresses() ) {
		return true;
	}

	return $value;
}

add_filter( 'monsterinsights_get_option_anonymize_ips', 'cmplz_monsterinsights_force_anonymize_ips', 30, 3 );

/**
 * Make sure Monsterinsights returns false for third party sharing when this option is selected in the wizard
 *
 * @param $value
 * @param $key
 * @param $default
 *
 * @return bool
 */
function cmplz_monsterinsights_force_demographics( $value, $key, $default ) {

	if ( cmplz_statistics_no_sharing_allowed() ) {
		return false;
	}

	return $value;
}

add_filter( 'monsterinsights_get_option_demographics',
	'cmplz_monsterinsights_force_demographics', 30, 3 );
