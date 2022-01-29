<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_PUBLIC_DIR . 'views/template_rm_user_form.php'); else {
/**
 * @internal Plugin Template File [For user forms]
 * 
 * This file renders the user made custom forms of the plugin on the front end
 * using the shortcode. * 
 */
?>

<div class="rmagic">



    <?php
    if ($data->fields_data)
    {
        $form = new RM_PFBC_Form('form_' . $data->form->form_id);


        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery", "focus"),
            "action" => "",
            "class" => "rmagic-form"
        ));

//Check if user exists
        if ($data->user_exists)
        {
            echo '<div class="rm_user_exists_alert">' . RM_UI_Strings::get('USER_EXISTS') . '</div>';
        }
        ?>
        <!--Dialogue Box Starts-->
        <div class="rmcontent">
            <?php
//Create stat_id field
            if ($data->stat_id)
            {
                $form->addElement(new Element_HTML('<div id="rm_stat_container" style="display:none">'));
                $form->addElement(new Element_Number('RM_Stats','stat_id', array('value'=>$data->stat_id, 'style'=>'display:none')));
                $form->addElement(new Element_HTML('</div>'));
            }

            if (!empty($data->form->form_options->form_custom_text))
                $form->addElement(new Element_HTML('<div class="rmheader">' . $data->form->form_options->form_custom_text . '</div>'));

            $form->addElement(new Element_HTML("<div class='rm_input_fields_container'>"));


// Check if registration fields required
            if ($data->form->get_form_type() == 1 && !is_user_logged_in())
            {

                /*
                 * Let users choose their role
                 */
                if (isset($data->allowed_roles) && !empty($data->allowed_roles))
                {
                    $form->addElement(new Element_Radio("<b>" . $data->role_as . ":</b>", "role_as", $data->allowed_roles, array("id" => "rm_", "required" => "1")));
                }

                require_once(__DIR__ . '/template_rm_register.php');
            }


            foreach ($data->fields_data as $field_data)
            {
                if ($field_data->field_type !== 'Password' || !$data->is_auto_generate)
                {

                    $element_class = "Element_" . $field_data->field_type;
                    $label = $field_data->field_label;
                    if ($field_data->field_type == 'Price')
                        $name = $field_data->field_type . "_" . $field_data->field_id . "_" . $field_data->field_value;
                    else
                        $name = $field_data->field_type . "_" . $field_data->field_id;

                    if (isset($field_data->field_value))
                        if ($field_data->field_type == 'Select')
                        {
                            $options = array();
                            $tmp_options = RM_Utilities::trim_array(explode(',', $field_data->field_value));
                            foreach ($tmp_options as $val)
                                $options[$val] = $val;
                        } elseif ($field_data->field_type == 'Radio' || $field_data->field_type == 'Checkbox' || $field_data->field_type == 'Terms')
                            $options = maybe_unserialize($field_data->field_value);
                        else
                            $options = $field_data->field_value;
                    else
                        $options = null;

                    if (isset($field_data->field_options))
                    {

                        $field_data->field_options = maybe_unserialize($field_data->field_options);
                    }


                    //var_dump($data->form->form_options);die;
                    if ($field_data->field_type === 'HTMLH' || $field_data->field_type === 'HTMLP')
                        $form->addElement(new $element_class($options));

                    elseif ($field_data->field_type == 'Price')
                    {
                        //$options = array("paypal_field_id"=>$options);
                        $price_field_id = (int) $field_data->field_value;

                        //////////// New APppPPPPPPpPPppPPPPpp
                        $paypal_field = new RM_PayPal_Fields();
                        $res = $paypal_field->load_from_db($price_field_id);

                        if (!$res)
                        {
                            $options = null;
                            continue;
                        }

                        $form->addElement(new Element_Hidden('rm_payment_form', $options));

                        // echo "<pre>", var_dump($field_data->properties),"</pre>"; 
                        $properties = array();

                        if (isset($field_data->properties['required']))
                            $properties['required'] = '1';
                        //echo '<pre>'; var_dump($field_data); die;
                        switch ($paypal_field->get_type())
                        {
                            case "fixed":
                                if ($data->currency_pos == 'before')
                                    $properties['value'] = $paypal_field->get_name() . " (" . $data->curr_symbol . " " . $paypal_field->get_value() . ")";
                                else
                                    $properties['value'] = $paypal_field->get_name() . " (" . $paypal_field->get_value() . " " . $data->curr_symbol . ")";
                                $properties['readonly'] = 1;
                                $properties['class'] = $paypal_field->get_class();
                                if ($paypal_field->get_extra_options() != 'yes')
                                    $element = new Element_Hidden($name, $label);
                                else
                                    $element = new Element_Textbox($label, $name, $properties);
                                break;

                            case "userdef":
                                if (isset($properties['readonly']))
                                    unset($properties['readonly']);
                                if (isset($properties['value']))
                                    unset($properties['value']);
                                $properties['class'] = $paypal_field->get_class();
                                $properties['placeholder'] = $paypal_field->get_name();
                                $properties['class'] = $paypal_field->get_class();
                                $properties['min'] = 0.01;
                                $properties['step'] = 0.01;
                                $element = new Element_Number($label, $name, $properties);
                                break;


                            case "multisel":
                            case "dropdown":
                                //echo '<pre>'; var_dump($paypal_field); die;
                                $labels = maybe_unserialize($paypal_field->get_option_label());
                                $prices = maybe_unserialize($paypal_field->get_option_price());
                                $vals = array();
                                $i = 0;
                                foreach ($prices as $price)
                                {
                                    {
                                        if ($data->currency_pos == 'before')
                                            $vals["_" . $i] = $labels[$i] . " (" . $data->curr_symbol . " " . $price . ")";
                                        else
                                            $vals["_" . $i] = $labels[$i] . " (" . $price . " " . $data->curr_symbol . ")";
                                    }

                                    $i++;
                                }


                                $properties['id'] = 'id_rm_multisel_paypal_field';

                                if ($paypal_field->get_type() == 'multisel')
                                    $element = new Element_Checkbox($label, $name, $vals, $properties);
                                else
                                {
                                    if (!isset($field_data->properties['required']))
                                        $vals = array(null => RM_UI_Strings::get('SELECT_FIELD_FIRST_OPTION')) + $vals;
                                    $element = new Element_Select($label, $name, $vals, $properties);
                                }
                                break;
                            /*
                              case "dropdown":
                              $element = new Element_Select($label, $name, $properties, $others);
                              break;
                             */
                        }

                        $form->addElement($element);
                        $options = null;
                        continue;
                    }
                    else if ($field_data->field_type == 'Select')
                    {
                        $options = array(null => RM_UI_Strings::get('SELECT_FIELD_FIRST_OPTION')) + $options;
                        $form->addElement(new $element_class($label, $name, $options, $field_data->properties));
                    } else if ($field_data->field_type == 'Radio' || $field_data->field_type == 'Checkbox' || $field_data->field_type == 'Terms')
                    {
                        $form->addElement(new $element_class($label, $name, $options, $field_data->properties));
                    } else if ($field_data->field_type === 'File')
                    {
                        $form->addElement(new $element_class($label, $name, $field_data->properties));
                        $form->addElement(new Element_Hidden($name, "__RM"));
                    } else
                        $form->addElement(new $element_class($label, $name, $field_data->properties));

                    $options = null;
                }
            }

// Checking if captcha is enabled
            if (get_option('rm_option_enable_captcha') == "yes")
                $form->addElement(new Element_Captcha());
            //if mailchimp's opt in box is enabled
            if ($data->is_mailchimp_enabled == true && $data->form->form_options->form_is_opt_in_checkbox == 1)
            {
                $form->addElement(new Element_Checkbox('', 'rm_subscribe_mc', array(1 => $data->form->form_options->form_opt_in_text ? : RM_UI_Strings::get('MSG_SUBSCRIBE'))));
            }
          

            $form->addElement(new Element_HTML('</div>'));


            // If submit button label given in form configuration
            $btn_label = $data->form->form_options->form_submit_btn_label;
            $form->addElement(new Element_Button($btn_label != "" ? $btn_label : __('Submit', 'custom-registration-form-builder-with-submission-manager'), "submit", array("bgColor" => "#$data->submit_btn_bgcolor", "fgColor" => "#$data->submit_btn_fgcolor")));

            if ($data->expired)
                if ($data->form->form_options->form_message_after_expiry)
                    echo $data->form->form_options->form_message_after_expiry;
                else
                    echo RM_UI_Strings::get('MSG_FORM_EXPIRY');
            else
            {
                //Expiry drama
                $exp_str = '<div class="rm_expiry_stat_container">';
                if ($data->expiry_details->state !== 'perpetual')
                {
                    if ($data->expiry_details->state === 'expired')
                        $exp_str .= '<div class="rm-formcard-expired">' . __('Expired','custom-registration-form-builder-with-submission-manager'). '</div>';
                    else
                    {
                        switch ($data->expiry_details->criteria)
                        {
                             case 'both':
                                $exp_str .= '<div class="rm-formcard-expired">' .sprintf(esc_html__('%d out of %d filled and %d days to go','custom-registration-form-builder-with-submission-manager' ),($data->expiry_details->sub_limit - $data->expiry_details->remaining_subs),$data->expiry_details->sub_limit,$data->expiry_details->remaining_days).' </div>';

                            case 'subs':
                                $total = $data->expiry_details->sub_limit;
                                $rem = $data->expiry_details->remaining_subs;
                                $wtot = 100;
                                $rem = ($rem * 100) / $total;
                                $done = 100 - $rem;
                                
                                if ($data->expiry_details->criteria == 'subs')
                                    $exp_str .= '<div class="rm-formcard-expired">' . sprintf(esc_html__('%d out of %d filled.','custom-registration-form-builder-with-submission-manager'),($data->expiry_details->sub_limit - $data->expiry_details->remaining_subs),$data->expiry_details->sub_limit).'</div>';
                                break;

                            case 'date':
                                $exp_str .= '<div class="rm-formcard-expired">' .sprintf(esc_html__('%d days to go.','custom-registration-form-builder-with-submission-manager'),$data->expiry_details->remaining_days). '</div>';
                                break;
                        }
                    }

                    $exp_str .= '</div>';
                    echo $exp_str;
                }
                /*                 * ****** End expiry drama ************ */

                $form->render();
            }
            ?>
        </div>
        <?php
    } else
        echo RM_UI_Strings::get('MSG_NO_FIELDS');
    ?>

</div>

<?php   
}