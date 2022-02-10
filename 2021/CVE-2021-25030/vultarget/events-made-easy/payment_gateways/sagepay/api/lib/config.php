<?php
//------------------------------------------------------------------------------
//
// Sage Pay Kit - Configuration File 
// 
// Lines beginning '//' are ignored. The format of each line is:
// 
// key => value or 'value'
//
// All keys must be present or an exception will be thrown.
//
// Mandatory Properties:
//   Must have a value provided (or an exception will be thrown) 
//
// Optional Properties:
// 	May or may not have a value 
//

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * Setting the PHP error reporting level to -1 essentially forces PHP to
 * report every error, and is guranteed to show every error on future
 * versions of PHP. This will insure that our handlers above are
 * notified about everything.
 */
error_reporting(-1);

/**
 * At the same time we want to disable PHP's default error display since
 * we are now using our own.
 */
ini_set('display_errors', 'Off');

// Configuration file
return array(
// Mandatory  parameter 
//   Set to any of: TEST for the Test Server and LIVE for the live environment
    'env' => 'test',
// Optional  parameter supported protocol version (3.00 is the newest and the only available)
    'protocolVersion' => 3.00,
// Mandatory. Set this value to the Vendor Name assigned to you by Sage Pay or chosen when you applied
    'vendorName' => 'protxross',
// Mandatory. Set this to indicate the currency in which you wish to trade. 
// You will need a merchant number in this currency
    'currency' => 'GBP',
// Mandatory. Usually PAYMENT. This can be DEFERRED or AUTHENTICATE if your Sage Pay 
// account supports those payment types 
// NB Ideally all DEFERRED transaction should be released within 6 days (according to card scheme rules).
// DEFERRED transactions can be ABORTed before a RELEASE if necessary
    'txType' => 'PAYMENT',
// Mandatory
// Qualified Domain Name of your server. 
// This should start http:// or https:// and should be the name by which our servers can call back to yours 
// i.e. it MUST be resolvable externally, and have access granted to the Sage Pay servers 
// examples would be https://www.mysite.com or http://212.111.32.22/ 
    'siteFqdns' =>
    array(
        'live' => '',
        'test' => 'http://192.168.13.11/VspPHPKit/',
    ),
// Optional setting. If you are a Sage Pay Partner and wish to flag the transactions 
// with your unique partner id, it should be set here
    'partnerId' => '',
// Optional setting to set vendor data 
    'vendorData' => '',
// Optional
// 0 = If AVS/CV2 enabled then check them.  If rules apply, use rules (default).
// 1 = Force AVS/CV2 checks even if not enabled for the account. If rules apply, use rules.
// 2 = Force NO AVS/CV2 checks even if enabled on account.
// 3 = Force AVS/CV2 checks even if not enabled for the account but DON'T apply any rules.
    'applyAvsCv2' => 0,
// Optional	
// 0 = If 3D-Secure checks are possible and rules allow, perform the checks and apply the authorisation rules. (default)
// 1 = Force 3D-Secure checks for this transaction if possible and apply rules for authorisation.
// 2 = Do not perform 3D-Secure checks for this transaction and always authorise.
// 3 = Force 3D-Secure checks for this transaction if possible but ALWAYS obtain an auth code, irrespective of rule base.	
    'apply3dSecure' => 0,
// Optional property. (Server & Form protocols only)
// For charities registered for Gift Aid, set to 1 to display the Gift Aid check 
// box on the payment pages, or else 0
    'allowGiftAid' => 1,
// Optional	
// Use this to send surcharge xml and override the default values set for your account.
// See the protocol docs for further explanation on using the surcharge xml. 	
    'surcharges' =>
    array(
        array(
            'paymentType' => 'MC',
            'percentage' => 5,
        ),
        array(
            'paymentType' => 'VISA',
            'fixed' => 3.5,
        ),
    ),
// Optional setting. if you are a vendor that has a merchant category code of 6012, then you can fill in extra details required for authorisation for Visa transactions
    'collectRecipientDetails' => false,
//  Mandatory property, set this value to the Encryption password assigned to you by Sage Pay 	
    'formPassword' =>
    array(
        'test' => 'TPjs72eMz5qBnaTa',
        'live' => '',
    ),
// Mandatory parameters form notifications URLs appended to siteFQDN value
    'formSuccessUrl' => 'form/success',
    'formFailureUrl' => 'form/failure',
//Optional setting. Set to tell the Sage Pay System which merchant account to use. If omitted,
//	the system will use E, then M, then C by default
//	E = Use the e-commerce merchant account (default)
//	M = Use the mail order/telephone order account (if present)
//	C = Use the continuous authority merchant account (if present)
    'accountType' => 'E',
// Mandatory Server notification URLs
    'serverNotificationUrl' => '',
// Optional
//  0 = Do not send either customer or vendor e-mails,
//  1 = Send customer and vendor e-mails if address(es) are provided(DEFAULT).
//  2 = Send Vendor Email but not Customer Email. If you do not supply this field, 1 is assumed and e-mails are sent if addresses are provided.
    'sendEmail' => 0,
// Optional
//  You can specify any custom message to send to your customers in their confirmation e-mail here
//  The field can contain HTML if you wish, and be different for each order.  This field is optional	
    'emailMessage' => '',
// Optional setting. Set this to the mail address which will receive order confirmations and failures    
	'vendorEmail' => '',
// Optional parameter, this value will be used to set the BillingAgreement field in the registration POST
// A default is value of 0 is used if this parameter is not included in this properties file
    'billingAgreement' => 1,
// Mandatory parameter, salt used for hashing the password in the local database	
// i.e. value: q8W#e1_    
    'customerPasswordSalt' => '',
// Optional parameter, set this to true to use colon delimited format for the basket instead of XML
// Note: The 'Trips' details on the 'Extra Information' page will not be displayed if this flag is set to true. 
    'basketAsXmlDisable' => false,
// Set this to true if you want to store all logs in debug.log file 	
    'logError' => true,
// Optional	
//  The language the customer sees the payment pages in is determined by the code sent here. If this is NULL then the language default of the shoppers browser will be used. 
//  If the language is not supported then the language supported in the templates will be used
//  Currently supported languages in the Default templates are :
//  French, German, Spanish, Portuguese, Dutch and English
    'language' => null,
// Optional parameter reference to the website this transaction came from. This field is useful if transactions can originate from more than one website.  Supplying this information will enable reporting to be performed by website.	
    'website' => '',
    'requestTimeout' => 30,
    'caCertPath' => '',
);
