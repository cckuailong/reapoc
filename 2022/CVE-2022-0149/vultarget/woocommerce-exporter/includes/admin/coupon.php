<?php
// HTML template for disabled Coupon Sorting widget on Store Exporter screen
function woo_ce_coupon_sorting() {

	ob_start(); ?>
<p><label><?php _e( 'Coupon Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="coupon_orderby" disabled="disabled">
		<option value="ID"><?php _e( 'Coupon ID', 'woocommerce-exporter' ); ?></option>
		<option value="title"><?php _e( 'Coupon Code', 'woocommerce-exporter' ); ?></option>
		<option value="date"><?php _e( 'Date Created', 'woocommerce-exporter' ); ?></option>
		<option value="modified"><?php _e( 'Date Modified', 'woocommerce-exporter' ); ?></option>
		<option value="rand"><?php _e( 'Random', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="coupon_order" disabled="disabled">
		<option value="ASC"><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Coupons within the exported file. By default this is set to export Coupons by Coupon ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
	ob_end_flush();

}