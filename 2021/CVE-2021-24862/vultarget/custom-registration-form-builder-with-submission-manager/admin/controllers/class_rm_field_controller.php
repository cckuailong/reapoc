<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of calss_rm_field_controller
 *
 * @author CMSHelplive
 */
class RM_Field_Controller {

    public $mv_handler;

    function __construct() {
        $this->mv_handler = new RM_Model_View_Handler();
    }

    public function add($model, $service, $request, $params) {
        if (isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id']))
            $fields_data = $service->get_all_form_fields($request->req['rm_form_id']);
        else
            die(RM_UI_Strings::get('MSG_NO_FORM_SELECTED'));
        
        if (isset($request->req['rm_form_page_no']))
            $form_page_no = $request->req['rm_form_page_no'];
        else
            $form_page_no = 1;

        if ($this->mv_handler->validateForm("add-field")) {
            $request->req['page_no'] = $form_page_no;
            $new_field_order = intval($service->get_fields_highest_order($request->req['rm_form_id'], $form_page_no)) + 1;
            $request->req['field_order'] = $new_field_order;
            
            //Setup icon props
            $f_icon = new stdClass;
            $f_icon->codepoint = $request->req['input_selected_icon_codepoint'];
            $f_icon->fg_color = $request->req['icon_fg_color'];
            $f_icon->bg_color = $request->req['icon_bg_color'];
            $f_icon->shape = $request->req['icon_shape'];
            if(defined('REGMAGIC_ADDON'))
                $f_icon->bg_alpha = $request->req['icon_bg_alpha'];
            $request->req['icon'] = $f_icon;            
            /////////////////////
            if($request->req['field_type'] === "Repeatable_M"){
                 $request->req['field_type']= 'Repeatable';
            }
            //Setup rating field props
            if($request->req['field_type'] === "Rating"){
                $rating_conf = new stdClass;
                $rating_conf->max_stars = $request->req['rating_max_stars'];
                $rating_conf->star_face = $request->req['rating_star_face'];
                $rating_conf->step_size = $request->req['rating_step_size'];
                $rating_conf->star_color = $request->req['rating_star_color'];
                $request->req['rating_conf'] = $rating_conf; 
            }
            // Reset conditions if field type changed
            $temp_field= new RM_Fields();
            if(isset($request->req['field_id'])){
                 $temp_field->load_from_db($request->req['field_id']);
                 if($temp_field->is_field_primary && $temp_field->get_field_type()=='Email'){
                    $request->req['is_deletion_allowed']= 0; 
                 }
                 else
                 {
                     $request->req['is_deletion_allowed']= 1;
                 }
            }
            else{
                $request->req['is_deletion_allowed']= 1;
            }
           
            if($temp_field->get_field_type()==$request->req['field_type']){
                $request->req['conditions']= $temp_field->get_field_conditions();
            }
            
            $model->set($request->req);
            if (isset($request->req['field_id']))
                $service->update($model, $service, $request, $params);
            else
                $service->add($model, $service, $request, $params);
            RM_Utilities::sync_username_hide_option($request->req['rm_form_id']);
            RM_DBManager::update_form_published_pages($request->req["rm_form_id"]);
            RM_Utilities::redirect(admin_url('/admin.php?page=' . $params['xml_loader']->request_tree->success . '&rm_form_id=' . $request->req["rm_form_id"] . '&rm_form_page_no=' . $form_page_no));
            //$this->view->render();
        } else {

            // Edit for request
            if (isset($request->req['rm_field_id'])) {
                $model->load_from_db($request->req['rm_field_id']);
            }

            $data = new stdClass;
            $data->model = $model;
            $data->selected_field = isset($request->req['rm_field_type']) ? $request->req['rm_field_type'] : null;
            if ($data->selected_field=="Repeatable_M") { 
                  $data->model->field_options->field_is_multiline=1;
            }
            
            if(strtolower($data->selected_field)=="mobile"){
                $data->country_fields = $service->get_country_field_dd($request->req['rm_form_id']);
            }
            
            $data->form_id = $request->req['rm_form_id'];
            $data->paypal_fields = RM_Utilities::get_paypal_field_types($service);
            if(defined('REGMAGIC_ADDON'))
                $data->validations_array = RM_Utilities::get_validations_array();
            $user_service= new RM_User_Services();
            $data->metas= $user_service->get_user_meta_dropdown();
            $view = $this->mv_handler->setView("field_add");
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
            
            RM_DBManager::update_form_published_pages($request->req["rm_form_id"]);
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
    
    public function manage($model, $service, $request, $params) {
        $data = new stdClass;
        $request->req['rm_form_id']= absint($request->req['rm_form_id']);
        $data->active_step = isset($request->req['astep']) ? $request->req['astep'] : "build";
        $data->def_form_id = $service->get_setting('default_form_id');
        $fields_data= $service->get_all_form_fields($request->req['rm_form_id']);
        
         if(!empty($request->req['rm_field_type'])){
            
            if($request->req['rm_field_type']=='Username' && !$service->has_user_name($request->req['rm_form_id'])){
                 $service->create_default_username_field($request->req['rm_form_id']);
            } else if($request->req['rm_field_type']=='UserPassword' && !$service->has_user_password($request->req['rm_form_id'])){
                $service->create_default_password_field($request->req['rm_form_id']);
            }
        }
        
        $options= new RM_Options();
        
        if (isset($request->req['rm_action'])) {
            if ($request->req['rm_action'] === 'delete')
                $this->remove_field($model, $service, $request, $params);
            elseif ($request->req['rm_action'] === 'add_page') {
                if(defined('REGMAGIC_ADDON')) {
                    $this->add_page($model, $service, $request, $params);
                    return;
                } else {
                    $data->current_page = $this->add_page($model, $service, $request, $params);
                }
            } elseif ($request->req['rm_action'] === 'delete_page')
                $this->delete_page($model, $service, $request, $params);
            elseif ($request->req['rm_action'] === 'rename_page')
                $this->rename_page($model, $service, $request, $params);
        }
        
        /* Saving conditional fields */
        if(isset($request->req['dfield'])){
          $dField= new RM_Fields();
          $dField->load_from_db($request->req['dfield']);
          $dType= $dField->get_field_type();
          //$allowed_c_fields= RM_Utilities::get_allowed_conditional_fields(); 
          $cField= new RM_Fields();
          $dField->field_options->conditions= array("rules"=> array(),"settings"=>array());
          if(empty($request->req['cfields'])){
                $dField->field_options->conditions= array();
                $dField->field_options->conditions['settings']= array();
                $dField->update_into_db(); 
          } else{
          foreach($request->req['cfields'] as $index=>$cf_id){
                if((int)$cf_id==0 || $cf_id==$dField->field_id)
                    continue;
                $cField->load_from_db($cf_id);
                $cType= $cField->get_field_type();
                @$dField->field_options->conditions['rules']['c_'.$cf_id.'_'.$index]= array("controlling_field"=>$cf_id,"op"=>$request->req['op'][$index],"values"=>explode(',',$request->req['values'][$index]));
                @$dField->field_options->conditions['settings']= array('combinator'=> isset($request->req['combinator'])?$request->req['combinator']:'OR');
                $dField->update_into_db(); 
               }
             }
             $data->show_conditions= true;
         }

        if (isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id']))
            $fields_data = $service->get_all_form_fields($request->req['rm_form_id']);
        else
            die(RM_UI_Strings::get('MSG_NO_FORM_SELECTED'));
       
        $data->theme = $options->get_value_of('theme');
        $data->fields_data = $fields_data;
        $data->forms = RM_Utilities::get_forms_dropdown($service);
        $form = new RM_Forms();
        $form->load_from_db($request->req['rm_form_id']);
        $data->form_id = $request->req['rm_form_id'];
        $data->form= $form;
        $data->field_types = defined('REGMAGIC_ADDON') ? RM_Utilities::get_field_types(true,$form->form_type) : RM_Utilities::get_field_types();
        $data->prev_page= $options->get_value_of('front_sub_page_id');
        $fopts = $form->get_form_options();
        
        if(!defined('REGMAGIC_ADDON')) {
            $g = array_keys($data->field_types);
            if($data->fields_data && is_array($data->fields_data))
                foreach($data->fields_data as $in => $out)
                {
                    if(!in_array($out->field_type, $g))
                            unset($data->fields_data[$in]);
                }
        }
            
        if (!$fopts->form_pages) {
            $data->total_page = 1;
            $data->form_pages = array('Page 1');
            $data->ordered_form_pages = array(0);
        } else {
            $data->total_page = count($fopts->form_pages);
            $data->form_pages = $fopts->form_pages;
            if (!$fopts->ordered_form_pages)
            {
                $data->ordered_form_pages = array_keys($data->form_pages);
            }
            else 
                $data->ordered_form_pages = $fopts->ordered_form_pages;
        }
        
        if (!isset($data->current_page))
            $data->current_page = isset($request->req['rm_form_page_no']) ? $request->req['rm_form_page_no'] : $data->ordered_form_pages[0]+1;
        
        $data->has_dismissed_first_time_instructions = RM_Utilities::has_action_occured('dismiss_field_manager_instructions');
        
        $data->form_name = htmlentities(stripslashes($form->form_name));
        // Submit field - button related config
        $data->form_options = $fopts;
        // End submit field        
        //$view = $this->mv_handler->setView("field_manager");
        $view = $this->mv_handler->setView("formflow_main");
        $view->render($data);
    }

    public function add_page($model, $service, $request, $params) {
        if (isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id'])) {
            if(defined('REGMAGIC_ADDON')) {
                $page_no = $service->manage_form_page('add_page', $request->req['rm_form_id'], null);
                RM_DBManager::update_form_published_pages($request->req["rm_form_id"]);
                RM_Utilities::redirect("?page=rm_field_manage&rm_form_id=".$request->req['rm_form_id']."&rm_form_page_no=$page_no");
            } else
                return $service->manage_form_page('add_page', $request->req['rm_form_id'], null);
        } else
            die(RM_UI_Strings::get('MSG_NO_FORM_SELECTED'));
    }

    public function delete_page($model, $service, $request, $params) {
        if (isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id'])) {
            if (isset($request->req['rm_form_page_no'])) {
                $service->manage_form_page('delete_page', $request->req['rm_form_id'], $request->req['rm_form_page_no']);
                $request->req['rm_form_page_no'] = 1;
                RM_DBManager::update_form_published_pages($request->req["rm_form_id"]);
            }
        } else
            die(RM_UI_Strings::get('MSG_NO_FORM_SELECTED'));
    }

    public function rename_page($model, $service, $request, $params) {
        if (isset($request->req['rm_form_id']) && is_numeric($request->req['rm_form_id'])) {
            if (isset($request->req['rm_form_page_no']) && isset($request->req['rm_form_page_name'])) {
                $service->manage_form_page('rename_page', $request->req['rm_form_id'], $request->req['rm_form_page_no'], $request->req['rm_form_page_name']);
                RM_DBManager::update_form_published_pages($request->req["rm_form_id"]);
            }
        } else
            die(RM_UI_Strings::get('MSG_NO_FORM_SELECTED'));
    }
    
    public function set_page_order($model, $service, $request, $params) {
        $service->set_page_order($request->req['form_id'], $request->req['data']);
        RM_DBManager::update_form_published_pages($request->req["form_id"]);
    }

    public function set_order($model, $service, $request, $params) {
        $service->set_field_order($request->req['data']);
        
        $form_id = RM_DBManager::get_form_id_by_field_id($request->req['data'][array_key_first($request->req['data'])]);
        RM_DBManager::update_form_published_pages($form_id);
    }

    public function remove_field($model, RM_Services $service, $request, $params) {
        if (isset($request->req['rm_field_id'])) {
            $result = $service->remove($request->req['rm_field_id'], null, array());
            RM_DBManager::update_form_published_pages($request->req["rm_form_id"]);
            if(defined('REGMAGIC_ADDON'))
                RM_Utilities::sync_username_hide_option($request->req['rm_form_id']);
        }
        else
            die(RM_UI_Strings::get('MSG_NO_FIELD_SELECTED'));
    }

    public function duplicate($model, $service, $request, $params) {
        $selected = isset($request->req['rm_selected']) ? $request->req['rm_selected'] : null;
        $ids = $service->duplicate($selected);
        RM_DBManager::update_form_published_pages($request->req["rm_form_id"]);
        if(defined('REGMAGIC_ADDON'))
            RM_Utilities::redirect($this->get_redirection_url_for_actions($request));
        else
            $this->manage($model, $service, $request, $params);
    }

    public function remove($model, RM_Services $service, $request, $params) {
        $selected = isset($request->req['rm_selected']) ? $request->req['rm_selected'] : null;
        $service->remove($selected);
        RM_DBManager::update_form_published_pages($request->req["rm_form_id"]);
        if(defined('REGMAGIC_ADDON'))
            RM_Utilities::redirect($this->get_redirection_url_for_actions($request));
        else
            $this->manage($model, $service, $request, $params);
    }
    
    public function get_redirection_url_for_actions($request){
        $url  = '?page=rm_field_manage';
        if(isset($request->req['rm_form_id']))
            $url .= "&rm_form_id=".$request->req['rm_form_id'];
        if(isset($request->req['curr_page_no']))
            $url .= "&rm_form_page_no=".$request->req['curr_page_no'];
        return $url;
    }

}
