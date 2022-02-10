<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
/**
 * Add notice to tell a user to choose Analytics
 *
 * @param $args
 */

function cmplz_google_site_kit_show_compile_statistics_notice( $args ) {
	cmplz_sidebar_notice(
		sprintf( __( "Because you're using %s, you can choose which plugin should insert the relevant snippet.", 'complianz-gdpr' ), "Google Site Kit" )
		. cmplz_read_more( "https://complianz.io/configuring-google-site-kit/" ),
	'warning' );
}

add_action( 'cmplz_notice_compile_statistics',
	'cmplz_google_site_kit_show_compile_statistics_notice', 10, 1 );
