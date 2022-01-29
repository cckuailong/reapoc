<?php

/**
 *
 *
 * @author CMSHelplive
 */
class RM_Submission_Service extends RM_Services
{
    public function __construct($model = null) {
        parent::__construct($model);
    }
    
    public function set_model($model=null){
        $this->model= $model;
    }
    
    /*
     * Function to change is_read status for submission
     * 
     */
    public function change_read_status($status=0){
        $this->model->set_is_read($status);
        $this->model->update_into_db();
    }
    
    public function update_read_status($options){
        $fid = $options->form_id;
        $status = $options->read_status;
        $filter = $options->filter;
        
        if($filter == null)
            RM_DBManager::update_read_status_all_submissions($fid, $status);
    }
    
    /*
     * Function to get unreaded submissions count
     * Function to get all notes for a submission
     */
    
    public function get_notes($submission_id){
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_Submission_Service_Addon();
            return $addon_service->get_notes($submission_id, $this);
        }
    }
   
}