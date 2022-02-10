<?php
/*
 * Example 10 -  Using OAuth access token to prepare a new payment.
 */
try {
    /*
     * Initialize the Mollie API library with your OAuth access token.
     */
    require "../initialize_with_oauth.php";
    /*
     * Generate a unique order id for this example. It is important to include this unique attribute
     * in the redirectUrl (below) so a proper return page can be shown to the customer.
     */
    $orderId = time();
    /*
     * Determine the url parts to these example files.
     */
    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
    $hostname = $_SERVER['HTTP_HOST'] ? : "my.app";
    $path = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);
    /*
     * Since unlike an API key the OAuth access token does NOT belong to a profile, we need to retrieve a profile
     * so we can specify the profileId-parameter when creating a payment below.
     */
    $profiles = $mollie->profiles->page();
    $profile = reset($profiles);

    /**
     * Paramaters for creating a payment via oAuth
     *
     * @See https://docs.mollie.com/reference/v2/payments-api/create-payment
     */
    $payment = $mollie->payments->create([
        "amount" => [
            "value" => "10.00",
            "currency" => "EUR",
        ],
        "description" => "My first API payment",
        "redirectUrl" => "{$protocol}://{$hostname}{$path}/return.php?order_id={$orderId}",
        "webhookUrl" => "{$protocol}://{$hostname}{$path}/webhook.php",
        "metadata" => [
            "order_id" => $orderId,
        ],
        "profileId" => $profile->id, // This is specifically necessary for payment resources via OAuth access.
    ]);

    /*
     * In this example we store the order with its payment status in a database.
     */
    database_write($orderId, $payment->status);

    /*
     * Send the customer off to complete the payment.
     * This request should always be a GET, thus we enforce 303 http response code
     */
    if (PHP_SAPI === "cli") {
        echo "Redirect to: " . $payment->getCheckoutUrl() . PHP_EOL;

        return;
    }
    header("Location: " . $payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
