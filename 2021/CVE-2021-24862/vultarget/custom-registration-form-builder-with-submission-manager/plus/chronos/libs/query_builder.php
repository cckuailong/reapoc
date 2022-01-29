<?php

class RM_Chronos_Query_Builder {
    protected $user_query_arg;
    protected $sub_query_arg;
    protected $has_build_started;
    protected $form_id;
    const QUERY_ARG_NO_EFFECT = '__no_effect'; // This query argument has no effect, unset it.
    const QUERY_ARG_NULL_SET = '__null_set'; // This query argument guaranttees null data set, return empty data.

    public function __construct($form_id) {
        $this->has_build_started = false;
        $this->user_query_arg = array();
        $this->sub_query_arg = array();
        $this->submission_query_arg = array();
        $this->form_id = $form_id;
    }
    
    public function build_query(RM_Chronos_Rule_Abstract $rule, $join_type = 'AND') {        
            switch($rule->get_type()) {
                case RM_Chronos_Rule_Interface::RULE_TYPE_USER_STATE:
                    $this->build_user_query($rule, $join_type);
                    break;
                
                case RM_Chronos_Rule_Interface::RULE_TYPE_PAYMENT_GATEWAY:
                case RM_Chronos_Rule_Interface::RULE_TYPE_PAYMENT_STATUS:
                case RM_Chronos_Rule_Interface::RULE_TYPE_SUB_TIME:
                case RM_Chronos_Rule_Interface::RULE_TYPE_FIELD_VALUE:   
                    $this->build_sub_query($rule, $join_type);
                    break;
            }
    }
    
    protected function build_sub_query(RM_Chronos_Rule_Abstract $rule, $join_type) {
        //Field value related query args
        if($rule->get_type() == RM_Chronos_Rule_Interface::RULE_TYPE_FIELD_VALUE) {
            if(!isset($this->sub_query_arg['field_search'])) {
                $this->sub_query_arg['field_search'] = array(array('field_id'=>$rule->attr_name,
                    'field_value' => $rule->attr_value));
            } else {
                $this->sub_query_arg['field_search']['relation'] = $join_type;
                $this->sub_query_arg['field_search'][] = array('field_id'=>$rule->attr_name,
                    'field_value' => $rule->attr_value);
            }    
        }
        //submission time related query args
        if($rule->get_type() == RM_Chronos_Rule_Interface::RULE_TYPE_SUB_TIME) {
            if(!isset($this->sub_query_arg['submission_time'])) {
                $this->sub_query_arg['submission_time'] = array(array('age'=>$rule->attr_value, 'operator' => $rule->operator));
            } else {
                $this->sub_query_arg['submission_time']['relation'] = $join_type;
                $this->sub_query_arg['submission_time'][] = array('age'=>$rule->attr_value, 'operator' => $rule->operator);
            }    
        }
        
        //payment related query args - gateway type
        if($rule->get_type() == RM_Chronos_Rule_Interface::RULE_TYPE_PAYMENT_GATEWAY) {
            if(!isset($this->sub_query_arg['payment_gateway'])) {
                $this->sub_query_arg['payment_gateway'] = $rule->attr_value;
            } else {
                if( $join_type == 'AND')
                    $this->sub_query_arg['payment_gateway'] = array_intersect($this->sub_query_arg['payment_gateway'], $rule->attr_value);
                else
                    $this->sub_query_arg['payment_gateway'] = array_unique(array_merge($this->sub_query_arg['payment_gateway'], $rule->attr_value));
            }    
        }
        //payment related query args - payment status
        if($rule->get_type() == RM_Chronos_Rule_Interface::RULE_TYPE_PAYMENT_STATUS) {
            if(!isset($this->sub_query_arg['payment_status'])) {
                $this->sub_query_arg['payment_status'] = $rule->attr_value;
            } else {
                if( $join_type == 'AND')
                    $this->sub_query_arg['payment_status'] = array_intersect($this->sub_query_arg['payment_status'], $rule->attr_value);
                else
                    $this->sub_query_arg['payment_status'] = array_unique(array_merge($this->sub_query_arg['payment_status'], $rule->attr_value));
            }    
        }
    }
    
    protected function build_user_query(RM_Chronos_Rule_Abstract $rule, $join_type) {
        if($rule->get_type() == RM_Chronos_Rule_Interface::RULE_TYPE_USER_STATE) {
            if(!isset($this->user_query_arg['user_state'])) {
                $this->user_query_arg['user_state'] = $rule->attr_value;
            } else if($this->user_query_arg['user_state'] !== self::QUERY_ARG_NO_EFFECT &&
                      $this->user_query_arg['user_state'] !== self::QUERY_ARG_NULL_SET) {
                if($this->user_query_arg['user_state'] !== $rule->attr_value)
                    $this->user_query_arg['user_state'] = ($join_type == 'AND') ? 
                        self::QUERY_ARG_NULL_SET : self::QUERY_ARG_NO_EFFECT;
            }                   
        }
        //Add further processing for different rules such as user_role rules
    }
    
    //This returns WP users and must be called in get_results.
    protected function get_users(array $submitters, $prime_joiner) {
        if(count($submitters) == 0)
            return array();
        
        $args = array('fields' => array('ID', 'user_email'),
                      'include' => $submitters
                      );
        
        if(isset($this->user_query_arg['user_state'])) {
            if($this->user_query_arg['user_state'] == 'active') {
                $args['meta_query'] = array(
                                                array('key' => 'rm_user_status', 'value' =>'v','compare' => 'NOT EXISTS'),
                                                'relation' => 'OR',
                                                array('key' => 'rm_user_status', 'value' => 0)
                                            );
            } else if($this->user_query_arg['user_state'] == 'inactive') {
                $args['meta_query'] = array( array('key' => 'rm_user_status', 'value' => 1));
            } else if($this->user_query_arg['user_state'] == self::QUERY_ARG_NULL_SET) {
                return array();
            } //Nothing to do for NO_EFFECT case here, just do not set meta_query arg.
        }
        $args['role__not_in']= array('Administrator');
        $user_query = new WP_User_Query($args);
        return $user_query->get_results();
    }
    
    //This returns submissions and must be called in get_results.
    protected function get_submissions($prime_joiner) {
        global $wpdb;
        $subs_table = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        $sub_fields_table = RM_Table_Tech::get_table_name_for('SUBMISSION_FIELDS');
        $pay_log_table = RM_Table_Tech::get_table_name_for('PAYPAL_LOGS');
        
        $query = "SELECT st.submission_id, st.user_email FROM $subs_table st WHERE st.form_id = {$this->form_id} AND st.submission_id IN (SELECT MAX(submission_id) FROM `$subs_table` WHERE `form_id` = {$this->form_id} GROUP BY `user_email`)";
                
        //Submissions time rule
        $extended_where = "";
        if(isset($this->sub_query_arg['submission_time'])) {
            $rel = isset($this->sub_query_arg['submission_time']['relation']) ? $this->sub_query_arg['submission_time']['relation'] : null;
            unset($this->sub_query_arg['submission_time']['relation']);// To prevent it from getting in the foreach loop. 
            foreach($this->sub_query_arg['submission_time'] as $sub_time) {
                $age = $sub_time['age'];
                $operator = $sub_time['operator'];
                if($extended_where != "")
                    $extended_where .= $rel? " $rel " : " AND ";
                $extended_where .= "DATE_SUB(CAST(NOW() AS DATETIME), INTERVAL {$age} DAY) {$operator} st.submitted_on";
            }
        }
        if($extended_where != "")
            $query .= " $prime_joiner ($extended_where)";
        
        //Payment gateway rule
        $extended_where = "";
        $payment_sub_query = "";
        if(isset($this->sub_query_arg['payment_gateway']) && is_array($this->sub_query_arg['payment_gateway']) && (count($this->sub_query_arg['payment_gateway']) > 0)) {
            $pgws = implode("','", $this->sub_query_arg['payment_gateway']);
            $extended_where .= "st.submission_id IN (SELECT submission_id FROM $pay_log_table WHERE 'pay_proc' IN ('$pgws'))";
            $payment_sub_query .= "`pay_proc` IN ('$pgws')";
        }
        if(isset($this->sub_query_arg['payment_status']) && is_array($this->sub_query_arg['payment_status']) && (count($this->sub_query_arg['payment_status']) > 0)) {
            $status_array = array();
            foreach($this->sub_query_arg['payment_status'] as $status) {
                if($status == 'completed')
                    $status_array = array_merge($status_array, array('succeeded','completed','Completed'));
                else if($status == 'pending')
                    $status_array = array_merge($status_array, array('pending','Pending'));
                else if($status == 'canceled')
                    $status_array = array_merge($status_array, array('canceled','Canceled'));
            }
                
            $status_array = implode("','", $status_array);
            if($payment_sub_query != "")
                    $payment_sub_query .= $prime_joiner? " $prime_joiner " : " AND ";
            $payment_sub_query .= "`status` IN ('$status_array')";
        }
        if($payment_sub_query != "")
            $extended_where = "st.submission_id IN (SELECT submission_id FROM $pay_log_table WHERE $payment_sub_query)";
        if($extended_where != "")
            $query .= " $prime_joiner $extended_where";
        
        $fsearch_query = $this->build_field_search_query();
        if($fsearch_query != "")
            $query .= " $prime_joiner st.submission_id IN ($fsearch_query)";
                
        $wpdb->query('SET time_zone = "+00:00"');
        $result = $wpdb->get_results($query);
        if(!$result)
            return array();
        else
            return $result;
    }
    
    public function get_results($prime_joiner = 'AND') {
        global $wpdb;
        $rm_subs_table = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        $user_table = $wpdb->users;
        $submitters = array();
        $subs = array();
        //here filter submitters according to subs rule before passing on.
        if(count($this->sub_query_arg) > 0) {
            $subs = $this->get_submissions($prime_joiner);
            //var_dump($subs);
            $subs_emails = wp_list_pluck($subs, 'user_email');
            $subs_emails = implode("','", $subs_emails);
            $submitters = $wpdb->get_results("SELECT `ID` FROM `$user_table` WHERE `user_email` IN ('$subs_emails')", OBJECT_K);
        } else {
            $subs = $this->get_submissions($prime_joiner);
            $submitters = $wpdb->get_results("SELECT `ID` FROM `$user_table` WHERE `user_email` IN (SELECT `user_email` FROM $rm_subs_table WHERE `form_id` = {$this->form_id})", OBJECT_K);
        }
        
        if(!$submitters or count($submitters) == 0) {
            $submitters = array();
        }
        else
            $submitters = array_keys($submitters);
        
        $wp_users = $this->get_users($submitters, $prime_joiner);
        //var_dump($wp_users);
        
        if(count($this->user_query_arg) > 0) {
            $subs = array_uintersect($subs, $wp_users, function($a,$b){return strcasecmp($a->user_email, $b->user_email);});
        }
        
        $result_set = new stdClass;
        $result_set->wp_users = $wp_users;
        $result_set->subs = $subs;
//var_dump($result_set);
        if(count($this->sub_query_arg) > 0 && !empty($this->sub_query_arg['custom_status_attr'])){
            $result_set->cus_status = $this->sub_query_arg['custom_status_attr'];
        }
        return $result_set;             
    }
    
    protected function build_field_search_query($field_joiner = 'OR') {
        global $wpdb;
        //Right now only OR is supported, hence overwrite it.
        $field_joiner = 'OR';
        $sub_fields_table = RM_Table_Tech::get_table_name_for('SUBMISSION_FIELDS');
        
        $query = "SELECT `submission_id` FROM `$sub_fields_table`";

        $ext_query = "";
        
        if(isset($this->sub_query_arg['field_search'])) {
            $rel = isset($this->sub_query_arg['field_search']['relation']) ? $this->sub_query_arg['field_search']['relation'] : null;
            unset($this->sub_query_arg['field_search']['relation']);// To prevent it from getting in the foreach loop. 
            foreach($this->sub_query_arg['field_search'] as $field_search) {
                $field_id = $field_search['field_id'];
                $value_array = $field_search['field_value'];
                if($ext_query != "")
                    $ext_query .= " OR ";
                $ext_query .= ""; 
                $ival_q = "";
                foreach($value_array as $val => $op) {                    
                    if($ival_q != "")
                        $ival_q .= " OR ";
                    //Remove multiple spaces
                    $search_val = preg_replace('!\s+!', ' ', $val);
                    $search_val = $wpdb->esc_like($search_val);
                    $search_val = str_replace(" ", "%",$search_val);
                    $ival_q .= "`value` $op '%$search_val%'";
                }
                if($ival_q != "")
                    $ext_query .= "(`field_id` = $field_id AND ($ival_q))";                
            }
        }
        if($ext_query != "")
            $query .= " WHERE `form_id` = {$this->form_id} AND $ext_query";
        else
            $query = "";
        
        return $query;
    }
}
