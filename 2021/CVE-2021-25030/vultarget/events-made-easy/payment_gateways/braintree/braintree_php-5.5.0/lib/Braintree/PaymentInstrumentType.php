<?php
namespace Braintree;

class PaymentInstrumentType
{
    const ANDROID_PAY_CARD    = 'android_pay_card'; // NEXT_MAJOR_VERSION rename Android Pay to Google Pay
    const APPLE_PAY_CARD      = 'apple_pay_card';
    const CREDIT_CARD         = 'credit_card';
    const LOCAL_PAYMENT       = 'local_payment';
    const MASTERPASS_CARD     = 'masterpass_card'; //NEXT_MAJOR_VERSION remove this deprecated constant
    const PAYPAL_ACCOUNT      = 'paypal_account';
    const PAYPAL_HERE         = 'paypal_here';
    const SAMSUNG_PAY_CARD    = 'samsung_pay_card';
    const US_BANK_ACCOUNT     = 'us_bank_account';
    const VENMO_ACCOUNT       = 'venmo_account';
    const VISA_CHECKOUT_CARD  = 'visa_checkout_card';
}
