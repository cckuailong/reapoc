<?php
/*
 * How to create a regular subscription.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Determine the url parts to these example files.
     */
    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);

    /*
     * Retrieve the last created customer for this example.
     * If no customers are created yet, run create-customer example.
     */
    $customer = $mollie->customers->page(null, 1)[0];

    /*
     * Generate a unique subscription id for this example. It is important to include this unique attribute
     * in the webhookUrl (below) so new payments can be associated with this subscription.
     */
    $subscriptionId = time();

    /**
     * Customer Subscription creation parameters.
     *
     * @See: https://docs.mollie.com/reference/v2/subscriptions-api/create-subscription
     */
    $subscription = $customer->createSubscription([
        "amount" => [
            "value" => "10.00", // You must send the correct number of decimals, thus we enforce the use of strings
            "currency" => "EUR",
        ],
        "times" => 12,
        "interval" => "1 month",
        "description" => "Subscription #{$subscriptionId}",
        "webhookUrl" => "{$protocol}://{$hostname}{$path}/webhook.php",
        "metadata" => [
            "subscription_id" => $subscriptionId,
        ],
    ]);

    /*
     * The subscription will be either pending or active depending on whether the customer has
     * a pending or valid mandate. If the customer has no mandates an error is returned. You
     * should then set up a "first payment" for the customer.
     */
    echo "<p>The subscription status is '" . htmlspecialchars($subscription->status) . "'.</p>\n";
    echo "<p>";
    echo '<a href="' . $protocol . '://' . $hostname . $path . '/17-cancel-subscription.php?subscription_id=' . $subscription->id . '">18-cancel-subscription</a><br>';
    echo "</p>";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
