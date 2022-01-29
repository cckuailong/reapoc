<?php

class RM_Chronos_Toolkit {
    
    public static function get_merged_email($sub_id, $template, $user_id = null) {
       
        $gopt = new RM_Options();
        $rm_email= new RM_Email();
        global $wpdb;
        
        $subs_table = RM_Table_Tech::get_table_name_for("SUBMISSIONS");
        
        if(!$sub_id)
            return false;
        
        $sub_data = $wpdb->get_var($wpdb->prepare("SELECT `data` FROM {$subs_table} WHERE `submission_id` = %d",$sub_id));
        $sub_data = maybe_unserialize($sub_data);
        
        if(!$sub_data)
            return false;
        
        /*
         * Loop through serialized data for submission
         */
        
        $field_values = array();
        foreach ($sub_data as $field_id => $val) {   
            $field = new RM_Fields();
            $field_exists = $field->load_from_db($field_id);
            if(empty($field_exists))
                continue;
            $val->type= $field->field_type; // Copying field type from database.
            $key = "{$val->type}_{$field_id}";
            
            if (is_array($val->value)) {
                $values = '';
                // Check attachment type field
                if (isset($val->value['rm_field_type']) && $val->value['rm_field_type'] == 'File') {
                    unset($val->value['rm_field_type']);
                    /*
                     * Grab all the attachments as links
                     */
                    foreach ($val->value as $attachment_id) {
                        $values .= wp_get_attachment_link($attachment_id) . '    ';
                    }
                    $field_values[$key] = $values;
                }elseif (isset($val->value['rm_field_type']) && $val->value['rm_field_type'] == 'Address'){
                    unset($val->value['rm_field_type']);
                    foreach($val->value as $in =>  $value){
                       if(empty($value))
                           unset($val->value[$in]);
                    }
                    $field_values[$key] =  implode(', ', $val->value);
                } elseif ($val->type == 'Checkbox') {   
                    $field_values[$key] = implode(', ',RM_Utilities::get_lable_for_option($field_id, $val->value));                    
                }else {
                    $field_values[$key] = implode(', ', $val->value);
                }
            } else {
                if ($val->type == 'Radio' || $val->type == 'Select') {   
                    $field_values[$key] = RM_Utilities::get_lable_for_option($field_id, $val->value);
                }
                else
                    $field_values[$key] =  $val->value;
            }
        }
        
        
        foreach($field_values as $place_holder => $value) {
            $template = str_replace("{{{$place_holder}}}", $value, $template);
            $template = str_replace("%{$place_holder}%", $value, $template);
        }
        //Remove reamining placeholders, if any.
        $template = preg_replace("/{{.*?}}/", "", $template);
        return $template;     
    }
    
    public static function get_already_processed_subs($task_id, $action) {
        global $wpdb;        
        $task_exe_log_table = RM_Chronos::get_table_name_for("TASK_EXE_LOG");
        
        $res = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$task_exe_log_table} WHERE `task_id` = %d AND `action` = %s",$task_id,$action));
        
        if(!$res) {
            $res = new stdClass;
            $res->user_ids = array();
            $res->sub_ids = array();
            return $res;
        } else {
            $res->user_ids = json_decode($res->user_ids, true);
            $res->sub_ids = json_decode($res->sub_ids, true);
            return $res;
        }
    }
    
    public static function update_processed_subs($task_id, $action, array $processed_ids, $type = 'SUBS') {
        global $wpdb;        
        $task_exe_log_table = RM_Chronos::get_table_name_for("TASK_EXE_LOG");
        $subs = array();
        $uids = array();
        $meta = array();
        
        if($type == 'SUBS')
            $subs = $processed_ids;
        else
            $uids = $processed_ids;     
                
        $res = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$task_exe_log_table} WHERE `task_id` = %d AND `action` = %s",$task_id,$action));
        
        if(!$res) { //non-existent record, insert new.
            $subs = json_encode($subs);
            $uids = json_encode($uids);
            $meta = json_encode($meta);
            $wpdb->insert( $task_exe_log_table,
                            array('task_id' => $task_id, 
                                    'action' => $action,
                                    'sub_ids' => $subs,
                                    'user_ids' => $uids,
                                    'meta' => $meta), 
                            array('%d','%d','%s','%s','%s') );
        } else {
            $prev_subs = json_decode($res->sub_ids);
            $prev_uids = json_decode($res->user_ids);
            $subs = array_unique(array_merge($prev_subs, $subs));
            $uids = array_unique(array_merge($prev_uids, $uids));
            $subs = json_encode($subs);
            $uids = json_encode($uids);
            $meta = json_encode($meta);
            $wpdb->update( $task_exe_log_table,
                            array('sub_ids' => $subs,
                                    'user_ids' => $uids,
                                    'meta' => $meta),  
                            array('texe_log_id' => $res->texe_log_id), 
                            array('%s','%s','%s'),
                            array('%d') );
            return $res;
        }
    }
    
    public static function safe_array_fetch($array, $key, $def_val = "") {
        return isset($array[$key]) ? $array[$key] : $def_val;
    }
    
}

