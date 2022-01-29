<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RM_Frontend_Form_Contact extends RM_Frontend_Form_Multipage//RM_Frontend_Form_Base
{

    public function __construct(RM_Forms $be_form, $ignore_expiration=false)
    {
        parent::__construct($be_form, $ignore_expiration);
        $this->set_form_type(RM_CONTACT_FORM);
    }

    public function pre_sub_proc($request, $params)
    {
        return true;
    }

    public function post_sub_proc($request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_form_contact = new RM_Frontend_Form_Contact_Addon();
            return $addon_form_contact->post_sub_proc($request, $params, $this);
        }
        if(isset($params['paystate']) && $params['paystate'] != 'post_payment')      
            if ($this->service->get_setting('enable_mailchimp') == 'yes')
            {
                if($this->form_options->form_is_opt_in_checkbox == 1 || (isset($this->form_options->form_is_opt_in_checkbox[0]) && $this->form_options->form_is_opt_in_checkbox[0] == 1))
                {
                    if(isset($request['rm_subscribe_mc']))
                        $this->service->subscribe_to_mailchimp($request, $this->get_form_options());
                }
                else
                    $this->service->subscribe_to_mailchimp($request, $this->get_form_options());
            }

        return null;
    }

    public function hook_post_field_addition_to_page($form, $page_no, $editing_sub=null)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_form_contact = new RM_Frontend_Form_Contact_Addon();
            return $addon_form_contact->hook_post_field_addition_to_page($form, $page_no, $this, $editing_sub);
        }
        //if (count($this->form_pages) == $page_no)
        { 
            if ($this->has_price_field())
                $this->add_payment_fields($form);
            
            if (get_option('rm_option_enable_captcha') == "yes")
                $form->addElement(new Element_Captcha());
            if ($this->service->get_setting('enable_mailchimp') == 'yes' && $this->form_options->form_is_opt_in_checkbox == 1)
            {
                //This outer div is added so that the optin text can be made full width by CSS.
                $form->addElement(new Element_HTML('<div class="rm_optin_text">'));
                
                if($this->form_options->form_opt_in_default_state == 'Checked')
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_mc', array(1 => $this->form_options->form_opt_in_text ? : RM_UI_Strings::get('MSG_SUBSCRIBE')),array("value"=>1)));
                else 
                    $form->addElement(new Element_Checkbox('', 'rm_subscribe_mc', array(1 => $this->form_options->form_opt_in_text ? : RM_UI_Strings::get('MSG_SUBSCRIBE'))));
            
                $form->addElement(new Element_HTML('</div>'));
            }
            
            if($this->form_options->show_total_price)
            {
                $gopts = new RM_Options;
                $total_price_localized_string = RM_UI_Strings::get('FE_FORM_TOTAL_PRICE');
                $curr_symbol = $gopts->get_currency_symbol();
                $curr_pos = $gopts->get_value_of('currency_symbol_position');
                $price_formatting_data = json_encode(array("loc_total_text" => $total_price_localized_string, "symbol" => $curr_symbol, "pos" => $curr_pos));
                $form->addElement(new Element_HTML("<div class='rmrow rm_total_price' style='{$this->form_options->style_label}' data-rmpriceformat='$price_formatting_data'></div>"));
            }
            
        }
    }

    public function base_render($form,$editing_sub=null)
    {
        $this->prepare_fields_for_render($form,$editing_sub);
        
        $this->prepare_button_for_render($form,$editing_sub);

        if (count($this->fields) !== 0)
            $form->render();
        else
            echo RM_UI_Strings::get('MSG_NO_FIELDS');
    }

    public function get_prepared_data_primary($request)
    {
        $data = array();

        foreach ($this->fields as $field)
        {
            if(is_array($field) && $field[0]->is_primary())
                $field = $field[0];
            if ($field->get_field_type() == 'Email' && $field->is_primary())
            {
                $field_data = $field->get_prepared_data($request);

                $data['user_email'] = (object) array('label' => $field_data->label,
                            'value' => $field_data->value,
                            'type' => $field_data->type);

                break;
            }
        }
        return $data;
    }

    public function get_prepared_data_dbonly($request,$fields=null)
    {
        $data = array();
        
        if($fields!=null):
            $this->fields= $fields;
        endif;

        foreach ($this->fields as $field)
        {
            if(is_array($field) && $field[0]->is_primary())
                $field = $field[0];
            //if (in_array($field->get_field_type(),array('Spacing','Timer')) /*$field->get_field_type() == 'HTMLH' || $field->get_field_type() == 'HTMLP'|| $field->get_field_type() == 'HTML'|| $field->get_field_type() == 'HTMLCustomized'|| */)
            if (in_array($field->get_field_type(),RM_Utilities::csv_excluded_widgets()))
            {
                continue;
            }

            $field_data = $field->get_prepared_data($request);
            /*if($field->get_field_type()=="HTMLCustomized"){
               $html_field= new RM_Fields();
               $html_field->load_from_db($field->get_field_id());
               $field_data->value= $html_field->get_field_value();
               
               if(strtolower($html_field->get_field_type())=="link")
               {    
                    $field_options=  $html_field->field_options;
                    $field_data->value= $html_field->field_options->link_type=="url" ? $html_field->field_options->link_href : get_permalink($html_field->field_options->link_page);
               }
            }*/
            
            if ($field_data === null)
                continue;

            $data[$field_data->field_id] = (object) array('label' => $field_data->label,
                        'value' => $field_data->value,
                        'type' => $field_data->type,
                        'meta' => isset($field_data->meta)?$field_data->meta:null);
        }

        return $data;
    }

}