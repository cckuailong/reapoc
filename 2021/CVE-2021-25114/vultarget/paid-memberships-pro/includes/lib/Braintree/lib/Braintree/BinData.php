<?php
namespace Braintree;

class BinData extends Base
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
     * returns a string representation of the bin data
     * @return string
     */
    public function  __toString()
    {
        return __CLASS__ . '[' .
            Util::attributesToString($this->_attributes) .']';
    }

}
class_alias('Braintree\BinData', 'Braintree_BinData');
