<?php
/*
 * How to refund a payment programmatically
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

    if (isset($_GET['payment_id'])) {
        /*
         * Retrieve the payment you want to refund from the API.
         */
        $paymentId = $_GET['payment_id'];
        $payment = $mollie->payments->get($paymentId);

        if ($payment->canBeRefunded() && $payment->amountRemaining->currency === 'EUR' && $payment->amountRemaining->value >= '2.00') {
            /*
             * Refund € 2,00 of the payment.
             *
             * https://docs.mollie.com/reference/v2/refunds-api/create-refund
             */
            $refund = $payment->refund([
                "amount" => [
                    "currency" => "EUR",
                    "value" => "2.00", // You must send the correct number of decimals, thus we enforce the use of strings
                ],
            ]);

            echo "{$refund->amount->currency} {$refund->amount->value} of payment {$paymentId} refunded.", PHP_EOL;
        } else {
            echo "Payment {$paymentId} can not be refunded.", PHP_EOL;
        }

        /*
         * Retrieve all refunds on a payment.
         */
        echo "<ul>";
        foreach ($payment->refunds() as $refund) {
            echo "<li>";
            echo "<strong style='font-family: monospace'>" . htmlspecialchars($refund->id) . "</strong><br />";
            echo htmlspecialchars($refund->description) . "<br />";
            echo htmlspecialchars($refund->amount->currency) . " " . htmlspecialchars($refund->amount->value) . "<br />";
            echo "Status: " . htmlspecialchars($refund->status);
            echo "</li>";
        }
        echo "</ul>";
    }

    echo "Refund payment: ";
    echo "<form method='get'><input name='payment_id' value='tr_xxx'/><input type='submit' /></form>";

    echo "<p>";
    echo '<a href="' . $protocol . '://' . $hostname . $path . '/create-payment.php">Create a payment</a><br>';
    echo '<a href="' . $protocol . '://' . $hostname . $path . '/create-ideal-payment.php">Create an iDEAL payment</a><br>';
    echo '<a href="' . $protocol . '://' . $hostname . $path . '/list-payments.php">List payments</a><br>';
    echo "</p>";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
