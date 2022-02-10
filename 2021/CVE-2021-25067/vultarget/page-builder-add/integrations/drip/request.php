<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once ULPB_PLUGIN_PATH.'/integrations/drip/Drip.php';
require_once ULPB_PLUGIN_PATH.'/integrations/drip/Dataset.php';
require_once ULPB_PLUGIN_PATH.'/integrations/drip/Response.php';
require_once ULPB_PLUGIN_PATH.'/integrations/drip/Batch.php';

$Drip = new Drip($apikey, $apiAddress);



$data = new Dataset('subscribers', [
	'email' => $email,
	'custom_fields'=> $customs
]);

$Response = $Drip->post('subscribers', $data);

if ($Response->status == 200) {
  $subscribers = $Response->subscribers;
} else {
  echo $Response->error;
  echo $Response->message;
}


?>