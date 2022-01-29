<?php

abstract class RM_Chronos_Rule_Abstract implements RM_Chronos_Rule_Interface {
    
    public $attr_name;
    public $attr_value;
    public $operator;
    public $rule_id;
    
    public function __construct(RM_Chronos_Rule_Model $model)
    {
        foreach($model->props as $prop => $val) {
            if(property_exists($this, $prop))
                    $this->$prop = $val;
        }         
    }
        
    abstract public function get_type();    
}
