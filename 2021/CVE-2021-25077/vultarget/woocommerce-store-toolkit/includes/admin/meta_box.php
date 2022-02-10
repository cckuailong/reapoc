<?php
include_once( WOO_ST_PATH . 'includes/admin/meta_box.php' );

function woo_st_add_data_meta_boxes( $post_type, $post = '' ) {

	if( $post->post_status == 'auto-draft' )
		return;

	// Product
	$post_type = 'product';
	if( apply_filters( 'woo_st_product_data_meta_box', true ) )
		add_meta_box( 'woo-product-post_data', __( 'Product Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_product_data_meta_box', $post_type, 'normal', 'default' );
	$post_type = 'product_variation';
	if( apply_filters( 'woo_st_product_data_meta_box', true ) )
		add_meta_box( 'woo-product-post_data', __( 'Product Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_product_data_meta_box', $post_type, 'normal', 'default' );

	// Order
	$post_type = 'shop_order';
	if( apply_filters( 'woo_st_order_data_meta_box', true ) )
		add_meta_box( 'woo-order-post_data', __( 'Order Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_order_data_meta_box', $post_type, 'normal', 'default' );
	if( apply_filters( 'woo_st_order_items_data_meta_box', true ) )
		add_meta_box( 'woo-order-post_item', __( 'Order Items Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_order_items_data_meta_box', $post_type, 'normal', 'default' );
	if( apply_filters( 'woo_st_order_refunds_data_meta_box', true ) )
		add_meta_box( 'woo-order-post_refund', __( 'Refunds Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_order_refunds_data_meta_box', $post_type, 'normal', 'default' );
	
	// So we can view the Related Orders meta box on the Edit Order screen
	$unlock_related_orders = get_option( WOO_ST_PREFIX . '_unlock_related_orders', 0 );
	if(
		!empty( $unlock_related_orders ) || 
		apply_filters( 'woo_st_order_related_orders_meta_box', false )
	) {
		add_meta_box( 'woo-order-related_orders', __( 'Related Orders', 'woocommerce-store-toolkit' ), 'woo_st_order_related_orders_meta_box', $post_type, 'side', 'default' );
	}

	// Coupon
	$post_type = 'shop_coupon';
	if( apply_filters( 'woo_st_coupon_data_meta_box', true ) )
		add_meta_box( 'woo-coupon-post_data', __( 'Coupon Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_coupon_data_meta_box', $post_type, 'normal', 'default' );

	// Attachment
	$post_type = 'attachment';
	if( apply_filters( 'woo_st_attachment_data_meta_box', true ) )
		add_meta_box( 'attachment-post_data', __( 'Attachment Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_attachment_data_meta_box', $post_type, 'normal', 'default' );

	// 3rd party

	// WooCommerce Subscriptions - http://www.woothemes.com/products/woocommerce-subscriptions/
	$post_type = 'shop_subscription';
	if( post_type_exists( $post_type ) ) {
		if( apply_filters( 'woo_st_order_data_meta_box', true ) )
			add_meta_box( 'woo-order-post_data', __( 'Subscription Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_order_data_meta_box', $post_type, 'normal', 'default' );
	}

	// WooCommerce - Store Exporter Deluxe - https://www.visser.com.au/plugins/store-exporter-deluxe/
	$post_type = 'scheduled_export';
	if( post_type_exists( $post_type ) ) {
		if( apply_filters( 'woo_st_scheduled_export_data_meta_box', true ) )
			add_meta_box( 'woo-scheduled_export-post_data', __( 'Scheduled Export Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_scheduled_export_data_meta_box', $post_type, 'normal', 'default' );
	}

	// WooCommerce - Store Exporter Deluxe - https://www.visser.com.au/plugins/store-exporter-deluxe/
	$post_type = 'export_template';
	if( apply_filters( 'woo_st_export_template_data_meta_box', true ) )
		add_meta_box( 'woo-coupon-post_data', __( 'Export Template Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_export_template_data_meta_box', $post_type, 'normal', 'default' );

	// WooCommerce Events - http://www.woocommerceevents.com/
	if( class_exists( 'WooCommerce_Events' ) ) {
		$post_type = 'event_magic_tickets';
		if( apply_filters( 'woo_st_event_data_meta_box', true ) )
			add_meta_box( 'woo-event-post_data', __( 'Event Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_event_data_meta_box', $post_type, 'normal', 'default' );
	}

	// WooCommerce Bookings - http://www.woothemes.com/products/woocommerce-bookings/
	if( class_exists( 'WC_Bookings' ) ) {
		$post_type = 'wc_booking';
		if( apply_filters( 'woo_st_booking_data_meta_box', true ) )
			add_meta_box( 'woo-booking-post_data', __( 'Booking Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_booking_data_meta_box', $post_type, 'normal', 'default' );
	}

	// WooCommerce Memberships - http://www.woothemes.com/products/woocommerce-memberships/
	if( function_exists( 'init_woocommerce_memberships' ) ) {
		$post_type = 'wc_user_membership';
		if( apply_filters( 'woo_st_user_membership_data_meta_box', true ) )
			add_meta_box( 'woo-user_membership-post_data', __( 'User Membership Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_user_membership_data_meta_box', $post_type, 'normal', 'low' );
		$post_type = 'wc_membership_plan';
		if( apply_filters( 'woo_st_membership_plan_data_meta_box', true ) )
			add_meta_box( 'woo-membership_plan-post_data', __( 'Membership Plan Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_membership_plan_data_meta_box', $post_type, 'normal', 'low' );
		// These guys think they are special...
		add_filter( 'wc_memberships_allowed_meta_box_ids', 'woo_st_extend_wc_memberships_allowed_meta_box_ids' );
	}

	// WooCommerce Appointments - http://www.bizzthemes.com/plugins/woocommerce-appointments/
	if( class_exists( 'WC_Appointments' ) ) {
		$post_type = 'wc_appointment';
		if( apply_filters( 'woo_st_appointment_data_meta_box', true ) )
			add_meta_box( 'woo-appointment-post_data', __( 'Appointment Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_generic_data_meta_box', $post_type, 'normal', 'low' );
	}

	// Advanced Custom Fields - http://www.advancedcustomfields.com
	if( class_exists( 'acf' ) ) {
		$acf_version = ( defined( 'ACF_VERSION' ) ? ACF_VERSION : false );
		if( version_compare( $acf_version, '5.6', '>=' ) )
			$post_type = 'acf-field-group';
		else
			$post_type = 'acf';
		if( apply_filters( 'woo_st_acf_data_meta_box', true ) )
			add_meta_box( 'woo-acf-post_data', __( 'ACF Post Meta', 'woocommerce-store-toolkit' ), 'woo_st_generic_data_meta_box', $post_type, 'normal', 'low' );
	}

}

// WooCommerce Memberships - http://www.woothemes.com/products/woocommerce-memberships/
function woo_st_extend_wc_memberships_allowed_meta_box_ids( $meta_boxes ) {

	$meta_boxes[] = 'woo-user_membership-post_data';
	$meta_boxes[] = 'woo-membership_plan-post_data';
	return $meta_boxes;

}

function woo_st_product_data_meta_box() {

	global $post;

	$post_meta = get_post_custom( $post->ID );

	$type = 'product';
	$template = 'post_data.php';
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

function woo_st_order_data_meta_box() {

	global $post;

	$post_meta = get_post_custom( $post->ID );

	$type = 'order';
	$template = 'post_data.php';
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

function woo_st_order_items_data_meta_box() {

	global $post, $wpdb;

	$order_items_sql = $wpdb->prepare( "SELECT `order_item_id` as id, `order_item_name` as name, `order_item_type` as type FROM `" . $wpdb->prefix . "woocommerce_order_items` WHERE `order_id` = %d", $post->ID );
	if( $order_items = $wpdb->get_results( $order_items_sql ) ) {
		foreach( $order_items as $key => $order_item ) {
			$order_itemmeta_sql = $wpdb->prepare( "SELECT `meta_key`, `meta_value` FROM `" . $wpdb->prefix . "woocommerce_order_itemmeta` AS order_itemmeta WHERE `order_item_id` = %d ORDER BY `order_itemmeta`.`meta_key` ASC", $order_item->id );
			$order_items[$key]->meta = $wpdb->get_results( $order_itemmeta_sql );
		}
	}

	$type = 'order_item';
	$template = 'order_item_data.php';
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

function woo_st_order_refunds_data_meta_box() {

	global $post;

	$refunds = woo_st_get_order_refunds( $post->ID );

	$type = 'refund';
	$template = 'order_refund_data.php';
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

function woo_st_order_related_orders_meta_box() {

	global $post;

	$post_id = ( $post->ID ? $post->ID : false );
	$orders = array();
	$user_id = get_post_meta( $post_id, '_customer_user', true );
	$matching = false;
	if( !empty( $user_id ) ) {
		$matching = 'user_id';
		// Check if a User has been linked to this Order
		$args = array(
			'fields' => 'ids'
		);
		$orders = woo_st_get_user_orders( $user_id, $args );
	} else {
		$matching = 'billing_email';
		// Fallback to the Billing e-mail address
		$billing_email = get_post_meta( $post_id, '_billing_email', true );
		if( !empty( $billing_email ) ) {
			$post_type = 'shop_order';
			$args = array(
				'post_type' => $post_type,
				'fields' => 'ids'
			);
			$woocommerce_version = woo_get_woo_version();
			// Check if this is a pre-WooCommerce 2.2 instance
			if( version_compare( $woocommerce_version, '2.2' ) >= 0 )
				$args['post_status'] = ( function_exists( 'wc_get_order_statuses' ) ? apply_filters( 'woo_st_order_post_status', array_keys( wc_get_order_statuses() ) ) : 'any' );
			else
				$args['post_status'] = apply_filters( 'woo_st_order_post_status', 'publish' );
			$args['meta_query'][] = array(
				'key' => '_billing_email',
				'value' => $billing_email
			);
			$order_ids = new WP_Query( $args );
			$orders = ( !empty( $order_ids->posts ) ? $order_ids->posts : false );
		}
	}

	// Remove this Order from the list
	if( !empty( $orders ) ) {
		$needle = array_search( $post_id, $orders );
		if( $needle !== false )
			unset( $orders[$needle] );
	}

	$type = 'order';
	$template = 'order_related_orders.php';
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

function woo_st_coupon_data_meta_box() {

	global $post;

	$post_meta = get_post_custom( $post->ID );

	$type = 'coupon';
	$template = 'post_data.php';
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

function woo_st_export_template_data_meta_box() {

	global $post;

	$post_meta = get_post_custom( $post->ID );

	$type = 'export_template';
	$template = 'post_data.php';
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

function woo_st_category_data_meta_box( $term = '', $taxonomy = '' ) {

	$term_taxonomy = 'product_cat';
	$term_meta = get_term_meta( $term->term_id );

	// We support up to 5 levels deep; can be extended further as needed

	// Term
	$category_heirachy = $term->name;
	$category_depth = 1;
	if( !empty( $term->parent ) ) {
		// Term > Term
		$parent = get_term( $term->parent );
		if( !is_wp_error( $parent ) ) {
			$category_depth++;
			$category_heirachy = $parent->name . ' &raquo; ' . $category_heirachy;
			// Term > Term > Term
			$parent = get_term( $parent->parent );
			if( !is_wp_error( $parent ) ) {
				$category_depth++;
				$category_heirachy = $parent->name . ' &raquo; ' . $category_heirachy;
				// Term > Term > Term > Term
				$parent = get_term( $parent->parent );
				if( !is_wp_error( $parent ) ) {
					$category_depth++;
					$category_heirachy = $parent->name . ' &raquo; ' . $category_heirachy;
					// Term > Term > Term > Term > Term
					$parent = get_term( $parent->parent );
					if( !is_wp_error( $parent ) ) {
						$category_heirachy = $parent->name . ' &raquo; ' . $category_heirachy;
					}
				}
			}
		}
	}

	$type = 'category';
	$class = 'category_data';

	$template = 'term_data.php';
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

	$template = 'category_data.php';
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

function woo_st_tag_data_meta_box( $term = '', $taxonomy = '' ) {

	$term_taxonomy = 'product_tag';
	$term_meta = get_term_meta( $term->term_id );

	$type = 'tag';
	$class = 'tag_data';
	$template = 'term_data.php';
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

function woo_st_brand_data_meta_box( $term = '', $taxonomy = '' ) {

	$term_taxonomy = 'product_brand';
	$term_meta = get_term_meta( $term->term_id );

	$type = 'brand';
	$class = 'brand_data';
	$template = 'term_data.php';
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

function woo_st_product_vendor_data_meta_box( $term = '', $taxonomy = '' ) {

	$term_taxonomy = 'yith_shop_vendor';
	$term_meta = get_term_meta( $term->term_id );

	$type = 'product_vendor';
	$template = 'post_data.php';
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

function woo_st_user_orders( $user ) {

	if( !current_user_can( 'manage_woocommerce' ) )
		return;

	$user_id = $user->data->ID;
	$posts_per_page = apply_filters( 'woo_st_user_orders_posts_per_page', 10 );
	$args = array(
		'numberposts' => $posts_per_page
	);
	$total_orders = woo_st_get_user_orders( $user_id, $args, 'found_posts' );
	$paged = ( isset( $_GET['paged'] ) ? $_GET['paged'] : 1 );
	if( !empty( $paged ) )
		$args['paged'] = $paged;
	$max_page = absint( $total_orders / $posts_per_page );
	$orders = ( !empty( $total_orders ) ? woo_st_get_user_orders( $user_id, $args ) : false );

	$type = 'user';
	$template = 'user_orders.php';
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


function woo_st_user_data_meta_box( $user = '' ) {

	$user_id = $user->data->ID;
	$user_meta = get_user_meta( $user_id );

	$template = 'user_data.php';
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

function woo_st_scheduled_export_data_meta_box() {

	global $post;

	$post_meta = get_post_custom( $post->ID );

	$type = 'scheduled_export';
	$template = 'post_data.php';
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

function woo_st_event_data_meta_box() {

	global $post;

	$post_meta = get_post_custom( $post->ID );

	$type = 'event';
	$template = 'post_data.php';
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

function woo_st_booking_data_meta_box() {

	global $post;

	$post_meta = get_post_custom( $post->ID );

	$type = 'booking';
	$template = 'post_data.php';
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

function woo_st_user_membership_data_meta_box() {

	global $post;

	$post_meta = get_post_custom( $post->ID );

	$type = 'user_membership';
	$template = 'post_data.php';
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

function woo_st_generic_data_meta_box() {

	global $post;

	$post_meta = get_post_custom( $post->ID );

	$type = 'post';
	$template = 'post_data.php';
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

function woo_st_membership_plan_data_meta_box() {

	global $post;

	$post_meta = get_post_custom( $post->ID );

	$type = 'membership_plan';
	$template = 'post_data.php';
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

function woo_st_attachment_data_meta_box() {

	global $post;

	$post_meta = get_post_custom( $post->ID );

	$type = 'attachment';
	$template = 'post_data.php';
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