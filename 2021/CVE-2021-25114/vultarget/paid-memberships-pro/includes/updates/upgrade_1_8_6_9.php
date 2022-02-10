<?php
/*
	Upgrade to 1.8.6.9

	1. Find all orders for stripe gateway with a subscription_transaction_id LIKE 'cus_%'
	2. Search for an order for the same user_id and membership_id with a subscription_transaction_id LIKE 'sub_%'
	3. Replace subscription_transaction_id field.
*/
function pmpro_upgrade_1_8_6_9() {
	global $wpdb;
	$orders = $wpdb->get_results("SELECT id, user_id, membership_id, subscription_transaction_id FROM $wpdb->pmpro_membership_orders WHERE gateway = 'stripe' AND subscription_transaction_id LIKE 'cus_%'");
	
	if(!empty($orders)) {
		if(count($orders) > 100) {
			//if more than 100 orders, we'll need to do this via AJAX
			pmpro_addUpdate('pmpro_upgrade_1_8_6_9_ajax');
		} else {
			//less than 100, let's just do them now		
			$subids = array();
					
			foreach($orders as $order) {
				if(!empty($subids[$order->subscription_transaction_id])) {
					$wpdb->query("UPDATE $wpdb->pmpro_membership_orders SET subscription_transaction_id = '" . esc_sql($subids[$order->subscription_transaction_id]) . "' WHERE id = '" . $order->id . "' LIMIT 1");

					//echo "Updating subid for #" . $order->id . " " . $order->subscription_transaction_id . ".<br />";
				}
				elseif(isset($subids[$order->subscription_transaction_id])) {
					//no sub id found, so let it go

					//echo "No subid found for #" . $order->id . " " . $order->subscription_transaction_id . " in cache.<br />";
				}
				else {
					//need to look for a sub id in the database
					$subid = $wpdb->get_var("SELECT subscription_transaction_id FROM $wpdb->pmpro_membership_orders WHERE membership_id = '" . $order->membership_id . "' AND user_id = '" . $order->user_id . "' AND subscription_transaction_id LIKE 'sub_%' LIMIT 1");
					$subids[$order->subscription_transaction_id] = $subid;
					if(!empty($subid)) {
						$wpdb->query("UPDATE $wpdb->pmpro_membership_orders SET subscription_transaction_id = '" . esc_sql($subid) . "' WHERE id = '" . $order->id . "' LIMIT 1");

						//echo "Updating subid for #" . $order->id . " " . $order->subscription_transaction_id . ".<br />";	
					}
					else {
						//echo "No subid found for #" . $order->id . " " . $order->subscription_transaction_id . ".<br />";
					}
				}
			}
		}
	}

	pmpro_setOption("db_version", "1.869");
	return 1.869;
}

/*
	If a site has > 100 orders then we run this pasrt of the update via AJAX from the updates page.
*/
function pmpro_upgrade_1_8_6_9_ajax() {
	global $wpdb;

	//keeping track of which order we're working on
	$last_order_id = get_option('pmpro_upgrade_1_8_6_9_last_order_id', 0);
	
	//get orders
	$orders = $wpdb->get_results("SELECT id, user_id, membership_id, subscription_transaction_id FROM $wpdb->pmpro_membership_orders WHERE id > $last_order_id AND gateway = 'stripe' AND subscription_transaction_id LIKE 'cus_%' ORDER BY id LIMIT 100");

	if(empty($orders)) {
		//done with this update
		pmpro_removeUpdate('pmpro_upgrade_1_8_6_9_ajax');
		delete_option('pmpro_upgrade_1_8_6_9_last_order_id');
	} else {
		$subids = array();					//cache of subids found
		foreach($orders as $order) {
			$last_order_id = $order->id;	//keeping track of the last order we processed
			if(!empty($subids[$order->subscription_transaction_id])) {
				$wpdb->query("UPDATE $wpdb->pmpro_membership_orders SET subscription_transaction_id = '" . esc_sql($subids[$order->subscription_transaction_id]) . "' WHERE id = '" . $order->id . "' LIMIT 1");
			}
			elseif(isset($subids[$order->subscription_transaction_id])) {
				//no sub id found, so let it go
			}
			else {
				//need to look for a sub id in the database
				$subid = $wpdb->get_var("SELECT subscription_transaction_id FROM $wpdb->pmpro_membership_orders WHERE membership_id = '" . $order->membership_id . "' AND user_id = '" . $order->user_id . "' AND subscription_transaction_id LIKE 'sub_%' LIMIT 1");
				$subids[$order->subscription_transaction_id] = $subid;
				if(!empty($subid)) {
					$wpdb->query("UPDATE $wpdb->pmpro_membership_orders SET subscription_transaction_id = '" . esc_sql($subid) . "' WHERE id = '" . $order->id . "' LIMIT 1");
				}
				else {
					//no sub id found, so let it go
				}
			}
		}

		update_option('pmpro_upgrade_1_8_6_9_last_order_id', $last_order_id);
	}
}
