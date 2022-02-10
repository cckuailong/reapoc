<?php
/*
 * How to create a new customer in the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Retrieve the last created customer for this example.
     * If no customers are created yet, run create-customer example.
     */
    $customer = $mollie->customers->page(null, 1)[0];

    /*
     * Generate a unique order id for this example. It is important to include this unique attribute
     * in the redirectUrl (below) so a proper return page can be shown to the customer.
     */
    $orderId = time();

    /*
     * Determine the url parts to these example files.
     */
    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
    $hostname = $_SERVER['HTTP_HOST'];

    /**
     * Linking customers to payments has a few benefits
     *
     * @see https://docs.mollie.com/reference/v2/customers-api/create-customer-payment
     */
    $payment = $customer->createPayment([
        "amount" => [
            "value" => "10.00", // You must send the correct number of decimals, thus we enforce the use of strings
            "currency" => "EUR",
        ],
        "description" => "Order #{$orderId}",
        "redirectUrl" => "{$protocol}://{$hostname}/payments/return.php?order_id={$orderId}",
        "webhookUrl" => "{$protocol}://{$hostname}/payments/webhook.php",
        "metadata" => [
            "order_id" => $orderId,
        ],
    ]);

    /*
     * In this example we store the order with its payment status in a database.
     */
    database_write($orderId, $payment->status);

    /*
     * Send the customer off to complete the payment.
     * This request should always be a GET, thus we enforce 303 http response code
     */
    header("Location: " . $payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
