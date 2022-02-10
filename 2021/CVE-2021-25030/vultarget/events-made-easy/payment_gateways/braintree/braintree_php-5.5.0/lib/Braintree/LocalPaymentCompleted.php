<?php
namespace Braintree;

/**
 * Braintree LocalPaymentCompleted module
 *
 * @package    Braintree
 * @category   Resources
 */

/**
 * Manages Braintree LocalPaymentCompleted 
 *
 * <b>== More information ==</b>
 *
 *
 * @package    Braintree
 * @category   Resources
 *
 * @property-read string $paymentId
 * @property-read string $payerId
 * @property-read string $paymentMethodNonce
 * @property-read \Braintree\Transaction $transaction
 */
class LocalPaymentCompleted extends Base
{
    /**
     *  factory method: returns an instance of GrantedPaymentInstrumentUpdate
     *  to the requesting method, with populated properties
     *
     * @ignore
     * @return LocalPaymentCompleted 
     */
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

    /* instance methods */

    /**
     * sets instance properties from an array of values
     *
     * @access protected
     * @param array $LocalPaymentCompletedAttribs array of localPaymentCompleted data
     * @return void
     */
    protected function _initialize($localPaymentCompletedAttribs)
    {
        // set the attributes
        $this->_attributes = $localPaymentCompletedAttribs;

        if (isset($transactionAttribs['transaction'])) {
            $this->_set('transaction',
                new Transaction(
                    $transactionAttribs['transaction']
                )
            );
        }
    }

    /**
     * create a printable representation of the object as:
     * ClassName[property=value, property=value]
     * @return string
     */
    public function  __toString()
    {
        return __CLASS__ . '[' .
                Util::attributesToString($this->_attributes) . ']';
    }
}
