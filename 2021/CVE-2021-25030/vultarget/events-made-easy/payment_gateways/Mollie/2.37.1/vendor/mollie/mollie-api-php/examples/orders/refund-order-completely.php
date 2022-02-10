<?php
/*
 * Refund all eligible items for an order using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Refund all eligible items for an order with ID "ord_8wmqcHMN4U".
     *
     * See: https://docs.mollie.com/reference/v2/orders-api/create-order-refund
     */

    $order = $mollie->orders->get('ord_8wmqcHMN4U');
    $refund = $order->refundAll();

    echo 'Refund ' . $refund->id . ' was created for order ' . $order->id;
    echo 'You will receive ' . $refund->amount->currency . $refund->amount->value;
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
