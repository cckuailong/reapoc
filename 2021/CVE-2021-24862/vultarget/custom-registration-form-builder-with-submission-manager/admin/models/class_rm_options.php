<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/* Option name and values must be in lower case and should not 
 * contain white spaces. 
 * Values must be strings. Do not use true/false etc as the value instead 
 * use yes/no etc.
 * 
 *
 */

class RM_Options
{

    public $default;
    public $options_name_and_methods;
    public $prefix;

    public function __construct()
    {
        $this->default = array();
        $this->prefix = 'rm_option_';

        //Initialize default values.
        $this->default['currency'] = 'USD';
        $this->default['currency_symbol'] = '$';
        $this->default['payment_gateway'] = null;
        $this->default['currency_symbol_position'] = 'before';
        $this->default['enable_captcha'] = 'no';
        $this->default['sub_limit_antispam'] = 20;
        $this->default['edd_notice'] = '1';
        $this->default['wc_notice'] = '1';
        $this->default['php_notice'] = '1';
        $this->default['auto_generated_password'] = 'no';
        $this->default['user_auto_approval'] = 'yes';
        $this->default['admin_notification'] = 'no';
        $this->default['admin_email'] = get_option('admin_email');
        $this->default['user_notification_for_notes'] = 'yes';
        $this->default['user_ip'] = 'yes';
        $this->default['front_sub_page_id']='';
        $this->default['banned_ip'] = null;
        $this->default['banned_email'] = null;
        //SMTP stuff
        $this->default['enable_smtp'] = 'no';
        $this->default['smtp_encryption_type'] = 'enc_none';
        $this->default['hide_toolbar'] = 'no';
        $this->default['enable_toolbar_for_admin'] = 'no';
//Possible theme options: 1.matchmytheme 2.classic 3.blue
        $this->default['theme'] = 'matchmytheme';
        $this->default['form_layout'] = 'label_left';
        $this->default['enable_social'] = 'no';
        $this->default['enable_facebook'] = 'no';
        $this->default['display_progress_bar'] = 'no';
        $this->default['enable_twitter'] = 'no';
        $this->default['enable_mailchimp'] = 'no';
        $this->default['send_password'] = 'yes';
        $this->default['allowed_file_types'] = 'jpg|jpeg|png|gif|doc|pdf|docx|txt';
        $this->default['allow_multiple_file_uploads'] = 'no';
        $this->default['submission_on_card'] = 'all';
        $this->default['senders_display_name'] = get_bloginfo('name', 'display');
        $this->default['senders_email'] = get_option('admin_email');
        $this->default['smtp_senders_email'] = get_option('admin_email');
        $this->default['an_senders_display_name'] = '{{user}}'; //get_bloginfo('name', 'display');
        $this->default['an_senders_email'] = '{{useremail}}';
        $this->default['enable_wordpress_default'] = 'no';
        $this->default['done_with_review_banner'] = 'no';
        $this->default['display_floating_action_btn'] = 'no';
        $this->default['hide_magic_panel_styler'] = 'no';
        $this->default['floating_icon_bck_color'] = '008d7d';
        $this->default['fab_color'] = '00aeff';
        $this->default['fab_theme'] = 'Light';
        $this->default['review_events'] = array(
            'event'=>0,
            'status'=>array(
                'flag'=>0,
                'time'=>''
            ),
            'rating'=>0
        );
        $this->default['show_asterix'] = 'yes';
        $this->default['rm_option_default_forms'] = null;
        $this->default['tour_state'] = array('form_manager_tour' => false,'form_gensett_tour' => false,'form_setting_dashboard_tour' => false, 'submissions_tour' => false);
        $this->default['has_subbed_fb_page'] = 'no';
        $this->default['one_time_actions'] = array();
        $this->default['session_policy'] = 'db';
        $this->default['redirect_admin_to_dashboard_post_login'] = 'yes';
        $this->default['is_visit_welcome_page'] = 'no';
        $this->default['recaptcha_v']= 'v2';
        $this->default['dropbox_notice'] = '1';
        $this->default['wepay_notice'] = '1';
        $this->default['stripe_notice'] = '1';
        $this->default['mailpoet_notice'] = '1';
        $this->default['acc_act_link_expiry'] = '';
        $this->default['submission_pdf_font'] = 'freeserif';
        $this->default['acc_act_notice'] = RM_UI_Strings::get('DEFAULT_ACC_ACT_VALUE');
        $this->default['acc_invalid_act_code'] = RM_UI_Strings::get('DEFAULT_INVALID_ACC_ACT_CODE_VALUE');
        $this->default['acc_act_link_exp_notice'] = RM_UI_Strings::get('DEFAULT_ACC_ACT_LINK_NOTICE_VALUE');
        $this->default['login_error_message'] = RM_UI_Strings::get('DEFAULT_LOGIN_ERR_MSG_VALUE');
        $this->default['prov_act_acc'] = '';
        $this->default['prov_acc_act_criteria'] = '';
        $this->default['enable_gplus'] = 'no';
        $this->default['enable_linked'] = 'no';
        $this->default['enable_window_login'] = 'no';
        $this->default['enable_twitter_login'] = 'no';
        $this->default['enable_instagram_login'] = 'no';
        $this->default['enable_aweber'] = 'no';
        $this->default['enable_custom_pw_rests'] = 'no';
        $this->default['custom_pw_rests'] = (object)array('selected_rules' => array('PWR_MINLEN'), 'min_len' => 7);
        $this->default['submission_on_card'] = 'all';
        $this->default['user_role_custom_data'] = array();
        $this->default['fab_links'] = array();
        $this->default['show_tabs'] = array(
            'payment' => 1,
            'details' => 1,
            'submissions' => 1
        );
        $this->default['rm_submission_filters'] = null;
        $this->default['admin_notification_includes_pdf'] = 'yes';
        $this->default['post_submission_redirection_url'] = '__current_url';
        $this->default['session_policy'] = 'db';
        $this->default['include_stripe'] = 'yes';
        $this->default['gplus_client_id'] = null;
        $this->default['windows_client_id'] = null;
        $this->default['linkedin_api_key'] = null;
        $this->default['linkedin_secret_key'] = null;
        //Initialize options' names and sanitizers if any.
        $this->options_name_and_methods = array(
            'currency' => 'sanitize_currency',
            'currency_symbol' => null,
            'session_policy'=>null,
            'payment_gateway' => null,
            'stripe_api_key' => null,
            'front_sub_page_id'=>null,
            'an_senders_email'=>null,
            'stripe_publish_key' => null,
            'paypal_email' => 'sanitize_email',
            'paypal_test_mode' => 'sanitize_checkbox',
            'display_progress_bar' => 'sanitize_checkbox',
            'an_senders_display_name' => 'sanitize_an_senders_display_name',
            'paypal_page_style' => null,
            'currency_symbol_position' => 'sanitize_currency_pos',
            'enable_captcha' => 'sanitize_checkbox',
            'sub_limit_antispam' => 'sanitize_submission_limit_antispam',
            'edd_notice' => null,
            'wc_notice' => null,
            'php_notice' => null,
            'public_key' => null,
            'private_key' => null,
            'public_key3' => null,
            'private_key3' => null,
            'recaptcha_v'=>null,
            //'captcha_req_method' => 'sanitize_captcha_req_method',
            'auto_generated_password' => 'sanitize_checkbox',
            'user_auto_approval' => null,
            'admin_email' => 'sanitize_email_list',
            'admin_notification' => 'sanitize_checkbox',
            'senders_display_name' => 'sanitize_senders_display_name',
            'senders_email' => 'sanitize_email',
            'enable_wordpress_default' => 'sanitize_checkbox',
            'wordpress_default_email_to' => null,
            'wordpress_default_email_message' => null,
            'user_notification_for_notes' => 'sanitize_checkbox',
            'user_ip' => 'sanitize_checkbox',
            'enable_smtp' => 'sanitize_checkbox',
            'smtp_host' => null,
            'smtp_encryption_type' => 'sanitize_smtp_enctype',
            'smtp_port' => null,
            'smtp_auth' => 'sanitize_checkbox',
            'smtp_user_name' => null,
            'smtp_senders_email' => null,
            'smtp_password' => 'sanitize_password',
            'theme' => null,
            'form_layout' => null,
            'enable_social' => 'sanitize_checkbox',
            'facebook_app_id' => null,
            'facebook_app_secret' => null,
            'gplus_client_id' => null,
            'windows_client_id' => null,
            'linkedin_api_key' => null,
            'linkedin_secret_key'=>null,
            'enable_facebook' => 'sanitize_checkbox',
            'enable_twitter' => 'sanitize_checkbox',
            'consumer_key' => null,
            'consumer_secret' => null,
            'enable_mailchimp' => 'sanitize_checkbox',
            'mailchimp_key' => null,
            'mailchimp_double_optin' => 'sanitize_checkbox',
            'google_map_key' => null,
            'send_password' => 'sanitize_checkbox',
            'allowed_file_types' => 'sanitize_allowed_file_types',
            'allow_multiple_file_uploads' => 'sanitize_checkbox',
            'hide_toolbar' => 'sanitize_checkbox',
            'enable_toolbar_for_admin' => 'sanitize_checkbox',
            'default_registration_url' => null,
            'post_submission_redirection_url' => null,
            'done_with_review_banner' => null,
            'banned_ip' => 'sanitize_banned_ip',
            'banned_email' => 'sanitize_banned_email',
            'google_map_key' => null,
            'enable_custom_pw_rests' => 'sanitize_checkbox',
            'custom_pw_rests' => 'sanitize_custom_pw_rests',
            'blacklisted_usernames' => 'sanitize_banned_email',
            'default_form_id' => null,
            'display_floating_action_btn' => 'sanitize_checkbox',
            'hide_magic_panel_styler' => 'sanitize_checkbox',
            'floating_icon_bck_color' => null,
            'fab_color' => null,
            'fab_theme' => null,
            'fab_icon' => null,
            'submission_on_card' => null,
            'review_events' => null,
            'show_asterix' =>null,
            'rm_option_default_forms' =>null,
            'tour_state' => null,
            'post_logout_redirection_page_id' => null,
            'has_subbed_fb_page' => null,
            'one_time_actions' => null,
            'redirect_admin_to_dashboard_post_login' => 'sanitize_checkbox',
            'is_visit_welcome_page'=>null,
            'include_stripe'=>'sanitize_checkbox',
            'dropbox_notice' => null,
            'wepay_notice' => null,
            'stripe_notice' => null,
            'mailpoet_notice' => null,
            'aw_consumer_key' => null,
            'aw_consumer_secret' => null,
            'aw_access_key' => null,
            'aw_oauth_id'=>null,
            'aw_access_secret' => null,
            'tw_consumer_key' => null,
            'tw_consumer_secret' => null,
            'instagram_client_id' => null,
            'instagram_client_secret' => null,
            'enable_aweber' => 'sanitize_checkbox',
            'enable_gplus' => 'sanitize_checkbox',
            'enable_linked' => 'sanitize_checkbox',
            'enable_window_login' => 'sanitize_checkbox',
            'enable_twitter_login' => 'sanitize_checkbox',
            'enable_instagram_login' => 'sanitize_checkbox',
            'sub_pdf_header_text' => null,
            'sub_pdf_header_img' => null,
            'submission_pdf_font'=>null,
            'user_role_custom_data' =>null,
            'fab_links' =>null,
            'show_tabs' =>null,
            'rm_submission_filters' =>null,
            'admin_notification_includes_pdf' => 'sanitize_checkbox',
            'acc_act_link_expiry'=>'',
            'acc_act_notice'=>null,
            'acc_invalid_act_code'=>null,
            'acc_act_link_exp_notice'=>null,
            'login_error_message'=>null,
            'prov_act_acc'=>'sanitize_checkbox',
            'prov_acc_act_criteria'=>null
        );
    }

    public function get_value_of($option)
    {
        if ($option === 'currency_symbol')
            return $this->get_currency_symbol();

        if ($option === 'senders_email_formatted')
        {
            $disp_name = $this->get_value_of('senders_display_name');
            $sender_mail = $this->get_value_of('senders_email');

            if ($disp_name && $sender_mail)
                $str = $disp_name . " <" . $sender_mail . ">";
            elseif ($disp_name && !$sender_mail)
                $str = $disp_name;
            elseif (!$disp_name && $sender_mail)
                $str = $sender_mail;
            elseif (!$disp_name && !$sender_mail)
                $str = $this->default['senders_display_name'] . " <" . $this->default['senders_email'] . ">";

            return $str;
        }

        //To be on safe side, also prepend prefix before using the option name.
        $prefixed_option = strtolower($this->prefix . $option);

        $value = get_option($prefixed_option, null);
        if($option=='senders_email' && empty($value)){
            $value= get_option('admin_email');
        }

        //If option is not in database try to load default value.
        if (null === $value)
        {
            return isset($this->default[$option]) ? $this->default[$option] : null;
        } else
        {
            if ($option === 'smtp_password' && $value)
                $value = RM_Utilities::dec_str($value);
            elseif ($option === 'admin_email' && trim($value) === '')
                $value = $this->default[$option];
            elseif ($option === 'allowed_file_types' && trim($value) === '')
                $value = $this->default[$option];
                
            return $value;
        }
    }

    public function get_currency_symbol($curr = null)
    {
        $currency = $curr? $curr : $this->get_value_of('currency');
        $curr_arr = $this->currency_array();
        return isset($curr_arr[$currency]) ? html_entity_decode($curr_arr[$currency]) : $currency;
    }

    public function get_formatted_amount($amount, $curr = null, $use_symbol = true)
    {
        $position = $this->get_value_of('currency_symbol_position');

        $currency = $curr? : $this->get_value_of('currency');

        $symbol = $use_symbol ? $this->get_currency_symbol($currency) : (($position === 'before') ? $currency . " " : " " . $currency);

        if ($position === 'before')
            return $symbol . $amount;

        return $amount . $symbol;
    }

    //Resets option to its default value
    public function reset($option)
    {
        //To be on safe side, also prepend prefix before using the option name.
        $prefixed_option = strtolower($this->prefix . $option);

        $value = isset($this->default[$option]) ? $this->default[$option] : null;
        $this->set_value_of($option, $value);
    }

    public function set_value_of($option, $value)
    {
        $option = strtolower($option);

        //Update only if it is a valid option
        if (array_key_exists($option, $this->options_name_and_methods))
        {
            //Call sanitizer if exists
            $sanitizer_method = $this->options_name_and_methods[$option];
            if ($sanitizer_method != null)
                $value = $this->$sanitizer_method($value);

            $option = $this->prefix . $option;
            if(null === $value)
                $value = '';
            update_option($option, $value, false);
        } else
            return false;
    }

    public function set_values($asso_array_of_options)
    {
        if (is_array($asso_array_of_options))
        {
            foreach ($asso_array_of_options as $option => $value)
            {
                $this->set_value_of($option, $value);
            }
        }
    }

    public function get_all_options()
    {
        $options_arr = array();

        foreach ($this->options_name_and_methods as $option => $method)
        {
            $options_arr[$option] = $this->get_value_of($option);
        }
        return $options_arr;
    }

    public function currency_array()
    {
        return array(
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'AUD' => '$',
            'BRL' => 'R$',
            'CAD' => '$',
            'CZK' => 'Kč',
            'DKK' => 'kr',
            'HKD' => '$',
            'HRK' => 'kn',
            'HUF' => 'Ft',
            'ILS' => '₪',
            'JPY' => '¥',
            'MYR' => 'RM',
            'MXN' => '$',
            'NZD' => '$',
            'NOK' => 'kr',
            'PHP' => '₱',
            'PLN' => 'zł',
            'SGD' => '$',
            'SEK' => 'kr',
            'CHF' => 'CHF',
            'TWD' => 'NT$',
            'THB' => '฿',
            'INR' => '₹',
            'TRY' => 'TRY',
            'RIAL' => 'RIAL',
            'RUB' => 'руб',
            'NGN' => '&#x20a6;',
            'ZAR' => 'R',
            'ZMW' => 'ZK',
            'GHS' => 'GH&#x20B5;'
            );
    }

    //Sanitizer methods for specific options.
    //If any supplied value is invalid it will revert to default or null value.
    public function sanitize_currency($curr)
    {
        $valid_currencies = $this->currency_array();

        if (!array_key_exists($curr, $valid_currencies))
            return $this->default['currency'];
        else
            return $curr;
    }

    public function sanitize_checkbox($val)
    {
        if ($val === 'yes')
            return 'yes';
        else
            return null;
    }

    public function sanitize_currency_pos($val)
    {
        if ($val === 'after')
            return 'after';
        else
            return 'before';
    }

    public function sanitize_allowed_file_types($val)
    {
        //strip out all the whitespaces
        $val = preg_replace('/\s+/', '', $val);

        //check the validity. Allowed chars: a-z,A-Z,0-9 and '|'.
        $tmp = preg_replace('/[a-zA-Z\|0-9]*/', '', $val);
        //it $tmp is empty then it means the string matched completely.

        if ($tmp === '')
            return trim(strtolower($val), '|');
        else
            return $this->default['allowed_file_types'];
    }

    public function sanitize_email($val)
    {
        if (!filter_var($val, FILTER_VALIDATE_EMAIL))
            return null;
        else
            return $val;
    }

    public function sanitize_language($val)
    {
        $valid_languages = array(
            "ar",
            "af",
            "am",
            "hy",
            "az",
            "eu",
            "bn",
            "bg",
            "ca",
            "zh-CN",
            "zh-HK",
            "zh-TW",
            "hr",
            "cs",
            "da",
            "nl",
            "en",
            "en-GB",
            "et",
            "fil",
            "fi",
            "fr-CA",
            "fr",
            "gl",
            "ka",
            "de",
            "de-AT",
            "de-CH",
            "el",
            "gu",
            "iw",
            "hi",
            "hu",
            "is",
            "id",
            "it",
            "ja",
            "kn",
            "ko",
            "lo",
            "lv",
            "lt",
            "ms",
            "ml",
            "mr",
            "mn",
            "no",
            "ps",
            "fa",
            "pl",
            "pt",
            "pt-BR",
            "pt-PT",
            "ro",
            "ru",
            "sr",
            "si",
            "sk",
            "sl",
            "es-419",
            "es",
            "sw",
            "sv",
            "ta",
            "te",
            "th",
            "tr",
            "uk",
            "ur",
            "vi",
            "zu"
        );

        if (!in_array($val, $valid_languages))
            return $this->default['captcha_language'];
        else
            return $val;
    }

    public function sanitize_submission_limit_antispam($val)
    {
        $val = (int) $val;
        if ($val >= 0)
            return $val;
        else
            return $this->default['sub_limit_antispam'];
    }

    public function sanitize_captcha_req_method($val)
    {
        if ($val === 'socketpost')
            return 'socketpost';
        else
            return 'curlpost';
    }

    //removes any invalid email from a string of comma separated email addresses.
    public function sanitize_email_list($val)
    {
        $emails = explode(',', $val);
        $processed_emails = array();

        foreach ($emails as $email)
        {
            if ($this->sanitize_email($email) != null)
                $processed_emails[] = $email;
        }

        return implode(",", $processed_emails);
    }

    public function sanitize_smtp_enctype($val)
    {
        if ($val === 'enc_tls' || $val === 'enc_ssl')
            return $val;
        else
            return 'enc_none';
    }

    public function sanitize_password($val)
    {
        return RM_Utilities::enc_str($val);
    }

    public function sanitize_senders_display_name($val)
    {
        return trim($val);
    }
    
    public function sanitize_an_senders_display_name($val)
    {
        return trim($val);
    }
    
    public function sanitize_banned_ip($val)
    {  
      
        //remove multiple whitespaces and newline with single whitespace.
        if(is_array($val))
        {
           $ips= $val;
        }
        else
        {
            $val = preg_replace('/\s+/', ' ',$val);
            $val = trim($val);
            if($val == '')
            return array();
            $ips = explode(' ', $val);
        }
        
        $sanitized_ips = array();
        
        foreach($ips as $val)
        {
            //Check if ipv6, save and continue in that case.
            if((bool)filter_var($val, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
            {
                $sanitized_ips[] = $val;
                continue;
            }
                    
            $ip_as_arr = explode('.', $val);

            $c = count($ip_as_arr);

            if( $c < 4)
            {
                for($i=$c+1;$i<=4;$i++)
                    $ip_as_arr[] = '000';
            }
            else if($c > 4)
                $ip_as_arr = array_slice($ip_as_arr, 0, 4);

            foreach($ip_as_arr as $index => $ip_part)
            {
                $val = intval($ip_part);

                if(strlen($ip_part) > 3)
                    $ip_as_arr[$index] = substr($ip_part,0,3);

                if(strlen($ip_part) == 3 && $ip_as_arr[$index][0] != '0' && $ip_as_arr[$index][0] != '1' && $ip_as_arr[$index][0] != '2' && $ip_as_arr[$index][0] != '?')
                    $ip_as_arr[$index][0] = '2';

                if($val > 255)
                    $ip_as_arr[$index] = '255';
            }

            //$sanitized_ips[] = sprintf("%'03s.%'03s.%'03s.%'03s",$ip_as_arr[0],$ip_as_arr[1],$ip_as_arr[2],$ip_as_arr[3]);
            $sanitized_ips[] = sprintf("%s.%s.%s.%s",$ip_as_arr[0],$ip_as_arr[1],$ip_as_arr[2],$ip_as_arr[3]);
        }
        return $sanitized_ips;
    }
    
    public function sanitize_banned_email($val)
    {        
        $emails = array();
        
        if(!is_array($val))
        {
            //remove multiple whitespaces and newline with single whitespace.
            $val = preg_replace('/\s+/', ' ',$val);

            $val = trim($val);

            if($val == '')
                return array();

            $emails = explode(' ', $val);        
        }
        else{
            foreach($val as $e)
                $emails[] = trim($e);
        }
        return $emails;
    }
    
    public function sanitize_custom_pw_rests($val)
    {        
        if(isset($val->min_len))
        {
            if($val->min_len < 5)
                $val->min_len = 5;
            
            if(isset($val->max_len))
            {
                if($val->max_len < $val->min_len)
                    $val->max_len = $val->min_len;
            }
        }
        
        return $val;
    }
}