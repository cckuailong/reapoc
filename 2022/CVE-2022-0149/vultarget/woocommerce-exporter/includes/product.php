<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	if( !function_exists( 'woo_ce_get_export_type_product_count' ) ) {
		function woo_ce_get_export_type_product_count() {

			$count = 0;
			$post_type = apply_filters( 'woo_ce_get_export_type_product_count_post_types', array( 'product', 'product_variation' ) );

			// Check if the existing Transient exists
			$cached = get_transient( WOO_CE_PREFIX . '_product_count' );
			if( $cached == false ) {
				$args = array(
					'post_type' => $post_type,
					'posts_per_page' => 1,
					'fields' => 'ids',
					'suppress_filters' => true
				);
				$count_query = new WP_Query( $args );
				$count = $count_query->found_posts;
				set_transient( WOO_CE_PREFIX . '_product_count', $count, HOUR_IN_SECONDS );
			} else {
				$count = $cached;
			}
			return $count;

		}
	}

	/* End of: WordPress Administration */

}

// Returns a list of Product export columns
function woo_ce_get_product_fields( $format = 'full' ) {

	$export_type = 'product';

	$fields = array();
	$fields[] = array(
		'name' => 'parent_id',
		'label' => __( 'Parent ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'parent_sku',
		'label' => __( 'Parent SKU', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_id',
		'label' => __( 'Product ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sku',
		'label' => __( 'Product SKU', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Product Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_title',
		'label' => __( 'Post Title', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Slug', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'permalink',
		'label' => __( 'Permalink', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_url',
		'label' => __( 'Product URI', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Description', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'excerpt',
		'label' => __( 'Excerpt', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_date',
		'label' => __( 'Product Published', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_modified',
		'label' => __( 'Product Modified', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'type',
		'label' => __( 'Type', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'visibility',
		'label' => __( 'Visibility', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'featured',
		'label' => __( 'Featured', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'virtual',
		'label' => __( 'Virtual', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'downloadable',
		'label' => __( 'Downloadable', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'price',
		'label' => __( 'Price', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sale_price',
		'label' => __( 'Sale Price', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'net_price',
		'label' => __( 'Net Price', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sale_price_dates_from',
		'label' => __( 'Sale Price Dates From', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sale_price_dates_to',
		'label' => __( 'Sale Price Dates To', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'weight',
		'label' => __( 'Weight', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'weight_unit',
		'label' => __( 'Weight Unit', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'height',
		'label' => __( 'Height', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'height_unit',
		'label' => __( 'Height Unit', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'width',
		'label' => __( 'Width', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'width_unit',
		'label' => __( 'Width Unit', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'length',
		'label' => __( 'Length', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'length_unit',
		'label' => __( 'Length Unit', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'category',
		'label' => __( 'Category', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'category_level_1',
		'label' => __( 'Category: Level 1', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'category_level_2',
		'label' => __( 'Category: Level 2', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'category_level_3',
		'label' => __( 'Category: Level 3', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'tag',
		'label' => __( 'Tag', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'image',
		'label' => __( 'Featured Image', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'image_thumbnail',
		'label' => __( 'Featured Image Thumbnail', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'image_embed',
		'label' => __( 'Featured Image (Embed)', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'image_title',
		'label' => __( 'Featured Image Title', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'image_caption',
		'label' => __( 'Featured Image Caption', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'image_alt',
		'label' => __( 'Featured Image Alternative Text', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'image_description',
		'label' => __( 'Featured Image Description', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'product_gallery',
		'label' => __( 'Product Gallery', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_gallery_thumbnail',
		'label' => __( 'Product Gallery Thumbnail', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'product_gallery_embed',
		'label' => __( 'Product Gallery (Embed)', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'tax_status',
		'label' => __( 'Tax Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'tax_class',
		'label' => __( 'Tax Class', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'shipping_class',
		'label' => __( 'Shipping Class', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'download_file_name',
		'label' => __( 'Download File Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'download_file_path',
		'label' => __( 'Download File URL Path', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'download_limit',
		'label' => __( 'Download Limit', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'download_expiry',
		'label' => __( 'Download Expiry', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'download_type',
		'label' => __( 'Download Type', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'manage_stock',
		'label' => __( 'Manage Stock', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'quantity',
		'label' => __( 'Quantity', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'stock_status',
		'label' => __( 'Stock Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'allow_backorders',
		'label' => __( 'Allow Backorders', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'sold_individually',
		'label' => __( 'Sold Individually', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'total_sales',
		'label' => __( 'Total Sales', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'grouped_products',
		'label' => __( 'Grouped Products', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'upsell_ids',
		'label' => __( 'Up-Sells', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'crosssell_ids',
		'label' => __( 'Cross-Sells', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'external_url',
		'label' => __( 'External URL', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'button_text',
		'label' => __( 'Button Text', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'purchase_note',
		'label' => __( 'Purchase Note', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_status',
		'label' => __( 'Product Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'enable_reviews',
		'label' => __( 'Enable Reviews', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'review_count',
		'label' => __( 'Review Count', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'rating_count',
		'label' => __( 'Rating Count', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'average_rating',
		'label' => __( 'Average Rating', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'menu_order',
		'label' => __( 'Sort Order', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_author',
		'label' => __( 'Post Author', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_name',
		'label' => __( 'Username', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'user_role',
		'label' => __( 'User Role', 'woocommerce-exporter' )
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

	$sorting = false;
	$remember = woo_ce_get_option( $export_type . '_fields', array() );
	if( !empty( $remember ) ) {
		$remember = maybe_unserialize( $remember );
		$size = count( $fields );
		for( $i = 0; $i < $size; $i++ ) {
			$fields[$i]['disabled'] = ( isset( $fields[$i]['disabled'] ) ? $fields[$i]['disabled'] : 0 );
			$fields[$i]['default'] = 1;
			if( !array_key_exists( $fields[$i]['name'], $remember ) )
				$fields[$i]['default'] = 0;
		}
	}

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
			if( empty( $sorting ) )
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
function woo_ce_override_product_field_labels( $fields = array() ) {

	$export_type = 'product';

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
add_filter( 'woo_ce_product_fields', 'woo_ce_override_product_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_product_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_product_fields();
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

// Returns a list of WooCommerce Products
function woo_ce_get_products( $args = array() ) {

	global $export;

	$limit_volume = -1;
	$offset = 0;
	$product_category = false;
	$product_tag = false;
	$product_status = false;
	$product_type = false;
	$orderby = 'ID';
	$order = 'ASC';
	if( $args ) {
		$limit_volume = ( isset( $args['limit_volume'] ) ? $args['limit_volume'] : false );
		$offset = ( isset( $args['offset'] ) ? $args['offset'] : false );
		if( !empty( $args['product_category'] ) )
			$product_category = $args['product_category'];
		if( !empty( $args['product_tag'] ) )
			$product_tag = $args['product_tag'];
		if( !empty( $args['product_status'] ) )
			$product_status = $args['product_status'];
		if( !empty( $args['product_type'] ) )
			$product_type = $args['product_type'];
		if( isset( $args['product_orderby'] ) )
			$orderby = $args['product_orderby'];
		if( isset( $args['product_order'] ) )
			$order = $args['product_order'];
	}

	$post_type = apply_filters( 'woo_ce_get_products_post_type', array( 'product', 'product_variation' ) );
	$post_status = apply_filters( 'woo_ce_get_products_status', array( 'publish', 'pending', 'draft', 'future' ) );

	$args = array(
		'post_type' => $post_type,
		'orderby' => $orderby,
		'order' => $order,
		'offset' => $offset,
		'posts_per_page' => $limit_volume,
		'post_status' => woo_ce_post_statuses( $post_status, true ),
		'fields' => 'ids',
		'suppress_filters' => false
	);

	// Filter Products by Product Type
	if(
		is_array( $product_type ) && 
		!empty( $product_type )
	) {
		// Check if we are just exporting Variations
		if(
			in_array( 'variation', $product_type ) && 
			count( $product_type ) == 1
		) {
			$args['post_type'] = array( 'product_variation' );
		}
	}

	// Check if we are doing a Variation export
	if( !in_array( 'product_variation', $args['post_type'] ) ) {

	}

	// Filter Products by Product Category
	if( $product_category ) {
		$term_taxonomy = 'product_cat';
		// Check if tax_query has been created
		if( !isset( $args['tax_query'] ) )
			$args['tax_query'] = array();
		$args['tax_query'][] = array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'id',
				'terms' => $product_category
			)
		);
	}

	// Filter Products by Product Tag
	if( $product_tag ) {
		$term_taxonomy = 'product_tag';
		// Check if tax_query has been created
		if( !isset( $args['tax_query'] ) )
			$args['tax_query'] = array();
		$args['tax_query'][] = array(
			array(
				'taxonomy' => $term_taxonomy,
				'field' => 'id',
				'terms' => $product_tag
			)
		);
	}

	// Filter Products by Post Status
	if( $product_status ) {
		$args['post_status'] = woo_ce_post_statuses( $product_status, true );
	}


	// Sort Products by SKU
	if( $orderby == 'sku' ) {
		$args['orderby'] = 'meta_value';
		$args['meta_key'] = '_sku';
	}

	$products = array();
	$product_ids = new WP_Query( $args );
	if( $product_ids->posts ) {
		foreach( $product_ids->posts as $product_id ) {

			// Check that a WP_Post didn't sneak through...
			if( is_object( $product_id ) )
				$product_id = ( isset( $product_id->ID ) ? absint( $product_id->ID ) : $product_id );

			// Get Product details
			$product = get_post( $product_id );

			// Filter out Variations that don't have a Parent Product that exists
			if( 
				isset( $product->post_type ) && 
				$product->post_type == 'product_variation'
			) {

				// Filter out Variations that don't have a Parent Product that exists
				if( $product->post_parent ) {
					// Check if Parent exists
					if( get_post( $product->post_parent ) == false ) {
						unset( $product_id, $product );
						continue;
					}
				}

				// Allow filtering Variations by parent Variable filters
				if(
					is_array( $product_type ) &&
					!empty( $product_type
				) ) {
					// Check if we are just exporting Variations
					if(
						in_array( 'variation', $product_type ) && 
						count( $product_type ) == 1
					) {

						// Filter Variations by Product Category
						if( $product_category ) {
							$term_taxonomy = 'product_cat';
							$response = array_intersect( woo_ce_get_product_assoc_categories( $product->post_parent, false, $term_taxonomy, 'ids' ), $product_category );
							if( empty( $response ) ) {
								unset( $product_id, $product );
								continue;
							}
							unset( $response );
						}

						// Filter Variations by Product Tag
						if( $product_tag ) {
							$term_taxonomy = 'product_tag';
							$response = array_intersect( woo_ce_get_product_assoc_tags( $product->post_parent, $term_taxonomy, 'ids' ), $product_tag );
							if( empty( $response ) ) {
								unset( $product_id, $product );
								continue;
							}
							unset( $response );
						}

					}
				}

			}

			if( isset( $product_id ) )
				$products[] = $product_id;

			// Override for exporting Variations without Variables
			if( is_array( $product_type ) && !empty( $product_type ) ) {
				if( in_array( 'variation', $product_type ) && in_array( 'variable', $product_type ) == false ) {
					$term_taxonomy = 'product_type';
					if( $product->post_type == 'product' && has_term( 'variable', $term_taxonomy, $product_id ) ) {
						// Remove the Variable Product ID
						$key = array_search( $product_id, $products );
						if( $key !== false )
							unset( $products[$key] );
					}
				}
			}

		}
		// Only populate the $export Global if it is an export
		if( isset( $export ) )
			$export->total_rows = count( $products );
		unset( $product_ids, $product_id );
	}
	return $products;

}

function woo_ce_get_product_data( $product_id = 0, $args = array() ) {

	// Get Product defaults
	$weight_unit = get_option( 'woocommerce_weight_unit' );
	$dimension_unit = get_option( 'woocommerce_dimension_unit' );
	$height_unit = $dimension_unit;
	$width_unit = $dimension_unit;
	$length_unit = $dimension_unit;

	$product = get_post( $product_id );
	$_product = ( function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : false );
	// Check for corrupt Products, and old school WooCommerce...
	if( !version_compare( woo_get_woo_version(), '2.2', '<' ) && $_product == false )
		return false;

	$product->parent_id = '';
	$product->parent_sku = '';
	if( $product->post_type == 'product_variation' ) {
		// Assign Parent ID for Variants then check if Parent exists
		if( $product->parent_id = $product->post_parent )
			$product->parent_sku = get_post_meta( $product->post_parent, '_sku', true );
		else
			$product->parent_id = '';
	}
	$product->product_id = $product_id;
	$product->sku = get_post_meta( $product_id, '_sku', true );
	$product->name = html_entity_decode( get_the_title( $product_id ) );
	if( $product->post_type <> 'product_variation' )
		$product->permalink = get_permalink( $product_id );
	$product->product_url = ( method_exists( $_product, 'get_permalink' ) ? $_product->get_permalink() : get_permalink( $product_id ) );
	$product->slug = $product->post_name;
	$product->user_name = woo_ce_get_username( $product->post_author );
	$product->user_role = woo_ce_format_user_role_label( woo_ce_get_user_role( $product->post_author ) );
	$product->description = woo_ce_format_description_excerpt( $product->post_content );
	$product->excerpt = woo_ce_format_description_excerpt( $product->post_excerpt );
	$product->regular_price = get_post_meta( $product_id, '_regular_price', true );
	// Check that a valid price has been provided and that wc_format_localized_price() exists
	if( isset( $product->regular_price ) && $product->regular_price != '' && function_exists( 'wc_format_localized_price' ) )
		$product->regular_price = wc_format_localized_price( $product->regular_price );
	$product->price = get_post_meta( $product_id, '_price', true );
	if( $product->regular_price != '' && ( $product->regular_price <> $product->price ) )
		$product->price = $product->regular_price;
	// Check that a valid price has been provided and that wc_format_localized_price() exists
	if( isset( $product->price ) && $product->price != '' && function_exists( 'wc_format_localized_price' ) )
		$product->price = wc_format_localized_price( $product->price );
	$product->sale_price = get_post_meta( $product_id, '_sale_price', true );
	// Check that a valid price has been provided and that wc_format_localized_price() exists
	if( isset( $product->sale_price ) && $product->sale_price != '' && function_exists( 'wc_format_localized_price' ) )
		$product->sale_price = wc_format_localized_price( $product->sale_price );
	$product->net_price = ( $product->sale_price != '' ? $product->sale_price : $product->price );
	$product->sale_price_dates_from = woo_ce_format_product_sale_price_dates( get_post_meta( $product_id, '_sale_price_dates_from', true ) );
	$product->sale_price_dates_to = woo_ce_format_product_sale_price_dates( get_post_meta( $product_id, '_sale_price_dates_to', true ) );
	$product->post_date = woo_ce_format_date( $product->post_date );
	$product->post_modified = woo_ce_format_date( $product->post_modified );
	$product->type = woo_ce_get_product_assoc_type( $product_id );
	if( $product->post_type == 'product_variation' ) {
		$product->description = woo_ce_format_description_excerpt( get_post_meta( $product_id, '_variation_description', true ) );
		// Override the Product Type for Variations
		$product->type = __( 'Variation', 'woocommerce-exporter' );
	}
	if( version_compare( woo_get_woo_version(), '3.0', '>=' ) ) {
		$product->visibility = woo_ce_format_product_visibility( $product_id );
		$term_taxonomy = 'product_visibility';
		$product->featured = woo_ce_format_switch( has_term( 'featured', $term_taxonomy, $product_id ) );
	} else {
		$product->visibility = woo_ce_format_product_visibility( $product_id, get_post_meta( $product_id, '_visibility', true ) );
		$product->featured = woo_ce_format_switch( get_post_meta( $product_id, '_featured', true ) );
	}
	$product->virtual = woo_ce_format_switch( get_post_meta( $product_id, '_virtual', true ) );
	$product->downloadable = woo_ce_format_switch( get_post_meta( $product_id, '_downloadable', true ) );
	$product->weight = get_post_meta( $product_id, '_weight', true );
	$product->weight_unit = ( $product->weight != '' ? $weight_unit : '' );
	$product->height = get_post_meta( $product_id, '_height', true );
	$product->height_unit = ( $product->height != '' ? $height_unit : '' );
	$product->width = get_post_meta( $product_id, '_width', true );
	$product->width_unit = ( $product->width != '' ? $width_unit : '' );
	$product->length = get_post_meta( $product_id, '_length', true );
	$product->length_unit = ( $product->length != '' ? $length_unit : '' );
	$product->category = woo_ce_get_product_assoc_categories( $product_id, $product->parent_id );
	$product->tag = woo_ce_get_product_assoc_tags( $product_id );
	$product->manage_stock = get_post_meta( $product_id, '_manage_stock', true );
	$product->allow_backorders = woo_ce_format_product_allow_backorders( get_post_meta( $product_id, '_backorders', true ) );
	$product->sold_individually = woo_ce_format_switch( get_post_meta( $product_id, '_sold_individually', true ) );
	$product->upsell_ids = woo_ce_get_product_assoc_upsell_ids( $product_id );
	$product->crosssell_ids = woo_ce_get_product_assoc_crosssell_ids( $product_id );
	$product->quantity = get_post_meta( $product_id, '_stock', true );
	// Override Variable with total stock quantity
	$term_taxonomy = 'product_type';
	if ( has_term( 'variable', $term_taxonomy, $product_id ) ) {
		if (
			version_compare( woo_get_woo_version(), '3.0', '>=' ) && 
			$product->manage_stock == 'no'
		) {
			$product_variations = ( method_exists( $_product, 'get_available_variations' ) ? $_product->get_available_variations() : $product->quantity );
			if( ! empty( $product_variations ) ) {
				$quantity = 0;
				foreach ( $product_variations as $variation ) {
					$quantity += $variation['max_qty'];
				}
				$product->quantity = $quantity;
				unset( $variation, $quantity );
			}
			unset( $product_variations );
		} else if ( version_compare( woo_get_woo_version(), '2.7', '>=' ) ) {
			$product->quantity = ( method_exists( $_product, 'get_stock_quantity' ) ? $_product->get_stock_quantity() : $product->quantity );
		} else {
			$product->quantity = ( method_exists( $_product, 'get_total_stock' ) ? $_product->get_total_stock() : $product->quantity );
		}
	}
	$product->quantity = ( function_exists( 'wc_stock_amount' ) ? wc_stock_amount( $product->quantity ) : $product->quantity );
	if(
		$product->manage_stock == 'no' && 
		! $product->quantity
	) {
		$product->quantity = '';
	}
	$product->stock_status = woo_ce_format_product_stock_status( get_post_meta( $product_id, '_stock_status', true ), $product->quantity );
	$product->image = woo_ce_get_product_assoc_featured_image( $product_id );
	$product->product_gallery = woo_ce_get_product_assoc_product_gallery( $product_id );
	$product->tax_status = woo_ce_format_product_tax_status( get_post_meta( $product_id, '_tax_status', true ) );
	$product->tax_class = woo_ce_format_product_tax_class( get_post_meta( $product_id, '_tax_class', true ) );
	$product->shipping_class = woo_ce_get_product_assoc_shipping_class( $product_id );
	$product->external_url = get_post_meta( $product_id, '_product_url', true );
	$product->button_text = get_post_meta( $product_id, '_button_text', true );
	$product->download_file_path = woo_ce_get_product_assoc_download_files( $product_id, 'url' );
	$product->download_file_name = woo_ce_get_product_assoc_download_files( $product_id, 'name' );
	$product->download_limit = get_post_meta( $product_id, '_download_limit', true );
	$product->download_expiry = get_post_meta( $product_id, '_download_expiry', true );
	$product->download_type = woo_ce_format_product_download_type( get_post_meta( $product_id, '_download_type', true ) );
	$product->purchase_note = get_post_meta( $product_id, '_purchase_note', true );
	$product->product_status = woo_ce_format_post_status( $product->post_status );
	$product->enable_reviews = woo_ce_format_comment_status( $product->comment_status );
	$product->menu_order = $product->menu_order;
	unset( $_product );

	// Scan for global Attributes first
	$attributes = woo_ce_get_product_attributes();
	if( !empty( $attributes ) && $product->post_type == 'product_variation' ) {
		// We're dealing with a single Variation, strap yourself in.
		foreach( $attributes as $attribute ) {
			$attribute_value = get_post_meta( $product_id, sprintf( 'attribute_pa_%s', $attribute->attribute_name ), true );
			if( !empty( $attribute_value ) ) {
				$term_id = term_exists( $attribute_value, sprintf( 'pa_%s', $attribute->attribute_name ) );
				if( $term_id !== 0 && $term_id !== null && !is_wp_error( $term_id ) ) {
					$term = get_term( $term_id['term_id'], sprintf( 'pa_%s', $attribute->attribute_name ) );
					$attribute_value = $term->name;
					unset( $term );
				}
				unset( $term_id );
			}
			$product->{'attribute_' . $attribute->attribute_name} = $attribute_value;
			unset( $attribute_value );
		}
	} else {
		// Either the Variation Parent or a Simple Product, scan for global and custom Attributes
		$product->attributes = maybe_unserialize( get_post_meta( $product_id, '_product_attributes', true ) );
		if( !empty( $product->attributes ) ) {
			// Check for taxonomy-based attributes
			foreach( $attributes as $attribute ) {
				if( isset( $product->attributes['pa_' . $attribute->attribute_name] ) )
					$product->{'attribute_' . $attribute->attribute_name} = woo_ce_get_product_assoc_attributes( $product_id, $product->attributes['pa_' . $attribute->attribute_name], 'product' );
				else
					$product->{'attribute_' . $attribute->attribute_name} = woo_ce_get_product_assoc_attributes( $product_id, $attribute, 'global' );
			}
			// Check for per-Product attributes (custom)
			foreach( $product->attributes as $key => $attribute ) {
				if( $attribute['is_taxonomy'] == 0 ) {
					if( !isset( $product->{'attribute_' . $key} ) )
						$product->{'attribute_' . $key} = $attribute['value'];
				}
			}
		}
	}

	// Allow Plugin/Theme authors to add support for additional Product columns
	$product = apply_filters( 'woo_ce_product_item', $product, $product_id );

	return $product;

}

function woo_ce_export_dataset_override_product( $output = null, $export_type = null ) {

	global $export;

	if( $products = woo_ce_get_products( $export->args ) ) {
		$separator = $export->delimiter;
		$size = $export->total_columns;
		$export->total_rows = count( $products );
		// Generate the export headers
		if( in_array( $export->export_format, array( 'csv' ) ) ) {
			for( $i = 0; $i < $size; $i++ ) {
				if( $i == ( $size - 1 ) )
					$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
				else
					$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
			}
		}
		$weight_unit = get_option( 'woocommerce_weight_unit' );
		$dimension_unit = get_option( 'woocommerce_dimension_unit' );
		$height_unit = $dimension_unit;
		$width_unit = $dimension_unit;
		$length_unit = $dimension_unit;
		if( !empty( $export->fields ) ) {
			foreach( $products as $product ) {

				$product = woo_ce_get_product_data( $product, $export->args );
				foreach( $export->fields as $key => $field ) {
					if( isset( $product->$key ) ) {
						if( is_array( $field ) ) {
							foreach( $field as $array_key => $array_value ) {
								if( !is_array( $array_value ) ) {
									if( in_array( $export->export_format, array( 'csv' ) ) )
										$output .= woo_ce_escape_csv_value( $array_value, $export->delimiter, $export->escape_formatting );
								}
							}
						} else {
							if( in_array( $export->export_format, array( 'csv' ) ) )
								$output .= woo_ce_escape_csv_value( $product->$key, $export->delimiter, $export->escape_formatting );
						}
					}
					if( in_array( $export->export_format, array( 'csv' ) ) )
						$output .= $separator;
				}

				if( in_array( $export->export_format, array( 'csv' ) ) )
					$output = substr( $output, 0, -1 ) . "\n";
			}
		}
		unset( $products, $product );
	}
	return $output;

}

// Returns Product Categories associated to a specific Product
function woo_ce_get_product_assoc_categories( $product_id = 0, $parent_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_cat';
	// Return Product Categories of Parent if this is a Variation
	$categories = array();
	if( !empty( $parent_id ) )
		$product_id = $parent_id;
	if( !empty( $product_id ) )
		$categories = wp_get_object_terms( $product_id, $term_taxonomy );
	if( !empty( $categories ) && !is_wp_error( $categories ) ) {
		$size = apply_filters( 'woo_ce_get_product_assoc_categories_size', count( $categories ) );
		for( $i = 0; $i < $size; $i++ ) {
			if( $categories[$i]->parent == '0' ) {
				$output .= $categories[$i]->name . $export->category_separator;
			} else {
				// Check if Parent -> Child
				$parent_category = get_term( $categories[$i]->parent, $term_taxonomy );
				// Check if Parent -> Child -> Subchild
				if( $parent_category->parent == '0' ) {
					$output .= $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
					$output = str_replace( $parent_category->name . $export->category_separator, '', $output );
				} else {
					$root_category = get_term( $parent_category->parent, $term_taxonomy );
					$output .= $root_category->name . '>' . $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
					$output = str_replace( array(
						$root_category->name . '>' . $parent_category->name . $export->category_separator,
						$parent_category->name . $export->category_separator
					), '', $output );
				}
				unset( $root_category, $parent_category );
			}
		}
		$output = substr( $output, 0, -1 );
	} else {
		$output .= __( 'Uncategorized', 'woocommerce-exporter' );
	}
	return $output;

}

// Returns Product Tags associated to a specific Product
function woo_ce_get_product_assoc_tags( $product_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_tag';
	$tags = wp_get_object_terms( $product_id, $term_taxonomy );
	if( !empty( $tags ) && is_wp_error( $tags ) == false ) {
		$size = count( $tags );
		for( $i = 0; $i < $size; $i++ ) {
			if( $tag = get_term( $tags[$i]->term_id, $term_taxonomy ) )
				$output .= $tag->name . $export->category_separator;
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns the Featured Image associated to a specific Product
function woo_ce_get_product_assoc_featured_image( $product_id = 0 ) {

	$output = '';
	if( !empty( $product_id ) ) {
		$thumbnail_id = get_post_meta( $product_id, '_thumbnail_id', true );
		if( !empty( $thumbnail_id ) ) {
			$output = wp_get_attachment_url( $thumbnail_id );
		}
	}
	return $output;

}

// Returns the Product Galleries associated to a specific Product
function woo_ce_get_product_assoc_product_gallery( $product_id = 0, $image_format = 'full' ) {

	global $export;

	if( !empty( $product_id ) ) {
		$images = get_post_meta( $product_id, '_product_image_gallery', true );
		if( !empty( $images ) ) {
			$images = explode( ',', $images );
			$size = count( $images );
			for( $i = 0; $i < $size; $i++ ) {
				// Post ID
				if( $export->gallery_formatting == 0 ) {
					continue;
				// Media URL
				} else {
					if( $image_format == 'full' )
						$images[$i] = wp_get_attachment_url( $images[$i] );
					else if( $image_format == 'thumbnail' )
						$images[$i] = wp_get_attachment_thumb_url( $images[$i] );
				}
			}
			$output = implode( $export->category_separator, $images );
			return $output;
		}
	}

}

// Returns the Product Type of a specific Product
function woo_ce_get_product_assoc_type( $product_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_type';
	$types = wp_get_object_terms( $product_id, $term_taxonomy );
	if( empty( $types ) )
		$types = array( get_term_by( 'name', 'simple', $term_taxonomy ) );
	if( $types ) {
		$size = count( $types );
		for( $i = 0; $i < $size; $i++ ) {
			$type = get_term( $types[$i]->term_id, $term_taxonomy );
			$output .= woo_ce_format_product_type( $type->name ) . $export->category_separator;
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns the Shipping Class of a specific Product
function woo_ce_get_product_assoc_shipping_class( $product_id = 0 ) {

	global $export;

	$output = '';
	$term_taxonomy = 'product_shipping_class';
	$types = wp_get_object_terms( $product_id, $term_taxonomy );
	if( empty( $types ) )
		$types = get_term_by( 'name', 'simple', $term_taxonomy );
	if( !empty( $types ) ) {
		$size = count( $types );
		for( $i = 0; $i < $size; $i++ ) {
			$type = get_term( $types[$i]->term_id, $term_taxonomy );
			if( is_wp_error( $type ) !== true )
				$output .= $type->name . $export->category_separator;
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns the Up-Sell associated to a specific Product
function woo_ce_get_product_assoc_upsell_ids( $product_id = 0 ) {

	global $export;

	$output = '';
	if( $product_id ) {
		$upsell_ids = get_post_meta( $product_id, '_upsell_ids', true );
		// Convert Product ID to Product SKU as per Up-Sells Formatting
		if( $export->upsell_formatting == 1 && !empty( $upsell_ids ) ) {
			$size = count( $upsell_ids );
			for( $i = 0; $i < $size; $i++ ) {
				$upsell_ids[$i] = get_post_meta( $upsell_ids[$i], '_sku', true );
				if( empty( $upsell_ids[$i] ) )
					unset( $upsell_ids[$i] );
			}
			// 'reindex' array
			$upsell_ids = array_values( $upsell_ids );
		}
		$output = woo_ce_convert_product_ids( $upsell_ids );
	}
	return $output;

}

// Returns the Cross-Sell associated to a specific Product
function woo_ce_get_product_assoc_crosssell_ids( $product_id = 0 ) {

	global $export;

	$output = '';
	if( $product_id ) {
		$crosssell_ids = get_post_meta( $product_id, '_crosssell_ids', true );
		// Convert Product ID to Product SKU as per Cross-Sells Formatting
		if( $export->crosssell_formatting == 1 && !empty( $crosssell_ids ) ) {
			$size = count( $crosssell_ids );
			for( $i = 0; $i < $size; $i++ ) {
				$crosssell_ids[$i] = get_post_meta( $crosssell_ids[$i], '_sku', true );
				// Remove Cross-Sell if SKU is empty
				if( empty( $crosssell_ids[$i] ) )
					unset( $crosssell_ids[$i] );
			}
			// 'reindex' array
			$crosssell_ids = array_values( $crosssell_ids );
		}
		$output = woo_ce_convert_product_ids( $crosssell_ids );
	}
	return $output;
	
}

// Returns Product Attributes associated to a specific Product
function woo_ce_get_product_assoc_attributes( $product_id = 0, $attribute = array(), $type = 'product' ) {

	global $export;

	$output = '';
	if( $product_id ) {
		$terms = array();
		if( $type == 'product' ) {
			if( $attribute['is_taxonomy'] == 1 )
				$term_taxonomy = $attribute['name'];
		} else if( $type == 'global' ) {
			$term_taxonomy = 'pa_' . $attribute->attribute_name;
		}
		$terms = wp_get_object_terms( $product_id, $term_taxonomy );
		if( !empty( $terms ) && is_wp_error( $terms ) == false ) {
			$size = count( $terms );
			for( $i = 0; $i < $size; $i++ )
				$output .= $terms[$i]->name . $export->category_separator;
			unset( $terms );
		}
		$output = substr( $output, 0, -1 );
	}
	return $output;

}

// Returns File Downloads associated to a specific Product
function woo_ce_get_product_assoc_download_files( $product_id = 0, $type = 'url' ) {

	global $export;

	$output = '';
	if( $product_id ) {
		if( version_compare( woo_get_woo_version(), '2.0', '>=' ) ) {
			// If WooCommerce 2.0+ is installed then use new _downloadable_files Post meta key
			if( $file_downloads = maybe_unserialize( get_post_meta( $product_id, '_downloadable_files', true ) ) ) {
				foreach( $file_downloads as $file_download ) {
					if( $type == 'url' )
						$output .= $file_download['file'] . $export->category_separator;
					else if( $type == 'name' )
						$output .= $file_download['name'] . $export->category_separator;
				}
				unset( $file_download, $file_downloads );
			}
			$output = substr( $output, 0, -1 );
		} else {
			// If WooCommerce -2.0 is installed then use legacy _file_paths Post meta key
			if( $file_downloads = maybe_unserialize( get_post_meta( $product_id, '_file_paths', true ) ) ) {
				foreach( $file_downloads as $file_download ) {
					if( $type == 'url' )
						$output .= $file_download . $export->category_separator;
				}
				unset( $file_download, $file_downloads );
			}
			$output = substr( $output, 0, -1 );
		}
	}
	return $output;

}

function woo_ce_format_product_visibility( $product_id = 0, $visibility = '' ) {

	$output = '';
	// Check for empty default for Visibility
	if( empty( $visibility ) ) {
		$visibility = 'visible';
		if( !empty( $product_id ) ) {
			// Fall back to checking Term Taxonomy
			$term_taxonomy = 'product_visibility';
			$args = array(
				'fields' => 'names'
			);
			$terms = wp_get_object_terms( $product_id, $term_taxonomy, $args );
			if( !empty( $terms ) && is_wp_error( $terms ) == false ) {
				// Just for fun we have to combine Terms to decipher the Visibility
				if( in_array( 'exclude-from-search', $terms ) && in_array( 'exclude-from-catalog', $terms ) )
					$visibility = 'hidden';
				else if( in_array( 'exclude-from-search', $terms ) )
					$visibility = 'catalog';
				else if( in_array( 'exclude-from-catalog', $terms ) )
					$visibility = 'search';
			}
		}
	}
	switch( $visibility ) {

		default:
		case 'visible':
			if( apply_filters( 'woo_ce_format_product_visibility_legacy_labels', false ) )
				$output = __( 'Catalog & Search', 'woocommerce-exporter' );
			else
				$output = __( 'Shop and search results', 'woocommerce-exporter' );
			break;

		case 'catalog':
			if( apply_filters( 'woo_ce_format_product_visibility_legacy_labels', false ) )
				$output = __( 'Catalog', 'woocommerce-exporter' );
			else
				$output = __( 'Shop only', 'woocommerce-exporter' );
			break;

		case 'search':
			if( apply_filters( 'woo_ce_format_product_visibility_legacy_labels', false ) )
				$output = __( 'Search', 'woocommerce-exporter' );
			else
				$output = __( 'Search results only', 'woocommerce-exporter' );
			break;

		case 'hidden':
			$output = __( 'Hidden', 'woocommerce-exporter' );
			break;

	}
	return $output;

}

function woo_ce_format_product_allow_backorders( $allow_backorders = '' ) {

	$output = '';
	if( !empty( $allow_backorders ) ) {
		switch( $allow_backorders ) {

			case 'yes':
			case 'no':
				$output = woo_ce_format_switch( $allow_backorders );
				break;

			case 'notify':
				$output = __( 'Notify', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_download_type( $download_type = '' ) {

	$output = __( 'Standard', 'woocommerce-exporter' );
	if( !empty( $download_type ) ) {
		switch( $download_type ) {

			case 'application':
				$output = __( 'Application', 'woocommerce-exporter' );
				break;

			case 'music':
				$output = __( 'Music', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_stock_status( $stock_status = '', $stock = '' ) {

	$output = '';
	if( empty( $stock_status ) && !empty( $stock ) ) {
		if( $stock )
			$stock_status = 'instock';
		else
			$stock_status = 'outofstock';
	}
	if( $stock_status ) {
		switch( $stock_status ) {

			case 'instock':
				$output = __( 'In Stock', 'woocommerce-exporter' );
				break;

			case 'outofstock':
				$output = __( 'Out of Stock', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_tax_status( $tax_status = null ) {

	$output = '';
	if( !empty( $tax_status ) ) {
		switch( $tax_status ) {
	
			case 'taxable':
				$output = __( 'Taxable', 'woocommerce-exporter' );
				break;
	
			case 'shipping':
				$output = __( 'Shipping Only', 'woocommerce-exporter' );
				break;

			case 'none':
				$output = __( 'None', 'woocommerce-exporter' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_tax_class( $tax_class = '' ) {

	global $export;

	$output = '';
	if( $tax_class ) {
		switch( $tax_class ) {

			case '*':
				$tax_class = __( 'Standard', 'woocommerce-exporter' );
				break;

			case 'reduced-rate':
				$tax_class = __( 'Reduced Rate', 'woocommerce-exporter' );
				break;

			case 'zero-rate':
				$tax_class = __( 'Zero Rate', 'woocommerce-exporter' );
				break;

		}
		$output = $tax_class;
	}
	return $output;

}

function woo_ce_format_product_type( $type_id = '' ) {

	$output = $type_id;
	if( $output ) {
		$product_types = apply_filters( 'woo_ce_format_product_types', array(
			'simple' => __( 'Simple Product', 'woocommerce' ),
			'downloadable' => __( 'Downloadable', 'woocommerce' ),
			'grouped' => __( 'Grouped Product', 'woocommerce' ),
			'virtual' => __( 'Virtual', 'woocommerce' ),
			'variable' => __( 'Variable', 'woocommerce' ),
			'external' => __( 'External/Affiliate Product', 'woocommerce' ),
			'variation' => __( 'Variation', 'woocommerce-exporter' ),
			'subscription' => __( 'Simple Subscription', 'woocommerce-exporter' ),
			'variable-subscription' => __( 'Variable Subscription', 'woocommerce-exporter' )
		) );
		if( isset( $product_types[$type_id] ) )
			$output = $product_types[$type_id];
	}
	return $output;

}

// Returns a list of WooCommerce Product Types to export process
function woo_ce_get_product_types() {

	$term_taxonomy = 'product_type';
	$args = array(
		'hide_empty' => 0
	);

	// Allow other developers to bake in their own filters
	$args = apply_filters( 'woo_ce_get_product_types_args', $args );

	$types = get_terms( $term_taxonomy, $args );
	if( !empty( $types ) && is_wp_error( $types ) == false ) {
		$output = array();
		$size = count( $types );
		for( $i = 0; $i < $size; $i++ ) {
			$output[$types[$i]->slug] = array(
				'name' => ucfirst( $types[$i]->name ),
				'count' => $types[$i]->count
			);
			// Override the Product Type count for Downloadable and Virtual
			if( in_array( $types[$i]->slug, array( 'downloadable', 'virtual' ) ) ) {
				if( $types[$i]->slug == 'downloadable' ) {
					$args = array(
						'meta_key' => '_downloadable',
						'meta_value' => 'yes'
					);
				} else if( $types[$i]->slug == 'virtual' ) {
					$args = array(
						'meta_key' => '_virtual',
						'meta_value' => 'yes'
					);
				}
				$output[$types[$i]->slug]['count'] = woo_ce_get_product_type_count( 'product', $args );
			}
		}
		$output['variation'] = array(
			'name' => __( 'Variation', 'woocommerce-exporter' ),
			'count' => woo_ce_get_product_type_count( 'product_variation' )
		);

		// Allow Plugin/Theme authors to add support for additional Product Types
		$output = apply_filters( 'woo_ce_get_product_types_output', $output );

		asort( $output );
		return $output;
	}

}

function woo_ce_get_product_type_count( $post_type = 'product', $args = array() ) {

	$defaults = array(
		'post_type' => $post_type,
		'posts_per_page' => 1,
		'fields' => 'ids'
	);
	$args = wp_parse_args( $args, $defaults );
	$product_ids = new WP_Query( $args );
	$size = $product_ids->found_posts;

	// Allow Plugin/Theme authors to override Product Type counts as needed
	$size = apply_filters( 'woo_ce_get_product_type_count', $size, $post_type );

	return $size;

}

// Returns a list of WooCommerce Product Attributes to export process
function woo_ce_get_product_attributes( $slice = '' ) {

	if( apply_filters( 'woo_ce_enable_product_attributes', true ) == false )
		return false; 

	global $wpdb;

	$output = array();
	$attributes_sql = "SELECT * FROM `" . $wpdb->prefix . "woocommerce_attribute_taxonomies`";
	$attributes = $wpdb->get_results( $attributes_sql );
	$wpdb->flush();
	if( !empty( $attributes ) ) {
		$output = $attributes;
		unset( $attributes );
	} else {
		$output = ( function_exists( 'wc_get_attribute_taxonomies' ) ? wc_get_attribute_taxonomies() : array() );
	}

	return $output;

}

function woo_ce_format_product_sale_price_dates( $sale_date = '' ) {

	$output = $sale_date;
	if( $sale_date )
		$output = woo_ce_format_date( date( 'Y-m-d H:i:s', $sale_date ) );
	return $output;

}