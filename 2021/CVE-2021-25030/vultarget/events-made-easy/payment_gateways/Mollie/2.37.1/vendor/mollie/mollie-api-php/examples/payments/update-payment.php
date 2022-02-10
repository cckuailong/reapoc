<?php
/*
 * How to prepare a new payment with the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key.
     *
     * See: https://www.mollie.com/dashboard/developers/api-keys
     */
    require "../initialize.php";

    /*
     * Payment parameters:
     *   description   Description of the payment.
     *   redirectUrl   Redirect location. The customer will be redirected there after the payment.
     *   webhookUrl    Webhook location, used to report when the payment changes state.
     *   metadata      Custom metadata that is stored with the payment.
     */


    $payment = $mollie->payments->get("tr_7UhSN1zuXS");
    $newOrderId = 98765;
    $payment->description = "Order #".$newOrderId;
    $payment->redirectUrl = "https://example.org/webshop/order/98765/";
    $payment->webhookUrl = "https://example.org/webshop/payments/webhook/";
    $payment->metadata = ["order_id" => $newOrderId];

    $payment = $payment->update();
    /*
     * In this example we store the order with its payment status in a database.
     */
    database_write($newOrderId, $payment->status);

    /*
     * Send the customer off to complete the payment.
     * This request should always be a GET, thus we enforce 303 http response code
     */
    header("Location: " . $payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
