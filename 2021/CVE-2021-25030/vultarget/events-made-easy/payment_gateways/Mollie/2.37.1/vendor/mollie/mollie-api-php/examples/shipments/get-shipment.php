<?php
/*
 * Retrieve a shipment using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Retrieve a shipment with ID "shp_3wmsgCJN4U" for the order with ID "ord_8wmqcHMN4U".
     *
     * See: https://docs.mollie.com/reference/v2/shipments-api/get-shipment
     */

    $order = $mollie->orders->get('ord_8wmqcHMN4U');
    $shipment = $order->getShipment("shp_3wmsgCJN4U");

    echo 'Shipment with ID ' . $shipment->id. ' for order with ID ' . $order->id . '.';
    foreach ($shipment->lines as $line) {
        echo $line->name . ' - status: <b>' . $line->status . '</b>.';
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
