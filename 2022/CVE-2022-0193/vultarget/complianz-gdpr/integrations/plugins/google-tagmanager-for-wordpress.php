<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * Set analytics as suggested stats tool in the wizard
 */
add_filter( 'cmplz_default_value', 'cmplz_gtm4wp_set_default', 20, 2 );
function cmplz_gtm4wp_set_default( $value, $fieldname ) {
	if ( $fieldname == 'compile_statistics' ) {
		return "google-tag-manager";
	}

	return $value;
}

/**
 * Remove stats
 *
 * */

function cmplz_gtm4wp_remove_actions() {
	remove_action( 'cmplz_notice_compile_statistics',
		'cmplz_show_compile_statistics_notice', 10 );
}

add_action( 'init', 'cmplz_gtm4wp_remove_actions' );

//function cmplz_edit_known_script_tags($tags){
//	if (($key = array_search('gtm.js', $tags)) !== false) {
//		unset($tags[$key]);
//	}
//	return $tags;
//}
//add_filter('cmplz_known_script_tags', 'cmplz_edit_known_script_tags');
//

/**
 * We remove some actions to integrate fully
 * */
function cmplz_gtm4wp_remove_scripts_statistics() {
	remove_action( 'cmplz_statistics_script',
		array( COMPLIANZ::$cookie_admin, 'get_statistics_script' ), 10 );
}

add_action( 'after_setup_theme', 'cmplz_gtm4wp_remove_scripts_statistics' );

/**
 * Add notice to tell a user to choose Analytics
 *
 * @param $args
 */
function cmplz_gtm4wp_show_compile_statistics_notice( $args ) {
	cmplz_sidebar_notice( sprintf( __( "You use %s, which means the answer to this question should be Google Tag Manager.",
		'complianz-gdpr' ), 'Google Tag Manager for WordPress' ) );
}

add_action( 'cmplz_notice_compile_statistics',
	'cmplz_gtm4wp_show_compile_statistics_notice', 10, 1 );


add_action( 'admin_init', 'cmplz_gtm4wp_options' );
function cmplz_gtm4wp_options() {
	$storedoptions = (array) get_option( GTM4WP_OPTIONS );
	$save          = false;

	if ( isset( $storedoptions[ GTM4WP_OPTION_INCLUDE_VISITOR_IP ] ) ) {
		if ( cmplz_no_ip_addresses()
		     && $storedoptions[ GTM4WP_OPTION_INCLUDE_VISITOR_IP ]
		) {
			$storedoptions[ GTM4WP_OPTION_INCLUDE_VISITOR_IP ] = false;
			$save                                              = true;
		} elseif ( ! cmplz_no_ip_addresses()
		           && ! ! $storedoptions[ GTM4WP_OPTION_INCLUDE_VISITOR_IP ]
		) {
			$save                                              = true;
			$storedoptions[ GTM4WP_OPTION_INCLUDE_VISITOR_IP ] = true;
		}
	}

	//handle sharing of data
	if ( isset( $storedoptions[ GTM4WP_OPTION_INCLUDE_REMARKETING ] ) ) {
		if ( cmplz_statistics_no_sharing_allowed()
		     && $storedoptions[ GTM4WP_OPTION_INCLUDE_REMARKETING ]
		) {
			$save                                               = true;
			$storedoptions[ GTM4WP_OPTION_INCLUDE_REMARKETING ] = false;

		} elseif ( ! cmplz_statistics_no_sharing_allowed()
		           && ! $storedoptions[ GTM4WP_OPTION_INCLUDE_REMARKETING ]
		) {
			$save                                               = true;
			$storedoptions[ GTM4WP_OPTION_INCLUDE_REMARKETING ] = true;
		}
	}

	if ( $save ) {
		update_option( GTM4WP_OPTIONS, $storedoptions );
	}
}

/**
 * Make sure there's no warning about configuring GA anymore
 *
 * @param $warnings
 *
 * @return mixed
 */

function cmplz_gtm4wp_filter_warnings( $warnings ) {
	unset($warnings['gtm-needs-configuring']);
	return $warnings;
}

add_filter( 'cmplz_warning_types', 'cmplz_gtm4wp_filter_warnings' );

/**
 * Hide the stats configuration options when gtm4wp is enabled.
 *
 * @param $fields
 *
 * @return mixed
 */

function cmplz_gtm4wp_filter_fields( $fields ) {
	unset( $fields['configuration_by_complianz'] );
	unset( $fields['GTM_code'] );
	return $fields;
}

add_filter( 'cmplz_fields', 'cmplz_gtm4wp_filter_fields', 20, 1 );


/**
 * Tell the user the consequences of choices made
 */
function cmplz_gtm4wp_compile_statistics_more_info_notice() {
	if ( cmplz_no_ip_addresses() ) {
		cmplz_sidebar_notice( sprintf( __( "You have selected you anonymize IP addresses. This setting is now enabled in %s.",
			'complianz-gdpr' ), 'Google Tag Manager for WordPress' ) );
	}
	if ( cmplz_statistics_no_sharing_allowed() ) {
		cmplz_sidebar_notice( sprintf( __( "You have selected you do not share data with third-party networks. Remarketing is now disabled in %s.",
			'complianz-gdpr' ), 'Google Tag Manager for WordPress' ) );
	}
}

add_action( 'cmplz_notice_compile_statistics_more_info',
	'cmplz_gtm4wp_compile_statistics_more_info_notice' );
