<?php
/*
 * Example 32 - Create a shipment for part of an order using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "./initialize.php";

    /*
     * Create a shipment for only two lines of the order with ID "ord_8wmqcHMN4U".
     *
     * See: https://docs.mollie.com/reference/v2/shipments-api/create-shipment
     */

    $order = $mollie->orders->get('ord_8wmqcHMN4U');
    $lineId1 = $order->lines()[0]->id;
    $lineId2 = $order->lines()[1]->id;
    $shipment = $order->createShipment(
        [
            'lines' => [
                [
                    'id' => $lineId1,
                    // assume all is shipped if no quantity is specified
                ],
                [
                    'id' => $lineId2,
                    'quantity' => 1, // you can set the quantity if not all is shipped at once
                ],
            ],
        ]
    );

    echo 'A shipment with ID ' . $shipment->id. ' has been created for your order with ID ' . $order->id . '.';
    foreach ($shipment->lines as $line) {
        echo $line->name . '- status: <b>' . $line->status . '</b>.';
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
