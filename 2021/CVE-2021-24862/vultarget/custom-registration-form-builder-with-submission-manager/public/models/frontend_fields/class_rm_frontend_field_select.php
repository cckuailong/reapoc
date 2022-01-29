<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RM_Frontend_Field_Select extends RM_Frontend_Field_Multivalue
{
    //public $field_value; //Choices from which user can select

    public function __construct($id, $label,$field_name, $options, $value, $page_no = 1, $is_primary = false, $extra_opts = null)
    {
        if(isset($options['value']))
        {
            if(!is_array($options['value']))
                $options['value'] = RM_Utilities::trim_array(explode(',', $options['value']));
            else
                $options['value'] = $options['value'];
        }
        parent::__construct($id, 'Select',$field_name, $label,$options, $value, $page_no, $is_primary, $extra_opts);
        
        if(isset($options['multiple']))
            $multiple=$options['multiple'];
        else
            $multiple='';
        
        $options = RM_Utilities::process_field_options($value);
        
        if($multiple=='multiple')
              $options = array(null => RM_UI_Strings::get('SELECT_FIELD_MULTI_OPTION')) + $options;
        else
             $options = array(null => RM_UI_Strings::get('SELECT_FIELD_FIRST_OPTION')) + $options;
        
        $this->field_value = $options;        
    }
    
    public function get_pfbc_field()
    {
        if ($this->pfbc_field)
            return $this->pfbc_field;
        else {
            $class_name = "Element_" . $this->field_type;
            $this->set_conditional_properties();
            $label = $this->get_formatted_label();
            if(isset($this->field_options['multiple'])) {
               $this->field_options['field_hint'] = RM_UI_Strings::get('HINT_MULTISELECT_FIELD');
            }
            $this->pfbc_field = new $class_name($label, $this->field_name, $this->field_value, $this->field_options);            
            $this->add_custom_validations();
            return $this->pfbc_field;
        }
    }
}