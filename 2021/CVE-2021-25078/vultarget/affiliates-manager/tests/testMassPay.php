<?php

//require_once "../boot-strap.php";

require_once "../source/PayPal/Service.php";

$service = new WPAM_PayPal_Service(
	"https://api-3t.sandbox.paypal.com/nvp",
	"castor_1294111788_biz_api1.gmail.com",
	"1294111809",
	"AsaT6blQoEKAqTh8yzrIjhorxcKDAOtY0N2oc-MOoTj-YCr7wxRiRVtJ"
);
$request = new WPAM_PayPal_MassPayRequest(WPAM_PayPal_MassPayRequest::RECEIVERTYPE_EMAIL_ADDRESS, 'USD', 'Affiliate payout');
$request->addRecipient('castor_1294112124_per@gmail.com', '20.23', 1337);
$request->addRecipient('castor_1294112097_per@gmail.com', '19.99', 1338);


$response = $service->doMassPay($request);
print_r($response);