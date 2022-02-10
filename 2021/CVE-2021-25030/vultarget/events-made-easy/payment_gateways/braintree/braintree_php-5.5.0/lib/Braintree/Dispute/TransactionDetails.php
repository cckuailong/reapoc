<?php
namespace Braintree\Dispute;

use Braintree\Instance;

/**
 * Transaction details for a dispute
 *
 * @package    Braintree
 *
 * @property-read string $amount
 * @property-read \DateTime $createdAt
 * @property-read string $id
 * @property-read int $installmentCount
 * @property-read string $orderId
 * @property-read string $paymentInstrumentSubtype
 * @property-read string $purchaseOrderNumber
 */

/**
 * Creates an instance of DisbursementDetails as returned from a transaction
 *
 *
 * @package    Braintree
 *
 * @property-read string $amount
 * @property-read string $id
 */
class TransactionDetails extends Instance
{
}
