<?php
/**
 * REST API plugin class
 *
 * @since             1.18.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}


/**
 * REST API plugin class
 */
class TInvWL_Includes_API_Frontend {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wishlist';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'v1';

	/**
	 * Register the routes for wishlist.
	 */
	public function register_routes() {

		register_rest_route( $this->namespace . '/' . $this->rest_base, '/products',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'get_wishlist_products_data' ),
					'args'                => $this->get_wishlist_products_params(),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	/**
	 * @return array
	 */
	public function get_wishlist_products_params() {
		$params        = array();
		$params['ids'] = array(
			'description'   => __( 'Limit result set to specific ids.', 'ti-woocommerce-wishlist' ),
			'type'          => 'array',
			'items'         => array(
				'type' => 'integer',
			),
			'default'       => array(),
			'show_in_index' => true,
		);

		$params['counter'] = array(
			'description' => __( 'Return wishlist products counter.', 'ti-woocommerce-wishlist' ),
			'type'        => 'bool',
			'default'     => false,
		);

		return $params;
	}

	/**
	 *  Get wishlist products data.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return WP_REST_Response
	 */
	public function get_wishlist_products_data( $request ) {

		$data = array();

		$ids = $request['ids'];

		if ( $ids ) {
			$wishlist_data = array();

			$add_class = new TInvWL_Public_AddToWishlist( TINVWL_PREFIX );

			$args     = array(
				'include' => $ids,
				'limit'   => count( $ids ),
			);
			$products = wc_get_products( $args );

			foreach ( $products as $product ) {
				$wishlist_data[ $product->get_id() ] = $add_class->user_wishlist( $product );
			}
			$data['products'] = $wishlist_data;
		}

		$counter = $request['counter'];

		if ( $counter ) {
			$data['counter'] = TInvWL_Public_WishlistCounter::counter();
		}

		return rest_ensure_response( $data );

	}
}
