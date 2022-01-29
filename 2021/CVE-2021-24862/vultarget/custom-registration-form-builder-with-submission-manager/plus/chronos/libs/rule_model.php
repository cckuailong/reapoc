<?php

class RM_Chronos_Rule_Model {
    
    protected $def_props = array('rule_id'=>null,
                          'type'=>null,
                          'attr_name'=>null,
                          'attr_value'=>null,
                          'operator' => null,
                          'meta'=>array());
    protected $data_specifiers = array('%d', '%d', '%s', '%s', '%s', '%s');
    public $props;
    
    public function __construct() {
        $this->props = $this->def_props;
    }
    
    public function __set($attr, $val) {
        if(array_key_exists($attr, $this->def_props)) {
            if(is_array($val))
                $val = json_encode($val);
            $this->props[$attr] = $val;
        }
    }
    
    public function __get($attr) {
        return isset($this->props[$attr]) ? $this->props[$attr] :null;
    }
    
    public function create(array $data) {
        //Fill in missing values with defaults
        if(is_array($data['attr_value']))
            $data['attr_value'] = json_encode ($data['attr_value']);
        $data = array_merge($this->props, $data);
        //Remove any unwanted keys if present
        $this->props = array_intersect_key($data, $this->def_props);
    }   
    
    public function load_from_db($rule_id) {
        global $wpdb;
        
        $table_name = RM_Chronos::get_table_name_for('RULES');
        $db_row = $wpdb->get_row($wpdb->prepare("SELECT * from `$table_name` where `rule_id` = %d",$rule_id));  
        if(!$db_row)
            return false;
        $db_row->meta = maybe_unserialize($db_row->meta);
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
        $table_name = RM_Chronos::get_table_name_for('RULES');
        $result = $wpdb->insert($table_name, $data, $data_specifiers);
        if ($result != false) {
            $this->props['rule_id'] = $wpdb->insert_id;
            return $this->props['rule_id'];
        }
        else
            return false;
    }
    
    public function update_into_db() {
        global $wpdb;
        //Remove "unique id" entries as they are not needed for row update.
        $data = array_slice($this->props,1);
        $data_specifiers = array_slice($this->data_specifiers, 1);
        $data['meta'] = maybe_serialize($data['meta']);
        $table_name = RM_Chronos::get_table_name_for('RULES');
        return $wpdb->update($table_name, $data, array('rule_id' => $this->props['rule_id']), $data_specifiers, array('%d'));        
    }
    
    public function remove_from_db() {
        global $wpdb;
        $table_name = RM_Chronos::get_table_name_for('RULES');
        return $wpdb->delete($table_name, array('rule_id' => $this->props['rule_id']), array('%d'));
    }
}