<?php
/*
 * How to create a new customer in the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /**
     * Customer creation parameters.
     *
     * @See https://docs.mollie.com/reference/v2/customers-api/create-customer
     */
    $customer = $mollie->customers->create([
        "name" => "Luke Skywalker",
        "email" => "luke@example.org",
        "metadata" => [
            "isJedi" => true,
        ],
    ]);
    echo "<p>New customer created " . htmlspecialchars($customer->id) . " (" . htmlspecialchars($customer->name) . ").</p>";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
