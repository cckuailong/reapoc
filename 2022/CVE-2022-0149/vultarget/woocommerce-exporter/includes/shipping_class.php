<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	if( !function_exists( 'woo_ce_get_export_type_shipping_class_count' ) ) {
		function woo_ce_get_export_type_shipping_class_count() {

			$count = 0;
			// Check if the existing Transient exists
			$cached = get_transient( WOO_CE_PREFIX . '_shipping_class_count' );
			if( $cached == false ) {
				$term_taxonomy = 'product_shipping_class';
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				set_transient( WOO_CE_PREFIX . '_shipping_class_count', $count, HOUR_IN_SECONDS );
			} else {
				$count = $cached;
			}

			return $count;

		}
	}

	/* End of: WordPress Administration */

}

// Returns a list of Shipping Classes export columns
function woo_ce_get_shipping_class_fields( $format = 'full' ) {

	$export_type = 'shipping_class';

	$fields = array();
	$fields[] = array(
		'name' => 'term_id',
		'label' => __( 'Term ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Shipping Class Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Shipping Class Slug', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Shipping Class Description', 'woo_ce' )
	);

/*
	$fields[] = array(
		'name' => '',
		'label' => __( '', 'woo_ce' )
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