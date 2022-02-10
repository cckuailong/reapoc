<?php
/*
 * How to update an order with the Mollie API
 */

try {
    /*
     * Initialize the Mollie API library with your API key.
     *
     * See: https://www.mollie.com/dashboard/developers/api-keys
     */
    require "../initialize.php";

    /*
     * Order parameters:
     *   billingAddress   The billing person and address for the order.
     *   shippingAddress  The shipping address for the order.
     *   orderNumber      The order number. For example, 16738.
     *   redirectUrl      The URL your customer will be redirected to after the payment process.
     *   webhookUrl       Set the webhook URL, where we will send order status changes to.
     */


    $order = $mollie->orders->get("ord_kEn1PlbGa");
    $order->billingAddress->organizationName = "Mollie B.V.";
    $order->billingAddress->streetAndNumber = "Keizersgracht 126";
    $order->billingAddress->city = "Amsterdam";
    $order->billingAddress->region = "Noord-Holland";
    $order->billingAddress->postalCode = "1234AB";
    $order->billingAddress->country = "NL";
    $order->billingAddress->title = "Dhr";
    $order->billingAddress->givenName = "Piet";
    $order->billingAddress->familyName = "Mondriaan";
    $order->billingAddress->email = "piet@mondriaan.com";
    $order->billingAddress->phone = "+31208202070";
    $order->update();

    /*
     * Send the customer off to complete the order payment.
     * This request should always be a GET, thus we enforce 303 http response code
     */
    header("Location: " . $order->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
