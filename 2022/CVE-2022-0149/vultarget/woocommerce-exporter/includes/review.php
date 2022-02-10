<?php
// Returns a list of Review export columns
function woo_ce_get_review_fields( $format = 'full' ) {

	$export_type = 'review';

	$fields = array();
	$fields[] = array(
		'name' => 'comment_ID',
		'label' => __( 'Review ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_post_ID',
		'label' => __( 'Product ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sku',
		'label' => __( 'Product SKU', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_name',
		'label' => __( 'Product Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_author',
		'label' => __( 'Reviewer', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_author_email',
		'label' => __( 'E-mail', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_content',
		'label' => __( 'Content', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_date',
		'label' => __( 'Review Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'rating',
		'label' => __( 'Rating', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'verified',
		'label' => __( 'Verified', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'comment_author_IP',
		'label' => __( 'IP Address', 'woocommerce-exporter' )
	);

/*
	$fields[] = array(
		'name' => '',
		'label' => __( '', 'woocommerce-exporter' )
	);
*/

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