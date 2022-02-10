<?php
//Render the edit payment button tab

require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_paypal_buy_now_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_paypal_subscription_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_paypal_smart_checkout_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_stripe_buy_now_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_stripe_subscription_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_stripe_sca_buy_now_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_stripe_sca_subscription_button.php';
require_once SIMPLE_WP_MEMBERSHIP_PATH . 'views/payments/payment-gateway/admin_braintree_buy_now_button.php';

do_action( 'swpm_edit_payment_button_process_submission' ); //Addons can use this hook to save the data after the form submit.
?>

<div class="swpm-grey-box">
	<?php echo SwpmUtils::_( 'You can edit a payment button using this interface.' ); ?>
</div>

<?php
//Trigger the action hook. The addons can render the payment button edit interface using this hook
//Button type (button_type) and Button id (button_id) must be present in the REQUEST
$button_type = sanitize_text_field( $_REQUEST['button_type'] );
$button_id   = sanitize_text_field( $_REQUEST['button_id'] );
$button_id   = absint( $button_id );
do_action( 'swpm_edit_payment_button_for_' . $button_type, $button_id );

