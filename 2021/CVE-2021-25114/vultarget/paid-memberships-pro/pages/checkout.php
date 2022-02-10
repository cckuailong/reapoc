<?php
/**
 * Template: Checkout
 *
 * See documentation for how to override the PMPro templates.
 * @link https://www.paidmembershipspro.com/documentation/templates/
 *
 * @version 2.0
 *
 * @author Paid Memberships Pro
 */

global $gateway, $pmpro_review, $skip_account_fields, $pmpro_paypal_token, $wpdb, $current_user, $pmpro_msg, $pmpro_msgt, $pmpro_requirebilling, $pmpro_level, $pmpro_levels, $tospage, $pmpro_show_discount_code, $pmpro_error_fields;
global $discount_code, $username, $password, $password2, $bfirstname, $blastname, $baddress1, $baddress2, $bcity, $bstate, $bzipcode, $bcountry, $bphone, $bemail, $bconfirmemail, $CardType, $AccountNumber, $ExpirationMonth,$ExpirationYear;

/**
 * Filter to set if PMPro uses email or text as the type for email field inputs.
 *
 * @since 1.8.4.5
 *
 * @param bool $use_email_type, true to use email type, false to use text type
 */
$pmpro_email_field_type = apply_filters('pmpro_email_field_type', true);

// Set the wrapping class for the checkout div based on the default gateway;
$default_gateway = pmpro_getOption( 'gateway' );
if ( empty( $default_gateway ) ) {
	$pmpro_checkout_gateway_class = 'pmpro_checkout_gateway-none';
} else {
	$pmpro_checkout_gateway_class = 'pmpro_checkout_gateway-' . $default_gateway;
}
?>

<?php do_action('pmpro_checkout_before_form'); ?>

<div id="pmpro_level-<?php echo $pmpro_level->id; ?>" class="<?php echo pmpro_get_element_class( $pmpro_checkout_gateway_class, 'pmpro_level-' . $pmpro_level->id ); ?>">
<form id="pmpro_form" class="<?php echo pmpro_get_element_class( 'pmpro_form' ); ?>" action="<?php if(!empty($_REQUEST['review'])) echo pmpro_url("checkout", "?level=" . $pmpro_level->id); ?>" method="post">

	<input type="hidden" id="level" name="level" value="<?php echo esc_attr($pmpro_level->id) ?>" />
	<input type="hidden" id="checkjavascript" name="checkjavascript" value="1" />
	<?php if ($discount_code && $pmpro_review) { ?>
		<input class="<?php echo pmpro_get_element_class( 'input pmpro_alter_price', 'discount_code' ); ?>" id="discount_code" name="discount_code" type="hidden" size="20" value="<?php echo esc_attr($discount_code) ?>" />
	<?php } ?>

	<?php if($pmpro_msg) { ?>
		<div id="pmpro_message" class="<?php echo pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ); ?>">
			<?php echo apply_filters( 'pmpro_checkout_message', $pmpro_msg, $pmpro_msgt ) ?>
		</div>
	<?php } else { ?>
		<div id="pmpro_message" class="<?php echo pmpro_get_element_class( 'pmpro_message' ); ?>" style="display: none;"></div>
	<?php } ?>

	<?php if($pmpro_review) { ?>
		<p><?php _e('Almost done. Review the membership information and pricing below then <strong>click the "Complete Payment" button</strong> to finish your order.', 'paid-memberships-pro' );?></p>
	<?php } ?>

	<?php
		$include_pricing_fields = apply_filters( 'pmpro_include_pricing_fields', true );
		if ( $include_pricing_fields ) {
		?>
		<div id="pmpro_pricing_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_pricing_fields' ); ?>">
			<h3>
				<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php _e('Membership Level', 'paid-memberships-pro' );?></span>
				<?php if(count($pmpro_levels) > 1) { ?><span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-msg' ); ?>"><a href="<?php echo pmpro_url("levels"); ?>"><?php _e('change', 'paid-memberships-pro' );?></a></span><?php } ?>
			</h3>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
				<p>
					<?php printf(__('You have selected the <strong>%s</strong> membership level.', 'paid-memberships-pro' ), $pmpro_level->name);?>
				</p>

				<?php
					/**
					 * All devs to filter the level description at checkout.
					 * We also have a function in includes/filters.php that applies the the_content filters to this description.
					 * @param string $description The level description.
					 * @param object $pmpro_level The PMPro Level object.
					 */
					$level_description = apply_filters('pmpro_level_description', $pmpro_level->description, $pmpro_level);
					if(!empty($level_description))
						echo $level_description;
				?>

				<div id="pmpro_level_cost">
					<?php if($discount_code && pmpro_checkDiscountCode($discount_code)) { ?>
						<?php printf(__('<p class="' . pmpro_get_element_class( 'pmpro_level_discount_applied' ) . '">The <strong>%s</strong> code has been applied to your order.</p>', 'paid-memberships-pro' ), $discount_code);?>
					<?php } ?>
					<?php echo wpautop(pmpro_getLevelCost($pmpro_level)); ?>
					<?php echo wpautop(pmpro_getLevelExpiration($pmpro_level)); ?>
				</div>

				<?php do_action("pmpro_checkout_after_level_cost"); ?>

				<?php if($pmpro_show_discount_code) { ?>
					<?php if($discount_code && !$pmpro_review) { ?>
						<p id="other_discount_code_p" class="<?php echo pmpro_get_element_class( 'pmpro_small', 'other_discount_code_p' ); ?>"><a id="other_discount_code_a" href="#discount_code"><?php _e('Click here to change your discount code.', 'paid-memberships-pro' );?></a></p>
					<?php } elseif(!$pmpro_review) { ?>
						<p id="other_discount_code_p" class="<?php echo pmpro_get_element_class( 'pmpro_small', 'other_discount_code_p' ); ?>"><?php _e('Do you have a discount code?', 'paid-memberships-pro' );?> <a id="other_discount_code_a" href="#discount_code"><?php _e('Click here to enter your discount code', 'paid-memberships-pro' );?></a>.</p>
					<?php } elseif($pmpro_review && $discount_code) { ?>
						<p><strong><?php _e('Discount Code', 'paid-memberships-pro' );?>:</strong> <?php echo $discount_code?></p>
					<?php } ?>
				<?php } ?>

				<?php if($pmpro_show_discount_code) { ?>
				<div id="other_discount_code_tr" style="display: none;">
					<label for="other_discount_code"><?php _e('Discount Code', 'paid-memberships-pro' );?></label>
					<input id="other_discount_code" name="other_discount_code" type="text" class="<?php echo pmpro_get_element_class( 'input pmpro_alter_price', 'other_discount_code' ); ?>" size="20" value="<?php echo esc_attr($discount_code); ?>" />
					<input type="button" name="other_discount_code_button" id="other_discount_code_button" value="<?php _e('Apply', 'paid-memberships-pro' );?>" />
				</div>
				<?php } ?>
			</div> <!-- end pmpro_checkout-fields -->
		</div> <!-- end pmpro_pricing_fields -->
		<?php
		} // if ( $include_pricing_fields )
	?>

	<?php
		do_action('pmpro_checkout_after_pricing_fields');
	?>

	<?php if(!$skip_account_fields && !$pmpro_review) { ?>

	<?php 
		// Get discount code from URL parameter, so if the user logs in it will keep it applied.
		$discount_code_link = !empty( $discount_code) ? '&discount_code=' . $discount_code : ''; 
	?>
	<div id="pmpro_user_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_user_fields' ); ?>">
		<hr />
		<h3>
			<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php _e('Account Information', 'paid-memberships-pro' );?></span>
			<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-msg' ); ?>"><?php _e('Already have an account?', 'paid-memberships-pro' );?> <a href="<?php echo wp_login_url( apply_filters( 'pmpro_checkout_login_redirect', pmpro_url("checkout", "?level=" . $pmpro_level->id . $discount_code_link) ) ); ?>"><?php _e('Log in here', 'paid-memberships-pro' );?></a></span>
		</h3>
		<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-username', 'pmpro_checkout-field-username' ); ?>">
				<label for="username"><?php _e('Username', 'paid-memberships-pro' );?></label>
				<input id="username" name="username" type="text" class="<?php echo pmpro_get_element_class( 'input', 'username' ); ?>" size="30" value="<?php echo esc_attr($username); ?>" />
			</div> <!-- end pmpro_checkout-field-username -->

			<?php
				do_action('pmpro_checkout_after_username');
			?>

			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-password', 'pmpro_checkout-field-password' ); ?>">
				<label for="password"><?php _e('Password', 'paid-memberships-pro' );?></label>
				<input id="password" name="password" type="password" class="<?php echo pmpro_get_element_class( 'input', 'password' ); ?>" size="30" value="<?php echo esc_attr($password); ?>" />
			</div> <!-- end pmpro_checkout-field-password -->

			<?php
				$pmpro_checkout_confirm_password = apply_filters("pmpro_checkout_confirm_password", true);
				if($pmpro_checkout_confirm_password) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-password2', 'pmpro_checkout-field-password2' ); ?>">
						<label for="password2"><?php _e('Confirm Password', 'paid-memberships-pro' );?></label>
						<input id="password2" name="password2" type="password" class="<?php echo pmpro_get_element_class( 'input', 'password2' ); ?>" size="30" value="<?php echo esc_attr($password2); ?>" />
					</div> <!-- end pmpro_checkout-field-password2 -->
				<?php } else { ?>
					<input type="hidden" name="password2_copy" value="1" />
				<?php }
			?>

			<?php
				do_action('pmpro_checkout_after_password');
			?>

			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bemail', 'pmpro_checkout-field-bemail' ); ?>">
				<label for="bemail"><?php _e('Email Address', 'paid-memberships-pro' );?></label>
				<input id="bemail" name="bemail" type="<?php echo ($pmpro_email_field_type ? 'email' : 'text'); ?>" class="<?php echo pmpro_get_element_class( 'input', 'bemail' ); ?>" size="30" value="<?php echo esc_attr($bemail); ?>" />
			</div> <!-- end pmpro_checkout-field-bemail -->

			<?php
				$pmpro_checkout_confirm_email = apply_filters("pmpro_checkout_confirm_email", true);
				if($pmpro_checkout_confirm_email) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bconfirmemail', 'pmpro_checkout-field-bconfirmemail' ); ?>">
						<label for="bconfirmemail"><?php _e('Confirm Email Address', 'paid-memberships-pro' );?></label>
						<input id="bconfirmemail" name="bconfirmemail" type="<?php echo ($pmpro_email_field_type ? 'email' : 'text'); ?>" class="<?php echo pmpro_get_element_class( 'input', 'bconfirmemail' ); ?>" size="30" value="<?php echo esc_attr($bconfirmemail); ?>" />
					</div> <!-- end pmpro_checkout-field-bconfirmemail -->
				<?php } else { ?>
					<input type="hidden" name="bconfirmemail_copy" value="1" />
				<?php }
			?>

			<?php
				do_action('pmpro_checkout_after_email');
			?>

			<div class="<?php echo pmpro_get_element_class( 'pmpro_hidden' ); ?>">
				<label for="fullname"><?php _e('Full Name', 'paid-memberships-pro' );?></label>
				<input id="fullname" name="fullname" type="text" class="<?php echo pmpro_get_element_class( 'input', 'fullname' ); ?>" size="30" value="" autocomplete="off"/> <strong><?php _e('LEAVE THIS BLANK', 'paid-memberships-pro' );?></strong>
			</div> <!-- end pmpro_hidden -->

		</div>  <!-- end pmpro_checkout-fields -->
	</div> <!-- end pmpro_user_fields -->
	<?php } elseif($current_user->ID && !$pmpro_review) { ?>
		<div id="pmpro_account_loggedin" class="<?php echo pmpro_get_element_class( 'pmpro_message pmpro_alert', 'pmpro_account_loggedin' ); ?>">
			<?php printf(__('You are logged in as <strong>%s</strong>. If you would like to use a different account for this membership, <a href="%s">log out now</a>.', 'paid-memberships-pro' ), $current_user->user_login, wp_logout_url($_SERVER['REQUEST_URI'])); ?>
		</div> <!-- end pmpro_account_loggedin -->
	<?php } ?>

	<?php
		do_action('pmpro_checkout_after_user_fields');
	?>

	<?php
		do_action('pmpro_checkout_boxes');
	?>

	<?php if(pmpro_getGateway() == "paypal" && empty($pmpro_review) && true == apply_filters('pmpro_include_payment_option_for_paypal', true ) ) { ?>
	<div id="pmpro_payment_method" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_payment_method' ); ?>" <?php if(!$pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
		<hr />
		<h3>
			<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php _e('Choose your Payment Method', 'paid-memberships-pro' ); ?></span>
		</h3>
		<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
			<span class="<?php echo pmpro_get_element_class( 'gateway_paypal' ); ?>">
				<input type="radio" name="gateway" value="paypal" <?php if(!$gateway || $gateway == "paypal") { ?>checked="checked"<?php } ?> />
				<a href="javascript:void(0);" class="<?php echo pmpro_get_element_class( 'pmpro_radio' ); ?>"><?php _e('Check Out with a Credit Card Here', 'paid-memberships-pro' );?></a>
			</span>
			<span class="<?php echo pmpro_get_element_class( 'gateway_paypalexpress' ); ?>">
				<input type="radio" name="gateway" value="paypalexpress" <?php if($gateway == "paypalexpress") { ?>checked="checked"<?php } ?> />
				<a href="javascript:void(0);" class="<?php echo pmpro_get_element_class( 'pmpro_radio' ); ?>"><?php _e('Check Out with PayPal', 'paid-memberships-pro' );?></a>
			</span>
		</div> <!-- end pmpro_checkout-fields -->
	</div> <!-- end pmpro_payment_method -->
	<?php } ?>

	<?php
		$pmpro_include_billing_address_fields = apply_filters('pmpro_include_billing_address_fields', true);
		if($pmpro_include_billing_address_fields) { ?>
	<div id="pmpro_billing_address_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_billing_address_fields' ); ?>" <?php if(!$pmpro_requirebilling || apply_filters("pmpro_hide_billing_address_fields", false) ){ ?>style="display: none;"<?php } ?>>
		<hr />
		<h3>
			<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php _e('Billing Address', 'paid-memberships-pro' );?></span>
		</h3>
		<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bfirstname', 'pmpro_checkout-field-bfirstname' ); ?>">
				<label for="bfirstname"><?php _e('First Name', 'paid-memberships-pro' );?></label>
				<input id="bfirstname" name="bfirstname" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bfirstname' ); ?>" size="30" value="<?php echo esc_attr($bfirstname); ?>" />
			</div> <!-- end pmpro_checkout-field-bfirstname -->
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-blastname', 'pmpro_checkout-field-blastname' ); ?>">
				<label for="blastname"><?php _e('Last Name', 'paid-memberships-pro' );?></label>
				<input id="blastname" name="blastname" type="text" class="<?php echo pmpro_get_element_class( 'input', 'blastname' ); ?>" size="30" value="<?php echo esc_attr($blastname); ?>" />
			</div> <!-- end pmpro_checkout-field-blastname -->
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-baddress1', 'pmpro_checkout-field-baddress1' ); ?>">
				<label for="baddress1"><?php _e('Address 1', 'paid-memberships-pro' );?></label>
				<input id="baddress1" name="baddress1" type="text" class="<?php echo pmpro_get_element_class( 'input', 'baddress1' ); ?>" size="30" value="<?php echo esc_attr($baddress1); ?>" />
			</div> <!-- end pmpro_checkout-field-baddress1 -->
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-baddress2', 'pmpro_checkout-field-baddress2' ); ?>">
				<label for="baddress2"><?php _e('Address 2', 'paid-memberships-pro' );?></label>
				<input id="baddress2" name="baddress2" type="text" class="<?php echo pmpro_get_element_class( 'input', 'baddress2' ); ?>" size="30" value="<?php echo esc_attr($baddress2); ?>" />
			</div> <!-- end pmpro_checkout-field-baddress2 -->
			<?php
				$longform_address = apply_filters("pmpro_longform_address", true);
				if($longform_address) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bcity', 'pmpro_checkout-field-bcity' ); ?>">
						<label for="bcity"><?php _e('City', 'paid-memberships-pro' );?></label>
						<input id="bcity" name="bcity" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bcity' ); ?>" size="30" value="<?php echo esc_attr($bcity); ?>" />
					</div> <!-- end pmpro_checkout-field-bcity -->
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bstate', 'pmpro_checkout-field-bstate' ); ?>">
						<label for="bstate"><?php _e('State', 'paid-memberships-pro' );?></label>
						<input id="bstate" name="bstate" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bstate' ); ?>" size="30" value="<?php echo esc_attr($bstate); ?>" />
					</div> <!-- end pmpro_checkout-field-bstate -->
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bzipcode', 'pmpro_checkout-field-bzipcode' ); ?>">
						<label for="bzipcode"><?php _e('Postal Code', 'paid-memberships-pro' );?></label>
						<input id="bzipcode" name="bzipcode" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bzipcode' ); ?>" size="30" value="<?php echo esc_attr($bzipcode); ?>" />
					</div> <!-- end pmpro_checkout-field-bzipcode -->
				<?php } else { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bcity_state_zip', 'pmpro_checkout-field-bcity_state_zip' ); ?>">
						<label for="bcity_state_zip' ); ?>"><?php _e('City, State Zip', 'paid-memberships-pro' );?></label>
						<input id="bcity" name="bcity" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bcity' ); ?>" size="14" value="<?php echo esc_attr($bcity); ?>" />,
						<?php
							$state_dropdowns = apply_filters("pmpro_state_dropdowns", false);
							if($state_dropdowns === true || $state_dropdowns == "names") {
								global $pmpro_states;
								?>
								<select name="bstate" class="<?php echo pmpro_get_element_class( '', 'bstate' ); ?>">
									<option value="">--</option>
									<?php
										foreach($pmpro_states as $ab => $st) { ?>
											<option value="<?php echo esc_attr($ab);?>" <?php if($ab == $bstate) { ?>selected="selected"<?php } ?>><?php echo $st;?></option>
									<?php } ?>
								</select>
							<?php } elseif($state_dropdowns == "abbreviations") {
								global $pmpro_states_abbreviations;
								?>
								<select name="bstate" class="<?php echo pmpro_get_element_class( '', 'bstate' ); ?>">
									<option value="">--</option>
									<?php
										foreach($pmpro_states_abbreviations as $ab)
										{
									?>
										<option value="<?php echo esc_attr($ab);?>" <?php if($ab == $bstate) { ?>selected="selected"<?php } ?>><?php echo $ab;?></option>
									<?php } ?>
								</select>
							<?php } else { ?>
								<input id="bstate" name="bstate" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bstate' ); ?>" size="2" value="<?php echo esc_attr($bstate); ?>" />
						<?php } ?>
						<input id="bzipcode" name="bzipcode" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bzipcode' ); ?>" size="5" value="<?php echo esc_attr($bzipcode); ?>" />
					</div> <!-- end pmpro_checkout-field-bcity_state_zip -->
			<?php } ?>

			<?php
				$show_country = apply_filters("pmpro_international_addresses", true);
				if($show_country) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bcountry', 'pmpro_checkout-field-bcountry' ); ?>">
						<label for="bcountry"><?php _e('Country', 'paid-memberships-pro' );?></label>
						<select name="bcountry" id="bcountry" class="<?php echo pmpro_get_element_class( '', 'bcountry' ); ?>">
						<?php
							global $pmpro_countries, $pmpro_default_country;
							if(!$bcountry) {
								$bcountry = $pmpro_default_country;
							}
							foreach($pmpro_countries as $abbr => $country) { ?>
								<option value="<?php echo $abbr?>" <?php if($abbr == $bcountry) { ?>selected="selected"<?php } ?>><?php echo $country?></option>
							<?php } ?>
						</select>
					</div> <!-- end pmpro_checkout-field-bcountry -->
				<?php } else { ?>
					<input type="hidden" name="bcountry" value="US" />
				<?php } ?>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bphone', 'pmpro_checkout-field-bphone' ); ?>">
				<label for="bphone"><?php _e('Phone', 'paid-memberships-pro' );?></label>
				<input id="bphone" name="bphone" type="text" class="<?php echo pmpro_get_element_class( 'input', 'bphone' ); ?>" size="30" value="<?php echo esc_attr(formatPhone($bphone)); ?>" />
			</div> <!-- end pmpro_checkout-field-bphone -->
			<?php if($skip_account_fields) { ?>
			<?php
				if($current_user->ID) {
					if(!$bemail && $current_user->user_email) {
						$bemail = $current_user->user_email;
					}
					if(!$bconfirmemail && $current_user->user_email) {
						$bconfirmemail = $current_user->user_email;
					}
				}
			?>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bemail', 'pmpro_checkout-field-bemail' ); ?>">
				<label for="bemail"><?php _e('Email Address', 'paid-memberships-pro' );?></label>
				<input id="bemail" name="bemail" type="<?php echo ($pmpro_email_field_type ? 'email' : 'text'); ?>" class="<?php echo pmpro_get_element_class( 'input', 'bemail' ); ?>" size="30" value="<?php echo esc_attr($bemail); ?>" />
			</div> <!-- end pmpro_checkout-field-bemail -->
			<?php
				$pmpro_checkout_confirm_email = apply_filters("pmpro_checkout_confirm_email", true);
				if($pmpro_checkout_confirm_email) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_checkout-field-bconfirmemail', 'pmpro_checkout-field-bconfirmemail' ); ?>">
						<label for="bconfirmemail"><?php _e('Confirm Email', 'paid-memberships-pro' );?></label>
						<input id="bconfirmemail" name="bconfirmemail" type="<?php echo ($pmpro_email_field_type ? 'email' : 'text'); ?>" class="<?php echo pmpro_get_element_class( 'input', 'bconfirmemail' ); ?>" size="30" value="<?php echo esc_attr($bconfirmemail); ?>" />
					</div> <!-- end pmpro_checkout-field-bconfirmemail -->
				<?php } else { ?>
					<input type="hidden" name="bconfirmemail_copy" value="1" />
				<?php } ?>
			<?php } ?>
		</div> <!-- end pmpro_checkout-fields -->
	</div> <!--end pmpro_billing_address_fields -->
	<?php } ?>

	<?php do_action("pmpro_checkout_after_billing_fields"); ?>

	<?php
		$pmpro_accepted_credit_cards = pmpro_getOption("accepted_credit_cards");
		$pmpro_accepted_credit_cards = explode(",", $pmpro_accepted_credit_cards);
		$pmpro_accepted_credit_cards_string = pmpro_implodeToEnglish($pmpro_accepted_credit_cards);
	?>

	<?php
		$pmpro_include_payment_information_fields = apply_filters("pmpro_include_payment_information_fields", true);
		if($pmpro_include_payment_information_fields) { ?>
		<div id="pmpro_payment_information_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_payment_information_fields' ); ?>" <?php if(!$pmpro_requirebilling || apply_filters("pmpro_hide_payment_information_fields", false) ) { ?>style="display: none;"<?php } ?>>
			<hr />
			<h3>
				<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php _e('Payment Information', 'paid-memberships-pro' );?></span>
				<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-msg' ); ?>"><?php printf(__('We Accept %s', 'paid-memberships-pro' ), $pmpro_accepted_credit_cards_string);?></span>
			</h3>
			<?php $sslseal = pmpro_getOption("sslseal"); ?>
			<?php if(!empty($sslseal)) { ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields-display-seal' ); ?>">
			<?php } ?>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
				<?php
					$pmpro_include_cardtype_field = apply_filters('pmpro_include_cardtype_field', false);
					if($pmpro_include_cardtype_field) { ?>
						<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-card-type', 'pmpro_payment-card-type' ); ?>">
							<label for="CardType"><?php _e('Card Type', 'paid-memberships-pro' );?></label>
							<select id="CardType" name="CardType" class="<?php echo pmpro_get_element_class( '', 'CardType' ); ?>">
								<?php foreach($pmpro_accepted_credit_cards as $cc) { ?>
									<option value="<?php echo $cc; ?>" <?php if($CardType == $cc) { ?>selected="selected"<?php } ?>><?php echo $cc; ?></option>
								<?php } ?>
							</select>
						</div>
					<?php } else { ?>
						<input type="hidden" id="CardType" name="CardType" value="<?php echo esc_attr($CardType);?>" />						
					<?php } ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-account-number', 'pmpro_payment-account-number' ); ?>">
					<label for="AccountNumber"><?php _e('Card Number', 'paid-memberships-pro' );?></label>
					<input id="AccountNumber" name="AccountNumber" class="<?php echo pmpro_get_element_class( 'input', 'AccountNumber' ); ?>" type="text" size="30" value="<?php echo esc_attr($AccountNumber); ?>" data-encrypted-name="number" autocomplete="off" />
				</div>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-expiration', 'pmpro_payment-expiration' ); ?>">
					<label for="ExpirationMonth"><?php _e('Expiration Date', 'paid-memberships-pro' );?></label>
					<select id="ExpirationMonth" name="ExpirationMonth" class="<?php echo pmpro_get_element_class( '', 'ExpirationMonth' ); ?>">
						<option value="01" <?php if($ExpirationMonth == "01") { ?>selected="selected"<?php } ?>>01</option>
						<option value="02" <?php if($ExpirationMonth == "02") { ?>selected="selected"<?php } ?>>02</option>
						<option value="03" <?php if($ExpirationMonth == "03") { ?>selected="selected"<?php } ?>>03</option>
						<option value="04" <?php if($ExpirationMonth == "04") { ?>selected="selected"<?php } ?>>04</option>
						<option value="05" <?php if($ExpirationMonth == "05") { ?>selected="selected"<?php } ?>>05</option>
						<option value="06" <?php if($ExpirationMonth == "06") { ?>selected="selected"<?php } ?>>06</option>
						<option value="07" <?php if($ExpirationMonth == "07") { ?>selected="selected"<?php } ?>>07</option>
						<option value="08" <?php if($ExpirationMonth == "08") { ?>selected="selected"<?php } ?>>08</option>
						<option value="09" <?php if($ExpirationMonth == "09") { ?>selected="selected"<?php } ?>>09</option>
						<option value="10" <?php if($ExpirationMonth == "10") { ?>selected="selected"<?php } ?>>10</option>
						<option value="11" <?php if($ExpirationMonth == "11") { ?>selected="selected"<?php } ?>>11</option>
						<option value="12" <?php if($ExpirationMonth == "12") { ?>selected="selected"<?php } ?>>12</option>
					</select>/<select id="ExpirationYear" name="ExpirationYear" class="<?php echo pmpro_get_element_class( '', 'ExpirationYear' ); ?>">
						<?php
							$num_years = apply_filters( 'pmpro_num_expiration_years', 10 );

							for($i = date_i18n("Y"); $i < intval( date_i18n("Y") ) + intval( $num_years ); $i++)
							{
						?>
							<option value="<?php echo $i?>" <?php if($ExpirationYear == $i) { ?>selected="selected"<?php } ?>><?php echo $i?></option>
						<?php
							}
						?>
					</select>
				</div>
				<?php
					$pmpro_show_cvv = apply_filters("pmpro_show_cvv", true);
					if($pmpro_show_cvv) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-cvv', 'pmpro_payment-cvv' ); ?>">
						<label for="CVV"><?php _e('Security Code (CVC)', 'paid-memberships-pro' );?></label>
						<input id="CVV" name="CVV" type="text" size="4" value="<?php if(!empty($_REQUEST['CVV'])) { echo esc_attr($_REQUEST['CVV']); }?>" class="<?php echo pmpro_get_element_class( 'input', 'CVV' ); ?>" />  <small>(<a href="javascript:void(0);" onclick="javascript:window.open('<?php echo pmpro_https_filter(PMPRO_URL); ?>/pages/popup-cvv.html','cvv','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=600, height=475');"><?php _e("what's this?", 'paid-memberships-pro' );?></a>)</small>
					</div>
				<?php } ?>
				<?php if($pmpro_show_discount_code) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-discount-code', 'pmpro_payment-discount-code' ); ?>">
						<label for="discount_code"><?php _e('Discount Code', 'paid-memberships-pro' );?></label>
						<input class="<?php echo pmpro_get_element_class( 'input pmpro_alter_price', 'discount_code' ); ?>" id="discount_code" name="discount_code" type="text" size="10" value="<?php echo esc_attr($discount_code); ?>" />
						<input type="button" id="discount_code_button" name="discount_code_button" value="<?php _e('Apply', 'paid-memberships-pro' );?>" />
						<p id="discount_code_message" class="<?php echo pmpro_get_element_class( 'pmpro_message', 'discount_code_message' ); ?>" style="display: none;"></p>
					</div>
				<?php } ?>
			</div> <!-- end pmpro_checkout-fields -->
			<?php if(!empty($sslseal)) { ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields-rightcol pmpro_sslseal', 'pmpro_sslseal' ); ?>"><?php echo stripslashes($sslseal); ?></div>
			</div> <!-- end pmpro_checkout-fields-display-seal -->
			<?php } ?>
		</div> <!-- end pmpro_payment_information_fields -->
	<?php } ?>

	<?php do_action('pmpro_checkout_after_payment_information_fields'); ?>

	<?php if($tospage && !$pmpro_review) { ?>
		<div id="pmpro_tos_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_tos_fields' ); ?>">
			<hr />
			<h3>
				<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php echo esc_html( $tospage->post_title );?></span>
			</h3>
			<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
				<div id="pmpro_license" class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field', 'pmpro_license' ); ?>">
<?php 
	/**
	 * Hook to run formatting filters before displaying the content of your "Terms of Service" page at checkout.
	 *
	 * @since 2.4.1
	 *
	 * @param string $pmpro_tos_content The content of the post assigned as the Terms of Service page.
	 * @param string $tospage The post assigned as the Terms of Service page.
	 *
	 * @return string $pmpro_tos_content
	 */
	$pmpro_tos_content = apply_filters( 'pmpro_tos_content', do_shortcode( $tospage->post_content ), $tospage );
	echo $pmpro_tos_content;
?>
				</div> <!-- end pmpro_license -->
				<?php
					if ( isset( $_REQUEST['tos'] ) ) {
						$tos = intval( $_REQUEST['tos'] );
					} else {
						$tos = "";
					}
				?>
				<input type="checkbox" name="tos" value="1" id="tos" <?php checked( 1, $tos ); ?> /> <label class="<?php echo pmpro_get_element_class( 'pmpro_label-inline pmpro_clickable', 'tos' ); ?>" for="tos"><?php printf(__('I agree to the %s', 'paid-memberships-pro' ), $tospage->post_title);?></label>
			</div> <!-- end pmpro_checkout-fields -->
		</div> <!-- end pmpro_tos_fields -->
		<?php
		}
	?>

	<?php do_action("pmpro_checkout_after_tos_fields"); ?>

	<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_captcha', 'pmpro_captcha' ); ?>">
	<?php
		global $recaptcha, $recaptcha_publickey;
		if ( $recaptcha == 2 || ( $recaptcha == 1 && pmpro_isLevelFree( $pmpro_level ) ) ) {
			echo pmpro_recaptcha_get_html($recaptcha_publickey, NULL, true);
		}
	?>
	</div> <!-- end pmpro_captcha -->

	<?php
		do_action('pmpro_checkout_after_captcha');
	?>

	<?php do_action("pmpro_checkout_before_submit_button"); ?>

	<div class="<?php echo pmpro_get_element_class( 'pmpro_submit' ); ?>">
		<hr />
		<?php if ( $pmpro_msg ) { ?>
			<div id="pmpro_message_bottom" class="<?php echo pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ); ?>"><?php echo $pmpro_msg; ?></div>
		<?php } else { ?>
			<div id="pmpro_message_bottom" class="<?php echo pmpro_get_element_class( 'pmpro_message' ); ?>" style="display: none;"></div>
		<?php } ?>

		<?php if($pmpro_review) { ?>

			<span id="pmpro_submit_span">
				<input type="hidden" name="confirm" value="1" />
				<input type="hidden" name="token" value="<?php echo esc_attr($pmpro_paypal_token); ?>" />
				<input type="hidden" name="gateway" value="<?php echo esc_attr($gateway); ?>" />
				<input type="submit" id="pmpro_btn-submit" class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-submit-checkout', 'pmpro_btn-submit-checkout' ); ?>" value="<?php _e('Complete Payment', 'paid-memberships-pro' );?> &raquo;" />
			</span>

		<?php } else { ?>

			<?php
				$pmpro_checkout_default_submit_button = apply_filters('pmpro_checkout_default_submit_button', true);
				if($pmpro_checkout_default_submit_button)
				{
				?>
				<span id="pmpro_submit_span">
					<input type="hidden" name="submit-checkout" value="1" />
					<input type="submit"  id="pmpro_btn-submit" class="<?php echo pmpro_get_element_class(  'pmpro_btn pmpro_btn-submit-checkout', 'pmpro_btn-submit-checkout' ); ?>" value="<?php if($pmpro_requirebilling) { _e('Submit and Check Out', 'paid-memberships-pro' ); } else { _e('Submit and Confirm', 'paid-memberships-pro' );}?> &raquo;" />
				</span>
				<?php
				}
			?>

		<?php } ?>

		<span id="pmpro_processing_message" style="visibility: hidden;">
			<?php
				$processing_message = apply_filters("pmpro_processing_message", __("Processing...", 'paid-memberships-pro' ));
				echo $processing_message;
			?>
		</span>
	</div>
</form>

<?php do_action('pmpro_checkout_after_form'); ?>

</div> <!-- end pmpro_level-ID -->
