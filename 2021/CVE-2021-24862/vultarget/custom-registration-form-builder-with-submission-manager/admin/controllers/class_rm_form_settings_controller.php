<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rm_form_settings_controller
 *
 * @author Cmshelplive
 */
class RM_Form_Settings_Controller {

    public $mv_handler;

    function __construct() {
        $this->mv_handler = new RM_Model_View_Handler();
    }

    function manage($form,  RM_Services $service, $request) {
        $data = new stdClass();
        if (!isset($request->req['rm_form_id']) || !(int)$request->req['rm_form_id'] || !$form->load_from_db($request->req['rm_form_id'])){
            $view = $this->mv_handler->setView('show_notice');
            $view->render(RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED'));
           return;
        }
                
        $form_options = $form->get_form_options();
        $global_option = new RM_Options;
        $acc_ctrl = $form_options->access_control;
        $data->form_id = (int)$request->req['rm_form_id'];
        $data->all_forms = RM_Utilities::get_forms_dropdown($service);
        //unset($data->all_forms[$data->form_id]);
        $data->form = $form;
        $data->form_options = $form_options;
        if(defined('REGMAGIC_ADDON'))
            $data->form_access = (isset($acc_ctrl->roles) || isset($acc_ctrl->passphrase) || isset($acc_ctrl->date)) ? RM_UI_Strings::get('FD_LABEL_RESTRICTED') : RM_UI_Strings::get('FD_LABEL_PUBLIC');
        else
            $data->form_access = RM_UI_Strings::get('FD_LABEL_PUBLIC');
        $data->captcha_sitekey = $global_option->get_value_of('public_key');
        $data->captcha_secret = $global_option->get_value_of('private_key');
        $data->auto_approval = $global_option->get_value_of('user_auto_approval');
        $this->add_form_stats($data);
        $this->add_form_submission_data($data);
        $this->add_form_attchment_data($data);
        
        if(isset($request->req['rm_tr']))
        {
            $data->timerange = $request->req['rm_tr'];            
        }
        else
        {
            $data->timerange = '30';
        }
        
        $this->add_form_timewise_stat($data);
        
        //Include joyride script and style
        wp_enqueue_script('rm_joyride_js', RM_BASE_URL.'admin/js/jquery.joyride-2.1.js');
        wp_enqueue_style('rm_joyride_css', RM_BASE_URL.'admin/css/joyride-2.1.css');
        
        $data->autostart_tour = !RM_Utilities::has_taken_tour('form_setting_dashboard_tour');
        $data->def_form_id = $service->get_setting('default_form_id');
        $installUrl = admin_url('update.php?action=install-plugin&plugin=profilegrid-user-profiles-groups-and-communities');
        $installUrl = wp_nonce_url($installUrl, 'install-plugin_profilegrid-user-profiles-groups-and-communities');
        $data->pg_install_url = $installUrl;
        $installUrl = admin_url('update.php?action=install-plugin&plugin=eventprime-event-calendar-management');
        $installUrl = wp_nonce_url($installUrl, 'install-plugin_eventprime-event-calendar-management');
        $data->ep_install_url = $installUrl;
        $view = $this->mv_handler->setView('form_settings');
        $view->render($data);
    }

    function general($model, $service, $request, $params) {
        $form_id= (isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id'])) ? absint($request->req['rm_form_id']):0;
        $model->load_from_db($form_id);
        
        $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
        if ($this->mv_handler->validateForm("form_sett_general")) {
            $options = array();
            if($model->form_type!=$request->req['form_type']){
                 do_action('rm_form_type_changed',$form_id,$request->req['form_type'],$model->form_type);
            }
            $options['form_type'] = $request->req['form_type'];
            $options['form_name'] = $request->req['form_name'];
            $options['form_description'] = $request->req['form_description'];
            $options['form_custom_text'] = $request->req['form_custom_text'];
            $options['show_total_price'] = isset($request->req['show_total_price']) ? $request->req['show_total_price'] : null;
            if(defined('REGMAGIC_ADDON')) {
                $options['no_prev_button'] = isset( $request->req['no_prev_button']) ? 1 : null;
                $options['sub_limit_ind_user']= empty($request->req['sub_limit_ind_user'])?0:$request->req['sub_limit_ind_user']; 
            }

            if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
                $model->load_from_db($request->req['rm_form_id']);
                $model->set($options);
                $model->update_into_db();
                do_action('rm_form_saved',$request->req['rm_form_id']);
                RM_Utilities::redirect('?page='.$next_page.'&rm_form_id='.$request->req['rm_form_id']);
                return;
            } else {
                $model->set($options);
                $form_id = $service->add_user_form();
                do_action('rm_form_saved',$form_id);
                RM_Utilities::redirect('?page=rm_field_manage&rm_form_id='.$form_id);
                return;
            }
        }
        //Include joyride script and style
        wp_enqueue_script('rm_joyride_js', RM_BASE_URL.'admin/js/jquery.joyride-2.1.js');
        wp_enqueue_style('rm_joyride_css', RM_BASE_URL.'admin/css/joyride-2.1.css');
        $data = new stdClass();
        $data->next_page = $next_page;
        $data->autostart_tour = !RM_Utilities::has_taken_tour('form_gensett_tour');
        $view = $this->mv_handler->setView('form_gen_sett');
        if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id'])
            $model->load_from_db($request->req['rm_form_id']);
        $data->model = $model;
        $view->render($data);
    }

    function limits($model, $service, $request, $params) {
        $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
        if ($this->mv_handler->validateForm("form_sett_limits")) {
            $options['form_should_auto_expire'] = isset($request->req['form_should_auto_expire']) ? $request->req['form_should_auto_expire'] : null;
            $options['form_expired_by'] = isset($request->req['form_expired_by']) ? $request->req['form_expired_by'] : null;
            $options['form_submissions_limit'] = $request->req['form_submissions_limit'];
            $options['form_expiry_date'] = $request->req['form_expiry_date'];
            if(defined('REGMAGIC_ADDON')) {
                $options['form_limit_by_cs'] = isset($request->req['form_limit_by_cs']) ? maybe_serialize($request->req['form_limit_by_cs']) : null;
            }
            
            if(isset( $request->req['form_message_after_expiry']))
                $options['form_message_after_expiry'] = $request->req['form_message_after_expiry'];
            
            $options['enable_captcha'] = isset($request->req['enable_captcha']) ? $request->req['enable_captcha'] : null;
            //$options['allow_multiple_file_uploads'] =isset( $request->req['allow_multiple_file_uploads']) ? $request->req['allow_multiple_file_uploads'] : null;
            $options['sub_limit_antispam'] = isset( $request->req['sub_limit_antispam']) ? $request->req['sub_limit_antispam'] : null;
            $options['post_expiry_action'] = isset( $request->req['post_expiry_action']) ? $request->req['post_expiry_action'] : 'display_message';
            $options['admin_notification'] = isset($request->req['admin_notification']) ? "yes" : null;
            if (isset($request->req['resp_emails']))
                 $options['admin_email'] = implode(",", $request->req['resp_emails']);        
            if(isset( $request->req['post_expiry_form_id']))
                $options['post_expiry_form_id'] = $request->req['post_expiry_form_id'];
            //var_dump($request->req);die;
            if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
                $model->load_from_db($request->req['rm_form_id']);
                $model->set($options);
                if ($model->validate_limits('form_sett_limits')) {
                    $model->update_into_db();
                RM_Utilities::redirect('?page='.$next_page.'&rm_form_id='.$request->req['rm_form_id']);
                    return;
                } else
                    $visited = true;
            } else {
                echo '<div class="rmnotice">' . RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED') . '</div>';
                return;
            }
        }

        if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
            $data = new stdClass();
            $data->next_page = $next_page;
            if (!isset($visited))
                $model->load_from_db($request->req['rm_form_id']);
            $view = $this->mv_handler->setView('form_limits_sett');
            $data->model = $model;
            $data->form_dropdown = RM_Utilities::get_forms_dropdown($service);
        } else {
            $view = $this->mv_handler->setView('show_notice');
            $data = RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED');
        }
        if(defined('REGMAGIC_ADDON'))
            $data->forms = $service->get_all(null);
        $view->render($data);
    }

    function post_sub($model, $service, $request, $params) {
        $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
        if ($this->mv_handler->validateForm("form_sett_post_sub")) {
            $options['form_success_message'] = $request->req['form_success_message'];
            $options['form_redirect'] = isset($request->req['form_redirect']) ? $request->req['form_redirect'] : 'none';
            $options['form_redirect_to_page'] = $request->req['form_redirect_to_page'];
            $options['form_redirect_to_url'] = $request->req['form_redirect_to_url'];
            if(defined('REGMAGIC_ADDON')) {
                $options['form_is_unique_token'] = isset($request->req['form_is_unique_token']) ? $request->req['form_is_unique_token'] : null;
                $options['should_export_submissions'] = isset($request->req['should_export_submissions']) ? $request->req['should_export_submissions'] : null;
                $options['export_submissions_to_url'] = isset($request->req['export_submissions_to_url']) ? $request->req['export_submissions_to_url'] : null;
            }

            if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
                $model->load_from_db($request->req['rm_form_id']);
                $model->set($options);
                if ($model->validate_post_sub('form_sett_post_sub')) {
                    $model->update_into_db();
                RM_Utilities::redirect('?page='.$next_page.'&rm_form_id='.$request->req['rm_form_id']);
                    return;
                } else
                    $visited = true;
            } else {
                echo '<div class="rmnotice">' . RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED') . '</div>';
                return;
            }
        }
        if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
            $data = new stdClass();
            $data->next_page = $next_page;
            $view = $this->mv_handler->setView('form_post_sub_sett');
            if (!isset($visited))
                $model->load_from_db($request->req['rm_form_id']);
            $data->model = $model;
            $data->wp_pages = RM_Utilities::wp_pages_dropdown();
        }else {
            $view = $this->mv_handler->setView('show_notice');
            $data = RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED');
        }
        $view->render($data);
    }
    
     function email_templates($model, $service, $request, $params)
     {
         $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
         if ($this->mv_handler->validateForm("form_sett_email_templates")) {
            $options['form_user_activated_notification'] = $request->req['form_user_activated_notification'];
            $options['form_admin_ns_notification'] = $request->req['form_admin_ns_notification'];
            $options['form_nu_notification'] = $request->req['form_nu_notification'];
            
            $options['form_nu_notification_sub'] = $request->req['form_nu_notification_sub'];
            $options['form_user_activated_notification_sub'] = $request->req['form_user_activated_notification_sub'];
            $options['form_admin_ns_notification_sub'] = $request->req['form_admin_ns_notification_sub'];
             
            if(defined('REGMAGIC_ADDON')) {
                $options['form_activate_user_notification_sub']= $request->req['form_activate_user_notification_sub'];
                $options['act_link_sub']= $request->req['act_link_sub'];
                $options['form_activate_user_notification'] = $request->req['form_activate_user_notification'];
                $options['act_link_message'] = $request->req['act_link_message'];
            }
            
             if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
                 $model->load_from_db($request->req['rm_form_id']);
                 $model->set($options);
                     $model->update_into_db();
                 RM_Utilities::redirect('?page='.$next_page.'&rm_form_id='.$request->req['rm_form_id']);
                     return;
             } 
         }
 
         if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
             $model->load_from_db($request->req['rm_form_id']);
             $data = new stdClass();
             $data->next_page = $next_page;
             $data->model = $model;
             $view = $this->mv_handler->setView('form_email_templates');
         }
  		  
         $view->render($data);
     
     }
     
    function autoresponder($model, $service, $request, $params) {
        $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
        if ($this->mv_handler->validateForm("form_sett_autoresponder")) {

            $options['form_should_send_email'] = isset($request->req['form_should_send_email']) ? $request->req['form_should_send_email'] : null;
            $options['form_email_subject'] = $request->req['form_email_subject'];
            $options['form_email_content'] = $request->req['form_email_content'];
            
            if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
                $model->load_from_db($request->req['rm_form_id']);
                $model->set($options);
                if ($model->validate_autoresponder('form_sett_autoresponder')) {
                    $model->update_into_db();
                RM_Utilities::redirect('?page='.$next_page.'&rm_form_id='.$request->req['rm_form_id']);
                    return;
                } else {
                    $visited = true;
                }
            } else {
                echo '<div class="rmnotice">' . RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED') . '</div>';
                return;
            }
        }

        if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
            if (!isset($visited))
                $model->load_from_db($request->req['rm_form_id']);
            $data = new stdClass();
            $data->next_page = $next_page;
            $data->model = $model;
            $view = $this->mv_handler->setView('form_autoresponder_sett');
        }else {
            $view = $this->mv_handler->setView('show_notice');
            $data = RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED');
        }
        $view->render($data);
    }

    function accounts($model, $service, $request, $params) {
        $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
        if ($this->mv_handler->validateForm("form_sett_accounts")) {

            $options['form_type'] = isset($request->req['form_type']) ? RM_REG_FORM : RM_CONTACT_FORM;
            if($options['form_type']==RM_REG_FORM){
                $service->add_primary_fields($request->req['rm_form_id']);
            }
            else
            {
                $service->remove_primary_fields($request->req['rm_form_id']);
            }
            if(defined('REGMAGIC_ADDON')) {
                $options['default_form_user_role'] = $request->req['default_form_user_role'];
                $options['form_user_field_label'] = $request->req['form_user_field_label'];
                $options['hide_username']= isset($request->req['hide_username']) ? 1 : 0;
                // $options['user_auto_approval'] = isset($request->req['user_auto_approval']) ? $request->req['user_auto_approval'] : null;
            } else {
                $options['default_form_user_role'] = 'subscriber';
                $options['form_user_field_label'] = '';
            }
            $options['form_user_role'] = isset($request->req['form_user_role']) ? $request->req['form_user_role'] : array();
            $options['form_should_user_pick'] = isset($request->req['form_should_user_pick']) ? $request->req['form_should_user_pick'] : null;
            $options['auto_login'] = isset($request->req['auto_login']) ? 1 : null;                
            
            if (isset($request->req['rm_form_id'])  && (int)$request->req['rm_form_id']) {
                $model->load_from_db($request->req['rm_form_id']);
                if($options['default_form_user_role'] != null)
                {
                    $gopts= new RM_Options;
                    $default_forms=array();
                    $opt_default_forms=$gopts->get_value_of('rm_option_default_forms');
                    $default_forms= maybe_unserialize($opt_default_forms);
                    $def=$default_forms;
                    if(is_array($def))
                    {
                        foreach($def as $key => $val)
                        {
                            if($val == $model->get_form_id() && $key != $options['default_form_user_role'])
                            {
                                $default_forms[$key] = null;
                            }
                        }
                        $opt_default_forms=  maybe_serialize($default_forms);
                        $gopts->set_value_of('rm_option_default_forms',$opt_default_forms);
                    }
                }
                $model->set($options);
                if ($model->validate_accounts('form_sett_accounts')) {
                    $model->update_into_db();
                    do_action('rm_form_saved',$request->req['rm_form_id']);
                    RM_Utilities::redirect('?page='.$next_page.'&rm_form_id='.$request->req['rm_form_id']);
                    return;
                } else
                    $visited = true;
            } else {
                echo '<div class="rmnotice">' . RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED') . '</div>';
                return;
            }
        }

        if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
            $data = new stdClass();
            $data->next_page = $next_page;
            if(defined('REGMAGIC_ADDON'))
                $data->roles = RM_Utilities::user_role_dropdown(true, true);
            else
                $data->roles = RM_Utilities::user_role_dropdown(true);
            if (!isset($visited))
                $model->load_from_db($request->req['rm_form_id']);
            $data->model = $model;
            $view = $this->mv_handler->setView('form_accounts_sett');
        }else {
            $data = RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED');
            $view = $this->mv_handler->setView('show_notice');
        }
        $view->render($data);
    }

    function view($model=null, $service=null, $request=null, $params=null) {
        if (current_user_can('manage_options')) {
            if (!$request instanceof RM_Request) {
                check_ajax_referer( 'rm_form_settings_controller', 'rm_ajaxnonce' );
                $postdata = file_get_contents("php://input");
                $request = json_decode($postdata);
                if(isset($request->form_id) && (int)$request->form_id){
                    $model = new RM_Forms;
                    $model->load_from_db($request->form_id);
                    $model->set((array)$request);
                    $model->update_into_db();
                    RM_DBManager::update_form_published_pages($request->form_id);
                    echo 'saved';
                }else
                    echo 'not saved';
                die;
            }
            if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
                $data = new stdClass();
                $view = $this->mv_handler->setView('form_view_sett');
                $model->load_from_db($request->req['rm_form_id']);
                $data->model = $model;
            } else {
                $data = RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED');
                $view = $this->mv_handler->setView('show_notice');
            }
            $view->render($data);
        }
    }

    function mailchimp($model, $service, $request, $params) {
        $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
        if ($this->mv_handler->validateForm("form_sett_mailchimp")) {
            $options = array();
            $options['mailchimp_list'] = $request->req['mailchimp_list'];
            $options['mailchimp_mapped_email'] = isset($request->req['email'])?$request->req['email']:null;
            $options['mailchimp_relations'] = $service->get_mailchimp_mapping($request->req);
            $options['form_is_opt_in_checkbox'] = isset($request->req['form_is_opt_in_checkbox']) ? $request->req['form_is_opt_in_checkbox'] : null;
            $options['form_opt_in_text'] = isset($request->req['form_opt_in_text'])?$request->req['form_opt_in_text']:null;
            $options['form_opt_in_default_state'] = isset($request->req['form_opt_in_default_state']) ? $request->req['form_opt_in_default_state'] : null;
            if(defined('REGMAGIC_ADDON'))
                $options['enable_mailchimp'] = isset($request->req['enable_mailchimp'])?$request->req['enable_mailchimp']:null;
           
            if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
                $model->load_from_db($request->req['rm_form_id']);
                $model->set($options);
                $model->update_into_db();
                RM_Utilities::redirect('?page='.$next_page.'&rm_form_id='.$request->req['rm_form_id']);
                return;
            } else {
                echo '<div class="rmnotice">' . RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED') . '</div>';
                return;
            }
        }

        if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
            $data = new stdClass();
            $data->next_page = $next_page;
            $data->form_id = $request->req['rm_form_id'];
            $model->load_from_db($request->req['rm_form_id']);
            $data->mc_form_list = $model->form_options->mailchimp_list;
            if(defined('REGMAGIC_ADDON')) {
                $global_settings = new RM_Options;
                $mc_enable = $global_settings->get_value_of('enable_mailchimp');
                $mc_api = $global_settings->get_value_of('mailchimp_key');
            }
            if($data->mc_form_list || (defined('REGMAGIC_ADDON') && $data->mc_form_list && $mc_enable=='yes' && $mc_api!=null))
                $data->mc_fields = $service->mc_field_mapping($data->form_id, $model->form_options);
            else
                $data->mc_fields = null;
            $data->model = $model;
            $data->error=null;
            try
             {
             $mclist = $service->get_list();
             if($mclist==null)
              $data->error= RM_UI_Strings::get('MC_ERROR');    
             }
             catch(Exception $e)
             {
                 $data->mailchimp_list=null;
                 $data->error= RM_UI_Strings::get('MC_ERROR');
             } 
            $data->mailchimp_list[''] = RM_UI_Strings::get('SELECT_LIST');
            if($mclist && isset($mclist['lists']))
            {
                foreach ($mclist['lists'] as $mcl) {
                    $data->mailchimp_list[$mcl['id']] = $mcl['name'];
                }
            }
            $view = $this->mv_handler->setView('form_mc_sett');
        }else{
            $data = RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED');
            $view = $this->mv_handler->setView('show_notice');
        }


        $view->render($data);
    }
    
    function access_control($model, $service, $request, $params) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Form_Settings_Controller_Addon();
            return $addon_controller->access_control($model, $service, $request, $params, $this);
        }
        $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
        if (isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id'])) {
            $model->load_from_db($request->req['rm_form_id']);
            $data = new stdClass();
            $data->next_page = $next_page;
            $data->model = $model;
            $data->form_id = $request->req['rm_form_id'];
            $data->all_roles = RM_Utilities::user_role_dropdown();
            $view = $this->mv_handler->setView('form_access_control_sett');
        } else {
            $data = RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED');
           // $view = $this->mv_handler->setView('show_notice');
        }
        $view->render($data);
    }

 function ccontact($model, $service, $request, $params) {
     $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
     if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
          
            $model->load_from_db($request->req['rm_form_id']);
            $data = new stdClass();
            $data->next_page = $next_page;
            $data->form_name = $model->form_name;
            $data->form_id = $request->req['rm_form_id'];
            $view = $this->mv_handler->setView('form_sett_constant_contact');
        }else{
            $data = RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED');
            $view = $this->mv_handler->setView('show_notice');
        }


        $view->render($data);
    }
    function aweber($model, $service, $request, $params) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Form_Settings_Controller_Addon();
            return $addon_controller->aweber($model, $service, $request, $params, $this);
        }
     $next_page = (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
     if (isset($request->req['rm_form_id']) && (int)$request->req['rm_form_id']) {
          
          $model->load_from_db($request->req['rm_form_id']);
            $data = new stdClass();
            $data->next_page = $next_page;
            $data->form_name = $model->form_name;
            $data->form_id = $request->req['rm_form_id'];
            $view = $this->mv_handler->setView('form_sett_aw');
        }else{
            $data = RM_UI_Strings::get('MSG_FS_NOT_AUTHORIZED');
            $view = $this->mv_handler->setView('show_notice');
        }


        $view->render($data);
    }
    
    public function add_form_stats(&$data){
        
        $stat_service = new RM_Analytics_Service;
                
        $total_entries =  (int)$stat_service->count('STATS', array('form_id' => (int)$data->form_id));

       //Average and failure rate
        $failed_submission = (int)$stat_service->count('STATS', array('form_id' => (int)$data->form_id, 'submitted_on' => null));
        
        $banned_submission = (int)$stat_service->count('STATS', array('form_id' => (int)$data->form_id, 'submitted_on' => 'banned'));
       
        $successful_submission = $total_entries - $failed_submission - $banned_submission;
        
        $data->conversion_rate = $total_entries ? round(($successful_submission / $total_entries)*100,2) : 0;
        $data->avg_time = $stat_service->get_average_filling_time($data->form_id);
        $data->visitors_count = $stat_service->get_visitors_count($data->form_id);
        $data->field_count = $stat_service->count('FIELDS', array('form_id' => $data->form_id), array('%d'));

    }
    
    public function qck_toggle($form, $service, $request){
        $form_id= absint($request->req['form_id']);
        $form->load_from_db($form_id);
        
        switch($request->req['name']){
            case 'enable_captcha':
                $option_val = $request->req['value'] === 'true' ? 'default' : 'no';
                break;
            case 'user_auto_approval' : 
                $option_val = $request->req['value'] === 'true' ? 'yes' : 'no';
                break;
            default :
                $option_val = $request->req['value'] === 'true' ? 1 : 0;
        }
        $option_name= $request->req['name'];
        if($option_name=='form_type'){
             do_action('rm_form_type_changed',$form_id,$option_val,!$option_val);
        }
         
        $form->set(array($request->req['name'] => $option_val));
        
        if($form->update_into_db())
            echo 'successfull';
        else
            echo 'failed';
        
        do_action('rm_form_saved',$request->req['form_id']);
        die;
        
    }
    
    public function add_form_submission_data(&$data){
        $service  = new RM_Submission_Service;
        $latest_subs = $service->get('SUBMISSIONS', array('form_id' => $data->form_id, 'child_id' => 0), array('%d', '%d'), 'results', 0, 6, '*', 'submission_id', true);
        $latest_sub_data = array();
        $i =0;
        if(is_array($latest_subs))
        foreach($latest_subs as $submission){
            $latest_sub_data[$i] = new stdClass();
            $user = get_user_by('email', $submission->user_email);
            if($user instanceof WP_User){
                if(isset($user->first_name) && trim($user->first_name))
                    $latest_sub_data[$i]->user_name = $user->first_name.' '.$user->last_name;
                else
                    $latest_sub_data[$i]->user_name = $user->display_name ? : $user->user_login;
                $latest_sub_data[$i]->user_avatar = get_avatar_url($user->ID) ? : RM_IMG_URL.'placeholder.jpg';
            }
            else{
                $latest_sub_data[$i]->user_name = $submission->user_email;
                $latest_sub_data[$i]->user_avatar = get_avatar_url($submission->user_email) ? : RM_IMG_URL.'placeholder.jpg';
            }
            $latest_sub_data[$i]->is_read = $submission->is_read;
            $latest_sub_data[$i]->submitted_on = RM_Utilities::localize_time($submission->submitted_on);
            $latest_sub_data[$i]->id = $submission->submission_id;
            $i++;
        } 
        $data->latest_subs = $latest_sub_data;
        $data->sub_count = $service->count('SUBMISSIONS', array('form_id' => $data->form_id, 'child_id' => 0), array('%d', '%d'));
    }
    
    public function add_form_attchment_data(&$data){
        $service = new RM_Attachment_Service();
        $data->attachment_count = $service->get_all_form_attachments($data->form_id, 'count');
        $attachments = $service->get_all_form_attachments($data->form_id, 'posts',6);
        //var_dump($attachments);die;
        $data->attachments = array();
        $i = 0;
        if ($attachments)
            foreach ($attachments as $att) {
                $data->attachments[$i] = new stdClass;
                $att_mime = explode('/', $att->post_mime_type);
                $att_type = $att_mime[0];
                switch ($att_type) {
                    case 'image':
                        $data->attachments[$i]->image_url = $att->guid;
                        break;
                    default:
                        $data->attachments[$i]->image_url = RM_IMG_URL . 'attachment-placeholder.png';
                }
                $data->attachments[$i]->name = basename($att->guid);
                $data->attachments[$i]->date = RM_Utilities::localize_time($att->post_date);
                $data->attachments[$i]->is_new = $att->post_date >= RM_Utilities::get_current_time(time() - (60 * 60));
                $i++;
            }
    }
    
    function override($model, $service, $request) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Form_Settings_Controller_Addon();
            return $addon_controller->override($model, $service, $request, $this);
        }
        $next_page =  (isset($_GET['rdrto']) && $_GET['rdrto']) ? $_GET['rdrto'] : "rm_form_sett_manage";
        
        $data = new stdClass();
        $data->next_page = $next_page;
        $view = $this->mv_handler->setView('form_sett_override');
        $data->model = $model;
        $view->render($data);
    }
    
    public function add_form_timewise_stat(&$data){
        
        $service = new RM_Analytics_Service();
        
        if($data->timerange > 90)
             $data->timerange = 90;
         
         $data->day_wise_stat = $service->day_wise_submission_stats($data->form_id, $data->timerange);
    }
    
    function profilegrid($model, $service, $request) {
        $data = new stdClass();
        $installUrl = admin_url('update.php?action=install-plugin&plugin=profilegrid-user-profiles-groups-and-communities');
        $installUrl = wp_nonce_url($installUrl, 'install-plugin_profilegrid-user-profiles-groups-and-communities');
        $data->pg_install_url = $installUrl;
        
        $view = $this->mv_handler->setView('profilegrid');
        $view->render($data);
    }
    
}
