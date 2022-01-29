<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_rm_front_controller
 *
 * @author CMSHelplive
 */
class RM_Front_Controller {

    public $mv_handler;

    public function __construct() {
        $this->mv_handler = new RM_Model_View_Handler;
    }

    public function set_otp($model, $service, $request, $params) {
        $key = false;
        $login_service= new RM_Login_Service();
        //var_dump($request->req);
        
        $gopt=new RM_Options;
        $blocked_ips=array();
        $blocked_ips=$gopt->get_value_of('banned_ip');
        
        $ip_as_arr = explode('.', $_SERVER['REMOTE_ADDR']);
        if(count($ip_as_arr)!=4){
            $sanitized_user_ip = $_SERVER['REMOTE_ADDR'];
        }else{
            //$sanitized_user_ip = sprintf("%'03s.%'03s.%'03s.%'03s", $ip_as_arr[0], $ip_as_arr[1], $ip_as_arr[2], $ip_as_arr[3]);
            $sanitized_user_ip = sprintf("%s.%s.%s.%s", $ip_as_arr[0], $ip_as_arr[1], $ip_as_arr[2], $ip_as_arr[3]);
        }
        $blocked_ips= is_array($blocked_ips) ? $blocked_ips : array();
        
        if(in_array($sanitized_user_ip, $blocked_ips)){
            echo '{"error":true,"show":"#rm_otp_enter_otp","hide":"#rm_noelement","reload":false,"msg":"'.__('Your IP has been banned by the Admin.','custom-registration-form-builder-with-submission-manager').'"}';exit;
        }
        
        if (isset($request->req['rm_otp_email']))
            $email = $request->req['rm_otp_email'];

        if (isset($request->req['rm_otp_key']))
            $key = $request->req['rm_otp_key'];

        if (isset($request->req['rm_user_key'], $request->req['rm_username'])) {
            $user_key = $request->req['rm_user_key'];
            $username = $request->req['rm_username'];
            $remember = isset($request->req['rm_remember']) ? true : false;
            
            $response= $service->login($username, $user_key, $remember);
            $decoded_response= json_decode($response);
           
            if(empty($decoded_response)){
                echo $response; 
                exit;
            }
            $log= array('ip'=> $_SERVER['REMOTE_ADDR'],'time'=> current_time('timestamp'),'status'=>1,'type'=>'normal','result'=>'success');
            if(email_exists($email)){
                    $log['email']= $email;
            }
            else
            {
                $log['username_used']= $email;
                
                $user = get_user_by('login',$email);
                if($user)
                {
                    $log['email']= $user->user_email;
                }
            }
            if(!empty($decoded_response->error)){
                $log['status']=0;
                $log['result']='failure';
                $log['failure_reason']= 'incorrect_password'; 
                
            }
            
            
            $login_service->insert_login_log($log);
            echo $response;
            exit;
        }

        echo $service->set_otp($email, $key);

        exit;
    }

    public function submissions($model, RM_Front_Service $service, $request, $params) {
        
        //Load custom tabs
        wp_enqueue_script("rm_ctabs_script",RM_BASE_URL."public/js/rm_custom_tabs.js");
        wp_enqueue_style("rm_ctabs_style",RM_BASE_URL."public/css/rm_custom_tabs.css");
        if(defined('REGMAGIC_ADDON'))
            wp_enqueue_style("rm_ctabs_style_addon",RM_ADDON_BASE_URL."public/css/rm_custom_tabs.css");
        $layout_view= '';
         if(!empty($params['attr']) && !empty($params['attr']['view'])){
             $layout_view= $params['attr']['view'];
        }
         
        $i = $j = 0;
        $user_email = $service->get_user_email();
        //var_dump($user_email);die;
        if (null != $user_email) {

            if (isset($request->req['submission_id'])) {
                $submission = new RM_Submissions();
                $submission->load_from_db($request->req['submission_id']);

                $child_id = $submission->get_child_id();
                if ($child_id != 0) {
                    $request->req['submission_id'] = $submission->get_last_child();
                    return $this->submissions($model, $service, $request, $params);
                }

                if ($submission->get_user_email() == $user_email) {
                    $view = $this->mv_handler->setView('front_submission_data', true);

                    $data = new stdClass;

                    $settings = new RM_Options;

                    if ($service->get_editable_fields($submission->get_form_id()))
                        $data->is_editable = true;
                    else
                        $data->is_editable = false;

                    $data->is_authorized = true;
                    $data->submission = $submission;

                    $data->payment = $service->get('PAYPAL_LOGS', array('submission_id' => $service->get_oldest_submission_from_group($submission->get_submission_id())), array('%d'), 'row', 0, 99999);

                    if ($data->payment != null) {
                        $data->payment->total_amount = $settings->get_formatted_amount($data->payment->total_amount, $data->payment->currency);

                        if ($data->payment->log)
                            $data->payment->log = maybe_unserialize($data->payment->log);
                    }

                    if(defined('REGMAGIC_ADDON')) {
                        $submission_service = new RM_Submission_Service;
                        $data->notes = $submission_service->get_notes($submission->get_submission_id());
                    } else {
                        $data->notes = $service->get('NOTES', array('submission_id' => $submission->get_submission_id(), 'status' => 'publish'), array('%d', '%s'), 'results', 0, 99999, '*', null, true);
                    }
                    $i = 0;
                    if (is_array($data->notes))
                        foreach ($data->notes as $note) {
                            $data->notes[$i]->author = get_userdata($note->published_by)->display_name;
                            if ($note->last_edited_by)
                                $data->notes[$i++]->editor = get_userdata($note->last_edited_by)->display_name;
                            else
                                $data->notes[$i++]->editor = null;
                        }
                    /*
                     * Check submission type
                     */
                    $form = new RM_Forms();
                    $form->load_from_db($submission->get_form_id());
                    $form_type = $form->get_form_type() == "1" ?__('Registration','custom-registration-form-builder-with-submission-manager') : __('Non WP Account','custom-registration-form-builder-with-submission-manager');
                    $data->form_type = $form_type;
                    $data->form_type_status = $form->get_form_type();
                    $data->form_name = $form->get_form_name();
                    $data->form_is_unique_token = $form->get_form_is_unique_token();

                    /*
                     * User details if form is registration type
                     */
                    if ($form->get_form_type() == "1") {
                        $email = $submission->get_user_email();
                        if ($email != "") {
                            $user = get_user_by('email', $email);
                            $data->user = $user;
                        }
                    }
                    return $view->read($data);
                } else
                    $view = $this->mv_handler->setView('not_authorized', true);
                $msg = RM_UI_Strings::get('MSG_INVALID_SUBMISSION_ID_FOR_EMAIL');
                return $view->read($msg);
            } else { //end if for individual submission. Started for all submissions
                $data = new stdClass;
                $data->is_authorized = true;
                $data->submissions = array();
                $data->form_names = array();
                $data->submission_exists = false;
                $data->total_submission_count = 0;

                //data for user page
                $user = get_user_by('email', $user_email);
                if ($user instanceof WP_User) {
                    $data->is_user = true;
                    $data->user = $user;
                    $data->custom_fields = $service->get_custom_fields($user_email);
                    if(is_array($data->custom_fields))
                        foreach($data->custom_fields as $id => $f)
                            if($f->type == 'Rating')unset($data->custom_fields[$id]);
                } else {
                    $data->is_user = false;
                }

                //For pagination of submissions
                $entries_per_page_sub = 20;
                $req_page_sub = (isset($request->req['rm_reqpage_sub']) && $request->req['rm_reqpage_sub'] > 0) ? $request->req['rm_reqpage_sub'] : 1;
                $offset_sub = ($req_page_sub - 1) * $entries_per_page_sub;

                if (isset($request->req['rm_edit_user_details'])) {
                    $form_ids = json_decode(stripslashes($request->req['form_ids']));
                    $submissions = $service->get_latest_submission_for_user($user_email, $form_ids);
                    $data->total_submission_count = $total_entries_sub = count($submissions);
                    $distinct = true;
                } else {
                    $submissions = $service->get_submissions_by_email($user_email, $entries_per_page_sub, $offset_sub);
                    $data->total_submission_count = $total_entries_sub = $service->get_submission_count($user_email);
                    $distinct = false;
                }

                $submission_ids = array();
                if ($submissions) {
                    $data->submission_exists = true;
                    foreach ($submissions as $submission) {

                        $form_name = $service->get('FORMS', array('form_id' => $submission->form_id), array('%d'), 'var', 0, 1, 'form_name');

                        $data->submissions[$i] = new stdClass();
                        $data->submissions[$i]->submission_ids = array();
                        $data->submissions[$i]->submission_id = $submission->submission_id;
                        $data->submissions[$i]->submitted_on = $submission->submitted_on;
                        $data->submissions[$i]->form_name = $form_name;
                        $data->form_names[$submission->submission_id] = $form_name;
                        $submission_ids[$i] = $service->get_oldest_submission_from_group($submission->submission_id);
                        $i++;
                    }
                    $total_entries_pay = 0;
                    $settings = new RM_Options;
                    $data->date_format = get_option('date_format');
                    $data->payments = $service->get_payments_by_submission_id($submission_ids, 999999, 0, null, true);
                    if ($data->payments)
                        foreach ($data->payments as $i => $p) {
                            if (!isset($data->form_names[$p->submission_id])) {
                                $data->form_names[$p->submission_id] = $service->get('FORMS', array('form_id' => $p->form_id), array('%d'), 'var', 0, 1, 'form_name');
                            }
                            $data->payments[$i]->total_amount = $settings->get_formatted_amount($data->payments[$i]->total_amount, $data->payments[$i]->currency);
                            $total_entries_pay = $i+1;
                        }

                    //For pagination of payments
                    $entries_per_page_pay = 20;
                    $req_page_pay = (isset($request->req['rm_reqpage_pay']) && $request->req['rm_reqpage_pay'] > 0) ? $request->req['rm_reqpage_pay'] : 1;
                    $data->offset_pay = $offset_pay = ($req_page_pay - 1) * $entries_per_page_pay;
                    $data->total_pages_pay = (int) ($total_entries_pay / $entries_per_page_pay) + (($total_entries_pay % $entries_per_page_pay) == 0 ? 0 : 1);
                    $data->curr_page_pay = $req_page_pay;
                    $data->starting_serial_number_pay = $offset_pay + 1;
                    $data->end_offset_this_page = ($data->curr_page_pay < $data->total_pages_pay) ? $data->offset_pay + $entries_per_page_pay : $total_entries_pay;
                    //Pagination Ends payments
                    //$data->rm_slug = $request->req['page'];
                    //$data->stat_data = $service->get_form_stats($data->current_form_id, $offset, $entries_per_page);
                    $data->total_pages_sub = (int) ($total_entries_sub / $entries_per_page_sub) + (($total_entries_sub % $entries_per_page_sub) == 0 ? 0 : 1);
                    $data->curr_page_sub = $req_page_sub;
                    $data->starting_serial_number_sub = $offset_sub + 1;
                    //Pagination Ends submissions

                    if(defined('REGMAGIC_ADDON')) {
                        $data->inbox = $this->get_inbox_data($user_email, $service, $request, $params);
                    }
                    
                    $data->active_tab_index = $distinct ? 1 : (isset($request->req['rm_tab']) ? (int) $request->req['rm_tab'] : 0);
                    switch($layout_view){
                        case 'registrations': $view = $this->mv_handler->setView('registrations_view', true); break;
                        case 'payments': $view = $this->mv_handler->setView('user_payments_view', true); break;  
                        case 'inbox': $view = $this->mv_handler->setView('front_inbox', true); break;
                        case 'orders': 
                        case 'downloads':
                        case 'addresses': $data->extened_view= $layout_view; $view = $this->mv_handler->setView('extended_sub_view', true); break;    
                        default: $view = $this->mv_handler->setView('front_submissions', true);
                    }
                    return $view->read($data);
                } elseif ($data->is_user === true) {
                    $data->payments = false;
                    $data->submissions = false;
                    $data->active_tab_index = 0;
                    if(defined('REGMAGIC_ADDON')) {
                        $data->inbox = $this->get_inbox_data($user_email, $service, $request, $params);
                    } else {
                        $view = $this->mv_handler->setView('front_submissions', true);
                    }
                    
                    switch($layout_view){
                        case 'registrations': $view = $this->mv_handler->setView('registrations_view', true); break;
                        case 'payments': $view = $this->mv_handler->setView('user_payments_view', true); break;  
                        case 'inbox': $view = $this->mv_handler->setView('front_inbox', true); break;
                        case 'orders':
                        case 'downloads':
                        case 'addresses':$data->extened_view= $layout_view; $view = $this->mv_handler->setView('extended_sub_view', true); break;    
                        default: $view = $this->mv_handler->setView('front_submissions', true);
                    }
                    return $view->read($data);
                } else {
                    $view = $this->mv_handler->setView('not_authorized', true);
                    $msg = RM_UI_Strings::get('MSG_NO_SUBMISSION_FRONT');
                    return $view->read($msg);
                }
            }
        } else {
            $login_service= new RM_Login_Service();
            $v_options= $login_service->get_validations();
            $view = $this->mv_handler->setView('not_authorized', true);
            //$msg = RM_UI_Strings::get('MSG_NOT_AUTHORIZED');
            $msg = $v_options['sub_error_msg'];
            return $view->read($msg,false,true);
        }
    }

    public function log_off($model, RM_Front_Service $service, $request, $params) {
        $user_email = $service->get_user_email();

        if (null != $user_email) {
            $service->log_front_user_off($user_email);
            if (!isset($request->req['rm_do_not_redirect']))
                RM_Utilities::redirect(get_permalink(get_option('rm_option_front_sub_page_id')));
            else
                RM_Utilities::redirect(get_permalink());
        }
    }

    public function reset_pass_page($model, RM_Front_Service $service, $request) {
        if (is_user_logged_in()) {
            if ($this->mv_handler->validateForm("rm_reset_pass_form")) {
                if ($service->should_reset_password($request->req)) {
                    wp_set_password($request->req['new_pass'], get_current_user_id());
                    RM_Utilities::redirect(wp_login_url(get_permalink()) . '&is_reset=1', false, 0, 1000);
                    return RM_UI_Strings::get('PASS_RESET_SUCCESSFUL');
                }
            }
            $view = $this->mv_handler->setView('reset_password', true);
            return $view->read();
        }
    }

    public function save_fab_settings($model, $service, $request) {
        $theme = isset($request->req['fab_theme']) ? $request->req['fab_theme'] : false;
        $color = isset($request->req['fab_color']) ? $request->req['fab_color'] : false;

        $service->save_fab_settings($theme, $color);
    }

    public function fab($model, $service, $request) {
        $setting = new RM_Options;
        if ($setting->get_value_of('display_floating_action_btn') === 'yes') {
            $param = new stdClass ();
            $param->action_btn_style = 'background-color:#' . $setting->get_value_of('floating_icon_bck_color');

            $param->default_form = (int) $setting->get_value_of('default_form_id');
            $floating_widget = new RM_Floating_Widget($param);
            $floating_widget->show_widget();
        }
    }
    
    public function user_list($model, $service, $request, $params) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Front_Controller_Addon();
            return $addon_controller->user_list($model, $service, $request, $params, $this);
        }
    }
    
    public function get_inbox_data($user_email, $service, $request, $params) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Front_Controller_Addon();
            return $addon_controller->get_inbox_data($user_email, $service, $request, $params);
        }
    }

}