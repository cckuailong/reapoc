<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RM_Frontend_Field_Visible_Only extends RM_Frontend_Field_Base
{
    public $field_value;
    public $field_class;
    
    public function __construct($id, $type,$field_name, $label, $options, $value, $page_no = 1, $is_primary = false, $extra_opts = null)
    {
        parent::__construct($id, $type,$field_name, $label, $options, $page_no, $is_primary,$extra_opts);
        
        $this->field_value = $value;
        $this->field_class = isset($options['class'])?$options['class']:null;
    }
    
    public function get_pfbc_field()
    {
        if ($this->pfbc_field)
            return $this->pfbc_field;
        else
        {   
            $class_name = "Element_" . $this->field_type;
            $this->pfbc_field = new $class_name($this->field_value,$this->field_class, $this->field_options);
            return $this->pfbc_field;
        }  
    }
    
    public function get_prepared_data($request)
    {
        $pfbc_field= $this->get_pfbc_field();
        $data = new stdClass;
        $data->field_id = $this->get_field_id();
        $data->type = $this->get_field_type();
        $data->label = $this->get_field_label();
        $data->value = $pfbc_field->getAttribute("value");
        return $data;
    }
}