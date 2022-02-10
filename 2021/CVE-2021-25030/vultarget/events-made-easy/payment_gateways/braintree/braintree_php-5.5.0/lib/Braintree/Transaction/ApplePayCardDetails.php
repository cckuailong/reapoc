<?php
namespace Braintree\Transaction;

use Braintree\Instance;

/**
 * Apple Pay card details from a transaction
 *
 * @package    Braintree
 * @subpackage Transaction
 */

/**
 * creates an instance of ApplePayCardDetails
 *
 *
 * @package    Braintree
 * @subpackage Transaction
 *
 * @property-read string $bin
 * @property-read string $cardType
 * @property-read string $cardholderName
 * @property-read string $commercial
 * @property-read string $country_of_issuance
 * @property-read string $debit
 * @property-read string $durbin_regulated
 * @property-read string $expirationMonth
 * @property-read string $expirationYear
 * @property-read string $healthcare
 * @property-read string $issuing_bank
 * @property-read string $paymentInstrumentName
 * @property-read string $payroll
 * @property-read string $prepaid
 * @property-read string $product_id
 * @property-read string $sourceDescription
 */
class ApplePayCardDetails extends Instance
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
