<?php
/*
 * Handle an order status change using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * After your webhook has been called with the order ID in its body, you'd like
     * to handle the order's status change. This is how you can do that.
     *
     * See: https://docs.mollie.com/reference/v2/orders-api/get-order
     */
    $order = $mollie->orders->get($_POST["id"]);
    $orderId = $order->metadata->order_id;

    /*
     * Update the order in the database.
     */
    database_write($orderId, $order->status);

    if ($order->isPaid() || $order->isAuthorized()) {
        /*
         * The order is paid or authorized
         * At this point you'd probably want to start the process of delivering the product to the customer.
         */
    } elseif ($order->isCanceled()) {
        /*
         * The order is canceled.
         */
    } elseif ($order->isExpired()) {
        /*
         * The order is expired.
         */
    } elseif ($order->isCompleted()) {
        /*
         * The order is completed.
         */
    } elseif ($order->isPending()) {
        /*
         * The order is pending.
         */
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
