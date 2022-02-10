<?php
// HTML template for disabled Shipping Class Sorting widget on Store Exporter screen
function woo_ce_shipping_class_sorting() {

	$shipping_class_orderby = 'ID';
	$shipping_class_order = 'DESC';

	ob_start(); ?>
<p><label><?php _e( 'Shipping Class Sorting', 'woo_ce' ); ?></label></p>
<div>
	<select name="shipping_class_orderby" disabled="disabled">
		<option value="id"<?php selected( 'id', $shipping_class_orderby ); ?>><?php _e( 'Term ID', 'woo_ce' ); ?></option>
		<option value="name"<?php selected( 'name', $shipping_class_orderby ); ?>><?php _e( 'Shipping Class Name', 'woo_ce' ); ?></option>
	</select>
	<select name="shipping_class_order" disabled="disabled">
		<option value="ASC"<?php selected( 'ASC', $shipping_class_order ); ?>><?php _e( 'Ascending', 'woo_ce' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $shipping_class_order ); ?>><?php _e( 'Descending', 'woo_ce' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Shipping Classes within the exported file. By default this is set to export Shipping Classes by Term ID in Desending order.', 'woo_ce' ); ?></p>
</div>
<?php
	ob_end_flush();

}