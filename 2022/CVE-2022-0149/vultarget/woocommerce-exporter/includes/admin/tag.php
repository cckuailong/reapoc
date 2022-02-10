<?php
// HTML template for Tag Sorting widget on Store Exporter screen
function woo_ce_tag_sorting() {

	$tag_orderby = woo_ce_get_option( 'tag_orderby', 'ID' );
	$tag_order = woo_ce_get_option( 'tag_order', 'DESC' );

	ob_start(); ?>
<p><label><?php _e( 'Product Tag Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="tag_orderby">
		<option value="id"<?php selected( 'id', $tag_orderby ); ?>><?php _e( 'Term ID', 'woocommerce-exporter' ); ?></option>
		<option value="name"<?php selected( 'name', $tag_orderby ); ?>><?php _e( 'Tag Name', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="tag_order">
		<option value="ASC"<?php selected( 'ASC', $tag_order ); ?>><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $tag_order ); ?>><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Product Tags within the exported file. By default this is set to export Product Tags by Term ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
	ob_end_flush();

}