<?php
/* * ***************************************************************
 * Render the new Stripe Subscription payment button creation interface
 * ************************************************************** */

function swpm_render_new_edit_stripe_subscription_button_interface( $opts, $edit = false ) {

	//Test for PHP v5.6.0 or show error and don't show the remaining interface.
	if ( version_compare( PHP_VERSION, '5.6.0' ) < 0 ) {
		//This server can't handle Stripe library
		echo '<div class="swpm-red-box">';
		echo '<p>The Stripe payment gateway libary requires at least PHP 5.6.0. Your server is using a very old version of PHP that Stripe does not support.</p>';
		echo '<p>Request your hosting provider to upgrade your PHP to a more recent version then you will be able to use the Stripe gateway.<p>';
		echo '</div>';
		return;
	}

	if ( isset( $opts['stripe_use_global_keys'][0] ) ) {
		$use_global_keys = $opts['stripe_use_global_keys'][0];
	} else {
		$use_global_keys = $edit ? false : true;
	}

	$stripe_test_publishable_key = isset( $opts['stripe_test_publishable_key'][0] ) ? $opts['stripe_test_publishable_key'][0] : '';
	$stripe_test_secret_key      = isset( $opts['stripe_test_secret_key'][0] ) ? $opts['stripe_test_secret_key'][0] : '';

	$stripe_live_publishable_key = isset( $opts['stripe_live_publishable_key'][0] ) ? $opts['stripe_live_publishable_key'][0] : '';
	$stripe_live_secret_key      = isset( $opts['stripe_live_secret_key'][0] ) ? $opts['stripe_live_secret_key'][0] : '';

	function swpm_stripe_subscr_gen_curr_opts( $selected = false ) {
		$curr_arr = array(
			'USD' => 'US Dollars ($)',
			'EUR' => 'Euros (€)',
			'GBP' => 'Pounds Sterling (£)',
			'AUD' => 'Australian Dollars ($)',
			'BRL' => 'Brazilian Real (R$)',
			'CAD' => 'Canadian Dollars ($)',
			'CNY' => 'Chinese Yuan',
			'CZK' => 'Czech Koruna',
			'DKK' => 'Danish Krone',
			'HKD' => 'Hong Kong Dollar ($)',
			'HUF' => 'Hungarian Forint',
			'INR' => 'Indian Rupee',
			'IDR' => 'Indonesia Rupiah',
			'ILS' => 'Israeli Shekel',
			'JPY' => 'Japanese Yen (¥)',
			'MYR' => 'Malaysian Ringgits',
			'MXN' => 'Mexican Peso ($)',
			'NZD' => 'New Zealand Dollar ($)',
			'NOK' => 'Norwegian Krone',
			'PHP' => 'Philippine Pesos',
			'PLN' => 'Polish Zloty',
			'SGD' => 'Singapore Dollar ($)',
			'ZAR' => 'South African Rand (R)',
			'KRW' => 'South Korean Won',
			'SEK' => 'Swedish Krona',
			'CHF' => 'Swiss Franc',
			'TWD' => 'Taiwan New Dollars',
			'THB' => 'Thai Baht',
			'TRY' => 'Turkish Lira',
			'VND' => 'Vietnamese Dong',
		);
		$out      = '';
		foreach ( $curr_arr as $key => $value ) {
			if ( $selected !== false && $selected == $key ) {
				$sel = ' selected';
			} else {
				$sel = '';
			}
			$out .= '<option value="' . $key . '"' . $sel . '>' . $value . '</option>';
		}
		return $out;
	}
	?>

<div class="swpm-orange-box">
	View the <a target="_blank" href="https://simple-membership-plugin.com/create-stripe-subscription-button-membership-payment/">documentation</a>&nbsp;
	to learn how to create a Stripe Subscription payment button and use it.
</div>

<form id="stripe_subsciption_button_config_form" method="post">

	<div class="postbox">
		<h3 class="hndle"><label for="title"><?php echo SwpmUtils::_( 'Stripe Subscription Button Configuration' ); ?></label></h3>
		<div class="inside">
			<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">
				<?php if ( ! $edit ) { ?>
				<input type="hidden" name="button_type" value="<?php echo sanitize_text_field( $_REQUEST['button_type'] ); ?>">
				<input type="hidden" name="swpm_button_type_selected" value="1">
				<?php } else { ?>
				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Button ID' ); ?></th>
					<td>
						<input type="text" size="10" name="button_id" value="<?php echo $opts['button_id']; ?>" readonly required />
						<p class="description">This is the ID of this payment button. It is automatically generated for you and it cannot be changed.</p>
					</td>
				</tr>
				<?php } ?>
				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Button Title' ); ?></th>
					<td>
						<input type="text" size="50" name="button_name" value="<?php echo ( $edit ? $opts['button_title'] : '' ); ?>" required />
						<p class="description">Give this membership payment button a name. Example: Gold membership payment</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Membership Level' ); ?></th>
					<td>
						<select id="membership_level_id" name="membership_level_id">
							<?php echo ( $edit ? SwpmUtils::membership_level_dropdown( $opts['membership_level_id'][0] ) : SwpmUtils::membership_level_dropdown() ); ?>
						</select>
						<p class="description">Select the membership level this payment button is for.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Stripe API ID' ); ?></th>
					<td>
						<input type="text" name="stripe_plan_id" value="<?php echo ( $edit ? $opts['stripe_plan_id'][0] : '' ); ?>" required />
						<p class="description">
							ID of the plan that you want subscribers to be assigned to. You can get more details in the
							<a href="https://simple-membership-plugin.com/create-stripe-subscription-button-membership-payment/" target="_blank">documentation</a>.
						</p>
					</td>
				</tr>

			</table>

		</div>
	</div><!-- end of main button configuration box -->

	<div class="postbox">
		<h3 class="hndle"><label for="title"><?php echo SwpmUtils::_( 'Stripe API Settings' ); ?></label></h3>
		<div class="inside">

			<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Use Global API Keys Settings' ); ?></th>
					<td>
						<input type="checkbox" name="stripe_use_global_keys" value="1" <?php echo $use_global_keys ? ' checked' : ''; ?> />
						<p class="description"><?php echo SwpmUtils::_( 'Use API keys from <a href="admin.php?page=simple_wp_membership_settings&tab=2" target="_blank">Payment Settings</a> tab.' ); ?></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Test Publishable Key' ); ?></th>
					<td>
						<input type="text" size="100" name="stripe_test_publishable_key" value="<?php echo esc_attr( $edit ? $stripe_test_publishable_key : '' ); ?>" />
						<p class="description">Enter your Stripe test publishable key.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Test Secret Key' ); ?></th>
					<td>
						<input type="text" size="100" name="stripe_test_secret_key" value="<?php echo esc_attr( $edit ? $stripe_test_secret_key : '' ); ?>" />
						<p class="description">Enter your Stripe test secret key.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Live Publishable Key' ); ?></th>
					<td>
						<input type="text" size="100" name="stripe_live_publishable_key" value="<?php echo esc_attr( $edit ? $stripe_live_publishable_key : '' ); ?>" />
						<p class="description">Enter your Stripe live publishable key.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Live Secret Key' ); ?></th>
					<td>
						<input type="text" size="100" name="stripe_live_secret_key" value="<?php echo esc_attr( $edit ? $stripe_live_secret_key : '' ); ?>" />
						<p class="description">Enter your Stripe live secret key.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Webook Endpoint URL' ); ?></th>
					<td>
						<kbd><?php echo SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/?swpm_process_stripe_subscription=1&hook=1'; ?></kbd>
						<p class="description">You should create a new Webhook in your Stripe account and put this URL there if you want the plugin to handle subscription expiration automatically.<br />
							You can get more info in the <a href="https://simple-membership-plugin.com/create-stripe-subscription-button-membership-payment/" target="_blank">documentation</a>.
						</p>
					</td>
				</tr>

				<script>
				var swpmInputsArr = ['stripe_test_publishable_key', 'stripe_test_secret_key', 'stripe_live_publishable_key', 'stripe_live_secret_key'];
				jQuery('input[name="stripe_use_global_keys"').change(function() {
					var checked = jQuery(this).prop('checked');
					jQuery.each(swpmInputsArr, function(index, el) {
						jQuery('input[name="' + el + '"]').prop('disabled', checked);
					});
				});
				jQuery('input[name="stripe_use_global_keys"').trigger('change');
				</script>

			</table>
		</div>
	</div><!-- end of Stripe API Keys box -->

	<div class="postbox">
		<h3 class="hndle"><label for="title"><?php echo SwpmUtils::_( 'Optional Details' ); ?></label></h3>
		<div class="inside">

			<table class="form-table" width="100%" border="0" cellspacing="0" cellpadding="6">

				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Collect Customer Address' ); ?></th>
					<td>
						<input type="checkbox" name="collect_address" value="1" <?php echo ( $edit ? ( ( isset( $opts['stripe_collect_address'][0] ) && $opts['stripe_collect_address'][0] === '1' ) ? ' checked' : '' ) : '' ); ?> />
						<p class="description">Enable this option if you want to collect customer address during Stripe checkout.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Return URL' ); ?></th>
					<td>
						<input type="text" size="100" name="return_url" value="<?php echo ( $edit ? $opts['return_url'][0] : '' ); ?>" />
						<p class="description">This is the URL the user will be redirected to after a successful payment. Enter the URL of your Thank You page here.</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php echo SwpmUtils::_( 'Button Image URL' ); ?></th>
					<td>
						<input type="text" size="100" name="button_image_url" value="<?php echo ( $edit ? $opts['button_image_url'][0] : '' ); ?>" />
						<p class="description">If you want to customize the look of the button using an image then enter the URL of the image.</p>
					</td>
				</tr>

			</table>
		</div>
	</div><!-- end of optional details box -->

	<p class="submit">
		<?php wp_nonce_field( 'swpm_admin_add_edit_stripe_subs_btn', 'swpm_admin_add_edit_stripe_subs_btn' ); ?>
		<input type="submit" name="swpm_stripe_subscription_<?php echo ( $edit ? 'edit' : 'save' ); ?>_submit" class="button-primary" value="<?php echo SwpmUtils::_( 'Save Payment Data' ); ?>">
	</p>

</form>

	<?php
}

add_action( 'swpm_create_new_button_for_stripe_subscription', 'swpm_create_new_stripe_subscription_button' );

function swpm_create_new_stripe_subscription_button() {
	swpm_render_new_edit_stripe_subscription_button_interface( '' );
}

add_action( 'swpm_edit_payment_button_for_stripe_subscription', 'swpm_edit_stripe_subscription_button' );

function swpm_edit_stripe_subscription_button() {
	//Retrieve the payment button data and present it for editing.

	$button_id = sanitize_text_field( $_REQUEST['button_id'] );
	$button_id = absint( $button_id );

	$button = get_post( $button_id ); //Retrieve the CPT for this button

	$post_meta                 = get_post_meta( $button_id );
	$post_meta['button_title'] = $button->post_title;
	$post_meta['button_id']    = $button_id;

	swpm_render_new_edit_stripe_subscription_button_interface( $post_meta, true );
}

/*
 * Process submission and save the new PayPal Subscription payment button data
 */
add_action( 'swpm_create_new_button_process_submission', 'swpm_save_edit_stripe_subscription_button_data' );
add_action( 'swpm_edit_payment_button_process_submission', 'swpm_save_edit_stripe_subscription_button_data' );

function swpm_save_edit_stripe_subscription_button_data() {
	if ( isset( $_REQUEST['swpm_stripe_subscription_save_submit'] ) ) {
		$edit = false;
	}
	if ( isset( $_REQUEST['swpm_stripe_subscription_edit_submit'] ) ) {
		$edit = true;
	}
	if ( isset( $edit ) ) {
		//This is a Stripe subscription button save or edit event. Process the submission.
		check_admin_referer( 'swpm_admin_add_edit_stripe_subs_btn', 'swpm_admin_add_edit_stripe_subs_btn' );
		if ( $edit ) {
			$button_id   = sanitize_text_field( $_REQUEST['button_id'] );
			$button_id   = absint( $button_id );
			$button_type = sanitize_text_field( $_REQUEST['button_type'] );
			$button_name = sanitize_text_field( $_REQUEST['button_name'] );

			$button_post = array(
				'ID'         => $button_id,
				'post_title' => $button_name,
				'post_type'  => 'swpm_payment_button',
			);
			wp_update_post( $button_post );
		} else {
			$button_id   = wp_insert_post(
				array(
					'post_title'   => sanitize_text_field( $_REQUEST['button_name'] ),
					'post_type'    => 'swpm_payment_button',
					'post_content' => '',
					'post_status'  => 'publish',
				)
			);
			$button_type = sanitize_text_field( $_REQUEST['button_type'] );
		}

		update_post_meta( $button_id, 'button_type', $button_type );
		update_post_meta( $button_id, 'membership_level_id', sanitize_text_field( $_REQUEST['membership_level_id'] ) );
		update_post_meta( $button_id, 'return_url', trim( sanitize_text_field( $_REQUEST['return_url'] ) ) );
		update_post_meta( $button_id, 'button_image_url', trim( sanitize_text_field( $_REQUEST['button_image_url'] ) ) );
		update_post_meta( $button_id, 'stripe_collect_address', isset( $_POST['collect_address'] ) ? '1' : '' );

		//API details
		$stripe_test_secret_key      = filter_input( INPUT_POST, 'stripe_test_secret_key', FILTER_SANITIZE_STRING );
		$stripe_test_publishable_key = filter_input( INPUT_POST, 'stripe_test_publishable_key', FILTER_SANITIZE_STRING );
		$stripe_live_secret_key      = filter_input( INPUT_POST, 'stripe_live_secret_key', FILTER_SANITIZE_STRING );
		$stripe_live_publishable_key = filter_input( INPUT_POST, 'stripe_live_publishable_key', FILTER_SANITIZE_STRING );
		if ( isset( $stripe_test_secret_key ) ) {
			update_post_meta( $button_id, 'stripe_test_secret_key', sanitize_text_field( $stripe_test_secret_key ) );
		}
		if ( isset( $stripe_test_publishable_key ) ) {
			update_post_meta( $button_id, 'stripe_test_publishable_key', sanitize_text_field( $stripe_test_publishable_key ) );
		}
		if ( isset( $stripe_live_secret_key ) ) {
			update_post_meta( $button_id, 'stripe_live_secret_key', sanitize_text_field( $stripe_live_secret_key ) );
		}
		if ( isset( $stripe_live_publishable_key ) ) {
			update_post_meta( $button_id, 'stripe_live_publishable_key', sanitize_text_field( $stripe_live_publishable_key ) );
		}

		$stripe_use_global_keys = filter_input( INPUT_POST, 'stripe_use_global_keys', FILTER_SANITIZE_NUMBER_INT );
		$stripe_use_global_keys = $stripe_use_global_keys ? true : false;

		update_post_meta( $button_id, 'stripe_use_global_keys', $stripe_use_global_keys );

		if ( $edit ) {
			// let's see if Stripe details (plan ID and Secret Key) are valid
			$stripe_error_msg = '';
			$settings         = SwpmSettings::get_instance();
			$sandbox_enabled  = $settings->get_value( 'enable-sandbox-testing' );
			if ( $sandbox_enabled ) {
				$secret_key = $stripe_test_secret_key ? $stripe_test_secret_key : $settings->get_value( 'stripe-test-secret-key' );
			} else {
				$secret_key = $stripe_live_secret_key ? $stripe_live_secret_key : $settings->get_value( 'stripe-live-secret-key' );
			}

			require_once SIMPLE_WP_MEMBERSHIP_PATH . 'lib/stripe-util-functions.php';
			$result = StripeUtilFunctions::get_stripe_plan_info( $secret_key, sanitize_text_field( $_REQUEST['stripe_plan_id'] ) );
			if ( $result['success'] ) {
				$plan_data = $result['plan_data'];
			} else {
				$stripe_error_msg = $result['error_msg'];
			}
		}

		//Subscription billing details
		update_post_meta( $button_id, 'stripe_plan_id', sanitize_text_field( $_REQUEST['stripe_plan_id'] ) );
		update_post_meta( $button_id, 'stripe_plan_data', ( isset( $plan_data ) ? $plan_data : false ) );

		if ( $edit ) {
			if ( empty( $stripe_error_msg ) ) {
				echo '<div id="message" class="updated fade"><p>Payment button data successfully updated!</p></div>';
			} else {
				echo '<div id="message" class="error"><p>' . $stripe_error_msg . '</p></div>';
			}
		} else {
			//Redirect to the edit interface of this button with $button_id
			$url = admin_url() . 'admin.php?page=simple_wp_membership_payments&tab=edit_button&button_id=' . $button_id . '&button_type=' . $button_type;
			SwpmMiscUtils::redirect_to_url( $url );
		}
	}
}
