<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RM_Chronos_Task_Model {
    protected $def_props = array('task_id'=>null,
                          'form_id'=>null,
                          'name'=>null,
                          'desc'=>null,
                          'must_rules'=>array(),
                          'any_rules' => array(),
                          'is_active' => 1,
                          'actions' => array(),
                          'task_order' => 1,
                          'meta' => array()
                          );
    
    protected $data_specifiers = array('%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s');
    public $props;
    
    public function __construct() {
        $this->props = $this->def_props;
    }
    
    public function __set($attr,$val) {
        if(array_key_exists($attr, $this->def_props))
                $this->props[$attr] = $val;
    }
    
    public function __get($attr) {
        return isset($this->props[$attr]) ? $this->props[$attr] : null;
    }
    
    public function create(array $data) {
        //Fill in missing values with defaults        
        $data = array_merge($this->props, $data);
        //Remove any unwanted keys if present
        $this->props = array_intersect_key($data, $this->def_props);
    }
    
    public function load_from_db($task_id) {
        global $wpdb;
        
        $table_name = RM_Chronos::get_table_name_for('TASKS');
        $db_row = $wpdb->get_row($wpdb->prepare("SELECT * from `$table_name` where `task_id` = %d",$task_id));               
        if(!$db_row)
            return false;
        
        $db_row->meta = maybe_unserialize($db_row->meta);
        
        $db_row->must_rules = maybe_unserialize($db_row->must_rules);
        if(!$db_row->must_rules || !is_array($db_row->must_rules))
            $db_row->must_rules = array();
        
        $db_row->any_rules = maybe_unserialize($db_row->any_rules);
        if(!$db_row->any_rules || !is_array($db_row->any_rules))
            $db_row->any_rules = array();
        
        $db_row->actions = maybe_unserialize($db_row->actions);
        if(!$db_row->actions || !is_array($db_row->actions))
            $db_row->actions = array();
        
        $db_row = (array)$db_row;
        $this->create($db_row);
        return true;
    }
    
    public function insert_into_db() {
        global $wpdb;
        //Remove "unique id" entries as they are not needed for row creation.
        $data = array_slice($this->props,1);
        $data_specifiers = array_slice($this->data_specifiers, 1);
        $data['meta'] = maybe_serialize($data['meta']); 
        
        if(!$data['must_rules'] || !is_array($data['must_rules']))
            $data['must_rules'] = array();
        $data['must_rules'] = maybe_serialize($data['must_rules']);        
        
        if(!$data['any_rules'] || !is_array($data['any_rules']))
            $data['any_rules'] = array();
        $data['any_rules'] = maybe_serialize($data['any_rules']);
        
        if(!$data['actions'] || !is_array($data['actions']))
            $data['actions'] = array();
        $data['actions'] = maybe_serialize($data['actions']);               
        
        $table_name = RM_Chronos::get_table_name_for('TASKS');
        $max_order = $wpdb->get_var($wpdb->prepare("SELECT MAX(`task_order`) FROM {$table_name} WHERE `form_id` = %d",$data['form_id']));
        if($max_order!== NULL)
            $data['task_order'] = $max_order + 1;
        
        $result = $wpdb->insert($table_name, $data, $data_specifiers);
        if ($result != false) {
            $this->props['task_id'] = $wpdb->insert_id;
            return $this->props['task_id'];
        }
        else
            return false;
    }
    
    public function update_into_db() {
        global $wpdb;
        
        //Remove "unique id" entries as they are not needed for row update.
        $data = array_slice($this->props,1);
        $data_specifiers = array_slice($this->data_specifiers, 1);
        if(!$data['meta'] || !is_array($data['meta']))
            $data['meta'] = array();
        $data['meta'] = maybe_serialize($data['meta']);
        
        if(!$data['must_rules'] || !is_array($data['must_rules']))
            $data['must_rules'] = array();
        $data['must_rules'] = maybe_serialize($data['must_rules']);
        
        if(!$data['any_rules'] || !is_array($data['any_rules']))
            $data['any_rules'] = array();
        $data['any_rules'] = maybe_serialize($data['any_rules']);
        
        if(!$data['actions'] || !is_array($data['actions']))
            $data['actions'] = array();
        $data['actions'] = maybe_serialize($data['actions']);
        $table_name = RM_Chronos::get_table_name_for('TASKS');
        return $wpdb->update($table_name, $data, array('task_id' => $this->props['task_id']), $data_specifiers, array('%d'));        
    }
    
    public function remove_from_db() {
        global $wpdb;
        $table_name = RM_Chronos::get_table_name_for('TASKS');
        $wpdb->delete($table_name, array('task_id' => $this->props['task_id']), array('%d'));
        
        //Remove rules too
        if(count($this->props['must_rules']) > 0) {
            //First check if these rules are not being used in any other task (by means of duplication)
            $rule_search_pattern = maybe_serialize($this->props['must_rules']);
            $test_rules = $wpdb->get_results("SELECT `must_rules` FROM {$table_name} WHERE `must_rules` = '{$rule_search_pattern}'");
            if(!$test_rules || count($test_rules) == 0) {
                $rules_table = RM_Chronos::get_table_name_for('RULES');
                $rule_ids = implode(',', $this->props['must_rules']);
                $wpdb->query("DELETE FROM {$rules_table} WHERE `rule_id` IN ({$rule_ids})");
            }
        }
    }
    
}