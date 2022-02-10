<?php
//Render the create new payment button tab

require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_paypal_buy_now_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_paypal_subscription_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_paypal_smart_checkout_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_stripe_buy_now_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_stripe_sca_buy_now_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_stripe_subscription_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_stripe_sca_subscription_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_braintree_buy_now_button.php';

do_action( 'swpm_create_new_button_process_submission' ); //Addons can use this hook to save the data after the form submit then redirect to the "edit" interface of that newly created button.
?>

<div class="swpm-grey-box">
<?php echo SwpmUtils::_( 'You can create a new payment button for your memberships using this interface.' ); ?>
</div>

<?php
if ( ! isset( $_REQUEST['swpm_button_type_selected'] ) ) {
	//Button type hasn't been selected. Show the selection option.
	?>
	<div class="postbox">
		<h3 class="hndle"><label for="title"><?php echo SwpmUtils::_( 'Select Payment Button Type' ); ?></label></h3>
		<div class="inside">
			<form action="" method="post">
			<table class="form-table" role="presentation">
			<tr>
			<td>
			<fieldset>
				<label><input type="radio" name="button_type" value="pp_buy_now" checked /> <?php SwpmUtils::e( 'PayPal Buy Now' ); ?></label>
				<br />
				<label><input type="radio" name="button_type" value="pp_subscription" /> <?php SwpmUtils::e( 'PayPal Subscription' ); ?></label>
				<br />
				<label><input type="radio" name="button_type" value="pp_smart_checkout" /> <?php SwpmUtils::e( 'PayPal Smart Checkout' ); ?></label>
				<br />
				<label><input type="radio" name="button_type" value="braintree_buy_now" /> <?php SwpmUtils::e( 'Braintree Buy Now' ); ?></label>
				<br />
				<label><input type="radio" name="button_type" value="stripe_sca_buy_now" /> <?php SwpmUtils::e( 'Stripe SCA Buy Now' ); ?></label>
				<br />
				<label><input type="radio" name="button_type" value="stripe_sca_subscription" /> <?php SwpmUtils::e( 'Stripe SCA Subscription' ); ?></label>
				<br />
				<label><input type="radio" name="button_type" value="stripe_buy_now" /> <?php SwpmUtils::e( 'Stripe Legacy Buy Now (deprecated)' ); ?></label>
				<br />
				<label><input type="radio" name="button_type" value="stripe_subscription" /> <?php SwpmUtils::e( 'Stripe Legacy Subscription (deprecated)' ); ?></label>
				<br />
			</fieldset>
			</td>
			</tr>
			</table>
	<?php
	apply_filters( 'swpm_new_button_select_button_type', '' );
	wp_nonce_field( 'swpm_admin_create_btns', 'swpm_admin_create_btns' );
	?>

				<br />
				<input type="submit" name="swpm_button_type_selected" class="button-primary" value="<?php echo SwpmUtils::_( 'Next' ); ?>" />
			</form>

		</div>
	</div><!-- end of .postbox -->
	<?php
} else {
	//Button type has been selected. Show the payment button configuration option.
	//check the nonce first
	check_admin_referer( 'swpm_admin_create_btns', 'swpm_admin_create_btns' );
	//Fire the action hook. The addons can render the payment button configuration option as appropriate.
	$button_type = sanitize_text_field( $_REQUEST['button_type'] );
	do_action( 'swpm_create_new_button_for_' . $button_type );
	//The payment addons will create the button from then redirect to the "edit" interface of that button after save.
}
