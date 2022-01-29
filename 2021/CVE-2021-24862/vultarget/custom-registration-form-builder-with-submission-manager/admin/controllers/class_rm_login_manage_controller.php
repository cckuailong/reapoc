<?php
/**
 *
 * @author CMSHelplive
 */
class RM_Login_Manage_Controller {

    public $mv_handler;

    function __construct() {
        $this->mv_handler = new RM_Model_View_Handler();
    }
 
    public function manage($model, $service, $request, $params) {
        $view = $this->mv_handler->setView('login_field_manager');
        if (isset($request->req['rm_action']) && $request->req['rm_action'] === 'delete')
        {
            $field_index= absint($request->req['field_index']); 
            $service->remove_field(array($field_index));
            RM_Utilities::redirect(admin_url('/admin.php?page=rm_login_field_manage'));
            die();
        }
        
        $form= $service->get_form();
        $form= json_decode($form,true);
        $btn_options= $service->get_button_config();
        $data = array('form_fields'=>$form['form_fields'],'buttons'=>$btn_options,'all_forms'=>RM_Utilities::get_forms_dropdown(new RM_Services()));
        $view->render($data);
    }
    
    public function add($model, $service, $request, $params) {
        $form= $service->get_form();
        $form= json_decode($form,true);
        $fields= &$form['form_fields'];
        
        if ($this->mv_handler->validateForm("login-add-field")) {
            $field_type= sanitize_text_field($request->req['field_type']);
            $field_index = isset($request->req['field_index']) ? $request->req['field_index'] : null;
            $model->initialize($field_type);
            $model->set($request->req);
            $login_form= array();
            $field_options= $model->get_field_options();
            if($field_index===null){  // Inserting new field
                array_push($fields,$field_options);
            }
            else{                     // Updating existing field  
                foreach($fields as $index=>$single_field){
                    if($single_field['field_type']==$field_type){
                        $fields[$index]= $field_options;
                    }
                };
            }
            
            $f_icon = new stdClass;
            $f_icon->codepoint = $request->req['input_selected_icon_codepoint'];
            $f_icon->fg_color = $request->req['icon_fg_color'];
            $f_icon->bg_color = $request->req['icon_bg_color'];
            $f_icon->shape = $request->req['icon_shape'];
            $f_icon->bg_alpha = $request->req['icon_bg_alpha'];
            $field_options['icon']=  $f_icon;   
            
            $service->update_form_fields($form);
            RM_Utilities::redirect(admin_url('/admin.php?page=rm_login_field_manage'));
        } else {
            $view = $this->mv_handler->setView('login_field_add');
            $params= $request->req;
            $selected_field = isset($request->req['field_type']) ? $request->req['field_type'] : null;
            $field_index = isset($request->req['field_index']) ? $request->req['field_index'] : null;
            // Loading specific field data
            $field= array();
            if($field_index!==null){
                foreach($fields as $index=>$single_field){
                    if($single_field['field_type']==$selected_field){
                        $field= $fields[$index]; 
                    }
                }
            }
            $data = compact("params","selected_field","field_index","field");
            $view->render($data);
        }
    }
    
    public function add_widget($model, $service, $request, $params){
        if (isset($request->req['rm_form_page_no']))
            $form_page_no = $request->req['rm_form_page_no'];
        else
            $form_page_no = 1;
        
        if ($this->mv_handler->validateForm("add-widget")){
            $request->req['page_no'] = $form_page_no;
            $new_field_order = intval($service->get_fields_highest_order($request->req['rm_form_id'], $form_page_no)) + 1;
            $request->req['field_order'] = $new_field_order;
            $model->set($request->req);
            /////////////////////
            if(isset($request->req['field_id'])){
                    $temp_model= new RM_Fields();
                    $temp_model->load_from_db($request->req['field_id']);
                    $request->req['conditions']= $temp_model->get_field_conditions();
                    $service->update($model, $service, $request, $params);
            } else{
                $service->add($model, $service, $request, $params);
               // die('firsttime');
            }
            
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success . '&rm_form_id=' . $request->req["rm_form_id"] . '&rm_form_page_no=' . $form_page_no));
        }
        isset($request->req['rm_field_id']) ? $model->load_from_db($request->req['rm_field_id']) : '';
        $data = new stdClass;
        $data->selected_field = isset($request->req['rm_field_type']) ? $request->req['rm_field_type'] : null;
        
        $data->form_id = $request->req['rm_form_id'];
        $data->model= $model;
        $view = $this->mv_handler->setView("add_widget");
        $view->render($data);
    }
    
    public function set_order($model, $service, $request, $params) {
        $indexes= $request->req['data'];
        $form= json_decode($service->get_form(),true);
        $old_fields= $form['form_fields'];
        $new_fields= array();
        foreach($indexes as $index=>$field_index){
            $new_fields[$index]= $old_fields[$field_index];
        }
        $form['form_fields']= array_values($new_fields);
        $service->update_form_fields($form);
    }
    
    public function view_sett($model=null, $service=null, $request=null, $params=null){
        
         if (!$request instanceof RM_Request) {
            $postdata = file_get_contents("php://input");
            $request = (array) json_decode($postdata);
            $service= new RM_Login_Service;
            $service->save_form_design($request);
            
            $buttons= $service->get_button_config();
            $buttons['login_btn']=$request['form_submit_btn_label'];
            $service->update_button_config($buttons);
            echo 'saved';
            die;
        }
        
        $fields= $service->get_un_password_fields();
        $design= $service->get_form_design();
       
        if($design==false){
            $design= array();
        }
       
        $view = $this->mv_handler->setView("login_view_sett");
        $buttons= $service->get_button_config();
        $view->render(compact('fields','design','buttons'));
    }
   
    public function sett_manage($model, $service, $request, $params){ 
        $data = new stdClass();
        $view = $this->mv_handler->setView("login_sett_manage");
        
        if(isset($request->req['action']) && $request->req['action']=='rm_login_log_export'){
            $submissions = $service->get_logs_to_export();
            $csv = $service->create_csv($submissions);
            $service->download_file($csv,true);
        }else if(isset($request->req['action']) && $request->req['action']=='rm_login_log_reset'){
            $reset_log = $service->reset_login_log();
        }
        
        if(isset($request->req['rm_tr'])){
            $data->timerange = $request->req['rm_tr'];            
        } else {
            $data->timerange = '30';
        }
        $data->all_forms = RM_Utilities::get_forms_dropdown(new RM_Services());
        $this->add_form_stats($data);
        
        $this->add_form_timewise_stat($data);
        
        $view->render($data);
    }
    
    public function log_export(){
        
    }
    
    public function add_form_stats(&$data){
        /*
        $stat_service = new RM_Analytics_Service;
                
        $total_entries =  (int)$stat_service->count('STATS', array('form_id' => (int)$data->form_id));

       //Average and failure rate
        $failed_submission = (int)$stat_service->count('STATS', array('form_id' => (int)$data->form_id, 'submitted_on' => null));
        
        $banned_submission = (int)$stat_service->count('STATS', array('form_id' => (int)$data->form_id, 'submitted_on' => 'banned'));
       
        $successful_submission = $total_entries - $failed_submission - $banned_submission;
        
        $data->conversion_rate = $total_entries ? round(($successful_submission / $total_entries)*100,2) : 0;
        $data->avg_time = $stat_service->get_average_filling_time($data->form_id);
        $data->visitors_count = $stat_service->get_visitors_count($data->form_id);*/
        $data->buttons = RM_DBManager::get_login_fields_details('buttons');
        $data->login_count = RM_DBManager::count_login_log();
        $data->field_count = RM_DBManager::count_login_field();
        $data->success_rate = RM_DBManager::get_login_success_rate();
        $data->login_log = RM_DBManager::get_login_log(6);      //Number of record to show 6

    }
    
    public function add_form_timewise_stat(&$data){
        
        $service = new RM_Analytics_Service();
        
        if($data->timerange > 90)
             $data->timerange = 90;
         
         $data->day_wise_stat = $service->day_wise_login_stats($data->timerange);
    }
    
    public function sett_redirections($model, $service, $request, $params){
        $data= new stdClass();
        $user_service= new RM_User_Services();
        $data->roles= $user_service->get_user_roles();
        if($this->mv_handler->validateForm("add-login-redirection")) {
            if($request->req['redirection_type']=='role_based' && empty($request->req['role_based_login_redirection'])){
                RM_PFBC_Form::setError('add-login-redirection',__('You must select the role to implement the redirections.','custom-registration-form-builder-with-submission-manager'));
            }else{
                $params= array();
                $params['redirection_type']= sanitize_text_field($request->req['redirection_type']);
                $params['redirection_link']= sanitize_text_field($request->req['redirection_link']);
                if(isset($request->req['admin_redirection_link'])){
                    $params['admin_redirection_link']= absint($request->req['admin_redirection_link']);
                } else{
                    $params['admin_redirection_link']= 0;
                }

                $params['logout_redirection']= $request->req['logout_redirection'];
                if(isset($request->req['role_based_login_redirection'])){
                    $params['role_based_login_redirection']= $request->req['role_based_login_redirection'];
                }
                else{
                   $params['role_based_login_redirection']= array(); 
                }
                foreach($data->roles as $role=>$role_name){
                    $role= strtolower(str_replace(' ', '', $role));
                    if(isset($request->req[$role.'_login_redirection'])){
                        $role_login_redirection= $role.'_login_redirection';
                        $params[$role_login_redirection] = sanitize_text_field($request->req[$role.'_login_redirection']);
                    }

                    if(isset($request->req[$role.'_logout_redirection'])){
                        $role_logout_redirection= $role.'_logout_redirection';
                        $params[$role_logout_redirection] = sanitize_text_field($request->req[$role.'_logout_redirection']);
                    }
                }

                $service->update_redirection($params);
                RM_Utilities::redirect(admin_url('/admin.php?page=rm_login_sett_manage'));
            }
        }
        $data->params= $service->get_redirections();
        
        $view = $this->mv_handler->setView("login_sett_redirections");
        $view->render($data);
    }
    
    public function val_sec($model, $service, $request, $params){
        $data= new stdClass();
        if($this->mv_handler->validateForm("add-login-validation")) {
            $params= array();
            $params['un_error_msg']= sanitize_text_field($request->req['un_error_msg']);
            $params['pass_error_msg']= sanitize_text_field($request->req['pass_error_msg']);
            $params['sub_error_msg']= sanitize_text_field($request->req['sub_error_msg']);
            $params['en_recovery_link']= isset($request->req['en_recovery_link']) ? absint($request->req['en_recovery_link']) : 0;
            $params['en_failed_user_notification']= isset($request->req['en_failed_user_notification']) ? absint($request->req['en_failed_user_notification']) : 0;
            $params['en_failed_admin_notification']= isset($request->req['en_failed_admin_notification']) ? absint($request->req['en_failed_admin_notification']) : 0;
            $params['en_captcha']= isset($request->req['en_captcha']) ? absint($request->req['en_captcha']) : 0;
            $params['allowed_failed_attempts']= absint($request->req['allowed_failed_attempts']);
            $params['allowed_failed_duration']= absint($request->req['allowed_failed_duration']);
            $params['en_ban_ip']= isset($request->req['en_ban_ip']) ? absint($request->req['en_ban_ip']) : 0;
            $params['allowed_attempts_before_ban']= isset($request->req['allowed_attempts_before_ban'])?absint($request->req['allowed_attempts_before_ban']):6;
            $params['allowed_duration_before_ban']= isset($request->req['allowed_duration_before_ban'])?absint($request->req['allowed_duration_before_ban']):60;
            $params['ban_type']= isset($request->req['ban_type'])?sanitize_text_field($request->req['ban_type']):'temp';
            $params['ban_duration']= isset($request->req['ban_duration'])?absint($request->req['ban_duration']):1440;
            $params['ban_error_msg']= isset($request->req['ban_error_msg'])?$request->req['ban_error_msg']:'Your IP has been banned by the Admin due to repeated failed login attempts.';
            $params['notify_admin_on_ban']= isset($request->req['notify_admin_on_ban']) ? absint($request->req['notify_admin_on_ban']) : 0;
            $service->update_validations($params);
            RM_Utilities::redirect(admin_url('/admin.php?page=rm_login_sett_manage'));
        }
        $data->params= $service->get_validations();
        $view = $this->mv_handler->setView("login_sett_validation");
        $view->render($data);
    }
    
    public function recovery($model, $service, $request, $params){
        $data= new stdClass();
        if($this->mv_handler->validateForm("login-recovery")) {
            $params= array();
            $params['en_pwd_recovery']= isset($request->req['en_pwd_recovery']) ? absint($request->req['en_pwd_recovery']) : 0;
            $params['recovery_link_text']= sanitize_text_field($request->req['recovery_link_text']);
            $params['recovery_page']= absint($request->req['recovery_page']);
            $params['rec_email_label']= sanitize_text_field($request->req['rec_email_label']);
            $params['rec_btn_label']= sanitize_text_field($request->req['rec_btn_label']);
            $params['rec_link_sent_msg']= wp_kses_post(stripslashes($request->req['rec_link_sent_msg']));
            $params['rec_email_not_found_msg']= wp_kses_post(stripslashes($request->req['rec_email_not_found_msg']));
            $params['rec_new_pass_label']= sanitize_text_field($request->req['rec_new_pass_label']);
            $params['rec_conf_pass_label']= sanitize_text_field($request->req['rec_conf_pass_label']);
            $params['rec_pass_btn_label']= sanitize_text_field($request->req['rec_pass_btn_label']);
            $params['rec_pass_match_err']= sanitize_text_field($request->req['rec_pass_match_err']);
            $params['rec_pas_suc_message']= wp_kses_post(stripslashes($request->req['rec_pas_suc_message']));
            $params['rec_invalid_reset_err']= wp_kses_post(stripslashes($request->req['rec_invalid_reset_err']));
            $params['rec_tok_sub_label']= sanitize_text_field($request->req['rec_tok_sub_label']);
            $params['rec_invalid_tok_err']= wp_kses_post(stripslashes($request->req['rec_invalid_tok_err']));
            $params['rec_link_expiry']= absint($request->req['rec_link_expiry']);
            $params['rec_link_exp_err']= sanitize_text_field($request->req['rec_link_exp_err']);
            $params['rec_redirect_default']= isset($request->req['rec_redirect_default']) ? absint($request->req['rec_redirect_default']) : 0;

            if(!empty($params['en_pwd_recovery']) && empty($params['recovery_link_text'])){
                RM_PFBC_Form::setError('login-recovery',__('You must define text for the link.','custom-registration-form-builder-with-submission-manager'));
            }
            else
            {
              $service->update_recovery_options($params);
              RM_Utilities::redirect(admin_url('/admin.php?page=rm_login_sett_manage'));  
            }
            
        }
        $data->params= $service->get_recovery_options();
        $view = $this->mv_handler->setView("login_recovery");
        $view->render($data);
    }
    
    public function email_temp($model, $service, $request, $params){
        $data= new stdClass();
        if($this->mv_handler->validateForm("login-email-temp")) {
            $params= array();
            $params['failed_login_err']= $request->req['failed_login_err'];
            $params['otp_message']= $request->req['otp_message'];
            $params['pass_reset']= $request->req['pass_reset'];
            $params['failed_login_err_admin']= $request->req['failed_login_err_admin'];
            $params['ban_message_admin']= $request->req['ban_message_admin'];
            $service->update_template_options($params);
            RM_Utilities::redirect(admin_url('/admin.php?page=rm_login_sett_manage'));
        }
        $data->params= $service->get_template_options();
        $view = $this->mv_handler->setView("login_email_temp");
        $view->render($data);
    }
    
    public function two_factor_auth($model, $service, $request, $params){
        if(defined('REGMAGIC_ADDON')) {
            $addon_controller = new RM_Login_Manage_Controller_Addon();
            return $addon_controller->two_factor_auth($model, $service, $request, $params, $this);
        }
    }
    
    public function integrations($model, $service, $request, $params){
        $data= new stdClass();
        $data->type= $request->req['type'];
        $setting_service= new RM_Setting_Service();
        $setting_service->set_model($model);
        if($this->mv_handler->validateForm("login-integrations")) {
            $options= array();
            if($data->type=='fb'){
                $options['enable_facebook'] = isset($request->req['enable_facebook']) ? "yes" : null;
                $options['facebook_app_id'] = $request->req['facebook_app_id'];
            }
            else if($data->type=='inst'){
                $options['enable_instagram_login'] = isset($request->req['enable_instagram_login']) ? "yes" : null;
                $options['instagram_client_id'] = $request->req['instagram_client_id'];
                $options['instagram_client_secret'] = $request->req['instagram_client_secret'];
            }
            else if($data->type=='win'){
                $options['enable_window_login'] = isset($request->req['enable_window_login']) ? "yes" : null;
                $options['windows_client_id'] = $request->req['windows_client_id'];
            }
            else if($data->type=='google'){
                $options['enable_gplus'] = isset($request->req['enable_gplus']) ? "yes" : null;
                $options['gplus_client_id'] = $request->req['gplus_client_id']; 
            }
            else if($data->type=='tw'){
                $options['enable_twitter_login'] = isset($request->req['enable_twitter_login']) ? "yes" : null;
                $options['tw_consumer_key'] = $request->req['tw_consumer_key'];
                $options['tw_consumer_secret'] = $request->req['tw_consumer_secret'];
            }
            else if($data->type=='linked'){
                $options['enable_linked'] = isset($request->req['enable_linked']) ? "yes" : null;
                $options['linkedin_api_key'] = $request->req['linkedin_api_key'];
            }
            $setting_service->save_options($options);
            RM_Utilities::redirect(admin_url('/admin.php?page=rm_login_sett_manage'));
        }
        $data->options = $setting_service->get_options();
        $view = $this->mv_handler->setView("login_integrations");
        $view->render($data);
    }
    
    public function view($model, $service, $request, $params){
        $data= new stdClass();
        if($this->mv_handler->validateForm("login-view")) {
            $params= array();
            $params['display_user_avatar']= isset($request->req['display_user_avatar']) ? absint($request->req['display_user_avatar']) : 0;
            $params['display_user_name']= isset($request->req['display_user_name']) ? absint($request->req['display_user_name']) : 0;
            $params['display_greetings']= isset($request->req['display_greetings']) ? absint($request->req['display_greetings']) : 0;
            $params['greetings_text']= sanitize_text_field($request->req['greetings_text']);
            $params['display_custom_msg']= isset($request->req['display_custom_msg']) ? absint($request->req['display_custom_msg']) : 0;
            $params['custom_msg']= sanitize_text_field($request->req['custom_msg']);
            $params['separator_bar_color']= $request->req['separator_bar_color'];
            $params['display_account_link']= isset($request->req['display_account_link']) ? absint($request->req['display_account_link']) : 0;
            $params['account_link_text']= sanitize_text_field($request->req['account_link_text']);
            $params['display_logout_link']= isset($request->req['display_logout_link']) ? absint($request->req['display_logout_link']) : 0;
            $params['logout_text']= sanitize_text_field($request->req['logout_text']);
            $service->update_login_view_options($params);
            RM_Utilities::redirect(admin_url('/admin.php?page=rm_login_sett_manage'));
        }
        $data->params= $service->get_login_view_options();
        $view = $this->mv_handler->setView("login_view");
        $view->render($data);
    }
    
    public function analytics($model, $service, $request, $params){
        $data= new stdClass();
        
        //For pagination
        $entries_per_page = 10;
        $req_page = (isset($request->req['rm_reqpage']) && $request->req['rm_reqpage'] > 0) ? $request->req['rm_reqpage'] : 1;
        $offset = ($req_page - 1) * $entries_per_page;
        $total_entries =  RM_DBManager::count_login_log();

        $data->rm_slug = $request->req['page'];
        
        $data->total_pages = (int) ($total_entries / $entries_per_page) + (($total_entries % $entries_per_page) == 0 ? 0 : 1);
        $data->curr_page = $req_page;
        $data->starting_serial_number = $offset + 1;
        //Pagination Ends
        
        $data->login_logs = RM_DBManager::get_login_log_results($request->req,$offset,$entries_per_page);
        $data->all_forms=RM_Utilities::get_forms_dropdown(new RM_Services());
        
        if(isset($request->req['rm_tr'])){
            $data->timerange = $request->req['rm_tr'];            
        } else {
            $data->timerange = '30';
        }
        $this->add_form_timewise_stat($data);
        
        $view = $this->mv_handler->setView("login_analytics");
        $view->render($data);
    }
    
    public function retention($model, $service, $request, $params){
        $data= new stdClass();
        if($this->mv_handler->validateForm("login-retention")) {
            $params= array();
            $params['logs_retention']= sanitize_text_field($request->req['logs_retention']);
            $params['no_of_records']= absint($request->req['no_of_records']);
            $params['no_of_days']= absint($request->req['no_of_days']);
            $data->options= $service->update_log_options($params);
            RM_Utilities::redirect(admin_url('/admin.php?page=rm_login_sett_manage'));
        }
        $data->params= $service->get_log_options();
        $view = $this->mv_handler->setView("login_retention");
        $view->render($data);
    }
    
    public function advanced($model, $service, $request, $params){
        $data= new stdClass();
        
        //For pagination
        $entries_per_page = 10;
        $req_page = (isset($request->req['rm_reqpage']) && $request->req['rm_reqpage'] > 0) ? $request->req['rm_reqpage'] : 1;
        $offset = ($req_page - 1) * $entries_per_page;
        $total_entries =  RM_DBManager::count_login_log_results($request->req);

        $data->rm_slug = $request->req['page'];
        
        $data->total_pages = (int) ($total_entries / $entries_per_page) + (($total_entries % $entries_per_page) == 0 ? 0 : 1);
        $data->curr_page = $req_page;
        $data->starting_serial_number = $offset + 1;
        //Pagination Ends
        
        $data->login_logs = RM_DBManager::get_login_log_results($request->req,$offset,$entries_per_page);
        
        $view = $this->mv_handler->setView("login_advanced");
        $view->render($data);
    }
   
}