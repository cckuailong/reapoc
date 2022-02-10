<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	if( !function_exists( 'woo_ce_get_export_type_subscription_count' ) ) {
		function woo_ce_get_export_type_subscription_count( $count = 0, $export_type = '', $args ) {

			if( $export_type <> 'subscription' )
				return $count;

			$count = 0;
			// Check that WooCommerce Subscriptions exists
			if( class_exists( 'WC_Subscriptions' ) ) {
				$count = woo_ce_get_subscription_count();
			}

			return $count;

		}
		add_filter( 'woo_ce_get_export_type_count', 'woo_ce_get_export_type_subscription_count', 10, 3 );
	}

	function woo_ce_get_subscription_count() {

		$count = 0;
		// Check if the existing Transient exists
		$cached = get_transient( WOO_CE_PREFIX . '_subscription_count' );
		if( $cached == false ) {
			// Allow store owners to force the Subscription count
			$count = apply_filters( 'woo_ce_get_subscription_count', $count );
			if( $count == 0 ) {
				$wcs_version = woo_ce_get_wc_subscriptions_version();
				if( version_compare( $wcs_version, '2.0.1', '<' ) ) {
					if( method_exists( 'WC_Subscriptions', 'is_large_site' ) ) {
						// Does this store have roughly more than 3000 Subscriptions
						if( false === WC_Subscriptions::is_large_site() ) {
							if( class_exists( 'WC_Subscriptions_Manager' ) ) {
								// Check that the get_all_users_subscriptions() function exists
								if( method_exists( 'WC_Subscriptions_Manager', 'get_all_users_subscriptions' ) ) {
									if( $subscriptions = WC_Subscriptions_Manager::get_all_users_subscriptions() ) {
										if( version_compare( $wcs_version, '2.0.1', '<' ) ) {
											foreach( $subscriptions as $key => $user_subscription ) {
												if( !empty( $user_subscription ) ) {
													foreach( $user_subscription as $subscription )
														$count++;
												}
											}
											unset( $subscriptions, $subscription, $user_subscription );
										}
									}
								}
							}
						} else {
							if( method_exists( 'WC_Subscriptions', 'get_total_subscription_count' ) )
								$count = WC_Subscriptions::get_total_subscription_count();
							else
								$count = "~2500";
						}
					} else {
						if( method_exists( 'WC_Subscriptions', 'get_subscription_count' ) )
							$count = WC_Subscriptions::get_subscription_count();
					}
				} else {
					if( function_exists( 'wcs_get_subscriptions' ) ) {
						$args = array(
							'subscriptions_per_page' => -1,
							'subscription_status' => 'trash'
						);
						$count += count( wcs_get_subscriptions( $args ) );
						$args['subscription_status'] = 'any';
						$count += count( wcs_get_subscriptions( $args ) );
					}
				}
			}
			set_transient( WOO_CE_PREFIX . '_subscription_count', $count, HOUR_IN_SECONDS );
		} else {
			$count = $cached;
		}
		return $count;

	}

	/* End of: WordPress Administration */

}

// Returns a list of Subscription export columns
function woo_ce_get_subscription_fields( $format = 'full' ) {

	$export_type = 'subscription';

	$fields = array();
	$fields[] = array(
		'name' => 'subscription_id',
		'label' => __( 'Subscription ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_id',
		'label' => __( 'Order ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'status',
		'label' => __( 'Subscription Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'recurring',
		'label' => __( 'Recurring', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user',
		'label' => __( 'User', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_id',
		'label' => __( 'User ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_status',
		'label' => __( 'Order Status', 'woocommerce-exporter' )
	);
	// Check if this is a pre-WooCommerce 2.2 instance
	$woocommerce_version = woo_get_woo_version();
	if( version_compare( $woocommerce_version, '2.2', '<' ) ) {
		$fields[] = array(
			'name' => 'post_status',
			'label' => __( 'Post Status', 'woocommerce-exporter' )
		);
	}
	$fields[] = array(
		'name' => 'transaction_id',
		'label' => __( 'Transaction ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'start_date',
		'label' => __( 'Start Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'end_date',
		'label' => __( 'End Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'trial_end_date',
		'label' => __( 'Trial End Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'last_payment',
		'label' => __( 'Last Payment', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'next_payment',
		'label' => __( 'Next Payment', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'payment_method',
		'label' => __( 'Payment Method', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_total',
		'label' => __( 'Order Total', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_subtotal',
		'label' => __( 'Order Subtotal', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'recurring_total',
		'label' => __( 'Recurring Total', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_method_id',
		'label' => __( 'Shipping Method ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_method',
		'label' => __( 'Shipping Method', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_cost',
		'label' => __( 'Shipping Cost', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sign_up_fee',
		'label' => __( 'Sign-up Fee', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'trial_length',
		'label' => __( 'Trial Length', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'trial_period',
		'label' => __( 'Trial Period', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'coupon',
		'label' => __( 'Coupon Code', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'related_orders',
		'label' => __( 'Related Orders', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_full_name',
		'label' => __( 'Billing: Full Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_first_name',
		'label' => __( 'Billing: First Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_last_name',
		'label' => __( 'Billing: Last Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_company',
		'label' => __( 'Billing: Company', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_address',
		'label' => __( 'Billing: Street Address (Full)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_address_1',
		'label' => __( 'Billing: Street Address 1', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_address_2',
		'label' => __( 'Billing: Street Address 2', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_city',
		'label' => __( 'Billing: City', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_postcode',
		'label' => __( 'Billing: ZIP Code', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_state',
		'label' => __( 'Billing: State (prefix)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_state_full',
		'label' => __( 'Billing: State', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_country',
		'label' => __( 'Billing: Country (prefix)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_country_full',
		'label' => __( 'Billing: Country', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_phone',
		'label' => __( 'Billing: Phone Number', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_email',
		'label' => __( 'Billing: E-mail Address', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_full_name',
		'label' => __( 'Shipping: Full Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_first_name',
		'label' => __( 'Shipping: First Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_last_name',
		'label' => __( 'Shipping: Last Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_company',
		'label' => __( 'Shipping: Company', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_address',
		'label' => __( 'Shipping: Street Address (Full)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_address_1',
		'label' => __( 'Shipping: Street Address 1', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_address_2',
		'label' => __( 'Shipping: Street Address 2', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_city',
		'label' => __( 'Shipping: City', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_postcode',
		'label' => __( 'Shipping: ZIP Code', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_state',
		'label' => __( 'Shipping: State (prefix)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_state_full',
		'label' => __( 'Shipping: State', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_country',
		'label' => __( 'Shipping: Country (prefix)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_country_full',
		'label' => __( 'Shipping: Country', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_product_id',
		'label' => __( 'Subscription Items: Product ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_variation_id',
		'label' => __( 'Subscription Items: Variation ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_sku',
		'label' => __( 'Subscription Items: Product SKU', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_name',
		'label' => __( 'Subscription Items: Product Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_variation',
		'label' => __( 'Subscription Items: Product Variation', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_quantity',
		'label' => __( 'Subscription Items: Quantity', 'woocommerce-exporter' )
	);

/*
	$fields[] = array(
		'name' => '',
		'label' => __( '', 'woocommerce-exporter' )
	);
*/

	// Allow Plugin/Theme authors to add support for additional columns
	$fields = apply_filters( 'woo_ce_' . $export_type . '_fields', $fields, $export_type );

	switch( $format ) {

		case 'summary':
			$output = array();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( isset( $fields[$i] ) )
					$output[$fields[$i]['name']] = 'on';
			}
			return $output;
			break;

		case 'full':
		default:
			$sorting = woo_ce_get_option( $export_type . '_sorting', array() );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				$fields[$i]['reset'] = $i;
				$fields[$i]['order'] = ( isset( $sorting[$fields[$i]['name']] ) ? $sorting[$fields[$i]['name']] : $i );
			}
			// Check if we are using PHP 5.3 and above
			if( version_compare( phpversion(), '5.3' ) >= 0 )
				usort( $fields, woo_ce_sort_fields( 'order' ) );
			return $fields;
			break;

	}

}

function woo_ce_get_subscription_statuses() {

	$subscription_statuses = array(
		'active'    => __( 'Active', 'woocommerce-subscriptions' ),
		'cancelled' => __( 'Cancelled', 'woocommerce-subscriptions' ),
		'expired'   => __( 'Expired', 'woocommerce-subscriptions' ),
		'pending'   => __( 'Pending', 'woocommerce-subscriptions' ),
		'failed'   => __( 'Failed', 'woocommerce-subscriptions' ),
		'on-hold'   => __( 'On-hold', 'woocommerce-subscriptions' ),
		'trash'     => __( 'Deleted', 'woocommerce-exporter' ),
	);

	return apply_filters( 'woo_ce_subscription_statuses', $subscription_statuses );

}

function woo_ce_get_wc_subscriptions_version() {

	if( class_exists( 'WC_Subscriptions' ) ) {
		return WC_Subscriptions::$version;
	}

}

function woo_ce_get_subscription_products() {

	$term_taxonomy = 'product_type';
	$args = array(
		'post_type' => array( 'product', 'product_variation' ),
		'posts_per_page' => -1,
		'fields' => 'ids',
		'suppress_filters' => false,
		'tax_query' => array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'slug',
				'terms' => array( 'subscription', 'variable-subscription' )
			)
		)
	);
	$products = array();
	$product_ids = new WP_Query( $args );
	if( $product_ids->posts ) {
		foreach( $product_ids->posts as $product_id )
			$products[] = $product_id;
	}

	return $products;

}