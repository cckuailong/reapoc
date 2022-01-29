<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_options_controller
 *
 * @author CMSHelplive
 */
class RM_Options_Controller
{

    public $mv_handler;

    function __construct()
    {
        $this->mv_handler = new RM_Model_View_Handler();
    }

    public function add()
    {
        $this->service->add();
        $this->view->render();
    }

    public function get_options()
    {
        $data = $this->service->get_options();
        $this->view->render($data);
    }

    public function user($model, $service, $request, $params)
    {
        if ($this->mv_handler->validateForm("options_users"))
        {
            $options = array();

            $options['auto_generated_password'] = isset($request->req['auto_generated_password']) ? "yes" : null;
            $options['send_password'] = isset($request->req['send_password']) ? "yes" : null;
            if(defined('REGMAGIC_ADDON')) {
                $options['user_auto_approval'] = isset($request->req['user_auto_approval']) ? $request->req['user_auto_approval'] : null;
                $options['acc_act_link_expiry'] = $request->req['acc_act_link_expiry'];
                $options['acc_act_notice'] = $request->req['acc_act_notice'];
                $options['acc_invalid_act_code'] = $request->req['acc_invalid_act_code'];
                $options['acc_act_link_exp_notice'] = $request->req['acc_act_link_exp_notice'];
                $options['login_error_message'] = $request->req['login_error_message'];
                $options['prov_act_acc'] = isset($request->req['prov_act_acc']) ? 'yes' : null;
                $options['prov_acc_act_criteria'] = isset($request->req['prov_acc_act_criteria']) ? $request->req['prov_acc_act_criteria'] : '';
            }
            $service->set_model($model);
            $service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {
            $view = $this->mv_handler->setView('options_user');
            $service->set_model($model);
            $data = $service->get_options();
            $view->render($data);
        }
    }

    public function manage($model, RM_Setting_Service $service, $request, $params)
    {
        $view = $this->mv_handler->setView('options_manager');
        $view->render();
    }

    public function general(RM_Options $model, RM_Setting_Service $service, $request, $params)
    {
        
        if ($this->mv_handler->validateForm("options_general") && current_user_can('manage_options'))
        {
            $retrieved_nonce = $request->req['_wpnonce'];
	    if (!wp_verify_nonce($retrieved_nonce, 'rm_options_general' ) ) die( __('Failed security check','custom-registration-form-builder-with-submission-manager') );
            
            $options = array();
            $options['theme'] = $request->req['theme'];
            $options['allowed_file_types'] = isset($request->req['allowed_file_types']) ? $request->req['allowed_file_types']: null;
            $options['default_registration_url'] = isset($request->req['default_registration_url']) ? $request->req['default_registration_url'] : '';
            $options['post_submission_redirection_url'] = isset($request->req['post_submission_redirection_url']) ? $request->req['post_submission_redirection_url'] : '';
            $options['post_logout_redirection_page_id'] = isset($request->req['post_logout_redirection_page_id']) ? $request->req['post_logout_redirection_page_id'] : '';
            $options['hide_toolbar'] = isset($request->req['hide_toolbar']) ? "yes" : null;
            $options['enable_toolbar_for_admin'] = isset($request->req['enable_toolbar_for_admin']) ? "yes" : null;
//            $options['login_page_url'] = $request->req['login_page_url'];
            $options['user_ip'] = isset($request->req['user_ip']) ? "yes" : null;
            $options['allow_multiple_file_uploads'] = isset($request->req['allow_multiple_file_uploads']) ? "yes" : null;
            $options['form_layout'] = $request->req['form_layout'];
            $options['display_progress_bar'] = isset($request->req['display_progress_bar']) ? "yes" : null;
            $options['submission_on_card'] = $request->req['submission_on_card'];
            $options['show_asterix'] = isset($request->req['show_asterix']) ? "yes" : null;
            $options['redirect_admin_to_dashboard_post_login'] = isset($request->req['redirect_admin_to_dashboard_post_login']) ? "yes" : null;
            if(defined('REGMAGIC_ADDON')) {
                $options['sub_pdf_header_text'] = $request->req['sub_pdf_header_text'];
                $options['submission_pdf_font'] = $request->req['submission_pdf_font'];
            }
            if(isset($_FILES['sub_pdf_header_img'])){
                $att_service = new RM_Attachment_Service;
                $attach_id = $att_service->media_handle_attachment('sub_pdf_header_img', 0);
                if (!is_wp_error($attach_id))
                {
                    $options['sub_pdf_header_img'] = $attach_id;
                }
                else
                {
                    if($request->req['rm_pdf_logo_removal']=='true')
                    {
                        $options['sub_pdf_header_img'] = null;
                    }
                }
            }
            
            $service->set_model($model);

            $service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {
            $view = $this->mv_handler->setView('options_general');
            $service->set_model($model);
            $data = $service->get_options();

            //Add an extra space around the extensions for better visibility for end user.
            //While saving they are automatically stripped off.
            $data['allowed_file_types'] = str_replace("|"," | ",$data['allowed_file_types']);
            
            $view->render($data);
        }
    }
    
    public function fab(RM_Options $model, RM_Setting_Service $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Options_Controller_Addon();
            return $addon_controller->fab($model, $service, $request, $params, $this);
        }
        if ($this->mv_handler->validateForm("options_fab"))
        {
            $options = array();
            $options['display_floating_action_btn'] = isset($request->req['display_floating_action_btn']) ? "yes" : null;
            $options['hide_magic_panel_styler'] = isset($request->req['hide_magic_panel_styler']) ? "yes" : null;
            $options['fab_icon'] = $request->req['fab_icon'];
           
            $service->set_model($model);

            $service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {
            $view = $this->mv_handler->setView('options_fab');
            $service->set_model($model);
            $data = $service->get_options();            
            $view->render($data);
        }
    }

    public function security($model, RM_Setting_Service $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Options_Controller_Addon();
            return $addon_controller->security($model, $service, $request, $params, $this);
        }
        if ($this->mv_handler->validateForm("options_security"))
        {
            $options = array();

            $options['enable_captcha'] = isset($request->req['enable_captcha']) ? "yes" : null;
           // $options['captcha_language'] = $request->req['captcha_language'];
            $options['public_key'] = isset($request->req['public_key'])?$request->req['public_key']:null;
            $options['private_key'] = isset($request->req['private_key'])?$request->req['private_key']:null;
            $options['public_key3'] = isset($request->req['public_key3']) ? $request->req['public_key3'] : null;
            $options['private_key3'] = isset($request->req['private_key3']) ? $request->req['private_key3'] : null;
            $options['recaptcha_v']= $request->req['recaptcha_v'];
            $options['sub_limit_antispam'] = $request->req['sub_limit_antispam'];
            $options['enable_captcha_under_login'] = isset($request->req['enable_captcha_under_login']) ? "yes" : null;
           // $options['captcha_req_method'] = $request->req['captcha_req_method'];

            $service->set_model($model);

            $service->save_options($options);
           RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {
            $view = $this->mv_handler->setView('options_security');
            $service->set_model($model);
            $data = $service->get_options();
            $view->render($data);
        }
    }

    public function autoresponder($model, $service, $request, $params)
    {
        if ($this->mv_handler->validateForm("options_autoresponder"))
        {
            $options = array();

            $options['admin_notification'] = isset($request->req['admin_notification']) ? "yes" : null;
            if (isset($request->req['resp_emails']))
                $options['admin_email'] = implode(",", $request->req['resp_emails']);
            //var_dump($options['admin_email']);die;
            $options['senders_display_name'] = $request->req['senders_display_name'];
            $options['senders_email'] = $request->req['senders_email'];
            $options['an_senders_display_name'] = $request->req['an_senders_display_name'];
            $options['an_senders_email'] = $request->req['an_senders_email'];
            $options['enable_smtp'] = $request->req['enable_smtp']=='yes' ? "yes" : null;
            $options['smtp_encryption_type'] = $request->req['smtp_encryption_type'];
            $options['smtp_host'] = $request->req['smtp_host'];
            $options['smtp_port'] = $request->req['smtp_port'];
            
            $options['smtp_auth'] = isset($request->req['smtp_auth']) ? "yes" : null;
            $options['smtp_user_name'] = $request->req['smtp_user_name'];
            $options['smtp_password'] = $request->req['smtp_password'];
            $options['smtp_senders_email'] = $request->req['smtp_senders_email'];
            $options['enable_wordpress_default'] = isset($request->req['enable_wordpress_default']) ? "yes" : null;
            $options['wordpress_default_email_to'] = $request->req['wordpress_default_email_to'];
            $options['wordpress_default_email_message'] = $request->req['wordpress_default_email_message'];
            if(defined('REGMAGIC_ADDON')) {
                $options['user_notification_for_notes'] = isset($request->req['user_notification_for_notes']) ? "yes" : null;
                $options['admin_notification_includes_pdf'] = isset($request->req['admin_notification_includes_pdf']) ? "yes" : 'no';
            }
            
            $service->set_model($model);

            $service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {
            $view = $this->mv_handler->setView('options_autoresponder');
            $service->set_model($model);
            $data = $service->get_options();
            $view->render($data);
        }
    }

    public function thirdparty($model, RM_Setting_Service $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Options_Controller_Addon();
            return $addon_controller->thirdparty($model, $service, $request, $params, $this);
        }
        if ($this->mv_handler->validateForm("options_thirdparty"))
        {
            $options = array();

            $options['enable_facebook'] = isset($request->req['enable_facebook']) ? "yes" : null;
            $options['facebook_app_id'] = isset($request->req['facebook_app_id']) ? $request->req['facebook_app_id'] : '';
            //$options['facebook_app_secret'] = $request->req['facebook_app_secret'];
            $options['enable_mailchimp'] = isset($request->req['enable_mailchimp']) ? "yes" : null;
            $options['mailchimp_key'] = $request->req['mailchimp_key'];
            $options['mailchimp_double_optin'] = isset($request->req['mailchimp_double_optin']) ? "yes" : null;
            $options['google_map_key'] = $request->req['google_map_key'];
            $service->set_model($model);

            $service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {

            $view = $this->mv_handler->setView('options_thirdparty');
            $service->set_model($model);
            $data = $service->get_options();
            $view->render($data);
        }
    }
    
    public function default_pages($model,RM_Setting_Service $service, $request, $params){
        $service->set_model($model);
        $options = $service->get_options();
        if ($this->mv_handler->validateForm("rm_default_pages"))
        {
            $options['default_registration_url'] = $request->req['default_registration_url'];
            $options['front_sub_page_id'] = $request->req['default_user_acc_page'];
            $service->save_options($options);
            
            $options_model= new RM_Options();
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        }              
        $view = $this->mv_handler->setView('options_default_pages');
        $view->render($options);
    }
    
    public function user_privacy($model, RM_Setting_Service $service, $request, $params)
    {
        if ($this->mv_handler->validateForm("options_user_privacy"))
        {
           $gopt= new RM_Options;
           $old_banned_ips = array();
           $ip_banned= $gopt->get_value_of('banned_ip');
           if(!empty($ip_banned)){
               $old_banned_ips= $ip_banned;
           }
            
            $options = array();

            $options['enable_captcha'] = isset($request->req['enable_captcha']) ? "yes" : null;
           // $options['captcha_language'] = $request->req['captcha_language'];
            $options['public_key'] = isset($request->req['public_key']) ? $request->req['public_key'] : null;
            $options['public_key'] = isset($request->req['public_key']) ? $request->req['public_key'] : null;
            $options['sub_limit_antispam'] = $request->req['sub_limit_antispam'];
            $options['banned_ip'] = $request->req['banned_ip'];
            $options['banned_email'] = $request->req['banned_email'];
            $options['blacklisted_usernames'] = $request->req['blacklisted_usernames'];
            $options['enable_captcha_under_login'] = isset($request->req['enable_captcha_under_login']) ? "yes" : null;
           // $options['captcha_req_method'] = $request->req['captcha_req_method'];
            $options['enable_custom_pw_rests'] = isset($request->req['enable_custom_pw_rests']) ? "yes" : null;
            $custom_pw_rests = isset($request->req['custom_pw_rests']) ? $request->req['custom_pw_rests'] : null;
             
             if(!$custom_pw_rests)
             {
                 $custom_pw_rests = (object) array('selected_rules' => array(), 'min_len' => $request->req['PWR_MINLEN'], 'max_len' => $request->req['PWR_MAXLEN']);
             }
             else
             {
                 $custom_pw_rests = (object) array('selected_rules' => $custom_pw_rests, 'min_len' => $request->req['PWR_MINLEN'], 'max_len' => $request->req['PWR_MAXLEN']);
             }
             
            
            $service->set_model($model);

            $service->save_options($options);
            
            // Identiying deleted IPS
            $options['custom_pw_rests'] = $custom_pw_rests;
            $recent_banned_ips = array();
            if(!empty($ip_banned)){
                $recent_banned_ips= $ip_banned;
            }
            $diff= array_diff($old_banned_ips,$recent_banned_ips);
            if(!empty($diff)){
                foreach($diff as $ip){
                    do_action('rm_ip_unblocked',$ip);
                }
            }
           RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {
            $view = $this->mv_handler->setView('options_user_privacy');
            $service->set_model($model);
            $data = $service->get_options();
            $view->render($data);
        }
    }

    public function payment($model, RM_Setting_Service $service, $request, $params)
    {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Options_Controller_Addon();
            return $addon_controller->payment($model, $service, $request, $params, $this);
        }
        if ($this->mv_handler->validateForm("options_payment"))
        {
            $options = array();

            $options['payment_gateway'] = isset($request->req['payment_gateway']) ? array('paypal') : null;
            $options['paypal_test_mode'] = isset($request->req['paypal_test_mode']) ? "yes" : null;
            if(isset($request->req['paypal_page_style']))
                $options['paypal_page_style'] = $request->req['paypal_page_style'];
            
            if(isset($request->req['paypal_email']))
                $options['paypal_email'] = $request->req['paypal_email'];
            $options['currency'] = $request->req['currency'];
            $options['currency_symbol_position'] = $request->req['currency_symbol_position'];

            $service->set_model($model);

            $service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        } else
        {

            $view = $this->mv_handler->setView('options_payment');
            $service->set_model($model);
            $data = $service->get_options();
            
            
            $options_s_api = array("id" => "rm_s_api_key_tb", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_STRP_API_KEY'), "disabled" => true);
            $options_s_pub = array("id" => "rm_s_publish_key_tb", "longDesc" => RM_UI_Strings::get('MSG_BUY_PRO_INLINE'), "disabled" => true);
            $options_pp_test_cb = array("id" => "rm_pp_test_cb", "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_TESTMODE'));
            $options_pp_email = array("id" => "rm_pp_email_tb", "value" => $data['paypal_email'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_PP_EMAIL'));
            $options_pp_pstyle = array("id" => "rm_pp_style_tb", "value" => $data['paypal_page_style'], "longDesc" => RM_UI_Strings::get('HELP_OPTIONS_PYMNT_PP_PAGESTYLE'));

            if($data['paypal_test_mode'] == 'yes')
                $options_pp_test_cb['value'] = 'yes';
            
            $pay_procs_options = array("paypal" => "<img src='" . RM_IMG_URL . "/paypal-logo.png" . "'></img>",
                                      "stripe" => "<img src='" . RM_IMG_URL . "/stripe-logo.png" . "'></img>",
                                      "asim" => "<img style='width:auto;' src='" . RM_IMG_URL . "premium/adn-small.png" . "'>",
                                      "wepay" => "<img style='width:auto;' src='" . RM_IMG_URL . "premium/rm_wepay.png" . "'>",
                                      "offline"=>"<strong>Offline</strong>");
            
            $pay_procs_configs = array("paypal" => array(
                                            new Element_Checkbox(RM_UI_Strings::get('LABEL_TEST_MODE'), "paypal_test_mode", array("yes" => ''), $options_pp_test_cb),
                                            new Element_Email(RM_UI_Strings::get('LABEL_PAYPAL_EMAIL'), "paypal_email", $options_pp_email),
                                            new Element_Textbox(RM_UI_Strings::get('LABEL_PAYPAL_STYLE'), "paypal_page_style", $options_pp_pstyle)
                                            ),
                                        "stripe" => array(new Element_HTML('<div><p class="rm_buy_pro_wrap">'.RM_UI_Strings::get('MSG_BUY_PRO_INLINE').'</p></div>')),
                                        "asim" => array(new Element_HTML('<div><p class="rm_buy_pro_wrap">'.RM_UI_Strings::get('MSG_BUY_PRO_INLINE').'</p></div>')),
                                        "wepay" => array(new Element_HTML('<div><p class="rm_buy_pro_wrap">'.RM_UI_Strings::get('MSG_BUY_PRO_INLINE').'</p></div>')),
                                        "offline" => array(new Element_HTML('<div><p class="rm_buy_pro_wrap">'.RM_UI_Strings::get('MSG_BUY_PRO_INLINE').'</p></div>'))
                                     );
            $data['pay_procs_options'] = $pay_procs_options;
            $data['pay_procs_configs'] = $pay_procs_configs;
            $view->render($data);
        }
    }

    public function advance($model, RM_Setting_Service $service, $request, $params)
    {   
        if ($this->mv_handler->validateForm("options_advance"))
        {
            $options = array();
            if(defined('REGMAGIC_ADDON'))
                $options['include_stripe'] = isset($request->req['include_stripe']) ? 'yes' : null;
            $options['session_policy'] = $request->req['session_policy'];
            $service->set_model($model);
            $service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success));
        }
        $data= new stdClass();
        $service->set_model($model);
        $data = $service->get_options();
        $view = $this->mv_handler->setView('options_advance');
        $view->render($data);
    }
    
    public function eventprime($model,$service,$request,$params){
        $data= new stdClass();
        $installUrl = admin_url('update.php?action=install-plugin&plugin=eventprime-event-calendar-management');
        $installUrl = wp_nonce_url($installUrl, 'install-plugin_eventprime-event-calendar-management');
        $data->ep_install_url= $installUrl;
        $view = $this->mv_handler->setView('options_eventprime');
        $view->render($data);
    }
}
