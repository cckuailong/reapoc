<?php
/**
 * Description of RM_Email_Service
 *
 * @author CMSHelplive
 */
class RM_Email_Service
{
    /*
     * Sending submission details to admin
     */                    
    public static function notify_submission_to_admin($params,$token='')
    {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Email_Service_Addon::notify_submission_to_admin($params,$token);
        }
        $gopt = new RM_Options();
        $rm_email= new RM_Email();
        
        $notification_msg= self::get_notification_message($params->form_id,'form_admin_ns_notification'); 
     
        $email_content='';
        $user_email = '';
        /*
         * Loop through serialized data for submission
         */
        if (is_array($params->sub_data)) {
            foreach ($params->sub_data as $field_id => $val) {
                $email_content .= '<div class="rm-email-content-row-new"> <span class="key">' . $val->label . ':</span>';

                if (is_array($val->value)) {
                    $values = '';
                    // Check attachment type field
                    if (isset($val->value['rm_field_type']) && $val->value['rm_field_type'] == 'File') {
                        unset($val->value['rm_field_type']);

                        /*
                         * Grab all the attachments as links
                         */
                        foreach ($val->value as $attachment_id) {
                            $values .= wp_get_attachment_link($attachment_id) . '    ';
                        }

                        $email_content .= '<span class="key-val">' . $values . '</span><br/>';
                    }elseif (isset($val->value['rm_field_type']) && $val->value['rm_field_type'] == 'Address'){
                        unset($val->value['rm_field_type']);
                        foreach($val->value as $in =>  $value){
                           if(empty($value))
                               unset($val->value[$in]);
                        }
                        $email_content .= '<span class="key-val">' . implode(', ', $val->value) . '</span><br/>';
                    } elseif ($val->type == 'Checkbox') {   
                         $email_content .= '<span class="key-val">' . implode(', ',RM_Utilities::get_lable_for_option($field_id, $val->value)) . '</span><br/>';
                    }else {
                        $email_content .= '<span class="key-val">' . implode(', ', $val->value) . '</span><br/>';
                    }
                } else {
                    $primary_fields= RM_DBManager::get_primary_fields_id($params->form_id,'email');
                    if ($val->type == 'Email' && $user_email=='' && in_array($field_id,$primary_fields)){
                        $user_email = $val->value;
                    }
                    if ($val->type == 'Radio' || $val->type == 'Select') {   
                       $email_content .= '<span class="key-val">' . RM_Utilities::get_lable_for_option($field_id, $val->value). '</span><br/>';
                    }
                    else
                        $email_content .= '<span class="key-val">' . $val->value . '</span><br/>';
                }

                 $email_content .= "</div>";
            }
        }
        /*
          Set unique token */
        if ($token) {
            $email_content .= '<div class="rm-email-content-row"> <span class="key">' . RM_UI_Strings::get('LABEL_UNIQUE_TOKEN_EMAIL') . ':</span>';
            $email_content .= '<span class="key-val">' . $token . '</span><br/>';
            $email_content .= "</div>";
        }

        $notification_msg= str_replace('{{SUBMISSION_DATA}}', $email_content, $notification_msg);
        
        $is_wc_fields = 0;
        
        $service = new RM_Services();
        $fields = $service->get_all_form_fields($params->form_id);
        foreach($fields as $field){
            if($field->field_type=='WCBilling'){
                $is_wc_fields = 1;
            }else if($field->field_type=='WCShipping'){
                $is_wc_fields = 1;
            }else if($field->field_type=='WCBillingPhone'){
                $is_wc_fields = 1;
            }
        }
        
        $user_details_by_email = get_user_by( 'email', $user_email );
        
        $form = new RM_Forms();
        $form->load_from_db($params->form_id);
        
        if($is_wc_fields==1 && $form->get_form_type()!=1 && !$user_details_by_email){
            $notification_msg .= $notification_msg.'<br><br>Note: Billing/ Shipping/ Phone number field was not update in customer profile since this user is not registered on your site yet.';
        }
        
        $rm_email->message($notification_msg);
        // Prepare recipients

        $to = array();
        $header = '';

        if ($gopt->get_value_of('admin_notification') == "yes") {
            $to = explode(',',$gopt->get_value_of('admin_email'));
        }
    
        $subject= $form->form_options->form_admin_ns_notification_sub;
        if(empty($subject))
            $subject = $params->form_name . " " . RM_UI_Strings::get('LABEL_NEWFORM_NOTIFICATION') . " ";
        $rm_email->subject($subject);
        $rm_email->useAdminFrom= false;
        
        $from_email= $gopt->get_value_of('an_senders_email');
        $from_email= trim($from_email);
        if($from_email=="{{useremail}}"){
            $primary_fields= RM_DBManager::get_primary_fields_id($params->form_id,'email');
            if(count($primary_fields)){
                $from_email= isset($params->sub_data[$primary_fields[0]]) ? $params->sub_data[$primary_fields[0]]->value : '';
            }
        }
        $disp_name= $gopt->get_value_of('an_senders_display_name'); 
        $dname= '';
        if(stristr($disp_name, '{{user}}')){
            $sub_data= $params->sub_data;
            $first_name='';
            $last_name='';
            $user_email;
            if(!empty($sub_data)){
                foreach($sub_data as $fdata){
                     if($fdata->type=='Fname'){
                        $first_name=  $fdata->value;
                     } else if($fdata->type=='Lname'){
                         $last_name=  $fdata->value;
                     }  
                }
            }
            $dname= $first_name.' '.$last_name;
            if(trim($dname)==''){
                $primary_fields= RM_DBManager::get_primary_fields_id($params->form_id,'email');
                $dname= isset($params->sub_data[$primary_fields[0]]) ? $params->sub_data[$primary_fields[0]]->value : '';
            }
        }
        $disp_name= str_replace('{{user}}', $dname, $disp_name);
        if(empty($disp_name))
        {
            $disp_name= get_bloginfo('name', 'display');
        }

       // $from_email = $disp_name . " <" . $from_email . ">";
        $rm_email->set_from_name($disp_name);
        $rm_email->from($from_email);
       
          
        foreach($to as $recepient)
        {
            $rm_email->to($recepient);
            if($rm_email->send())
                $params->sent_successfully = true;     
            else
                $params->sent_successfully = false;     

            self::save_sent_emails($params,$rm_email,RM_EMAIL_POSTSUB_ADMIN);
        }
        
    }
    /*
     * Sending Username and Password credentials on new user registration.
     */
    public static function notify_new_user($params,$user_id=0)
    {
        // Check if it is disabled from custom filter
        $enabled = apply_filters('rm_new_user_enabled',true,$params);
        if(empty($enabled))
            return;
        $gopt = new RM_Options();
        $rm_email= new RM_Email();
        if(!empty($user_id)) {
            $login_service= new RM_Login_Service;
            $recovery_options= $login_service->get_recovery_options();
            $page_id= $recovery_options['recovery_page'];
            if(!empty($page_id)){
                $recovery_link= get_permalink($page_id);
                $token= wp_generate_password(8,false);
                update_user_meta($user_id,'rm_pass_token',$token);
                $hours= $recovery_options['rec_link_expiry'];
                if(!empty($hours)){
                    update_user_meta($user_id,'rm_pass_expiry_token',time() + ($hours*3600));
                } else {
                    update_user_meta($user_id,'rm_pass_expiry_token',0);
                }
                $recovery_link= add_query_arg('reset_token',$token,$recovery_link);
            } else {
                $recovery_link= '';
            }
        } else {
            $recovery_link= '';
        }
        $notification_msg= self::get_notification_message($params->form_id,'form_nu_notification'); 
        $notification_msg = str_replace('{{SITE_NAME}}', get_bloginfo('name', 'display'), $notification_msg);
        $notification_msg = str_replace('%SITE_NAME%', get_bloginfo('name', 'display'), $notification_msg);
        
        $notification_msg = str_replace('{{USER_NAME}}', sanitize_text_field($params->username), $notification_msg);
        $notification_msg = str_replace('%USER_NAME%', sanitize_text_field($params->username), $notification_msg);
        
        $notification_msg = str_replace('{{USER_PASS}}', $params->password, $notification_msg);
        $notification_msg = str_replace('%USER_PASS%', $params->password, $notification_msg);
        $notification_msg = str_replace('{{SITE_URL}}',site_url(), $notification_msg);
        $notification_msg = str_replace('{{SITE_ADMIN}}',get_option('admin_email'),$notification_msg);
        
        $notification_msg = str_replace('{{PASS_RESET_LINK}}',$recovery_link,$notification_msg);
        $notification_msg = apply_filters('rm_new_user_message',$notification_msg,$params);
        $rm_email->message($notification_msg);
        
        $form= new RM_Forms();
        $form->load_from_db($params->form_id);        
        $form_options= $form->form_options;
        
        $subject= $form_options->form_nu_notification_sub;
        if(empty($subject))
            $subject= RM_UI_Strings::get('MAIL_NEW_USER_DEF_SUB');
        $rm_email->subject($subject);
        $rm_email->to($params->email);
        $rm_email->from($gopt->get_value_of('senders_email_formatted'));
        $rm_email->send();
    }
    
    /*
     * Sending user activation link to admin
     */
    public static function notify_admin_to_activate_user($params)
    {
        if(defined('REGMAGIC_ADDON')) {
            return RM_Email_Service_Addon::notify_admin_to_activate_user($params);
        }
        // Check if it is disabled from custom filter
        $enabled = apply_filters('rm_user_activation_link_to_admin',true,$params);
        if(empty($enabled))
            return;
        
        $gopt = new RM_Options();
        $rm_email= new RM_Email();
        $user_email = $params->email;
        
        if(isset($params->form_id))        
        	$notification_msg= RM_Email_Service::get_notification_message($params->form_id,'form_activate_user_notification'); 
        else	
        	$notification_msg= RM_Email_Service::get_notification_message('social_media','form_activate_user_notification');
        
        $notification_msg = str_replace('{{SITE_NAME}}', get_bloginfo('name', 'display'), $notification_msg);
        $notification_msg = str_replace('%SITE_NAME%', get_bloginfo('name', 'display'), $notification_msg);
        
        if(isset($params->username)){
            $notification_msg = str_replace('{{USER_NAME}}', $params->username, $notification_msg);
            $notification_msg = str_replace('%USER_NAME%', $params->username, $notification_msg);
        } else {        
            $notification_msg = str_replace('{{USER_NAME}}', '', $notification_msg);
            $notification_msg = str_replace('%USER_NAME%','', $notification_msg);        
        }
        
        if(isset($params->email)) {
            $notification_msg = str_replace('{{USER_EMAIL}}', $user_email, $notification_msg);
            $notification_msg = str_replace('%USER_EMAIL%', $user_email, $notification_msg);
        } else {
            $notification_msg = str_replace('{{USER_EMAIL}}', '', $notification_msg);
            $notification_msg = str_replace('%USER_EMAIL%', '', $notification_msg);
        }
         
        $notification_msg = str_replace('{{ACTIVATION_LINk}}', $params->link, $notification_msg);
        $notification_msg = str_replace('%ACTIVATION_LINk%', $params->link, $notification_msg);
        //Fix for lower case 'k'
        $notification_msg = str_replace('{{ACTIVATION_LINK}}', $params->link, $notification_msg);
        $notification_msg = str_replace('%ACTIVATION_LINK%', $params->link, $notification_msg);
        $notification_msg = apply_filters('rm_user_activation_msg_to_admin',$notification_msg,$params);
        $rm_email->message($notification_msg);
        
        $form= new RM_Forms();
        $form->load_from_db($params->form_id);        
        $form_options= $form->form_options;
        
        $subject=$form_options->form_activate_user_notification_sub;
        if(empty($subject))
            RM_UI_Strings::get('MAIL_ACTIVATE_USER_DEF_SUB');
        $rm_email->subject($subject);
        $rm_email->to(get_option('admin_email'));
        $rm_email->from($gopt->get_value_of('senders_email_formatted'));
        
        if($rm_email->send())
            $params->sent_successfully = true;     
        else
            $params->sent_successfully = false;
        self::save_sent_emails($params,$rm_email,RM_EMAIL_USER_ACTIVATION_ADMIN);
        
    }
    /*
     *  Send auto reponder message to user on new submission
     */
    public static function auto_responder($params,$token='')
    {
        $gopt = new RM_Options();
        $rm_email= new RM_Email();

       
        $email_content = '<div class="mail-wrapper">';
        /* Preparing content for front end notification */
        $email_content .= wpautop($params->email_content) . '<br><br>';
        
        // Replacing Username and password
        if(!empty($params->req['username'])){
            $email_content = str_replace('{{Username}}',$params->req['username'], $email_content);
        }
        if(!empty($params->req['pwd'])){
            $email_content = str_replace('{{UserPassword}}',$params->req['pwd'], $email_content);
        }
        if(isset($params->total_price)){
            $email_content = str_replace('{{TOTAL_AMOUNT}}',RM_Utilities::get_formatted_price($params->total_price), $email_content);
        }
        
        /*
          Set unique token */
        if ($token) {
            $email_content .= '<div class="rm-email-content-row"> <span class="key">' . RM_UI_Strings::get('LABEL_UNIQUE_TOKEN_EMAIL') . ':</span>';
            $email_content .= '<span class="key-val">' . $token . '</span><br/>';
            $email_content .= "</div>";
        }

        foreach ($params->req as $key => $val) {
            //echo "<pre", var_dump($request->req),die;
            $key_parts = explode('_', $key);
            if (!is_array($val)){                    
                if ($key_parts[0] == 'File' || $key_parts[0] == 'Image') {

                    $field_id = $key_parts[1];
                    //Try to find value in db_data if provided.                        
                    $values='';
                    if(isset($params->db_data, $params->db_data[$field_id]))
                    {
                        /*
                        * Grab all the attachments as links
                        */
                        if(is_array($params->db_data[$field_id]->value) && count($params->db_data[$field_id]->value)>0)
                            foreach ($params->db_data[$field_id]->value as $attachment_id) {
                                if($attachment_id != 'File')
                                $values .= wp_get_attachment_link($attachment_id) . '    ';
                            }

                    }

                    $email_content = str_replace('{{' . $key . '}}', $values, $email_content);

                }
                elseif ($key_parts[0] == 'Radio' || $key_parts[0] == 'Select') {   
                   $values = '';
                   $values =  RM_Utilities::get_lable_for_option($key_parts[1], $val);
                   $email_content = str_replace('{{' . $key . '}}', $values, $email_content);
                }
                else
                    $email_content = str_replace('{{' . $key . '}}', $val, $email_content);                   
            }
            else {
                if (isset($val['rm_field_type']) && $val['rm_field_type'] == 'Address'){
                unset($val['rm_field_type']);
                            foreach ($val as $in => $value) {
                                if (empty($value))
                                    unset($val[$in]);
                            }
                }
                elseif ($key_parts[0] == 'Checkbox') {   
                     $val = RM_Utilities::get_lable_for_option($key_parts[1], $val);
                }
                $email_content = str_replace('{{' . $key . '}}', implode(', ', $val), $email_content);
            }
        }

        $out = array();
        $preg_result = preg_match_all('/{{(.*?)}}/', $email_content, $out);

        if ($preg_result) {
            $id_vals = array();

            foreach ($params->req as $key => $val) {
                //$val would be like '{field_type}_{field_id}'

                $key_parts = explode('_', $key);
                $k_c = count($key_parts);
                if ($k_c >= 2 && is_numeric($key_parts[$k_c - 1])) {
                    if (is_array($val))
                        $val = implode(", ", $val);

                    if ($key_parts[0] === 'Fname' || $key_parts[0] === 'Lname' || $key_parts[0] === 'BInfo') {
                        $id_vals[$key_parts[0]] = $val;
                    } else
                        $id_vals[$key_parts[1]] = $val;
                }
            }

            foreach ($out[1] as $caught) {
                //echo "<br>".$caught;$parameters
                $x = explode("_", $caught);
                $id = $x[count($x) - 1];
                if (is_numeric($id)) {
                    if (isset($id_vals[(int) $id]))
                        $email_content = str_replace('{{' . $caught . '}}', $id_vals[(int) $id], $email_content);
                }
                else {
                    switch ($caught) {
                        case 'first_name':
                            if (isset($id_vals['Fname']))
                                $email_content = str_replace('{{' . $caught . '}}', $id_vals['Fname'], $email_content);
                            break;

                        case 'last_name':
                            if (isset($id_vals['Lname']))
                                $email_content = str_replace('{{' . $caught . '}}', $id_vals['Lname'], $email_content);
                            break;

                        case 'description':
                            if (isset($id_vals['BInfo']))
                                $email_content = str_replace('{{' . $caught . '}}', $id_vals['BInfo'], $email_content);
                            break;
                    }
                }

                //Blank the placeholder if still any remaining.
                $email_content = str_replace('{{' . $caught . '}}', '', $email_content);
            }
        }
        
        $email_content .=  "</div>";
        $email_content= do_shortcode(wpautop($email_content));
        $rm_email->message($email_content);
        // Prepare recipients
        $rm_email->subject($params->email_subject? : RM_UI_Strings::get('MAIL_REGISTRAR_DEF_SUB'));
        $rm_email->to($params->email);
        $rm_email->from($gopt->get_value_of('senders_email_formatted'));
        
        if($rm_email->send())
            $params->sent_successfully = true;     
        else
            $params->sent_successfully = false;     
        
        self::save_sent_emails($params,$rm_email,RM_EMAIL_AUTORESP);
        
    }
    
    /*
     * Send notification to user as soon as account is activated.
     */
    public static function notify_user_on_activation($params)
    {
        $gopt = new RM_Options();
        $rm_email= new RM_Email();
        $notification_msg= self::get_notification_message($params->form_id,'form_user_activated_notification'); 
        $notification_msg = str_replace('{{SITE_NAME}}',get_bloginfo('name', 'display'), $notification_msg);
        $notification_msg = str_replace('%SITE_NAME%',get_bloginfo('name', 'display'), $notification_msg);
        
        $notification_msg = str_replace('{{SITE_URL}}',get_site_url(),$notification_msg);
        $notification_msg = str_replace('{{SITE_ADMIN}}',get_option('admin_email'),$notification_msg);
        $notification_msg = str_replace('%SITE_URL%',get_site_url(),$notification_msg);
        $notification_msg= do_shortcode(wpautop($notification_msg));
        $rm_email->message($notification_msg);
        $form= new RM_Forms();
        $form->load_from_db($params->form_id);        
        $form_options= $form->form_options;
        $subject= $form_options->form_user_activated_notification_sub;
        if(empty($subject))
            $subject= RM_UI_Strings::get('MAIL_ACOOUNT_ACTIVATED_DEF_SUB');
        $rm_email->subject($subject);
        $rm_email->to($params->email);
        $rm_email->from($gopt->get_value_of('senders_email_formatted'));
        
        if($rm_email->send())
            $params->sent_successfully = true;     
        else
            $params->sent_successfully = false;     
        
        self::save_sent_emails($params,$rm_email,RM_EMAIL_USER_ACTIVATED_USER);
        
    }
    
    /*
     * Quickly send generic emails, used on user view page, back-end.
     */
    public static function quick_email($params)
    {
        $gopt = new RM_Options();
        $rm_email= new RM_Email();
        
        $rm_email->message($params->message);
        $rm_email->subject($params->subject);
        $rm_email->to($params->to);
        $rm_email->from($gopt->get_value_of('senders_email_formatted'));
        
        if($rm_email->send())
            $params->sent_successfully = true;     
        else
            $params->sent_successfully = false;
        
        if(!$params->do_not_save)
        self::save_sent_emails($params,$rm_email,$params->type);
    }
        
    public static function save_sent_emails($params,$rm_email,$type)
    {
            
        $additional_data = array();
        if(isset($params->sub_id))
            $additional_data['exdata'] = $params->sub_id;
        if(isset($params->form_id))
            $additional_data['form_id'] = $params->form_id;      
        
        $sent_on = gmdate('Y-m-d H:i:s');  
        $form_id = null;
        $exdata = null;
        $was_sent_successfully = (isset($params->sent_successfully) && $params->sent_successfully) ? 1 : 0 ;
        if(is_array($additional_data) && count($additional_data) > 0)
        {
            if(isset($additional_data['form_id'])) $form_id = $additional_data['form_id'];
            if(isset($additional_data['exdata'])) $exdata = $additional_data['exdata'];
        }
        $row_data = array('type' => $type, 'to' => $rm_email->get_to(), 'sub' => htmlspecialchars($rm_email->get_subject()), 'body' => htmlspecialchars($rm_email->get_message()), 'sent_on' => $sent_on, 'headers' => $rm_email->get_header(), 'form_id' => $form_id,'exdata' => $exdata,'was_sent_success' => $was_sent_successfully);
        $fmts = array('%d','%s','%s','%s','%s', '%s', '%d', '%s', '%d');

        RM_DBManager::insert_row('SENT_EMAILS', $row_data, $fmts);
    }
    
    public static function get_notification_message($form_id,$type)
    {
        $form= new RM_Forms();
        
        if(defined('REGMAGIC_ADDON')) {
            if($form_id=='social_media')
                return RM_Email_Service::get_default_messages($type);
        }
        
        $form->load_from_db($form_id);
        if(isset($form->form_options->$type) && trim($form->form_options->$type)!="")
            return wpautop($form->form_options->$type);
        else
            return wpautop(self::get_default_messages($type));
    }
    
    public static function get_default_messages($type)
    {   
        $email_content= '';
        if($type=="form_nu_notification")
        {
            $email_content = '<div class="mail-wrapper">'.RM_UI_Strings::get('MAIL_BODY_NEW_USER_NOTIF').'</div>';
        }elseif($type=="form_user_activated_notification")
        {
             $email_content = '<div style="font-size:14px">';
             $email_content .=  RM_UI_Strings::get('MAIL_ACCOUNT_ACTIVATED');
             $email_content .= '</div>';
        }elseif($type=="form_activate_user_notification")
        {
            $email_content = '<div style="font-size:14px">';
            $email_content .= '<div class="mail-wrapper" style="border: 1px solid black; padding: 20px; box-shadow: .1px .1px 8px .1px grey; font-size: 14px; font-family: monospace;"> <div class="mail_body" style="padding: 20px;">' . RM_UI_Strings::get('MAIL_NEW_USER1') . '.<br/> ' . RM_UI_Strings::get('LABEL_USER_NAME') . ' : {{USER_NAME}} <br/> ' . RM_UI_Strings::get('LABEL_USEREMAIL') . ' : {{USER_EMAIL}} <br/> <br/>' . RM_UI_Strings::get('MAIL_NEW_USER2') . '<br/> <div class="rm-btn-link" style="width: 100%; text-align: center; margin-top: 10px; margin-bottom: 15px;"><a class="rm_btn" href="{{ACTIVATION_LINk}}" style="border: 1px solid; padding: 4px; background-color: powderblue; box-shadow: 1px 1px 3px .1px;">'.__("Activate",'custom-registration-form-builder-with-submission-manager').'</a></div> <div class="link-div" style="border: 1px dotted; padding: 13px; background-color: white; margin-top: 4px; width: 100%;"> ' . RM_UI_Strings::get('MAIL_NEW_USER3') . '.<br/> <a class="rm-link" href="{{ACTIVATION_LINk}}" style="color: blue; font-size: 11px;">{{ACTIVATION_LINk}}</a> </div> </div> </div>';            
            $email_content .= '</div>';
        } elseif($type=='form_admin_ns_notification')
        {
            $email_content= '{{SUBMISSION_DATA}}';
        }
        
        if(defined('REGMAGIC_ADDON')) {
            if($type=='act_link_message'){
                $email_content = '<div style="font-size:14px">';
                $email_content .= RM_UI_Strings::get('DEFAULT_ACT_LINK_MSG_VALUE');
                $email_content .= '</div>';
            }
        }
        
        return $email_content;
    }
    
    public static function send_activation_link($user_id){
        if(defined('REGMAGIC_ADDON'))
            return RM_Email_Service_Addon::send_activation_link($user_id);
    }
    
    public static function send_2fa_otp($options){
        if(defined('REGMAGIC_ADDON'))
            return RM_Email_Service_Addon::send_2fa_otp($options);
    }
    
    public static function notify_failed_login_to_user($user){
        $login_service= new RM_Login_Service;
        $template_options= $login_service->get_template_options();
        $message= wpautop(str_replace(array('{{username}}','{{sitename}}','{{Login_IP}}','{{login_time}}'),array($user->user_login,get_bloginfo('title'),$_SERVER["REMOTE_ADDR"], RM_Utilities::get_current_time(current_time('timestamp'))),$template_options['failed_login_err']));
        $rm_email= new RM_Email();
        $rm_email->message($message);
        $rm_email->subject(__("Failed login Attempt",'custom-registration-form-builder-with-submission-manager'));
        $rm_email->to($user->user_email);
        $gopt = new RM_Options();
        $rm_email->from($gopt->get_value_of('admin_email'));
        $rm_email->send();
    }
    
    public static function notify_failed_login_to_admin($user){
        $login_service= new RM_Login_Service;
        $template_options= $login_service->get_template_options();
        $message= wpautop(str_replace(array('{{username}}','{{sitename}}','{{Login_IP}}','{{login_time}}'),array($user->user_login,get_bloginfo('title'),$_SERVER["REMOTE_ADDR"], RM_Utilities::get_current_time(current_time('timestamp'))),$template_options['failed_login_err_admin']));
        $rm_email= new RM_Email();
        $rm_email->message($message);
        $rm_email->subject(__("Failed login Attempt",'custom-registration-form-builder-with-submission-manager'));
        $gopt = new RM_Options();
        $rm_email->to($gopt->get_value_of('admin_email'));
        $rm_email->send();
    }
    
    public static function notify_admin_on_ip_ban($args){
       $login_service= new RM_Login_Service;
        $template_options= $login_service->get_template_options();
        $message= wpautop(str_replace(array('{{login_IP}}','{{ban_period}}','{{ban_trigger}}'),array($_SERVER["REMOTE_ADDR"],$args['ban_period'],$args['ban_trigger']),$template_options['ban_message_admin']));
        $rm_email= new RM_Email();
        $rm_email->message($message);
        $rm_email->subject(__("IP Blocked",'custom-registration-form-builder-with-submission-manager'));
        $gopt = new RM_Options();
        $rm_email->to($gopt->get_value_of('admin_email'));
        $rm_email->send();
    }
    
    public static function notify_lost_password_token($user){
        $login_service= new RM_Login_Service;
        $gopt = new RM_Options();
        $template_options= $login_service->get_template_options();
        $recovery_options= $login_service->get_recovery_options();
        $username= $user->user_login;
        $page_id= $recovery_options['recovery_page'];
        if(empty($page_id)){
            return false;
        }
        
        $recovery_link= get_permalink($page_id);
        $token= wp_generate_password(8,false );
        update_user_meta($user->ID,'rm_pass_token',$token);
        $hours= $recovery_options['rec_link_expiry'];
        if(!empty($hours)){
            update_user_meta($user->ID,'rm_pass_expiry_token',time() + ($hours*3600));
        }
        else{
            update_user_meta($user->ID,'rm_pass_expiry_token',0);
        }
        $recovery_link= esc_url(add_query_arg( 'reset_token',$token,$recovery_link));
        $message= wpautop(str_replace(array('{{site_name}}','{{username}}','{{password_recovery_link}}','{{security_token}}'),array(get_bloginfo('name'),$username,$recovery_link,$token),$template_options['pass_reset']));
        //echo $message;
        $rm_email= new RM_Email();
        $rm_email->message($message);
        $rm_email->subject(__("Reset Password",'custom-registration-form-builder-with-submission-manager'));
        $gopt = new RM_Options();
        $rm_email->to($user->user_email);
        $rm_email->send();
        return true;
    }
}