<?php
// HTML template for Category Sorting widget on Store Exporter screen
function woo_ce_category_sorting() {

	$category_orderby = woo_ce_get_option( 'category_orderby', 'ID' );
	$category_order = woo_ce_get_option( 'category_order', 'DESC' );

	ob_start(); ?>
<p><label><?php _e( 'Category Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="category_orderby">
		<option value="id"<?php selected( 'id', $category_orderby ); ?>><?php _e( 'Term ID', 'woocommerce-exporter' ); ?></option>
		<option value="name"<?php selected( 'name', $category_orderby ); ?>><?php _e( 'Category Name', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="category_order">
		<option value="ASC"<?php selected( 'ASC', $category_order ); ?>><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $category_order ); ?>><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Categories within the exported file. By default this is set to export Categories by Term ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
	ob_end_flush();

}