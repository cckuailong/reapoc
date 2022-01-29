<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RM_Frontend_Field_Base
{

    public $field_id;
    public $field_type;    //Element type
    public $field_label;   //Second argument in PFBC field contructor
    //public $field_value;   //Third argument (array or single val)
    public $field_options; //Last array in PFBC construct
    public $field_name;    //Name in the request variable
    public $pfbc_field;
    public $is_primary;
    public $page_no;
    public $primary_field_indices;
    public $x_options;
    public $field_model;

    public function __construct($id, $type,$field_name, $label, $options, $page_no = 1, $is_primary = false, $extra_opts = null)
    {
        $this->field_id = $id;
        $this->field_type = $type;
        $this->field_label = $label;
        //$this->field_value = $be_field->get_field_value();
        $this->field_options = $options;
        $this->field_name = ($field_name == '') ? $this->field_type . "_" . $this->field_id : $field_name;
        $this->pfbc_field = null;
        $this->is_primary = $is_primary;
        $this->page_no = $page_no;
        $this->x_options = $extra_opts;
        $this->field_model= new RM_Fields();
        $this->field_model->load_from_db($id);
    }

    public function get_page_no()
    {
        return $this->page_no;
    }

    public function get_field_id()
    {
        return $this->field_id;
    }

    public function get_field_type()
    {
        return $this->field_type;
    }

    public function get_field_label($with_icon=false)
    {
        if(!$with_icon)
            return $this->field_label;
        else
            return $this->get_formatted_label();
    }

    public function is_primary()
    {
        return $this->is_primary;
    }

//    public function get_field_value()
//    {
//        return $this->field_value;
//    }

    public function get_field_options()
    {
        return $this->field_options;
    }

    public function get_field_name()
    {
        return $this->field_name;
    }

    public function set_field_name($field_name)
    {
        $this->field_name = $field_name;
    }

    public function set_field_label($field_label)
    {
        $this->field_label = $field_label;
    }

//    public function set_field_value($field_value)
//    {
//        $this->field_value = $field_value;
//    }

    public function set_field_options($field_options)
    {
        $this->field_options = $field_options;
    }

    public function render()
    {
        $this->get_pfbc_field();
        if(is_array($this->pfbc_field)):
            foreach($this->pfbc_field as $pfbc_field):
                $pfbc_field->render();
            endforeach;
            
        else:
           $this->pfbc_field->render(); 
   
        endif;
    }
    
    public function jquery_document_ready(){
        $this->get_pfbc_field();
        ob_start();
        if(is_array($this->pfbc_field)):
            foreach($this->pfbc_field as $pfbc_field):
                $pfbc_field->jQueryDocumentReady();
            endforeach;
            
        else:
           $this->pfbc_field->jQueryDocumentReady(); 
   
        endif;
        
        return ob_get_clean();
    }

    public function is_valid($value,$form_id,$validation_errors=null)
    {
        $this->get_pfbc_field();
        
        if(is_array($this->pfbc_field)):
            foreach($this->pfbc_field as $pfbc_field):
                 $pfbc_field->isValid($value,$form_id,$validation_errors);
            endforeach;         
        else:
            $this->pfbc_field->isValid($value,$form_id,$validation_errors);
        endif;
    }

    //This should be called and added to PFBC form and then rendered.
    public function get_pfbc_field()
    {
        if ($this->pfbc_field)
            return $this->pfbc_field;
        else
        {
            $class_name = "Element_" . $this->field_type;
            
            // Check if this is primary email field (To implement the real time validation)
            if(strtolower($this->field_type)=="email" && $this->is_primary())
                $class_name= "Element_UserEmail";
            
            $this->set_conditional_properties();
            $label = $this->get_formatted_label();
            $this->pfbc_field = new $class_name($label, $this->field_name, $this->field_options);
            $this->add_custom_validations();
            return $this->pfbc_field;
        }
    }
    
    public function add_custom_validations()
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_field_base = new RM_Frontend_Field_Base_Addon();
            return $addon_field_base->add_custom_validations($this);
        }
        if($this->is_primary() && $this->field_type=='Email')
        { 
           $this->pfbc_field->addValidation(new Validation_Email(RM_UI_Strings::get('FORM_ERR_INVALID_EMAIL'))); 
           $form_id= $this->field_model->get_form_id();
           $form= new RM_Forms();
           $form->load_from_db($form_id);
           if($form->get_form_type()==RM_REG_FORM){
               $this->pfbc_field->addValidation(new Validation_UserEmail(RM_UI_Strings::get('LABEL_USER_EMAIL_EXISTS'),new RM_User_Email_Validator($this->field_model->field_id,$this->field_model->get_form_id())));
           }
        }
    }
    
    public function set_conditional_properties()
    {   
        $cond_option=array();$cond_value=array();$cond_op= array();
        if(!empty($this->field_model->field_options->conditions['rules']) && is_array($this->field_model->field_options->conditions['rules'])){
            $conditions= $this->field_model->field_options->conditions['rules'];
            $values= array();
            foreach($conditions as $condition)
            {   
                $cf_id= $condition['controlling_field'];
                $cf_field= new RM_Fields();
                if($cf_field->load_from_db($cf_id)){
                    $cType= $cf_field->get_field_type();
                    $field_name= $cType.'_'.$cf_id;
                    
                    //Special Fields 
                    if($cType=="Country")
                    {  
                        $pfbc_field= new Element_Country("country","country");
                        $country_list= $pfbc_field->getOptions();
                        $country= array();
                        if(is_array($condition['values'])){
                            foreach($condition['values'] as $country_val){
                                $country[]= array_search($country_val, $country_list);
                            }
                        }
                        $values= implode(',',$country);
                    }
                    else if($cType=="Timezone")
                    {
                        $pfbc_field= new Element_Timezone("tz","tz");
                        $timezone_list= $pfbc_field->getOptions();
                        $timezone= array();
                        if(is_array($condition['values'])){
                            foreach($condition['values'] as $timezone_val){
                                $timezone[]= array_search($timezone_val, $timezone_list);
                            }
                        }
                        $values= implode(',',$timezone);
                    }
                    else if($cType=="Language")
                    {
                        $pfbc_field= new Element_Timezone("tz","tz");
                        $lang_list= RM_Utilities::get_language_array();
                        $languages= array();
                        if(is_array($condition['values'])){
                            foreach($condition['values'] as $lang){
                                $languages[]= array_search($lang, $lang_list);
                            }
                        }
              
                        $values= implode(',',$languages);
                    }
                    else if($cType=="Checkbox" || $cType=="Multi-Dropdown")
                    {  
                        $field_name .='[]'; 
                        if(is_array($condition['values'])){
                            $values= implode(',', $condition['values']);
                        }
                    }
                    else{
                        if(is_array($condition['values'])){
                            $values= implode(',', $condition['values']);
                        }
                        else
                            $values= $condition['values'][0];
                    }
                    $cond_option[]= $field_name;        
                    $cond_value[]=  empty($values) ? "_" : $values;
                    $cond_op[]= $condition['op'];
                }
               
                
            }

            if(count($cond_option)>0){
                $this->field_options['data-cond-option']= implode('|', $cond_option);
                $this->field_options['data-cond-value']= implode('|', $cond_value);
                $this->field_options['data-cond-operator']= implode('|', $cond_op);
                $settings= $this->field_model->field_options->conditions['settings'];
                if(count($cond_option)>1)
                $this->field_options['data-cond-comb']= empty($settings['combinator']) ? 'OR': $settings['combinator'];
                $this->field_options['class']= !empty($this->field_model->field_options->field_css_class) ? $this->field_model->field_options->field_css_class . " data-conditional" : "data-conditional";
            }
           
        }
        
    }
    
    public function get_prepared_data($request)
    {
        $data = new stdClass;
        $data->field_id = $this->get_field_id();
        $data->type = $this->get_field_type();
        $data->label = $this->get_field_label();
        $data->value = isset($request[$this->field_name]) ? $request[$this->field_name] : null;
        return $data;
    }

    public function get_formatted_label()
    {
        if (isset($this->x_options->icon) && $this->x_options->icon->codepoint)
        {
            if ($this->x_options->icon->shape == 'square')
                $radius = '0px';
            else if ($this->x_options->icon->shape == 'round')
                $radius = '100px';
            else if ($this->x_options->icon->shape == 'sticker')
                $radius = '4px';

            $bg_r = intval(substr($this->x_options->icon->bg_color, 0, 2), 16);
            $bg_g = intval(substr($this->x_options->icon->bg_color, 2, 2), 16);
            $bg_b = intval(substr($this->x_options->icon->bg_color, 4, 2), 16);
            $bg_a = isset($this->x_options->icon->bg_alpha) ? $this->x_options->icon->bg_alpha : 1;

            $icon_style = "style=\"padding:5px;color:#{$this->x_options->icon->fg_color};background-color:rgba({$bg_r},{$bg_g},{$bg_b},{$bg_a});border-radius:{$radius};\"";
            return '<span><i class="material-icons rm_front_field_icon"' . $icon_style . ' id="id_show_selected_icon">' . $this->x_options->icon->codepoint . ';</i></span>' . $this->field_label;
        } else
            return $this->field_label;
    }

}
