<?php
/*
 * Cancel order lines using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Cancel an order line with ID "odl_dgtxyl" for order ID "ord_8wmqcHMN4U"
     *
     * See: https://docs.mollie.com/reference/v2/orders-api/cancel-order-line
     */

    $orderId = 'ord_8wmqcHMN4U';
    $lineId = 'odl_dgtxyl';

    $order = $mollie->orders->get($orderId);
    $line = $order->lines()->get($lineId);
    if ($line && $line->isCancelable) {
        $order->cancelLines([
            'lines' => [
                [
                    'id' => $lineId,
                    'quantity' => 1, // optional parameter
                ],
            ],
        ]);

        $updatedOrder = $mollie->orders->get($orderId);

        echo 'Your order ' . $order->id . ' was updated:';
        foreach ($order->lines as $line) {
            echo $line->description . '. Status: <b>' . $line->status . '</b>.';
        }
    } else {
        echo "Unable to cancel line " . $lineId . " for your order " . $orderId . ".";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
