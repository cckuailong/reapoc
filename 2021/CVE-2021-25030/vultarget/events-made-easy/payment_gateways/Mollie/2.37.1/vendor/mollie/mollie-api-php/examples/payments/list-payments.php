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
    $payments = $mollie->payments->page();

    echo "<ul>";
    foreach ($payments as $payment) {
        echo "<li>";
        echo "<strong style='font-family: monospace'>" . htmlspecialchars($payment->id) . "</strong><br />";
        echo htmlspecialchars($payment->description) . "<br />";
        echo htmlspecialchars($payment->amount->currency) . " " . htmlspecialchars($payment->amount->value) . "<br />";

        echo "Status: " . htmlspecialchars($payment->status) . "<br />";

        if ($payment->hasRefunds()) {
            echo "Payment has been (partially) refunded.<br />";
        }

        if ($payment->hasChargebacks()) {
            echo "Payment has been charged back.<br />";
        }

        if ($payment->canBeRefunded() && $payment->amountRemaining->currency === 'EUR' && $payment->amountRemaining->value >= '2.00') {
            echo " (<a href=\"{$protocol}://{$hostname}{$path}/refund-payment.php?payment_id=" . htmlspecialchars($payment->id) . "\">refund</a>)";
        }

        echo "</li>";
    }
    echo "</ul>";

    /**
     * Get the next set of Payments if applicable
     */
    $nextPayments = $payments->next();

    if (! empty($nextPayments)) {
        echo "<ul>";
        foreach ($nextPayments as $payment) {
            echo "<li>";
            echo "<strong style='font-family: monospace'>" . htmlspecialchars($payment->id) . "</strong><br />";
            echo htmlspecialchars($payment->description) . "<br />";
            echo htmlspecialchars($payment->amount->currency) . " " . htmlspecialchars($payment->amount->value) . "<br />";

            echo "Status: " . htmlspecialchars($payment->status) . "<br />";

            if ($payment->hasRefunds()) {
                echo "Payment has been (partially) refunded.<br />";
            }

            if ($payment->hasChargebacks()) {
                echo "Payment has been charged back.<br />";
            }

            if ($payment->canBeRefunded() && $payment->amountRemaining->currency === 'EUR' && $payment->amountRemaining->value >= '2.00') {
                echo " (<a href=\"{$protocol}://{$hostname}{$path}/refund-payment.php?payment_id=" . htmlspecialchars($payment->id) . "\">refund</a>)";
            }

            echo "</li>";
        }
        echo "</ul>";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
