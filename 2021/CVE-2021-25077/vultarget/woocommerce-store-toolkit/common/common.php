<?php
/**
*
* Filename: common.php
* Description: common.php loads commonly accessed functions across the Visser Labs suite.
* 
* Free
* - woo_get_action
* - woo_get_woo_version
*
*/

if( is_admin() ) {

	/* Start of: WordPress Administration */

	// Load Dashboard widgets
	include_once( WOO_ST_PATH . 'includes/common-dashboard_widgets.php' );

	/* End of: WordPress Administration */

}

if( !function_exists( 'woo_get_action' ) ) {
	function woo_get_action( $prefer_get = false ) {

		if ( isset( $_GET['action'] ) && $prefer_get )
			return sanitize_text_field( $_GET['action'] );

		if ( isset( $_POST['action'] ) )
			return sanitize_text_field( $_POST['action'] );

		if ( isset( $_GET['action'] ) )
			return sanitize_text_field( $_GET['action'] );

		return false;

	}
}

if( !function_exists( 'woo_get_woo_version' ) ) {
	function woo_get_woo_version() {

		$version = false;
		if( defined( 'WC_VERSION' ) ) {
			$version = WC_VERSION;
		 // Backwards compatibility
		} else if( defined( 'WOOCOMMERCE_VERSION' ) ) {
			$version = WOOCOMMERCE_VERSION;
		}
		return $version;
	
	}
}
?>