<?php
/*
 * How to cancel a subscription.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Retrieve the last created customer for this example.
     * If no customers are created yet, run the create-customer example.
     */
    $customer = $mollie->customers->page(null, 1)[0];

    /*
     * The subscription ID, starting with sub_
     */
    $subscriptionId = isset($_GET['subscription_id']) ? $_GET['subscription_id'] : '';

    /*
     * Customer Subscription deletion parameters.
     *
     * See: https://www.mollie.com/nl/docs/reference/subscriptions/delete
     */
    $canceledSubscription = $customer->cancelSubscription($subscriptionId);

    /*
     * The subscription status should now be canceled
     */
    echo "<p>The subscription status is now: '" . htmlspecialchars($canceledSubscription->status) . "'.</p>\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
