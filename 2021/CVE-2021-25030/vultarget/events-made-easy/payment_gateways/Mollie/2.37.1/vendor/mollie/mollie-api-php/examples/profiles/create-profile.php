<?php
/*
 * Create a profile via the Mollie API.
 */
try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize_with_oauth.php";

    /**
     * Create the profile
     *
     * @See https://docs.mollie.com/reference/v2/profiles-api/create-profile
     */
    $profile = $mollie->profiles->create([
        "name" => "My website name",
        "website" => "https://www.mywebsite.com",
        "email" => "info@mywebsite.com",
        "phone" => "+31208202070",
        "categoryCode" => 5399,
        "mode" => "live",
    ]);
    echo "<p>Profile created: " . htmlspecialchars($profile->name) . "</p>";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "<p>API call failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
