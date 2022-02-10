<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * @file
 * Provides constants for most other Commerce modules
 */

// Define variables to indicate the transaction mode.
define('SAGEPAY_ENV_LIVE', 'live');
define('SAGEPAY_ENV_TEST', 'test');

// Define transaction type
define('SAGEPAY_TXN_AUTHENTICATE', 'AUTHENTICATE');
define('SAGEPAY_TXN_AUTHORISE', 'AUTHORISE');
define('SAGEPAY_TXN_CANCEL', 'CANCEL');
define('SAGEPAY_TXN_DEFERRED', 'DEFERRED');
define('SAGEPAY_TXN_PAYMENT', 'PAYMENT');
define('SAGEPAY_TXN_VOID', 'VOID');
define('SAGEPAY_TXN_REFUND', 'REFUND');
define('SAGEPAY_TXN_REPEAT', 'REPEAT');
define('SAGEPAY_TXN_REPEATDEFERRED', 'REPEATDEFERRED');
define('SAGEPAY_TXN_ABORT', 'ABORT');
define('SAGEPAY_TXN_RELEASE', 'RELEASE');
define('SAGEPAY_TXN_COMPLETE', 'COMPLETE');

// Define Server URLs for all integration methods.
define('SAGEPAY_SERVER_SHOWPOST', 'https://test.sagepay.com/showpost/showpost.asp');
define('SAGEPAY_SERVER_SIMULATOR', 'https://test.sagepay.com/Simulator/VSPDirectGateway.asp');

define('SAGEPAY_DIRECT_SERVER_LIVE', 'https://live.sagepay.com/gateway/service/vspdirect-register.vsp');
define('SAGEPAY_DIRECT_SERVER_TEST', 'https://test.sagepay.com/gateway/service/vspdirect-register.vsp');

define('SAGEPAY_DIRECT_SERVER_3D_SECURE_CALLBACK_LIVE', 'https://live.sagepay.com/gateway/service/direct3dcallback.vsp');
define('SAGEPAY_DIRECT_SERVER_3D_SECURE_CALLBACK_TEST', 'https://test.sagepay.com/gateway/service/direct3dcallback.vsp');

define('SAGEPAY_FORM_SERVER_LIVE', 'https://live.sagepay.com/gateway/service/vspform-register.vsp');
define('SAGEPAY_FORM_SERVER_TEST', 'https://test.sagepay.com/gateway/service/vspform-register.vsp');

define('SAGEPAY_SERVER_SERVER_LIVE', 'https://live.sagepay.com/gateway/service/vspserver-register.vsp');
define('SAGEPAY_SERVER_SERVER_TEST', 'https://test.sagepay.com/gateway/service/vspserver-register.vsp');

define('SAGEPAY_SHARED_REPEAT_TRANSACTION_LIVE', 'https://live.sagepay.com/gateway/service/repeat.vsp');
define('SAGEPAY_SHARED_REPEAT_TRANSACTION_TEST', 'https://test.sagepay.com/gateway/service/repeat.vsp');

define('SAGEPAY_SHARED_ABORT_TRANSACTION_LIVE', 'https://live.sagepay.com/gateway/service/abort.vsp');
define('SAGEPAY_SHARED_ABORT_TRANSACTION_TEST', 'https://test.sagepay.com/gateway/service/abort.vsp');

define('SAGEPAY_SHARED_RELEASE_TRANSACTION_LIVE', 'https://live.sagepay.com/gateway/service/release.vsp');
define('SAGEPAY_SHARED_RELEASE_TRANSACTION_TEST', 'https://test.sagepay.com/gateway/service/release.vsp');

define('SAGEPAY_SHARED_REFUND_TRANSACTION_LIVE', 'https://live.sagepay.com/gateway/service/refund.vsp');
define('SAGEPAY_SHARED_REFUND_TRANSACTION_TEST', 'https://test.sagepay.com/gateway/service/refund.vsp');

define('SAGEPAY_SHARED_VOID_TRANSACTION_LIVE', 'https://live.sagepay.com/gateway/service/void.vsp');
define('SAGEPAY_SHARED_VOID_TRANSACTION_TEST', 'https://test.sagepay.com/gateway/service/void.vsp');

define('SAGEPAY_SHARED_AUTHORISE_TRANSACTION_LIVE', 'https://live.sagepay.com/gateway/service/authorise.vsp');
define('SAGEPAY_SHARED_AUTHORISE_TRANSACTION_TEST', 'https://test.sagepay.com/gateway/service/authorise.vsp');

define('SAGEPAY_SHARED_CANCEL_TRANSACTION_LIVE', 'https://live.sagepay.com/gateway/service/cancel.vsp');
define('SAGEPAY_SHARED_CANCEL_TRANSACTION_TEST', 'https://test.sagepay.com/gateway/service/cancel.vsp');

define('SAGEPAY_SERVER_TOKEN_REGISTER_LIVE', 'https://live.sagepay.com/gateway/service/token.vsp');
define('SAGEPAY_SERVER_TOKEN_REGISTER_TEST', 'https://test.sagepay.com/gateway/service/token.vsp');

define('SAGEPAY_DIRECT_TOKEN_REGISTER_LIVE', 'https://live.sagepay.com/gateway/service/directtoken.vsp');
define('SAGEPAY_DIRECT_TOKEN_REGISTER_TEST', 'https://test.sagepay.com/gateway/service/directtoken.vsp');

define('SAGEPAY_TOKEN_REMOVE_LIVE', 'https://live.sagepay.com/gateway/service/removetoken.vsp');
define('SAGEPAY_TOKEN_REMOVE_TEST', 'https://test.sagepay.com/gateway/service/removetoken.vsp');

define('SAGEPAY_PAYPAL_COMPLETION_TEST', 'https://test.sagepay.com/gateway/service/complete.vsp');
define('SAGEPAY_PAYPAL_COMPLETION_LIVE', 'https://live.sagepay.com/gateway/service/complete.vsp');

// Define Settings for integration method.
define('SAGEPAY_FORM', 'form');
define('SAGEPAY_SERVER', 'server');
define('SAGEPAY_DIRECT', 'direct');
define('SAGEPAY_TOKEN', 'token');
define('SAGEPAY_PAYPAL', 'paypal');


// Define remote status codes for SagePay.
define('SAGEPAY_REMOTE_STATUS_DEFERRED', 'DEFERRED');
define('SAGEPAY_REMOTE_STATUS_REPEAT_DEFERRED', 'REPEAT_DEFERRED');
define('SAGEPAY_REMOTE_STATUS_AUTHENTICATED', 'AUTHENTICATED');
define('SAGEPAY_REMOTE_STATUS_REGISTERED', 'REGISTERED');
define('SAGEPAY_REMOTE_STATUS_FAIL', 'FAIL');
define('SAGEPAY_REMOTE_STATUS_INVALID', 'INVALID');
define('SAGEPAY_REMOTE_STATUS_STARTED', 'STARTED');
define('SAGEPAY_REMOTE_STATUS_OK', 'OK');
define('SAGEPAY_REMOTE_STATUS_UNKNOWN', 'UNKNOWN');
define('SAGEPAY_REMOTE_STATUS_PAYMENT', 'PAYMENT');
define('SAGEPAY_REMOTE_STATUS_REFUNDED', 'REFUNDED');
define('SAGEPAY_REMOTE_STATUS_VOIDED', 'VOIDED');
define('SAGEPAY_REMOTE_STATUS_CANCELLED', 'CANCELLED');
define('SAGEPAY_REMOTE_STATUS_3D_SECURE', '3DSECURE');
define('SAGEPAY_REMOTE_STATUS_PAYPAL_REDIRECT', 'PPREDIRECT');
define('SAGEPAY_REMOTE_STATUS_PAYPAL_OK', 'PAYPALOK');
define('SAGEPAY_REMOTE_STATUS_NOTAUTHED', 'NOTAUTHED');
define('SAGEPAY_REMOTE_STATUS_MALFORMED', 'MALFORMED');
define('SAGEPAY_REMOTE_STATUS_ERROR', 'ERROR');
define('SAGEPAY_REMOTE_STATUS_ABORTED', 'ABORTED');

// Define account type
define('SAGEPAY_ACCOUNT_ECOMMERCE', 'E');
define('SAGEPAY_ACCOUNT_CONTINUOUS', 'C');
define('SAGEPAY_ACCOUNT_MAIL', 'M');

// Define Server profile
define('SAGEPAY_SERVER_PROFILE_NORMAL', 'NORMAL');
define('SAGEPAY_SERVER_PROFILE_LOW', 'LOW');

