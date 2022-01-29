<?php

class RM_Chronos_Rule_FieldValue extends RM_Chronos_Rule_Abstract {
    
    public function __construct(RM_Chronos_Rule_Model $model)
    {
        parent::__construct($model);
        $this->attr_value = json_decode($this->attr_value, true);
    }
    
    public function get_type() {
        return RM_Chronos_Rule_Interface::RULE_TYPE_FIELD_VALUE;
    }            
}


