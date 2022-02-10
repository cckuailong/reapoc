<?php
namespace Braintree;

/**
 * @property-read string $enrolled
 * @property-read boolean $liabilityShiftPossible
 * @property-read string $liabilityShifted
 * @property-read string $status
 * @property-read boolean $xid
 * @property-read string $cavv
 * @property-read string $eciFlag
 * @property-read string $dsTransactionId
 * @property-read string $threeDSecureVersion
 */
class ThreeDSecureInfo extends Base
{
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);

        return $instance;
    }

    protected function _initialize($attributes)
    {
        $this->_attributes = $attributes;
    }

    /**
     * returns a string representation of the three d secure info
     * @return string
     */
    public function  __toString()
    {
        return __CLASS__ . '[' .
                Util::attributesToString($this->_attributes) .']';
    }

}
