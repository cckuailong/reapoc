<?php
/*
 * List orders using the Mollie API.
 */


try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * List the most recent orders
     *
     * See: https://docs.mollie.com/reference/v2/orders-api/list-orders
     */
    echo '<ul>';
    $latestOrders = $mollie->orders->page();
    printOrders($latestOrders);

    $previousOrders = $latestOrders->next();
    printOrders($previousOrders);
    echo '</ul>';
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}

function printOrders($orders)
{
    if (empty($orders)) {
        return ;
    }

    foreach ($orders as $order) {
        echo '<li><b>Order ' . htmlspecialchars($order->id) . ':</b> (' . htmlspecialchars($order->createdAt) . ')';
        echo '<br>Status: <b>' . htmlspecialchars($order->status);
        echo '<table border="1"><tr><th>Billed to</th><th>Shipped to</th><th>Total amount</th></tr>';
        echo '<tr>';
        echo '<td>' . htmlspecialchars($order->shippingAddress->givenName) . ' ' . htmlspecialchars($order->shippingAddress->familyName) . '</td>';
        echo '<td>' . htmlspecialchars($order->billingAddress->givenName) . ' ' . htmlspecialchars($order->billingAddress->familyName) . '</td>';
        echo '<td>' . htmlspecialchars($order->amount->currency) . str_replace('.', ',', htmlspecialchars($order->amount->value)) . '</td>';
        echo '</tr>';
        echo '</table>';
        echo '<a href="'. $order->getCheckoutUrl() .'" target="_blank">Click here to pay</a>';
        echo '</li>';
    }
}
