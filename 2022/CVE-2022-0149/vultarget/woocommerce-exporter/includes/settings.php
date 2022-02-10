<?php
function woo_ce_export_settings_quicklinks() {

	ob_start(); ?>
<li>| <a href="#xml-settings"><?php _e( 'XML Settings', 'woocommerce-exporter' ); ?></a> |</li>
<li><a href="#rss-settings"><?php _e( 'RSS Settings', 'woocommerce-exporter' ); ?></a> |</li>
<li><a href="#scheduled-exports"><?php _e( 'Scheduled Exports', 'woocommerce-exporter' ); ?></a> |</li>
<li><a href="#cron-exports"><?php _e( 'CRON Exports', 'woocommerce-exporter' ); ?></a> |</li>
<li><a href="#orders-screen"><?php _e( 'Orders Screen', 'woocommerce-exporter' ); ?></a> |</li>
<li><a href="#export-triggers"><?php _e( 'Export Triggers', 'woocommerce-exporter' ); ?></a></li>
<?php
	ob_end_flush();

}

function woo_ce_export_settings_csv() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	ob_start(); ?>
<tr>
	<th>
		<label for="header_formatting"><?php _e( 'Header formatting', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" name="header_formatting" value="1"<?php checked( 1, 1 ); ?> />&nbsp;<?php _e( 'Include export field column headers', 'woocommerce-exporter' ); ?></label></li>
			<li><label><input type="radio" name="header_formatting" value="0" disabled="disabled" />&nbsp;<?php _e( 'Do not include export field column headers', 'woocommerce-exporter' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></li>
		</ul>
		<p class="description"><?php _e( 'Choose the header format that suits your spreadsheet software (e.g. Excel, OpenOffice, etc.). This rule applies to CSV, XLS and XLSX export types.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}

// Returns the disabled HTML template for the Enable CRON and Secret Export Key options for the Settings screen
function woo_ce_export_settings_extend() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	// RSS settings
	$rss_title = __( 'Title of your RSS feed', 'woocommerce-exporter' );
	$rss_link = __( 'URL to your RSS feed', 'woocommerce-exporter' );
	$rss_description = __( 'Summary description of your RSS feed', 'woocommerce-exporter' );

	// Scheduled exports
	$auto_commence_date = date( 'd/m/Y H:i', current_time( 'timestamp', 1 ) );
	// Override to enable the Export Type to include all export types
	$types = array(
		'product' => __( 'Products', 'woocommerce-exporter' ),
		'category' => __( 'Categories', 'woocommerce-exporter' ),
		'tag' => __( 'Tags', 'woocommerce-exporter' ),
		'brand' => __( 'Brands', 'woocommerce-exporter' ),
		'order' => __( 'Orders', 'woocommerce-exporter' ),
		'customer' => __( 'Customers', 'woocommerce-exporter' ),
		'user' => __( 'Users', 'woocommerce-exporter' ),
		'coupon' => __( 'Coupons', 'woocommerce-exporter' ),
		'subscription' => __( 'Subscriptions', 'woocommerce-exporter' ),
		'product_vendor' => __( 'Product Vendors', 'woocommerce-exporter' ),
		'shipping_class' => __( 'Shipping Classes', 'woocommerce-exporter' )
	);
	$order_statuses = woo_ce_get_order_statuses();
	$product_types = woo_ce_get_product_types();
	$args = array(
		'hide_empty' => 1
	);
	$product_categories = woo_ce_get_product_categories( $args );
	$product_tags = woo_ce_get_product_tags( $args );

	$auto_interval = 1440;
	$auto_format = 'csv';
	$order_filter_date_variable = '';

	// Send to e-mail
	$email_to = get_option( 'admin_email', '' );
	$email_subject = __( '[%store_name%] Export: %export_type% (%export_filename%)', 'woocommerce-exporter' );

	// Post to remote URL
	$post_to = 'http://www.domain.com/custom-post-form-processor.php';

	// Export to FTP
	$ftp_method_host = 'ftp.domain.com';
	$ftp_method_port = '';
	$ftp_method_protocol = 'ftp';
	$ftp_method_user = 'export';
	$ftp_method_pass = '';
	$ftp_method_path = 'wp-content/uploads/export/';
	$ftp_method_filename = 'fixed-filename';
	$ftp_method_passive = 'auto';
	$ftp_method_timeout = '';

	$scheduled_fields = 'all';

	// CRON exports
	$secret_key = '-';
	$cron_fields = 'all';

	$cron_fields = 'all';

	// Orders Screen
	$order_actions_csv = 0;
	$order_actions_tsv = 0;
	$order_actions_xml = 0;
	$order_actions_xls = 0;
	$order_actions_xlsx = 0;

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

	ob_start(); ?>
<tr id="xml-settings">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-media-code"></div>&nbsp;<?php _e( 'XML Settings', 'woocommerce-exporter' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<?php _e( 'Attribute display', 'woocommerce-exporter' ); ?>
	</th>
	<td>
		<ul>
			<li><label><input type="checkbox" name="xml_attribute_url" value="1" disabled="disabled" /> <?php _e( 'Site Address', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_title" value="1" disabled="disabled" /> <?php _e( 'Site Title', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_date" value="1" disabled="disabled" /> <?php _e( 'Export Date', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_time" value="1" disabled="disabled" /> <?php _e( 'Export Time', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_export" value="1" disabled="disabled" /> <?php _e( 'Export Type', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_orderby" value="1" disabled="disabled" /> <?php _e( 'Export Order By', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_order" value="1" disabled="disabled" /> <?php _e( 'Export Order', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_limit" value="1" disabled="disabled" /> <?php _e( 'Limit Volume', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_offset" value="1" disabled="disabled" /> <?php _e( 'Volume Offset', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
		</ul>
		<p class="description"><?php _e( 'Control the visibility of different attributes in the XML export.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<!-- #xml-settings -->

<tr id="rss-settings">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-media-code"></div>&nbsp;<?php _e( 'RSS Settings', 'woocommerce-exporter' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_title"><?php _e( 'Title element', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<input name="rss_title" type="text" id="rss_title" value="<?php echo esc_attr( $rss_title ); ?>" class="regular-text" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
		<p class="description"><?php _e( 'Defines the title of the data feed (e.g. Product export for WordPress Shop).', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_link"><?php _e( 'Link element', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<input name="rss_link" type="text" id="rss_link" value="<?php echo esc_attr( $rss_link ); ?>" class="regular-text" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
		<p class="description"><?php _e( 'A link to your website, this doesn\'t have to be the location of the RSS feed.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_description"><?php _e( 'Description element', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<input name="rss_description" type="text" id="rss_description" value="<?php echo esc_attr( $rss_description ); ?>" class="large-text" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
		<p class="description"><?php _e( 'A description of your data feed.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<!-- #rss-settings -->

<tr id="scheduled-exports">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3>
			<div class="dashicons dashicons-calendar"></div>&nbsp;<?php _e( 'Scheduled Exports', 'woocommerce-exporter' ); ?>
		</h3>
		<p class="description"><?php _e( 'Automatically generate exports and apply filters to export just what you need.<br />Adjusting options within the Scheduling sub-section will after clicking Save Changes refresh the scheduled export engine, editing filters, formats, methods, etc. will not affect the scheduling of the current scheduled export.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr>
	<th><label for="enable_auto"><?php _e( 'Enable scheduled exports', 'woocommerce-exporter' ); ?></label></th>
	<td>
		<select id="enable_auto" name="enable_auto" disabled="disabled">
			<option value="1" disabled="disabled"><?php _e( 'Yes', 'woocommerce-exporter' ); ?></option>
			<option value="0" selected="selected"><?php _e( 'No', 'woocommerce-exporter' ); ?></option>
		</select>
		<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'Enabling Scheduled Exports will trigger automated exports at the intervals specified under Scheduling within each scheduled export. You can suspend individual scheduled exports by changing the Post Status.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>

<tr>
	<th>&nbsp;</th>
	<td>
		<p>
			<a href="<?php echo add_query_arg( array( 'tab' => 'scheduled_export' ) ); ?>"><?php _e( 'View Scheduled Exports', 'woocommerce-exporter' ); ?></a>
		</p>
	</td>
</tr>

<tr id="cron-exports">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-clock"></div>&nbsp;<?php _e( 'CRON Exports', 'woocommerce-exporter' ); ?></h3>
		<p class="description"><?php printf( __( 'Store Exporter Deluxe supports exporting via a command line request. For sample CRON requests and supported arguments consult our <a href="%s" target="_blank">online documentation</a>.', 'woocommerce-exporter' ), $troubleshooting_url ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="enable_cron"><?php _e( 'Enable CRON', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<select id="enable_cron" name="enable_cron">
			<option value="1" disabled="disabled"><?php _e( 'Yes', 'woocommerce-exporter' ); ?></option>
			<option value="0" selected="selected"><?php _e( 'No', 'woocommerce-exporter' ); ?></option>
		</select>
		<p class="description"><?php _e( 'Enabling CRON allows developers to schedule automated exports and connect with Store Exporter Deluxe remotely.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="secret_key"><?php _e( 'Export secret key', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<input name="secret_key" type="text" id="secret_key" value="<?php echo esc_attr( $secret_key ); ?>" class="large-text code" disabled="disabled" /><br /><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'This secret key (can be left empty to allow unrestricted access) limits access to authorised developers who provide a matching key when working with Store Exporter Deluxe.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="cron_fields"><?php _e( 'Export fields', 'woocommerce-exporter' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" id="cron_fields" name="cron_fields" value="all"<?php checked( $cron_fields, 'all' ); ?> /> <?php _e( 'Include all Export Fields for the requested Export Type', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="radio" name="cron_fields" value="saved"<?php checked( $cron_fields, 'saved' ); ?> disabled="disabled" /> <?php _e( 'Use the saved Export Fields preference set on the Export screen for the requested Export Type', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
		</ul>
		<p class="description"><?php _e( 'Control whether all known export fields are included or only checked fields from the Export Fields section on the Export screen for each Export Type. Default is to include all export fields.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<!-- #cron-exports -->

<tr id="orders-screen">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<?php _e( 'Orders Screen', 'woocommerce-exporter' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<?php _e( 'Actions display', 'woocommerce-exporter' ); ?>
	</th>
	<td>
		<ul>
			<li><label><input type="checkbox" name="order_actions_csv" value="1"<?php checked( $order_actions_csv ); ?> disabled="disabled" /> <?php _e( 'Export to CSV', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></label></li>
			<li><label><input type="checkbox" name="order_actions_tsv" value="1"<?php checked( $order_actions_tsv ); ?> disabled="disabled" /> <?php _e( 'Export to TSV', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="order_actions_xls" value="1"<?php checked( $order_actions_xls ); ?> disabled="disabled" /> <?php _e( 'Export to XLS', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="order_actions_xlsx" value="1"<?php checked( $order_actions_xlsx ); ?> disabled="disabled" /> <?php _e( 'Export to XLSX', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="order_actions_xml" value="1"<?php checked( $order_actions_xml ); ?> disabled="disabled" /> <?php _e( 'Export to XML', 'woocommerce-exporter' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span></label></li>
		</ul>
		<p class="description"><?php _e( 'Control the visibility of different Order actions on the WooCommerce &raquo; Orders screen.', 'woocommerce-exporter' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}

function woo_ce_export_settings_save() {

	$export_filename = strip_tags( $_POST['export_filename'] );
	woo_ce_update_option( 'export_filename', $export_filename );
	woo_ce_update_option( 'delete_file', absint( $_POST['delete_file'] ) );
	woo_ce_update_option( 'encoding', sanitize_text_field( $_POST['encoding'] ) );
	woo_ce_update_option( 'delimiter', sanitize_text_field( $_POST['delimiter'] ) );
	woo_ce_update_option( 'category_separator', sanitize_text_field( $_POST['category_separator'] ) );
	woo_ce_update_option( 'bom', absint( $_POST['bom'] ) );
	woo_ce_update_option( 'escape_formatting', sanitize_text_field( $_POST['escape_formatting'] ) );
	if( $_POST['date_format'] == 'custom' && !empty( $_POST['date_format_custom'] ) ) {
		woo_ce_update_option( 'date_format', sanitize_text_field( $_POST['date_format_custom'] ) );
	} else {
		woo_ce_update_option( 'date_format', sanitize_text_field( $_POST['date_format'] ) );
	}

	// Export Triggers

	$message = __( 'Changes have been saved.', 'woocommerce-exporter' );
	woo_ce_admin_notice( $message );

}