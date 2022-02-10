<?php
/*
 * How to list your payments.
 */

try {
    /*
     * Initialize the Mollie API library with your API key.
     *
     * See: https://www.mollie.com/dashboard/developers/api-keys
     */
    require "../initialize.php";

    /*
     * Determine the url parts to these example files.
     */
    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);

    /*
     * Get the all payments for this API key ordered by newest.
     */
    $paymentLinks = $mollie->paymentLinks->page();

    echo "<ul>";
    foreach ($paymentLinks as $paymentLink) {
        echo "<li>";
        echo "<strong style='font-family: monospace'>" . htmlspecialchars($paymentLink->id) . "</strong><br />";
        echo htmlspecialchars($paymentLink->description) . "<br />";
        echo htmlspecialchars($paymentLink->amount->currency) . " " . htmlspecialchars($paymentLink->amount->value) . "<br />";
        echo "Link: " . htmlspecialchars($paymentLink->getPaymentLinkUrl()) . "<br />";

        echo "</li>";
    }
    echo "</ul>";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
