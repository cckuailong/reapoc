<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RM_Frontend_Form_Reg extends RM_Frontend_Form_Multipage//RM_Frontend_Form_Base
{

    public $form_user_role;
    public $default_form_user_role;
    public $user_exists;
    public $user_id;
    
    public function __construct(RM_Forms $be_form, $ignore_expiration=false)
    {
        parent::__construct($be_form, $ignore_expiration);
        $this->form_user_role = $be_form->get_form_user_role();
        $this->default_form_user_role = $be_form->get_default_form_user_role();
        $this->set_form_type(RM_REG_FORM);
        $this->user_exists = false;
        $this->user_id = 0;
    }
    
    public function get_registered_user_id()
    {
        return $this->user_id;
    }
    
    //Returning false here will prevent submission in form controller.
    public function pre_sub_proc($request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_form_reg = new RM_Frontend_Form_Reg_Addon();
            return $addon_form_reg->pre_sub_proc($request, $params, $this);
        }
        $form_name = 'form_' . $this->form_id  . "_" . $this->form_number;
        
        if (!is_user_logged_in())
        { 
            $prime_data = $this->get_prepared_data_primary($request);
            
            if(!isset($prime_data['user_email'], $prime_data['username']))
                return false;
            
            $email = $prime_data['user_email']->value;
            $username = $prime_data['username']->value;
            
            if(isset($prime_data['email_confirmation'])){
                $email_conf = trim($prime_data['email_confirmation']->value);
                if($email !== $email_conf)
                {
                    RM_PFBC_Form::setError($form_name, RM_UI_Strings::get("ERR_EMAIL_MISMATCH"));
                    return false;
                }
            }
            
            if(isset($prime_data['password']))
            {
                                
                $password = $prime_data['password']->value;
                if(isset($prime_data['password_confirmation'])){
                    $password_conf = $prime_data['password_confirmation']->value;
                    if($password !== $password_conf)
                    {
                        RM_PFBC_Form::setError($form_name, RM_UI_Strings::get("ERR_PW_MISMATCH"));
                        return false;
                    }
                }   
            }            
            
            $valid_character_error= RM_Utilities::validate_username_characters($username,$this->form_id);
            if(!empty($valid_character_error) && empty($this->form_options->hide_username)){
                RM_PFBC_Form::setError($form_name, $valid_character_error);
                return false;
            }
            
            $user = get_user_by('login', $username);
            if (!empty($user))
            {
                $this->user_exists = true;
                RM_PFBC_Form::setError($form_name, RM_UI_Strings::get("USERNAME_EXISTS"));
                return false;
            } 
            
            $user = get_user_by('email', $email);


                if (!empty($user))
                {
                    $this->user_exists = true;
                    RM_PFBC_Form::setError($form_name, RM_UI_Strings::get("USERNAME_EXISTS"));
                    return false;
                } 
            
            
            RM_PFBC_Form::clearErrors($form_name);
            return true;            
        }

        return true;
    }

     public function post_sub_proc($request, $params) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_form_reg = new RM_Frontend_Form_Reg_Addon();
            return $addon_form_reg->post_sub_proc($request, $params, $this);
        }
        $prime_data = $this->get_prepared_data_primary($request);            
        $x = null;
        if (!is_user_logged_in())
        {
            if(isset($params['paystate']))
            {
                if($params['paystate'] == 'pre_payment' || $params['paystate'] == 'na')
                {
                    if(!isset($prime_data['user_email'], $prime_data['username']))
                        return false;

                    $email = $prime_data['user_email']->value;
                    $username = $prime_data['username']->value;
                    
                    $password_field= $this->service->get_primary_field_options('userpassword',$this->get_form_id());
                    if (empty($password_field))
                        $password = null;
                    else
                    {
                      if(!isset($prime_data['password']))
                          return false;
                      $password = $prime_data['password']->value;
                    }

                    if($params['paystate'] == 'pre_payment')
                        $user_id = $this->service->register_user($username, $email, $password, $this->form_id, false, null);
                    else
                        $user_id = $this->service->register_user($username, $email, $password, $this->form_id, true, null);

                    $this->user_id = $user_id;
                    update_user_meta($user_id, 'RM_UMETA_FORM_ID', $this->form_id);
                    update_user_meta($user_id, 'RM_UMETA_SUB_ID', $params['sub_detail']->submission_id);
                    $x = array('user_id'=>$user_id);
                    
                    $this->service->get_user_service()->set_user_role($user_id, get_option('default_role'));
                        
                }
                if($params['paystate'] == 'post_payment' || $params['paystate'] == 'na')
                {
                    if($params['paystate'] == 'post_payment' && (isset($params['is_paid']) && $params['is_paid']))
                            $user_id = $this->service->get_user_service()->activate_user_by_id($this->user_id);    
                    if ($this->form_options->auto_login)
                    {                       
                        $_SESSION['RM_SLI_UID'] = $this->user_id;
                        $user = get_user_by('ID',$this->user_id);
                        $login_service= new RM_Login_Service();
                        $login_service->insert_login_log(array('email'=>$user->user_email,'username_used'=>$user->user_email,'ip'=> $_SERVER['REMOTE_ADDR'],'time'=> current_time('timestamp'),'status'=>1,'type'=>'normal','result'=>'success','social_type'=>''));
                    } 
                }
            }
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
        
        return $x;
    }
    
    public function hook_pre_field_addition_to_page($form, $page_no)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_form_reg = new RM_Frontend_Form_Reg_Addon();
            return $addon_form_reg->hook_pre_field_addition_to_page($form, $page_no, $this);
        }
        //if (1 == $page_no)
        {
            if ($this->preview || !is_user_logged_in())
            { /*
             * Let users choose their role
             */

                if (!empty($this->form_options->form_should_user_pick) || !(isset($this->form_user_role) && !empty($this->form_user_role)))
                {
                    $role_pick = $this->form_options->form_should_user_pick;

                    if ($role_pick)
                    {
                        global $wp_roles;
                        $allowed_roles = array();
                        $default_wp_roles = $wp_roles->get_names();
                        $form_roles = $this->form_user_role;
                        if (is_array($form_roles) && count($form_roles) > 0)
                        {
                            foreach ($form_roles as $val)
                            {
                                if (array_key_exists($val, $default_wp_roles))
                                    $allowed_roles[$val] = $default_wp_roles[$val];
                            }
                        }

                        $role_as = empty($this->form_options->form_user_field_label) ? RM_UI_Strings::get('LABEL_ROLE_AS') : $this->form_options->form_user_field_label;

                        $form->addElement(new Element_Radio($role_as, "role_as", $allowed_roles, array("id" => "rm_", "style" => $this->form_options->style_textfield, "required" => "1","labelStyle" => $this->form_options->style_label)));
                    }
                }

                //$form->addElement(new Element_Username(RM_UI_Strings::get('LABEL_USERNAME'), "username", array("value" => "","labelStyle" => $this->form_options->style_label, "style" => $this->form_options->style_textfield, "required" => "1", "placeholder" => RM_UI_Strings::get('LABEL_USERNAME'))));
                /*
                if ($this->service->get_setting('auto_generated_password') != 'yes')
                {
                    $form->addElement(new Element_Password(RM_UI_Strings::get('LABEL_PASSWORD'), "pwd", array("required" => 1,"placeholder"=>RM_UI_Strings::get('LABEL_PASSWORD'), "id" => "rm_reg_form_pw_".$this->form_id."_".$this->form_number, "longDesc" => RM_UI_Strings::get('HELP_PASSWORD_MIN_LENGTH'), "minlength" => 7,"labelStyle" => $this->form_options->style_label, "style" => $this->form_options->style_textfield, "validation" => new Validation_RegExp("/.{7,}/", "Error: The %element% must be atleast 7 characters long."))));
                    $form->addElement(new Element_Password(RM_UI_Strings::get('LABEL_PASSWORD_AGAIN'), "password_confirmation", array("required" => 1,"placeholder"=>RM_UI_Strings::get('LABEL_PASSWORD_PH_AGAIN'),"labelStyle" => $this->form_options->style_label, "style" => $this->form_options->style_textfield, "id" => 'rm_reg_form_pw_reentry')));
                }
                 */
            }
        }
        
    }
    
    public function hook_post_field_addition_to_page($form, $page_no, $editing_sub = null)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_form_reg = new RM_Frontend_Form_Reg_Addon();
            return $addon_form_reg->hook_post_field_addition_to_page($form, $page_no, $this, $editing_sub);
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

    public function base_render($form, $editing_sub = null)
    {        
        //parent::base_render($form);

        if(defined('REGMAGIC_ADDON')) {
            $this->prepare_fields_for_render($form, $editing_sub);
            $this->prepare_button_for_render($form, $editing_sub);
        } else {
            $this->prepare_fields_for_render($form);
            $this->prepare_button_for_render($form);
        }
       
        if (count($this->fields) !== 0)
            $form->render();
        else
            echo RM_UI_Strings::get('MSG_NO_FIELDS');
    }
      
    public function get_jqvalidator_config_JS()
    {
        if(!is_user_logged_in())
        {
        $password_match_error = RM_UI_Strings::get('ERR_PW_MISMATCH');
        $email_match_error = RM_UI_Strings::get("ERR_EMAIL_MISMATCH");
        $rm_service= new RM_Services();
        $pass_field= $rm_service->get_primary_field_options('userpassword',$this->form_id);
        $email_field= $rm_service->get_primary_field_options('email',$this->form_id);
        
        if(!empty($pass_field)){
            $field_options= maybe_unserialize($pass_field->field_options);
            $password_match_error=$field_options->pass_mismatch_err; 
        }
            
        if(!empty($email_field)){
            $field_options= maybe_unserialize($email_field->field_options);
            if(!empty($field_options->email_mismatch_err))
                $email_match_error=$field_options->email_mismatch_err;
        }
            
        $form_num = $this->form_number;
        $form_id = $this->form_id;
        $form_counter= RM_Public::$form_counter;    
$str = <<<JSHD
        jQuery.validator.setDefaults({errorClass: 'rm-form-field-invalid-msg',
                                        ignore:':hidden,.ignore,:not(:visible),.rm_untouched',wrapper:'div',
                                       errorPlacement: function(error, element) {
                                                            //error.appendTo(element.closest('.rminput'));
                                                            error.appendTo(element.closest('div'));
                                                          },
                                      rules: {        
        password: {
            required: true,
            minlength: 7
        },
        password_confirmation: {
            required: true,
            equalTo: "#rm_reg_form_pw_{$form_id}_{$form_counter}"
        },
        email_confirmation: {
            required: true,
            equalTo: "#rm_reg_form_email_{$form_id}_{$form_counter}"
        }
            },
        messages: {
        password_confirmation: {
            equalTo: "{$password_match_error}"
        },
        email_confirmation: {
            equalTo: "{$email_match_error}"
        }
            }
                                    });
JSHD;
        return $str;
        }
        else
            return parent::get_jqvalidator_config_JS();
    }

    //Primary array must be indexed by some unique identifier instead of field_id.
    public function get_prepared_data_primary($request)
    {
        $data = array();           
        if(isset($this->primary_field_indices['user_email']) && isset($request[$this->primary_field_indices['user_email']]))
        {
            $field = $this->fields[$this->primary_field_indices['user_email']];
            $field_data = $field->get_prepared_data($request);

            $data['user_email'] = (object) array('label' => $field_data->label,
                            'value' => $field_data->value,
                            'type' => $field_data->type);
            // If Hidden username is configured then copying Email in Username
            $form_id= $this->get_form_id();
            if(RM_Utilities::is_username_hidden($form_id)):
                $data['username'] = (object) array('label' => RM_UI_Strings::get('LABEL_USERNAME'),
                            'value' => $field_data->value,
                            'type' => 'username');
            endif;
        }
        
        if (isset($request['pwd']))
        {
            $data['password'] = (object) array('label' => RM_UI_Strings::get('LABEL_PASSWORD'),
                        'value' => $request['pwd'],
                        'type' => 'password');
        }
        
        if (isset($request['password_confirmation']))
        {
            $data['password_confirmation'] = (object) array('label' => RM_UI_Strings::get('LABEL_PASSWORD_AGAIN'),
                        'value' => $request['password_confirmation'],
                        'type' => 'password');
        }
        
        if (isset($request['email_confirmation']))
        {
            $data['email_confirmation'] = (object) array('label' => RM_UI_Strings::get('LABEL_EMAIL_AGAIN'),
                        'value' => $request['email_confirmation'],
                        'type' => 'email');
        }

        if (isset($request['username']))
        {
            $data['username'] = (object) array('label' => RM_UI_Strings::get('LABEL_USERNAME'),
                        'value' => $request['username'],
                        'type' => 'username');
        }


        return $data;
    }

    //Make sure that this data is indexed by field_id only
    public function get_prepared_data_dbonly($request)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_form_reg = new RM_Frontend_Form_Reg_Addon();
            return $addon_form_reg->get_prepared_data_dbonly($request, $this);
        }
        $data = array();

        foreach ($this->fields as $field)
        {
           //if (in_array($field->get_field_type(),array('Spacing','Timer')))
            if (in_array($field->get_field_type(),RM_Utilities::csv_excluded_widgets()))
            {
                continue;
            }
            
            $field_data = $field->get_prepared_data($request);
           /* if($field->get_field_type()=="HTMLCustomized"){
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
                                                          'type' => $field_data->type);
        }

        return $data;
    }

    //Need to overload the method to add username and password fields as they are not included in the default fields list of the form.
    //Since this method return data for all fields, do not rely on any fixed type of indexing while iterating.
    public function get_prepared_data_all($request)
    {
        $data = parent::get_prepared_data_all($request);

        if (isset($request['password']))
        {
            $data['password'] = (object) array('label' => RM_UI_Strings::get('LABEL_PASSWORD'),
                        'value' => $request['password'],
                        'type' => 'password');
        }
        
        if (isset($request['password_confirmation']))
        {
            $data['password_confirmation'] = (object) array('label' => RM_UI_Strings::get('LABEL_PASSWORD_AGAIN'),
                        'value' => $request['password_confirmation'],
                        'type' => 'password');
        }
        
        if (isset($request['email_confirmation']))
        {
            $data['email_confirmation'] = (object) array('label' => RM_UI_Strings::get('LABEL_EMAIL_AGAIN'),
                        'value' => $request['email_confirmation'],
                        'type' => 'email');
        }

        if (isset($request['username']))
        {
            $data['username'] = (object) array('label' => RM_UI_Strings::get('LABEL_USERNAME'),
                        'value' => $request['username'],
                        'type' => 'username');
        }

        return $data;
    }
    
    public function get_allowed_roles($include_paid_roles = true) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_form_reg = new RM_Frontend_Form_Reg_Addon();
            return $addon_form_reg->get_allowed_roles($this, $include_paid_roles);
        }
    }

    //Overridden method for adding support for paid user roles.
    public function has_price_field() {
        $field_test = parent::has_price_field();
        $role_test = $this->has_paid_role();

        return ($field_test || $role_test);
    }

    public function has_paid_role() {
        if(defined('REGMAGIC_ADDON')) {
            $addon_form_reg = new RM_Frontend_Form_Reg_Addon();
            return $addon_form_reg->has_paid_role($this);
        }
    }

    //get price of a paid role.
    public function get_role_cost($role) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_form_reg = new RM_Frontend_Form_Reg_Addon();
            return $addon_form_reg->get_role_cost($role, $this);
        }
    }

    //Overridden method for adding support for paid user roles.
    public function get_pricing_detail($request) {
        $data = parent::get_pricing_detail($request);
        $label = null;
        if (isset($request['role_as']) && !empty($request['role_as'])) {
            $role_cost = $this->get_role_cost($request['role_as']);
            $label = $request['role_as'];
        } else if (!empty($this->default_form_user_role)) {
            $role_cost = $this->get_role_cost($this->default_form_user_role);
            $label = $this->default_form_user_role;
        } else
            $role_cost = null;

        $price_flag = false;

        if ($data === null) {
            $data = new stdClass;
            $data->billing = array();
            $data->total_price = 0.0;
        } else
            $price_flag = true;

        if ($role_cost) {
            $data->billing[] = (object) array('label' => $label, 'price' => $role_cost);
            $data->total_price += $role_cost;
            $price_flag = true;
        }

        return $price_flag ? $data : null;
    }
    
}