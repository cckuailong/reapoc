<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	if( !function_exists( 'woo_ce_get_export_type_brand_count' ) ) {
		function woo_ce_get_export_type_brand_count( $count = 0, $export_type = '', $args ) {

			if( $export_type <> 'brand' )
				return $count;

			$count = 0;
			// Check if the existing Transient exists
			$cached = get_transient( WOO_CE_PREFIX . '_brand_count' );
			if( $cached == false ) {
				$term_taxonomy = apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' );
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				set_transient( WOO_CE_PREFIX . '_brand_count', $count, HOUR_IN_SECONDS );
			} else {
				$count = $cached;
			}
			return $count;

		}
		add_filter( 'woo_ce_get_export_type_count', 'woo_ce_get_export_type_brand_count', 10, 3 );
	}

	/* End of: WordPress Administration */

}

// Returns a list of Brand export columns
function woo_ce_get_brand_fields( $format = 'full' ) {

	$export_type = 'brand';

	$fields = array();
	$fields[] = array(
		'name' => 'term_id',
		'label' => __( 'Term ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Brand Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Brand Slug', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'parent_id',
		'label' => __( 'Parent Term ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Brand Description', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'image',
		'label' => __( 'Brand Image', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'image_embed',
		'label' => __( 'Brand Image (Embed)', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'count',
		'label' => __( 'Count', 'woocommerce-exporter' )
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
	$fields = apply_filters( 'woo_ce_' . $export_type . '_fields', $fields, $export_type );

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

function woo_ce_override_brand_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'brand_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_brand_fields', 'woo_ce_override_brand_field_labels', 11 );

// Returns a list of WooCommerce Product Brands to export process
function woo_ce_get_product_brands( $args = array() ) {

	$term_taxonomy = apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' );
	$defaults = array(
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => 0
	);
	$args = wp_parse_args( $args, $defaults );
	$brands = get_terms( $term_taxonomy, $args );
	if( !empty( $brands ) && is_wp_error( $brands ) == false ) {
		foreach( $brands as $key => $brand ) {
			$brands[$key]->description = woo_ce_format_description_excerpt( $brand->description );
			$brands[$key]->parent_name = '';
			if( $brands[$key]->parent_id = $brand->parent ) {
				if( $parent_brand = get_term( $brands[$key]->parent_id, $term_taxonomy ) ) {
					$brands[$key]->parent_name = $parent_brand->name;
				}
				unset( $parent_brand );
			} else {
				$brands[$key]->parent_id = '';
			}
			$brands[$key]->image = ( function_exists( 'get_brand_thumbnail_url' ) ? get_brand_thumbnail_url( $brand->term_id ) : false );
		}
		return $brands;
	}

}