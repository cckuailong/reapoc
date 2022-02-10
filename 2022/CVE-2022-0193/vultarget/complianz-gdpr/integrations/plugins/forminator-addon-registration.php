<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
add_action( 'forminator_addons_loaded', 'cmplz_load_forminator_addon' );
function cmplz_load_forminator_addon() {
	require_once dirname( __FILE__ ) . '/forminator-addon-class.php';

	if ( class_exists( 'Forminator_Addon_Loader' ) ) {
		Forminator_Addon_Loader::get_instance()
		                       ->register( 'CMPLZ_Forminator_Addon' );
	}
}
