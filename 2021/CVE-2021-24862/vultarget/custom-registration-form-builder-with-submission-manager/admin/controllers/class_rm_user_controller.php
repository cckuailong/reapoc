<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Controller to handle USER related requests
 *
 * @author CMSHelplive
 */
class RM_USER_Controller
{

    public $mv_handler;

    function __construct()
    {
        $this->mv_handler = new RM_Model_View_Handler();
    }

    /*
     * List all the existing user roles
     */

    public function role_manage($model, RM_User_Services $service, $request, $params)
    {
        $roles = $service->get_roles_by_status();

        // To remove existing errors from the form
        if (!isset($request->req['rm_submitted']))
            $this->mv_handler->clearFormErrors("rm_user_role_add_form");
        $view_data = new stdClass();
        $view_data->roles = $roles;
        if(defined('REGMAGIC_ADDON'))
            $view_data->custom_role_data = $service->get_setting('user_role_custom_data');
        $view = $this->mv_handler->setView('user_roles_manager');
        $view->render($view_data);
    }

    /*
     * Creation of new Role
     */

    public function role_add($model, RM_User_Services $service, $request, $params)
    {
        $retrieved_nonce = $request->req['_wpnonce'];
        if (!wp_verify_nonce($retrieved_nonce, 'rm_user_role_manage' ) ) die( __('Failed security check','custom-registration-form-builder-with-submission-manager') );
        if(current_user_can('manage_options')):
            if ($this->mv_handler->validateForm("rm_user_role_add_form")) {
                if (isset($request->req['rm_role_name']) && $request->req['rm_display_name'] && $request->req['rm_user_capability']) {
                    if(defined('REGMAGIC_ADDON')) {
                        $additional_data = array();
                        $additional_data['is_paid'] = isset($request->req['rm_role_is_paid']) ? true : false;
                        $additional_data['amount'] = isset($request->req['rm_role_amt']) ? $request->req['rm_role_amt'] : null;
                    }
                    if (!$service->create_role($request->req['rm_role_name'], $request->req['rm_display_name'], $request->req['rm_user_capability']))
                    {
                        //Role add was not success
                    }
                }
            } else {
                // Edit for request
                if (isset($request->req['role_id'])) {
                    $model->load_from_db($request->req['role_id']);
                }
            }
            $this->role_manage($model, $service, $request, $params);
        endif;
    }

    /*
     * Deletion of a role. After deletion all the corresponding users automatically assigns to subscriber role.
     */

    public function role_delete($model, RM_User_Services $service, $request, $params)
    {
        $retrieved_nonce = $request->req['_wpnonce'];
	if (!wp_verify_nonce($retrieved_nonce, 'rm_user_role_manage' ) ) die( __('Failed security check','custom-registration-form-builder-with-submission-manager') );
        if (isset($request->req['rm_roles']) && current_user_can('manage_options'))
        {
            $service->delete_roles($request->req['rm_roles']);
        }
        $this->role_manage($model, $service, $request, $params);
    }

    public function manage($model, RM_User_Services $service, $request, $params)
    {
        $filter= new RM_User_Filter($request,$service);
        $view_data = new stdClass();
        $view_data->filter= $filter;
        $view_data->users = $filter->get_records();
        $view_data->rm_slug = $request->req['page'];
        $view = $this->mv_handler->setView('user_manager');
        $view->render($view_data);
    }

    public function delete($model, RM_User_Services $service, $request, $params)
    {
        $retrieved_nonce = $request->req['_wpnonce'];
        if (!wp_verify_nonce($retrieved_nonce, 'rm_user_manage' ) ) die( __('Failed security check','custom-registration-form-builder-with-submission-manager') );
	
        if (isset($request->req['rm_users']) && current_user_can('manage_options')){
            $delete_option = $request->req['rm_delete_option'];
            $reassign_user = ($delete_option=='reassign' && isset($request->req['rm_reassign_user'])) ? absint($request->req['rm_reassign_user']) : null;
            $users = $service->delete($request->req['rm_users'],$reassign_user);
        }
        $this->manage($model,$service,$request,$params);
    }

    public function deactivate($model, RM_User_Services $service, $request, $params)
    {
        if (isset($request->req['rm_users']))
            $users = $service->deactivate($request->req['rm_users']);
        $this->manage($model,$service,$request,$params);
    }

    public function activate($model, RM_User_Services $service, $request, $params)
    {

        if (isset($request->req['rm_users']))
            $users = $service->activate($request->req['rm_users']);
            $service->notify_users($request->req['rm_users'],'user_activated');
        $this->manage($model,$service,$request,$params);
    }

    public function view($model, RM_User_Services $service, $request, $params)
    {

        if (isset($request->req['user_id']))
        {
            $curr_user = wp_get_current_user();
            if (isset($curr_user->ID))
                $curr_user_id = $curr_user->ID;
            else
                $curr_user_id = null;

            $user = $service->get_user_by('id', $request->req['user_id']);

            if (!$user instanceof WP_User)
            {
                $view = $this->mv_handler->setView('show_notice');
                $data = RM_UI_Strings::get('MSG_DO_NOT_HAVE_ACCESS');
                $view->render($data);
                return;
            }

            $view_data = new stdClass();
            $view_data->user = $user;
            $view_data->user_meta = get_user_meta($request->req['user_id']);
            $view_data->custom_fields = $service->get_custom_fields($user->user_email);
            $view_data->curr_user = $curr_user_id;
            $view_data->submissions = array();
            $view_data->payments = array();
            $view_data->sent_emails = array();
            
            $sent_emails = $service->get('SENT_EMAILS',array('to' => $user->user_email), array('%s'), 'results', 0, 10, '*', null, true);
            $view_data->sent_emails = $sent_emails;

            if(defined('REGMAGIC_ADDON')) {
                $tab_titles = array('rmfirsttabcontent' => (object)array('title' => RM_UI_Strings::get('LABEL_CUSTOM_FIELD'), 'icon' => "<i class='rm-user-view-tab-icons fa fa-address-card'></i>") ,
                                'rmsecondtabcontent' => (object)array('title' => RM_UI_Strings::get('LABEL_SUBMISSIONS'), 'icon' => "<i class='rm-user-view-tab-icons fa fa-check-square'></i>"),
                                'rmthirdtabcontent' => (object)array('title' => RM_UI_Strings::get('LABEL_PAYMENTS'), 'icon' => "<i class='rm-user-view-tab-icons fa fa-credit-card-alt'></i>"),
                                'rmfourthtabcontent' => (object)array('title' => RM_UI_Strings::get('LABEL_SENT_EMAILS'), 'icon' => "<i class='rm-user-view-tab-icons fa fa-envelope'></i>"));
            
                $tab_contents = array('rmfirsttabcontent' => null ,
                                      'rmsecondtabcontent' => null,
                                      'rmthirdtabcontent' => null,
                                      'rmfourthtabcontent' => null);

                $tab_titles = apply_filters('rm_filter_user_view_tab_titles', $tab_titles);
                $tab_contents = apply_filters('rm_filter_user_view_tab_contents', $tab_contents, $request->req['user_id']);

                $view_data->tab_titles = $tab_titles;
                $view_data->tab_contents = $tab_contents;
            }
            
            $submissions = $service->get_submissions_by_email($user->user_email, 10);
            if ($submissions) {
                $i = 0;
                foreach ($submissions as $submission) {
                    $form_name = $service->get('FORMS', array('form_id' => $submission->form_id), array('%d'), 'var', 0, 1, 'form_name');

                    $view_data->submissions[$i] = new stdClass();
                    $view_data->submissions[$i]->submission_id = $submission->submission_id;
                    $view_data->submissions[$i]->submitted_on = $submission->submitted_on;
                    $view_data->submissions[$i]->form_id = $submission->form_id;
                    $view_data->submissions[$i++]->form_name = $form_name;

                    $result = $service->get('PAYPAL_LOGS', array('submission_id' => $service->get_oldest_submission_from_group($submission->submission_id)), array('%d'), 'row', 0, 10, '*', null, true);
                    if ($result)
                        $view_data->payments[] = array('form_name' => $form_name, 'submission_id' => $submission->submission_id, 'form_id' => $submission->form_id, 'payment' => $result);
                }
            }
            
            $view_data->login_logs = RM_DBManager::get_login_log_by_email($user->user_email);

            $view = $this->mv_handler->setView('user_view');
            $view->render($view_data);
        } else
                RM_Utilities::redirect('?page=rm_user_manage');
    }

    /*
      public function search($model, RM_User_Services $service, $request, $params)
      {
      $request->user_ids = array();
      if (isset($request->req['rm_to_search']))
      {
      $keyword = $request->req['rm_to_search'];
      $args = array(
      'search' => '*' . $keyword . "*",
      'search_columns' => array('display_name', 'user_email', "user_login")
      );
      $a = new WP_User_Query($args);
      //echo '<pre>';

      $authors = $a->get_results();

      //echo'<pre>';var_dump($authors);die;
      }

      if (isset($request->req['rm_search_by']))
      {
      if (isset($request->req['filter_between']) && is_array($request->req['filter_between']))
      {
      $user_ids = $service->user_search($request->req['filter_between'], 'time');

      $request->user_ids = $user_ids;
      }

      if (isset($request->req['user_status']) && is_array($request->req['user_status']))
      {
      $user_ids = $service->user_search($request->req['user_status'], 'user_status');

      $request->user_ids = $user_ids;
      }

      if (isset($request->req['field_name']) && trim($request->req['rm_search_by']) != "")
      {
      $user_ids = $service->user_search($request->req['rm_search_by'], $request->req['field_name']);

      $request->user_ids = $user_ids;
      }
      }

      $this->manage($model, $service, $request, null);
      }
     */

    public function edit($model, RM_User_Services $service, $request, $params)
    {
        $retrieved_nonce = $request->req['_wpnonce'];
        if (!wp_verify_nonce($retrieved_nonce, 'edit_rm_user' ) ) die( __('Failed security check','custom-registration-form-builder-with-submission-manager') );
	
        if (isset($request->req['user_id']) &&  current_user_can('manage_options'))
        {
            if ($this->mv_handler->validateForm("rm_edit_user"))
            {
                if (isset($request->req['user_password']) && isset($request->req['user_password_conf']))
                {
                    if ($request->req['user_password'] && $request->req['user_password_conf'] && $request->req['user_id'])
                        $service->reset_user_password($request->req['user_password'], $request->req['user_password_conf'], $request->req['user_id']);
                    $service->set_user_role($request->req['user_id'], $request->req['user_role']);
                } else
                {
                    die(RM_UI_Strings::get('MSG_USER_PASS_NOT_SET'));
                }
                $this->view($model, $service, $request, $params);
            } else
            {
                if (!isset($request->req['rm_submitted']))
                {
                    $this->mv_handler->clearFormErrors("rm_edit_user");
                }
                $view_data = new stdClass();
                $view_data->user = $service->get_user_by('id', $request->req['user_id']);
                $view_data->roles = RM_Utilities::user_role_dropdown(false);
                $view = $this->mv_handler->setView('user_edit');
                $view->render($view_data);
            }
        }
    }

    public function widget($model, RM_User_Services $service, $request, $params)
    {
        if ($params['user'] instanceof WP_User)
        {
            $data = new stdClass;

            $submissions = $service->get_submissions_by_email($params['user']->user_email, 10);

            $sub_data = array();

            $count = 0;
            if ($submissions)
            {
                foreach ($submissions as $submission)
                {
                    //echo "<br>ID: ".$submission->form_id." : ".RM_Utilities::localize_time($submission->submitted_on, 'M dS Y, h:ia')." : ";
                    $name = $service->get('FORMS', array('form_id' => $submission->form_id), array('%d'), 'var', 0, 10, 'form_name');
                    $date = RM_Utilities::localize_time($submission->submitted_on, 'M dS Y, h:ia');
                    $payment_status = $service->get('PAYPAL_LOGS', array('submission_id' => $submission->submission_id), array('%d'), 'var', 0, 10, 'status');

                    $sub_data[] = (object) array('submission_id' => $submission->submission_id, 'name' => $name, 'date' => $date, 'payment_status' => $payment_status);

                    $count++;
                }
            }

            $data->submissions = $sub_data;
            $data->total_sub = $count;
            $data->view_action_link = null;
            
            if(current_user_can('manage_options')) {
                $data->view_action_link = "admin.php?page=rm_submission_view&rm_submission_id=%d";            
            } else {
                $sub_page_id = get_option('rm_option_front_sub_page_id', null);
                $sub_page_url = $sub_page_id ? get_permalink($sub_page_id) : null;
                if($sub_page_url) {
                    $data->view_action_link = esc_url(add_query_arg('submission_id', '%d', $sub_page_url));
                }                   
            }
            
            $view = $this->mv_handler->setView('user_edit_widget');
            $view->render($data);
        }
    }
    
     public function exists($model, $service, $request, $params){ 
         $response= array('status'=>0);
        // Check if form is User Registration type
         if(isset($request->req['form_id']) && $request->req['form_id'])
         {
             $form = new RM_Forms;
             $form->load_from_db($request->req['form_id']);
             if($form->get_form_type() != 1 ){
                 echo json_encode($response);
                 die;
             }
         }
             
         if(isset($request->req['username']) && $request->req['username']){
             $username= $request->req['username'];
             $valid_character_error= RM_Utilities::validate_username_characters($request->req['username'],$request->req['form_id']);
             if(!empty($valid_character_error)){
                  $response['status']=1;
                  $response['msg']= $valid_character_error;
             }
             $username_field_arr= RM_DBManager::get_field_by_type($request->req['form_id'],'Username');
             if(!empty($username_field_arr)){
                $user= get_user_by('login', $username);
                $username_field= new RM_Fields();
                $username_field->load_from_db($username_field_arr->field_id);
                if($user instanceof WP_User){
                   $response['status']=1;
                   $response['msg']= $username_field->field_options->user_exists_error;
                }
             }
         }
         
         if(defined('REGMAGIC_ADDON')) {
             if(isset($request->req['email']) && $request->req['email']){ 
                if(!empty($form)){
                    $fopts = $form->get_form_options();
                    $factrl = $fopts->access_control;
                    if(!empty($factrl->domain)){
                        $domains = explode(',', $factrl->domain);
                        $parts = explode('@',$request->req['email']); // Separate string by @ characters (there should be only one)
                        $domain = array_pop($parts); // Remove and return the last part, which should be the domain
                        // Check if the domain is in our list
                        if (!in_array($domain,$domains)){
                           $response['status']=1;
                           $response['msg']= $factrl->fail_msg;
                           echo json_encode($response);
                           die;
                        }
                    }
                }
             }
         } else {
             if(isset($request->req['email']) && $request->req['email']){ 
                 $user= get_user_by('email', $request->req['email']);
                 if($user instanceof WP_User)
                    $response['status']=1;
                    $response['msg']= RM_UI_Strings::get("USEREMAIL_EXISTS");
             }
         }
         echo json_encode($response);
         die;
         
     }
    
}