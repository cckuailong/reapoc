<?php

/**
 * the file for the sanitizer functions.
 *
 * @link       http://registration_magic.com
 * @since      1.0.0
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/includes
 */
class RM_Sanitizer
{

	/**
	 * Sanitizes the request variable
	 *
	 * @since    1.0.0
	 */
	public function sanitize_request($req)
	{
        $request = RM_Utilities::trim_array($req);

        // Changes made by Kevin S. begin
        // Removing characters from input strings that might be HTML
        if(!empty($request) && !is_null($request) && isset($request['page']))
        {
            $request = $this->sanitize_all_requests($request['page'],$request);
        }
        else
        {
            if (!empty($request) && !is_null($request))
            {
                foreach($request as $key => $value) {
                    if (is_array($value)) {
                        $request[$key] = $this->sanitized_array($value);
                        continue;
                    }
                    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_REQUEST['page'])) {
                        $request[$key] = $this->sanitize_query_elements($value);
                    } else {
                        $request[$key] = wp_kses_post($value);
                    }
                    // Confirming integer values for variables that should be integers
                    if (in_array($key,array('rm_form_id',
                                            'rm_submission_id',
                                            'rm_tr','rm_reqpage',
                                            'rm_form_page_no',
                                            'page_no',
                                            'field_id',
                                            'rm_field_id',
                                            'field_order'))) {
                        $request[$key] = absint($value);
                    }
                }
            }
        }
        
       
        // Changes made by Kevin S. end
        
        // made changes by vincent andrew
        // $request['rm_form_id']=filter_var($_GET['rm_form_id'], FILTER_SANITIZE_NUMBER_INT);
        if(isset($_GET['page']) && is_array($request) && is_string($_GET['page']))
            $request['page'] = trim(filter_var($_GET['page'], FILTER_SANITIZE_STRING));
        
        return $request;
	}


        public function sanitize_all_requests($page,$request)
        {
            $sanitize_method = 'get_sanitized_' . strtolower($page) . '_page';
            $sanitize_array = array();
            foreach($request as $key=>$value)
            {
                if(is_array($value))
                {
                    if ($page == 'rm_field_manage' && $key == 'op')
                        $sanitize_array[$key] = $value;
                    else
                        $sanitize_array[$key] = $this->sanitized_array($value);
                }
                else
                {
                    if (method_exists($this, $sanitize_method)) 
                    {
                        $sanitize_array[$key] = $this->$sanitize_method($key, $value);
                    } 
                    else 
                    {
                        if (isset($request['rm_field_type']) && $request['rm_field_type'] == 'RichText' && $key == 'field_value') {
                            $sanitize_array[$key] = wp_kses_post($value);
                        } else {
                            if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_REQUEST['page'])) {
                                $sanitize_array[$key] = $this->sanitize_query_elements($value);
                            } else {
                                $sanitize_array[$key] = $this->default_sanitize($key, $value);
                            }
                        }
                    }
                }
            }
            
            return $sanitize_array;
        }
        
        public function sanitized_array($var)
        {
            if (is_array($var) || is_object($var)) {
                foreach ($var as $key => $var_) {
                    $var[$key] = $this->sanitized_array($var_);
                }
            } else {
                if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_REQUEST['page'])) {
                    $var = $this->sanitize_query_elements($var);
                } else {
                    $var = sanitize_text_field($var);
                }
            }
            return $var;
        }
    
        public function sanitize_query_elements($var)
        {
            $var_lower = strtolower($var);
            if (
                strpos($var_lower, 'select') !== false ||
                strpos($var_lower, 'update') !== false ||
                strpos($var_lower, 'insert') !== false ||
                strpos($var_lower, 'delete') !== false ||
                strpos($var_lower, 'order') !== false ||
                strpos($var_lower, 'union') !== false
            ) {
                return '';
            } else {
                return sanitize_text_field($var);
            }
        }
        
        public function get_sanitized_rm_invitations_manage_page($key, $value)
        {
            switch($key)
            {
                case 'rm_mail_body':
                    $value = wp_kses_post($value);
                    break;
                default:
                    $value = sanitize_text_field($value);
                    break;
            }
            return $value;
        }
        
        public function get_sanitized_rm_form_sett_general_page($key, $value)
        {
             switch($key)
            {
                case 'form_custom_text':
                    $value = wp_kses_post($value);
                    break;
                default:
                    $value = sanitize_text_field($value);
                    break;
            }
            return $value;
        }
        
        public function get_sanitized_rm_form_sett_post_sub_page($key, $value)
        {
             switch($key)
            {
                case 'form_success_message':
                    $value = wp_kses_post($value);
                    break;
                default:
                    $value = sanitize_text_field($value);
                    break;
            }
            return $value;
        }
        
        public function get_sanitized_rm_form_sett_email_templates_page($key, $value)
        {
             switch($key)
            {
                case 'form_nu_notification':
                    $value = wp_kses_post($value);
                    break;
                case 'form_user_activated_notification':
                    $value = wp_kses_post($value);
                    break;
                case 'form_admin_ns_notification':
                    $value = wp_kses_post($value);
                    break;
                case 'form_activate_user_notification':
                    $value = wp_kses_post($value);
                    break;
                case 'act_link_message':
                    $value = wp_kses_post($value);
                    break;
                default:
                    $value = sanitize_text_field($value);
                    break;
            }
            return $value;
        }
        
        public function get_sanitized_rm_login_email_temp_page($key, $value)
        {
             switch($key)
            {
                case 'failed_login_err':
                    $value = wp_kses_post($value);
                    break;
                case 'otp_message':
                    $value = wp_kses_post($value);
                    break;
                case 'pass_reset':
                    $value = wp_kses_post($value);
                    break;
                case 'failed_login_err_admin':
                    $value = wp_kses_post($value);
                    break;
                case 'ban_message_admin':
                    $value = wp_kses_post($value);
                    break;
                default:
                    $value = sanitize_text_field($value);
                    break;
            }
            return $value;
        }
        
        public function get_sanitized_rm_form_sett_autoresponder_page($key, $value)
        {
             switch($key)
            {
                case 'form_email_content':
                    $value = wp_kses_post($value);
                    break;
                default:
                    $value = sanitize_text_field($value);
                    break;
            }
            return $value;
        }
    
        public function get_sanitized_rm_options_payment_page($key, $value)
        {
             switch($key)
            {
                case 'ex_olp_info':
                    $value = wp_kses_post($value);
                    break;
                default:
                    $value = sanitize_text_field($value);
                    break;
            }
            return $value;
        }
    
        public function get_sanitized_rm_form_add_cstatus_page($key, $value)
        {
             switch($key)
            {
                case 'cs_email_user_body':
                    $value = wp_kses_post($value);
                    break;
                case 'cs_email_admin_body':
                    $value = wp_kses_post($value);
                    break;
                default:
                    $value = sanitize_text_field($value);
                    break;
            }
            return $value;
        }
        
        public function default_sanitize($key,$value)
        {
            return sanitize_text_field($value);
        }

}
