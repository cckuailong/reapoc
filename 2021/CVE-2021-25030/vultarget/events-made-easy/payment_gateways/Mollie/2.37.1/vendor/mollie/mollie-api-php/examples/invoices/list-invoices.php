<?php
/*
 * List the Mollie invoices.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize_with_oauth.php";

    /*
    * Get all the activated methods for this API key.
    */
    $invoices = $mollie->invoices->all();
    foreach ($invoices as $invoice) {
        echo '<li><b>Invoice ' . htmlspecialchars($invoice->reference) . ':</b> (' . htmlspecialchars($invoice->issuedAt) . ')';
        echo '<br>Status: <b>' . $invoice->status;
        echo '<table border="1"><tr><th>Period</th><th>Description</th><th>Count</th><th>VAT Percentage</th><th>Amount</th></tr>';
        foreach ($invoice->lines as $line) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($line->period) . '</td>';
            echo '<td>' . htmlspecialchars($line->description) . '</td>';
            echo '<td align="right">' . htmlspecialchars($line->count) . '</td>';
            echo '<td align="right">' . htmlspecialchars($line->vatPercentage) . '</td>';
            echo '<td align="right">' . htmlspecialchars($line->amount->currency . " " . $line->amount->value) . '</td>';
            echo '</tr>';
        }
        echo '<tr><th colspan="5" align="right">Gross Total</th><th align="right">' . htmlspecialchars($invoice->grossAmount->value . " " . $invoice->grossAmount->currency) . '</th></tr>';
        echo '</table>';
        echo '<a href="'. $invoice->_links->pdf->href .'" target="_blank">Click here to open PDF</a>';
        echo '</li>';
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
