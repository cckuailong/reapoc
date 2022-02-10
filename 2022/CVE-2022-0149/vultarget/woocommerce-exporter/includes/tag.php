<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	if( !function_exists( 'woo_ce_get_export_type_tag_count' ) ) {
		function woo_ce_get_export_type_tag_count() {

			$count = 0;
			// Check if the existing Transient exists
			$cached = get_transient( WOO_CE_PREFIX . '_tag_count' );
			if( $cached == false ) {
				$term_taxonomy = 'product_tag';
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				set_transient( WOO_CE_PREFIX . '_tag_count', $count, HOUR_IN_SECONDS );
			} else {
				$count = $cached;
			}

			return $count;

		}
	}

	/* End of: WordPress Administration */

}

// Returns a list of Product Tag export columns
function woo_ce_get_tag_fields( $format = 'full' ) {

	$export_type = 'tag';

	$fields = array();
	$fields[] = array(
		'name' => 'term_id',
		'label' => __( 'Term ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'name',
		'label' => __( 'Tag Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Tag Slug', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'term_url',
		'label' => __( 'Term URI', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Description', 'woocommerce-exporter' )
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

	// Allow Plugin/Theme authors to add support for additional columns
	$fields = apply_filters( 'woo_ce_' . $export_type . '_fields', $fields, $export_type );

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
function woo_ce_override_tag_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'tag_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}

	return $fields;

}
add_filter( 'woo_ce_tag_fields', 'woo_ce_override_tag_field_labels', 11 );

// Returns the export column header label based on an export column slug
function woo_ce_get_tag_field( $name = null, $format = 'name' ) {

	$output = '';
	if( $name ) {
		$fields = woo_ce_get_tag_fields();
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

// Returns a list of WooCommerce Product Tags to export process
function woo_ce_get_product_tags( $args = array() ) {

	$term_taxonomy = 'product_tag';
	$defaults = array(
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => 0
	);
	$args = wp_parse_args( $args, $defaults );
	$tags = get_terms( $term_taxonomy, $args );
	if( !empty( $tags ) && is_wp_error( $tags ) == false ) {
		$size = count( $tags );
		for( $i = 0; $i < $size; $i++ ) {
			$tags[$i]->term_url = get_term_link( $tags[$i], $term_taxonomy );
			$tags[$i]->description = woo_ce_format_description_excerpt( $tags[$i]->description );
			$tags[$i]->disabled = 0;
			if( $tags[$i]->count == 0 )
				$tags[$i]->disabled = 1;
		}

		return $tags;

	}

}

function woo_ce_export_dataset_override_tag( $output = null, $export_type = null ) {

	global $export;

	$args = array(
		'orderby' => ( isset( $export->args['tag_orderby'] ) ? $export->args['tag_orderby'] : 'ID' ),
		'order' => ( isset( $export->args['tag_order'] ) ? $export->args['tag_order'] : 'ASC' ),
	);
	if( $tags = woo_ce_get_product_tags( $args ) ) {
		$separator = $export->delimiter;
		$size = $export->total_columns;
		$export->total_rows = count( $tags );
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
			foreach( $tags as $tag ) {

				foreach( $export->fields as $key => $field ) {
					if( isset( $tag->$key ) ) {
						if( in_array( $export->export_format, array( 'csv' ) ) )
							$output .= woo_ce_escape_csv_value( $tag->$key, $export->delimiter, $export->escape_formatting );
					}
					if( in_array( $export->export_format, array( 'csv' ) ) )
						$output .= $separator;
				}
				if( in_array( $export->export_format, array( 'csv' ) ) )
					$output = substr( $output, 0, -1 ) . "\n";
			}
		}
		unset( $tags, $tag );
	}

	return $output;

}