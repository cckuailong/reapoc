<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	if( !function_exists( 'woo_ce_get_export_type_category_count' ) ) {
		function woo_ce_get_export_type_category_count() {

			$count = 0;
			$term_taxonomy = 'product_cat';

			// Check if the existing Transient exists
			$cached = get_transient( WOO_CE_PREFIX . '_category_count' );
			if( $cached == false ) {
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				set_transient( WOO_CE_PREFIX . '_category_count', $count, HOUR_IN_SECONDS );
			} else {
				$count = $cached;
			}
			return $count;

		}
	}

	/* End of: WordPress Administration */

}

// Returns a list of Category export columns
function woo_ce_get_category_fields( $format = 'full' ) {

	$export_type = 'category';

	$fields = array();
	$fields[] = array(
		'name' => 'term_id',
		'label' => __( 'Term ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Slug', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'term_url',
		'label' => __( 'Term URI', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'parent_id',
		'label' => __( 'Parent Term ID', 'woocommerce-exporter' )
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
		'name' => 'category_level_4',
		'label' => __( 'Category: Level 4', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'category_level_5',
		'label' => __( 'Category: Level 5', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'category_level_6',
		'label' => __( 'Category: Level 6', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'category_level_7',
		'label' => __( 'Category: Level 7', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Description', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'display_type',
		'label' => __( 'Display Type', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'image',
		'label' => __( 'Image', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'image_embed',
		'label' => __( 'Image (Embed)', 'woocommerce-exporter' ),
		'disabled' => 1
	);
	$fields[] = array(
		'name' => 'count',
		'label' => __( 'Count', 'woocommerce-exporter' ),
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

	// Allow Plugin/Theme authors to add support for additional columns
	$fields = apply_filters( 'woo_ce_' . $export_type . '_fields', $fields, $export_type );

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_filter_sanitize_key' );

	if( $remember = woo_ce_get_option( $export_type . '_fields', array() ) ) {
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

// Check if we should override field labels from the Field Editor
function woo_ce_override_category_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'category_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}

	return $fields;

}
add_filter( 'woo_ce_category_fields', 'woo_ce_override_category_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_category_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_category_fields();
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

// Returns a list of WooCommerce Product Categories to export process
function woo_ce_get_product_categories( $args = array() ) {

	$term_taxonomy = 'product_cat';
	$defaults = array(
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => 0
	);
	$args = wp_parse_args( $args, $defaults );
	$categories = get_terms( $term_taxonomy, $args );
	if( !empty( $categories ) && is_wp_error( $categories ) == false ) {
		foreach( $categories as $key => $category ) {
			$categories[$key]->description = woo_ce_format_description_excerpt( $category->description );
			$categories[$key]->term_url = get_term_link( $category, $term_taxonomy );

			// Category heirachy
			$categories[$key]->parent_name = '';
			// Term
			if( $categories[$key]->parent_id = $category->parent ) {
				if( $parent_category = get_term( $categories[$key]->parent_id, $term_taxonomy ) ) {
					$categories[$key]->parent_name = $parent_category->name;
				}
				unset( $parent_category );
			} else {
				$categories[$key]->parent_id = '';
			}

			$categories[$key]->image = woo_ce_get_category_thumbnail_url( $category->term_id );
			$categories[$key]->display_type = get_term_meta( $category->term_id, 'display_type', true );
		}
		return $categories;
	}

}

function woo_ce_export_dataset_override_category( $output = null, $export_type = null ) {

	global $export;

	$args = array(
		'orderby' => ( isset( $export->args['category_orderby'] ) ? $export->args['category_orderby'] : 'ID' ),
		'order' => ( isset( $export->args['category_order'] ) ? $export->args['category_order'] : 'ASC' ),
	);
	if( $categories = woo_ce_get_product_categories( $args ) ) {
		$separator = $export->delimiter;
		$size = $export->total_columns;
		$export->total_rows = count( $categories );
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
			foreach( $categories as $category ) {

				foreach( $export->fields as $key => $field ) {
					if( isset( $category->$key ) ) {
						if( in_array( $export->export_format, array( 'csv' ) ) )
							$output .= woo_ce_escape_csv_value( $category->$key, $export->delimiter, $export->escape_formatting );
					}
					if( in_array( $export->export_format, array( 'csv' ) ) )
						$output .= $separator;
				}
				if( in_array( $export->export_format, array( 'csv' ) ) )
					$output = substr( $output, 0, -1 ) . "\n";
			}
		}
		unset( $categories, $category );
	}

	return $output;

}

function woo_ce_get_category_thumbnail_url( $category_id = 0, $size = 'full' ) {

	if( $thumbnail_id = get_term_meta( $category_id, 'thumbnail_id', true ) ) {
		$image_attributes = wp_get_attachment_image_src( $thumbnail_id, $size );
		if( is_array( $image_attributes ) )
			return current( $image_attributes );
	}

}