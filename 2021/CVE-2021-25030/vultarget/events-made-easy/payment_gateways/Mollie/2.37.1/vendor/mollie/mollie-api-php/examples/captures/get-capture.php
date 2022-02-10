<?php
/*
 * Retrieve a payment capture using the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Retrieve a capture with ID 'cpt_4qqhO89gsT' for payment with
     * ID 'tr_WDqYK6vllg'.
     *
     * See: https://docs.mollie.com/reference/v2/captures-api/get-capture
     */

    $payment = $mollie->payments->get('tr_WDqYK6vllg');
    $capture = $payment->getCapture('cpt_4qqhO89gsT');

    $amount = $capture->amount->currency . ' ' . $capture->amount->value;

    echo 'Captured ' . $amount;
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
