<?php

/**
 * Class Tribe__Tickets__Attendee_Registration__Meta
 *
 * @since 4.9
 */
class Tribe__Tickets__Attendee_Registration__Meta {

	/**
	 * Add a product-deletion parameter to the shopping URL on Paypal in order to clear old products
	 * if the order is cancelled within Paypal.
	 *
	 * @since 4.9
	 *
	 * @param array $args PayPal Add To Cart URL arguments.
	 *
	 * @filter tribe_tickets_commerce_paypal_add_to_cart_args 10 1
	 *
	 * @return array
	 */
	public function add_product_delete_to_paypal_url( array $args ) {
		$args['shopping_url'] = add_query_arg( [ 'clear_product_cache' => true ], $args['shopping_url'] );

		return $args;
	}
}
