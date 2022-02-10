<?php
// Display admin notice on screen load
function woo_ce_admin_notice( $message = '', $priority = 'updated', $screen = '' ) {

	if( $priority == false || $priority == '' )
		$priority = 'updated';
	if( $message <> '' ) {
		ob_start();
		woo_ce_admin_notice_html( $message, $priority, $screen );
		$output = ob_get_contents();
		ob_end_clean();
		// Check if an existing notice is already in queue
		$existing_notice = get_transient( WOO_CE_PREFIX . '_notice' );
		if( $existing_notice !== false ) {
			$existing_notice = base64_decode( $existing_notice );
			$output = $existing_notice . $output;
		}
		$response = set_transient( WOO_CE_PREFIX . '_notice', base64_encode( $output ), DAY_IN_SECONDS );
		// Check if the Transient was saved
		if( $response !== false )
			add_action( 'admin_notices', 'woo_ce_admin_notice_print' );
	}

}

// HTML template for admin notice
function woo_ce_admin_notice_html( $message = '', $priority = 'updated', $screen = '', $id = '' ) {

	// Default priority to updated
	if( empty( $priority ) )
		$priority = 'updated';
	// Display admin notice on specific screen
	if( !empty( $screen ) ) {

		global $pagenow;

		if( is_array( $screen ) ) {
			if( in_array( $pagenow, $screen ) == false )
				return;
		} else {
			if( $pagenow <> $screen )
				return;
		}

	}

	// Override for WooCommerce notice styling
	if( $priority == 'notice' )
		$priority = 'updated woocommerce-message'; ?>
<div id="<?php echo ( !empty( $id ) ? sprintf( 'message-%s', $id ) : 'message' ); ?>" class="<?php echo $priority; ?>">
	<p><?php echo $message; ?></p>
</div>
<?php

}

// Grabs the WordPress transient that holds the admin notice and prints it
function woo_ce_admin_notice_print() {

	$output = get_transient( WOO_CE_PREFIX . '_notice' );
	if( $output !== false ) {
		delete_transient( WOO_CE_PREFIX . '_notice' );
		$output = base64_decode( $output );
		echo $output;
	}

}

// HTML template header on Store Exporter screen
function woo_ce_template_header( $title = '', $icon = 'woocommerce' ) {

	if( $title )
		$output = $title;
	else
		$output = __( 'Store Export', 'woocommerce-exporter' ); ?>
<div id="woo-ce" class="wrap">
	<div id="icon-<?php echo $icon; ?>" class="icon32 icon32-woocommerce-importer"><br /></div>
	<h2>
		<?php echo $output; ?>
	</h2>
<?php

}

// HTML template footer on Store Exporter screen
function woo_ce_template_footer() { ?>
</div>
<!-- .wrap -->
<?php

}

function woo_ce_quick_export_in_process() {

	$notice_timeout = apply_filters( 'woo_ce_quick_export_in_process_notice_timeout', 10 );
	$message = sprintf( __( 'Your Quick Export is now running and a export download will be delivered in a moment. This notice will hide automatically in %d seconds.', 'woocommerce-exporter' ), $notice_timeout );

	// Allow Plugin/Theme authors to adjust this message
	$message = apply_filters( 'woo_ce_quick_export_in_process_message', $message );

	echo '<div id="messages-quick_export">';

	woo_ce_admin_notice_html( $message, false, false, 'quick_export' );

	if( !woo_ce_get_option( 'dismiss_max_input_vars_prompt', 0 ) ) {
		$troubleshooting_url = 'https://www.visser.com.au/documentation/store-exporter-deluxe/troubleshooting/';

		$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_max_input_vars_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_max_input_vars_prompt' ) ) ) );
		$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . '<strong>It looks like you have more HTML FORM fields on this screen than your hosting server can process.</strong><br /><br />Just a heads up this PHP configration option <code>max_input_vars</code> limitation may affect export generation and/or saving changes to Scheduled Exports and Export Templates.';
		$message .= sprintf( ' <a href="%s" target="_blank">%s</a>', $troubleshooting_url . '#unable-to-edit-or-save-export-field-changes-on-the-edit-export-template-screen-or-the-quick-export-screen-just-refreshes', __( 'Need help?', 'woocommerce-exporter' ) );
		woo_ce_admin_notice_html( $message, 'error', false, 'max_input_vars' );
	}

	echo '</div>';

}
add_action( 'woo_ce_export_before_options', 'woo_ce_quick_export_in_process' );

// Add Export and Docs links to the Plugins screen
function woo_ce_add_settings_link( $links, $file ) {

	// Manually force slug
	$this_plugin = WOO_CE_RELPATH;

	if( $file == $this_plugin ) {
		$docs_url = 'http://www.visser.com.au/docs/';
		$docs_link = sprintf( '<a href="%s" target="_blank">' . __( 'Docs', 'woocommerce-exporter' ) . '</a>', $docs_url );
		$export_link = sprintf( '<a href="%s">' . __( 'Export', 'woocommerce-exporter' ) . '</a>', esc_url( add_query_arg( 'page', 'woo_ce', 'admin.php' ) ) );
		array_unshift( $links, $docs_link );
		array_unshift( $links, $export_link );
	}
	return $links;

}
add_filter( 'plugin_action_links', 'woo_ce_add_settings_link', 10, 2 );

function woo_ce_admin_custom_fields_save() {

	// Save Custom Product Meta
	if( isset( $_POST['custom_products'] ) ) {
		$custom_products = $_POST['custom_products'];
		$custom_products = explode( "\n", trim( $custom_products ) );
		if( !empty( $custom_products ) ) {
			$size = count( $custom_products );
			if( !empty( $size ) ) {
				for( $i = 0; $i < $size; $i++ )
					$custom_products[$i] = sanitize_text_field( trim( stripslashes( $custom_products[$i] ) ) );
				woo_ce_update_option( 'custom_products', $custom_products );
			}
		} else {
			woo_ce_update_option( 'custom_products', '' );
		}
		unset( $custom_products );
	}

}

// Add Store Export page to WooCommerce screen IDs
function woo_ce_wc_screen_ids( $screen_ids = array() ) {

	$screen_ids[] = 'woocommerce_page_woo_ce';
	return $screen_ids;

}
add_filter( 'woocommerce_screen_ids', 'woo_ce_wc_screen_ids', 10, 1 );

// Add Store Export to WordPress Administration menu
function woo_ce_admin_menu() {

	// Check the User has the view_woocommerce_reports capability
	$user_capability = apply_filters( 'woo_ce_admin_user_capability', 'view_woocommerce_reports' );

	$hook = add_submenu_page( 'woocommerce', __( 'Store Exporter', 'woocommerce-exporter' ), __( 'Store Export', 'woocommerce-exporter' ), $user_capability, 'woo_ce', 'woo_ce_html_page' );
	// Load scripts and styling just for this Screen
	add_action( 'admin_print_styles-' . $hook, 'woo_ce_enqueue_scripts' );
	add_action( 'current_screen', 'woo_ce_admin_current_screen' );

}
add_action( 'admin_menu', 'woo_ce_admin_menu', 11 );

// Load CSS and jQuery scripts for Store Exporter screen
function woo_ce_enqueue_scripts() {

	// Simple check that WooCommerce is activated
	if( class_exists( 'WooCommerce' ) ) {

		global $woocommerce;

		// Load WooCommerce default Admin styling
		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );

	}

	// Date Picker Addon
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui' );
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_style( 'jquery-ui-datepicker', plugins_url( '/templates/admin/jquery-ui-datepicker.css', WOO_CE_RELPATH ) );

	// Time Picker, Date Picker Addon
	wp_enqueue_script( 'jquery-ui-timepicker', plugins_url( '/js/jquery.timepicker.js', WOO_CE_RELPATH ), array( 'jquery', 'jquery-ui-datepicker' ) );
	wp_enqueue_style( 'jquery-ui-datepicker', plugins_url( '/templates/admin/jquery-ui-timepicker.css', WOO_CE_RELPATH ) );

	// Chosen
	wp_enqueue_style( 'jquery-chosen', plugins_url( '/templates/admin/chosen.css', WOO_CE_RELPATH ) );
	wp_enqueue_script( 'jquery-chosen', plugins_url( '/js/jquery.chosen.js', WOO_CE_RELPATH ), array( 'jquery' ) );

	// Common
	wp_enqueue_style( 'woo_ce_styles', plugins_url( '/templates/admin/export.css', WOO_CE_RELPATH ) );
	wp_enqueue_script( 'woo_ce_scripts', plugins_url( '/templates/admin/export.js', WOO_CE_RELPATH ), array( 'jquery', 'jquery-ui-sortable' ) );
	wp_enqueue_style( 'dashicons' );

	if( WOO_CE_DEBUG ) {
		wp_enqueue_style( 'jquery-csvToTable', plugins_url( '/templates/admin/jquery-csvtable.css', WOO_CE_RELPATH ) );
		wp_enqueue_script( 'jquery-csvToTable', plugins_url( '/js/jquery.csvToTable.js', WOO_CE_RELPATH ), array( 'jquery' ) );
	}
	wp_enqueue_style( 'woo_vm_styles', plugins_url( '/templates/admin/woocommerce-admin_dashboard_vm-plugins.css', WOO_CE_RELPATH ) );

}

function woo_ce_admin_export_bar_menu( $admin_bar ) {

	// Limit this only to the Quick Export tab
	$tab = ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : false );
	if( !isset( $_GET['tab'] ) && woo_ce_get_option( 'skip_overview', false ) )
		$tab = 'export';

	if( $tab <> 'export' )
		return;

	$args = array(
		'id' => 'quick-export',
		'title' => __( 'Quick Export', 'woocommerce-exporter' ),
		'href' => '#'
	);
	$admin_bar->add_menu( $args );

}

function woo_ce_admin_current_screen() {

	$screen = get_current_screen();

	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'woo_ce_styles', plugins_url( '/templates/admin/export.css', WOO_CE_RELPATH ) );
	wp_enqueue_script( 'woo_ce_scripts', plugins_url( '/templates/admin/export.js', WOO_CE_RELPATH ), array( 'jquery', 'jquery-ui-sortable' ) );

	switch( $screen->id ) {

		case 'woocommerce_page_woo_ce':

			$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/';

			$screen->add_help_tab( array(
				'id' => 'woo_ce',
				'title' => __( 'Store Exporter', 'woocommerce-exporter' ),
				'content' => 
					'<p>' . __( 'Thank you for using Store Exporter :) Should you need help using this Plugin please read the documentation, if an issue persists get in touch with us on the WordPress.org Support tab for this Plugin.', 'woocommerce-exporter' ) . '</p>' .
					'<p><a href="' . $troubleshooting_url . '" target="_blank" class="button button-primary">' . __( 'Documentation', 'woocommerce-exporter' ) . '</a> <a href="' . 'http://wordpress.org/support/plugin/woocommerce-exporter' . '" target="_blank" class="button">' . __( 'Forum Support', 'woocommerce-exporter' ) . '</a></p>'
			) );

			add_action( 'admin_bar_menu', 'woo_ce_admin_export_bar_menu', 100 );

			// This function only runs on the Quick Export screen
			add_action( 'admin_footer', 'woo_ce_admin_export_footer_javascript' );
			break;

	}

}

function woo_ce_admin_plugin_row() {

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/';

	// Detect if another e-Commerce platform is activated
	if( !woo_is_woo_activated() && ( woo_is_jigo_activated() || woo_is_wpsc_activated() ) ) {
		$message = __( 'We have detected another e-Commerce Plugin than WooCommerce activated, please check that you are using Store Exporter for the correct platform.', 'woocommerce-exporter' );
		$message .= sprintf( ' <a href="%s" target="_blank">%s</a>', __( 'Need help?', 'woocommerce-exporter' ), $troubleshooting_url );
		echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange"><div class="update-message">' . $message . '</div></td></tr>';
	} else if( !woo_is_woo_activated() ) {
		$message = __( 'We have been unable to detect the WooCommerce Plugin activated on this WordPress site, please check that you are using Exporter Deluxe for the correct platform.', 'woocommerce-exporter' );
		$message .= sprintf( ' <a href="%s" target="_blank">%s</a>', $troubleshooting_url, __( 'Need help?', 'woocommerce-exporter' ) );
		echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange"><div class="update-message">' . $message . '</div></td></tr>';
	}

}
 
// HTML active class for the currently selected tab on the Store Exporter screen
function woo_ce_admin_active_tab( $tab_name = null, $tab = null ) {

	if( isset( $_GET['tab'] ) && !$tab )
		$tab = $_GET['tab'];
	else if( !isset( $_GET['tab'] ) && woo_ce_get_option( 'skip_overview', false ) )
		$tab = 'export';
	else
		$tab = 'overview';

	$output = '';
	if( isset( $tab_name ) && $tab_name ) {
		if( $tab_name == $tab )
			$output = ' nav-tab-active';
	}
	echo $output;

}

// HTML template for each tab on the Store Exporter screen
function woo_ce_tab_template( $tab = '' ) {

	if( !$tab )
		$tab = 'overview';

	// Store Exporter Deluxe
	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/';

	if( in_array( $tab, array( 'export', 'scheduled_export', 'export_template', 'settings', 'tools' ) ) ) {

		// Upgrade notice for Quick Export screen
		if(
			woo_ce_get_option( 'show_upgrade_prompt', 0 ) && 
			!woo_ce_get_option( 'dismiss_upgrade_prompt', 0 )
		) {
			$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_upgrade_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_upgrade_prompt' ) ) ) );
			$message = '';
			$message .= '<span style="float:right;">';
			$message .= '<a href="' . $woo_cd_url . '" target="_blank" class="button button-primary">' . __( 'Upgrade now', 'woocommerce-exporter' ) . '</a>' . '&nbsp;';
			$message .= '<a href="' . $woo_cd_url . '" target="_blank" class="button">' . __( 'Tell me more', 'woocommerce-exporter' ) . '</a>' . '<br />';
			$message .= '<a href="' . $dismiss_url . '" style="float: right; margin-top:0.5em;">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a>';
			$message .= '</span>';
			$message .= '<img src="'  . plugins_url( '/templates/admin/images/icon.png', WOO_CE_RELPATH ) . '" alt="" style="height:64px; margin-right:0.5em; float:left;" />';
			$message .= '<strong>' . __( 'Unlock business focused WooCommerce exports. Scheduled Exports, Export Templates, Order, Customer, Subscription exports and more!', 'woocommerce-exporter' ) . '</strong> ';
			$message .= '<br />' . sprintf( __( 'Upgrade to %s to unlock all of the business-focused features, filters and options.', 'woocommerce-exporter' ), $woo_cd_link );
			$message .= '<br class="clear" />';
			woo_ce_admin_notice_html( $message, 'updated' );
		}

	}

	switch( $tab ) {

		case 'overview':

			// Welcome notice for Overview screen
			if( !woo_ce_get_option( 'dismiss_overview_prompt', 0 ) ) {
				$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_overview_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_overview_prompt' ) ) ) );
				$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . '<strong>' . __( 'Welcome aboard!', 'woocommerce-exporter' ) . '</strong> ';
				$message .= sprintf( __( 'Jump over to the <a href="%s">Quick Export screen</a> to create your first export.', 'woocommerce-exporter' ), add_query_arg( array( 'tab' => 'export' ) ) );
				woo_ce_admin_notice_html( $message, 'notice' );
			}

			$skip_overview = woo_ce_get_option( 'skip_overview', false );
			break;

		case 'export':

			// Welcome notice for Quick Export screen
			if( !woo_ce_get_option( 'dismiss_quick_export_prompt', 0 ) ) {
				$dismiss_url = esc_url( add_query_arg( array( 'action' => 'dismiss_quick_export_prompt', '_wpnonce' => wp_create_nonce( 'woo_ce_dismiss_quick_export_prompt' ) ) ) );
				$message = '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woocommerce-exporter' ) . '</a></span>' . '<strong>' . __( 'This is where the magic happens...', 'woocommerce-exporter' ) . '</strong> ';
				$message .= '<br /><br />' . __( 'Select an export type from the list below to expand the list of available export fields and filters, try switching between different export types to see the different options. When you are ready select the fields you would like to export and click the Export button below, Store Exporter will create an export file for you to save to your computer.', 'woocommerce-exporter' );
				woo_ce_admin_notice_html( $message, 'notice' );
			}

			$export_type = ( isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '' );
			$export_types = array_keys( woo_ce_get_export_types() );

			// Check if the default export type exists
			if( !in_array( $export_type, $export_types ) )
				$export_type = 'product';

			$product = woo_ce_get_export_type_count( 'product' );
			$category = woo_ce_get_export_type_count( 'category' );
			$tag = woo_ce_get_export_type_count( 'tag' );
			$brand = '999';
			$order = woo_ce_get_export_type_count( 'order' );
			$customer = '999';
			$user = woo_ce_get_export_type_count( 'user' );
			$review = '999';
			$coupon = '999';
			$attribute = '999';
			$subscription = '999';
			$product_vendor = '999';
			$commission = '999';
			$shipping_class = '999';
			$ticket = '999';

			$product_fields = false;
			$category_fields = false;
			$tag_fields = false;
			$brand_fields = false;
			$order_fields = false;
			$customer_fields = false;
			$user_fields = false;
			$review_fields = false;
			$coupon_fields = false;
			$attribute_fields = false;
			$subscription_fields = false;
			$product_vendor_fields = false;
			$commission_fields = false;
			$shipping_class_fields = false;
			$ticket_fields = false;
			$booking_fields = false;

			// Start loading the Quick Export screen
			add_action( 'woo_ce_export_before_options', 'woo_ce_export_export_types' );
			add_action( 'woo_ce_export_after_options', 'woo_ce_export_export_options' );

			// Products
			if( $product_fields = ( function_exists( 'woo_ce_get_product_fields' ) ? woo_ce_get_product_fields() : false ) ) {
				foreach( $product_fields as $key => $product_field )
					$product_fields[$key]['disabled'] = ( isset( $product_field['disabled'] ) ? $product_field['disabled'] : 0 );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_category' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_tag' );
				if( function_exists( 'woo_ce_products_filter_by_product_brand' ) )
					add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_brand' );
				if( function_exists( 'woo_ce_products_filter_by_product_vendor' ) )
					add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_vendor' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_status' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_product_type' );
				add_action( 'woo_ce_export_product_options_before_table', 'woo_ce_products_filter_by_stock_status' );
				add_action( 'woo_ce_export_product_options_after_table', 'woo_ce_product_sorting' );
				add_action( 'woo_ce_export_options', 'woo_ce_products_upsell_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_products_crosssell_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_export_options_product_gallery_formatting' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_products_custom_fields' );
			}

			// Categories
			if( $category_fields = ( function_exists( 'woo_ce_get_category_fields' ) ? woo_ce_get_category_fields() : false ) ) {
				foreach( $category_fields as $key => $category_field )
					$category_fields[$key]['disabled'] = ( isset( $category_field['disabled'] ) ? $category_field['disabled'] : 0 );
				add_action( 'woo_ce_export_category_options_after_table', 'woo_ce_category_sorting' );
			}

			// Product Tags
			if( $tag_fields = ( function_exists( 'woo_ce_get_tag_fields' ) ? woo_ce_get_tag_fields() : false ) ) {
				foreach( $tag_fields as $key => $tag_field )
					$tag_fields[$key]['disabled'] = ( isset( $tag_field['disabled'] ) ? $tag_field['disabled'] : 0 );
				add_action( 'woo_ce_export_tag_options_after_table', 'woo_ce_tag_sorting' );
			}

			// Brands
			if( $brand_fields = ( function_exists( 'woo_ce_get_brand_fields' ) ? woo_ce_get_brand_fields() : false ) ) {
				foreach( $brand_fields as $key => $brand_field )
					$brand_fields[$key]['disabled'] = ( isset( $brand_field['disabled'] ) ? $brand_field['disabled'] : 0 );
				add_action( 'woo_ce_export_brand_options_before_table', 'woo_ce_brand_sorting' );
			}

			// Orders
			if( $order_fields = ( function_exists( 'woo_ce_get_order_fields' ) ? woo_ce_get_order_fields() : false ) ) {
				foreach( $order_fields as $key => $order_field )
					$order_fields[$key]['disabled'] = ( isset( $order_field['disabled'] ) ? $order_field['disabled'] : 0 );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_date' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_status' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_customer' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_billing_country' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_shipping_country' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_user_role' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_coupon' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product_category' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product_tag' );
				if( function_exists( 'woo_ce_orders_filter_by_product_brand' ) )
					add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_product_brand' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_order_id' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_payment_gateway' );
				add_action( 'woo_ce_export_order_options_before_table', 'woo_ce_orders_filter_by_shipping_method' );
				add_action( 'woo_ce_export_order_options_after_table', 'woo_ce_order_sorting' );
				add_action( 'woo_ce_export_options', 'woo_ce_orders_items_formatting' );
				add_action( 'woo_ce_export_options', 'woo_ce_orders_max_order_items' );
				add_action( 'woo_ce_export_options', 'woo_ce_orders_items_types' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_orders_custom_fields' );
			}

			// Customers
			if( $customer_fields = ( function_exists( 'woo_ce_get_customer_fields' ) ? woo_ce_get_customer_fields() : false ) ) {
				foreach( $customer_fields as $key => $customer_field )
					$customer_fields[$key]['disabled'] = ( isset( $customer_field['disabled'] ) ? $customer_field['disabled'] : 0 );
				add_action( 'woo_ce_export_customer_options_before_table', 'woo_ce_customers_filter_by_status' );
				add_action( 'woo_ce_export_customer_options_before_table', 'woo_ce_customers_filter_by_user_role' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_customers_custom_fields' );
			}

			// Users
			if( $user_fields = ( function_exists( 'woo_ce_get_user_fields' ) ? woo_ce_get_user_fields() : false ) ) {
				foreach( $user_fields as $key => $user_field )
					$user_fields[$key]['disabled'] = ( isset( $user_field['disabled'] ) ? $user_field['disabled'] : 0 );
				add_action( 'woo_ce_export_user_options_after_table', 'woo_ce_user_sorting' );
				add_action( 'woo_ce_export_after_form', 'woo_ce_users_custom_fields' );
			}

			// Reviews
			if( $review_fields = ( function_exists( 'woo_ce_get_review_fields' ) ? woo_ce_get_review_fields() : false ) ) {
				foreach( $review_fields as $key => $review_field )
					$review_fields[$key]['disabled'] = ( isset( $review_field['disabled'] ) ? $review_field['disabled'] : 0 );
			}

			// Coupons
			if( $coupon_fields = ( function_exists( 'woo_ce_get_coupon_fields' ) ? woo_ce_get_coupon_fields() : false ) ) {
				foreach( $coupon_fields as $key => $coupon_field )
					$coupon_fields[$key]['disabled'] = ( isset( $coupon_field['disabled'] ) ? $coupon_field['disabled'] : 0 );
				add_action( 'woo_ce_export_coupon_options_before_table', 'woo_ce_coupon_sorting' );
			}

			// Subscriptions
			if( $subscription_fields = ( function_exists( 'woo_ce_get_subscription_fields' ) ? woo_ce_get_subscription_fields() : false ) ) {
				foreach( $subscription_fields as $key => $subscription_field )
					$subscription_fields[$key]['disabled'] = ( isset( $subscription_field['disabled'] ) ? $subscription_field['disabled'] : 0 );
				add_action( 'woo_ce_export_subscription_options_before_table', 'woo_ce_subscriptions_filter_by_subscription_status' );
				add_action( 'woo_ce_export_subscription_options_before_table', 'woo_ce_subscriptions_filter_by_subscription_product' );
			}

			// Product Vendors
			if( $product_vendor_fields = ( function_exists( 'woo_ce_get_product_vendor_fields' ) ? woo_ce_get_product_vendor_fields() : false ) ) {
				foreach( $product_vendor_fields as $key => $product_vendor_field )
					$product_vendor_fields[$key]['disabled'] = ( isset( $product_vendor_field['disabled'] ) ? $product_vendor_field['disabled'] : 0 );
			}

			// Commissions
			if( $commission_fields = ( function_exists( 'woo_ce_get_commission_fields' ) ? woo_ce_get_commission_fields() : false ) ) {
				foreach( $commission_fields as $key => $commission_field )
					$commission_fields[$key]['disabled'] = ( isset( $commission_field['disabled'] ) ? $commission_field['disabled'] : 0 );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commissions_filter_by_date' );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commissions_filter_by_product_vendor' );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commissions_filter_by_commission_status' );
				add_action( 'woo_ce_export_commission_options_before_table', 'woo_ce_commission_sorting' );
			}

			// Shipping Classes
			if( $shipping_class_fields = ( function_exists( 'woo_ce_get_shipping_class_fields' ) ? woo_ce_get_shipping_class_fields() : false ) ) {
				foreach( $shipping_class_fields as $key => $shipping_class_field )
					$shipping_class_fields[$key]['disabled'] = ( isset( $shipping_class_field['disabled'] ) ? $shipping_class_field['disabled'] : 0 );
				add_action( 'woo_ce_export_shipping_class_options_after_table', 'woo_ce_shipping_class_sorting' );
			}

			// Tickets

			if( $ticket_fields = ( function_exists( 'woo_ce_get_ticket_fields' ) ? woo_ce_get_ticket_fields() : false ) ) {
				foreach( $ticket_fields as $key => $ticket_field )
					$ticket_fields[$key]['disabled'] = ( isset( $ticket_field['disabled'] ) ? $ticket_field['disabled'] : 0 );
			}

			// Bookings
			if( $booking_fields = ( function_exists( 'woo_ce_get_booking_fields' ) ? woo_ce_get_booking_fields() : false ) ) {
				foreach( $booking_fields as $key => $booking_field )
					$booking_fields[$key]['disabled'] = ( isset( $booking_field['disabled'] ) ? $booking_field['disabled'] : 0 );
			}

			// Attributes
			if( $attribute_fields = ( function_exists( 'woo_ce_get_attribute_fields' ) ? woo_ce_get_attribute_fields() : false ) ) {
				foreach( $attribute_fields as $key => $attribute_field )
					$attribute_fields[$key]['disabled'] = ( isset( $attribute_field['disabled'] ) ? $attribute_field['disabled'] : 0 );
			}
			break;

		case 'fields':
			$export_type = ( isset( $_GET['type'] ) ? sanitize_text_field( $_GET['type'] ) : '' );
			$export_types = array_keys( woo_ce_get_export_types() );
			$fields = array();
			if( in_array( $export_type, $export_types ) ) {
				if( has_filter( 'woo_ce_' . $export_type . '_fields', 'woo_ce_override_' . $export_type . '_field_labels' ) )
					remove_filter( 'woo_ce_' . $export_type . '_fields', 'woo_ce_override_' . $export_type . '_field_labels', 11 );
				if( function_exists( sprintf( 'woo_ce_get_%s_fields', $export_type ) ) )
					$fields = call_user_func( 'woo_ce_get_' . $export_type . '_fields' );
				$labels = woo_ce_get_option( $export_type . '_labels', array() );
			}
			break;

		case 'archive':
			if( isset( $_GET['deleted'] ) ) {
				$message = __( 'Archived export has been deleted.', 'woocommerce-exporter' );
				woo_ce_admin_notice( $message );
			}
			if( $files = woo_ce_get_archive_files() ) {
				foreach( $files as $key => $file )
					$files[$key] = woo_ce_get_archive_file( $file );
			}
			break;

		case 'settings':
			$export_filename = woo_ce_get_option( 'export_filename', '' );
			// Default export filename
			if( $export_filename == false )
				$export_filename = '%store_name%-export_%dataset%-%date%-%time%-%random%.csv';
			$delete_file = woo_ce_get_option( 'delete_file', 1 );
			$timeout = woo_ce_get_option( 'timeout', 0 );
			$encoding = woo_ce_get_option( 'encoding', 'UTF-8' );
			$bom = woo_ce_get_option( 'bom', 1 );
			$delimiter = woo_ce_get_option( 'delimiter', ',' );
			$category_separator = woo_ce_get_option( 'category_separator', '|' );
			$escape_formatting = woo_ce_get_option( 'escape_formatting', 'all' );
			$date_format = woo_ce_get_option( 'date_format', 'd/m/Y' );
			// Reset the Date Format if corrupted
			if( $date_format == '1' || $date_format == '' || $date_format == false )
				$date_format = 'd/m/Y';
			$file_encodings = ( function_exists( 'mb_list_encodings' ) ? mb_list_encodings() : false );
			add_action( 'woo_ce_export_settings_top', 'woo_ce_export_settings_quicklinks' );
			add_action( 'woo_ce_export_settings_after', 'woo_ce_export_settings_csv' );
			add_action( 'woo_ce_export_settings_after', 'woo_ce_export_settings_extend' );
			break;

		case 'tools':
			// Product Importer Deluxe
			$woo_pd_url = 'http://www.visser.com.au/woocommerce/plugins/product-importer-deluxe/';
			$woo_pd_target = ' target="_blank"';
			if( function_exists( 'woo_pd_init' ) ) {
				$woo_pd_url = esc_url( add_query_arg( array( 'page' => 'woo_pd', 'tab' => null ) ) );
				$woo_pd_target = false;
			}

			// Store Toolkit
			$woo_st_url = 'http://www.visser.com.au/woocommerce/plugins/store-toolkit/';
			$woo_st_target = ' target="_blank"';
			if( function_exists( 'woo_st_admin_init' ) ) {
				$woo_st_url = esc_url( add_query_arg( array( 'page' => 'woo_st', 'tab' => null ) ) );
				$woo_st_target = false;
			}

			// Export modules
			$module_status = ( isset( $_GET['module_status'] ) ? sanitize_text_field( $_GET['module_status'] ) : false );
			$modules = woo_ce_modules_list( $module_status );
			$modules_all = get_transient( WOO_CE_PREFIX . '_modules_all_count' );
			$modules_active = get_transient( WOO_CE_PREFIX . '_modules_active_count' );
			$modules_inactive = get_transient( WOO_CE_PREFIX . '_modules_inactive_count' );
			break;

	}
	if( $tab ) {
		if( file_exists( WOO_CE_PATH . 'templates/admin/tabs-' . $tab . '.php' ) ) {
			include_once( WOO_CE_PATH . 'templates/admin/tabs-' . $tab . '.php' );
		} else {
			$message = sprintf( __( 'We couldn\'t load the export template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woocommerce-exporter' ), 'tabs-' . $tab . '.php', WOO_CE_PATH . 'templates/admin/...' );
			woo_ce_admin_notice_html( $message, 'error' );
			ob_start(); ?>
<p><?php _e( 'You can see this error for one of a few common reasons', 'woocommerce-exporter' ); ?>:</p>
<ul class="ul-disc">
	<li><?php _e( 'WordPress was unable to create this file when the Plugin was installed or updated', 'woocommerce-exporter' ); ?></li>
	<li><?php _e( 'The Plugin files have been recently changed and there has been a file conflict', 'woocommerce-exporter' ); ?></li>
	<li><?php _e( 'The Plugin file has been locked and cannot be opened by WordPress', 'woocommerce-exporter' ); ?></li>
</ul>
<p><?php _e( 'Jump onto our website and download a fresh copy of this Plugin as it might be enough to fix this issue. If this persists get in touch with us.', 'woocommerce-exporter' ); ?></p>
<?php
			ob_end_flush();
		}
	}

}

function woo_ce_export_export_types() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$export_type = sanitize_text_field( ( isset( $_POST['dataset'] ) ? $_POST['dataset'] : woo_ce_get_option( 'last_export', 'product' ) ) );
	$product = woo_ce_get_export_type_count( 'product' );
	$category = woo_ce_get_export_type_count( 'category' );
	$tag = woo_ce_get_export_type_count( 'tag' );
	$brand = '999';
	$order = woo_ce_get_export_type_count( 'order' );
	$customer = '999';
	$user = woo_ce_get_export_type_count( 'user' );
	$review = '999';
	$coupon = '999';
	$attribute = '999';
	$subscription = '999';
	$product_vendor = '999';
	$commission = '999';
	$shipping_class = '999';
	$ticket = '999';

	ob_start();
?>
<div id="export-type">
	<h3>
		<?php _e( 'Export Types', 'woocommerce-exporter' ); ?>
		<img class="help_tip" data-tip="<?php _e( 'Select the data type you want to export. Export Type counts are refreshed hourly and can be manually refreshed by clicking the <em>Refresh counts</em> link.', 'woocommerce-exporter' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />
	</h3>
	<div class="inside">
		<table class="form-table widefat striped">
			<thead>

				<tr>
					<th width="1%">&nbsp;</th>
					<th class="column_export-type"><?php _e( 'Export Type', 'woocommerce-exporter' ); ?></th>
					<th class="column_records">
						<?php _e( 'Records', 'woocommerce-exporter' ); ?>
						(<a href="<?php echo esc_url( add_query_arg( array( 'action' => 'refresh_export_type_counts', '_wpnonce' => wp_create_nonce( 'woo_ce_refresh_export_type_counts' ) ) ) ); ?>"><?php _e( 'Refresh counts', 'woocommerce-exporter' ); ?></a>)
					</th>
					<th width="1%"><attr title="<?php _e( 'Actions', 'woocommerce-exporter' ); ?>">...</attr></th>
				</tr>

			</thead>
			<tbody>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="product" name="dataset" value="product"<?php disabled( $product, 0 ); ?><?php checked( ( empty( $product ) ? '' : $export_type ), 'product' ); ?> />
					</td>
					<td class="name">
						<label for="product"><?php _e( 'Products', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<?php echo $product; ?>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="category" name="dataset" value="category"<?php disabled( $category, 0 ); ?><?php checked( ( empty( $category ) ? '' : $export_type ), 'category' ); ?> />
					</td>
					<td class="name">
						<label for="category"><?php _e( 'Categories', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<?php echo $category; ?>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="tag" name="dataset" value="tag"<?php disabled( $tag, 0 ); ?><?php checked( ( empty( $tag ) ? '' : $export_type ), 'tag' ); ?> />
					</td>
					<td class="name">
						<label for="tag"><?php _e( 'Tags', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<?php echo $tag; ?>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="brand" name="dataset" value="brand"<?php disabled( $brand, 0 ); ?><?php checked( ( empty( $brand ) ? '' : $export_type ), 'brand' ); ?> />
					</td>
					<td class="name">
						<label for="brand"><?php _e( 'Brands', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<span class="description"><?php echo ( $brand = 999 ? 'N/A' : $brand ); ?></span>
						<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="order" name="dataset" value="order"<?php disabled( $order, 0 ); ?><?php checked( ( empty( $order ) ? '' : $export_type ), 'order' ); ?>/>
					</td>
					<td class="name">
						<label for="order"><?php _e( 'Orders', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<?php echo $order; ?>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="customer" name="dataset" value="customer"<?php disabled( $customer, 0 ); ?><?php checked( ( empty( $customer ) ? '' : $export_type ), 'customer' ); ?>/>
					</td>
					<td class="name">
						<label for="customer"><?php _e( 'Customers', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<span class="description"><?php echo ( $customer = 999 ? 'N/A' : $customer ); ?></span>
						<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="user" name="dataset" value="user"<?php disabled( $user, 0 ); ?><?php checked( ( empty( $user ) ? '' : $export_type ), 'user' ); ?>/>
					</td>
					<td class="name">
						<label for="user"><?php _e( 'Users', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<?php echo $user; ?>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="review" name="dataset" value="review"<?php disabled( $review, 0 ); ?><?php checked( ( empty( $review ) ? '' : $export_type ), 'review' ); ?>/>
					</td>
					<td class="name">
						<label for="review"><?php _e( 'Reviews', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<span class="description"><?php echo ( $review = 999 ? 'N/A' : $review ); ?></span>
						<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="coupon" name="dataset" value="coupon"<?php disabled( $coupon, 0 ); ?><?php checked( ( empty( $coupon ) ? '' : $export_type ), 'coupon' ); ?> />
					</td>
					<td class="name">
						<label for="coupon"><?php _e( 'Coupons', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<span class="description"><?php echo ( $coupon = 999 ? 'N/A' : $coupon ); ?></span>
						<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="subscription" name="dataset" value="subscription"<?php disabled( $subscription, 0 ); ?><?php checked( ( empty( $subscription ) ? '' : $export_type ), 'subscription' ); ?> />
					</td>
					<td class="name">
						<label for="subscription"><?php _e( 'Subscriptions', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<span class="description"><?php echo ( $subscription = 999 ? 'N/A' : $subscription ); ?></span>
						<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="product_vendor" name="dataset" value="product_vendor"<?php disabled( $product_vendor, 0 ); ?><?php checked( ( empty( $product_vendor ) ? '' : $export_type ), 'product_vendor' ); ?> />
					</td>
					<td class="name">
						<label for="product_vendor"><?php _e( 'Product Vendors', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<span class="description"><?php echo ( $product_vendor = 999 ? 'N/A' : $product_vendor ); ?></span>
						<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="commission" name="dataset" value="commission"<?php disabled( $commission, 0 ); ?><?php checked( ( empty( $commission ) ? '' : $export_type ), 'commission' ); ?> />
					</td>
					<td class="name">
						<label for="commission"><?php _e( 'Commissions', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<span class="description"><?php echo ( $commission = 999 ? 'N/A' : $commission ); ?></span>
						<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="shipping_class" name="dataset" value="shipping_class"<?php disabled( $shipping_class, 0 ); ?><?php checked( ( empty( $shipping_class ) ? '' : $export_type ), 'shipping_class' ); ?> />
					</td>
					<td class="name">
						<label for="shipping_class"><?php _e( 'Shipping Classes', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<span class="description"><?php echo ( $shipping_class = 999 ? 'N/A' : $shipping_class ); ?></span>
						<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>

<!--
				<tr>
					<td width="1%" class="sort">
						<input type="radio" id="attribute" name="dataset" value="attribute"<?php disabled( $attribute, 0 ); ?><?php checked( ( empty( $attribute ) ? '' : $export_type ), 'attribute' ); ?> />
					</td>
					<td class="name">
						<label for="attribute"><?php _e( 'Attributes', 'woocommerce-exporter' ); ?></label>
					</td>
					<td>
						<span class="description"><?php echo ( $attribute = 999 ? 'N/A' : $attribute ); ?></span>
						<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
					</td>
					<td width="1%" class="actions">
						<span class="dashicons dashicons-no-alt"></span>
					</td>
				</tr>
-->
			</tbody>
		</table>
		<!-- .form-table -->
		<p>
			<input id="quick_export" type="button" value="<?php _e( 'Quick Export', 'woocommerce-exporter' ); ?>" class="button" />
		</p>
	</div>
	<!-- .inside -->
</div>
<!-- .postbox -->

<hr />

<?php
	ob_end_flush();

}

function woo_ce_export_export_options() {

	ob_start();

	$template = 'quick_export.php';
	if( file_exists( WOO_CE_PATH . 'includes/admin/' . $template ) ) {

		include_once( WOO_CE_PATH . 'includes/admin/' . $template );

		add_action( 'woo_ce_export_options', 'woo_ce_export_options_export_format' );
		add_action( 'woo_ce_export_options', 'woo_ce_export_options_export_template' );
		add_action( 'woo_ce_export_options', 'woo_ce_export_options_troubleshooting' );
		add_action( 'woo_ce_export_options', 'woo_ce_export_options_limit_volume' );
		add_action( 'woo_ce_export_options', 'woo_ce_export_options_volume_offset' );

	} else {

		$message = sprintf( __( 'We couldn\'t load the template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woocommerce-exporter' ), $template, WOO_CE_PATH . 'includes/admin/...' );
?>
<p><strong><?php echo $message; ?></strong></p>
<p><?php _e( 'You can see this error for one of a few common reasons', 'woocommerce-exporter' ); ?>:</p>
<ul class="ul-disc">
	<li><?php _e( 'WordPress was unable to create this file when the Plugin was installed or updated', 'woocommerce-exporter' ); ?></li>
	<li><?php _e( 'The Plugin files have been recently changed and there has been a file conflict', 'woocommerce-exporter' ); ?></li>
	<li><?php _e( 'The Plugin file has been locked and cannot be opened by WordPress', 'woocommerce-exporter' ); ?></li>
</ul>
<p><?php _e( 'Jump onto our website and download a fresh copy of this Plugin as it might be enough to fix this issue. If this persists get in touch with us.', 'woocommerce-exporter' ); ?></p>
<?php

	}
?>
<div class="postbox" id="export-options">
	<h3 class="hndle"><?php _e( 'Export Options', 'woocommerce-exporter' ); ?></h3>
	<div class="inside">
		<p class="description"><?php _e( 'Use this section to customise your export file to suit your needs, export options change based on the selected export type. You can find additional export options under the Settings tab at the top of this screen.', 'woocommerce-exporter' ); ?></p>

		<?php do_action( 'woo_ce_export_options_before' ); ?>

		<table class="form-table">

			<?php do_action( 'woo_ce_export_options' ); ?>

			<?php do_action( 'woo_ce_export_options_table_after' ); ?>

		</table>
		<p class="description"><?php _e( 'Click the Export button above to apply these changes and generate your export file.', 'woocommerce-exporter' ); ?></p>

		<?php do_action( 'woo_ce_export_options_after' ); ?>

	</div>
</div>
<!-- .postbox -->

<?php
	ob_end_flush();

}
// This function only runs on the Quick Export screen
function woo_ce_admin_export_footer_javascript() {

	// Limit this only to the Quick Export tab
	$tab = ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : false );
	if( !isset( $_GET['tab'] ) && woo_ce_get_option( 'skip_overview', false ) )
		$tab = 'export';
	if( $tab <> 'export' )
		return;

	$notice_timeout = apply_filters( 'woo_ce_quick_export_in_process_notice_timeout', 10 );
	$notice_timeout = ( !empty( $notice_timeout ) ? $notice_timeout * 1000 : 0 );

	$total = false;
	// Displays a notice where the maximum PHP FORM limit is below the number of detected FORM elements
	if( !woo_ce_get_option( 'dismiss_max_input_vars_prompt', 0 ) ) {
		if( function_exists( 'ini_get' ) )
			$total = ini_get( 'max_input_vars' );
	}

	// In-line javascript
	ob_start(); ?>
<script type="text/javascript">
jQuery(document).ready( function($) {

	// This shows the Quick export in progress... notice
<?php if( !empty( $notice_timeout ) ) { ?>
	$j("#postform").on("submit", function(){
		$j('#message.error').fadeOut('slow');
		$j('#message-quick_export').fadeIn().delay(<?php echo $notice_timeout; ?>).fadeOut('slow');
		scroll(0,0);
	});
<?php } ?>

<?php if( $total && !woo_ce_get_option( 'dismiss_max_input_vars_prompt', 0 ) ) { ?>
	// Check that the number of FORM fields is below the PHP FORM limit
	var current_fields = jQuery('#postform').find('input, textarea, select').length;
	var max_fields = '<?php echo $total; ?>';
	if( current_fields && max_fields ) {
		if( current_fields > max_fields ) {
			jQuery('#message-max_input_vars').fadeIn();
		}
	}
<?php } ?>

	// This triggers the Quick Export button from the admin menu bar
	jQuery("li#wp-admin-bar-quick-export .ab-item").on( "click", function() {
		jQuery('#quick_export').trigger('click');
		return false;
	});

});
</script>
<?php
	ob_end_flush();

}

// Display the memory usage in the screen footer
function woo_ce_admin_footer_text( $footer_text = '' ) {

	$current_screen = get_current_screen();
	$pages = array(
		'woocommerce_page_woo_ce'
	);
	// Check to make sure we're on the Export screen
	if( 
		isset( $current_screen->id ) && 
		apply_filters( 'woo_ce_display_admin_footer_text', in_array( $current_screen->id, $pages ) )
	) {
		$memory_usage = woo_ce_current_memory_usage( false );
		$memory_limit = absint( ini_get( 'memory_limit' ) );
		$memory_percent = absint( $memory_usage / $memory_limit * 100 );
		$memory_color = 'font-weight:normal;';
		if( $memory_percent > 75 )
			$memory_color = 'font-weight:bold; color:orange;';
		if( $memory_percent > 90 )
			$memory_color = 'font-weight:bold; color:red;';
		$footer_text .= ' | ' . sprintf( __( 'Memory: %s of %s MB (%s)', 'woocommerce-exporter' ), $memory_usage, $memory_limit, sprintf( '<span style="%s">%s</span>', $memory_color, $memory_percent . '%' ) );
		$footer_text .= ' | ' . sprintf( __( 'Stopwatch: %s seconds', 'woocommerce-exporter' ), timer_stop(0, 3) );
	}
	return $footer_text;

}

function woo_ce_modules_status_class( $status = 'inactive' ) {

	$output = '';
	switch( $status ) {

		case 'active':
			$output = 'green';
			break;

		case 'inactive':
			$output = 'yellow';
			break;

	}
	echo $output;

}

function woo_ce_modules_status_label( $status = 'inactive' ) {

	$output = '';
	switch( $status ) {

		case 'active':
			$output = __( 'OK', 'woocommerce-exporter' );
			break;

		case 'inactive':
			$output = __( 'Install', 'woocommerce-exporter' );
			break;

	}
	echo $output;

}

// HTML template for header prompt on Store Exporter screen
function woo_ce_support_donate() {

	$woo_cd_url = 'https://www.visser.com.au/plugins/store-exporter-deluxe/?platform=wc';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Upgrade to Store Exporter Deluxe', 'woocommerce-exporter' ) . '</a>', $woo_cd_url );

	$output = '';
	$show = true;
	if( function_exists( 'woo_vl_we_love_your_plugins' ) ) {
		if( in_array( WOO_CE_DIRNAME, woo_vl_we_love_your_plugins() ) )
			$show = false;
	}
	if( $show ) {
		$donate_url = 'http://www.visser.com.au/donate/';
		$rate_url = 'http://wordpress.org/support/view/plugin-reviews/' . WOO_CE_DIRNAME;
		$output = '
<div id="support-donate_rate" class="support-donate_rate">
	<p>' . sprintf( __( '<strong>Like this Plugin?</strong> %s and %s.', 'woocommerce-exporter' ), $woo_cd_link, '<a href="' . esc_url( add_query_arg( array( 'rate' => '5' ), $rate_url ) ) . '#postform" target="_blank">rate / review us on WordPress.org</a>' ) . '</p>
</div>
';
	}
	echo $output;

}