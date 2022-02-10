<?php
/**
 * REST API plugin class
 *
 * @since             1.13.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * REST API plugin class
 */
class TInvWL_API {
	public static function init() {
		add_filter( 'woocommerce_api_classes', __CLASS__ . '::includes' );

		add_action( 'rest_api_init', __CLASS__ . '::register_routes', 15 );
	}

	/**
	 * Include the required files for the REST API and add register the wishlist
	 * API class in the WC_API_Server.
	 *
	 * @param Array $wc_api_classes WC_API::registered_resources list of api_classes
	 *
	 * @return array
	 */
	public static function includes( $wc_api_classes ) {

		if ( ! defined( 'WC_API_REQUEST_VERSION' ) || 3 == WC_API_REQUEST_VERSION ) {
			array_push( $wc_api_classes, 'TInvWL_Includes_API_Wishlist' );
		}

		return $wc_api_classes;
	}

	/**
	 * Load the new REST API wishlist endpoints
	 *
	 */
	public static function register_routes() {
		global $wp_version;

		$controller = new TInvWL_Includes_API_Frontend();
		$controller->register_routes();

		if ( version_compare( $wp_version, 4.4, '<' ) || ( ! defined( 'WC_VERSION' ) || version_compare( WC_VERSION, '2.6', '<' ) ) ) {
			return;
		}
		$controller = new TInvWL_Includes_API_Wishlist();
		$controller->register_routes();
	}
}
