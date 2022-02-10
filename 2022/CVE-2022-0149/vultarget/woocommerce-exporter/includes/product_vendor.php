<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	// YITH WooCommerce Multi Vendor Premium - http://yithemes.com/themes/plugins/yith-woocommerce-product-vendors/
	function woo_ce_get_export_type_product_vendor_count( $count = 0, $export_type = '', $args ) {

		if( $export_type <> 'product_vendor' )
			return $count;

		$count = 0;
		$term_taxonomy = apply_filters( 'woo_ce_product_vendor_term_taxonomy', 'wcpv_product_vendors' );
		// Check if the existing Transient exists
		$cached = get_transient( WOO_CE_PREFIX . '_product_vendor_count' );
		if( $cached == false ) {
			if( taxonomy_exists( $term_taxonomy ) )
				$count = wp_count_terms( $term_taxonomy );
			set_transient( WOO_CE_PREFIX . '_product_vendor_count', $count, HOUR_IN_SECONDS );
		} else {
			$count = $cached;
		}

		return $count;

	}
	add_filter( 'woo_ce_get_export_type_count', 'woo_ce_get_export_type_product_vendor_count', 10, 3 );

	/* End of: WordPress Administration */

}

// Returns a list of Product Vendor export columns
function woo_ce_get_product_vendor_fields( $format = 'full', $post_ID = 0 ) {

	$export_type = 'product_vendor';

	$fields = array();
	$fields[] = array(
		'name' => 'ID',
		'label' => __( 'Product Vendor ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'title',
		'label' => __( 'Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Slug', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Description', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'url',
		'label' => __( 'Product Vendor URL', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'commission',
		'label' => __( 'Commission', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'paypal_email',
		'label' => __( 'PayPal E-mail Address', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_name',
		'label' => __( 'Vendor Username', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_id',
		'label' => __( 'Vendor User ID', 'woocommerce-exporter' )
	);

/*
	$fields[] = array(
		'name' => '',
		'label' => __( '', 'woocommerce-exporter' )
	);
*/

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_filter_sanitize_key' );

	// Allow Plugin/Theme authors to add support for additional columns
	$fields = apply_filters( sprintf( WOO_CE_PREFIX . '_%s_fields', $export_type ), $fields, $export_type );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_filter_sanitize_key' );

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
			// Load the default sorting
			$sorting = woo_ce_get_option( sprintf( '%s_sorting', $export_type ), array() );
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

// Check if we should override field labels from the Field Editor
function woo_ce_override_product_vendor_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'product_vendor_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}

	return $fields;

}
add_filter( 'woo_ce_product_vendor_fields', 'woo_ce_override_product_vendor_field_labels', 11 );

// Returns a list of Product Vendor Term IDs
function woo_ce_get_product_vendors( $args = array(), $output = 'term_id' ) {

	global $export;

	// Product Vendors - http://www.woothemes.com/products/product-vendors/
	// YITH WooCommerce Multi Vendor Premium - http://yithemes.com/themes/plugins/yith-woocommerce-product-vendors/
	$term_taxonomy = apply_filters( 'woo_ce_product_vendor_term_taxonomy', 'wcpv_product_vendors' );
	$defaults = array(
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => 0
	);
	$args = wp_parse_args( $args, $defaults );

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_product_vendors_args', $args );

	$product_vendors = get_terms( $term_taxonomy, $args );
	if( !empty( $product_vendors ) && is_wp_error( $product_vendors ) == false ) {
		if( $output == 'term_id' ) {
			$vendor_ids = array();
			foreach( $product_vendors as $key => $product_vendor )
				$vendor_ids[] = $product_vendor->term_id;
			// Only populate the $export Global if it is an export
			if( isset( $export ) )
				$export->total_rows = count( $vendor_ids );
			unset( $product_vendors, $product_vendor );
			return $vendor_ids;
		} else if( $output == 'full' ) {
			return $product_vendors;
		}
	}

}