<?php
namespace Braintree\Transaction;

use Braintree\Instance;

/**
 * Local payment details from a transaction
 *
 * @package    Braintree
 * @subpackage Transaction
 */

/**
 * creates an instance of LocalPaymentDetails
 *
 *
 * @package    Braintree
 * @subpackage Transaction
 *
 * @property-read string $captureId
 * @property-read string $customField
 * @property-read string $description
 * @property-read string $debugId
 * @property-read string $payerId
 * @property-read string $paymentId
 * @property-read string $fundingSource
 * @property-read string $refundFromTransactionFeeAmount
 * @property-read string $refundFromTransactionFeeCurrencyIsoCode
 * @property-read string $refundId
 * @property-read string $transactionFeeAmount
 * @property-read string $transactionFeeCurrencyIsoCode
 */
class LocalPaymentDetails extends Instance
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
