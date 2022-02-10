<?php
/**
 * Code to aid with user data privacy, e.g. GDPR compliance
 * 
 * @since  1.9.5
 */

/** 
 * Add suggested Privacy Policy language for PMPro
 * @since 1.9.5
 */
function pmpro_add_privacy_policy_content() {	
	// Check for support.
	if ( ! function_exists( 'wp_add_privacy_policy_content') ) {
		return;
	}

	$content = '';
	$content .= '<h2>' . __( 'Data Collected to Manage Your Membership', 'paid-memberships-pro' ) . '</h2>';
	$content .= '<p>' . __( "At checkout, we will collect your name, email address, username, and password. This information is used to setup your account for our site. If you are redirected to an offsite payment gateway to complete your payment, we may store this information in a temporary session variable to setup your account when you return to our site.", 'paid-memberships-pro' ) . '</p>';
	$content .= '<p>' . __( "At checkout, we may also collect your billing address and phone number. This information is used to confirm your credit card. The billing address and phone number are saved by our site to prepopulate the checkout form for future purchases and so we can get in touch with you if needed to discuss your order.", 'paid-memberships-pro' ) . '</p>';
	$content .= '<p>' . __( "At checkout, we may also collect your credit card number, expiration date, and security code. This information is passed to our payment gateway to process your purchase. The last 4 digits of your credit card number and the expiration date are saved by our site to use for reference and to send you an email if your credit card will expire before the next recurring payment.", 'paid-memberships-pro' ) . '</p>';
	$content .= '<p>' . __( "When logged in, we use cookies to track some of your activity on our site including logins, visits, and page views.", 'paid-memberships-pro' ) . '</p>';

	wp_add_privacy_policy_content( 'Paid Memberships Pro', $content );
}
add_action( 'admin_init', 'pmpro_add_privacy_policy_content' );

/**
 * Register the personal data eraser for PMPro
 * @param array $erasers All erasers added so far
 */
function pmpro_register_personal_data_erasers( $erasers = array() ) {
	$erasers[] = array(
 		'eraser_friendly_name' => __( 'Paid Memberships Pro Data' ),
 		'callback'             => 'pmpro_personal_data_eraser',
 	);

	return $erasers;
}
add_filter( 'wp_privacy_personal_data_erasers', 'pmpro_register_personal_data_erasers' );

/**
 * Personal data eraser for PMPro data.
 * @since 1.9.5
 * @param string $email_address Email address of the user to be erased.
 * @param int    $page          For batching
 */
function pmpro_personal_data_eraser( $email_address, $page = 1 ) {
	global $wpdb;

	// What user is this?
	$user = get_user_by( 'email', $email_address );

	$num_items_removed = 0;
	$num_items_retained = 0;
	$messages = array();
	$done = false;

	if( !empty( $user ) ) {
		// Erase any data we have about this user.
		$user_meta_fields_to_erase = pmpro_get_personal_user_meta_fields_to_erase();

		$sqlQuery = $wpdb->prepare( "DELETE FROM {$wpdb->usermeta} WHERE user_id = %d AND meta_key IN( [IN_CLAUSE] )", intval( $user->ID ) );

		$in_clause_data = array_map( 'esc_sql', $user_meta_fields_to_erase );
		$in_clause = "'" . implode( "', '", $in_clause_data ) . "'";	
		$sqlQuery = preg_replace( '/\[IN_CLAUSE\]/', $in_clause, $sqlQuery );

		$wpdb->query( $sqlQuery );
		$num_deleted = $wpdb->rows_affected;
		$num_items_removed += $num_deleted;

		// We retain all orders. Get the number of them to report them as retained.
		$sqlQuery = $wpdb->prepare( "SELECT COUNT(id) FROM {$wpdb->pmpro_membership_orders} WHERE user_id = %d", intval( $user->ID ) );
		$num_orders = $wpdb->get_var( $sqlQuery );
		if( $num_orders > 0 ) {
			$num_items_retained += $num_orders;
			// We could have used _n below, but that doesn't work well with our script for generating the .pot file.
			if( $num_orders == 1 ) {
				$messages[] = __( '1 PMPro order was retained for business records.', 'paid-memberships-pro' );
			} else {
				$messages[] = sprintf( __( '%d PMPro orders were retained for business records.', 'paid-memberships-pro' ), $num_orders );
			}
		}

		// Warn the admin if this user has an active subscription
		$messages[] = __( "Please note that data erasure will not cancel a user's membership level or any active subscriptions. Please edit or delete the user through the WordPress dashboard.", 'paid-memberships-pro' );
	}

	// Set done to false if we still have stuff to erase.
	$done = true;

	return array(
 		'items_removed'  => $num_items_removed,
 		'items_retained' => $num_items_retained,
 		'messages'       => $messages,
 		'done'           => $done,
 	);
}

/**
 * Register the personal data exporter for PMPro.
 * @since 1.9.5
 * @param array $exporters All exporters added so far
 */
function pmpro_register_personal_data_exporters( $exporters ) {
	$exporters[] = array(
		'exporter_friendly_name' => __( 'Paid Memberships Pro Data' ),
		'callback'               => 'pmpro_personal_data_exporter',
	);

	return $exporters;
}
add_filter( 'wp_privacy_personal_data_exporters', 'pmpro_register_personal_data_exporters' );

/**
 * Personal data exporter for PMPro data.
 * @since 1.9.5
 */
function pmpro_personal_data_exporter( $email_address, $page = 1 ) {
	global $wpdb;

	$data_to_export = array();

	// What user is this?
	$user = get_user_by( 'email', $email_address );

	if( !empty( $user ) ) {
		// Add data stored in user meta.
		$personal_user_meta_fields = pmpro_get_personal_user_meta_fields();
		$sqlQuery = $wpdb->prepare( 
			"SELECT meta_key, meta_value
			 FROM {$wpdb->usermeta}
			 WHERE user_id = %d
			 AND meta_key IN( [IN_CLAUSE] )", intval( $user->ID ) );
		
		$in_clause_data = array_map( 'esc_sql', array_keys( $personal_user_meta_fields ) );
		$in_clause = "'" . implode( "', '", $in_clause_data ) . "'";	
		$sqlQuery = preg_replace( '/\[IN_CLAUSE\]/', $in_clause, $sqlQuery );
		
		$personal_user_meta_data = $wpdb->get_results( $sqlQuery, OBJECT_K );
		
		$user_meta_data_to_export = array();
		foreach( $personal_user_meta_fields as $key => $name ) {
			if( !empty( $personal_user_meta_data[$key] ) ) {
				$value = $personal_user_meta_data[$key]->meta_value;
			} else {
				$value = '';
			}

			$user_meta_data_to_export[] = array(
				'name' => $name,
				'value' => $value,
			);
		}

		$data_to_export[] = array(
			'group_id'    => 'pmpro_user_data',
			'group_label' => __( 'Paid Memberships Pro User Data' ),
			'item_id'     => "user-{$user->ID}",
			'data'        => $user_meta_data_to_export,
		);
		

		// Add membership history.
		$sqlQuery = $wpdb->prepare(
			"SELECT * FROM {$wpdb->pmpro_memberships_users}
			 WHERE user_id = %d
			 ORDER BY id DESC", intval( $user->ID ) );
			 
		$history = $wpdb->get_results( $sqlQuery );
		foreach( $history as $item ) {
			if( $item->enddate === null || $item->enddate == '0000-00-00 00:00:00' ) {
				$item->enddate = __( 'Never', 'paid-memberships-pro' );
			} else {
				$item->enddate = date( get_option( 'date_format' ), strtotime( $item->enddate, current_time( 'timestamp' ) ) );
			}

			$history_data_to_export = array(
				array(
					'name'  => __( 'Level ID', 'paid-memberships-pro' ),
					'value' => $item->membership_id, 
				),
				array(
					'name'  => __( 'Start Date', 'paid-memberships-pro' ),
					'value' => date( get_option( 'date_format' ), strtotime( $item->startdate, current_time( 'timestamp' ) ) ),
				),
				array(
					'name'  => __( 'Date Modified', 'paid-memberships-pro' ),
					'value' => date( get_option( 'date_format' ), strtotime( $item->modified, current_time( 'timestamp' ) ) ),
				),
				array(
					'name'  => __( 'End Date', 'paid-memberships-pro' ),
					'value' => $item->enddate,
				),
				array(
					'name'  => __( 'Level Cost', 'paid-memberships-pro' ),
					'value' => pmpro_getLevelCost( $item, false, true ),
				),
				array(
					'name' => __( 'Status', 'paid-memberships-pro' ),
					'value' => $item->status,
				),
			);

			$data_to_export[] = array(
				'group_id'    => 'pmpro_membership_history',
				'group_label' => __( 'Paid Memberships Pro Membership History' ),
				'item_id'     => "memberships_users-{$item->id}",
				'data'        => $history_data_to_export,
			);
		}

		// Add order history.
		$sqlQuery = $wpdb->prepare(
			"SELECT id FROM {$wpdb->pmpro_membership_orders}
			 WHERE user_id = %d
			 ORDER BY id DESC", intval( $user->ID ) );
			 
		$order_ids = $wpdb->get_col( $sqlQuery );		
		
		foreach( $order_ids as $order_id ) {
			$order = new MemberOrder( $order_id );
			$order->getMembershipLevel();
			
			$order_data_to_export = array(
				array(
					'name' => __( 'Order ID', 'paid-memberships-pro' ),
					'value' => $order->id,
				),
				array(
					'name' => __( 'Order Code', 'paid-memberships-pro' ),
					'value' => $order->code,
				),
				array(
					'name' => __( 'Order Date', 'paid-memberships-pro' ),
					'value' => date( get_option( 'date_format' ), $order->getTimestamp() ),
				),
				array(
					'name' => __( 'Level', 'paid-memberships-pro' ),
					'value' => $order->membership_level->name,
				),
				array(
					'name' => __( 'Billing Name', 'paid-memberships-pro' ),
					'value' => $order->billing->name,
				),
				array(
					'name' => __( 'Billing Street', 'paid-memberships-pro' ),
					'value' => $order->billing->street,
				),
				array(
					'name' => __( 'Billing City', 'paid-memberships-pro' ),
					'value' => $order->billing->city,
				),
				array(
					'name' => __( 'Billing State', 'paid-memberships-pro' ),
					'value' => $order->billing->state,
				),
				array(
					'name' => __( 'Billing Postal Code', 'paid-memberships-pro' ),
					'value' => $order->billing->zip,
				),
				array(
					'name' => __( 'Billing Country', 'paid-memberships-pro' ),
					'value' => $order->billing->country,
				),
				array(
					'name' => __( 'Billing Phone', 'paid-memberships-pro' ),
					'value' => formatPhone( $order->billing->phone ),
				),
				array(
					'name' => __( 'Sub Total', 'paid-memberships-pro' ),
					'value' => $order->subtotal,
				),
				array(
					'name' => __( 'Tax', 'paid-memberships-pro' ),
					'value' => $order->tax,
				),
				array(
					'name' => __( 'Coupon Amount', 'paid-memberships-pro' ),
					'value' => $order->couponamount,
				),
				array(
					'name' => __( 'Total', 'paid-memberships-pro' ),
					'value' => $order->total,
				),
				array(
					'name' => __( 'Payment Type', 'paid-memberships-pro' ),
					'value' => $order->payment_type,
				),
				array(
					'name' => __( 'Card Type', 'paid-memberships-pro' ),
					'value' => $order->cardtype,
				),
				array(
					'name' => __( 'Account Number', 'paid-memberships-pro' ),
					'value' => $order->accountnumber,
				),
				array(
					'name' => __( 'Expiration Month', 'paid-memberships-pro' ),
					'value' => $order->expirationmonth,
				),
				array(
					'name' => __( 'Expiration Year', 'paid-memberships-pro' ),
					'value' => $order->expirationyear,
				),
				array(
					'name' => __( 'Status', 'paid-memberships-pro' ),
					'value' => $order->status,
				),
				array(
					'name' => __( 'Gateway', 'paid-memberships-pro' ),
					'value' => $order->gateway,
				),
				array(
					'name' => __( 'Gateway Environment', 'paid-memberships-pro' ),
					'value' => $order->gateway_environment,
				),
				array(
					'name' => __( 'Payment Transaction ID', 'paid-memberships-pro' ),
					'value' => $order->payment_transaction_id,
				),
				array(
					'name' => __( 'Subscription Transaction ID', 'paid-memberships-pro' ),
					'value' => $order->subscription_transaction_id,
				),
				// Note: Order notes, session_id, and paypal_token are excluded.
			);
			
			$data_to_export[] = array(
				'group_id'    => 'pmpro_order_history',
				'group_label' => __( 'Paid Memberships Pro Order History' ),
				'item_id'     => "membership_order-{$order->id}",
				'data'        => $order_data_to_export,
			);
		}		
	}

	$done = true;
	
	return array(
		'data' => $data_to_export,
		'done' => $done,
	);
}

/**
 * Get list of user meta fields with labels to include in the PMPro data exporter
 * @since 1.9.5
 */
function pmpro_get_personal_user_meta_fields() {
	$fields = array(
		'pmpro_bfirstname' => __( 'Billing First Name', 'paid-memberships-pro' ),
		'pmpro_blastname' => __( 'Billing Last Name', 'paid-memberships-pro' ),
		'pmpro_baddress1' => __( 'Billing Address 1', 'paid-memberships-pro' ),
		'pmpro_baddress2' => __( 'Billing Address 2', 'paid-memberships-pro' ),
		'pmpro_bcity' => __( 'Billing City', 'paid-memberships-pro' ),
		'pmpro_bstate' => __( 'Billing State/Province', 'paid-memberships-pro' ),
		'pmpro_bzipcode' => __( 'Billing Postal Code', 'paid-memberships-pro' ),
		'pmpro_bphone' => __( 'Billing Phone Number', 'paid-memberships-pro' ),
		'pmpro_bcountry' => __( 'Billing Country', 'paid-memberships-pro' ),
		'pmpro_CardType' => __( 'Credit Card Type', 'paid-memberships-pro' ),
		'pmpro_AccountNumber' => __( 'Credit Card Account Number', 'paid-memberships-pro' ),
		'pmpro_ExpirationMonth' => __( 'Credit Card Expiration Month', 'paid-memberships-pro' ),
		'pmpro_ExpirationYear' => __( 'Credit Card Expiration Year', 'paid-memberships-pro' ),
		'pmpro_logins' => __( 'Login Data', 'paid-memberships-pro' ),
		'pmpro_visits' => __( 'Visits Data', 'paid-memberships-pro' ),
		'pmpro_views' => __( 'Views Data', 'paid-memberships-pro' ),
	);

	$fields = apply_filters( 'pmpro_get_personal_user_meta_fields', $fields );

	return $fields;
}

/**
 * Get list of user meta fields to include in the PMPro data eraser
 * @since 1.9.5
 */
function pmpro_get_personal_user_meta_fields_to_erase() {
	$fields = array(
		'pmpro_bfirstname',
		'pmpro_blastname',
		'pmpro_baddress1',
		'pmpro_baddress2',
		'pmpro_bcity',
		'pmpro_bstate',
		'pmpro_bzipcode',
		'pmpro_bphone',
		'pmpro_bcountry',
		'pmpro_CardType',
		'pmpro_AccountNumber',
		'pmpro_ExpirationMonth',
		'pmpro_ExpirationYear',
		'pmpro_logins',
		'pmpro_visits',
		'pmpro_views',
	);

	$fields = apply_filters( 'pmpro_get_personal_user_meta_fields_to_erase', $fields );

	return $fields;
}

/**
 * Save a TOS consent timestamp to user meta.
 * @since 1.9.5
 */
function pmpro_save_consent( $user_id = NULL, $post_id = NULL, $post_modified = NULL, $order_id = NULL ) {
	// Default to current user.
	if( empty( $user_id ) ) {
		global $current_user;
		$user_id = $current_user->ID;
	}

	if( empty( $user_id ) ) {
		return false;
	}

	// Default to the TOS post chosen on the advanced settings page
	if( empty( $post_id ) ) {
		$post_id = pmpro_getOption( 'tospage' );
	}

	if( empty( $post_id ) ) {
		return false;
	}

	$post = get_post( $post_id );

	if( empty( $post_modified ) ) {
		$post_modified = $post->post_modified;
	}

	$log = pmpro_get_consent_log( $user_id );
	$log[] = array(
		'user_id' => $user_id,
		'post_id' => $post_id,
		'post_modified' => $post_modified,
		'order_id' => $order_id,
		'consented' => true,
		'timestamp' => current_time( 'timestamp' ),
	);

	update_user_meta( $user_id, 'pmpro_consent_log', $log );
	return true;
}

/**
 * Get the TOS consent log from user meta.
 * @since  1.9.5
 */
function pmpro_get_consent_log( $user_id = NULL, $reversed = true ) {
	// Default to current user.
	if( empty( $user_id ) ) {
		global $current_user;
		$user_id = $current_user->ID;
	}

	if( empty( $user_id ) ) {
		return false;
	}

	$log = get_user_meta( $user_id, 'pmpro_consent_log', true );

	// Default log.
	if( empty( $log ) ) {
		$log = array();
	}

	if( $reversed ) {
		$log = array_reverse( $log );
	}

	return $log;
}

/**
 * Update TOS consent log after checkout.
 * @since 1.9.5
 */
function pmpro_after_checkout_update_consent( $user_id, $order ) {
	if( !empty( $_REQUEST['tos'] ) ) {
		$tospage_id = pmpro_getOption( 'tospage' );
		pmpro_save_consent( $user_id, $tospage_id, NULL, $order->id );
	} elseif ( !empty( $_SESSION['tos'] ) ) {
		// PayPal Express and others might save tos info into a session variable
		$tospage_id = $_SESSION['tos']['post_id'];
		$tospage_modified = $_SESSION['tos']['post_modified'];
		pmpro_save_consent( $user_id, $tospage_id, $tospage_modified, $order->id );
		unset( $_SESSION['tos'] );
	}
}
add_action( 'pmpro_after_checkout', 'pmpro_after_checkout_update_consent', 10, 2 );
add_action( 'pmpro_before_send_to_paypal_standard', 'pmpro_after_checkout_update_consent', 10, 2);
add_action( 'pmpro_before_send_to_twocheckout', 'pmpro_after_checkout_update_consent', 10, 2);

/**
 * Convert a consent entry into a English sentence.
 * @since  1.9.5
 */
function pmpro_consent_to_text( $entry ) {
	// Check for bad data. Shouldn't happen in practice.
	if ( empty( $entry ) || empty( $entry['user_id'] ) ) {		
		return '';
	}
	
	$user = get_userdata( $entry['user_id'] );
	$post = get_post( $entry['post_id'] );

	$s = sprintf( __('%s agreed to %s (ID #%d, last modified %s) on %s.' ),
				  $user->display_name,
				  $post->post_title,
				  $post->ID,
				  $entry['post_modified'],
				  date( get_option( 'date_format' ), $entry['timestamp'] ) );

	if( !pmpro_is_consent_current( $entry ) ) {
		$s .= ' ' . __( 'That post has since been updated.', 'paid-memberships-pro' );
	}

	return $s;
}

/**
 * Check if a consent entry is current.
 * @since  1.9.5
 */
function pmpro_is_consent_current( $entry ) {
	$post = get_post( $entry['post_id'] );
	if( !empty( $post ) && !empty( $post->post_modified ) && $post->post_modified == $entry['post_modified'] ) {
		return true;
	}
	return false;
}
