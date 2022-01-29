<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RM_Frontend_Field_Hidden extends RM_Frontend_Field_Base
{
    public $field_value;
    public $field_class;
            
    public function get_pfbc_field()
    {
        if ($this->pfbc_field)
            return $this->pfbc_field;
        else
        {
            $class_name = "Element_" . $this->field_type;
            //Add hidden field name as field id so user can modify field using JS if required.
            $this->field_options['id'] = $this->field_label;
            $value = isset($this->field_options['value']) ? $this->field_options['value'] : '';
            $this->pfbc_field = new $class_name($this->field_name, $value, $this->field_options);
            return $this->pfbc_field;
        }  
    }
}