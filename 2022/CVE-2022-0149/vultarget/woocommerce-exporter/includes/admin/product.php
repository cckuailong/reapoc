<?php
// HTML template for Filter Products by Product Category widget on Store Exporter screen
function woo_ce_products_filter_by_product_category() {

	$args = array(
		'hide_empty' => 1
	);
	$product_categories = woo_ce_get_product_categories( $args );

	ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-categories" /> <?php _e( 'Filter Products by Product Category', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-categories" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_categories ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Category...', 'woocommerce-exporter' ); ?>" name="product_filter_category[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_categories as $product_category ) { ?>
				<option value="<?php echo $product_category->term_id; ?>"<?php disabled( $product_category->count, 0 ); ?>><?php echo woo_ce_format_product_category_label( $product_category->name, $product_category->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_category->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Categories were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Categories you want to filter exported Products by. Product Categories not assigned to Products are hidden from view. Default is to include all Product Categories.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-categories -->
<?php
	ob_end_flush();

}

// HTML template for Filter Products by Product Tag widget on Store Exporter screen
function woo_ce_products_filter_by_product_tag() {

	$args = array(
		'hide_empty' => 1
	);
	$product_tags = woo_ce_get_product_tags( $args );

	ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-tags" /> <?php _e( 'Filter Products by Product Tag', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-tags" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_tags ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Tag...', 'woocommerce-exporter' ); ?>" name="product_filter_tag[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_tags as $product_tag ) { ?>
				<option value="<?php echo $product_tag->term_id; ?>"<?php disabled( $product_tag->count, 0 ); ?>><?php echo $product_tag->name; ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_tag->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Tags were found.', 'woocommerce-exporter' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Tags you want to filter exported Products by. Product Tags not assigned to Products are hidden from view. Default is to include all Product Tags.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-tags -->
<?php
	ob_end_flush();

}

// HTML template for Filter Products by Product Brand widget on Store Exporter screen
function woo_ce_products_filter_by_product_brand() {

	// Check if Brands is available
	if( woo_ce_detect_product_brands() == false )
		return;

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$args = array(
		'hide_empty' => 1,
		'orderby' => 'term_group'
	);
	$product_brands = woo_ce_get_product_brands( $args );

	ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-brands" /> <?php _e( 'Filter Products by Product Brands', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-products-filters-brands" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_brands ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Brand...', 'woocommerce-exporter' ); ?>" name="product_filter_brand[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_brands as $product_brand ) { ?>
				<option value="<?php echo $product_brand->term_id; ?>"<?php disabled( $product_brand->count, 0 ); ?>><?php echo woo_ce_format_product_category_label( $product_brand->name, $product_brand->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_brand->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Brands were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Brands you want to filter exported Products by. Default is to include all Product Brands.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-brands -->
<?php
	ob_end_flush();

}

// HTML template for Filter Products by Product Vendor widget on Store Exporter screen
function woo_ce_products_filter_by_product_vendor() {

	if( class_exists( 'WooCommerce_Product_Vendors' ) == false )
		return;

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$args = array(
		'hide_empty' => 1
	);
	$product_vendors = woo_ce_get_product_vendors( $args, 'full' );

	ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-vendors" /> <?php _e( 'Filter Products by Product Vendors', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-products-filters-vendors" class="separator">
<?php if( $product_vendors ) { ?>
	<ul>
	<?php foreach( $product_vendors as $product_vendor ) { ?>
		<li>
			<label><input type="checkbox" name="product_filter_vendor[<?php echo $product_vendor->term_id; ?>]" value="<?php echo $product_vendor->term_id; ?>" title="<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_vendor->term_id ); ?>"<?php disabled( $product_vendor->count, 0 ); ?> disabled="disabled" /> <?php echo $product_vendor->name; ?></label>
			<span class="description">(<?php echo $product_vendor->count; ?>)</span>
		</li>
	<?php } ?>
	</ul>
	<p class="description"><?php _e( 'Select the Product Vendors you want to filter exported Products by. Default is to include all Product Vendors.', 'woocommerce-exporter' ); ?></p>
<?php } else { ?>
	<p><?php _e( 'No Product Vendors were found.', 'woocommerce-exporter' ); ?></p>
<?php } ?>
</div>
<!-- #export-products-filters-vendors -->
<?php
	ob_end_flush();

}

// HTML template for Filter Products by Product Status widget on Store Exporter screen
function woo_ce_products_filter_by_product_status() {

	$product_statuses = get_post_statuses();
	if( !isset( $product_statuses['trash'] ) )
		$product_statuses['trash'] = __( 'Trash', 'woocommerce-exporter' );

	ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-status" /> <?php _e( 'Filter Products by Product Status', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-status" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_statuses ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Status...', 'woocommerce-exporter' ); ?>" name="product_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_statuses as $key => $product_status ) { ?>
				<option value="<?php echo $key; ?>"><?php echo $product_status; ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Status were found.', 'woocommerce-exporter' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Status options you want to filter exported Products by. Default is to include all Product Status options.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-status -->
<?php
	ob_end_flush();

}

// HTML template for Filter Products by Product Type widget on Store Exporter screen
function woo_ce_products_filter_by_product_type() {

	$product_types = woo_ce_get_product_types();

	ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-type" /> <?php _e( 'Filter Products by Product Type', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-type" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_types ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Type...', 'woocommerce-exporter' ); ?>" name="product_filter_type[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_types as $key => $product_type ) { ?>
				<option value="<?php echo $key; ?>"><?php echo woo_ce_format_product_type( $product_type['name'] ); ?> (<?php echo $product_type['count']; ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Types were found.', 'woocommerce-exporter' ); ?></li>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Type\'s you want to filter exported Products by. Default is to include all Product Types and Variations.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-type -->
<?php
	ob_end_flush();

}

// HTML template for Filter Products by Product Type widget on Store Exporter screen
function woo_ce_products_filter_by_stock_status() {

	// Store Exporter Deluxe
	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	ob_start(); ?>
<p><label><input type="checkbox" id="products-filters-stock" /> <?php _e( 'Filter Products by Stock Status', 'woocommerce-exporter' ); ?></label></p>
<div id="export-products-filters-stock" class="separator">
	<ul>
		<li value=""><label><input type="radio" name="product_filter_stock" value="" checked="checked" /><?php _e( 'Include both', 'woocommerce-exporter' ); ?></label></li>
		<li value="instock"><label><input type="radio" name="product_filter_stock" value="instock" disabled="disabled" /><?php _e( 'In stock', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
		<li value="outofstock"><label><input type="radio" name="product_filter_stock" value="outofstock" disabled="disabled" /><?php _e( 'Out of stock', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
	</ul>
	<p class="description"><?php _e( 'Select the Stock Status\'s you want to filter exported Products by. Default is to include all Stock Status\'s.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-products-filters-stock -->
<?php
	ob_end_flush();

}

// HTML template for Product Sorting widget on Store Exporter screen
function woo_ce_product_sorting() {

	$product_orderby = woo_ce_get_option( 'product_orderby', 'ID' );
	$product_order = woo_ce_get_option( 'product_order', 'DESC' );

	ob_start(); ?>
<p><label><?php _e( 'Product Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="product_orderby">
		<option value="ID"<?php selected( 'ID', $product_orderby ); ?>><?php _e( 'Product ID', 'woocommerce-exporter' ); ?></option>
		<option value="title"<?php selected( 'title', $product_orderby ); ?>><?php _e( 'Product Name', 'woocommerce-exporter' ); ?></option>
		<option value="sku"<?php selected( 'sku', $product_orderby ); ?>><?php _e( 'Product SKU', 'woocommerce-exporter' ); ?></option>
		<option value="date"<?php selected( 'date', $product_orderby ); ?>><?php _e( 'Date Created', 'woocommerce-exporter' ); ?></option>
		<option value="modified"<?php selected( 'modified', $product_orderby ); ?>><?php _e( 'Date Modified', 'woocommerce-exporter' ); ?></option>
		<option value="rand"<?php selected( 'rand', $product_orderby ); ?>><?php _e( 'Random', 'woocommerce-exporter' ); ?></option>
		<option value="menu_order"<?php selected( 'menu_order', $product_orderby ); ?>><?php _e( 'Sort Order', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="product_order">
		<option value="ASC"<?php selected( 'ASC', $product_order ); ?>><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"<?php selected( 'DESC', $product_order ); ?>><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Products within the exported file. By default this is set to export Products by Product ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
	ob_end_flush();

}

// HTML template for Up-sells formatting on Store Exporter screen
function woo_ce_products_upsell_formatting() {

	$upsell_formatting = woo_ce_get_option( 'upsell_formatting', 1 );

	ob_start(); ?>
<tr class="export-options product-options">
	<th><label for=""><?php _e( 'Up-sells formatting', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<label><input type="radio" name="product_upsell_formatting" value="0"<?php checked( $upsell_formatting, 0 ); ?> />&nbsp;<?php _e( 'Export Up-Sells as Product ID', 'woocommerce-exporter' ); ?></label><br />
		<label><input type="radio" name="product_upsell_formatting" value="1"<?php checked( $upsell_formatting, 1 ); ?> />&nbsp;<?php _e( 'Export Up-Sells as Product SKU', 'woocommerce-exporter' ); ?></label>
		<p class="description"><?php _e( 'Choose the up-sell formatting that is accepted by your WooCommerce import Plugin (e.g. Product Importer Deluxe, Product Import Suite, etc.).', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>

<?php
	ob_end_flush();

}

// HTML template for Cross-sells formatting on Store Exporter screen
function woo_ce_products_crosssell_formatting() {

	$crosssell_formatting = woo_ce_get_option( 'crosssell_formatting', 1 );

	ob_start(); ?>
<tr class="export-options product-options">
	<th><label for=""><?php _e( 'Cross-sells formatting', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<label><input type="radio" name="product_crosssell_formatting" value="0"<?php checked( $crosssell_formatting, 0 ); ?> />&nbsp;<?php _e( 'Export Cross-Sells as Product ID', 'woocommerce-exporter' ); ?></label><br />
		<label><input type="radio" name="product_crosssell_formatting" value="1"<?php checked( $crosssell_formatting, 1 ); ?> />&nbsp;<?php _e( 'Export Cross-Sells as Product SKU', 'woocommerce-exporter' ); ?></label>
		<p class="description"><?php _e( 'Choose the cross-sell formatting that is accepted by your WooCommerce import Plugin (e.g. Product Importer Deluxe, Product Import Suite, etc.).', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>

<?php
	ob_end_flush();

}

// HTML template for Custom Products widget on Store Exporter screen
function woo_ce_products_custom_fields() {

	// Store Exporter Deluxe
	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	if( $custom_products = woo_ce_get_option( 'custom_products', '' ) )
		$custom_products = implode( "\n", $custom_products );
	$custom_attributes = '';

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

	ob_start(); ?>
<form method="post" id="export-products-custom-fields" class="export-options product-options">
	<div id="poststuff">

		<div class="postbox" id="export-options product-options">
			<h3 class="hndle"><?php _e( 'Custom Product Fields', 'woocommerce-exporter' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'To include additional custom Product meta or custom Attributes in the Export Products table above fill the meta text box then click Save Custom Fields.', 'woocommerce-exporter' ); ?></p>
				<table class="form-table">

					<tr>
						<th>
							<label><?php _e( 'Product meta', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_products" rows="5" cols="70"><?php echo esc_textarea( $custom_products ); ?></textarea>
							<p class="description"><?php _e( 'Include additional custom Product meta in your export file by adding each custom Product meta name to a new line above.<br />For example: <code>Customer UA</code> (new line) <code>Customer IP Address</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php _e( 'Custom attribute', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_attributes" rows="5" cols="70" disabled="disabled"><?php echo esc_textarea( $custom_attributes ); ?></textarea><br /><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
							<p class="description"><?php _e( 'Include custom Attributes in your export file by adding each custom Attribute name to a new line above.<br />For example: <code>condition</code> (new line) <code>colour</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

					<?php do_action( 'woo_ce_products_custom_fields' ); ?>

				</table>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Save Custom Fields', 'woocommerce-exporter' ); ?>" class="button" />
				</p>
				<p class="description"><?php printf( __( 'For more information on custom Product meta and Attributes consult our <a href="%s" target="_blank">online documentation</a>.', 'woocommerce-exporter' ), $troubleshooting_url ); ?></p>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->

	</div>
	<!-- #poststuff -->
	<input type="hidden" name="action" value="update" />
</form>
<!-- #export-products-custom-fields -->
<?php
	ob_end_flush();

}

function woo_ce_export_options_product_gallery_formatting() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	ob_start(); ?>
<tr class="export-options product-options">
	<th><label for=""><?php _e( 'Product gallery formatting', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<label><input type="radio" name="product_gallery_formatting" value="0"<?php checked( 0, 0 ); ?> />&nbsp;<?php _e( 'Export Product Gallery as Attachment ID', 'woocommerce-exporter' ); ?></label><br />
		<label><input type="radio" name="product_gallery_formatting" value="1" disabled="disabled" />&nbsp;<?php _e( 'Export Product Gallery as Image URL', 'woocommerce-exporter' ); ?> <span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label><br />
		<label><input type="radio" name="product_gallery_formatting" value="2" disabled="disabled" />&nbsp;<?php _e( 'Export Product Gallery as Image filepath', 'woocommerce-exporter' ); ?> <span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label>
		<hr />
		<label><input type="radio" name="product_gallery_unique" value="0"<?php checked( 0, 0 ); ?> />&nbsp;<?php _e( 'Export Product Gallery as a single combined image cell', 'woocommerce-exporter' ); ?></label><br />
		<label><input type="radio" name="product_gallery_unique" value="1" disabled="disabled" />&nbsp;<?php _e( 'Export Product Gallery as individual image cells', 'woocommerce-exporter' ); ?> <span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label>
		<p class="description"><?php _e( 'Choose the product gallery formatting that is accepted by your WooCommerce import Plugin (e.g. Product Importer Deluxe, Product Import Suite, etc.).', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}