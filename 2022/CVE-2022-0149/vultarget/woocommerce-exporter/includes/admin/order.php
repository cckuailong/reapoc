<?php
// HTML template for Filter Orders by Order Date widget on Store Exporter screen
function woo_ce_orders_filter_by_date() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$today = date( 'l' );
	$yesterday = date( 'l', strtotime( '-1 days' ) );
	$current_month = date( 'F' );
	$last_month = date( 'F', mktime( 0, 0, 0, date( 'n' )-1, 1, date( 'Y' ) ) );
	$order_dates_variable = '-';
	$order_dates_variable_length = '';
	$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );
	$order_dates_from = '-';
	$order_dates_to = '-';
	$order_dates_first_order = woo_ce_get_order_first_date( $date_format );
	$order_dates_last_order = woo_ce_get_order_date_filter( 'today', 'from', $date_format );
	$types = woo_ce_get_option( 'order_dates_filter' );

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-date"<?php checked( !empty( $types ), true ); ?> /> <?php _e( 'Filter Orders by Order Date', 'woocommerce-exporter' ); ?></label></p>
<div id="export-orders-filters-date" class="separator">
	<ul>
		<li>
			<label><input type="radio" name="order_dates_filter" value=""<?php checked( $types, false ); ?> /> <?php _e( 'All dates', 'woocommerce-exporter' ); ?> (<?php echo $order_dates_first_order; ?> - <?php echo $order_dates_last_order; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="today"<?php checked( $types, 'today' ); ?> /> <?php _e( 'Today', 'woocommerce-exporter' ); ?> (<?php echo $today; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="yesterday"<?php checked( $types, 'yesterday' ); ?> /> <?php _e( 'Yesterday', 'woocommerce-exporter' ); ?> (<?php echo $yesterday; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="current_week"<?php checked( $types, 'current_week' ); ?> /> <?php _e( 'Current week', 'woocommerce-exporter' ); ?></label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="last_week"<?php checked( $types, 'last_week' ); ?> /> <?php _e( 'Last week', 'woocommerce-exporter' ); ?></label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="current_month" disabled="disabled" /> <?php _e( 'Current month', 'woocommerce-exporter' ); ?> (<?php echo $current_month; ?>) <span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="last_month" disabled="disabled" /> <?php _e( 'Last month', 'woocommerce-exporter' ); ?> (<?php echo $last_month; ?>) <span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label>
		</li>
<!--
		<li>
			<label><input type="radio" name="order_dates_filter" value="last_quarter" disabled="disabled" /> <?php _e( 'Last quarter', 'woocommerce-exporter' ); ?> (Nov. - Jan.)</label>
		</li>
-->
		<li>
			<label><input type="radio" name="order_dates_filter" value="variable" disabled="disabled" /> <?php _e( 'Variable date', 'woocommerce-exporter' ); ?> <span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label>
			<div style="margin-top:0.2em;">
				<?php _e( 'Last', 'woocommerce-exporter' ); ?>
				<input type="text" name="order_dates_filter_variable" class="text code" size="4" maxlength="4" value="<?php echo $order_dates_variable; ?>" disabled="disabled" />
				<select name="order_dates_filter_variable_length" style="vertical-align:top;">
					<option value="">&nbsp;</option>
					<option value="second" disabled="disabled"><?php _e( 'second(s)', 'woocommerce-exporter' ); ?></option>
					<option value="minute" disabled="disabled"><?php _e( 'minute(s)', 'woocommerce-exporter' ); ?></option>
					<option value="hour" disabled="disabled"><?php _e( 'hour(s)', 'woocommerce-exporter' ); ?></option>
					<option value="day" disabled="disabled"><?php _e( 'day(s)', 'woocommerce-exporter' ); ?></option>
					<option value="week" disabled="disabled"><?php _e( 'week(s)', 'woocommerce-exporter' ); ?></option>
					<option value="month" disabled="disabled"><?php _e( 'month(s)', 'woocommerce-exporter' ); ?></option>
					<option value="year" disabled="disabled"><?php _e( 'year(s)', 'woocommerce-exporter' ); ?></option>
				</select>
			</div>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="manual" disabled="disabled" /> <?php _e( 'Fixed date', 'woocommerce-exporter' ); ?> <span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label>
			<div style="margin-top:0.2em;">
				<input type="text" size="10" maxlength="10" id="order_dates_from" name="order_dates_from" value="<?php echo esc_attr( $order_dates_from ); ?>" class="text" disabled="disabled" /> to <input type="text" size="10" maxlength="10" id="order_dates_to" name="order_dates_to" value="<?php echo esc_attr( $order_dates_to ); ?>" class="text" disabled="disabled" />
				<p class="description"><?php _e( 'Filter the dates of Orders to be included in the export. Default is this current week.', 'woocommerce-exporter' ); ?></p>
			</div>
		</li>
	</ul>
</div>
<!-- #export-orders-filters-date -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Customer widget on Store Exporter screen
function woo_ce_orders_filter_by_customer() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-customer" /> <?php _e( 'Filter Orders by Customer', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-customer" class="separator">
	<ul>
		<li>
			<select id="order_customer" name="order_filter_customer" class="chzn-select">
				<option value=""><?php _e( 'Show all customers', 'woocommerce-exporter' ); ?></option>
			</select>
		</li>
	</ul>
	<p class="description"><?php _e( 'Filter Orders by Customer (unique e-mail address) to be included in the export. Default is to include all Orders.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-customer -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Billing Country widget on Store Exporter screen
function woo_ce_orders_filter_by_billing_country() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$countries = woo_ce_allowed_countries();

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-billing_country" /> <?php _e( 'Filter Orders by Billing Country', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-billing_country" class="separator">
	<ul>
		<li>
<?php if( !empty( $countries ) ) { ?>
			<select id="order_billing_country" name="order_filter_billing_country" class="chzn-select">
				<option value="" disabled="disabled"><?php _e( 'Show all Countries', 'woocommerce-exporter' ); ?></option>
	<?php if( $countries ) { ?>
		<?php foreach( $countries as $country_prefix => $country ) { ?>
				<option value="<?php echo $country_prefix; ?>" disabled="disabled"><?php printf( '%s (%s)', $country, $country_prefix ); ?></option>
		<?php } ?>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Countries were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Filter Orders by Billing Country to be included in the export. Default is to include all Countries.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-customer -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Shipping Country widget on Store Exporter screen
function woo_ce_orders_filter_by_shipping_country() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$countries = woo_ce_allowed_countries();

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-shipping_country" /> <?php _e( 'Filter Orders by Shipping Country', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-shipping_country" class="separator">
	<ul>
		<li>
<?php if( !empty( $countries ) ) { ?>
			<select id="order_shipping_country" name="order_filter_shipping_country" class="chzn-select">
				<option value="" disabled="disabled"><?php _e( 'Show all Countries', 'woocommerce-exporter' ); ?></option>
	<?php foreach( $countries as $country_prefix => $country ) { ?>
				<option value="<?php echo $country_prefix; ?>" disabled="disabled"><?php printf( '%s (%s)', $country, $country_prefix ); ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Countries were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Filter Orders by Shipping Country to be included in the export. Default is to include all Countries.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-customer -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by User Role widget on Store Exporter screen
function woo_ce_orders_filter_by_user_role() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$user_roles = woo_ce_get_user_roles();

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-user_role" /> <?php _e( 'Filter Orders by User Role', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-user_role" class="separator">
	<ul>
		<li>
<?php if( !empty( $user_roles ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a User Role...', 'woocommerce-exporter' ); ?>" name="order_filter_user_role[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $user_roles as $key => $user_role ) { ?>
				<option value="<?php echo $key; ?>"><?php echo ucfirst( $user_role['name'] ); ?></option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No User Roles were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the User Roles you want to filter exported Orders by. Default is to include all User Role options.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-user_role -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Order ID widget on Store Exporter screen
function woo_ce_orders_filter_by_order_id() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-id" /> <?php _e( 'Filter Orders by Order ID', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-id" class="separator">
	<ul>
		<li>
			<label for="order_filter_id"><?php _e( 'Order ID', 'woocommerce-exporter' ); ?></label>:<br />
			<input type="text" id="order_filter_id" name="order_filter_id" value="-" class="text code" disabled="disabled" />
		</li>
	</ul>
	<p class="description"><?php _e( 'Enter the Order ID\'s you want to filter exported Orders by. Multiple Order ID\'s can be entered separated by the \',\' (comma) character. Default is to include all Orders.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-user_role -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Coupon Code widget on Store Exporter screen
function woo_ce_orders_filter_by_coupon() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$args = array(
		'coupon_orderby' => 'ID',
		'coupon_order' => 'DESC'
	);
	$coupons = woo_ce_get_coupons( $args );

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-coupon" /> <?php _e( 'Filter Orders by Coupon Code', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-coupon" class="separator">
	<ul>
		<li>
<?php if( !empty( $coupons ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Coupon...', 'woocommerce-exporter' ); ?>" name="order_filter_coupon[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $coupons as $coupon ) { ?>
				<option value="<?php echo $coupon; ?>"><?php echo get_the_title( $coupon ); ?> (<?php echo woo_ce_get_coupon_code_usage( get_the_title( $coupon ) ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Coupons were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Coupon Codes you want to filter exported Orders by. Default is to include all Orders with and without assigned Coupon Codes.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-coupon -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Payment Gateway widget on Store Exporter screen
function woo_ce_orders_filter_by_payment_gateway() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-payment_gateway" /> <?php _e( 'Filter Orders by Payment Gateway', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-payment_gateway" class="separator">
	<ul>
		<li>
			<select id="order_payment_gateway" name="order_payment_gateway" disabled="disabled">
				<option value=""><?php _e( 'Show all payment gateways', 'woocommerce-exporter' ); ?></option>
			</select>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Payment Gateways you want to filter exported Orders by. Default is to include all Orders.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-coupon -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Shipping Gateway widget on Store Exporter screen
function woo_ce_orders_filter_by_shipping_method() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-shipping_method" /> <?php _e( 'Filter Orders by Shipping Method', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-shipping_method" class="separator">
	<ul>
		<li>
			<select id="order_shipping_method" name="order_shipping_method" disabled="disabled">
				<option value=""><?php _e( 'Show all shipping methods', 'woocommerce-exporter' ); ?></option>
			</select>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Shipping Methods you want to filter exported Orders by. Default is to include all Orders.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-coupon -->
<?php
	ob_end_flush();

}

// HTML template for Order Items Formatting on Store Exporter screen
function woo_ce_orders_items_formatting() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	ob_start(); ?>
<tr class="export-options order-options">
	<th><label for="order_items"><?php _e( 'Order items formatting', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<ul>
			<li>
				<label><input type="radio" name="order_items" value="combined" checked="checked" />&nbsp;<?php _e( 'Place Order Items within a grouped single Order row', 'woocommerce-exporter' ); ?></label>
				<p class="description"><?php _e( 'For example: <code>Order Items: SKU</code> cell might contain <code>SPECK-IPHONE|INCASE-NANO|-</code> for 3 Order items within an Order', 'woocommerce-exporter' ); ?></p>
			</li>
			<li>
				<label><input type="radio" name="order_items" value="unique" disabled="disabled" />&nbsp;<?php _e( 'Place Order Items on individual cells within a single Order row', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label>
				<p class="description"><?php _e( 'For example: <code>Order Items: SKU</code> would become <code>Order Item #1: SKU</code> with <codeSPECK-IPHONE</code> for the first Order item within an Order', 'woocommerce-exporter' ); ?></p>
				<p><strong><?php _e( 'Note', 'woocommerce-exporter' ); ?></strong>: <?php _e( 'Custom field labels set for Order export fields will not be applied when using this Order Item Formatting rule, if you need custom field labels use another formatting rule.', 'woocommerce-exporter' ); ?></p>
			</li>
			<li>
				<label><input type="radio" name="order_items" value="individual" disabled="disabled" />&nbsp;<?php _e( 'Place each Order Item within their own Order row', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label>
				<p class="description"><?php _e( 'For example: An Order with 3 Order items will display a single Order item on each row', 'woocommerce-exporter' ); ?></p>
			</li>
		</ul>
		<p class="description"><?php _e( 'Choose how you would like Order Items to be presented within Orders.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}

// HTML template for Max Order Items widget on Store Exporter screen
function woo_ce_orders_max_order_items() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$max_size = 10;

	ob_start(); ?>
<tr id="max_order_items_option" class="export-options order-options">
	<th>
		<label for="max_order_items"><?php _e( 'Max unique Order items', 'woocommerce-exporter' ); ?>: </label>
	</th>
	<td>
		<input type="text" id="max_order_items" name="max_order_items" size="3" class="text" value="<?php echo esc_attr( $max_size ); ?>" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'Manage the number of Order Item colums displayed when the \'Place Order Items on individual cells within a single Order row\' Order items formatting option is selected.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}

// HTML template for Order Items Types on Store Exporter screen
function woo_ce_orders_items_types() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$types = woo_ce_get_order_items_types();
	$order_items_types = woo_ce_get_option( 'order_items_types', array() );
	// Default to Line Item
	if( empty( $order_items_types ) )
		$order_items_types = array( 'line_item' );

	ob_start(); ?>
<tr class="export-options order-options">
	<th><label><?php _e( 'Order item types', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<ul>
<?php foreach( $types as $key => $type ) { ?>
			<li><label><input type="checkbox" name="order_items_types[<?php echo $key; ?>]" value="<?php echo $key; ?>" disabled="disabled" /> <?php echo ucfirst( $type ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
<?php } ?>
		</ul>
		<p class="description"><?php _e( 'Choose what Order Item types are included within the Orders export. Default is to include all Order Item types.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Order Status widget on Store Exporter screen
function woo_ce_orders_filter_by_status() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$order_statuses = woo_ce_get_order_statuses();

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-status" /> <?php _e( 'Filter Orders by Order Status', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-status" class="separator">
	<ul>
		<li>
<?php if( !empty( $order_statuses ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Order Status...', 'woocommerce-exporter' ); ?>" name="order_filter_status[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $order_statuses as $order_status ) { ?>
				<option value="<?php echo $order_status->slug; ?>"><?php echo ucfirst( $order_status->name ); ?> (<?php echo $order_status->count; ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Order Status\'s were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Order Status you want to filter exported Orders by. Default is to include all Order Status options.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-status -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Product widget on Store Exporter screen
function woo_ce_orders_filter_by_product() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$args = array(
		'hide_empty' => 1
	);
	$products = woo_ce_get_products( $args );

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-product" /> <?php _e( 'Filter Orders by Product', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-product" class="separator">
	<ul>
		<li>
<?php if( !empty( $products ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product...', 'woocommerce-exporter' ); ?>" name="order_filter_product[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $products as $product ) { ?>
				<option value="<?php echo $product; ?>" disabled="disabled"><?php echo get_the_title( $product ); ?> (<?php printf( __( 'SKU: %s', 'woocommerce-exporter' ), get_post_meta( $product, '_sku', true ) ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Products were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Products you want to filter exported Orders by. Default is to include all Products.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-product -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Product Category widget on Store Exporter screen
function woo_ce_orders_filter_by_product_category() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$args = array(
		'hide_empty' => 1
	);
	$product_categories = woo_ce_get_product_categories( $args );

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-category" /> <?php _e( 'Filter Orders by Product Category', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-category" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_categories ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Category...', 'woocommerce-exporter' ); ?>" name="order_filter_category[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_categories as $product_category ) { ?>
				<option value="<?php echo $product_category->term_id; ?>"><?php echo woo_ce_format_product_category_label( $product_category->name, $product_category->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_category->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Categories were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Categories you want to filter exported Orders by. Default is to include all Product Categories.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-category -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Product Tag widget on Store Exporter screen
function woo_ce_orders_filter_by_product_tag() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$args = array(
		'hide_empty' => 1
	);
	$product_tags = woo_ce_get_product_tags( $args );

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-tag" /> <?php _e( 'Filter Orders by Product Tag', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-tag" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_tags ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Tag...', 'woocommerce-exporter' ); ?>" name="order_filter_tag[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_tags as $product_tag ) { ?>
				<option value="<?php echo $product_tag->term_id; ?>"><?php echo $product_tag->name; ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_tag->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Tags were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Tags you want to filter exported Orders by. Default is to include all Product Tags.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-tag -->
<?php
	ob_end_flush();

}

// HTML template for Filter Orders by Brand widget on Store Exporter screen
function woo_ce_orders_filter_by_product_brand() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	// WooCommerce Brands Addon - http://woothemes.com/woocommerce/
	// WooCommerce Brands - http://proword.net/Woocommerce_Brands/
	if( woo_ce_detect_product_brands() == false )
		return;

	$args = array(
		'hide_empty' => 1
	);
	$product_brands = woo_ce_get_product_brands( $args );

	ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-brand" /> <?php _e( 'Filter Orders by Product Brand', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></p>
<div id="export-orders-filters-brand" class="separator">
	<ul>
		<li>
<?php if( !empty( $product_brands ) ) { ?>
			<select data-placeholder="<?php _e( 'Choose a Product Category...', 'woocommerce-exporter' ); ?>" name="order_filter_brand[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_brands as $product_brand ) { ?>
				<option value="<?php echo $product_brand->term_id; ?>"><?php echo woo_ce_format_product_category_label( $product_brand->name, $product_brand->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woocommerce-exporter' ), $product_brand->term_id ); ?>)</option>
	<?php } ?>
			</select>
<?php } else { ?>
			<?php _e( 'No Product Brands were found.', 'woocommerce-exporter' ); ?>
<?php } ?>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Product Brands you want to filter exported Orders by. Default is to include all Product Brands.', 'woocommerce-exporter' ); ?></p>
</div>
<!-- #export-orders-filters-brand -->
<?php
	ob_end_flush();

}

// HTML template for Order Sorting widget on Store Exporter screen
function woo_ce_order_sorting() {

	ob_start(); ?>
<p><label><?php _e( 'Order Sorting', 'woocommerce-exporter' ); ?></label></p>
<div>
	<select name="order_orderby" disabled="disabled">
		<option value="ID"><?php _e( 'Order ID', 'woocommerce-exporter' ); ?></option>
		<option value="title"><?php _e( 'Order Name', 'woocommerce-exporter' ); ?></option>
		<option value="date"><?php _e( 'Date Created', 'woocommerce-exporter' ); ?></option>
		<option value="modified"><?php _e( 'Date Modified', 'woocommerce-exporter' ); ?></option>
		<option value="rand"><?php _e( 'Random', 'woocommerce-exporter' ); ?></option>
	</select>
	<select name="order_order" disabled="disabled">
		<option value="ASC"><?php _e( 'Ascending', 'woocommerce-exporter' ); ?></option>
		<option value="DESC"><?php _e( 'Descending', 'woocommerce-exporter' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Orders within the exported file. By default this is set to export Orders by Order ID in Desending order.', 'woocommerce-exporter' ); ?></p>
</div>
<?php
	ob_end_flush();

}

// HTML template for Custom Orders widget on Store Exporter screen
function woo_ce_orders_custom_fields() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$custom_orders = '-';
	$custom_order_items = '-';

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

	ob_start(); ?>
<form method="post" id="export-orders-custom-fields" class="export-options order-options">
	<div id="poststuff">

		<div class="postbox" id="export-options">
			<h3 class="hndle"><?php _e( 'Custom Order Fields', 'woocommerce-exporter' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'To include additional custom Order and Order Item meta in the Export Orders table above fill the Orders and Order Items text box then click Save Custom Fields.', 'woocommerce-exporter' ); ?></p>
				<table class="form-table">

					<tr>
						<th>
							<label><?php _e( 'Order meta', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_orders" rows="5" cols="70" disabled="disabled"><?php echo esc_textarea( $custom_orders ); ?></textarea>
							<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
							<p class="description"><?php _e( 'Include additional custom Order meta in your export file by adding each custom Order meta name to a new line above.<br />For example: <code>Customer UA, Customer IP Address</code>', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php _e( 'Order Item meta', 'woocommerce-exporter' ); ?></label>
						</th>
						<td>
							<textarea name="custom_order_items" rows="5" cols="70" disabled="disabled"><?php echo esc_textarea( $custom_order_items ); ?></textarea>
							<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
							<p class="description"><?php _e( 'Include additional custom Order Item meta in your export file by adding each custom Order Item meta name to a new line above.<br />For example: <code>Personalized Message</code>.', 'woocommerce-exporter' ); ?></p>
						</td>
					</tr>

				</table>
				<p class="submit">
					<input type="button" class="button button-disabled" value="<?php _e( 'Save Custom Fields', 'woocommerce-exporter' ); ?>" />
				</p>
				<p class="description"><?php printf( __( 'For more information on custom Order and Order Item meta consult our <a href="%s" target="_blank">online documentation</a>.', 'woocommerce-exporter' ), $troubleshooting_url ); ?></p>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->

	</div>
	<!-- #poststuff -->
	<input type="hidden" name="action" value="update" />
</form>
<!-- #export-orders-custom-fields -->
<?php
	ob_end_flush();

}