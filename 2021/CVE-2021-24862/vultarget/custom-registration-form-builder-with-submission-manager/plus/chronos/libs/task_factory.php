<?php

class RM_Chronos_Task_Factory {
    
    public function create_task($task_id) {
        
        $rule_factory = new RM_Chronos_Rule_Factory();
        $task_model =  new RM_Chronos_Task_Model();        
        
        if($task_model->load_from_db($task_id)) {
            $task = new RM_Chronos_Task($task_model);
            return $task;
        }
        return null;
    }
    
}