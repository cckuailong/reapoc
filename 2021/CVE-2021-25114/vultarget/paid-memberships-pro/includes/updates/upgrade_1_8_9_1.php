<?php
use Stripe\Customer as Stripe_Customer;
/*
	Upgrade to 1.8.9.1
	* Fixing Stripe orders where user_id/membership_id = 0
*/
function pmpro_upgrade_1_8_9_1() {
	global $wpdb;	
	
	//Fixing Stripe orders where user_id/membership_id = 0. (Sets up update via AJAX)
	$orders = $wpdb->get_col("SELECT id FROM $wpdb->pmpro_membership_orders WHERE gateway = 'stripe' AND user_id = 0 AND membership_id = 0 AND status <> 'error' ");			
	if(!empty($orders))
		pmpro_addUpdate('pmpro_upgrade_1_8_9_1_ajax');

	pmpro_setOption("db_version", "1.891");
	return 1.891;
}

/*
	We run this part of the update via AJAX from the updates page.
*/
function pmpro_upgrade_1_8_9_1_ajax() {
	global $wpdb;

	$debug = false;
	$run = true;

	//keeping track of which order we're working on
	$last_order_id = get_option('pmpro_upgrade_1_8_9_1_last_order_id', 0);
	
	//Fixing old $0 Stripe orders.	
	$orders = $wpdb->get_col("SELECT id FROM $wpdb->pmpro_membership_orders WHERE id > $last_order_id AND gateway = 'stripe' AND user_id = 0 AND membership_id = 0 AND status <> 'error' ORDER BY id LIMIT 2");
	
	//track progress
	$first_load = get_transient('pmpro_updates_first_load');
	if($first_load) {
		$total_orders = $wpdb->get_var("SELECT COUNT(id) FROM $wpdb->pmpro_membership_orders WHERE id > $last_order_id AND gateway = 'stripe' AND user_id = 0 AND membership_id = 0 AND status <> 'error' ");
		update_option('pmpro_upgrade_1_8_9_1_total', $total_orders, 'no');
		$progress = 0;
	} else {
		$total_orders = get_option('pmpro_upgrade_1_8_9_1_total', 0);
		$progress = get_option('pmpro_upgrade_1_8_9_1_progress', 0);
	}
	update_option('pmpro_upgrade_1_8_9_1_progress', $progress + count($orders), 'no');
	global $pmpro_updates_progress;
	if($total_orders > 0)
		$pmpro_updates_progress = "[" . $progress . "/" . $total_orders . "]";
	else
		$pmpro_updates_progress = "";
	
	if(empty($orders)) {
		//done with this update			
		pmpro_removeUpdate('pmpro_upgrade_1_8_9_1_ajax');
		delete_option('pmpro_upgrade_1_8_9_1_last_order_id');
		delete_option('pmpro_upgrade_1_8_9_1_total');
		delete_option('pmpro_upgrade_1_8_9_1_progress');
	} else {
		//need to keep working
		foreach($orders as $order_id) {				
			$last_order_id = $order_id;	//keeping track of the last order we processed
			
			//get order
			$order = new MemberOrder($order_id);
			
			//if we have a user_id, this has the same sub id as an earlier order and was already fixed
			if(!empty($order->user_id))
				continue;
			
			if($debug)
				echo "Order #" . $order->id . ", " . $order->code . " (" . $order->subscription_transaction_id . ")\n";
			
			//find the subscription (via remote_get since this isn't the version of the library we use)
			$subscription = json_decode(wp_remote_retrieve_body(wp_remote_get('https://api.stripe.com/v1/subscriptions/' . $order->subscription_transaction_id, array(
					'timeout' => 60,
					'sslverify' => FALSE,
					'httpversion' => '1.1',
					'headers'=>array('Authorization' => 'Bearer ' . pmpro_getOption("stripe_secretkey")),
			    ))));

			//no sub?
			if(empty($subscription) || empty($subscription->customer)) {
				if($debug)
					echo "- Can't find the subscription.\n";
				if($run)
					$wpdb->query("UPDATE $wpdb->pmpro_membership_orders SET `status` = 'error', notes = CONCAT(notes, '\nRecurring order we couldn\'t find the subscription.') WHERE id = $order->id LIMIT 1");
				
				continue;
			}
												
			//get customer
			$customer = Stripe_Customer::retrieve($subscription->customer);
			
			//no customer? mark order as error and bail
			if(empty($customer)) {
				if($debug)
					echo "- Can't find the customer.\n";
				if($run)
					$wpdb->query("UPDATE $wpdb->pmpro_membership_orders SET `status` = 'error', notes = CONCAT(notes, '\nRecurring order we couldn\'t find the original customer for.') WHERE id = $order->id LIMIT 1");

				continue;
			}
						
			//get past payments
			$invoices = $customer->invoices(array("limit"=>100));

			//find invoices for the same sub and see if we have a good order for it
			if(!empty($invoices)) {				
				foreach($invoices->data as $invoice) {
					//echo "- " . $invoice->subscription . ", " . $invoice->charge . ", " . $invoice->id . "<br />";
					if($invoice->subscription == $order->subscription_transaction_id) {
						//same sub. look for an order for this invoice or charge
						$old_order = $wpdb->get_row("SELECT id, user_id, membership_id, subscription_transaction_id
														 FROM $wpdb->pmpro_membership_orders 
														 WHERE gateway = 'stripe' AND
														     (payment_transaction_id = '" . $invoice->charge . "' OR payment_transaction_id = '" . $invoice->id . "') AND
															 user_id <> 0 AND
															 membership_id <> 0
													     LIMIT 1
														 ");													
						if(!empty($old_order)) {
							//found it, let's fix data
							if($debug)
								echo "- Order #" . $old_order->id . ", " . $old_order->code . " found! FIXED\n";
							
							if($run) {
								$sqlQuery = "UPDATE $wpdb->pmpro_membership_orders SET user_id = " . $old_order->user_id . ", membership_id = " . $old_order->membership_id . " WHERE user_id = 0 AND membership_id = 0 AND subscription_transaction_id = '" . $order->subscription_transaction_id . "' ";							
								$wpdb->query($sqlQuery);
							}

							continue 2;
						}
					}
				}
			}
			
			//didn't find an invoice for this sub
			if($debug)
				echo "- No invoice for this sub.\n";
			if($run)
				$wpdb->query("UPDATE $wpdb->pmpro_membership_orders SET `status` = 'error', notes = CONCAT(notes, '\nRecurring order we couldn\'t find the original customer for.') WHERE id = $order->id LIMIT 1");

			continue;
		}
		
		update_option('pmpro_upgrade_1_8_9_1_last_order_id', $last_order_id, 'no');
	}	
}
