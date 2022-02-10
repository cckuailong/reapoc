<?php
	//only admins can get this
	if(!function_exists("current_user_can") || (!current_user_can("manage_options") && !current_user_can("pmpro_discountcodes")))
	{
		die(__("You do not have permissions to perform this action.", 'paid-memberships-pro' ));
	}

	//vars
	global $wpdb, $pmpro_currency_symbol, $pmpro_stripe_error, $pmpro_braintree_error, $pmpro_payflow_error, $pmpro_twocheckout_error, $pmpro_pages, $gateway;

	$now = current_time( 'timestamp' );

	if(isset($_REQUEST['edit']))
		$edit = intval($_REQUEST['edit']);
	else
		$edit = false;

	if(isset($_REQUEST['copy']))
		$copy = intval($_REQUEST['copy']);
	else
		$copy = false;

	if(isset($_REQUEST['delete']))
		$delete = intval($_REQUEST['delete']);
	else
		$delete = false;

	if(isset($_REQUEST['saveid']))
		$saveid = intval($_POST['saveid']);
	else
		$saveid = false;

	if(isset($_REQUEST['s']))
		$s = sanitize_text_field($_REQUEST['s']);
	else
		$s = "";

	//some vars for the search
	if ( isset( $_REQUEST['pn'] ) ) {
		$pn = intval( $_REQUEST['pn'] );
	} else {
		$pn = 1;
	}

	if ( isset( $_REQUEST['limit'] ) ) {
		$limit = intval( $_REQUEST['limit'] );
	} else {
		/**
		 * Filter to set the default number of items to show per page
		 * on the Discount Codes page in the admin.
		 *
		 * @since 1.9.4
		 *
		 * @param int $limit The number of items to show per page.
		 */
		$limit = apply_filters( 'pmpro_discount_codes_per_page', 15 );
	}

	$end   = $pn * $limit;
	$start = $end - $limit;

	//check nonce for saving codes
	if (!empty($saveid) && (empty($_REQUEST['pmpro_discountcodes_nonce']) || !check_admin_referer('save', 'pmpro_discountcodes_nonce'))) {
		$pmpro_msgt = 'error';
		$pmpro_msg = __("Are you sure you want to do that? Try again.", 'paid-memberships-pro' );
		$saveid = false;
	}

	if($saveid)
	{
		//get vars
		//disallow/strip all non-alphanumeric characters except -
		$code = preg_replace("/[^A-Za-z0-9\-]/", "", sanitize_text_field($_POST['code']));
		$starts_month = intval($_POST['starts_month']);
		$starts_day = intval($_POST['starts_day']);
		$starts_year = intval($_POST['starts_year']);
		$expires_month = intval($_POST['expires_month']);
		$expires_day = intval($_POST['expires_day']);
		$expires_year = intval($_POST['expires_year']);
		$uses = intval($_POST['uses']);

		//fix up dates
		$starts = date("Y-m-d", strtotime($starts_month . "/" . $starts_day . "/" . $starts_year, $now ));
		$expires = date("Y-m-d", strtotime($expires_month . "/" . $expires_day . "/" . $expires_year, $now ));

		//insert/update/replace discount code
		pmpro_insert_or_replace(
			$wpdb->pmpro_discount_codes,
			array(
				'id'=>max($saveid, 0),
				'code' => $code,
				'starts' => $starts,
				'expires' => $expires,
				'uses' => $uses
			),
			array(
				'%d',
				'%s',
				'%s',
				'%s',
				'%d'
			)
		);

		//check for errors and show appropriate message if inserted or updated
		if(empty($wpdb->last_error)) {
			if($saveid < 1) {
				//insert
				$pmpro_msg = __("Discount code added successfully.", 'paid-memberships-pro' );
				$pmpro_msgt = "success";
				$saved = true;
				$edit = $wpdb->insert_id;
			} else {
				//updated
				$pmpro_msg = __("Discount code updated successfully.", 'paid-memberships-pro' );
				$pmpro_msgt = "success";
				$saved = true;
				$edit = $saveid;
			}
		} else {
			if($saveid < 1) {
				//error inserting
				$pmpro_msg = __("Error adding discount code. That code may already be in use.", 'paid-memberships-pro' ) . $wpdb->last_error;
				$pmpro_msgt = "error";
			} else {
				//error updating
				$pmpro_msg = __("Error updating discount code. That code may already be in use.", 'paid-memberships-pro' );
				$pmpro_msgt = "error";
			}
		}

		//now add the membership level rows
		if($saved && $edit > 0)
		{
			//get the submitted values
			$all_levels_a = $_REQUEST['all_levels'];
			if(!empty($_REQUEST['levels']))
				$levels_a = $_REQUEST['levels'];
			else
				$levels_a = array();
			$initial_payment_a = $_REQUEST['initial_payment'];

			if(!empty($_REQUEST['recurring']))
				$recurring_a = $_REQUEST['recurring'];
			$billing_amount_a = $_REQUEST['billing_amount'];
			$cycle_number_a = $_REQUEST['cycle_number'];
			$cycle_period_a = $_REQUEST['cycle_period'];
			$billing_limit_a = $_REQUEST['billing_limit'];

			if(!empty($_REQUEST['custom_trial']))
				$custom_trial_a = $_REQUEST['custom_trial'];
			$trial_amount_a = $_REQUEST['trial_amount'];
			$trial_limit_a = $_REQUEST['trial_limit'];

			if(!empty($_REQUEST['expiration']))
				$expiration_a = $_REQUEST['expiration'];
			$expiration_number_a = $_REQUEST['expiration_number'];
			$expiration_period_a = $_REQUEST['expiration_period'];

			//clear the old rows
			$wpdb->delete($wpdb->pmpro_discount_codes_levels, array('code_id' => $edit), array('%d'));

			//add a row for each checked level
			if(!empty($levels_a))
			{
				foreach($levels_a as $level_id)
				{
					$level_id = intval($level_id);	//sanitized

					//get the values ready
					$n = array_search($level_id, $all_levels_a); 	//this is the key location of this level's values
					$initial_payment = sanitize_text_field($initial_payment_a[$n]);

					//is this recurring?
					if(!empty($recurring_a))
					{
						if(in_array($level_id, $recurring_a))
							$recurring = 1;
						else
							$recurring = 0;
					}
					else
						$recurring = 0;

					if(!empty($recurring))
					{
						$billing_amount = sanitize_text_field($billing_amount_a[$n]);
						$cycle_number = intval($cycle_number_a[$n]);
						$cycle_period = sanitize_text_field($cycle_period_a[$n]);
						$billing_limit = intval($billing_limit_a[$n]);

						//custom trial
						if(!empty($custom_trial_a))
						{
							if(in_array($level_id, $custom_trial_a))
								$custom_trial = 1;
							else
								$custom_trial = 0;
						}
						else
							$custom_trial = 0;

						if(!empty($custom_trial))
						{
							$trial_amount = sanitize_text_field($trial_amount_a[$n]);
							$trial_limit = intval($trial_limit_a[$n]);
						}
						else
						{
							$trial_amount = '';
							$trial_limit = '';
						}
					}
					else
					{
						$billing_amount = '';
						$cycle_number = '';
						$cycle_period = 'Month';
						$billing_limit = '';
						$custom_trial = 0;
						$trial_amount = '';
						$trial_limit = '';
					}

					if(!empty($expiration_a))
					{
						if(in_array($level_id, $expiration_a))
							$expiration = 1;
						else
							$expiration = 0;
					}
					else
						$expiration = 0;

					if(!empty($expiration))
					{
						$expiration_number = intval($expiration_number_a[$n]);
						$expiration_period = sanitize_text_field($expiration_period_a[$n]);
					}
					else
					{
						$expiration_number = '';
						$expiration_period = 'Month';
					}

					if ( ! empty( $expiration ) && ! empty( $recurring ) ) {
						$expiration_warning_flag = true;
					}

					//okay, do the insert
					$wpdb->insert(
						$wpdb->pmpro_discount_codes_levels,
						array(
							'code_id' => $edit,
							'level_id' => $level_id,
							'initial_payment' => $initial_payment,
							'billing_amount' => $billing_amount,
							'cycle_number' => $cycle_number,
							'cycle_period' => $cycle_period,
							'billing_limit' => $billing_limit,
							'trial_amount' => $trial_amount,
							'trial_limit' => $trial_limit,
							'expiration_number' => $expiration_number,
							'expiration_period' => $expiration_period
						),
						array(
							'%d',
							'%d',
							'%f',
							'%f',
							'%d',
							'%s',
							'%d',
							'%f',
							'%d',
							'%d',
							'%s'
						)
					);

					if(empty($wpdb->last_error))
					{
						//okay
						do_action("pmpro_save_discount_code_level", $edit, $level_id);
					}
					else
					{
						$level = pmpro_getLevel($level_id);
						$level_errors[] = sprintf(__("Error saving values for the %s level.", 'paid-memberships-pro' ), $level->name);
					}
				}
			}

			//errors?
			if(!empty($level_errors))
			{
				$pmpro_msg = __("There were errors updating the level values: ", 'paid-memberships-pro' ) . implode(" ", $level_errors);
				$pmpro_msgt = "error";
			}
			else
			{
				do_action("pmpro_save_discount_code", $edit);

				//all good. set edit = false so we go back to the overview page
				$edit = false;
			}
		}
	}

	//check nonce for deleting codes
	if (!empty($delete) && (empty($_REQUEST['pmpro_discountcodes_nonce']) || !check_admin_referer('delete', 'pmpro_discountcodes_nonce'))) {
		$pmpro_msgt = 'error';
		$pmpro_msg = __("Are you sure you want to do that? Try again.", 'paid-memberships-pro' );
		$delete = false;
	}

	//are we deleting?
	if(!empty($delete))
	{
		//is this a code?
		$code = $wpdb->get_var( $wpdb->prepare( "SELECT code FROM $wpdb->pmpro_discount_codes WHERE id = %d LIMIT 1", $delete ) );
		if(!empty($code))
		{
			//action
			do_action("pmpro_delete_discount_code", $delete);

			//delete the code levels
			$r1 = $wpdb->delete($wpdb->pmpro_discount_codes_levels, array('code_id'=>$delete), array('%d'));

			if($r1 !== false)
			{
				//delete the code
				$r2 = $wpdb->delete($wpdb->pmpro_discount_codes, array('id'=>$delete), array('%d'));

				if($r2 !== false)
				{
					$pmpro_msg = sprintf(__("Code %s deleted successfully.", 'paid-memberships-pro' ), $code);
					$pmpro_msgt = "success";
				}
				else
				{
					$pmpro_msg = __("Error deleting discount code. The code was only partially deleted. Please try again.", 'paid-memberships-pro' );
					$pmpro_msgt = "error";
				}
			}
			else
			{
				$pmpro_msg = __("Error deleting code. Please try again.", 'paid-memberships-pro' );
				$pmpro_msgt = "error";
			}
		}
		else
		{
			$pmpro_msg = __("Code not found.", 'paid-memberships-pro' );
			$pmpro_msgt = "error";
		}
	}

	if( ! empty( $pmpro_msg ) && ! empty( $expiration_warning_flag ) ) {
		$pmpro_msg .= ' <strong>' . sprintf( __( 'WARNING: A level was set with both a recurring billing amount and an expiration date. You only need to set one of these unless you really want this membership to expire after a specific time period. For more information, <a target="_blank" href="%s">see our post here</a>.', 'paid-memberships-pro' ), 'https://www.paidmembershipspro.com/important-notes-on-recurring-billing-and-expiration-dates-for-membership-levels/?utm_source=plugin&utm_medium=pmpro-discountcodes&utm_campaign=blog&utm_content=important-notes-on-recurring-billing-and-expiration-dates-for-membership-levels' ) . '</strong>';

		if( $pmpro_msgt == 'success' ) {
			$pmpro_msgt = 'warning';
		}
	}

	require_once(dirname(__FILE__) . "/admin_header.php");
?>

	<?php if($edit) { ?>

		<h1>
			<?php
				if($edit > 0)
					echo __("Edit Discount Code", 'paid-memberships-pro' );
				else
					echo __("Add New Discount Code", 'paid-memberships-pro' );
			?>
		</h1>

		<?php if(!empty($pmpro_msg)) { ?>
			<div id="message" class="<?php if($pmpro_msgt == "success") echo "updated fade"; else echo "error"; ?>"><p><?php echo $pmpro_msg?></p></div>
		<?php } ?>

		<div>
			<?php
				// get the code...
				if($edit > 0)
				{
					$code = $wpdb->get_row(
						$wpdb->prepare("
						SELECT *, UNIX_TIMESTAMP(CONVERT_TZ(starts, '+00:00', @@global.time_zone)) as starts, UNIX_TIMESTAMP(CONVERT_TZ(expires, '+00:00', @@global.time_zone)) as expires
						FROM $wpdb->pmpro_discount_codes
						WHERE id = %d LIMIT 1",
						$edit ),
						OBJECT
					);

					$uses = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->pmpro_discount_codes_uses WHERE code_id = %d", $code->id ) );
					$levels = $wpdb->get_results( $wpdb->prepare("
					SELECT l.id, l.name, cl.initial_payment, cl.billing_amount, cl.cycle_number, cl.cycle_period, cl.billing_limit, cl.trial_amount, cl.trial_limit
					FROM $wpdb->pmpro_membership_levels l
					LEFT JOIN $wpdb->pmpro_discount_codes_levels cl
					ON l.id = cl.level_id
					WHERE cl.code_id = %s",
					$code->code
					) );
					$temp_code = $code;
				}
				elseif(!empty($copy) && $copy > 0)
				{
					$code = $wpdb->get_row(
						$wpdb->prepare("
						SELECT *, UNIX_TIMESTAMP(CONVERT_TZ(starts, '+00:00', @@global.time_zone)) as starts, UNIX_TIMESTAMP(CONVERT_TZ(expires, '+00:00', @@global.time_zone)) as expires
						FROM $wpdb->pmpro_discount_codes
						WHERE id = %d LIMIT 1",
						$copy ),
						OBJECT
					);

					$temp_code = $code;
				}

				// didn't find a discount code, let's add a new one...
				if(empty($code->id)) $edit = -1;

				//defaults for new codes
				if ( $edit == -1 )
				{
					$code = new stdClass();
					$code->code = pmpro_getDiscountCode();

					if( ! empty( $copy ) && $copy > 0 ) {
						$code->starts = $temp_code->starts;
						$code->expires = $temp_code->expires;
						$code->uses = $temp_code->uses;
					}
				}
			?>
			<form action="" method="post">
				<input name="saveid" type="hidden" value="<?php echo esc_attr( $edit ); ?>" />
				<?php wp_nonce_field('save', 'pmpro_discountcodes_nonce');?>
				<table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row" valign="top"><label><?php _e('ID', 'paid-memberships-pro' );?>:</label></th>
                        <td><p class="description"><?php if(!empty($code->id)) echo esc_html( $code->id ); else echo __("This will be generated when you save.", 'paid-memberships-pro' );?></p></td>
                    </tr>

                    <tr>
                        <th scope="row" valign="top"><label for="code"><?php _e('Code', 'paid-memberships-pro' );?>:</label></th>
                        <td><input name="code" type="text" size="20" value="<?php echo esc_attr( $code->code ); ?>" /></td>
                    </tr>

					<?php
						//some vars for the dates
						$current_day = date("j");
						if(!empty($code->starts))
							$selected_starts_day = date("j", $code->starts);
						else
							$selected_starts_day = $current_day;
						if(!empty($code->expires))
							$selected_expires_day = date("j", $code->expires);
						else
							$selected_expires_day = $current_day;

						$current_month = date("M");
						if(!empty($code->starts))
							$selected_starts_month = date("m", $code->starts);
						else
							$selected_starts_month = date("m");
						if(!empty($code->expires))
							$selected_expires_month = date("m", $code->expires);
						else
							$selected_expires_month = date("m");

						$current_year = date("Y");
						if(!empty($code->starts))
							$selected_starts_year = date("Y", $code->starts);
						else
							$selected_starts_year = $current_year;
						if(!empty($code->expires))
							$selected_expires_year = date("Y", $code->expires);
						else
							$selected_expires_year = (int)$current_year + 1;
					?>

					<tr>
                        <th scope="row" valign="top"><label for="starts"><?php _e('Start Date', 'paid-memberships-pro' );?>:</label></th>
                        <td>
							<select name="starts_month">
								<?php
									for($i = 1; $i < 13; $i++)
									{
									?>
									<option value="<?php echo esc_attr( $i )?>" <?php if($i == $selected_starts_month) { ?>selected="selected"<?php } ?>><?php echo esc_html( date_i18n( 'F', mktime( 0, 0, 0, $i, 2 ) ) ); ?></option>
									<?php
									}
								?>
							</select>
							<input name="starts_day" type="text" size="2" value="<?php echo esc_attr( $selected_starts_day ); ?>" />
							<input name="starts_year" type="text" size="4" value="<?php echo esc_attr( $selected_starts_year ); ?>" />
						</td>
                    </tr>

					<tr>
                        <th scope="row" valign="top"><label for="expires"><?php _e('Expiration Date', 'paid-memberships-pro' );?>:</label></th>
                        <td>
							<select name="expires_month">
								<?php
									for($i = 1; $i < 13; $i++)
									{
									?>
									<option value="<?php echo esc_attr( $i );?>" <?php if($i == $selected_expires_month) { ?>selected="selected"<?php } ?>><?php echo esc_html( date_i18n( 'F', mktime( 0, 0, 0, $i, 2 ) ) ); ?></option>
									<?php
									}
								?>
							</select>
							<input name="expires_day" type="text" size="2" value="<?php echo esc_attr( $selected_expires_day ); ?>" />
							<input name="expires_year" type="text" size="4" value="<?php echo esc_attr( $selected_expires_year ); ?>" />
						</td>
                    </tr>

					<tr>
                        <th scope="row" valign="top"><label for="uses"><?php _e('Uses', 'paid-memberships-pro' );?>:</label></th>
                        <td>
							<input name="uses" type="text" size="10" value="<?php if ( ! empty( $code->uses ) ) echo esc_attr( $code->uses ); ?>" />
							<p class="description"><?php _e('Leave blank for unlimited uses.', 'paid-memberships-pro' );?></p>
						</td>
                    </tr>

				</tbody>
			</table>

			<?php do_action("pmpro_discount_code_after_settings", $edit); ?>

			<h3><?php _e('Which Levels Will This Code Apply To?', 'paid-memberships-pro' ); ?></h3>

			<div class="pmpro_discount_levels">
			<?php
				$levels = $wpdb->get_results("SELECT * FROM $wpdb->pmpro_membership_levels");
				$levels = pmpro_sort_levels_by_order( $levels );
				foreach($levels as $level)
				{
					//if this level is already managed for this discount code, use the code values
					if($edit > 0 || ! empty( $copy ) )
					{
						$code_level = $wpdb->get_row( $wpdb->prepare("
						SELECT l.id, cl.*, l.name, l.description, l.allow_signups
						FROM $wpdb->pmpro_discount_codes_levels cl
						LEFT JOIN $wpdb->pmpro_membership_levels l
						ON cl.level_id = l.id
						WHERE cl.code_id = %d AND cl.level_id = %d LIMIT 1",
						$temp_code->id,
						$level->id )
					);
						if($code_level)
						{
							$level = $code_level;
							$level->checked = true;
						}
						else
							$level_checked = false;
					}
					else
						$level_checked = false;
				?>
				<div class="pmpro_discount_level <?php if ( ! pmpro_check_discount_code_level_for_gateway_compatibility( $level ) ) { ?>pmpro_error<?php } ?>">
					<input type="hidden" name="all_levels[]" value="<?php echo esc_attr( $level->id ); ?>" />
					<input type="checkbox" id="levels_<?php echo esc_attr( $level->id ); ?>" name="levels[]" value="<?php echo esc_attr( $level->id ); ?>" <?php if(!empty($level->checked)) { ?>checked="checked"<?php } ?> onclick="if(jQuery(this).is(':checked')) jQuery(this).next().next().show();	else jQuery(this).next().next().hide();" />
					<label for="levels_<?php echo esc_attr( $level->id ); ?>"><?php echo $level->name?></label>
					<div class="pmpro_discount_levels_pricing level_<?php echo esc_attr( $level->id ); ?>" <?php if(empty($level->checked)) { ?>style="display: none;"<?php } ?>>
						<table class="form-table">
						<tbody>
							<tr>
								<th scope="row" valign="top"><label for="initial_payment"><?php _e('Initial Payment', 'paid-memberships-pro' );?>:</label></th>
								<td>
									<?php
									if(pmpro_getCurrencyPosition() == "left")
										echo $pmpro_currency_symbol;
									?>
									<input name="initial_payment[]" type="text" size="20" value="<?php echo esc_attr( pmpro_filter_price_for_text_field( $level->initial_payment ) ); ?>" />
									<?php
									if(pmpro_getCurrencyPosition() == "right")
										echo $pmpro_currency_symbol;
									?>
									<p class="description"><?php _e('The initial amount collected at registration.', 'paid-memberships-pro' );?></p>
								</td>
							</tr>

							<tr>
								<th scope="row" valign="top"><label><?php _e('Recurring Subscription', 'paid-memberships-pro' );?>:</label></th>
								<td><input class="recurring_checkbox" id="recurring_<?php echo esc_attr( $level->id );?>" name="recurring[]" type="checkbox" value="<?php echo esc_attr( $level->id ); ?>" <?php if(pmpro_isLevelRecurring($level)) { echo "checked='checked'"; } ?> onclick="if(jQuery(this).prop('checked')) {					jQuery(this).parent().parent().siblings('.recurring_info').show(); if(!jQuery('#custom_trial_<?php echo esc_attr( $level->id ); ?>').is(':checked')) jQuery(this).parent().parent().siblings('.trial_info').hide();} else					jQuery(this).parent().parent().siblings('.recurring_info').hide();" /> <label for="recurring_<?php echo esc_attr( $level->id ); ?>"><?php _e('Check if this level has a recurring subscription payment.', 'paid-memberships-pro' );?></label></td>
							</tr>

							<tr class="recurring_info" <?php if(!pmpro_isLevelRecurring($level)) {?>style="display: none;"<?php } ?>>
								<th scope="row" valign="top"><label for="billing_amount"><?php _e('Billing Amount', 'paid-memberships-pro' );?>:</label></th>
								<td>
									<?php
									if(pmpro_getCurrencyPosition() == "left")
										echo $pmpro_currency_symbol;
									?>
									<input name="billing_amount[]" type="text" size="20" value="<?php echo esc_attr( pmpro_filter_price_for_text_field( $level->billing_amount ) );?>" />
									<?php
									if(pmpro_getCurrencyPosition() == "right")
										echo $pmpro_currency_symbol;
									?>
									<?php _e('per', 'paid-memberships-pro' ); ?>
									<input name="cycle_number[]" type="text" size="10" value="<?php echo esc_attr( $level->cycle_number ); ?>" />
									<select name="cycle_period[]">
									  <?php
										$cycles = array( __('Day(s)', 'paid-memberships-pro' ) => 'Day', __('Week(s)', 'paid-memberships-pro' ) => 'Week', __('Month(s)', 'paid-memberships-pro' ) => 'Month', __('Year(s)', 'paid-memberships-pro' ) => 'Year' );
										foreach ( $cycles as $name => $value ) {
										  echo "<option value='$value'";
										  if ( $level->cycle_period == $value ) echo " selected='selected'";
										  echo ">$name</option>";
										}
									  ?>
									</select>
									<p class="description"><?php _e('The amount to be billed one cycle after the initial payment.', 'paid-memberships-pro' );?></p>
									<?php if($gateway == "braintree") { ?>
										<strong <?php if(!empty($pmpro_braintree_error)) { ?>class="pmpro_red"<?php } ?>><?php _e('Braintree integration currently only supports billing periods of "Month" or "Year".', 'paid-memberships-pro' );?></strong>
									<?php } elseif($gateway == "stripe") { ?>
										<p class="description"><strong <?php if(!empty($pmpro_stripe_error)) { ?>class="pmpro_red"<?php } ?>><?php _e('Stripe integration does not allow billing periods longer than 1 year.', 'paid-memberships-pro' );?></strong></p>
									<?php }?>
								</td>
							</tr>

							<tr class="recurring_info" <?php if(!pmpro_isLevelRecurring($level)) {?>style="display: none;"<?php } ?>>
								<th scope="row" valign="top"><label for="billing_limit"><?php _e('Billing Cycle Limit', 'paid-memberships-pro' );?>:</label></th>
								<td>
									<input name="billing_limit[]" type="text" size="20" value="<?php echo esc_attr( $level->billing_limit ); ?>" />
									<p class="description">
										<?php _e('The <strong>total</strong> number of recurring billing cycles for this level, including the trial period (if applicable) but not including the initial payment. Set to zero if membership is indefinite.', 'paid-memberships-pro' );?>
										<?php if ( ( $gateway == "stripe" ) && ! function_exists( 'pmprosbl_plugin_row_meta' ) ) { ?>
											<br /><strong <?php if(!empty($pmpro_stripe_error)) { ?>class="pmpro_red"<?php } ?>><?php _e('Stripe integration currently does not support billing limits. You can still set an expiration date below.', 'paid-memberships-pro' );?></strong>
											<?php if ( ! function_exists( 'pmprosd_pmpro_membership_level_after_other_settings' ) ) {
													$allowed_sbl_html = array (
														'a' => array (
															'href' => array(),
															'target' => array(),
															'title' => array(),
														),
													);
													echo '<br />' . sprintf( wp_kses( __( 'Optional: Allow billing limits with Stripe using the <a href="%s" title="Paid Memberships Pro - Stripe Billing Limits Add On" target="_blank">Stripe Billing Limits Add On</a>.', 'paid-memberships-pro' ), $allowed_sbl_html ), 'https://www.paidmembershipspro.com/add-ons/pmpro-stripe-billing-limits/?utm_source=plugin&utm_medium=pmpro-membershiplevels&utm_campaign=add-ons&utm_content=stripe-billing-limits' ) . '</em></td></tr>';
											} ?>
									<?php } ?>
								</p>
								</td>
							</tr>

							<tr class="recurring_info" <?php if (!pmpro_isLevelRecurring($level)) echo "style='display:none;'";?>>
								<th scope="row" valign="top"><label><?php _e('Custom Trial', 'paid-memberships-pro' );?>:</label></th>
								<td>
									<input id="custom_trial_<?php echo esc_attr( $level->id ); ?>" id="custom_trial_<?php echo esc_attr( $level->id ); ?>" name="custom_trial[]" type="checkbox" value="<?php echo esc_attr( $level->id ); ?>" <?php if ( pmpro_isLevelTrial($level) ) { echo "checked='checked'"; } ?> onclick="if(jQuery(this).prop('checked')) jQuery(this).parent().parent().siblings('.trial_info').show();	else jQuery(this).parent().parent().siblings('.trial_info').hide();" /> <label for="custom_trial_<?php echo esc_attr( $level->id );?>"><?php _e('Check to add a custom trial period.', 'paid-memberships-pro' );?></label>
									<?php if($gateway == "twocheckout") { ?>
										<p class="description"><strong <?php if(!empty($pmpro_twocheckout_error)) { ?>class="pmpro_red"<?php } ?>><?php _e('2Checkout integration does not support custom trials. You can do one period trials by setting an initial payment different from the billing amount.', 'paid-memberships-pro' );?></strong></p>
									<?php } ?>
								</td>
							</tr>

							<tr class="trial_info recurring_info" <?php if (!pmpro_isLevelTrial($level)) echo "style='display:none;'";?>>
								<th scope="row" valign="top"><label for="trial_amount"><?php _e('Trial Billing Amount', 'paid-memberships-pro' );?>:</label></th>
								<td>
									<?php
									if(pmpro_getCurrencyPosition() == "left")
										echo $pmpro_currency_symbol;
									?>
									<input name="trial_amount[]" type="text" size="20" value="<?php echo esc_attr( pmpro_filter_price_for_text_field( $level->trial_amount ) );?>" />
									<?php
									if(pmpro_getCurrencyPosition() == "right")
										echo $pmpro_currency_symbol;
									?>
									<?php _e('for the first', 'paid-memberships-pro' );?>
									<input name="trial_limit[]" type="text" size="10" value="<?php echo esc_attr( $level->trial_limit ); ?>" />
									<?php _e('subscription payments', 'paid-memberships-pro' );?>.
									<?php if($gateway == "stripe") { ?>
										<p class="description"><strong <?php if(!empty($pmpro_stripe_error)) { ?>class="pmpro_red"<?php } ?>><?php _e('Stripe integration currently does not support trial amounts greater than $0.', 'paid-memberships-pro' );?></strong></p>
									<?php } elseif($gateway == "braintree") { ?>
										<p class="description"><strong <?php if(!empty($pmpro_braintree_error)) { ?>class="pmpro_red"<?php } ?>><?php _e('Braintree integration currently does not support trial amounts greater than $0.', 'paid-memberships-pro' );?></strong></p>
									<?php } elseif($gateway == "payflowpro") { ?>
										<p class="description"><strong <?php if(!empty($pmpro_payflow_error)) { ?>class="pmpro_red"<?php } ?>><?php _e('Payflow integration currently does not support trial amounts greater than $0.', 'paid-memberships-pro' );?></strong></p>
									<?php } ?>
								</td>
							</tr>

							<tr>
								<th scope="row" valign="top"><label><?php _e('Membership Expiration', 'paid-memberships-pro' );?>:</label></th>
								<td><input id="expiration_<?php echo esc_attr( $level->id ); ?>" name="expiration[]" type="checkbox" value="<?php echo esc_attr( $level->id ); ?>" <?php if(pmpro_isLevelExpiring($level)) { echo "checked='checked'"; } ?> onclick="if(jQuery(this).is(':checked')) { jQuery(this).parent().parent().siblings('.expiration_info').show(); } else { jQuery(this).parent().parent().siblings('.expiration_info').hide();}" /> <label for="expiration_<?php echo esc_attr( $level->id ); ?>"><?php _e('Check this to set when membership access expires.', 'paid-memberships-pro' );?></label></td>
							</tr>

							<tr class="expiration_info" <?php if(!pmpro_isLevelExpiring($level)) {?>style="display: none;"<?php } ?>>
								<th scope="row" valign="top"><label for="billing_amount"><?php _e('Expires In', 'paid-memberships-pro' );?>:</label></th>
								<td>
									<input id="expiration_number" name="expiration_number[]" type="text" size="10" value="<?php echo esc_attr( $level->expiration_number ); ?>" />
									<select id="expiration_period" name="expiration_period[]">
									  <?php

										$cycles = array( __('Hour(s)', 'paid-memberships-pro' ) => 'Hour', __('Day(s)', 'paid-memberships-pro' ) => 'Day', __('Week(s)', 'paid-memberships-pro' ) => 'Week', __('Month(s)', 'paid-memberships-pro' ) => 'Month', __('Year(s)', 'paid-memberships-pro' ) => 'Year' );
										foreach ( $cycles as $name => $value ) {

										  echo "<option value='$value'";
										  if ( $level->expiration_period == $value ) echo " selected='selected'";
										  echo ">$name</option>";
										}
									  ?>

									</select>
									<p class="description"><?php _e('Set the duration of membership access. Note that the any future payments (recurring subscription, if any) will be cancelled when the membership expires.', 'paid-memberships-pro' );?></p>
								</td>
							</tr>
						</tbody>
					</table>

					<?php do_action("pmpro_discount_code_after_level_settings", $edit, $level); ?>

					</div>
				</div>
				<script>

				</script>
				<?php
				}
			?>
			</div>

			<p class="submit topborder">
				<input name="save" type="submit" class="button button-primary" value="Save Code" />
				<input name="cancel" type="button" class="button" value="Cancel" onclick="location.href='<?php echo admin_url( '/admin.php?page=pmpro-discountcodes')?>';" />
			</p>
			</form>
		</div>

	<?php } else { ?>

		<h1 class="wp-heading-inline"><?php esc_html_e( 'Memberships Discount Codes', 'paid-memberships-pro' ); ?></h1>
		<a href="admin.php?page=pmpro-discountcodes&edit=-1" class="page-title-action"><?php esc_html_e( 'Add New Discount Code', 'paid-memberships-pro' ); ?></a>
		<hr class="wp-header-end">

		<?php
			$sqlQuery = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(CONVERT_TZ(starts, '+00:00', @@global.time_zone)) as starts, UNIX_TIMESTAMP(CONVERT_TZ(expires, '+00:00', @@global.time_zone)) as expires FROM $wpdb->pmpro_discount_codes ";
			if( ! empty( $s ) ) {
				$sqlQuery .= "WHERE code LIKE '%$s%' ";
			}

			$sqlQuery .= "ORDER BY id DESC ";

			$sqlQuery .= "LIMIT $start, $limit ";

			$codes = $wpdb->get_results($sqlQuery, OBJECT);

			$totalrows = $wpdb->get_var( "SELECT FOUND_ROWS() as found_rows" );

			if( empty( $s ) && empty( $codes ) ) { ?>
				<div class="pmpro-new-install">
					<h2><?php echo esc_attr_e( 'No Discount Codes Found', 'paid-memberships-pro' ); ?></h2>
					<h4><?php _e( 'Discount codes allow you to override your membership level\'s default pricing.', 'paid-memberships-pro' ); ?></h4>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=pmpro-discountcodes&edit=-1' ) ) ; ?>" class="button-primary"><?php esc_attr_e( 'Create a Discount Code', 'paid-memberships-pro' );?></a>
					<a href="<?php echo esc_url( 'https://www.paidmembershipspro.com/documentation/discount-codes/?utm_source=plugin&utm_medium=pmpro-discountcodes&utm_campaign=documentation&utm_content=discount-codes' ); ?>" target="_blank" class="button"><?php echo esc_attr_e( 'Documentation: Discount Codes', 'paid-memberships-pro' ); ?></a>
				</div> <!-- end pmpro-new-install -->
			<?php } else { ?>

				<?php if(!empty($pmpro_msg)) { ?>
					<div id="message" class="<?php if($pmpro_msgt == "success") echo "updated fade"; else echo "error"; ?>"><p><?php echo $pmpro_msg?></p></div>
				<?php } ?>

				<?php if ( ! empty( $codes ) ) { ?>
					<p class="subsubsub"><?php printf( __( "%d discount codes found.", 'paid-memberships-pro' ), $totalrows ); ?></span></p>
				<?php } ?>

				<form id="posts-filter" method="get" action="">
					<p class="search-box">
						<label class="screen-reader-text" for="post-search-input"><?php _e('Search Discount Codes', 'paid-memberships-pro' );?>:</label>
						<input type="hidden" name="page" value="pmpro-discountcodes" />
						<input id="post-search-input" type="text" value="<?php echo esc_attr( wp_unslash( $s ) ); ?>" name="s" size="30" />
						<input class="button" type="submit" value="<?php esc_attr_e('Search', 'paid-memberships-pro' );?>" id="search-submit "/>
					</p>
				</form>

				<br class="clear" />

				<table class="widefat">
				<thead>
					<tr>
						<th><?php _e('ID', 'paid-memberships-pro' );?></th>
						<th><?php _e('Code', 'paid-memberships-pro' );?></th>
						<th><?php _e('Starts', 'paid-memberships-pro' );?></th>
						<th><?php _e('Expires', 'paid-memberships-pro' );?></th>
						<th><?php _e('Uses', 'paid-memberships-pro' );?></th>
						<th><?php _e('Levels', 'paid-memberships-pro' );?></th>
						<?php do_action("pmpro_discountcodes_extra_cols_header", $codes);?>
					</tr>
				</thead>
				<tbody>
					<?php if ( !empty( $s ) && empty( $codes ) ) { ?>
					<tr>
						<td colspan="6">
							<?php echo esc_attr_e( 'Code not found.', 'paid-memberships-pro' ); ?>
						</td>
					</tr>
					<?php } ?>
					<?php
						foreach($codes as $code) {
							$uses = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->pmpro_discount_codes_uses WHERE code_id = %d", $code->id ) );
							?>
						<tr<?php if ( ! pmpro_check_discount_code_for_gateway_compatibility( $code->id ) ) { ?> class="pmpro_error"<?php } ?>>
							<td><?php echo $code->id?></td>
							<td class="has-row-actions">
								<a title="<?php echo esc_attr( sprintf( __( 'Edit Code: %s', 'paid-memberships-pro' ), $code->code ) ); ?>" href="<?php echo esc_url( add_query_arg( array( 'page' => 'pmpro-discountcodes', 'edit' => $code->id ), admin_url('admin.php' ) ) ); ?>"><?php echo $code->code?></a>
								<div class="row-actions">
									<?php
									$delete_text = esc_html(
										sprintf(
											// translators: %s is the Discount Code.
											__( 'Are you sure you want to delete the %s discount code? The subscriptions for existing users will not change, but new users will not be able to use this code anymore.', 'paid-memberships-pro' ),
											$code->code
										)
									);

									$delete_nonce_url = wp_nonce_url(
										add_query_arg(
											[
												'page'   => 'pmpro-discountcodes',
												'delete' => $code->id,
											],
											admin_url( 'admin.php' )
										),
										'delete',
										'pmpro_discountcodes_nonce'
									);

									$actions = [
										'edit'   => sprintf(
											'<a title="%1$s" href="%2$s">%3$s</a>',
											esc_attr__( 'Edit', 'paid-memberships-pro' ),
											esc_url(
												add_query_arg(
													[
														'page' => 'pmpro-discountcodes',
														'edit' => $code->id,
													],
													admin_url( 'admin.php' )
												)
											),
											esc_html__( 'Edit', 'paid-memberships-pro' )
										),
										'copy'   => sprintf(
											'<a title="%1$s" href="%2$s">%3$s</a>',
											esc_attr__( 'Copy', 'paid-memberships-pro' ),
											esc_url(
												add_query_arg(
													[
														'page' => 'pmpro-discountcodes',
														'edit' => - 1,
														'copy' => $code->id,
													],
													admin_url( 'admin.php' )
												)
											),
											esc_html__( 'Copy', 'paid-memberships-pro' )
										),
										'delete' => sprintf(
											'<a title="%1$s" href="%2$s">%3$s</a>',
											esc_attr__( 'Delete', 'paid-memberships-pro' ),
											'javascript:pmpro_askfirst(\'' . esc_js( $delete_text ) . '\', \'' . esc_js( $delete_nonce_url ) . '\'); void(0);',
											esc_html__( 'Delete', 'paid-memberships-pro' )
										),
									];

									if ( 0 < (int) $uses ) {
										$actions['orders'] = sprintf(
											'<a title="%1$s" href="%2$s">%3$s</a>',
											esc_attr__( 'View Orders', 'paid-memberships-pro' ),
											esc_url(
												add_query_arg(
													[
														'page'          => 'pmpro-orders',
														'discount-code' => $code->id,
														'filter'        => 'with-discount-code',
													],
													admin_url( 'admin.php' )
												)
											),
											esc_html__( 'Orders', 'paid-memberships-pro' )
										);
									}

									/**
									 * Filter the extra actions for this discount code.
									 *
									 * @since 2.6.2
									 *
									 * @param array  $actions The list of actions.
									 * @param object $code    The discount code data.
									 */
									$actions = apply_filters( 'pmpro_discountcodes_row_actions', $actions, $code );

									$actions_html = [];

									foreach ( $actions as $action => $link ) {
										$actions_html[] = sprintf(
											'<span class="%1$s">%2$s</span>',
											esc_attr( $action ),
											$link
										);
									}

									if ( ! empty( $actions_html ) ) {
										echo implode( ' | ', $actions_html );
									}
									?>
								</div>
							</td>
							<td>
								<?php echo date_i18n(get_option('date_format'), $code->starts)?>
							</td>
							<td>
								<?php echo date_i18n(get_option('date_format'), $code->expires)?>
							</td>
							<td>
								<?php
									if($code->uses > 0)
										echo "<strong>" . (int)$uses . "</strong>/" . $code->uses;
									else
										echo "<strong>" . (int)$uses . "</strong>/unlimited";
								?>
							</td>
							<td>
								<?php
									$sqlQuery = $wpdb->prepare("
										SELECT l.id, l.name
										FROM $wpdb->pmpro_membership_levels l
										LEFT JOIN $wpdb->pmpro_discount_codes_levels cl
										ON l.id = cl.level_id
										WHERE cl.code_id = %d",
										$code->id
									);
									$levels = $wpdb->get_results($sqlQuery);

									$level_names = array();
									foreach( $levels as $level ) {
										if ( ! empty( $pmpro_pages['checkout'] ) ) {
											$level_names[] = '<a title="' . pmpro_url( 'checkout', '?level=' . $level->id . '&discount_code=' . $code->code) . '" target="_blank" href="' . pmpro_url( 'checkout', '?level=' . $level->id . '&discount_code=' . $code->code) . '">' . $level->name . '</a>';
										} else {
											$level_names[] = $level->name;
										}
									}
									if( $level_names ) {
										echo implode( ', ', $level_names );
									} else {
										echo 'None';
									}
								?>
							</td>
							<?php do_action("pmpro_discountcodes_extra_cols_body", $code);?>
						</tr>
					<?php
					}
				}
				?>
		</tbody>
		</table>

		<?php
			$pagination_url = admin_url( "/admin.php?page=pmpro-discountcodes&s=" . $s );
			echo pmpro_getPaginationString( $pn, $totalrows, $limit, 1, $pagination_url, "&limit=$limit&pn=" );
		?>

	<?php } ?>

<?php
	require_once(dirname(__FILE__) . "/admin_footer.php");
?>
