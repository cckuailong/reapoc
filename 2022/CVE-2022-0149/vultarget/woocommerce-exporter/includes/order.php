<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	if( !function_exists( 'woo_ce_get_export_type_order_count' ) ) {
		function woo_ce_get_export_type_order_count() {

			$count = 0;
			$post_type = 'shop_order';

			$woocommerce_version = woo_get_woo_version();
			// Check if this is a WooCommerce 2.2+ instance (new Post Status)
			if( version_compare( $woocommerce_version, '2.2' ) >= 0 )
				$post_status = ( function_exists( 'wc_get_order_statuses' ) ? apply_filters( 'woo_ce_order_post_status', array_keys( wc_get_order_statuses() ) ) : 'any' );
			else
				$post_status = apply_filters( 'woo_ce_order_post_status', woo_ce_post_statuses() );
			// Check if the existing Transient exists
			$cached = get_transient( WOO_CE_PREFIX . '_order_count' );
			if( $cached == false ) {
				$args = array(
					'post_type' => $post_type,
					'posts_per_page' => 1,
					'post_status' => $post_status,
					'fields' => 'ids'
				);
				$count_query = new WP_Query( $args );
				$count = $count_query->found_posts;
				set_transient( WOO_CE_PREFIX . '_order_count', $count, HOUR_IN_SECONDS );
			} else {
				$count = $cached;
			}
			return $count;

		}
	}

	function woo_ce_order_dataset_args( $args, $export_type = '' ) {

		// Check if we're dealing with the Order Export Type
		if( $export_type <> 'order' )
			return $args;

		// Merge in the form data for this dataset
		$defaults = array(
			'order_dates_filter' => ( isset( $_POST['order_dates_filter'] ) ? sanitize_text_field( $_POST['order_dates_filter'] ) : false )
		);
		$args = wp_parse_args( $args, $defaults );

		// Save dataset export specific options
		if( $args['order_dates_filter'] <> woo_ce_get_option( 'order_dates_filter' ) )
			woo_ce_update_option( 'order_dates_filter', $args['order_dates_filter'] );

		return $args;

	}
	add_filter( 'woo_ce_extend_dataset_args', 'woo_ce_order_dataset_args', 10, 2 );

	/* End of: WordPress Administration */

}

// Returns a list of Order export columns
function woo_ce_get_order_fields( $format = 'full', $post_ID = 0 ) {

	$export_type = 'order';

	$fields = array();
	$fields[] = array(
		'name' => 'purchase_id',
		'label' => __( 'Order ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_id',
		'label' => __( 'Post ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_total',
		'label' => __( 'Order Total', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_subtotal',
		'label' => __( 'Order Subtotal', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_currency',
		'label' => __( 'Order Currency', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_discount',
		'label' => __( 'Order Discount', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'coupon_code',
		'label' => __( 'Coupon Code', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'coupon_expiry_date',
		'label' => __( 'Coupon Expiry Date', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'coupon_description',
		'label' => __( 'Coupon Description', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'purchase_total_tax',
		'label' => __( 'Order Total Tax', 'woocommerce-exporter' ),
		'disabled' => 1
	);
/*
	$fields[] = array(
		'name' => 'order_incl_tax',
		'label' => __( 'Order Incl. Tax', 'woocommerce-exporter' )
	);
*/
	$fields[] = array(
		'name' => 'order_subtotal_excl_tax',
		'label' => __( 'Order Subtotal Excl. Tax', 'woocommerce-exporter' ),
		'disabled' => 1
	);
/*
	$fields[] = array(
		'name' => 'order_tax_rate',
		'label' => __( 'Order Tax Rate', 'woocommerce-exporter' )
	);
*/
	$fields[] = array(
		'name' => 'order_sales_tax',
		'label' => __( 'Sales Tax Total', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_shipping_tax',
		'label' => __( 'Shipping Tax Total', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_incl_tax',
		'label' => __( 'Shipping Incl. Tax', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_excl_tax',
		'label' => __( 'Shipping Excl. Tax', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'refund_total',
		'label' => __( 'Refund Total', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'refund_date',
		'label' => __( 'Refund Date', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_tax_percentage',
		'label' => __( 'Order Tax Percentage', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'payment_gateway_id',
		'label' => __( 'Payment Gateway ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'payment_gateway',
		'label' => __( 'Payment Gateway', 'woocommerce-exporter' )
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
		'label' => __( 'Shipping Cost', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_weight_total',
		'label' => __( 'Shipping Weight', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'payment_status',
		'label' => __( 'Order Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_status',
		'label' => __( 'Post Status', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_key',
		'label' => __( 'Order Key', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'transaction_id',
		'label' => __( 'Transaction ID', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'created_via',
		'label' => __( 'Created Via', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'cart_hash',
		'label' => __( 'Cart Hash', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'purchase_date',
		'label' => __( 'Order Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_time',
		'label' => __( 'Order Time', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'customer_message',
		'label' => __( 'Customer Message', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'customer_notes',
		'label' => __( 'Customer Notes', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_notes',
		'label' => __( 'Order Notes', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	// PayPal
	$fields[] = array(
		'name' => 'paypal_payer_paypal_address',
		'label' => __( 'PayPal: Payer PayPal Address', 'woocommerce-exporter' ),
		'hover' => __( 'PayPal', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'paypal_payer_first_name',
		'label' => __( 'PayPal: Payer first name', 'woocommerce-exporter' ),
		'hover' => __( 'PayPal', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'paypal_payer_last_name',
		'label' => __( 'PayPal: Payer last name', 'woocommerce-exporter' ),
		'hover' => __( 'PayPal', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'paypal_payment_type',
		'label' => __( 'PayPal: Payment type', 'woocommerce-exporter' ),
		'hover' => __( 'PayPal', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'paypal_payment_status',
		'label' => __( 'PayPal: Payment status', 'woocommerce-exporter' ),
		'hover' => __( 'PayPal', 'woocommerce-exporter' ),
		'disabled' => 1
	);

	$fields[] = array(
		'name' => 'total_quantity',
		'label' => __( 'Total Quantity', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'total_order_items',
		'label' => __( 'Total Order Items', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'user_id',
		'label' => __( 'User ID', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'user_name',
		'label' => __( 'Username', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'user_role',
		'label' => __( 'User Role', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'ip_address',
		'label' => __( 'Checkout IP Address', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'browser_agent',
		'label' => __( 'Checkout Browser Agent', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'has_downloads',
		'label' => __( 'Has Downloads', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'has_downloaded',
		'label' => __( 'Has Downloaded', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'billing_full_name',
		'label' => __( 'Billing: Full Name', 'woocommerce-exporter' ),
		'disabled' => 1
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
		'label' => __( 'Billing: Street Address (Full)', 'woocommerce-exporter' ),
		'disabled' => 1
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
		'label' => __( 'Billing: State', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'billing_country',
		'label' => __( 'Billing: Country (prefix)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'billing_country_full',
		'label' => __( 'Billing: Country', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'billing_phone',
		'label' => __( 'Billing: Phone Number', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'billing_email',
		'label' => __( 'Billing: E-mail Address', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_full_name',
		'label' => __( 'Shipping: Full Name', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_first_name',
		'label' => __( 'Shipping: First Name', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_last_name',
		'label' => __( 'Shipping: Last Name', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_company',
		'label' => __( 'Shipping: Company', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_address',
		'label' => __( 'Shipping: Street Address (Full)', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_address_1',
		'label' => __( 'Shipping: Street Address 1', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_address_2',
		'label' => __( 'Shipping: Street Address 2', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_city',
		'label' => __( 'Shipping: City', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_postcode',
		'label' => __( 'Shipping: ZIP Code', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_state',
		'label' => __( 'Shipping: State (prefix)', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_state_full',
		'label' => __( 'Shipping: State', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_country',
		'label' => __( 'Shipping: Country (prefix)', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'shipping_country_full',
		'label' => __( 'Shipping: Country', 'woocommerce-exporter' ),
		'disabled' => 1
	);

/*
	$fields[] = array(
		'name' => '',
		'label' => __( '', 'woocommerce-exporter' )
	);
*/

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_filter_sanitize_key' );

	// Allow Plugin/Theme authors to add support for additional Order columns
	$fields = apply_filters( sprintf( WOO_CE_PREFIX . '_%s_fields', $export_type ), $fields, $export_type );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_filter_sanitize_key' );

	$fields[] = array(
		'name' => 'order_items_id',
		'label' => __( 'Order Items: ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_product_id',
		'label' => __( 'Order Items: Product ID', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_variation_id',
		'label' => __( 'Order Items: Variation ID', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_sku',
		'label' => __( 'Order Items: SKU', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_name',
		'label' => __( 'Order Items: Product Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_variation',
		'label' => __( 'Order Items: Product Variation', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_image_embed',
		'label' => __( 'Order Items: Featured Image (Embed)', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_description',
		'label' => __( 'Order Items: Product Description', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_excerpt',
		'label' => __( 'Order Items: Product Excerpt', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_publish_date',
		'label' => __( 'Order Items: Publish Date', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_modified_date',
		'label' => __( 'Order Items: Modified Date', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_tax_class',
		'label' => __( 'Order Items: Tax Class', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_quantity',
		'label' => __( 'Order Items: Quantity', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_total',
		'label' => __( 'Order Items: Total', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'order_items_subtotal',
		'label' => __( 'Order Items: Subtotal', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_rrp',
		'label' => __( 'Order Items: RRP', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_stock',
		'label' => __( 'Order Items: Stock', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_shipping_class',
		'label' => __( 'Order Items: Shipping Class', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_tax',
		'label' => __( 'Order Items: Tax', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_tax_percentage',
		'label' => __( 'Order Items: Tax Percentage', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_tax_subtotal',
		'label' => __( 'Order Items: Tax Subtotal', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	// Order Item: Tax Rate - ...
	$tax_rates = woo_ce_get_order_tax_rates();
	if( !empty( $tax_rates ) ) {
		foreach( $tax_rates as $tax_rate ) {
			$fields[] = array(
				'name' => sprintf( 'order_items_tax_rate_%d', $tax_rate['rate_id'] ),
				'label' => sprintf( __( 'Order Items: Tax Rate - %s', 'woocommerce-exporter' ), $tax_rate['label'] ),
				'disabled' => 1
			);
		}
	}
	unset( $tax_rates, $tax_rate );
	$fields[] = array(
		'name' => 'order_items_refund_subtotal',
		'label' => __( 'Order Items: Refund Subtotal', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_refund_quantity',
		'label' => __( 'Order Items: Refund Quantity', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_type',
		'label' => __( 'Order Items: Type', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_type_id',
		'label' => __( 'Order Items: Type ID', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_category',
		'label' => __( 'Order Items: Category', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_tag',
		'label' => __( 'Order Items: Tag', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_total_sales',
		'label' => __( 'Order Items: Total Sales', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_weight',
		'label' => __( 'Order Items: Weight', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_height',
		'label' => __( 'Order Items: Height', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_width',
		'label' => __( 'Order Items: Width', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_length',
		'label' => __( 'Order Items: Length', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'order_items_total_weight',
		'label' => __( 'Order Items: Total Weight', 'woocommerce-exporter' ),
		'disabled' => 1
	);

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_filter_sanitize_key' );

	// Allow Plugin/Theme authors to add support for additional Order Item columns
	$fields = apply_filters( sprintf( WOO_CE_PREFIX . '_%s_fields', 'order_items' ), $fields, $export_type );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_filter_sanitize_key' );


	switch( $format ) {

		case 'summary':
			$output = array();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( isset( $fields[$i] ) ) {
					if( !isset( $fields[$i]['disabled'] ) )
						$output[$fields[$i]['name']] = 'on';
				}
			}
			return $output;
			break;

		case 'full':
		default:
			// Load the default sorting
			if( empty( $sorting ) )
				$sorting = woo_ce_get_option( sprintf( '%s_sorting', $export_type ), array() );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( !isset( $fields[$i]['name'] ) ) {
					unset( $fields[$i] );
					continue;
				}
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

// Check if we should override field labels from the Field Editor
function woo_ce_override_order_field_labels( $fields = array() ) {

	$export_type = 'order';

	$labels = false;

	// Default to Quick Export labels
	if( empty( $labels ) )
		$labels = woo_ce_get_option( sprintf( '%s_labels', $export_type ), array() );

	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_order_fields', 'woo_ce_override_order_field_labels', 11 );
add_filter( 'woo_ce_order_items_fields', 'woo_ce_override_order_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_order_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_order_fields();
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			if( $fields[$i]['name'] == $name ) {
				switch( $format ) {

					case 'name':
						$output = $fields[$i]['label'];
						break;

					case 'full':
						$output = $fields[$i];
						break;

				}
				$i = $size;
			}
		}
	}
	return $output;

}

// Returns a list of Order IDs
function woo_ce_get_orders( $export_type = 'order', $args = array() ) {

	global $export;

	$limit_volume = -1;
	$offset = 0;

	if( $args ) {
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : false );
		$offset = $args['offset'];
		$orderby = ( isset( $args['order_orderby'] ) ? $args['order_orderby'] : 'ID' );
		$order = ( isset( $args['order_order'] ) ? $args['order_order'] : 'ASC' );
		$order_dates_filter = ( isset( $args['order_dates_filter'] ) ? $args['order_dates_filter'] : false );
		switch( $order_dates_filter ) {

			case 'today':
				$order_dates_from = woo_ce_get_order_date_filter( 'today', 'from' );
				$order_dates_to = woo_ce_get_order_date_filter( 'today', 'to' );
				break;

			case 'yesterday':
				$order_dates_from = woo_ce_get_order_date_filter( 'yesterday', 'from' );
				$order_dates_to = woo_ce_get_order_date_filter( 'yesterday', 'to' );
				break;

			case 'current_week':
				$order_dates_from = woo_ce_get_order_date_filter( 'current_week', 'from' );
				$order_dates_to = woo_ce_get_order_date_filter( 'current_week', 'to' );
				break;

			case 'last_week':
				$order_dates_from = woo_ce_get_order_date_filter( 'last_week', 'from' );
				$order_dates_to = woo_ce_get_order_date_filter( 'last_week', 'to' );
				break;

			default:
				$order_dates_from = false;
				$order_dates_to = false;
				break;

		}
		if( !empty( $order_dates_from ) && !empty( $order_dates_to ) ) {
			// From
			$order_dates_from = explode( '-', $order_dates_from );
			// Check that a valid date was provided
			if( isset( $order_dates_from[0] ) && isset( $order_dates_from[1] ) && isset( $order_dates_from[2] ) ) {
				$order_dates_from = array(
					'year' => absint( $order_dates_from[2] ),
					'month' => absint( $order_dates_from[1] ),
					'day' => absint( $order_dates_from[0] ),
					'hour' => ( isset( $order_dates_from[3] ) ? $order_dates_from[3] : 0 ),
					'minute' => ( isset( $order_dates_from[4] ) ? $order_dates_from[4] : 0 ),
					'second' => ( isset( $order_dates_from[5] ) ? $order_dates_from[5] : 0 )
				);
			} else {
				$order_dates_from = false;
			}
			// To
			$order_dates_to = explode( '-', $order_dates_to );
			// Check that a valid date was provided
			if( isset( $order_dates_to[0] ) && isset( $order_dates_to[1] ) && isset( $order_dates_to[2] ) ) {
				$order_dates_to = array(
					'year' => absint( $order_dates_to[2] ),
					'month' => absint( $order_dates_to[1] ),
					'day' => absint( $order_dates_to[0] ),
					'hour' => ( isset( $order_dates_to[3] ) ? $order_dates_to[3] : 23 ),
					'minute' => ( isset( $order_dates_to[4] ) ? $order_dates_to[4] : 59 ),
					'second' => ( isset( $order_dates_to[5] ) ? $order_dates_to[5] : 59 )
				);
			} else {
				$order_dates_to = false;
			}
		}
	}
	$post_type = 'shop_order';
	$args = array(
		'post_type' => $post_type,
		'orderby' => $orderby,
		'order' => $order,
		'offset' => $offset,
		'posts_per_page' => $limit_volume,
		'fields' => 'ids',
		'suppress_filters' => false
	);

	$woocommerce_version = woo_get_woo_version();
	// Check if this is a pre-WooCommerce 2.2 instance
	if( version_compare( $woocommerce_version, '2.2' ) >= 0 )
		$args['post_status'] = ( function_exists( 'wc_get_order_statuses' ) ? apply_filters( 'woo_ce_order_post_status', array_keys( wc_get_order_statuses() ) ) : 'any' );
	else
		$args['post_status'] = apply_filters( 'woo_ce_order_post_status', 'publish' );

	// Filter Order dates
	if( !empty( $order_dates_from ) && !empty( $order_dates_to ) ) {
		$args['date_query'] = array(
			array(
				'column' => apply_filters( 'woo_ce_get_orders_filter_order_dates_column', 'post_date' ),
				'before' => $order_dates_to,
				'after' => $order_dates_from,
				'inclusive' => true
			)
		);
	}

	$orders = array();

	$order_ids = new WP_Query( $args );

	if( $order_ids->posts ) {

		foreach( $order_ids->posts as $order_id ) {

			// Check that a WP_Post didn't sneak through...
			if( is_object( $order_id ) )
				$order_id = ( isset( $order_id->ID ) ? absint( $order_id->ID ) : $order_id );

			$order_id = apply_filters( 'woo_ce_get_order_id', $order_id );
			if( $order_id )
				$orders[] = $order_id;

		}

		// Only populate the $export Global if it is an export
		if( isset( $export ) ) {
			$export->total_rows = count( $orders );
		}
		unset( $order_ids, $order_id );

	}
	return $orders;

}

// Returns WooCommerce Order data associated to a specific Order
function woo_ce_get_order_wc_data( $order_id = 0 ) {

	if( !empty( $order_id ) ) {
		if( version_compare( woo_get_woo_version(), '2.7', '>=' ) ) {
			$order = ( class_exists( 'WC_Order' ) ? new WC_Order( $order_id ) : get_post( $order_id ) );
			$order->status = ( method_exists( $order, 'get_status' ) ? $order->get_status() : false );
			$order->order_date = ( method_exists( $order, 'get_date_created' ) ? $order->get_date_created() : false );
		} else {
			$order = ( class_exists( 'WC_Order' ) ? new WC_Order( $order_id ) : get_post( $order_id ) );
		}
		return $order;
	}

}

function woo_ce_get_order_data( $order_id = 0, $export_type = 'order', $args = array(), $fields = array() ) {

	global $export;

	// Check if this is a pre-WooCommerce 2.2 instance
	$woocommerce_version = woo_get_woo_version();

	$defaults = array(
		'order_items' => 'combined',
		'order_items_types' => array_keys( woo_ce_get_order_items_types() )
	);
	$args = wp_parse_args( $args, $defaults );

	// Get WooCommerce Order details
	$order = woo_ce_get_order_wc_data( $order_id );

	$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );

	$order->payment_status = $order->status;

	$order->purchase_total = get_post_meta( $order_id, '_order_total', true );
	$order->order_currency = get_post_meta( $order_id, '_order_currency', true );

	// Order billing details
	if( !apply_filters( 'woo_ce_get_order_data_legacy_billing_address', version_compare( $woocommerce_version, '3.0', '<' ) ) ) {
		// WC: 3.0+ Order billing address
		$billing_address = ( method_exists( $order, 'get_address' ) ? $order->get_address( 'billing' ) : false );
		if( !empty( $billing_address ) ) {
			$order->billing_first_name = $billing_address['first_name'];
			$order->billing_last_name = $billing_address['last_name'];
			$order->billing_company = $billing_address['company'];
			$order->billing_address_1 = $billing_address['address_1'];
			$order->billing_address_2 = $billing_address['address_2'];
			$order->billing_city = $billing_address['city'];
			$order->billing_postcode = $billing_address['postcode'];
			$order->billing_state = $billing_address['state'];
			$order->billing_country = $billing_address['country'];
		}
		unset( $billing_address );
	} else {
		// WC: Pre-3.0 Order billing address
		$order->billing_first_name = get_post_meta( $order_id, '_billing_first_name', true );
		$order->billing_last_name = get_post_meta( $order_id, '_billing_last_name', true );
		$order->billing_company = get_post_meta( $order_id, '_billing_company', true );
		$order->billing_address_1 = get_post_meta( $order_id, '_billing_address_1', true );
		$order->billing_address_2 = get_post_meta( $order_id, '_billing_address_2', true );
		$order->billing_city = get_post_meta( $order_id, '_billing_city', true );
		$order->billing_postcode = get_post_meta( $order_id, '_billing_postcode', true );
		$order->billing_state = get_post_meta( $order_id, '_billing_state', true );
		$order->billing_country = get_post_meta( $order_id, '_billing_country', true );
	}

	if( $export_type == 'order' ) {

		$order->payment_gateway_id = get_post_meta( $order_id, '_payment_method', true );
		$order->payment_gateway = woo_ce_format_order_payment_gateway( $order->payment_gateway_id );
		// WooCommerce 2.1+ stores the shipping method in Order Items, includes fallback support
		if( version_compare( woo_get_woo_version(), '2.1', '>=' ) ) {
			$order->shipping_method_id = woo_ce_get_order_assoc_shipping_method_id( $order_id );
			$order->shipping_method = $order->get_shipping_method();
		} else {
			$order->shipping_method_id = woo_ce_get_order_assoc_shipping_method_id( $order_id );
			$order->shipping_method = '';
		}
		$order->purchase_date = ( function_exists( 'wc_format_datetime' ) ? wc_format_datetime( $order->order_date, $date_format ) : woo_ce_format_date( $order->order_date ) );

		if( $order->order_items = woo_ce_get_order_items( $order_id, $args['order_items_types'] ) ) {
			$order->total_order_items = count( $order->order_items );
			if( $args['order_items'] == 'combined' ) {
				$order->order_items_id = '';
				$order->order_items_name = '';
				$order->order_items_quantity = '';
				$order->order_items_total = '';
				if( !empty( $order->order_items ) ) {
					foreach( $order->order_items as $order_item ) {
						$order->order_items_id .= $order_item->id . $export->category_separator;
						$order->order_items_name .= $order_item->name . $export->category_separator;
						if( empty( $order_item->quantity ) && '0' != $order_item->quantity )
							$order_item->quantity = '';
						$order->order_items_quantity .= $order_item->quantity . $export->category_separator;
						$order->order_items_total .= $order_item->total . $export->category_separator;
					}
					$order->order_items_id = substr( $order->order_items_id, 0, -1 );
					$order->order_items_name = substr( $order->order_items_name, 0, -1 );
					$order->order_items_quantity = substr( $order->order_items_quantity, 0, -1 );
					$order->order_items_total = substr( $order->order_items_total, 0, -1 );
				}
			}
		}

	}

	return $order;

}

function woo_ce_export_dataset_override_order( $output = null, $export_type = null ) {

	global $export;

	if( $orders = woo_ce_get_orders( 'order', $export->args ) ) {
		$separator = $export->delimiter;
		$size = $export->total_columns;
		$export->total_rows = count( $orders );
		// Generate the export headers
		if( in_array( $export->export_format, array( 'csv' ) ) ) {
			for( $i = 0; $i < $size; $i++ ) {
				if( $i == ( $size - 1 ) )
					$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
				else
					$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
			}
		}
		if( !empty( $export->fields ) ) {
			foreach( $orders as $order ) {

				$order = woo_ce_get_order_data( $order, 'order', $export->args, array_keys( $export->fields ) );
				foreach( $export->fields as $key => $field ) {
					if( isset( $order->$key ) ) {
						if( is_array( $field ) ) {
							foreach( $field as $array_key => $array_value ) {
								if( !is_array( $array_value ) ) {
									if( in_array( $export->export_format, array( 'csv' ) ) )
										$output .= woo_ce_escape_csv_value( $array_value, $export->delimiter, $export->escape_formatting );
								}
							}
						} else {
							if( in_array( $export->export_format, array( 'csv' ) ) )
								$output .= woo_ce_escape_csv_value( $order->$key, $export->delimiter, $export->escape_formatting );
						}
					}
					if( in_array( $export->export_format, array( 'csv' ) ) )
						$output .= $separator;
				}

				if( in_array( $export->export_format, array( 'csv' ) ) )
					$output = substr( $output, 0, -1 ) . "\n";
			}
		}
		unset( $orders, $order );
	}
	return $output;

}

// Returns a list of WooCommerce Tax Rates based on existing Orders
function woo_ce_get_order_tax_rates( $order_id = 0 ) {

	if( apply_filters( 'woo_ce_enable_order_tax_rates', true ) ) {

		global $wpdb;

		$order_item_type = 'tax';
		$tax_rates_sql = $wpdb->prepare( "SELECT order_items.order_item_id as item_id FROM " . $wpdb->prefix . "woocommerce_order_items as order_items WHERE order_items.order_item_type = %s", $order_item_type );
		if( !empty( $order_id ) ) {
			$tax_rates_sql .= $wpdb->prepare( " AND order_items.order_id = %d", $order_id );
		}
		$tax_rates_sql .= " GROUP BY order_items.order_item_name";
		$tax_rates = $wpdb->get_results( $tax_rates_sql, 'ARRAY_A' );
		if( !empty( $tax_rates ) ) {
			$meta_type = 'order_item';
			foreach( $tax_rates as $key => $tax_rate ) {
				$tax_rates[$key]['rate_id'] = get_metadata( $meta_type, $tax_rate['item_id'], 'rate_id', true );
				$tax_rates[$key]['label'] = get_metadata( $meta_type, $tax_rate['item_id'], 'label', true );
			}
			return $tax_rates;
		}

	}

}

function woo_ce_get_gravity_forms_products() {

	global $wpdb;

	$meta_key = '_gravity_form_data';
	$post_ids_sql = $wpdb->prepare( "SELECT `post_id`, `meta_value` FROM `$wpdb->postmeta` WHERE `meta_key` = %s GROUP BY `meta_value`", $meta_key );
	return $wpdb->get_results( $post_ids_sql );

}

function woo_ce_get_gravity_form_fields() {

	if( $gf_products = woo_ce_get_gravity_forms_products() ) {
		$fields = array();
		foreach( $gf_products as $gf_product ) {
			if( $gf_product_data = maybe_unserialize( get_post_meta( $gf_product->post_id, '_gravity_form_data', true ) ) ) {
				// Check the class and method for Gravity Forms exists
				if( class_exists( 'RGFormsModel' ) && method_exists( 'RGFormsModel', 'get_form_meta' ) ) {
					// Check the form exists
					$gf_form_meta = RGFormsModel::get_form_meta( $gf_product_data['id'] );
					if( !empty( $gf_form_meta ) ) {
						// Check that the form has fields assigned to it
						if( !empty( $gf_form_meta['fields'] ) ) {
							foreach( $gf_form_meta['fields'] as $gf_form_field ) {
								// Check for duplicate Gravity Form fields
								$gf_form_field['formTitle'] = $gf_form_meta['title'];
								// Do not include page and section breaks, hidden as exportable fields
								if( !in_array( $gf_form_field['type'], array( 'page', 'section', 'hidden' ) ) )
									$fields[] = $gf_form_field;
							}
						}
					}
					unset( $gf_form_meta );
				}
			}
		}
		return $fields;
	}

}

// Return the PHP date format for the requested Order Date filter
function woo_ce_get_order_date_filter( $filter = '', $format = '', $date_format = 'd-m-Y' ) {

	$output = false;
	if( !empty( $filter ) && !empty( $format ) ) {
		switch( $filter ) {

			// Today
			case 'today':
				if( $format == 'from' )
					$output = date( $date_format, strtotime( 'today' ) );
				else
					$output = date( $date_format, strtotime( 'tomorrow' ) );
				break;

			// Yesterday
			case 'yesterday':
				if( $format == 'from' )
					$output = date( $date_format, strtotime( 'yesterday' ) );
				else
					$output = date( $date_format, strtotime( 'yesterday' ) );
				break;

			// This week
			case 'current_week':
				if( $format == 'from' )
					$output = date( $date_format, strtotime( 'last Monday' ) );
				else
					$output = date( $date_format, strtotime( 'next Monday' ) );
				break;

			// Last week
			case 'last_week':
				if( $format == 'from' )
					$output = date( $date_format, strtotime( '-2 weeks Monday' ) );
				else
					$output = date( $date_format, strtotime( '-1 weeks Monday' ) );
				break;

/*
			case '':
				if( $format == 'from' )
					$output = ;
				else
					$output = ;
					break;
*/

			default:
				woo_ce_error_log( sprintf( 'Warning: %s', sprintf( __( 'Unknown Order Date filter %s provided, defaulted to none', 'woocommerce-exporter' ), $filter ) ) );
				break;

		}
	}
	return $output;

}

// Returns date of first Order received, any status
function woo_ce_get_order_first_date( $date_format = 'd/m/Y' ) {

	$output = date( $date_format, mktime( 0, 0, 0, date( 'n' ), 1 ) );

	$post_type = 'shop_order';
	$args = array(
		'post_type' => $post_type,
		'orderby' => 'post_date',
		'order' => 'ASC',
		'numberposts' => 1,
		'post_status' => 'any'
	);
	$orders = get_posts( $args );
	if( !empty( $orders ) ) {
		$output = date( $date_format, strtotime( $orders[0]->post_date ) );
		unset( $orders );
	}
	return $output;

}

// Returns a list of WooCommerce Order statuses
function woo_ce_get_order_statuses() {

	$terms = false;

	// Check if the existing Transient exists
	$cached = get_transient( WOO_CE_PREFIX . '_order_statuses' );
	if( $cached == false ) {

		// Check if this is a WooCommerce 2.2+ instance (new Post Status)
		$woocommerce_version = woo_get_woo_version();
		if( version_compare( $woocommerce_version, '2.2' ) >= 0 ) {
			// Convert Order Status array into our magic sauce
			$order_statuses = ( function_exists( 'wc_get_order_statuses' ) ? wc_get_order_statuses() : false );
			if( !empty( $order_statuses ) ) {
				$terms = array();
				$post_type = 'shop_order';
				$posts_count = wp_count_posts( $post_type );
				foreach( $order_statuses as $key => $order_status ) {
					$terms[] = (object)array(
						'name' => $order_status,
						'slug' => $key,
						'count' => ( isset( $posts_count->$key ) ? $posts_count->$key : 0 )
					);
				}
			}
		} else {
			$args = array(
				'hide_empty' => false
			);
			$terms = get_terms( 'shop_order_status', $args );
			if( empty( $terms ) || ( is_wp_error( $terms ) == true ) )
				$terms = array();
		}
		set_transient( WOO_CE_PREFIX . '_order_statuses', $terms, HOUR_IN_SECONDS );

	} else {
		$terms = $cached;
	}
	return $terms;

}

// Returns the Shipping Method ID associated to a specific Order
function woo_ce_get_order_assoc_shipping_method_id( $order_id = 0 ) {

	if( empty( $order_id ) )
		return false;

	if( class_exists( 'WC_Order' ) ) {
		$output = '';
		$order = new WC_Order( $order_id );
		if( method_exists( 'WC_Order', 'get_shipping_methods' ) ) {
			if( $shipping_methods = $order->get_shipping_methods() ) {
				foreach( $shipping_methods as $shipping_item_id => $shipping_item ) {
					if( isset( $shipping_item['method_id'] ) ) {
						$output = $shipping_item['method_id'];
					}
					if( empty( $output ) && isset( $shipping_item['item_meta'] ) ) {
						$output = ( isset( $shipping_item['item_meta']['method_id'] ) ? $shipping_item['item_meta']['method_id'] : false );
						if( is_array( $output ) )
							$output = ( isset( $output[0] ) ? $output[0] : false );
						break;
					}
					// Check if a value has been set
					if( !empty( $output ) )
						break;
				}
			}
			unset( $shipping_methods );
		} else {
			$output = get_post_meta( $order_id, '_shipping_method', true );
			if( is_array( $output ) )
				$output = ( isset( $output[0] ) ? $output[0] : false );
		}
		unset( $order );
		return apply_filters( 'woo_ce_get_order_assoc_shipping_method_id', $output );
	}

}

// Returns a list of Order Items for a specified Order
function woo_ce_get_order_items( $order_id = 0, $order_items_types = array() ) {

	global $export, $wpdb;

	$upload_dir = wp_upload_dir();

	if( !empty( $order_id ) ) {
		$order_items_sql = $wpdb->prepare( "SELECT `order_item_id` as id, `order_item_name` as name, `order_item_type` as type FROM `" . $wpdb->prefix . "woocommerce_order_items` WHERE `order_id` = %d", $order_id );

		if( $order_items = $wpdb->get_results( $order_items_sql ) ) {
			$wpdb->flush();

			// Default to Line Item for empty Order Item types
			if( empty( $order_items_types ) )
				$order_items_types = array( 'line_item' );
			foreach( $order_items as $key => $order_item ) {

				// Fetch all meta keys linked to an Order Item
				$order_item_meta_sql = $wpdb->prepare( "SELECT `meta_key`, `meta_value` FROM `" . $wpdb->prefix . "woocommerce_order_itemmeta` WHERE `order_item_id` = %d ORDER BY meta_key ASC", $order_item->id );
				if( $order_item_meta = $wpdb->get_results( $order_item_meta_sql ) ) {
					$order_items[$key]->quantity = '';
					$order_items[$key]->total = '';
					$size = count( $order_item_meta );
					for( $i = 0; $i < $size; $i++ ) {

						// Go through each Order Item meta found
						switch( $order_item_meta[$i]->meta_key ) {

							case '_qty':
								$order_items[$key]->quantity = $order_item_meta[$i]->meta_value;
								break;

							case '_product_id':
								break;

							case '_variation_id':
								break;
							case '_line_total':
								$order_items[$key]->total = woo_ce_format_price( $order_item_meta[$i]->meta_value );
								break;

						}
					}
				}
				unset( $order_item_meta );

				// Default the quantity to 1 for the Fee Order Item Type
				if( $order_items[$key]->type == 'fee' )
					$order_items[$key]->quantity = 1;

			}

			return $order_items;

		}
	}

}

// Returns a list of WooCommerce Order Item Types
function woo_ce_get_order_items_types() {

	$order_item_types = array(
		'line_item' => __( 'Line Item', 'woocommerce-exporter' ),
		'coupon' => __( 'Coupon', 'woocommerce-exporter' ),
		'fee' => __( 'Fee', 'woocommerce-exporter' ),
		'tax' => __( 'Tax', 'woocommerce-exporter' ),
		'shipping' => __( 'Shipping', 'woocommerce-exporter' )
	);

	// Allow Plugin/Theme authors to add support for additional Order Item types
	$order_item_types = apply_filters( 'woo_ce_order_item_types', $order_item_types );

	return $order_item_types;
	
}

function woo_ce_get_order_payment_gateways() {

	global $woocommerce;

	$output = false;

	// Test that payment gateways exist with WooCommerce 1.6 compatibility
	if( version_compare( $woocommerce->version, '2.0.0', '<' ) ) {
		if( $woocommerce->payment_gateways )
			$output = $woocommerce->payment_gateways->payment_gateways;
	} else {
		if( $woocommerce->payment_gateways() )
			$output = $woocommerce->payment_gateways()->payment_gateways();
	}
	// Add Other to list of payment gateways
	$output['other'] = (object)array(
		'id' => 'other',
		'title' => __( 'Other', 'woocommerce-exporter' ),
		'method_title' => __( 'Other', 'woocommerce-exporter' )
	);

	return $output;

}

function woo_ce_format_order_payment_gateway( $payment_id = '' ) {

	$output = $payment_id;
	$payment_gateways = woo_ce_get_order_payment_gateways();
	if( !empty( $payment_gateways ) ) {
		foreach( $payment_gateways as $payment_gateway ) {
			if( $payment_gateway->id == $payment_id ) {
				if( method_exists( $payment_gateway, 'get_title' ) )
					$output = $payment_gateway->get_title();
				else
					$output = $payment_id;
				break;
			}
		}
		unset( $payment_gateways, $payment_gateway );
	}
	if( empty( $payment_id ) )
		$output = __( 'N/A', 'woocommerce-exporter' );

	return $output;

}
