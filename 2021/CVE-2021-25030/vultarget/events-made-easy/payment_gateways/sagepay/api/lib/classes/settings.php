<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * Sagepay Settings class that helps wrapping config functionality
 * (read-only)
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepaySettings
{

    /**
     * Instance of SagepaySettings
     *
     * @var SagepaySettings 
     */
    private static $_instance = null;
    
    /**
     * <b>Mandatory.</b><br>
     * Specify the correct server environment to connect(test or live).<br>
     * Default: <i>test</i>
     *
     * @var string
     */
    private $_env = SAGEPAY_ENV_TEST;

    /**
     * <b>Mandatory.</b><br>
     * SagePay Protocol Version used for payment<br>
     * Default: 3.00
     *
     * @var float
     */
    private $_protocolVersion = 3.00;

    /**
     * <b>Mandatory.</b>
     * Vendor name provided by Sagepay service
     *
     * @var string
     */
    private $_vendorName = '';

    /**
     * Vendor email
     * Set this to the mail address which will receive order confirmations and failures
     *
     * @var string
     */
    private $_vendorEmail = '';

    /**
     * Use this to pass data you wish to be displayed against the transaction in MySagePay.
     *
     * @var string
     */
    private $_vendorData = '';

    /**
     * <b>Mandatory.</b>
     * Set this to indicate the currency in which you wish to trade.
     * Should be ISO 4217 Valid
     *
     * @link http://en.wikipedia.org/wiki/ISO_4217
     * @var string
     */
    private $_currency = 'GBP';

    /**
     * <b>Mandatory.</b>
     * Usually PAYMENT. This can be DEFERRED or AUTHENTICATE
     * if your Sage Pay account supports those payment types
     *
     * @var string
     */
    private $_txType = SAGEPAY_TXN_PAYMENT;

    /**
     * The URL of a vendor's server can be overwritten
     * which will send a custom notificationURL and failure/successURL to the Sage Pay gateway
     *
     * @var string[]
     */
    private $_siteFqdn = array(
        'test' => '',
        'live' => '',
    );

    /**
     * If you are a Sage Pay Partner and wish to flag the transactions with your unique partner id,
     * it should be set here
     *
     * @var string
     */
    private $_partnerId = '';

    /**
     * Apply Address Verification Status / Card Verification Value
     * 0 = If AVS/CV2 enabled then check them.  If rules apply, use rules (default).
     * 1 = Force AVS/CV2 checks even if not enabled for the account. If rules apply, use rules.
     * 2 = Force NO AVS/CV2 checks even if enabled on account.
     * 3 = Force AVS/CV2 checks even if not enabled for the account but DON'T apply any rules.
     *
     * @var int
     */
    private $_applyAvsCv2 = 0;

    /**
     * Apply 3D-Secure
     * 0 = If 3D-Secure checks are possible and rules allow, perform the checks and apply the authorisation rules. (default)
     * 1 = Force 3D-Secure checks for this transaction if possible and apply rules for authorisation.
     * 2 = Do not perform 3D-Secure checks for this transaction and always authorise.
     * 3 = Force 3D-Secure checks for this transaction if possible but ALWAYS obtain an auth code, irrespective of rule base.
     *
     * @var int
     */
    private $_apply3dSecure = 0;

    /**
     * For charities registered for Gift Aid,
     * set to 1 to display the Gift Aid check
     * box on the payment pages, or else 0
     * (Server & Form protocols only)
     *
     * @var int
     */
    private $_allowGiftAid = 0;

    /**
     * Use this to send surcharge xml and override the default values set for your account.
     *
     * @var array
     */
    private $_surcharges = array();

    /**
     * If you are a dealing with financial transfers then offer the option to collect
     * details about the recipient
     *
     * @var boolean
     */
    private $_collectRecipientDetails = false;

    // FORM Protocol only

    /**
     * Set this value to the AES encryption password assigned to you by Sage Pay
     *
     * @var array
     */
    private $_formPassword = array(
        'test' => '0123456789abcdef',
        'live' => '0123456789abcdef',
    );

    /**
     * Set this value for success page redirect for FORM Protocol
     *
     * @var string
     */
    private $_formSuccessUrl = '';

    /**
     * Set this value for failure page redirect for FORM Protocol
     *
     * @var string
     */
    private $_formFailureUrl = '';

    /**
     * Send email
     * 0 = Do not send either customer or vendor e-mails,
     * 1 = Send customer and vendor e-mails if address(es) are provided(DEFAULT).
     * 2 = Send Vendor Email but not Customer Email. If you do not supply this field, 1 is assumed and e-mails are sent if addresses are provided.
     *
     * @var int
     */
    private $_sendEmail = 1;

    /**
     * Contents of email message.
     * You can specify any custom message to send to your customers in their confirmation e-mail here
     * The field can contain HTML if you wish, and be different for each order.
     *
     * @var string
     */
    private $_emailMessage = 'Thanks for your order';

    // DIRECT & SERVER Protocol only

    /**
     * This value will be used to set the BillingAgreement field in the registration POST
     * A default is value of 0 is used if this parameter is not included in this properties file
     *
     * @var int
     */
    private $_billingAgreement = 0;

    // DIRECT Protocol only

    /**
     * Tell the SagePay System which merchant account to use.
     * If omitted the system will use E, then M, then C by default.
     * E = Use the e-commerce merchant account (default).
     * M = Use the mail
     * C = Use the continuous authority merchant account (if present).
     *
     * @var string
     */
    private $_accountType;

    /**
     * Any 7 character salt for the local customer password database used by the kit
     *
     * @var string
     */
    private $_customerPasswordSalt = '';

    /**
     * Set this to false to use colon delimited format for the basket instead of XML
     *
     * @var boolean
     */
    private $_basketAsXmlDisable = false;

    /**
     * Server profile used by default
     *
     * @var string
     */
    private $_serverProfile = SAGEPAY_SERVER_PROFILE_NORMAL;

    /**
     * Set this value for notification url called by Sagepay System for SERVER Protocol
     *
     * @var string
     */
    private $_serverNotificationUrl = '';

    /**
     * Set this value for callback url called by PayPal
     *
     * @var string
     */
    private $_paypalCallbackUrl = '';

    /**
     * List of Purchase URLs used for Registration Transaction
     *
     * @var array
     */
    private $_purchaseUrls = array(
        'test' => array(
            'form' => SAGEPAY_FORM_SERVER_TEST,
            'server' => SAGEPAY_SERVER_SERVER_TEST,
            'direct' => SAGEPAY_DIRECT_SERVER_TEST,
            'direct3d' => SAGEPAY_DIRECT_SERVER_3D_SECURE_CALLBACK_TEST,
            'paypal' => SAGEPAY_PAYPAL_COMPLETION_TEST,
        ),
        'live' => array(
            'form' => SAGEPAY_FORM_SERVER_LIVE,
            'server' => SAGEPAY_SERVER_SERVER_LIVE,
            'direct' => SAGEPAY_DIRECT_SERVER_TEST,
            'direct3d' => SAGEPAY_DIRECT_SERVER_3D_SECURE_CALLBACK_LIVE,
            'paypal' => SAGEPAY_PAYPAL_COMPLETION_LIVE,
        )
    );

    /**
     * List of Shared URLs used for admin panel actions
     *
     * @var array
     */
    private $_sharedUrls = array(
        'test' => array(
            'repeat' => SAGEPAY_SHARED_REPEAT_TRANSACTION_TEST,
            'abort' => SAGEPAY_SHARED_ABORT_TRANSACTION_TEST,
            'release' => SAGEPAY_SHARED_RELEASE_TRANSACTION_TEST,
            'refund' => SAGEPAY_SHARED_REFUND_TRANSACTION_TEST,
            'void' => SAGEPAY_SHARED_VOID_TRANSACTION_TEST,
            'authorise' => SAGEPAY_SHARED_AUTHORISE_TRANSACTION_TEST,
            'cancel' => SAGEPAY_SHARED_CANCEL_TRANSACTION_TEST,
        ),
        'live' => array(
            'repeat' => SAGEPAY_SHARED_REPEAT_TRANSACTION_LIVE,
            'abort' => SAGEPAY_SHARED_ABORT_TRANSACTION_LIVE,
            'release' => SAGEPAY_SHARED_RELEASE_TRANSACTION_LIVE,
            'refund' => SAGEPAY_SHARED_REFUND_TRANSACTION_LIVE,
            'void' => SAGEPAY_SHARED_VOID_TRANSACTION_LIVE,
            'authorise' => SAGEPAY_SHARED_AUTHORISE_TRANSACTION_LIVE,
            'cancel' => SAGEPAY_SHARED_CANCEL_TRANSACTION_LIVE,
        )
    );

    /**
     * List of Token URLs used for store/remove token
     *
     * @var array
     */
    private $_tokenUrls = array(
        'test' => array(
            'register-server' => SAGEPAY_SERVER_TOKEN_REGISTER_TEST,
            'register-direct' => SAGEPAY_DIRECT_TOKEN_REGISTER_TEST,
            'remove' => SAGEPAY_TOKEN_REMOVE_TEST,
        ),
        'live' => array(
            'register-server' => SAGEPAY_SERVER_TOKEN_REGISTER_LIVE,
            'register-direct' => SAGEPAY_DIRECT_TOKEN_REGISTER_LIVE,
            'remove' => SAGEPAY_TOKEN_REMOVE_LIVE,
        )
    );

    /**
     * If it is true, all logs will be stored in debug.log
     *
     * @var boolean
     */
    private $_logError = false;

    /**
     * The language the customer sees the payment pages in is determined by the code sent here.
     * If this is null then the language default of the shoppers browser will be used.
     *
     * @var string
     */
    private $_language = null;

    /**
     * Reference to the website this transaction came from.
     * This field is useful if transactions can originate from more than one website.
     *
     * @var string
     */
    private $_website = null;

    /**
     * Timeout for POST cURL requests
     *
     * @var int
     */
    private $_requestTimeout = 30;

    /**
     * The name of a file holding one or more certificates to verify the peer with.
     *
     * @var string
     */
    private $_caCertPath = '';

    /**
     * Initialize the configuration depends on array or if $config is null,
     * then load from file.
     *
     * @param array $config             Well-formed associative array
     * @param boolean $loadFileConfig   Load configurations from file
     */
    private function __construct(array $config, $loadFileConfig)
    {
        if ($loadFileConfig)
        {
            $fileConfig = $this->_loadFileConfig();
            $config = array_merge($fileConfig, $config);
        }
        $this->_applyConfig($config);
    }
    
    /**
     * Restrict clone functionality
     */
    private function __clone()
    {        
    }

    /**
     * Get instance of settings
     * 
     * @param array $config
     * @param boolean $loadFileConfig
     * @return SagepaySettings
     */
    static public function getInstance(array $config = array(), $loadFileConfig = true) 
    {
        if (self::$_instance === null)
        {
            self::$_instance = new SagepaySettings($config, $loadFileConfig);
        }
        return self::$_instance;
    }        

    /**
     * Get environment.
     *
     * @return string  Environment
     */
    public function getEnv()
    {
        return $this->_env;
    }

    /**
     * Set environment
     *
     * @param string $_env  Environment
     */
    public function setEnv($env)
    {
        if (in_array($env, array(SAGEPAY_ENV_TEST, SAGEPAY_ENV_LIVE)))
        {
            $this->_env = $env;
        }
        else
        {
            trigger_error("Invalid Environment value, [test, live] expected, " . $env . " given", E_USER_WARNING);
        }
    }

    /**
     * Get SagePay Protocol Version used for payment
     *
     * @return float  Protocol version
     */
    public function getProtocolVersion()
    {
        return number_format($this->_protocolVersion, 2);
    }

    /**
     * Set SagePay Protocol Version used for payment
     *
     * @param float $_protocolVersion  Protocol version
     */
    public function setProtocolVersion($protocolVersion)
    {
        if (is_float($protocolVersion))
        {
            $this->_protocolVersion = floatval($protocolVersion);
        }
        else
        {
            trigger_error("Invalid Protocol Version value, float expected, " . gettype($protocolVersion) . " given", E_USER_WARNING);
        }
    }

    /**
     * Get vendor name provided by Sagepay service
     *
     * @return string  Vendor name
     */
    public function getVendorName()
    {
        return $this->_vendorName;
    }

    /**
     * Set vendor name provided by Sagepay service
     *
     * @param string $vendorName Vendor name
     */
    public function setVendorName($vendorName)
    {
        $this->_vendorName = $vendorName;
    }

    /**
     * Get value of vendor data you wish to be displayed against the transaction in MySagePay.
     *
     * @return string Vendor data
     */
    public function getVendorData()
    {
        return $this->_vendorData;
    }

    /**
     * Set value of vendor data you wish to be displayed against the transaction in MySagePay.
     *
     * @param string $vendorData vendor data
     */
    public function setVendorData($vendorData)
    {
        $this->_vendorData = $vendorData;
    }

    /**
     * Get currency in which you wish to trade
     *
     * @return string Currency
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * Set currency in which you wish to trade
     *
     * @param string $currency Currency
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
    }

    /**
     * Get transaction type
     *
     * @return string Transaction type
     */
    public function getTxType()
    {
        return $this->_txType;
    }

    /**
     * Set transaction type
     *
     * @param string $txType Transaction type
     */
    public function setTxType($txType)
    {
        $this->_txType = $txType;
    }

    /**
     * Get list of URL of a vendor's server
     *
     * @return array  List of siteFqdn
     */
    public function getSiteFqdns()
    {
        return $this->_siteFqdn;
    }

    /**
     * Set list of URL of a vendor's server
     *
     * @param array $siteFqdn List of siteFqdn
     */
    public function setSiteFqdns(array $siteFqdn)
    {
        $this->_siteFqdn = $siteFqdn;
    }

    /**
     * Get unique partner ID
     *
     * @return string  Partner ID
     */
    public function getPartnerId()
    {
        return $this->_partnerId;
    }

    /**
     * Set unique partner ID
     *
     * @param string $partnerId  Partner ID
     */
    public function setPartnerId($partnerId)
    {
        $this->_partnerId = $partnerId;
    }

    /**
     * Get value of Address Verification Status / Card Verification Value option
     *
     * @return int  Apply AVS/CV2 validation option
     */
    public function getApplyAvsCv2()
    {
        return $this->_applyAvsCv2;
    }

    /**
     * Set value of Address Verification Status / Card Verification Value option
     *
     * @param int $applyAvsCv2 Apply AVS/CV2 validation option
     */
    public function setApplyAvsCv2($applyAvsCv2)
    {
        if (in_array($applyAvsCv2, range(0, 3)))
        {
            $this->_applyAvsCv2 = $applyAvsCv2;
        }
        else
        {
            trigger_error("Invalid Apply AVS/CV2 value, [0, 1, 2, 3] expected, " . $applyAvsCv2 . " given", E_USER_WARNING);
        }
    }

    /**
     * Get value of 3D Secure Verification option
     *
     * @return int  3D Secure Verification option
     */
    public function getApply3dSecure()
    {
        return $this->_apply3dSecure;
    }

    /**
     * Set value of 3D Secure Verification option
     *
     * @param int $apply3dSecure  3D Secure Verification option
     */
    public function setApply3dSecure($apply3dSecure)
    {
        if (in_array($apply3dSecure, range(0, 3)))
        {
            $this->_apply3dSecure = $apply3dSecure;
        }
        else
        {
            trigger_error("Invalid Apply 3D Secure value, [0, 1, 2, 3] expected, " . $apply3dSecure . " given", E_USER_WARNING);
        }
    }

    /**
     * Get value of Allow gift aid option
     *
     * @return int  Allow gift aid option
     */
    public function getAllowGiftAid()
    {
        return $this->_allowGiftAid;
    }

    /**
     * Set value of Allow gift aid option
     *
     * @param int $allowGiftAid  Allow gift aid option
     */
    public function setAllowGiftAid($allowGiftAid)
    {
        if (in_array($allowGiftAid, range(0, 1)))
        {
            $this->_allowGiftAid = $allowGiftAid;
        }
        else
        {
            trigger_error("Invalid Allow Gift Aid value, [0, 1] expected, " . $allowGiftAid . " given", E_USER_WARNING);
        }
    }

    /**
     * Get List of Surcharges
     *
     * @return array  list of Surcharges
     */
    public function getSurcharges()
    {
        return $this->_surcharges;
    }

    /**
     * Set list of Surcharges
     *
     * @param array $surcharges  list of Surcharges
     */
    public function setSurcharges($surcharges)
    {
        $this->_surcharges = $surcharges;
    }

    /**
     * Get value of Collect recipient details option
     *
     * @return boolean  Collect recipient details option
     */
    public function getCollectRecipientDetails()
    {
        return $this->_collectRecipientDetails;
    }

    /**
     * Set value of Collect recipient details option
     *
     * @param boolean $collect  Collect recipient details option
     */
    public function setCollectRecipientDetails($collect)
    {
        if (is_bool($collect))
        {
            $this->_collectRecipientDetails = $collect;
        }
        else
        {
            trigger_error("Invalid Collect Recipient Details value, boolean expected, " . gettype($collect) . " given", E_USER_WARNING);
        }
    }

    /**
     * Get list of FORM Protocol encryption password setting
     * AES encryption password assigned to you by Sage Pay
     *
     * @return array  list of FORM Protocol encryption password
     */
    public function getFormPassword()
    {
        return $this->_formPassword;
    }

    /**
     * Set list of FORM Protocol encryption password setting
     * AES encryption password assigned to you by Sage Pay
     *
     * @param array $formPassword  list of FORM Protocol encryption password
     */
    public function setFormPassword($formPassword)
    {
        $this->_formPassword = $formPassword;
    }

    /**
     * Get success page redirect for FORM Protocol
     *
     * @return string  FORM Protocol success URL
     */
    public function getFormSuccessUrl()
    {
        return $this->_formSuccessUrl;
    }


    /**
     * Set success page redirect for FORM Protocol
     *
     * @param string $formSuccessUrl  FORM Protocol success URL
     */
    public function setFormSuccessUrl($formSuccessUrl)
    {
        $this->_formSuccessUrl = $formSuccessUrl;
    }

    /**
     * Get failure page redirect for FORM Protocol
     *
     * @return string  FORM Protocol failure URL
     */
    public function getFormFailureUrl()
    {
        return $this->_formFailureUrl;
    }

    /**
     * Set failure page redirect for FORM Protocol
     *
     * @param string $formFailureUrl  FORM Protocol failure URL
     */
    public function setFormFailureUrl($formFailureUrl)
    {
        $this->_formFailureUrl = $formFailureUrl;
    }

    /**
     * Get merchant account type
     *
     * @return type
     */
    public function getAccountType()
    {
        return $this->_accountType;
    }

    /**
     * Set merchant account type
     *
     * @param string $accountType
     */
    public function setAccountType($accountType)
    {
        if (in_array($accountType, array(SAGEPAY_ACCOUNT_ECOMMERCE, SAGEPAY_ACCOUNT_CONTINUOUS, SAGEPAY_ACCOUNT_MAIL)))
        {
            $this->_accountType = $accountType;
        }
        else
        {
            trigger_error("Invalid Account Type value, [E, C, M] expected, '" . $accountType . "' given", E_USER_WARNING);
        }
    }

    /**
     * Get server profile used by default
     *
     * @return type  SERVER Protocol profile
     */
    public function getServerProfile()
    {
        return $this->_serverProfile;
    }

    /**
     * Set server profile used by default
     *
     * @param string $serverProfile  SERVER Protocol profile
     */
    public function setServerProfile($serverProfile)
    {
        if (in_array($serverProfile, array(SAGEPAY_SERVER_PROFILE_LOW, SAGEPAY_SERVER_PROFILE_NORMAL)))
        {
            $this->_serverProfile = $serverProfile;
        }
        else
        {
            trigger_error("Invalid Server Profile value, [NORMAL, LOW] expected, '" . $serverProfile . "' given", E_USER_WARNING);
        }
    }

    /**
     * Get value for notification url called by Sagepay System for SERVER Protocol
     *
     * @return string  SERVER Protocol Notification URL
     */
    public function getServerNotificationUrl()
    {
        return $this->_serverNotificationUrl;
    }

    /**
     * Set value for notification url called by Sagepay System for SERVER Protocol
     *
     * @param string $serverNotificationUrl  SERVER Protocol Notification URL
     */
    public function setserverNotificationUrl($serverNotificationUrl)
    {
        $this->_serverNotificationUrl = $serverNotificationUrl;
    }

    /**
     * Get password salt for the local customer password database used by the kit
     *
     * @return string  Password salt
     */
    public function getCustomerPasswordSalt()
    {
        return $this->_customerPasswordSalt;
    }

    /**
     * Set password salt for the local customer password database used by the kit
     * 7 chars length
     *
     * @param string $customerPasswordSalt  Password salt
     */
    public function setCustomerPasswordSalt($customerPasswordSalt)
    {
        $this->_customerPasswordSalt = substr($customerPasswordSalt, 0, 7);
    }

    /**
     * Get value of Billing Agreement option
     *
     * @return int  Billing Agreement option
     */
    public function getBillingAgreement()
    {
        return $this->_billingAgreement;
    }

    /**
     * Set value of Billing Agreement option
     *
     * @param int $billingAgreement  Billing Agreement option
     */
    public function setBillingAgreement($billingAgreement)
    {
        if (in_array($billingAgreement, array(0, 1), true))
        {
            $this->_billingAgreement = $billingAgreement;
        }
        else
        {
            trigger_error("Invalid Billing Agreement value, [0, 1] expected, " . $billingAgreement . " given", E_USER_WARNING);
        }
    }

    /**
     * Get value of Send e-mail option
     *
     * @return int  Send e-mail option
     */
    public function getSendEmail()
    {
        return $this->_sendEmail;
    }

    /**
     * Set value of Send e-mail option
     *
     * @param int $sendEmail  Send e-mail option
     */
    public function setSendEmail($sendEmail)
    {
        $this->_sendEmail = $sendEmail;
    }

    /**
     * Get e-mail message
     *
     * @return string  E-mail message
     */
    public function getEmailMessage()
    {
        return $this->_emailMessage;
    }

    /**
     * Set e-mail message
     *
     * @param string $emailMessage  E-mail message
     */
    public function setEmailMessage($emailMessage)
    {
        $this->_emailMessage = strip_tags($emailMessage);
    }

    /**
     * Get value of vendor email
     *
     * @return string Vendor email
     */
    public function getVendorEmail()
    {
        return $this->_vendorEmail;
    }

    /**
     * Set value of vendor email
     *
     * @param string $vendorEmail Vendor email
     */
    public function setVendorEmail($vendorEmail)
    {
        if (SagepayValid::email($vendorEmail))
        {
            $this->_vendorEmail = $vendorEmail;
        }
        else
        {
            trigger_error("Invalid Vendor Email value, email format expected, '" . $vendorEmail . "' given", E_USER_WARNING);
        }
    }

    /**
     * Get value of Basket as XML seeting
     *
     * @return boolean  Basket as XML
     */
    public function basketAsXmlDisabled()
    {
        return $this->_basketAsXmlDisable;
    }

    /**
     * Set value of Basket as XML seeting
     *
     * @param boolean $basketAsXmlDisable  Basket as XML
     */
    public function setBasketAsXmlDisable($basketAsXmlDisable)
    {
        if (is_bool($basketAsXmlDisable))
        {
            $this->_basketAsXmlDisable = $basketAsXmlDisable;
        }
        else
        {
            trigger_error("Invalid Basket as XML value, boolean expected, " . gettype($basketAsXmlDisable) . " given", E_USER_WARNING);
        }
    }

    /**
     * Get value of PayPal Callback URL
     *
     * @return string PayPal Callback URL
     */
    public function getPaypalCallbackUrl()
    {
        return $this->_paypalCallbackUrl;
    }

    /**
     * Set value of PayPal Callback URL
     *
     * @param string $paypalCallbackUrl PayPal Callback URL
     */
    public function setPaypalCallbackUrl($paypalCallbackUrl)
    {
        $this->_paypalCallbackUrl = $paypalCallbackUrl;
    }

    /**
     * Get list of Registration Service
     *
     * @return array List of Registration Service
     */
    public function getPurchaseUrls()
    {
        return $this->_purchaseUrls;
    }

    /**
     * Set list of Registration Services
     *
     * @param array $purchaseUrls List of Registration Service
     */
    public function setPurchaseUrls($purchaseUrls)
    {
        $this->_purchaseUrls = $this->_mergeEnvironmentUrls($this->_purchaseUrls, $purchaseUrls);
    }

    /**
     * Get list of Shared Services
     *
     * @return array  List of Shared Services
     */
    public function getSharedUrls()
    {
        return $this->_sharedUrls;
    }

    /**
     * Set list of Shared Services
     *
     * @param array $sharedUrls  List of Shared Services
     */
    public function setSharedUrls($sharedUrls)
    {
        $this->_sharedUrls = $this->_mergeEnvironmentUrls($this->_sharedUrls, $sharedUrls);
    }

    /**
     * Get list of Token Services
     *
     * @return array  List of Token Services
     */
    public function getTokenUrls()
    {
        return $this->_tokenUrls;
    }

    /**
     * Set list of Token Services
     *
     * @param array $tokenUrls  List of Token Services
     */
    public function setTokenUrls($tokenUrls)
    {
        $this->_tokenUrls = $this->_mergeEnvironmentUrls($this->_tokenUrls, $tokenUrls);
    }

    /**
     * Get Log Error option
     *
     * @return boolean
     */
    public function getLogError()
    {
        return $this->_logError;
    }

    /**
     * Set Log Error option
     *
     * @param boolean $logError
     */
    public function setLogError($logError)
    {
        if (is_bool($logError))
        {
            $this->_logError = $logError;
        }
        else
        {
            trigger_error("Invalid Log Error, boolean expected, " . gettype($logError) . " given", E_USER_WARNING);
        }
    }

    /**
     * Get language value ISO 639-1 valid
     *
     * @return type
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Get language value ISO 639-1 valid
     * @link http://en.wikipedia.org/wiki/ISO_639
     *
     * @param type $language
     */
    public function setLanguage($language)
    {
        $this->_language = substr($language, 0, 2);
    }

    /**
     * Get reference to the website this transaction came from.
     *
     * @return type
     */
    public function getWebsite()
    {
        return $this->_website;
    }

    /**
     * Set reference to the website this transaction came from.
     *
     * @param type $website
     */
    public function setWebsite($website)
    {
        if (!empty($website) && SagepayValid::url($website))
        {
            $this->_website = $website;
        }
        else
        {
            trigger_error("Invalid Website URL value, email format expected, '" . $website . "' given", E_USER_WARNING);
        }
    }

    /**
     * Get timeout for POST cURL requests
     *
     * @return int
     */
    public function getRequestTimeout()
    {
        return $this->_requestTimeout;
    }

    /**
     * Set timeout for POST cURL requests
     *
     * @param int $ttl
     */
    public function setRequestTimeout($ttl)
    {
        if (is_int($ttl))
        {
            $this->_requestTimeout = $ttl;
        }
        else
        {
            trigger_error("Invalid request timeout value, integer expected, '" . $ttl . "' given", E_USER_WARNING);
        }
    }

    /**
     * Get CACert file path
     *
     * @return string
     */
    public function getCaCertPath()
    {
        return $this->_caCertPath;
    }

    /**
     * Set CACert file path
     *
     * @param string $caCertPath
     */
    public function setCaCertPath($caCertPath)
    {
        $this->_caCertPath = $caCertPath;
    }

    /**
     * Get value of siteFqdn for specific environment
     *
     * @param string $env Specific environment
     *
     * @return string  SiteFqdn URL
     */
    public function getSiteFqdn($env = '')
    {
        $env = $this->_validEnvironment($env);
        return $this->_siteFqdn[$env];
    }

    /**
     * Set value of siteFqdn for specific environment
     *
     * @param  string  $siteFqdn  SiteFqdn URL
     * @param  string  $env       Specific environment
     */
    public function setSiteFqdn($siteFqdn, $env = '')
    {
        $env = $this->_validEnvironment($env);
        $this->_siteFqdn[$env] = $siteFqdn;
    }

    /**
     * Set Encryption password for specific environment
     *
     * @param  string  $password  Encryption password
     * @param  string  $env       Environment name, by default is using current environment value
     */
    public function setFormEncryptionPassword($password, $env = '')
    {
        $env = $this->_validEnvironment($env);
        $this->_formPassword[$env] = $password;
    }


    /**
     * Get full URL for Form Successes
     *
     * @param string $env Specific environment
     * @return string
     */
    public function getFullFormSuccessUrl($env = '')
    {
        $base = $this->getSiteFqdn($env);
        return $base . $this->_formSuccessUrl;
    }

    /**
     * Get full URL for Form Failures
     *
     * @param string $env Specific environment
     * @return string
     */
    public function getFullFormFailureUrl($env = '')
    {
        $base = $this->getSiteFqdn($env);
        return $base . $this->_formFailureUrl;
    }

    /**
     * Get full URL for Server Notifications
     *
     * @param string $env Specific environment
     * @return string
     */
    public function getFullServerNotificationUrl($env = '')
    {
        $base = $this->getSiteFqdn($env);
        return $base . $this->_serverNotificationUrl;
    }

    /**
     * Get Encryption password for specific environment
     *
     * @param  string  $env  Environment name, by default is using current environment value
     *
     * @return string  Encryption password
     */
    public function getFormEncryptionPassword($env = '')
    {
        $env = $this->_validEnvironment($env);
        return $this->_formPassword[$env];
    }

    /**
     * Get value of specific Registration Service
     *
     * @param  string  $method  Method alias
     * @param  string  $env     Environment name, by default is using current environment value
     *
     * @return string Registration Service URL
     */
    public function getPurchaseUrl($method, $env = '')
    {
        $env = $this->_validEnvironment($env);
        if (isset($this->_purchaseUrls[$env][$method]))
        {
            return $this->_purchaseUrls[$env][$method];
        }
        return '';
    }

    /**
     * Set value of specific method of Registration Services
     *
     * @param  string  $purchaseUrl  Registration Service URL
     * @param  string  $method       Method alias
     * @param  string  $env          Environment name, by default is using current environment value
     */
    public function setPurchaseUrl($purchaseUrl, $method, $env = '')
    {
        $env = $this->_validEnvironment($env);
        if (!empty($method) && !empty($purchaseUrl))
        {
            $this->_purchaseUrls[$env][$method] = $purchaseUrl;
        }
    }

    /**
     * Get value of specific method of Shared Services
     *
     * @param  string  $method  Method alias
     * @param  string  $env     Environment name, by default is using current environment value
     *
     * @return string Shared Service URL
     */
    public function getSharedUrl($method, $env = '')
    {
        $env = $this->_validEnvironment($env);
        if (isset($this->_sharedUrls[$env][$method]))
        {
            return $this->_sharedUrls[$env][$method];
        }
        return '';
    }

    /**
     * Set value of specific method of Shared Services
     *
     * @param  string  $sharedUrl  Shared Service URL
     * @param  string  $method     Method alias
     * @param  string  $env        Environment name, by default is using current environment value
     */
    public function setSharedUrl($sharedUrl, $method, $env = '')
    {
        $env = $this->_validEnvironment($env);
        if (!empty($method) && !empty($sharedUrl))
        {
            $this->_sharedUrls[$env][$method] = $sharedUrl;
        }
    }

    /**
     * Get url for specific token service and environment
     *
     * @param  string  $method  Method name
     * @param  string  $env     Environment name, by default is using current environment value
     *
     * @return string  Token Service URL
     */
    public function getTokenUrl($method, $env = '')
    {

        $env = $this->_validEnvironment($env);
        if (isset($this->_tokenUrls[$env][$method]))
        {
            return $this->_tokenUrls[$env][$method];
        }
        return '';
    }

    /**
     * Set url for specific token service and environment
     *
     * @param  string  $tokenUrl  Token Service URL
     * @param  string  $method    Method name
     * @param  string  $env       Environment name, by default is using current environment value
     */
    public function setTokenUrl($tokenUrl, $method, $env = '')
    {
        $env = $this->_validEnvironment($env);
        if (!empty($method) && !empty($tokenUrl))
        {
            $this->_tokenUrls[$env][$method] = $tokenUrl;
        }
    }

    /**
     * Load file configuration and return the $config array
     *
     * @return array Configuration
     */
    private function _loadFileConfig()
    {
        return include SAGEPAY_SDK_PATH . '/config.php';
    }

    /**
     * Insert values of array config into current instance SagepayConfig
     *
     * @param array $config
     */
    private function _applyConfig(array $config)
    {
        foreach ($config as $key => $val)
        {
            $prop = 'set' . ucfirst($key);
            if (method_exists($this, $prop))
            {
                $this->$prop($val);
            }
        }
    }

    /**
     * Validate value for environment
     * Set up current env if it is missing or is not valid environment
     *
     * @param  string  $env  Environment
     *
     * @return string Environment
     */
    private function _validEnvironment($env)
    {
        if (!in_array($env, array(SAGEPAY_ENV_TEST, SAGEPAY_ENV_LIVE)))
        {
            return $this->_env;
        }
        return $env;
    }

    /**
     * Recursive merge of URLs
     *
     * @param array $oldUrls Old URLs values
     * @param array $newUrls New URLs values
     *
     * @return array
     */
    private function _mergeEnvironmentUrls(array $oldUrls, array $newUrls)
    {
        foreach ($oldUrls as $env => $methods)
        {
            foreach (array_keys($methods) as $method)
            {
                if (isset($newUrls[$env][$method]) && !empty($newUrls[$env][$method]))
                {
                    $oldUrls[$env][$method] = $newUrls[$env][$method];
                }
            }
        }
        return $oldUrls;
    }

}
