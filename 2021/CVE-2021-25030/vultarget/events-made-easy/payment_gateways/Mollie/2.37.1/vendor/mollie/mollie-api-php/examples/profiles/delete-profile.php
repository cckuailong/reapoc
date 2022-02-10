<?php
/*
 * Delete a profile via the Mollie API.
 */
try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize_with_oauth.php";

    /**
     * Delete a profile via the profileId
     *
     * @See https://docs.mollie.com/reference/v2/profiles-api/delete-profile
     */
    $profile = $mollie->profiles->delete("pfl_v9hTwCvYqw");
    echo "<p>Profile deleted</p>";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "<p>API call failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
