<?php
// HTML template for Filter Subscriptions by Subscription Status widget on Store Exporter screen
function woo_ce_subscriptions_filter_by_subscription_status() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$subscription_statuses = woo_ce_get_subscription_statuses();

	ob_start(); ?>
<p><label><input type="checkbox" id="subscriptions-filters-status" /> <?php _e( 'Filter Subscriptions by Subscription Status', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-subscriptions-filters-status" class="separator">
	<ul>
		<li>
<?php if( !empty( $subscription_statuses ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Subscription Status...', 'woocommerce-exporter' ); ?>" name="subscription_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $subscription_statuses as $key => $subscription_status ) { ?>
				<option value="<?php echo $key; ?>"><?php echo $subscription_status; ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Subscription Status\'s have been found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Subscription Status options you want to filter exported Subscriptions by. Default is to include all Subscription Status options.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-subscriptions-filters-status -->
<?php
	ob_end_flush();

}

// HTML template for Filter Subscriptions by Subscription Product widget on Store Exporter screen
function woo_ce_subscriptions_filter_by_subscription_product() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$products = woo_ce_get_subscription_products();

	ob_start(); ?>
<p><label><input type="checkbox" id="subscriptions-filters-product" /> <?php _e( 'Filter Subscriptions by Subscription Product', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-subscriptions-filters-product" class="separator">
	<ul>
		<li>
<?php if( !empty( $products ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Subscription Product...', 'woocommerce-exporter' ); ?>" name="subscription_filter_product[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $products as $product ) { ?>
				<option value="<?php echo $product; ?>"><?php echo get_the_title( $product ); ?> (<?php printf( __( 'SKU: %s', 'woocommerce-exporter' ), get_post_meta( $product, '_sku', true ) ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Subscription Products were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Subscription Product you want to filter exported Subscriptions by. Default is to include all Subscription Products.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-subscriptions-filters-status -->
<?php
	ob_end_flush();

}