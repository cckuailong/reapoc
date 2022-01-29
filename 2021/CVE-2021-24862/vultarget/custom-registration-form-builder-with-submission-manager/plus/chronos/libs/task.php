<?php

class RM_Chronos_Task {
    
    public $task_id;
    public $name;
    public $desc;
    public $form_id;
    public $must_rules;
    public $any_rules;
    public $combiner;
    public $is_active;
    public $actions;
    public $meta;
    
    public function __construct(RM_Chronos_Task_Model $model) {
        
        foreach($model->props as $prop => $val) {
            if(property_exists($this, $prop))
                    $this->$prop = $val;
        }       
        
        $rule_factory = new RM_Chronos_Rule_Factory();
        $must_rules = array();
        foreach($model->must_rules as $rule_id) {
            $must_rules[] = $rule_factory->create_rule($rule_id);
        }
        
        $this->must_rules = $must_rules;
        
        $any_rules = array();
        foreach($model->any_rules as $rule_id) {
            $any_rules[] = $rule_factory->create_rule($rule_id);
        }
        $this->any_rules = $any_rules;
    }
    
    public function execute() {
        $context = array();
        $context['form_id'] = $this->form_id;
        $result = null;
        $users = null;
        $last_rule = null;
        $qb = new RM_Chronos_Query_Builder($this->form_id);
        foreach($this->must_rules as $rule) {
            $qb->build_query($rule, 'AND');
        }        
        $result_set = $qb->get_results();
        
        $data = new stdClass;
        $data->users = $result_set->wp_users;
        $data->subs = $result_set->subs;
        if(!empty($result_set->cus_status)){
            $data->cus_status = $result_set->cus_status;
        }
        $this->perform_actions($data);
    }
    
    protected function perform_actions($data) {
        $user_model= new RM_User;
        foreach($this->actions as $action) {
            switch($action) {
                case RM_Chronos_Action_Interface::ACTION_TYPE_ACTIVATE_USER:
                    if(isset($data->users) && is_array($data->users)) {
                        $prev_proc_users = RM_Chronos_Toolkit::get_already_processed_subs($this->task_id, $action);
                        $prev_proc_users = $prev_proc_users->user_ids;
                        $new_proc_users = array();
                        foreach($data->users as $user) {
                            $user_model->activate_user($user->ID);
                            $new_proc_users[] = $user->ID;
                        }
                        RM_Chronos_Toolkit::update_processed_subs($this->task_id, $action, $new_proc_users);
                    }
                    break;
                
                case RM_Chronos_Action_Interface::ACTION_TYPE_DEACTIVATE_USER:
                    if(isset($data->users) && is_array($data->users)) {
                        $prev_proc_users = RM_Chronos_Toolkit::get_already_processed_subs($this->task_id, $action);
                        $prev_proc_users = $prev_proc_users->user_ids;
                        $new_proc_users = array();
                        foreach($data->users as $user) {
                            $user_model->deactivate_user($user->ID);
                            $new_proc_users[] = $user->ID;
                        }
                        RM_Chronos_Toolkit::update_processed_subs($this->task_id, $action, $new_proc_users);
                    }
                    break;
                    
                case RM_Chronos_Action_Interface::ACTION_TYPE_DELETE_USER:
                    if(isset($data->users) && is_array($data->users)) {
                        foreach($data->users as $user) {
                            wp_delete_user($user->ID);
                        }
                    }
                    break;
                    
                case RM_Chronos_Action_Interface::ACTION_TYPE_SEND_EMAIL:
                    if(isset($data->subs) && is_array($data->subs)) {
                            $this->action_send_emails($data->subs);
                        }
                    break;
                case RM_Chronos_Action_Interface::ACTION_TYPE_APPLY_STATUS:
                    if(isset($data->subs) && is_array($data->subs)){
                        if(!empty($data->cus_status)){
                            $service = new RM_Services();
                            foreach($data->cus_status as $status_index){
                                foreach($data->subs as $submisison){
                                    $service->update_custom_statuses($status_index,$submisison->submission_id,$this->form_id,'append');
                                }
                            }
                        }
                    }
                    break;
                case RM_Chronos_Action_Interface::ACTION_TYPE_REMOVE_STATUS:
                    if(isset($data->subs) && is_array($data->subs)){
                        if(!empty($data->cus_status)){
                            $service = new RM_Services();
                            foreach($data->cus_status as $status_index){
                                foreach($data->subs as $submisison){
                                    $service->update_custom_statuses($status_index,$submisison->submission_id,$this->form_id,'delete');
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }
    
    public function action_send_emails($subs) {
        $template = isset($this->meta['email_template']) ?
                                $this->meta['email_template'] : "";
        $subject = isset($this->meta['email_subject']) ?
                                $this->meta['email_subject'] : "";

        $prev_proc_subs = RM_Chronos_Toolkit::get_already_processed_subs($this->task_id, RM_Chronos_Action_Interface::ACTION_TYPE_SEND_EMAIL);
        $prev_proc_subs = $prev_proc_subs->sub_ids;
        $new_proc_subs = array();
        if(!$subject && !$template)
            return false;

        foreach($subs as $sub) {
            if(in_array($sub->submission_id,$prev_proc_subs))
                    continue;
            $email_content = RM_Chronos_Toolkit::get_merged_email($sub->submission_id,$template);
            $params = array('sub_id' => $sub->submission_id, 'form_id' => $this->form_id);
            RM_Utilities::quick_email($sub->user_email, $subject, wpautop($email_content), RM_CHRONOS_ACTION_EMAIL, $params);
            $new_proc_subs[] = $sub->submission_id;
        }                        
        RM_Chronos_Toolkit::update_processed_subs($this->task_id, RM_Chronos_Action_Interface::ACTION_TYPE_SEND_EMAIL, $new_proc_subs);                    
    }
    
}
