<?php

class RM_Chronos_Task_Controller
{
    protected $service;
    
    function __construct()
    {
        $this->service = new RM_Chronos_Service;
    }

    public function add()
    {
       // $this->service->add();
       // $this->view->render();
    }
    
    public function delete_task($task_id)
    {
        $this->service->remove_task($task_id);     
    }

    public function manage_tasks($req)
    {
        if(isset($req['rmc_action'],$req['rmc_task_id'])) {
            switch($req['rmc_action']) {
                case 'delete':
                    $this->delete_task($req['rmc_task_id']);
                    break;
            }
        }
        $data = new stdClass;
        $data->forms = RM_Utilities::get_forms_dropdown($this->service);   
        reset($data->forms);
        $data->form_id = isset($req['rm_form_id']) ? $req['rm_form_id'] : key($data->forms);
        $data->tasks_data = RM_Chronos::get_tasks($data->form_id);
        $data->autostart_tour = !RM_Utilities::has_taken_tour('task_manager_tour');
        $data->page_url_base = admin_url("admin.php?page=rm_ex_chronos_manage_tasks");
        //Include joyride script and style
        wp_enqueue_script('rm_joyride_js', RM_BASE_URL.'admin/js/jquery.joyride-2.1.js');
        wp_enqueue_style('rm_joyride_css', RM_BASE_URL.'admin/css/joyride-2.1.css');
        wp_enqueue_style('rm_chronos_task_man_style', RM_Chronos::get_base_url()."templates/css/task_manager.css", array(), RM_PLUGIN_VERSION);
        wp_register_script('rm_chronos_script', RM_Chronos::get_base_url()."templates/js/chronos.js", array(), RM_PLUGIN_VERSION);
        $chronos_js_vars= array(
                            'empty_error'=>__('Value can not be empty','custom-registration-form-builder-with-submission-manager'),
                            'removing'=>__('Removing...','custom-registration-form-builder-with-submission-manager'),
                            'one_rule_error'=>__('Enable at least one rule','custom-registration-form-builder-with-submission-manager'),
                            'age_error'=>__('Age can not be empty','custom-registration-form-builder-with-submission-manager'),
                            'invalid_age'=>__('Invalid ages specified','custom-registration-form-builder-with-submission-manager'),
                            'empty_error'=>__('Value can not be empty','custom-registration-form-builder-with-submission-manager')
                            
        );
        wp_localize_script('rm_chronos_script','chronos_js_vars',$chronos_js_vars);
        wp_enqueue_script('rm_chronos_script');
        do_action('rm_pre_admin_template_render', "manage_tasks");
        include RM_Chronos::get_base_dir()."templates/task_manager.php";
    }
    
    public function edit_task($req)
    {
        //var_dump($req);
        if(isset($req['rmc-task-edit-form-subbed']) && $req['rmc-task-edit-form-subbed'] == 'yes') {
            $this->service->process_request($req);
            $form_id = RM_Chronos_Toolkit::safe_array_fetch($req, 'rm_form_id');
            RM_Utilities::redirect(admin_url("admin.php?page=rm_ex_chronos_manage_tasks&rm_form_id={$form_id}"));            
        }
        $data = new stdClass;
        $editor_service = new RM_Editor_Actions_Service;
        
        $data->forms = RM_Utilities::get_forms_dropdown($this->service);   
        reset($data->forms);
        $data->form_id = isset($req['rm_form_id']) ? $req['rm_form_id'] : key($data->forms);
        if(isset($req['rmc_task_id'])) {
            $data->task_id = $req['rmc_task_id'];
            $task_factory = new RM_Chronos_Task_Factory();
            $data->task = $task_factory->create_task($data->task_id);
        } else {
            $data->task_id = null;
            $data->task = null;
        }
        $data->init_field_config = $this->service->get_field_initial_config($data->task);
        $data->fields = $this->service->get_all_form_fields($data->form_id);//$editor_service->add_email($data->form_id);        
        //$data->task_data = null;
        $data->page_url_base = admin_url("admin.php?page=rm_ex_chronos_task_manager");
        
        $pay_procs_options = array("paypal" => "<img src='" . RM_IMG_URL . "/paypal-logo.png" . "'>",
                                   "stripe" => "<img src='" . RM_IMG_URL . "/stripe-logo.png" . "'>");
        //pass it through extensions so more pay procs can be added.    
        $data->pay_procs_options = apply_filters('rm_extend_payprocs_options',$pay_procs_options, $data);
        //slight change to handle anet => anet_sim anomaly
        if(isset($data->pay_procs_options['anet'])) {
            $data->pay_procs_options['anet_sim'] = $data->pay_procs_options['anet'];
            unset($data->pay_procs_options['anet']);
        }
        wp_enqueue_style('rm_chronos_edit_task_style', RM_Chronos::get_base_url()."templates/css/edit_task.css", array(), RM_PLUGIN_VERSION);
        wp_enqueue_script('rm_chronos_script', RM_Chronos::get_base_url()."templates/js/chronos.js", array(), RM_PLUGIN_VERSION);
        do_action('rm_pre_admin_template_render', "edit_tasks");
        include RM_Chronos::get_base_dir()."templates/edit_task.php";
    }
    
}
