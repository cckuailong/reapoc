<?php
/*
 * Update shipment tracking information using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Update the tracking information for a shipment with ID "shp_3wmsgCJN4U" and
     * order ID "ord_8wmqcHMN4U"
     *
     * See: https://docs.mollie.com/reference/v2/shipments-api/update-shipment
     */

    $order = $mollie->orders->get('ord_8wmqcHMN4U');
    $shipment = $order->getShipment("shp_3wmsgCJN4U");

    $shipment->tracking = [
            'carrier' => 'PostNL',
            'code' => '3SKABA000000000',
            'url' => 'http://postnl.nl/tracktrace/?B=3SKABA000000000&P=1016EE&D=NL&T=C',
        ];
    $shipment = $shipment->update();

    echo 'Shipment with ID ' . $shipment->id. ' for order with ID ' . $order->id . '.';
    echo 'Tracking information updated:';
    echo 'Carrier: ' . $shipment->tracking->carrier;
    echo 'Code: ' . $shipment->tracking->code;
    echo 'Url: ' . $shipment->tracking->url;

    foreach ($shipment->lines as $line) {
        echo $line->name . ' - status: <b>' . $line->status . '</b>.';
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
