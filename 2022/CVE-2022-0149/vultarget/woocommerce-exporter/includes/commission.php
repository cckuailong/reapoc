<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	if( !function_exists( 'woo_ce_get_export_type_commission_count' ) ) {
		function woo_ce_get_export_type_commission_count( $count = 0, $export_type = '', $args ) {

			if( $export_type <> 'commission' )
				return $count;

			$count = 0;
			// Check if the existing Transient exists
			$cached = get_transient( WOO_CE_PREFIX . '_commission_count' );
			if( $cached == false ) {
				$post_type = 'shop_commission';
				if( post_type_exists( $post_type ) ) {
					$count = wp_count_posts( $post_type );
				} else if( class_exists( 'WC_Vendors' ) ) {
					// Check for WC-Vendors
					global $wpdb;
					$count = $wpdb->get_var( 'SELECT COUNT(id) FROM `' . $wpdb->prefix . 'pv_commission`' );
				}
				set_transient( WOO_CE_PREFIX . '_commission_count', $count, HOUR_IN_SECONDS );
			} else {
				$count = $cached;
			}

			return $count;

		}
		add_filter( 'woo_ce_get_export_type_count', 'woo_ce_get_export_type_commission_count', 10, 3 );
	}

	// Returns date of first Commission received, any status
	function woo_ce_get_commission_first_date() {

		$output = date( 'd/m/Y', mktime( 0, 0, 0, date( 'n' ), 1 ) );
		$post_type = 'shop_commission';
		$args = array(
			'post_type' => $post_type,
			'orderby' => 'post_date',
			'order' => 'ASC',
			'numberposts' => 1
		);
		$commissions = get_posts( $args );
		if( $commissions ) {
			$commission = strtotime( $commissions[0]->post_date );
			$output = date( 'd/m/Y', $commission );
			unset( $commissions, $commission );
		}

		return $output;

	}

	// HTML template for disabled Commission Sorting widget on Store Exporter screen
	function woo_ce_commission_sorting() {

		ob_start(); ?>
<p><label><?php _e( 'Commission Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="commission_orderby" disabled="disabled">
		<option value="ID"><?php _e( 'Commission ID', 'woocommerce-exporter' ); ?></option>
		<option value="title"><?php _e( 'Commission Title', 'woocommerce-exporter' ); ?></option>
		<option value="date"><?php _e( 'Date Created', 'woocommerce-exporter' ); ?></option>
		<option value="modified"><?php _e( 'Date Modified', 'woocommerce-exporter' ); ?></option>
		<option value="rand"><?php _e( 'Random', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="commission_order" disabled="disabled">
		<option value="ASC"><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Commissions within the exported file. By default this is set to export Commissions by Commission ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	// HTML template for disabled Filter Commissions by Product Vendor widget on Store Exporter screen
	function woo_ce_commissions_filter_by_product_vendor() {

		$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
		$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

		$product_vendors = woo_ce_get_product_vendors( array(), 'full' );

		ob_start(); ?>
<p><label><input type="checkbox" id="commissions-filters-product_vendor" /> <?php _e( 'Filter Commissions by Product Vendors', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-commissions-filters-product_vendor" class="separator">
<?php if( $product_vendors ) { ?>
	<ul>
	<?php foreach( $product_vendors as $product_vendor ) { ?>
		<li>
			<label><input type="checkbox" name="commission_filter_product_vendor[<?php echo $product_vendor->term_id; ?>]" value="<?php echo $product_vendor->term_id; ?>" title="<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_vendor->term_id ); ?>"<?php disabled( $product_vendor->count, 0 ); ?> disabled="disabled" /> <?php echo $product_vendor->name; ?></label>
			<span class="description">(<?php echo $product_vendor->count; ?>)</span>
		</li>
	<?php } ?>
	</ul>
	<p class="description"><?php _e( 'Select the Product Vendors you want to filter exported Commissions by. Default is to include all Product Vendors.', 'woocommerce-exporter' ); ?></p>
<?php } else { ?>
	<p><?php _e( 'No Product Vendors were found.', 'woocommerce-exporter' ); ?></p>
<?php } ?>
</div>
<!-- #export-commissions-filters-product_vendor -->
<?php
		ob_end_flush();

	}

	// HTML template for disabled Filter Commissions by Commission Status widget on Store Exporter screen
	function woo_ce_commissions_filter_by_commission_status() {

		$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
		$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

		ob_start(); ?>
<p><label><input type="checkbox" id="commissions-filters-commission_status" /> <?php _e( 'Filter Commissions by Commission Status', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-commissions-filters-commission_status" class="separator">
	<ul>
		<li>
			<label><input type="checkbox" name="commission_filter_commission_status[]" value="unpaid"<?php disabled( woo_ce_commissions_stock_status_count( 'unpaid' ), 0 ); ?> disabled="disabled" /> <?php _e( 'Unpaid', 'woocommerce-exporter' ); ?></label>
			<span class="description">(<?php echo woo_ce_commissions_stock_status_count( 'unpaid' ); ?>)</span>
		</li>
		<li>
			<label><input type="checkbox" name="commission_filter_commission_status[]" value="paid"<?php disabled( woo_ce_commissions_stock_status_count( 'paid' ), 0 ); ?> disabled="disabled" /> <?php _e( 'Paid', 'woocommerce-exporter' ); ?></label>
			<span class="description">(<?php echo woo_ce_commissions_stock_status_count( 'paid' ); ?>)</span>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Commission Status you want to filter exported Commissions by. Default is to include all Commission Statuses.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-commissions-filters-commission_status -->
<?php
		ob_end_flush();

	}

	// HTML template for displaying the number of each export type filter on the Archives screen
	function woo_ce_commissions_stock_status_count( $type = '' ) {

		$output = 0;
		$post_type = 'shop_commission';
		$meta_key = '_paid_status';
		$args = array(
			'post_type' => $post_type,
			'meta_key' => $meta_key,
			'meta_value' => null,
			'numberposts' => -1,
			'fields' => 'ids'
		);
		if( $type )
			$args['meta_value'] = $type;
		$commission_ids = new WP_Query( $args );
		if( !empty( $commission_ids->posts ) )
			$output = count( $commission_ids->posts );

		return $output;

	}

	/* End of: WordPress Administration */

}

function woo_ce_get_commission_fields( $format = 'full' ) {

	$export_type = 'commission';

	$fields = array();
	$fields[] = array(
		'name' => 'ID',
		'label' => __( 'Commission ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_date',
		'label' => __( 'Commission Date', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'title',
		'label' => __( 'Commission Title', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_id',
		'label' => __( 'Product ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_name',
		'label' => __( 'Product Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_sku',
		'label' => __( 'Product SKU', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_vendor_id',
		'label' => __( 'Product Vendor ID', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'product_vendor_name',
		'label' => __( 'Product Vendor Name', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'commission_amount',
		'label' => __( 'Commission Amount', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'paid_status',
		'label' => __( 'Commission Status', 'woocommerce-exporter' )
	);
	$fields[] = array(
		'name' => 'post_status',
		'label' => __( 'Post Status', 'woocommerce-exporter' )
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