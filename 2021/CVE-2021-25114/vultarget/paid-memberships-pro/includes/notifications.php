<?php
/**
 * This code calls the server at notifications.paidmembershipspro.com
 * to see if there are any notifications to display to the user.
 * Notifications are shown on the PMPro settings pages in the dashboard.
 * Runs on the wp_ajax_pmpro_notifications hook.
 * Note we exit instead of returning because this is loaded via AJAX.
 */
function pmpro_notifications() {
	if ( current_user_can( 'manage_options' ) ) {		
		$notification = pmpro_get_next_notification();		
		if ( empty( $notification ) ) {
			exit;
		}
		
		$paused = pmpro_notifications_pause();		
		if ( $paused && empty( $_REQUEST['pmpro_notification'] ) && $notification->priority !== 1 ) {
			exit;
		}
		
		// Okay show the notification.
		?>
		<div class="pmpro_notification" id="<?php echo $notification->id; ?>">
		<?php if ( $notification->dismissable ) { ?>
			<button type="button" class="pmpro-notice-button notice-dismiss" value="<?php echo $notification->id; ?>"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'paid-memberships-pro' ); ?></span></button>
		<?php } ?>
			<div class="pmpro_notification-<?php echo $notification->type; ?>">
				<h3><span class="dashicons dashicons-<?php esc_attr_e( $notification->dashicon ); ?>"></span> <?php esc_html_e( $notification->title ); ?></h3>
				<?php 
					$allowed_html = array (
						'a' => array (
							'class' => array(),
							'href' => array(),
							'target' => array(),
							'title' => array(),
						),
						'p' => array(
							'class' => array(),
						),
						'b' => array(
							'class' => array(),
						),
						'em' => array(
							'class' => array(),
						),
						'br' => array(),
						'strike' => array(),
					);
					echo wp_kses( $notification->content, $allowed_html );
				?>
			</div>
		</div>
		<?php		
	}
	
	exit;
}
add_action( 'wp_ajax_pmpro_notifications', 'pmpro_notifications' );

/**
 * Get the highest priority applicable notification from the list.
 */
function pmpro_get_next_notification() {
	global $current_user;	
	if ( empty( $current_user->ID ) ) {
		return false;
	}
	
	// If debugging, clear the transient and get a specific notification.
	if ( ! empty( $_REQUEST['pmpro_notification'] ) ) {
		delete_transient( 'pmpro_notifications_' . PMPRO_VERSION );
		$pmpro_notifications = pmpro_get_all_notifications();
				
		if ( !empty( $pmpro_notifications ) ) {
			foreach( $pmpro_notifications as $notification ) {
				if ( $notification->id == $_REQUEST['pmpro_notification'] ) {
					return $notification;
				}
			}
			
			return false;
		} else {
			return false;
		}
	}
	
	// Get all applicable notifications.
	$pmpro_notifications = pmpro_get_all_notifications();
	if ( empty( $pmpro_notifications ) ) {
		return false;
	}
		
	// Filter out archived notifications.
	$pmpro_filtered_notifications = array();
	$archived_notifications = get_user_meta( $current_user->ID, 'pmpro_archived_notifications', true );
	foreach ( $pmpro_notifications as $notification ) {		
		if ( ( is_array( $archived_notifications ) && array_key_exists( $notification->id, $archived_notifications ) ) ) {
			continue;
		}

		$pmpro_filtered_notifications[] = $notification;		
	}	
		
	// Return the first one.
	if ( ! empty( $pmpro_filtered_notifications ) ) {
		$next_notification = $pmpro_filtered_notifications[0];
	} else {
		$next_notification = false;
	}
	
	return $next_notification;
}

/**
 * Get notifications from the notification server.
 */
function pmpro_get_all_notifications() {
	$pmpro_notifications = get_transient( 'pmpro_notifications_' . PMPRO_VERSION );
		
	if ( empty( $pmpro_notifications ) ) {
		// Set to NULL in case the below times out or fails, this way we only check once a day.
		set_transient( 'pmpro_notifications_' . PMPRO_VERSION, 'NULL', 86400 );
		
		// We use the filter to hit our testing servers.
		$pmpro_notification_url = apply_filters( 'pmpro_notifications_url', esc_url( 'https://notifications.paidmembershipspro.com/v2/notifications.json' ) );

		// Get notifications.
		$remote_notifications = wp_remote_get( $pmpro_notification_url );		
		$pmpro_notifications = json_decode( wp_remote_retrieve_body( $remote_notifications ) );
		
		// Update transient if we got something.
		if ( ! empty( $pmpro_notifications ) ) {
			set_transient( 'pmpro_notifications_' . PMPRO_VERSION, $pmpro_notifications, 86400 );
		}
	}
	
	// We expect an array.
	if( ! is_array( $pmpro_notifications ) ) {
		$pmpro_notifications = array();
	}
	
	// Filter notifications by start/end date.
	$pmpro_active_notifications = array();
	foreach( $pmpro_notifications as $notification ) {		
		$pmpro_active_notifications[] = $notification;
	}
		
	// Filter out notifications based on show/hide rules.
	$pmpro_applicable_notifications = array();
	foreach( $pmpro_active_notifications as $notification ) {
		if ( pmpro_is_notification_applicable( $notification ) ) {
			$pmpro_applicable_notifications[] = $notification;			
		}
	}
		
	// Sort by priority.	
	$pmpro_applicable_notifications = wp_list_sort( $pmpro_applicable_notifications, 'priority' );
	
	return $pmpro_applicable_notifications;
}

/**
 * Check rules for a notification.
 * @param object $notification The notification object.
 * @returns bool true if notification should be shown, false if not.
 */
function pmpro_is_notification_applicable( $notification ) {
	// If one is specified by URL parameter, it's allowed.
	if ( !empty( $_REQUEST['pmpro_notification'] ) && $notification->id == intval( $_REQUEST['pmpro_notification'] ) ) {
		return true;
	}
	
	// Hide if today's date is before notification start date.
	if ( date( 'Y-m-d', current_time( 'timestamp' ) ) < $notification->starts ) {
		return false;
	}

	// Hide if today's date is after end date.
	if ( date( 'Y-m-d', current_time( 'timestamp' ) ) > $notification->ends ) {
		return false;
	}

	// Check priority, e.g. if only security notifications should be shown.
	if ( $notification->priority > pmpro_get_max_notification_priority() ) {
		return false;
	}
	
	// Check show rules.
	if ( ! pmpro_should_show_notification( $notification ) ) {
		return false;
	}
	
	// Check hide rules.
	if ( pmpro_should_hide_notification( $notification ) ) {
		return false;
	}
	
	// If we get here, show it.
	return true;
}

/**
 * Check a notification to see if we should show it
 * based on the rules set.
 * Shows if ALL rules are true. (AND)
 * @param object $notification The notification object.
 */
function pmpro_should_show_notification( $notification ) {
	// default to showing
	$show = true;
	
	if ( !empty( $notification->show_if ) ) {
		foreach( $notification->show_if as $test => $data ) {
			$test_function = 'pmpro_notification_test_' . $test;
			if ( function_exists( $test_function ) ) {
				$show = call_user_func( $test_function , $data );
				if ( ! $show ) {
					// one test failed, let's not show
					break;
				}
			}
		}
	}
	
	return $show;
}

/**
 * Check a notification to see if we should hide it
 * based on the rules set.
 * Hides if ANY rule is true. (OR)
 * @param object $notification The notification object.
 */
function pmpro_should_hide_notification( $notification ) {		
	// default to NOT hiding
	$hide = false;
	
	if ( !empty( $notification->hide_if ) ) {		
		foreach( $notification->hide_if as $test => $data ) {
			$test_function = 'pmpro_notification_test_' . $test;
			if ( function_exists( $test_function ) ) {
				$hide = call_user_func( $test_function , $data );
				if ( $hide ) {					
					// one test passes, let's hide
					break;
				}
			}
		}
	}
	
	return $hide;
}

/**
 * Plugins active test.
 * @param array $plugins An array of plugin paths and filenames to check.
 * @returns bool true if ALL of the plugins are active (AND), false otherwise.
 */
function pmpro_notification_test_plugins_active( $plugins ) {
	if ( ! is_array( $plugins ) ) {
		$plugins = array( $plugins );
	}

	foreach( $plugins as $plugin ) {
		if ( ! pmpro_is_plugin_active( $plugin ) ) {			
			return false;
		}
	}
	
	return true;
}

/**
 * Plugin version test.
 * @param array $data Array from notification with plugin_file, comparison, and version to check.
 * @returns bool true if plugin is active and version comparison is true, false otherwise.
 */
function pmpro_notification_test_check_plugin_version( $data ) {
	if ( ! is_array( $data ) ) {
		return false;
	}
	
	if ( ! isset( $data[0] ) || !isset( $data[1] ) || !isset( $data[2] ) ) {
		return false;
	}
	
	return pmpro_check_plugin_version( $data[0], $data[1], $data[2] );
}

/**
 * PMPro license type test.
 * @param string $license PMPro license type to check for.
 * @returns bool true if the PMPro license type matches.
 */
function pmpro_notification_test_pmpro_license( $license_type ) {
	if ( empty( $license_type ) ) {
		// If no license type, check they DON'T have a valid license key
		$valid = ! pmpro_license_isValid();
	} else {
		// Check if they have a valid key of the type specified
		$valid = pmpro_license_isValid( NULL, $license_type );
	}
	
	return $valid;
}

/**
 * PMPro number of members test.
 * @param array $data Array from the notification with [0] comparison operator and [1] number of members.
 * @returns bool true if there are as many members as specified.
 */
function pmpro_notification_test_pmpro_num_members( $data ) {
	global $wpdb;
	static $num_members;
	
	if ( ! is_array( $data ) || !isset( $data[0] ) || !isset( $data[1] ) ) {
		return false;
	}
	
	if ( ! isset( $num_members ) ) {
		$sqlQuery = "SELECT COUNT(*) FROM ( SELECT user_id FROM $wpdb->pmpro_memberships_users WHERE status = 'active' GROUP BY user_id ) t1";
		$num_members = $wpdb->get_var( $sqlQuery );
	}

	return pmpro_int_compare( $num_members, $data[1], $data[0] );
}

/**
 * PMPro number of levels test.
 * @param array $data Array from the notification with [0] comparison operator and [1] number of levels.
 * @returns bool true if there are as many levels as specified.
 */
function pmpro_notification_test_pmpro_num_levels( $data ) {
	global $wpdb;
	static $num_levels;
	
	if ( ! is_array( $data ) || !isset( $data[0] ) || !isset( $data[1] ) ) {
		return false;
	}
	
	if ( ! isset( $num_levels ) ) {
		$sqlQuery = "SELECT COUNT(*) FROM $wpdb->pmpro_membership_levels";
		$num_levels = $wpdb->get_var( $sqlQuery );
	}

	return pmpro_int_compare( $num_levels, $data[1], $data[0] );
}

/**
 * PMPro number of discount codes test.
 * @param array $data Array from the notification with [0] comparison operator and [1] number of discount codes.
 * @returns bool true if there are as many discount codes as specified.
 */
function pmpro_notification_test_pmpro_num_discount_codes( $data ) {
	global $wpdb;
	static $num_codes;
	
	if ( ! is_array( $data ) || !isset( $data[0] ) || !isset( $data[1] ) ) {
		return false;
	}
	
	if ( ! isset( $num_codes ) ) {
		$sqlQuery = "SELECT COUNT(*) FROM $wpdb->pmpro_discount_codes";
		$num_codes = $wpdb->get_var( $sqlQuery );
	}

	return pmpro_int_compare( $num_codes, $data[1], $data[0] );
}

/**
 * PMPro number of orders test.
 * @param array $data Array from the notification with [0] comparison operator and [1] number of orders.
 * @returns bool true if there are as many orders as specified.
 */
function pmpro_notification_test_pmpro_num_orders( $data ) {
	global $wpdb;
	static $num_orders;
	
	if ( ! is_array( $data ) || !isset( $data[0] ) || !isset( $data[1] ) ) {
		return false;
	}
	
	if ( ! isset( $num_orders ) ) {
		$sqlQuery = "SELECT COUNT(*) FROM $wpdb->pmpro_membership_orders WHERE gateway_environment = 'live' AND status NOT IN('refunded', 'review', 'token', 'error')";
		$num_orders = $wpdb->get_var( $sqlQuery );
	}

	return pmpro_int_compare( $num_orders, $data[1], $data[0] );
}

/**
 * PMPro revenue test.
 * @param array $data Array from the notification with [0] comparison operator and [1] revenue.
 * Optionally $data can contain a third parameter to also check the currency code.
 * @returns bool true if there is as much revenue as specified.
 */
function pmpro_notification_test_pmpro_revenue( $data ) {
	global $wpdb;
	static $revenue;
	
	if ( ! is_array( $data ) || !isset( $data[0] ) || !isset( $data[1] ) ) {
		return false;
	}
	
	if ( ! isset( $revenue ) ) {
		$sqlQuery = "SELECT SUM(total) FROM $wpdb->pmpro_membership_orders WHERE gateway_environment = 'live' AND status NOT IN('refunded', 'review', 'token', 'error')";
		$revenue = $wpdb->get_var( $sqlQuery );
	}

	return pmpro_int_compare( $revenue, $data[1], $data[0] );
}

/**
 * PMPro setting test.
 * @param array $data Array from the notification with [0] setting name to check [1] value to check for.
 * @returns bool true if an option if found with the specified name and value.
 */
function pmpro_notification_test_pmpro_setting( $data ) {
	if ( ! is_array( $data ) || !isset( $data[0] ) || !isset( $data[1] ) ) {
		return false;
	}
	
	// remove the pmpro_ prefix if given
	if ( strpos( $data[0], 'pmpro_' ) === 0 ) {
		$data[0] = substr( $data[0], 6, strlen( $data[0] ) - 6 );
	}
		
	$option_value = pmpro_getOption( $data[0] );	
	if ( isset( $option_value ) && $option_value == $data[1] ) {
		return true;
	} else {
		return false;
	}
}

/**
 * PMPro site URL test.
 * @param string $string String or array of strings to look for in the site URL
 * @returns bool true if the string shows up in the site URL
 */
function pmpro_notification_test_site_url_match( $string ) {	
	if ( ! empty( $string ) ) {
		$strings_to_check = (array) $string;
		foreach( $strings_to_check as $check ) {
			if ( strpos( get_bloginfo( 'url' ), $check ) !== false ) {
				return true;
			}
		}		
	}
	return false;
}

/**
 * Get the max notification priority allowed on this site.
 * Priority is a value from 1 to 5, or 0.
 * 0: No notifications at all.
 * 1: Security notifications.
 * 2: Core PMPro updates.
 * 3: Updates to plugins already installed.
 * 4: Suggestions based on existing plugins and settings.
 * 5: Informative.
 */
function pmpro_get_max_notification_priority() {
	static $max_priority = null;

	if ( ! isset( $max_priority ) ) {
		$max_priority = pmpro_getOption( 'maxnotificationpriority' );
		
		// default to 5
		if ( empty( $max_priority ) ) {
			$max_priority = 5;
		}
		
		// filter allows for max priority 0 to turn them off entirely
		$max_priority = apply_filters( 'pmpro_max_notification_priority', $max_priority );
	}
	
	return $max_priority;
}

/**
 * Have we shown too many notifications recently.
 * By default we limit to 1 notification per 12 hour period
 * and 3 notifications per week.
 */
function pmpro_notifications_pause() {
	global $current_user;
	
	// No user? Pause.
	if ( empty( $current_user ) ) {
		return true;
	}
	
	$archived_notifications = get_user_meta( $current_user->ID, 'pmpro_archived_notifications', true );
	if ( ! is_array( $archived_notifications ) ) {
		return false;
	}			
	$archived_notifications = array_values( $archived_notifications );
	$num = count($archived_notifications);
	$now = current_time( 'timestamp' );
	
	// No archived (dismissed) notifications? Don't pause.
	if ( empty( $archived_notifications ) ) {
		return false;
	}
	
	// Last notification was dismissed < 12 hours ago. Pause.	
	$last_notification_date = $archived_notifications[$num - 1];	
	if ( strtotime( $last_notification_date, $now ) > ( $now - 3600*12 ) ) {		
		return true;
	}
	
	// If we have < 3 archived notifications. Don't pause.
	if ( $num < 3 ) {
		return false;
	}
	
	// If we've shown 3 this week already. Pause.
	$third_last_notification_date = $archived_notifications[$num - 3];
	if ( strtotime( $last_notification_date, $now ) > ( $now - 3600*24*7 ) ) {		
		return true;
	}
	
	// If we've gotten here, don't pause.
	return false;
}

/**
 * Move the top notice to the archives if dismissed.
 */
function pmpro_hide_notice() {
	global $current_user;
	$notification_id = sanitize_text_field( $_POST['notification_id'] );

	$archived_notifications = get_user_meta( $current_user->ID, 'pmpro_archived_notifications', true );

	if ( ! is_array( $archived_notifications ) ) {
		$archived_notifications = array();
	}

	$archived_notifications[$notification_id] = date_i18n( 'c' );
	
	update_user_meta( $current_user->ID, 'pmpro_archived_notifications', $archived_notifications );
	exit;
}
add_action( 'wp_ajax_pmpro_hide_notice', 'pmpro_hide_notice' );

/**
 * Show Powered by Paid Memberships Pro comment (only visible in source) in the footer.
 */
function pmpro_link() { ?>
Memberships powered by Paid Memberships Pro v<?php echo PMPRO_VERSION; ?>.
<?php }
function pmpro_footer_link() {
	if ( ! pmpro_getOption( 'hide_footer_link' ) ) { ?>
		<!-- <?php echo pmpro_link()?> -->
	<?php }
}
add_action( 'wp_footer', 'pmpro_footer_link' );