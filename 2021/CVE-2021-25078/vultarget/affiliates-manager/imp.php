<?php

/* Connection: close and Content-Length headers are sent, so that browsers disconnect
 * early on to minimize connection time */

//The following outputs a 1x1 pixel gif. It is not really need for the functionality but nice to have.
ignore_user_abort(true);
// ob_end_clean();
$gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
$length = strlen( $gif );

header('Content-type: image/gif');
header('Connection: close');
header("Content-Length: $length");
//End of gif output.

require_once ("../../../wp-load.php");
require_once ("config.php");
require_once ("source/Tracking/RequestTracker.php");

if (isset($_GET[WPAM_PluginConfig::$RefKey])
        && get_option(WPAM_PluginConfig::$AffEnableImpressions)) {
    try {
        $requestTracker = new WPAM_Tracking_RequestTracker();
        $request_data = array_map('strip_tags', $_GET);
        $requestTracker->handleImpression($request_data);
    } catch (Exception $e) {
        wp_die("WPAM FAILED: " . $e->getMessage());
    }
}

echo $gif;

exit;
