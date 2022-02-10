<?php
// HTML template for Coupon Sorting widget on Store Exporter screen
function woo_ce_brand_sorting() {

	$orderby = woo_ce_get_option( 'brand_orderby', 'ID' );
	$order = woo_ce_get_option( 'brand_order', 'DESC' );

	ob_start(); ?>
<p><label><?php _e( 'Brand Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="brand_orderby" disabled="disabled">
		<option value="id"><?php _e( 'Term ID', 'woocommerce-exporter' ); ?></option>
		<option value="name"><?php _e( 'Brand Name', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="brand_order" disabled="disabled">
		<option value="ASC"><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Brands within the exported file. By default this is set to export Product Brands by Term ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
	ob_end_flush();

}