<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rm_frontend_field_ggeo
 *
 * @author RegistrationMagic
 */
class RM_Frontend_Field_WCAddress extends RM_Frontend_Field_Base
{
    public $field_type;
    public function __construct($id, $type,$field_name, $label, $options , $page_no,$field_type, $is_primary = false, $extra_opts = null)
    {
        parent::__construct($id, $type,$field_name, $label, $options, $page_no,$is_primary, $extra_opts);
        $this->field_type= $field_type;
    }

    public function get_pfbc_field()
    {
        if ($this->pfbc_field)
            return $this->pfbc_field;
        else
        {
            $field_options= (array) $this->field_model->field_options;
            $field_options['field_id']=$this->field_id;
            $fo= $this->get_field_options();
            $field_options['form_id']= $this->field_model->get_form_id();
            $form= new RM_Forms();
            $form->load_from_db($field_options['form_id']);
            $form_options= $form->get_form_options();
            $field_options['textfield_style']='';
            $field_options['style_label']='';
            if(!empty($form_options->style_textfield)){
                $field_options['textfield_style']= $form_options->style_textfield;
            }
            if(!empty($form_options->style_label)){
                $field_options['style_label']= $form_options->style_label;
                $field_options['labelStyle']= $form_options->style_label;
            }
            //print_r($form_options); die;
            $field_options['value']=$fo['value'];
            $label = $this->get_formatted_label();
            $this->pfbc_field = new Element_WCAddress($label, $this->field_name, $this->field_type,$field_options);
            $this->add_custom_validations();
            return $this->pfbc_field;
        }
    }
    
     public function get_prepared_data($request)
    {
        $data = new stdClass;
        $data->field_id = $this->get_field_id();
        $data->type = $this->get_field_type();
        $data->label = $this->get_field_label();
        $data->value = isset($request['wc'.$this->field_type.'_'.$this->field_id]) ? $request['wc'.$this->field_type.'_'.$this->field_id] : null;
        return $data;
    }
}
