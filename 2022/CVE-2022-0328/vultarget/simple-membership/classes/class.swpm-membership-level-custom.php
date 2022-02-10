<?php
/**
 * Description of BMembershipLevelCustom
 *
 * @author nur
 */
class SwpmMembershipLevelCustom {
    private static $instances = array();
    private $level_id;
    private $fields;
    private function __construct() {
        $this->fields = array();
    }
    public static function get_instance_by_id($level_id){
        if (!isset(self::$instances[$level_id])){
            self::$instances[$level_id] = new SwpmMembershipLevelCustom();
            self::$instances[$level_id]->level_id = $level_id;
            self::$instances[$level_id]->load_by_id($level_id);
        }
        return self::$instances[$level_id];
    }
    public function load_by_id($level_id){
        global $wpdb;
        $query = 'SELECT * FROM ' . $wpdb->prefix . 'swpm_membership_meta_tbl WHERE level_id=%d';
        $results = $wpdb->get_results($wpdb->prepare($query, $level_id), ARRAY_A);
        foreach($results as $result){
            $this->fields[$result['meta_key']] = $result;
        }
    }
    public function set($item){
        $meta_key = preg_replace('|[^A-Z0-9_]|i', '', $item['meta_key']);
        $new = array(
            'meta_key'=>$meta_key,
            'level_id'=>$this->level_id,
            'meta_label'=> isset($item['meta_label'])?$item['meta_label']:'',
            'meta_value'=>$item['meta_value'],
            'meta_type'=> isset($item['meta_type'])?$item['meta_type']:'text',
            'meta_default'=> isset($item['meta_default'])?$item['meta_default']:'',
            'meta_context'=> $item['meta_context'],
            );
        if (isset($this->fields[$meta_key])){
            $new['id'] = $this->fields[$meta_key]['id'];
            $this->fields[$meta_key] = $new;
        }
        else{
            $this->fields[$meta_key] = $new;
        }
        $this->save($this->fields[$meta_key]);
    return $this;
    }
    public function get($meta_key, $default=''){
        $meta_key = preg_replace('|[^A-Z0-9_]|i', '', $meta_key);
        if (isset($this->fields[$meta_key])){
            return maybe_unserialize($this->fields[$meta_key]['meta_value']);

        }
        return $default;
    }
    public function get_by_context($context){
        $result = array();
        foreach ($this->fields as $key=>$field){
            if ($field['meta_context'] == $context){
                $result[$key] = $field;
            }
        }
        return $result;
    }
    private function save($field){
        global $wpdb;
        if (!isset($field['meta_key'])){retern;} // cannot continue without key field.
        $meta_key = preg_replace('|[^A-Z0-9_]|i', '', $field['meta_key']);
        $query = $wpdb->prepare(
                'REPLACE INTO ' . $wpdb->prefix. 'swpm_membership_meta_tbl
                (level_id, meta_key, meta_label, meta_value, meta_type, meta_default, meta_context)
                VALUES(%d, %s, %s, %s, %s, %s, %s); ',
                $this->level_id,
                $meta_key,
                isset($field['meta_label'])? sanitize_text_field($field['meta_label']): '',
                isset($field['meta_value'])? sanitize_text_field($field['meta_value']): '',
                'text', // at the moment we have only one type
                '',
                isset($field['meta_context'])? sanitize_text_field($field['meta_context']): 'default'
                );

        $wpdb->query($query);
    }
    public static function get_value_by_key($level_id, $key, $default= ''){
        return SwpmMembershipLevelCustom::get_instance_by_id($level_id)->get($key, $default);
    }
    public static  function get_value_by_context($level_id, $context){
        return SwpmMembershipLevelCustom::get_instance_by_id($level_id)->get_by_context($context);
    }
}
