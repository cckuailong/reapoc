<?php
/*

	Encrypted Buy Now Button Example

*/

// include class and init object
require 'paypal/Paypal.php';
$p = new Paypal;

// the paypal or paypal sandbox url
$p->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';

// the timeout in seconds before the button form is submitted to paypal
// this needs the included addevent javascript function
// 0 = no delay
// false = disable auto submission
$p->timeout = false;

// the button label
// false to disable button (if you want to rely only on the javascript auto-submission) not recommended
$p->button = 'Buy Now';

// use encryption (strongly recommended!)
$p->encrypt = true;
$p->private_key = "/path/to/certs/priv.pem";  		// your private key
$p->public_cert = "/path/to/certs/pub.pem";  		// your public certificate
$p->paypal_cert = "/path/to/certs/paypal_cert.pem";	// paypal's public cert
$p->cert_id = 'QPZHQYXS5NQFQ';						// the id of the certificate you wish to use


// the actual button parameters
// https://www.paypal.com/IntegrationCenter/ic_std-variable-reference.html
$p->add_field('charset','utf-8');
$p->add_field('currency_code','USD');
$p->add_field('business', 'your_paypal_email@example.com');
$p->add_field('return', 'http://example.com/thanks');
$p->add_field('cancel_return', 'http://example.com/cancel');
$p->add_field('notify_url', 'http://example.com/ipn.php');
$p->add_field('item_name', 'PHP Class Example');
$p->add_field('item_number', '1337');
$p->add_field('amount', '1000');


// output the button
echo $p->get_button();

?>