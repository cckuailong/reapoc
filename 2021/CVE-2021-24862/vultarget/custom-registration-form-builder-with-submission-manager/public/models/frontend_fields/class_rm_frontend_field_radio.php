<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RM_Frontend_Field_Radio extends RM_Frontend_Field_Multivalue
{
    public function __construct($id, $label,$field_name, $options, $value, $page_no = 1, $is_primary = false, $extra_opts = null)
    {
        parent::__construct($id, 'Radio',$field_name, $label, $options, $value, $page_no, $is_primary, $extra_opts);
        $this->field_value = RM_Utilities::process_field_options($value);
    }
}