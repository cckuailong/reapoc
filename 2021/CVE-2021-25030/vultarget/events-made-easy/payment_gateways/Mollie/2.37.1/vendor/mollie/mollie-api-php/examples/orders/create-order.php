<?php
/*
 * How to create a new order in the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Generate a unique order id for this example. It is important to include this unique attribute
     * in the redirectUrl (below) so a proper return page can be shown to the customer.
     */
    $orderId = time();

    /*
     * Determine the url parts to these example files.
     */
    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);

    /*
     * Order creation parameters.
     *
     * See: https://docs.mollie.com/reference/v2/orders-api/create-order
     */
    $order = $mollie->orders->create([
        "amount" => [
          "value" => "1027.99",
          "currency" => "EUR",
        ],
        "billingAddress" => [
          "streetAndNumber" => "Keizersgracht 313",
          "postalCode" => "1016 EE",
          "city" => "Amsterdam",
          "country" => "nl",
          "givenName" => "Luke",
          "familyName" => "Skywalker",
          "email" => "luke@skywalker.com",
        ],
        "shippingAddress" => [
          "streetAndNumber" => "Keizersgracht 313",
          "postalCode" => "1016 EE",
          "city" => "Amsterdam",
          "country" => "nl",
          "givenName" => "Luke",
          "familyName" => "Skywalker",
          "email" => "luke@skywalker.com",
        ],
        "metadata" => [
          "order_id" => $orderId,
        ],
        "consumerDateOfBirth" => "1958-01-31",
        "locale" => "en_US",
        "orderNumber" => strval($orderId),
        "redirectUrl" => "{$protocol}://{$hostname}{$path}/return.php?order_id={$orderId}",
        "webhookUrl" => "{$protocol}://{$hostname}{$path}/webhook.php",
        "method" => "ideal",
        "lines" => [
            [
                "sku" => "5702016116977",
                "name" => "LEGO 42083 Bugatti Chiron",
                "productUrl" => "https://shop.lego.com/nl-NL/Bugatti-Chiron-42083",
                "imageUrl" => 'https://sh-s7-live-s.legocdn.com/is/image//LEGO/42083_alt1?$main$',
                "quantity" => 2,
                "vatRate" => "21.00",
                "unitPrice" => [
                    "currency" => "EUR",
                    "value" => "399.00",
                ],
                "totalAmount" => [
                    "currency" => "EUR",
                    "value" => "698.00",
                ],
                "discountAmount" => [
                    "currency" => "EUR",
                    "value" => "100.00",
                ],
                "vatAmount" => [
                    "currency" => "EUR",
                    "value" => "121.14",
                ],
            ],
            [
                "type" => "digital",
                "sku" => "5702015594028",
                "name" => "LEGO 42056 Porsche 911 GT3 RS",
                "productUrl" => "https://shop.lego.com/nl-NL/Porsche-911-GT3-RS-42056",
                "imageUrl" => 'https://sh-s7-live-s.legocdn.com/is/image/LEGO/42056?$PDPDefault$',
                "quantity" => 1,
                "vatRate" => "21.00",
                "unitPrice" => [
                    "currency" => "EUR",
                    "value" => "329.99",
                ],
                "totalAmount" => [
                    "currency" => "EUR",
                    "value" => "329.99",
                ],
                "vatAmount" => [
                    "currency" => "EUR",
                    "value" => "57.27",
                ],
            ],
        ],
    ]);

    /*
     * Send the customer off to complete the order payment.
     * This request should always be a GET, thus we enforce 303 http response code
     */
    header("Location: " . $order->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
