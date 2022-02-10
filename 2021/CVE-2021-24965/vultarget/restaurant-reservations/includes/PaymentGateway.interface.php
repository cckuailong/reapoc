<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !interface_exists( 'rtbPaymentGateway' ) ) {
/**
 * Base interface to implement a Payment Gateway for Restaurant Reservations
 *
 * This class enforces the Payment Gateway base settings and their processing
 * methods. This class should be implemented for each type of
 * Payment Gateway. So, there would be a rtbPaymentStripe class or a
 * rtbPaymentPayPal class.
 *
 * @since 2.3.0
 */
interface rtbPaymentGateway {

  /**
   * Register the gateway.
   * 
   * $gateway_list['stripe'] => [
   *     'label'    => __( 'Stripe', 'restaurant-reservations' ),
   *     'instance' => $this
   * ];
   * 
   * @param  array  $gateway_list
   * @return array  $gateway_list
   */
  public static function register_gateway( array $gateway_list );

  /**
   * Print the payment form. The booking has been made and is on payment_pending
   * status. Display the payment form/button here
   * 
   * @param  rtbBooking $booking booking for which the payment is required
   * @return void                        Print the HTML
   */
  public function print_payment_form( $booking );

}

}