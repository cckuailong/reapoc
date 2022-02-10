<?php
// Add WooCommerce store details to WordPress Administration Dashboard
function woo_st_add_dashboard_widgets() {

	// Simple check that WooCommerce is activated
	if( class_exists( 'WooCommerce' ) ) {

		$user_capability = apply_filters( 'woo_st_dashboard_widgets', 'manage_options' );

		// Check for manage_options User Capability
		if( current_user_can( $user_capability ) ) {
			if( apply_filters( 'woo_st_dashboard_right_now', true ) )
				wp_add_dashboard_widget( 'woo_st-dashboard_right_now', __( 'Right Now in Store', 'woocommerce-store-toolkit' ), 'woo_st_dashboard_right_now' );
			if( apply_filters( 'woo_st_dashboard_sales_summary', true ) ) {
				if( function_exists( 'wc_price' ) )
					wp_add_dashboard_widget( 'woo_st-dashboard_sales', __( 'Sales Summary', 'woocommerce-store-toolkit' ), 'woo_st_dashboard_sales_summary' );
			}
		}

	}

}
add_action( 'wp_dashboard_setup', 'woo_st_add_dashboard_widgets' );

function woo_st_dashboard_right_now() {

	$order_count = array();
	$order_statuses = woo_st_get_order_statuses();
	if( !empty( $order_statuses ) && !is_wp_error( $order_statuses ) ) {
		foreach( $order_statuses as $order_status ) {
			switch( $order_status->term_id ) {

				case 'wc-pending':
					$order_count['pending'] = $order_status->count;
					break;

				case 'wc-on-hold':
					$order_count['onhold'] = $order_status->count;
					break;

				case 'wc-processing':
					$order_count['processing'] = $order_status->count;
					break;

				case 'wc-completed':
					$order_count['completed'] = $order_status->count;
					break;

				case 'wc-cancelled':
					$order_count['cancelled'] = $order_status->count;
					break;

				case 'wc-refunded':
					$order_count['refunded'] = $order_status->count;
					break;

				case 'wc-failed':
					$order_count['failed'] = $order_status->count;
					break;

			}
		}
	} else if( is_wp_error( $order_statuses ) ) {
		error_log( sprintf( '[store-toolkit] Warning: Deprecation warning in woo_st_dashboard_right_now(): %s', $order_statuses->get_error_message() ) );
	}

	$template = 'dashboard_right_now.php';
	if( file_exists( WOO_ST_PATH . 'templates/admin/' . $template ) ) {

		include_once( WOO_ST_PATH . 'templates/admin/' . $template );

	} else {

		$message = sprintf( __( 'We couldn\'t load the template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woocommerce-store-toolkit' ), $template, WOO_ST_PATH . 'includes/admin/...' );
?>
<p><strong><?php echo $message; ?></strong></p>
<p><?php _e( 'You can see this error for one of a few common reasons', 'woocommerce-store-toolkit' ); ?>:</p>
<ul class="ul-disc">
	<li><?php _e( 'WordPress was unable to create this file when the Plugin was installed or updated', 'woocommerce-store-toolkit' ); ?></li>
	<li><?php _e( 'The Plugin files have been recently changed and there has been a file conflict', 'woocommerce-store-toolkit' ); ?></li>
	<li><?php _e( 'The Plugin file has been locked and cannot be opened by WordPress', 'woocommerce-store-toolkit' ); ?></li>
</ul>
<p><?php _e( 'Jump onto our website and download a fresh copy of this Plugin as it might be enough to fix this issue. If this persists get in touch with us.', 'woocommerce-store-toolkit' ); ?></p>
<?php

	}

}

function woo_st_dashboard_sales_summary() {

	global $wpdb;

	// Set defaults
	$sales_today = (float)0;
	$sales_yesterday = (float)0;
	$sales_week = (float)0;
	$sales_last_week = (float)0;
	$sales_month = (float)0;
	$sales_last_month = (float)0;

	$post_type = 'shop_order';
	$order_status = implode( "','", apply_filters( 'woo_st_sales_order_status', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) );

	// Get totals all time
	if( false === ( $sales_all_time = get_transient( WOO_ST_PREFIX . '_sales_all_time' ) ) ) {
		$sales_all_time = $wpdb->get_var( "SELECT SUM(meta.meta_value) AS total_sales FROM {$wpdb->posts} AS posts
	LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
	WHERE meta.meta_key = '_order_total'
	AND posts.post_type = 'shop_order'
	AND posts.post_status IN ( '" . $order_status . "' )
	" );
		set_transient( WOO_ST_PREFIX . '_sales_all_time', $sales_all_time, HOUR_IN_SECONDS );
	}

	// Get totals for last month

	// Get totals for this month

	// Get totals for last week

	// Get totals for this week
	if( false === ( $sales_week = get_transient( WOO_ST_PREFIX . '_sales_week' ) ) ) {
		$sales_week = $wpdb->get_var( "SELECT SUM(meta.meta_value) AS total_sales FROM {$wpdb->posts} AS posts
LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
WHERE meta.meta_key = '_order_total'
AND posts.post_type = 'shop_order'
AND posts.post_status IN ( '" . $order_status . "' )
AND posts.post_date > '" . date( "Y-m-d 00:00:00", strtotime( 'last Monday' ) ) . "' 
AND posts.post_date < '" . date( "Y-m-d 23:59:59", current_time( 'timestamp' ) ) . "'
" );
		set_transient( WOO_ST_PREFIX . '_sales_week', $sales_week, HOUR_IN_SECONDS );
	}

	// Get totals for yesterday
	if( false === ( $sales_yesterday = get_transient( WOO_ST_PREFIX . '_sales_yesterday' ) ) ) {
		$sales_yesterday = $wpdb->get_var( "SELECT SUM(meta.meta_value) AS total_sales FROM {$wpdb->posts} AS posts
LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
WHERE meta.meta_key = '_order_total'
AND posts.post_type = 'shop_order'
AND posts.post_status IN ( '" . $order_status . "' )
AND posts.post_date > '" . date( "Y-m-d 00:00:00", strtotime( '-1 days' ) ) . "' 
AND posts.post_date < '" . date( "Y-m-d 23:59:59", strtotime( '-1 days' ) ) . "'
" );
		set_transient( WOO_ST_PREFIX . '_sales_yesterday', $sales_yesterday, HOUR_IN_SECONDS );
	}

	// Get totals for today
	if( false === ( $sales_today = get_transient( WOO_ST_PREFIX . '_sales_today' ) ) ) {
		$sales_today = $wpdb->get_var( "SELECT SUM(meta.meta_value) AS total_sales FROM {$wpdb->posts} AS posts
LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
WHERE meta.meta_key = '_order_total'
AND posts.post_type = 'shop_order'
AND posts.post_status IN ( '" . $order_status . "' )
AND posts.post_date > '" . date( "Y-m-d 00:00:00", current_time( 'timestamp' ) ) . "' 
AND posts.post_date < '" . date( "Y-m-d 23:59:59", current_time( 'timestamp' ) ) . "'
" );
		set_transient( WOO_ST_PREFIX . '_sales_today', $sales_today, HOUR_IN_SECONDS );
	}

	$template = 'dashboard_sales_summary.php';
	if( file_exists( WOO_ST_PATH . 'templates/admin/' . $template ) ) {

		include_once( WOO_ST_PATH . 'templates/admin/' . $template );

	} else {

		$message = sprintf( __( 'We couldn\'t load the template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woocommerce-store-toolkit' ), $template, WOO_ST_PATH . 'includes/admin/...' );
?>
<p><strong><?php echo $message; ?></strong></p>
<p><?php _e( 'You can see this error for one of a few common reasons', 'woocommerce-store-toolkit' ); ?>:</p>
<ul class="ul-disc">
	<li><?php _e( 'WordPress was unable to create this file when the Plugin was installed or updated', 'woocommerce-store-toolkit' ); ?></li>
	<li><?php _e( 'The Plugin files have been recently changed and there has been a file conflict', 'woocommerce-store-toolkit' ); ?></li>
	<li><?php _e( 'The Plugin file has been locked and cannot be opened by WordPress', 'woocommerce-store-toolkit' ); ?></li>
</ul>
<p><?php _e( 'Jump onto our website and download a fresh copy of this Plugin as it might be enough to fix this issue. If this persists get in touch with us.', 'woocommerce-store-toolkit' ); ?></p>
<?php

	}

}