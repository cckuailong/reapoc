<?php
/*
 * Updating an existing profile via the Mollie API.
 */
try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize_with_oauth.php";

    /*
     * Retrieve an existing profile by his profileId
     */
    $profile = $mollie->profiles->get("pfl_eA4MSz7Bvy");

    /**
     * Profile fields that can be updated.
     *
     * @See https://docs.mollie.com/reference/v2/profiles-api/update-profile
     */
    $profile->name = "Mollie B.V.";
    $profile->website = 'www.mollie.com';
    $profile->email = 'info@mollie.com';
    $profile->phone = '0612345670';
    $profile->categoryCode = 5399;
    $profile->update();
    echo "<p>Profile updated: " . htmlspecialchars($profile->name) . "</p>";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "<p>API call failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
