<?php
/*
 * How to update an order line with the Mollie API
 */

try {
    /*
     * Initialize the Mollie API library with your API key.
     *
     * See: https://www.mollie.com/dashboard/developers/api-keys
     */
    require "../initialize.php";

    /*
     * Order line parameters:
     *   name        A description of the order line, for example LEGO 4440 Forest Police Station..
     *   imageUrl    A link pointing to an image of the product sold.
     *   productUrl  A link pointing to the product page in your web shop of the product sold.
     *   sku         The SKU, EAN, ISBN or UPC of the product sold. The maximum character length is 64.
     *   metadata    Provide any data you like, for example a string or a JSON object. We will save the data alongside the order line.
     *   quantity    The number of items in the order line.
     *   unitPrice   The price of a single item including VAT in the order line.
     *   discountAmount  Any discounts applied to the order line. For example, if you have a two-for-one sale, you should pass the amount discounted as a positive amount.
     *   totalAmount  The total amount of the line, including VAT and discounts. Adding all totalAmount values together should result in the same amount as the amount top level property.
     *   vatAmount  The amount of value-added tax on the line. The totalAmount field includes VAT, so the vatAmount can be calculated with the formula totalAmount × (vatRate / (100 + vatRate)).
     *   vatRate    The VAT rate applied to the order line, for example "21.00" for 21%. The vatRate should be passed as a string and not as a float to ensure the correct number of decimals are passed.
     */


    $order = $mollie->orders->get("ord_kEn1PlbGa");
    $line = $order->lines()->get('odl_1.uh5oen');

    $line->name = "Update line name description";

    $orderWithNewLineName = $line->update();

    /*
     * Send the customer off to complete the order payment.
     * This request should always be a GET, thus we enforce 303 http response code
     */
    header("Location: " . $orderWithNewLineName->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
