<?php
namespace Braintree\Transaction;

use Braintree\Instance;

/**
 * PayPal Here details from a transaction
 *
 * @package     Braintree
 * @subpackage  Transaction
 */

/**
 * creates and instance of PayPalHereDetails
 *
 *
 * @package     Braintree
 * @subpackage  Transaction
 *
 * @property-read string $authorizationId
 * @property-read string $captureId
 * @property-read string $invoiceId
 * @property-read string $last4
 * @property-read string $paymentId
 * @property-read string $payment_type
 * @property-read string $refundId
 * @property-read string $transactionFeeAmount
 * @property-read string $transactionFeeCurrencyIsoCode
 * @property-read string $transactionInitiationDate
 * @property-read string $transactionUpdatedDate
 */
class PayPalHereDetails extends Instance
{
    protected $_attributes = [];

     /**
     * @ignore
     */
     public function __construct($attributes)
     {
         parent::__construct($attributes);
     }
}
