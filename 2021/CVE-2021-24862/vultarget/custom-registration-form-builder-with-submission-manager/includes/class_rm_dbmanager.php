<?php

/**
 * Database management class
 *
 * Hold general methods of database operations
 * Singleton class and get_instance is used to access the insatnce of the class
 *
 * @author cmshelplive
 */
class RM_DBManager
{

    public static $instance;

    //Ensures that only one instance is being used.
    //All other functions should use it to access the DBM interface.
    public static function get_instance()
    {
        if (!isset(self::$instance) && !( self::$instance instanceof RM_DBManager ))
        {
            self::$instance = new RM_DBManager;
        }

        return self::$instance;
    }

    /**
     * Ref: http://www.phptherightway.com/pages/Design-Patterns.html
     * Private constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    public function __construct() {
        
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     */
    public function __clone() {
        
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     */
    public function __wakeup() {
        
    }

    /**
     * Inserts a new row into db
     *
     * @global      object    $wpdb
     * @param       string    $model_identifier
     * @param       array     $array_attributes
     * @param       array     $array_attribute_format
     * @return      boolean
     */
    public static function insert_row($model_identifier, $array_attributes, $array_attribute_format) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for($model_identifier);
        
        /* update by vincent andrew */
        $db_format = new RM_DB_FORMAT;
        foreach($array_attributes as $key=>$value)
        {
          $arg[] = $db_format->get_db_table_field_type($model_identifier,$key);
        }
         /* update by vincent andrew */
        
        $result = $wpdb->insert($table_name, $array_attributes, $arg);

        if ($result !== false)
            return $wpdb->insert_id;
        else
            return false;
    }
    
    public static function update_read_status_all_submissions($form_id, $status)
    {
        if(defined('REGMAGIC_ADDON'))
        {
            return RM_DBManager_Addon::update_read_status_all_submissions($form_id, $status);
        }
    }

    public static function update_row($model_identifier, $unique_id_value, $array_attributes, $array_attribute_format) {
        global $wpdb;

        $unique_id_name = RM_Table_Tech::get_unique_id_name($model_identifier);

        if ($unique_id_name === false)
            return false;

        //Safety check
        if ($unique_id_value === NULL)
            return false;

        $table_name = RM_Table_Tech::get_table_name_for($model_identifier);
        
        /* update by vincent andrew */
         if ( is_numeric($unique_id_value) ) 
         {
            $unique_id_value = (int) $unique_id_value;
            $result = $wpdb->get_row($wpdb->prepare("SELECT * from `$table_name` where $unique_id_name = %d", $unique_id_value));
         }
         else
         {
             $result = $wpdb->get_row($wpdb->prepare("SELECT * from `$table_name` where $unique_id_name = %s", $unique_id_value));
         }
         
         /* update by vincent andrew */
        
        if ($result === null)
            return false;
        /* update by vincent andrew */
        $db_format = new RM_DB_FORMAT;
        foreach($array_attributes as $key=>$value)
        {
          $arg[] = $db_format->get_db_table_field_type($model_identifier,$key);
        }
         /* update by vincent andrew */
        
        return $wpdb->update($table_name, $array_attributes, array($unique_id_name => $unique_id_value), $arg, array('%d'));
    }

    public static function remove_fields_for_page($page_no, $form_id) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('FIELDS');
        
        $wpdb->delete($table_name, array('page_no' => $page_no, 'form_id' => $form_id), array('%d', '%d'));
    }

    public static function remove_row($model_identifier, $unique_id_value = false, $where = null) {
        global $wpdb;

        $unique_id_name = RM_Table_Tech::get_unique_id_name($model_identifier);

        if ($unique_id_name === false)
            return false;

        $table_name = RM_Table_Tech::get_table_name_for($model_identifier);

        $result = $wpdb->get_row($wpdb->prepare("SELECT * from `$table_name` where $unique_id_name = %d", $unique_id_value));

        if ($result === null)
            return false;

        if (!$where)
            return $wpdb->delete($table_name, array($unique_id_name => $unique_id_value), array('%d'));

        elseif (is_array($where)) {
            if (false !== $unique_id_value)
                $where[$unique_id_name] = $unique_id_value;
            return $wpdb->delete($table_name, $where, array('%d'));
        } else
            throw new InvalidArgumentException("Invalid Argument 3 supplied to " . __CLASS__ . "::" . __FUNCTION__);
    }

    public static function get_row($model_identifier, $unique_id_value) {
        global $wpdb;

        $unique_id_name = RM_Table_Tech::get_unique_id_name($model_identifier);

        if ($unique_id_name === false)
            return false;

        $table_name = RM_Table_Tech::get_table_name_for($model_identifier);
        $res = $wpdb->get_row($wpdb->prepare("SELECT * from `$table_name` where $unique_id_name = %d", $unique_id_value));

        return $res;
    }

    /**
     * gets all the entries of a table spaecified.
     *
     * @global object $wpdb
     * @param   string    $model_identifier
     * @param   int       $limit    No of results to be returned
     * @param   int       $offset
     * @param   string    $column
     * @param   string    $sort_by
     * @param   boolean   $descending
     * @return  mixed       returns the result of the query or false if fails
     */
    public static function get_all($model_identifier, $offset = 0, $limit = 0, $column = '*', $sort_by = '', $descending = false) {
        return self::get($model_identifier, 1, null, 'results', $offset, $limit, $column, $sort_by, $descending);
    }

    /**
     * This function retrieves the fields corresponding to a form
     *
     * @global object $wpdb
     * @param int $form_id
     * @return mixed
     */
    public static function get_fields_by_form_id($form_id) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('FIELDS');

        $foreign_key = RM_Table_Tech::get_unique_id_name('FORMS');

        $results = $wpdb->get_results($wpdb->prepare("SELECT * from `$table_name` where `$foreign_key`=%d ORDER BY `page_no` ASC, `field_order` ASC", $form_id));

        if ($results === NULL || count($results) === 0) {
            return false;
        }

        return $results;
    }

    /**
     * This functions sets the order of the fields for a form
     * This function is now assigned to a ajax request so then arguments can not
     * be passed to the function.
     * This function should not be used for a direct ajax callback so another
     * function should be created that will use this function to update the order.
     */
    public static function set_field_order($order_list) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('FIELDS');

        $unique_id_name = RM_Table_Tech::get_unique_id_name('FIELDS');

        if ($unique_id_name === false)
            return false;
        if (count($order_list)) {
            foreach ($order_list as $order => $field_id) {
                $array_attributes = array('field_order' => $order);
                $array_attribute_format = array('%d');
                $result = $wpdb->update($table_name, $array_attributes, array($unique_id_name => $field_id), $array_attribute_format, array('%d'));
                if (false === $result)
                    return false;
            }
            return true;
        } else
            return false;
    }
    
    public static function set_form_page_order($form_id, $order_list)
    {
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::set_form_page_order($form_id, $order_list);
        }
    }

    public static function get_submissions_for_form($form_id, $limit = 9999999, $offset = 0, $column = '*', $sort_by = '', $descending = false) {

        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');

        if (empty($sort_by)) {

            $unique_id_name = RM_Table_Tech::get_unique_id_name('SUBMISSIONS');

            if ($unique_id_name === false)
                return false;

            $sort_by = $unique_id_name;
        }

        $foreign_key = RM_Table_Tech::get_unique_id_name('FORMS');

        if ($foreign_key === false)
            return false;

        if ($descending === false) {
            $results = $wpdb->get_results($wpdb->prepare("SELECT $column FROM `$table_name` WHERE `$foreign_key` = %d ORDER BY `$sort_by` LIMIT $limit OFFSET $offset", $form_id));
        } else {
            $results = $wpdb->get_results($wpdb->prepare("SELECT $column FROM `$table_name` WHERE `$foreign_key` = %d ORDER BY `$sort_by` DESC LIMIT $limit OFFSET $offset", $form_id));
        }

        if ($results === NULL || count($results) === 0) {
            return false;
        }

        return $results;
    }

    /**
     * get all the field values for a submission
     *
     * @global  object $wpdb
     * @param   int $submission_id
     * @return  boolean
     */
    public static function get_fields_for_submission($submission_id) {

        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSION_FIELDS');

        $unique_id_name = RM_Table_Tech::get_unique_id_name('SUBMISSION_FIELDS');

        if ($unique_id_name === false)
            return false;

        $foreign_key = RM_Table_Tech::get_unique_id_name('FORMS');

        if ($foreign_key === false)
            return false;

        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE `$foreign_key` = %d", $submission_id));

        if ($results === NULL || count($results) === 0) {
            return false;
        }

        return $results;
    }

    /**
     * This function searches all the submissions for a specific field value
     *
     * @param   $field_id       int         id of the field for which the value is searched
     * @param   $field_value    string      value of the field to be searched
     * @param   $limit          int         number of results to be returned
     * @param   $offset         int         offset
     * @param   $sort_by        string      column name by which the results will be sorted
     * @param   $descending     bool        if set true results will be sorted in descending order
     *
     * @return  Array   array of all the submission ids for the field value
     */
    public static function search_submissions_for($field_id, $field_value, $limit = 9999999, $offset = 0, $sort_by = '', $descending = false) {

        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::search_submissions_for($field_id, $field_value, $limit, $offset, $sort_by, $descending);
        }
        
        global $wpdb;

        $desc = '';
        if ($descending)
            $desc = 'DESC';

        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSION_FIELDS');

        if (empty($sort_by)) {

            $unique_id_name = RM_Table_Tech::get_unique_id_name('SUBMISSION_FIELDS');

            if ($unique_id_name === false)
                return false;

            $sort_by = $unique_id_name;
        }

        $foreign_key = array();

        $foreign_key['submission'] = RM_Table_Tech::get_unique_id_name('SUBMISSIONS');

        $foreign_key['field'] = RM_Table_Tech::get_unique_id_name('FIELDS');

        $field = new RM_Fields;
        $field->load_from_db($field_id);
        if (in_array($field->field_type, array('Select', 'Radio', 'Checkbox'))) {
            $opts = RM_Utilities::process_field_options($field->get_field_value());
            $opt_label = array_search($field_value, $opts);
            if ($opt_label)
                $field_value = $opt_label;
        }


        $results = $wpdb->get_col($wpdb->prepare("SELECT `" . $foreign_key['submission'] . "` FROM $table_name WHERE `" . $foreign_key['field'] . "` = %d AND `value` LIKE %s ORDER BY `$sort_by` $desc LIMIT $limit OFFSET $offset", $field_id, '%' . $wpdb->esc_like(esc_sql($field_value)) . '%'));

        if ($results === NULL || count($results) === 0) {
            return false;
        }

        return $results;
    }
    
    public static function search_submissions_for_custom_status($status_index,$form_id)
    {
        if(defined('REGMAGIC_ADDON'))
            return RM_DBManager_Addon::search_submissions_for_custom_status($status_index,$form_id);
    }

    /**
     * to get all the submissions by a user by his email
     *
     * @global object $wpdb
     * @param    string     $user_email
     * @param    int        $limit
     * @param    int        $offset
     * @param    string     $sort_by
     * @param    boolean    $descending
     * @return   mixed       returns the result of the query or false if not successful
     */
    public static function get_submissions_for_user($user_email, $limit = 0, $offset = 0, $sort_by = '', $descending = false) {

        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');

        if (empty($sort_by)) {

            $unique_id_name = RM_Table_Tech::get_unique_id_name('SUBMISSIONS');

            if ($unique_id_name === false)
                return false;

            $sort_by = $unique_id_name;
        }

        $limit_query = "";
        if ($limit)
            $limit_query = "LIMIT $limit";

        if ($descending === false) {
            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `$table_name` WHERE `user_email` = %s AND `child_id` = 0 ORDER BY `$sort_by` $limit_query OFFSET $offset", esc_sql($user_email)));
        } else {
            $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM `$table_name` WHERE `user_email` = %s AND `child_id` = 0 ORDER BY `$sort_by` DESC $limit_query OFFSET $offset", esc_sql($user_email)));
        }

        if ($results === NULL || count($results) === 0) {
            return false;
        }

        return $results;
    }

    public static function group_by_total($model_identifier, $where, $data_specifiers = '', $group_by = null) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for($model_identifier);

        $unique_id_name = RM_Table_Tech::get_unique_id_name($model_identifier);

        if ($unique_id_name === false)
            return false;
         /* update by vincent andrew */
        $db_format = new RM_DB_FORMAT;
        $data = array();
         /* update by vincent andrew */
        $qry = "SELECT COUNT(*) FROM $table_name WHERE ";
        if (is_array($where)) {
            foreach ($where as $column_name => $column_value) {
                if ($column_value == null)
                {
                    $qry .= "`$column_name` IS NULL AND ";
                }
                elseif ($column_value == 'not null')
                {
                    $qry .= "`$column_name` IS NOT NULL AND ";
                }
                else
                {
                    $arg = $db_format->get_db_table_field_type($model_identifier,$column_name);
                    $qry .= "`$column_name` = $arg AND ";
                    $data[] = $column_value;
                }
            }

            $qry = substr($qry, 0, -4);
        } elseif ($where == 1) {
            $qry .= "1 ";
        } else {
            throw new InvalidArgumentException(
            __FUNCTION__ . " needs the second argument to be an array or 1,'" . gettype($where) . "'is passed.");
        }

        if ($group_by != null) {
            $qry .= "GROUP BY `$group_by`";
        }
        if(empty($data))
        {
            $count = $wpdb->get_results($qry);
        }
        else
        {
            $count = $wpdb->get_results($wpdb->prepare($qry,$data));
        }
        
        return $wpdb->num_rows;
    }

    public static function count($model_identifier, $where, $data_specifiers = '') {

        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for($model_identifier);

        $unique_id_name = RM_Table_Tech::get_unique_id_name($model_identifier);

        if ($unique_id_name === false)
            return false;

        $qry = "SELECT COUNT($unique_id_name) FROM $table_name WHERE ";
       
        $db_format = new RM_DB_FORMAT;
        $data = array();
         /* update by vincent andrew */
        if (is_array($where)) {
            foreach ($where as $column_name => $column_value) {
                if ($column_value === null)
                {
                    $qry .= "`$column_name` IS NULL AND ";
                }
                elseif ($column_value === 'not null')
                {
                    $qry .= "`$column_name` IS NOT NULL AND ";
                }
                else
                {
                    $arg = $db_format->get_db_table_field_type($model_identifier,$column_name);
                    $qry .= "`$column_name` = $arg AND ";
                    $data[] = $column_value;
                }
            }

            $qry = substr($qry, 0, -4);
        } elseif ($where == 1) {
            $qry .= "1 ";
        } else {
            throw new InvalidArgumentException(
            __FUNCTION__ . " needs the second argument to be an array or 1,'" . gettype($where) . "'is passed.");
        }


        if(empty($data))
        {
            $count = $wpdb->get_var($qry);
        }
        else
        {
            $count = $wpdb->get_var($wpdb->prepare($qry,$data));
        }
        if ($count === null) {
            return false;
        }
        return (int) $count;
    }
    
    //Count login fields
    public static function count_login_field()
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN');
        
        $result = $wpdb->get_var($wpdb->prepare("Select value FROM `$table_name` where `m_key` = %s",'fields'));
        $json_result = json_decode($result);
        //echo '<pre>';print_r($json_result);die;
        if (isset($json_result->form_fields))
        {
            return count($json_result->form_fields);
        }
        return 0;
    }
    
    //get login field detals
    public static function get_login_fields_details($m_key)
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN');
        
        switch ($m_key)
        {
            case 'buttons':
                $result = $wpdb->get_var($wpdb->prepare("Select value FROM `$table_name` where `m_key` = %s",'btn_config'));
                $json_result = json_decode($result);
                break;
            
            case 'login_log':
                $result = $wpdb->get_var($wpdb->prepare("Select value FROM `$table_name` where `m_key` = %s",'btn_config'));
                $json_result = json_decode($result);
                break;
            
            default: return false;
        }
        return $json_result;
    }
    
    //get login field detals
    public static function get_login_log_by_email($email)
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        $results = $wpdb->get_results($wpdb->prepare("Select * from `$table_name` WHERE `result`!='dummy' AND email = %s ORDER BY id DESC ",$email));
        return $results;
    }
    
    public static function get_login_log($limit=0)
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        $limit_str = $limit>0?' LIMIT '.$limit:'';
        $results = $wpdb->get_results($wpdb->prepare("Select * from `$table_name` WHERE `result`!='dummy' AND 1 = %d ORDER BY id DESC ".$limit_str,1));
        return $results;
    }
    
    //get login field details with filter
    public static function get_login_log_results($request,$offset=0,$limit=0)
    {
        //echo '<pre>';print_r($request);echo '</pre>';
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        
        $where_arr[] = " 1 = %d AND `result`!='dummy' ";
        $args[] = 1;
        
        if(isset($request['rm_login_type']) && $request['rm_login_type']!=''){
            $where_arr[] = " type = %s";
            $args[] = $request['rm_login_type'];
        }
        
        if(isset($request['rm_login_result']) && $request['rm_login_result']!=''){
            if($request['rm_login_result']!='success' && $request['rm_login_result']!='failure'){
                $where_arr[] = " failure_reason = %s";
                $args[] = $request['rm_login_result'];
            }else{
                $where_arr[] = " result = %s";
                $args[] = $request['rm_login_result'];
            }
        }
        
        if(isset($request['rm_search_value']) && $request['rm_search_value']!=''){
            $where_seacrh_text = " ( email = %s OR ip = %s ";
            $args[] = $request['rm_search_value'];
            $args[] = $request['rm_search_value'];
            
            $user = get_user_by('login',$request['rm_search_value']);
            if(!empty($user)){
                $where_seacrh_text.= " OR email = %s ";
                $args[] = $user->data->user_email;
            }
            
            $search_string = esc_attr( trim( $request['rm_search_value'] ) );
            $users = new WP_User_Query( array(
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'first_name',
                        'value'   => $search_string,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key'     => 'last_name',
                        'value'   => $search_string,
                        'compare' => 'LIKE'
                    )
                )
            ) );
            $users_found = $users->get_results();
            if(!empty($users_found)){
                $userIdCount = count($users_found);
                
                $placeholders = array();
                foreach($users_found as $user_data){
                    $args[] = $user_data->data->user_email;
                    $placeholders[] = '%s';
                }
                $where_seacrh_text.= " OR email IN (".implode(',',$placeholders).") ";
            }
            $where_seacrh_text.= " )";
            
            $where_arr[] = $where_seacrh_text;
        }
        
        $where = ' WHERE '.implode(' AND ',$where_arr);
        
        $limit_str = $limit>0?' LIMIT '.$offset.','.$limit:'';
        $results = $wpdb->get_results($wpdb->prepare("Select * from `$table_name` ".$where." ORDER BY id DESC ".$limit_str,$args));
        //echo '<pre>';print_r($results);die;
        return $results;
    }
    
    //get login field details with filter
    public static function count_login_log_results($request)
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        
        $where_arr[] = " 1 = %d";
        $args[] = 1;
        
        if(isset($request['rm_login_type']) && $request['rm_login_type']!=''){
            $where_arr[] = " type = %s";
            $args[] = $request['rm_login_type'];
        }
        
        if(isset($request['rm_login_result']) && $request['rm_login_result']!=''){
            $where_arr[] = " result = %s";
            $args[] = $request['rm_login_result'];
        }
        
        if(isset($request['rm_search_value']) && $request['rm_search_value']!=''){
            $where_seacrh_text = " ( email = %s OR ip = %s ";
            $args[] = $request['rm_search_value'];
            $args[] = $request['rm_search_value'];
            
            $search_string = esc_attr( trim( $request['rm_search_value'] ) );
            $users = new WP_User_Query( array(
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => 'first_name',
                        'value'   => $search_string,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key'     => 'last_name',
                        'value'   => $search_string,
                        'compare' => 'LIKE'
                    )
                )
            ) );
            $users_found = $users->get_results();
            if(!empty($users_found)){
                $userIdCount = count($users_found);
                
                $placeholders = array();
                foreach($users_found as $user_data){
                    $args[] = $user_data->data->user_email;
                    $placeholders[] = '%s';
                }
                $where_seacrh_text.= " OR email IN (".implode(',',$placeholders).") ";
            }
            $where_seacrh_text.= " )";
            
            $where_arr[] = $where_seacrh_text;
        }
        
        $where = ' WHERE '.implode(' AND ',$where_arr);
        
        $result = $wpdb->get_var($wpdb->prepare("Select count(*) from `$table_name` ".$where,$args));
        return $result;
    }
    
    //count login field detals
    public static function count_login_log()
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        
        $result = $wpdb->get_var("Select count(*) FROM `$table_name` WHERE `result`!='dummy'");
        return $result;
    }
    
    //get login success rate
    public static function get_login_success_rate()
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        $total_count = self::count_login_log();
        
        $success_login = $wpdb->get_var("Select count(*) FROM `$table_name` WHERE status = 1 AND `result`!='dummy'");
        if($total_count>0){
            return number_format(($success_login*100)/$total_count,2);
        }
        return 0;
    }
    
    public static function insert_login_log($args){
        $default_args= array('ip'=>'','time'=>date('Y-m-d H:i:s'),'status'=>0,'type'=>'normal','browser'=>'','failure_reason'=>'','result'=>'','login_url'=>'','social_type'=>'','ban'=>0,'username_used'=>''); // Default param values
        $args= array_merge($default_args,$args);
        //self::insert_row('LOGIN_LOG',array('email'=>$args['email'],'ip'=>$args['ip'],'time'=>$args['time'],'status'=>$args['status'],'type'=>$args['type'],'browser'=>$args['browser'],'failure_reason'=>$args['failure_reason'],'result'=>$args['result'],'login_url'=>$args['login_url']),array('%s','%s','%s','%d','%s','%s','%s','%s','%s'));
        if(empty($args['email'])){
            $args['email'] = $args['username_used'];
        }
        self::insert_row('LOGIN_LOG',array('email'=>$args['email'],'username_used'=>$args['username_used'],'ip'=>$args['ip'],'time'=>date('Y-m-d H:i:s'),'status'=>$args['status'],'type'=>$args['type'],'browser'=>$args['browser'],'failure_reason'=>$args['failure_reason'],'result'=>$args['result'],'login_url'=>$args['login_url'],'social_type'=>$args['social_type'],'ban'=>$args['ban']),array('%s','%s','%s','%s','%d','%s','%s','%s','%s','%s','%s','%d'));
    }
    
    /*
     * This function saved ordering of login fields
     */  
    public static function set_login_field_order($order_list)
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN');
        $unique_id_name = RM_Table_Tech::get_unique_id_name('LOGIN');

        if ($unique_id_name === false)
            return false;
        
        if (count($order_list))
        {
            $array_attributes = array('value' => json_encode($order_list));
            $array_attribute_format = array('%s');
            $result = $wpdb->update($table_name, $array_attributes, array('m_key'=>'fields_order'), $array_attribute_format, array('%s'));
            if (false === $result)
                return false;
            return true;
        } else
            return false;
    }
    
    /*
     * This function update login firlds details
     */  
    public static function update_login_form_options($meta,$data){
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN');
        $wpdb->update($table_name, array('value' => $data), array('m_key' => $meta), array('%s'));
    }
    
    public static function delete_login_log_days($days){
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        
        $time = date('Y-m-d', strtotime('-'.$days.' days'));
        $wpdb->query("DELETE FROM `$table_name` WHERE `time` < '".$time."'");
    }
    
    public static function delete_login_log_records($records){
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        
        if($records==0){
            $wpdb->query("TRUNCATE `$table_name`");
        }else{
            $result = $wpdb->get_row($wpdb->prepare("SELECT id from `$table_name` WHERE `result`!='dummy' ORDER BY id DESC LIMIT %d , 1", $records));
            if ($result === null)
                return false;

            $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` WHERE id <= %d",$result->id));
        }   
    }
    
    public static function query_login_form($meta){
        global $wpdb;
        return self::get('LOGIN', array('m_key'=>$meta), array('%s'));
    }
    
    public static function insert_login_form_options($meta,$value){
        global $wpdb;
        return self::insert_row('LOGIN', array('m_key'=>$meta,'value'=>$value), array('%s','%s'));
    }
    
    public static function delete_expired_otp(){
        global $wpdb;
        $wpdb->query("DELETE FROM $wpdb->prefix".'rm_front_users'." WHERE expiry < '".RM_Utilities::get_current_time()."'");
    }
    
    public static function check_fa_otp_expired($otp,$user_email){
        global $wpdb;
        $otp= sanitize_text_field($otp);
        $user_email= sanitize_text_field($user_email);
        $row= $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->prefix".'rm_front_users'." WHERE expiry < '".RM_Utilities::get_current_time()."' and email= %s and otp_code= %s"),$user_email,$otp); 
        return $row;
    }
    
    public static function count_failed_login_attempt($ip,$duration,$limit){
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        $last_time= RM_Utilities::get_current_time(time());
        $start_time= RM_Utilities::get_current_time(time() - $duration * 60);
        
        $results = $wpdb->get_results($wpdb->prepare("SELECT status,result from $table_name WHERE ip=%s and `time` between %s and %s ORDER BY id DESC limit %d ",$ip,$start_time,$last_time,$limit));
        $failed_count = 0;
        foreach($results as $result){
            if($result->result=='dummy'){
                $failed_count=0;
                break;
            }
            else if($result->status==0){
                $failed_count++;
            }
        }
        
        return $failed_count;
    }
    
    public static function update_login_log($row){
      self::update_row('LOGIN_LOG',$row['id'],$row,array('%d','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s'));
    }
    
    public static function remove_expired_bans(){
        global $wpdb;
        $ip = $_SERVER['REMOTE_ADDR'];
        //$wpdb->query("delete from ".$wpdb->prefix."rm_login_log where ban_til<='".RM_Utilities::get_current_time(time())."'");
        
        $banned_row = $wpdb->get_row("SELECT * from ".$wpdb->prefix."rm_login_log where ip='".$ip."' AND ban_til<='".RM_Utilities::get_current_time(time())."' LIMIT 1");
        if(!empty($banned_row)){
            $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."rm_login_log set ban_til=NULL where `ip` = %s",$ip));
            $unblock_ip = new RM_Submissions();
            $unblock_ip->unblock_ip($ip);
        }
    }
    
    public static function unblock_ip_from_login_logs($ip){
        global $wpdb;
        if(is_array($ip)){
            $ip_val = $ip['ip'];
        }else{
            $ip_val = $ip;
        }
        
        $temp_ip = str_replace('.0', '.', ltrim($ip_val, '0'));
        $table_name= $wpdb->prefix.'rm_login_log';
        $banned_row = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where (ip=%s OR ip=%s) AND ban=1 ORDER BY id DESC LIMIT %d",$ip_val,$temp_ip,1));
        $wpdb->query($wpdb->prepare("UPDATE `$table_name` set ban_til=NULL where `ip` = %s",$ip_val));
        // Inserting a dummy row to pass consecutive failed attempts for unblocked IPs
        if(!empty($banned_row)){
            $args= (array) $banned_row[0];
            unset( $args['id']);
            $args['result']='dummy';
            self::insert_login_log($args); 
        }
        
    }
    
    public static function consecutive_incorrect_otp_attempts($email,$limit){
        global $wpdb;
        $table_name= $wpdb->prefix.'rm_login_log';
        $results = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where email=%s ORDER BY id DESC LIMIT %d",$email,$limit));
        return $results;
    }
      
    public static function reset_login_log()
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');

        $qry = "TRUNCATE `$table_name`";
        $wpdb->query($qry);
    }

    //Run generic queries smartly.
    //Use placeholders #UID# for unique id name and #TNAME# for table name in query string.
    public static function get_generic($identifier, $select_clause, $where_clause, $format = OBJECT) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for($identifier);

        $unique_id_name = RM_Table_Tech::get_unique_id_name($identifier);

        $select_clause = str_replace('#UID#', "`$unique_id_name`", $select_clause);
        $where_clause = str_replace('#UID#', "`$unique_id_name`", $where_clause);
        $select_clause = str_replace('#TNAME#', "`$table_name`", $select_clause);
        $where_clause = str_replace('#TNAME#', "`$table_name`", $where_clause);

        $qry = "SELECT $select_clause FROM `$table_name` WHERE $where_clause";

        $wpdb->query('SET time_zone = "+00:00"');

        $results = $wpdb->get_results($qry, $format);

        if (!$results)
            return null;    //function failed.

        if (is_array($results) && count($results) == 0)
            return null;   //Query failed.

        return $results;
    }

    public static function get($model_identifier, $where, $data_specifier, $result_type = 'results', $offset = 0, $limit = 0, $column = '*', $sort_by = null, $descending = false) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for($model_identifier);

        $unique_id_name = RM_Table_Tech::get_unique_id_name($model_identifier);
        /* update by vincent andrew */
        if(empty($data_specifier) && is_array($where))
        {
            $data_specifier = array();
            $db_format = new RM_DB_FORMAT;
            foreach($where as $key=>$value)
            {
              $data_specifier[] = $db_format->get_db_table_field_type($model_identifier,$key);
            }
        }
         /* update by vincent andrew */
        if ($unique_id_name === false)
            return null;


        if (!$sort_by)
            $sort_by = $unique_id_name;

        $args = array();

        $qry = "SELECT $column FROM `$table_name` WHERE ";

        if (is_array($where)) {
            $i = 0;
            foreach ($where as $column_name => $column_value) {
                if ($i !== 0)
                    $qry .= " AND";
                $qry .= " `$column_name` = $data_specifier[$i] ";
                $args[] = $column_value;
                $i++;
            }
        }
        elseif ($where == 1) {
            $qry .= "1 ";
        } else {
            throw new InvalidArgumentException(
            __FUNCTION__ . " needs the second argument to be an array or 1,'" . gettype($where) . "'is passed.");
        }

        if ($descending === false) {
            if (!$limit)
                $qry .= "ORDER BY `$sort_by`";
            else
                $qry .= "ORDER BY `$sort_by` LIMIT $limit OFFSET $offset";
        } else {
            if (!$limit)
                $qry .= "ORDER BY `$sort_by` DESC";
            else
                $qry .= "ORDER BY `$sort_by` DESC LIMIT $limit OFFSET $offset";
        }

        if ($result_type === 'results' || $result_type === 'row' || $result_type === 'var' || $result_type === 'col') {
            $method_name = 'get_' . $result_type;
            if (count($args) === 0)
                $results = $wpdb->$method_name($qry);
            else
                $results = $wpdb->$method_name($wpdb->prepare($qry, $args));
        } else {
            return null;
        }

        if (is_array($results) && count($results) === 0) {
            return null;
        }

        return $results;
    }

    public static function delete_form_fields($form_id) {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('FIELDS');
        $foreign_key = RM_Table_Tech::get_unique_id_name('FORMS');
        if ($foreign_key === false)
            return false;
        $result = $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` where `$foreign_key` = %d", $form_id));
        if (!$result)
            return false;
        return true;
    }

    public static function delete_form_submissions($form_id) {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        $table_name_ = RM_Table_Tech::get_table_name_for('SUBMISSION_FIELDS');
        $foreign_key = RM_Table_Tech::get_unique_id_name('FORMS');
        if ($foreign_key === false)
            return false;
        $result = $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` where `$foreign_key` = %d", $form_id));
        $result_ = $wpdb->query($wpdb->prepare("DELETE FROM `$table_name_` where `$foreign_key` = %d", $form_id));
        if (!$result || !$result_)
            return false;
        return true;
    }

    public static function delete_form_payment_logs($form_id) {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('PAYPAL_LOGS');
        $foreign_key = RM_Table_Tech::get_unique_id_name('FORMS');
        if ($foreign_key === false)
            return false;
        $result = $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` where `$foreign_key` = %d", $form_id));
        if (!$result)
            return false;
        return true;
    }

    public static function delete_form_stats($form_id) {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('STATS');
        $foreign_key = RM_Table_Tech::get_unique_id_name('FORMS');
        if ($foreign_key === false)
            return false;
        $result = $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` where `$foreign_key` = %d", $form_id));
        if (!$result)
            return false;
        return true;
    }

    public static function delete_form_notes($form_id) {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('NOTES');
        $foreign_key = RM_Table_Tech::get_unique_id_name('FORMS');
        $foreign_key_sub = RM_Table_Tech::get_unique_id_name('SUBMISSIONS');
        $submission_ids = self::get('SUBMISSIONS', array($foreign_key => $form_id), array('%d'), 'col', 0, 999999, 'submission_id', null, true);

        $count = 0;
        if ($submission_ids) {
            $count = count($submission_ids);
            $id_str = implode(',', $submission_ids);
        } else
            return null;

        if ($foreign_key === false)
            return false;
        $data_specifiers = array_fill(0, $count, '%d');
        $specifier_str = implode(', ', $data_specifiers);
        $result = $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` where `$foreign_key_sub` IN ($specifier_str) ", $submission_ids));

        if (!$result)
            return false;
        return true;
    }

    public static function is_expired_by_date($form_id, &$remaining_days = null) {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('FORMS');
        $primary_key = RM_Table_Tech::get_unique_id_name('FORMS');
        $result = maybe_unserialize($wpdb->get_var($wpdb->prepare("Select form_options FROM `$table_name` where `$primary_key` = %d", $form_id)));

        if (isset($result->form_expiry_date)) {
            $form_expiry_date = strtotime($result->form_expiry_date);
            $current_time = intval(time() + (60 * 60 * floatval(get_option( 'gmt_offset', 0 ))));

            if ($current_time > $form_expiry_date) {
                if ($remaining_days !== null) {
                    $remaining_days = 0;
                }
                return true;
            } else {
                if ($remaining_days !== null) {
                    $diff = $form_expiry_date - $current_time;
                    $diff = (int) ($diff / 86400);
                    $remaining_days = $diff;
                }
            }
        } else
            $remaining_days = 'no_expiry_date';

        return false;
    }

    public static function is_expired_by_submissions($form_id, $limit, &$remaining_subs = null) {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        $num_submissions = $wpdb->get_var($wpdb->prepare("Select count(*) FROM `$table_name` where `form_id` = %d AND `child_id` = 0 ", $form_id));
        if ($num_submissions >= $limit) {
            $remaining_subs = 0;
            return true;
        } else
            $remaining_subs = $limit - $num_submissions;

        return false;
    }

    public static function get_primary_fields_by_type($form_id, $type) {
        global $wpdb;
        $email_fields = array();
        $primary_fields = array();

        $table_name = RM_Table_Tech::get_table_name_for('FIELDS');
        // echo "Select * from `$table_name` where form_id=$form_id and field_type='".$type."'"; die;
        $results = $wpdb->get_results($wpdb->prepare("Select * from `$table_name` where form_id=%d and field_type=%s AND `is_field_primary`=1", $form_id, $type));
        if (is_array($results)) {
            foreach ($results as $row) {
                $email_fields[] = $row->field_type . '_' . $row->field_id;
            }
        }
        $primary_fields['emails'] = $email_fields;

        return $primary_fields;
    }

    public static function get_results_for_last($interval, $form_id, $field_id, $field_value, $offset = 0, $limit = 999999, $sort_by = 'submission_id', $descending = false, $dates = null) {

        if (!(int) $form_id)
            return false;

        global $wpdb;

        $wpdb->query('SET time_zone = "+00:00"');

        //echo "<pre>",var_dump($wpdb->get_results('SELECT @@global.time_zone, @@session.time_zone')),die;

        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');

        $interval_string = '';
        $read_status = "";
        $qry = "";
        $data = array();
        $sub_ids = null;

        $searched = false;

        if ((int) $field_id) {
            $sub_ids = self::search_submissions_for($field_id, $field_value, 999999, 0, null, false);
            if ($sub_ids)
                $sub_ids = implode(',', $sub_ids);
            $searched = true;
        }
        $data2 = array();
        switch (strtolower($interval)) {
            case 'today':
                $interval_string = 'BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 day)';
                break;
            case 'week':
                $interval_string = 'BETWEEN DATE_ADD(CURDATE(), INTERVAL 1-DAYOFWEEK(CURDATE()) DAY) AND DATE_ADD(CURDATE(), INTERVAL 7-DAYOFWEEK(CURDATE()) DAY)';
                break;
            case 'month':
                $interval_string = 'BETWEEN DATE_SUB(CURDATE(),INTERVAL (DAY(CURDATE())-1) DAY) AND LAST_DAY(NOW())';
                break;
            case 'year':
                $interval_string = 'BETWEEN DATE_FORMAT(NOW() ,"%Y-01-01") AND LAST_DAY(NOW())';
                break;
            case 'custom':
                if (is_array($dates)) {
                    if ($dates['from'] != '' || $dates['upto'] != '') {
                        if ($dates['from'] == '') {
                            $interval_string = '<= \'' . $dates['upto'] . '\'';
                        } elseif ($dates['upto'] == '') {
                            $interval_string = '>= \'' . $dates['from'] . '\'';
                        } else
                            $interval_string = 'BETWEEN \'' . $dates['from'] . '\' AND \'' . $dates['upto'] . '\'';

                        break;
                    }
                    //Let it fall through to 'all' case.
                }

            case 'read':
            case 'unread':
            case 'all': {
                    if ((int) $field_id && $sub_ids)
                    {
                        $qry = "SELECT * FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 AND `submission_id` in($sub_ids) ";
                        $data[] = $form_id;
                    }elseif ($searched)
                    {
                        $qry = "SELECT * FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 AND  `submission_id` = 0 ";
                        $data[] = $form_id;
                    }else
                    {
                        $qry = "SELECT * FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 ";
                        $data[] = $form_id;
                    }
                    if (!$descending) {
                        $qry .= "ORDER BY `$sort_by` LIMIT $limit OFFSET $offset";
                    } else {
                        $qry .= "ORDER BY `$sort_by` DESC LIMIT $limit OFFSET $offset";
                    }
                    if(empty($data))
                    {
                        $results = $wpdb->get_results($qry);
                    }
                    else
                    {
                        $results = $wpdb->get_results($wpdb->prepare($qry,$data));
                    }
                    
                    if (is_array($results) && count($results) === 0) {
                        return null;
                    }

                    return $results;
                }

            default: return false;
        }

        if ((int) $field_id && $sub_ids)
        {
            $qry = "SELECT * FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 AND  `submission_id` in($sub_ids) AND (`submitted_on` $interval_string) $read_status ";
            $data2[] = $form_id;
        }
        elseif ($searched)
        {
            $qry = "SELECT * FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 AND  `submission_id` = 0 AND (`submitted_on` $interval_string) $read_status ";
            $data2[] = $form_id;
        }
        else
        {
            $qry = "SELECT * FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 AND  (`submitted_on` $interval_string) $read_status ";
            $data2[] = $form_id;
        }

        if (!$descending) {
            $qry .= "ORDER BY `$sort_by` LIMIT $limit OFFSET $offset";
        } else {
            $qry .= "ORDER BY `$sort_by` DESC LIMIT $limit OFFSET $offset";
        }
        
        if(empty($data2))
        {
            $results = $wpdb->get_results($qry);
        }
        else
        {
            $results = $wpdb->get_results($wpdb->prepare($qry,$data2));
        }
        
        if (is_array($results) && count($results) === 0) {
            return null;
        }

        return $results;
    }

    public static function get_results_for_last_col($interval, $form_id, $field_id, $field_value, $offset = 0, $limit = 999999, $sort_by = 'submission_id', $descending = false) {
        global $wpdb;

        $wpdb->query('SET time_zone = "+00:00"');

        //echo "<pre>",var_dump($wpdb->get_results('SELECT @@global.time_zone, @@session.time_zone')),die;

        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        $col_name = RM_Table_Tech::get_unique_id_name('SUBMISSIONS');

        $interval_string = '';

        $qry = "";

        $sub_ids = null;

        $searched = false;
        $data = array();
        $data2 = array();

        if ((int) $field_id) {
            $sub_ids = self::search_submissions_for($field_id, $field_value, 999999, 0, null, false);
            if ($sub_ids)
                $sub_ids = implode(',', $sub_ids);
            $searched = true;
        }

        switch (strtolower($interval)) {
            case 'today':
                $interval_string = 'BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 day)';
                break;
            case 'week':
                $interval_string = 'BETWEEN DATE_ADD(CURDATE(), INTERVAL 1-DAYOFWEEK(CURDATE()) DAY) AND DATE_ADD(CURDATE(), INTERVAL 7-DAYOFWEEK(CURDATE()) DAY)';
                break;
            case 'month':
                $interval_string = 'BETWEEN DATE_SUB(CURDATE(),INTERVAL (DAY(CURDATE())-1) DAY) AND LAST_DAY(NOW())';
                break;
            case 'year':
                $interval_string = 'BETWEEN DATE_FORMAT(NOW() ,"%Y-01-01") AND LAST_DAY(NOW())';
                break;
            case 'all':

                if ((int) $field_id && $sub_ids)
                {
                    $qry = "SELECT `$col_name` FROM `$table_name` WHERE `form_id` = %d AND `submission_id` in($sub_ids) ";
                    $data[] = $form_id;
                }
                elseif ($searched)
                {
                    $qry = "SELECT `$col_name` FROM `$table_name` WHERE `form_id` = %d AND `submission_id` = 0 ";
                    $data[] = $form_id;
                }
                else
                {
                    $qry = "SELECT `$col_name` FROM `$table_name` WHERE `form_id` = %d ";
                    $data[] = $form_id;
                }
                if ($descending === false) {
                    $qry .= "ORDER BY `$sort_by` LIMIT $limit OFFSET $offset";
                } else {
                    $qry .= "ORDER BY `$sort_by` DESC LIMIT $limit OFFSET $offset";
                }
                
                if(empty($data))
                {
                    $results = $wpdb->get_col($qry);
                }
                else
                {
                    $results = $wpdb->get_col($wpdb->prepare($qry,$data));
                }
                if (is_array($results) && count($results) === 0) {
                    return null;
                }

                return $results;

            default: return false;
        }

        if ((int) $field_id && $sub_ids)
        {
            $qry = "SELECT `$col_name` FROM `$table_name` WHERE `form_id` = %d AND `submission_id` in($sub_ids) AND (`submitted_on` $interval_string) ";
            $data2[] = $form_id;
        }
        else
        {
            $qry = "SELECT `$col_name` FROM `$table_name` WHERE `form_id` = %d AND (`submitted_on` $interval_string) ";
            $data2[] = $form_id;
        }
        if ($descending === false) {
            $qry .= "ORDER BY `$sort_by` LIMIT $limit OFFSET $offset";
        } else {
            $qry .= "ORDER BY `$sort_by` DESC LIMIT $limit OFFSET $offset";
        }
        //echo $qry;
        if(empty($data2))
        {
            $results = $wpdb->get_col($qry);
        }
        else
        {
            $results = $wpdb->get_col($wpdb->prepare($qry,$data2));
        }
        

        if (is_array($results) && count($results) === 0) {
            return null;
        }

        return $results;
    }

    public static function sidebar_user_search($criterion, $type) {

        global $wpdb;
        $user_ids = array();


        if ($type == "time") {
            $table_name = $wpdb->prefix . "users";
            foreach ($criterion as $period) {
                $query = "Select ID from $table_name where user_registered between %s and %s";
                $result = $wpdb->get_results($wpdb->prepare($query, $period['start'], $period['end']));
                foreach ($result as $el) {
                    $user_ids[] = $el->ID;
                }
            }
        }

        if ($type == "user_status") {
            $table_name = $wpdb->prefix . "usermeta";
            if (count($criterion) > 1) {
                $query = "Select distinct user_id from $table_name";
                $result = $wpdb->get_results($query);
            } else {
                $query = "Select distinct user_id from $table_name where meta_key='rm_user_status' and meta_value=%d";
                $result = $wpdb->get_results($wpdb->prepare($query, $criterion[0]));
            }

            $result = $wpdb->get_results($query);
            foreach ($result as $el) {
                $user_ids[] = $el->user_id;
            }
        }

        if ($type == "name") {
            $args = array(
                'search' => $criterion,
            );
            $users = get_users($args);
            foreach ($users as $user)
                $user_ids[] = $user->ID;
        }

        if ($type == "email") {
            $args = array(
                'search' => $criterion,
            );
            $users = get_users($args);
            foreach ($users as $user)
                $user_ids[] = $user->ID;
        }


        return array_unique($user_ids);
    }

    public static function delete_front_user($interval, $time_format, $by_last_activity = false) {

        global $wpdb;

        switch ($time_format) {
            case 'H':
            case 'h':
                $mul = 60 * 60;
                break;
            case 'S':
            case 's':
                $mul = 1;
                break;
            default :
                $mul = 1 * 60;
                break;
        }

        $table_name = RM_Table_Tech::get_table_name_for('FRONT_USERS');

        if ($by_last_activity)
            $qry = "DELETE FROM $table_name WHERE `last_activity_time` < '" . RM_Utilities::get_current_time(time() - $interval * $mul) . "'";
        else
            $qry = "DELETE FROM $table_name WHERE `created_date` < '" . RM_Utilities::get_current_time(time() - $interval * $mul) . "'";

        return $wpdb->query($qry);
    }

    public static function update_last_activity() {

        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('FRONT_USERS');

        return $wpdb->query("UPDATE $table_name set `last_activity_time`= '" . RM_Utilities::get_current_time() . "'");
    }

    public static function delete_rows($model_identifier, $where, $where_format = null) {

        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for($model_identifier);
        /* update by vincent andrew */
        $db_format = new RM_DB_FORMAT;
        foreach($where as $key=>$value)
        {
          $arg[] = $db_format->get_db_table_field_type($model_identifier,$key);
        }
         /* update by vincent andrew */
        return $wpdb->delete($table_name, $where, $arg);
    }

    public static function get_average_value($model_identifier, $column_name_that_has_numeric_values, $where = 1) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for($model_identifier);
        $db_format = new RM_DB_FORMAT;
        $qry = "SELECT AVG(`$column_name_that_has_numeric_values`) FROM `$table_name` WHERE";
        $argument_array = array();
        if (is_array($where)) {
            $i = 0;
            foreach ($where as $column_name => $column_value) {
                if ($i !== 0)
                    $qry .= " AND";
                if ($column_value == null)
                {
                    $qry .= "`$column_name` IS NULL";
                }
                else
                {
                    $arg = $db_format->get_db_table_field_type($model_identifier,$column_name);
                    $qry .= " `$column_name` = $arg";
                    $argument_array[] = $column_value;
                }
                $i++;
            }
        }
        elseif ($where == 1) {
            $qry .= " 1";
        } else {
            throw new InvalidArgumentException(
            __FUNCTION__ . " needs the second argument to be an array or 1,'" . gettype($where) . "'is passed.");
        }

        if(empty($argument_array))
        {
            $avg = $wpdb->get_var($qry);
        }
        else
        {
            $avg = $wpdb->get_var($wpdb->prepare($qry,$argument_array));
        }
        
        return floatval($avg);
    }

    public static function get_all_form_attachments($form_id,$selection='value') {

        if(defined('REGMAGIC_ADDON')){
            return RM_DBManager_Addon::get_all_form_attachments($form_id,$selection);
        }
        
        global $wpdb;

        $field_ids = self::get('FIELDS', array('field_type' => 'File', 'form_id' => $form_id), array('%s', '%d'), 'col', 0, 99999, 'field_id', null, false);

        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSION_FIELDS');

        $unique_id_name = RM_Table_Tech::get_unique_id_name('SUBMISSION_FIELDS');
        $argument_array = array();
        if ($unique_id_name === false)
            return null;
        if ($field_ids) {
            $qry = "SELECT `value` FROM $table_name WHERE ";
            $i = 0;
            
            foreach ($field_ids as $field_id) {
                if ($i === 0)
                    $qry .= "`field_id` = %d ";
                else
                    $qry .= "OR `field_id` = %d ";
                $argument_array[] = $field_id;
                $i++;
            }

            $qry .= "ORDER BY `$unique_id_name`";

            $results = $wpdb->get_col($wpdb->prepare($qry,$argument_array));
        } else
            return false;

        if (empty($results)) {
            return false;
        }

        return $results;
    }

    public static function delete_and_reset_table($identifier) {
        RM_Table_Tech::delete_and_reset_table($identifier);
    }

    public static function get_fields_filtered_by_types($form_id, array $types_array) {
        if (!(int) $form_id)
            return false;

        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('FIELDS');
        $count = count($types_array);
        $specifiers = array_fill(0, $count, '%s');
        $spcifier_str = implode(',', $specifiers);
        $qry = "SELECT * FROM `$table_name` WHERE `form_id` = $form_id AND `field_type` IN ($spcifier_str) ";	
        $result = $wpdb->get_results($wpdb->prepare($qry,$types_array));
        return $result;
    }

    /* Counts multiple distinct values in a given column,
     * optionally a where clause can be specified.
     */

    public static function count_multiple($identifier, $column, $where = 1, $in_spacifier = null) {
        /* SELECT `value`, COUNT(*) FROM `wp_rm_submission_fields` WHERE `field_id` = 94 GROUP BY `value` */
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for($identifier);

        $qry = "SELECT `$column`, COUNT(*) AS `count` FROM `$table_name` WHERE ";
        $data = array();
        /* update by vincent andrew */
        $db_format = new RM_DB_FORMAT;
      
         /* update by vincent andrew */
        if (is_array($where)) {
            foreach ($where as $column_name => $column_value) {
                if ($column_value == null)
                {
                    $qry .= "`$column_name` IS NULL AND ";
                }
                else if ($column_value == 'not null')
                {
                    $qry .= "`$column_name` IS NOT NULL AND ";
                }
                else if ($column_value[0] == '!') {
                    $act_val = substr($column_value, 1);
                    $arg = $db_format->get_db_table_field_type($identifier,$column_name);
                    $qry .= "`$column_name` != $arg AND ";
                    $data[] = $act_val;
                    
                } 
                else
                {
                    $arg = $db_format->get_db_table_field_type($identifier,$column_name);
                    $qry .= "`$column_name` = $arg AND ";
                    $data[] = $column_value;
                }
            }

            $qry = substr($qry, 0, -4);
        }
        elseif ($where == 1) {
            $qry .= "1 ";
        } else {
            throw new InvalidArgumentException(
            __FUNCTION__ . " needs the second argument to be an array or 1,'" . gettype($where) . "'is passed.");
        }

        if ($in_spacifier != null) {
            foreach ($in_spacifier as $in_field => $in_string) {
                $in_arr = explode(',', $in_string);
                $in_arr2 = array();

                foreach ($in_arr as $v)
                    $in_arr2[] = '"' . $v . '"';

                $in_string = implode(',', $in_arr2);

                $qry .= "AND `$in_field` IN ($in_string)";
            }
        }

        $qry .= "GROUP BY `$column`";
        //echo("<br>Query: ".$qry);
        if(empty($data))
        {
            $result = $wpdb->get_results($qry);
        }
        else 
        {
            $result = $wpdb->get_results($wpdb->prepare($qry,$data));
        }
        
        return $result;
    }

    /**
     * This function generates a "IN" query for a given array
     * 
     * @global object $wpdb
     * @param string    $model_identifier 
     * @param string    $column_to_search   name of the column to search for the values in the array
     * @param array     $types_array        array of values.
     * @return array
     */
    public static function get_results_for_array($model_identifier, $column_to_search, array $types_array) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for($model_identifier);
        $count = count($types_array);
        $specifiers = array_fill(0, $count, '%s');
        $spcifier_str = implode(',', $specifiers);
        $qry = "SELECT * FROM `$table_name` WHERE `$column_to_search` IN ($spcifier_str) ";

        $result = $wpdb->get_results($wpdb->prepare($qry, $types_array));
        return $result;
    }

    public static function get_sub_fields_for_array($model_identifier, $column_to_search, array $types_array, $column_to_search2, array $types_array2) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for($model_identifier);

        //$count = count($types_array);
        //$specifiers = array_fill(0, $count, '%s');
        //$spcifier_str = implode(',', $specifiers);
        $types_array_str = implode(',', $types_array);
        $qry = "SELECT * FROM `$table_name` WHERE `$column_to_search` IN ($types_array_str) ";

        $count = count($types_array2);
        $count = count(array_merge($types_array, $types_array2));
        $specifiers = array_fill(0, $count, '%s');
        $spcifier_str = implode(',', $specifiers);
        $qry .= " AND `$column_to_search2` IN ($spcifier_str)";

        $result = $wpdb->get_results($wpdb->prepare($qry, array_merge($types_array, $types_array2)));
        return $result;
    }

    public static function get_submissions($filter, $form_id = 0, $selection = "*", $sort_by = 'submission_id', $descending = true, $result_type='results', $paginated = true) {
        if(defined('REGMAGIC_ADDON')){
            return RM_DBManager_Addon::get_submissions($filter,$form_id,$selection,$sort_by,$descending,$result_type,$paginated);
        }
        if (!(int) $form_id)
            return false;
        global $wpdb;
        $wpdb->query('SET time_zone = "+00:00"');
        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        $qry = "";
        $interval_string = "";
        $sub_ids = null;
        $searched = false;
        $filters = $filter->filters;
        $data = array();
        $data2 = array();


        if (isset($filters['rm_field_to_search']) && (int) $filters['rm_field_to_search']) {
            $sub_ids = self::search_submissions_for($filters['rm_field_to_search'], $filters['rm_value_to_search'], 999999, 0, null, false);
            if ($sub_ids)
                $sub_ids = implode(',', $sub_ids);
            $searched = true;
        }

        switch (strtolower($filters['rm_interval'])) {
            case 'today':
                $interval_string = 'BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 day)';
                break;
            case 'week':
                //$interval_string = 'BETWEEN DATE_ADD(CURDATE(), INTERVAL 1-DAYOFWEEK(CURDATE()) DAY) AND DATE_ADD(CURDATE(), INTERVAL 7-DAYOFWEEK(CURDATE()) DAY)';
                // $interval_string = ' > DATE_SUB(NOW(), INTERVAL 1 WEEK) ';
                $interval_string = ' >DATE_ADD(CURDATE(), INTERVAL 1-DAYOFWEEK(CURDATE()) DAY) ';
                break;
            case 'month':
                $interval_string = 'BETWEEN DATE_SUB(CURDATE(),INTERVAL (DAY(CURDATE())-1) DAY) AND LAST_DAY(NOW())';
                break;
            case 'year':
                $interval_string = 'BETWEEN DATE_FORMAT(NOW() ,"%Y-01-01") AND LAST_DAY(NOW())';
                break;
            case 'custom':

                if ($filters['rm_fromdate'] != '' || $filters['rm_dateupto'] != '') {
                    if ($filters['rm_fromdate'] == '') {
                        $interval_string = '<= \'' . $filters['rm_dateupto'] . '\'';
                    } elseif ($filters['rm_dateupto'] == '') {
                        $interval_string = '>= \'' . $filters['rm_dateupto'] . '\'';
                    } else
                        $interval_string = 'BETWEEN \'' . $filters['rm_fromdate'] . ' 00:00:00\' AND \'' . $filters['rm_dateupto'] . ' 23:59:59\'';

                    break;
                }
            //Let it fall through to 'all' case.
            case 'all':
                if ((int) $filters['rm_field_to_search'] && $sub_ids)
                {
                    $qry = "SELECT $selection FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 AND `submission_id` in($sub_ids) ";
                    $data[] = $form_id;
                }
                elseif ($searched)
                {
                    $qry = "SELECT $selection FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 AND `submission_id` = 0 ";
                    $data[] = $form_id;
                }
                else
                {
                    $qry = "SELECT $selection FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 ";
                    $data[] = $form_id;
                }
                if ($selection == "*"):
                    if (!$descending)
                        $qry .= "ORDER BY `$sort_by` LIMIT " . $filter->pagination->entries_per_page . " OFFSET " . $filter->pagination->offset;
                    else
                        $qry .= "ORDER BY `$sort_by` DESC LIMIT " . $filter->pagination->entries_per_page . " OFFSET " . $filter->pagination->offset;
                endif;

                if(empty($data))
                {
                    $results = $wpdb->get_results($qry);
                }
                else
                {
                    $results = $wpdb->get_results($wpdb->prepare($qry,$data));
                }
                
                if (is_array($results) && count($results) === 0) {
                    return null;
                }

                return $results;

            default: return false;
        }

        if ((int) $filters['rm_field_to_search'] && $sub_ids)
        {
            $qry = "SELECT $selection FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 AND `submission_id` in($sub_ids) AND (`submitted_on` $interval_string) ";
            $data2[] = $form_id;
        }
        elseif ($searched)
        {
            $qry = "SELECT $selection FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 AND `submission_id` = 0 AND (`submitted_on` $interval_string) ";
            $data2[] = $form_id;
        }
        else
        {
            $qry = "SELECT $selection FROM `$table_name` WHERE `form_id` = %d AND `child_id` = 0 AND (`submitted_on` $interval_string) ";
            $data2[] = $form_id;
        }
        $qry = self::add_filter_queries($form_id, $filters, $qry);

        if ($selection == "*"):
            if (!$descending)
                $qry .= "ORDER BY `$sort_by` LIMIT " . $filter->pagination->entries_per_page . " OFFSET " . $filter->pagination->offset;
            else
                $qry .= "ORDER BY `$sort_by` DESC LIMIT " . $filter->pagination->entries_per_page . " OFFSET " . $filter->pagination->offset;
        endif;


        if(empty($data2))
        {
            $results = $wpdb->get_results($qry);
        }
        else
        {
            $results = $wpdb->get_results($wpdb->prepare($qry,$data2));
        }
        

        if (is_array($results) && count($results) === 0) {
            return null;
        }

        return $results;
    }

    public static function get_latest_submission_for_user($user_email, $form_ids = array()) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');

        $unique_id_name = RM_Table_Tech::get_unique_id_name('SUBMISSIONS');

        if ($unique_id_name === false)
            return false;

        if (count($form_ids) !== 0) {
            $count = count($form_ids);
            $data_specifiers = array_fill(0, $count, '%d');
            $specifier_str = implode(', ', $data_specifiers);
            $query = "SELECT * FROM $table_name WHERE $unique_id_name IN ("
                    . "SELECT MAX($unique_id_name) FROM `$table_name` where `user_email` =%s "
                    . " AND `form_id` IN ($specifier_str)  GROUP BY `form_id`)"
                    . "ORDER BY $unique_id_name DESC";
            if(!is_array($form_ids)){
               $form_ids= explode(',',$form_ids);
            }
            $results = $wpdb->get_results($wpdb->prepare($query, array_merge(array($user_email), $form_ids)));
        } else {
            $query = "SELECT * FROM $table_name WHERE $unique_id_name IN ("
                    . "SELECT MAX($unique_id_name) FROM `$table_name` where `user_email` =%s GROUP BY `form_id`)"
                    . "ORDER BY $unique_id_name DESC";
            $results = $wpdb->get_results($wpdb->prepare($query, $user_email));
        }

        if ($results === NULL || count($results) === 0) {
            return false;
        }
        
        return $results;
    }
    
    public static function get_recent_submissions_for_user($user_email,$form_ids = array()){
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_recent_submissions_for_user($user_email,$form_ids);
        }
    }
    
    public static function get_edd_user_details($user_email){
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_edd_user_details($user_email);
        }
    }
    
    public static function get_recent_edd_orders_for_user($payment_ids){
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_recent_edd_orders_for_user($payment_ids);
        }
    }
    
    public static function get_custom_statuses($submission_id,$form_id){
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_custom_statuses($submission_id,$form_id);
        }
    }
    
    public static function update_custom_statuses($status_index,$submission_id,$form_id,$action,$clear_index){
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::update_custom_statuses($status_index,$submission_id,$form_id,$action,$clear_index);
        }
    }

    public static function add_filter_queries($form_id, $filters, $qry) {
        if(defined('REGMAGIC_ADDON')){
            return RM_DBManager_Addon::add_filter_queries($form_id,$filters,$qry);
        }
        $records = array();
        $excluded_records = array();

        if (!empty($filters['filter_tags'])) {
            $filter_tags = explode(',', $filters['filter_tags']);
            if (is_array($filter_tags)) {
                foreach ($filter_tags as $filter_tag):
                    switch (strtolower($filter_tag)) {
                        case 'attachment':
                            $submission_ids = self::get_all_form_attachments($form_id, ' distinct submission_id ');
                            if ($submission_ids):
                                $records = array_merge($records, $submission_ids);
                            endif;

                            break;

                        case 'no attachment':
                            $submission_ids = self::get_all_form_attachments($form_id, ' distinct submission_id ');
                            if ($submission_ids):
                                $excluded_records = array_merge($excluded_records, $submission_ids);
                            endif;

                            break;

                        case 'have note':
                            $submission_ids = self::get_submissions_with_note($form_id);
                            if (count($submission_ids)):
                                $records = array_merge($records, $submission_ids);
                            endif;

                            break;

                        case 'payment pending':
                            $submission_ids = self::get_submissions_payment_status($form_id, "'pending'");
                            if (count($submission_ids)):
                                $records = array_merge($records, $submission_ids);
                            endif;

                            break;

                        case 'payment received':
                            $submission_ids = self::get_submissions_payment_status($form_id, "'succeeded','completed'");
                            if (count($submission_ids)):
                                $records = array_merge($records, $submission_ids);
                            endif;

                            break;

                        case 'read':
                            $submission_ids = self::get_submission_read_count($form_id, 1, false);
                            if (count($submission_ids)):
                                $records = array_merge($records, $submission_ids);
                            endif;

                            break;

                        case 'unread':
                            $submission_ids = self::get_submission_read_count($form_id, 0, false);
                            if (count($submission_ids)):
                                $records = array_merge($records, $submission_ids);
                            endif;

                            break;

                        case 'blocked':
                            $submission_ids = self::get_blocked_submission($form_id);
                            if (count($submission_ids)):
                                $records = array_merge($records, $submission_ids);
                            endif;

                            break;
                    }
                endforeach;

                if (empty($records) && empty($excluded_records)):
                    $qry .= " and submission_id in (-1) ";
                elseif (count($excluded_records)):
                    $submission_ids = implode(',', $excluded_records);
                    $qry .= " and submission_id not in ($submission_ids)";
                endif;

                if (count($records)) {
                    $records = array_unique($records);
                    $submission_ids = implode(',', $records);

                    if (count($excluded_records))
                        $qry .= " OR submission_id in ($submission_ids)";
                    else
                        $qry .= " AND submission_id in ($submission_ids)";
                }
            }
        }

        return $qry;
    }
    
    public static function get_submissions_with_note($form_id){
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_submissions_with_note($form_id);
        }
    }

    //get the newest submission's id from a group of edited submissions
    public static function get_latest_submission_from_group($submission_id) {
        $unique_id_name = RM_Table_Tech::get_unique_id_name('SUBMISSIONS');

        if ($unique_id_name === false)
            return false;

        $last_child = self::get('SUBMISSIONS', array($unique_id_name => $submission_id), array('%d'), 'var', 0, 1, 'last_child');

        if ($last_child === null)
            return false;
        elseif ((int) $last_child === 0)
            return $submission_id;

        return $last_child;
    }

    public static function get_oldest_submission_from_group($submission_id) {
        $last_child = self::get_latest_submission_from_group($submission_id);

        switch ($last_child) {
            case false:
                return false;

            default:
                global $wpdb;
                $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
                $unique_id_name = RM_Table_Tech::get_unique_id_name('SUBMISSIONS');
                $first_parent = $wpdb->get_var($wpdb->prepare("SELECT MIN($unique_id_name) FROM $table_name WHERE `last_child` = %d", $last_child));
                if ((int) $first_parent)
                    return $first_parent;
                else
                    return false;
        }
    }

    public static function update_submission_group_last_child($old_val, $new_val) {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        return $wpdb->query($wpdb->prepare("UPDATE $table_name SET `last_child` = %d WHERE `last_child` = %d", $new_val, $old_val));
    }

    public static function get_visitors_count($form_id) {

        if (!$form_id)
            return null;

        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('STATS');
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(DISTINCT user_ip) FROM $table_name WHERE `form_id` = %d AND `visited_on` >= UNIX_TIMESTAMP(CURDATE() - INTERVAL 29 DAY) AND `visited_on` <  UNIX_TIMESTAMP(CURDATE() + INTERVAL 1 DAY)", $form_id));
    }

    public static function get_sent_emails($form_id, $filter, $selection = "*", $sort_by = 'mail_id', $descending = true) {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('SENT_EMAILS');
        $uid = RM_Table_Tech::get_unique_id_name('SENT_EMAILS');
        $qry = "";
        $interval_string = null;
        $field_search_string = null;
        $searched = false;
        $filters = $filter->filters;

        $where_array = array();
        $format_clause = "";
        $where_clause = "";
        $argument_array = array();
        $wpdb->query('SET time_zone = "+00:00"');

        if ($form_id != null)
        {
            $where_array[] = "`form_id` = %d";
            $argument_array[] =  $form_id;
        }
        if (isset($filters['rm_field_to_search'], $filters['rm_value_to_search']) && trim($filters['rm_value_to_search']) != '') {
            $searched = true;
            //Sanitize against incorrect column name
            if (!in_array($filters['rm_field_to_search'], array('to', 'sub', 'body')))
                $filters['rm_field_to_search'] = 'body';

            $field_name = $filters['rm_field_to_search'];

            //Prepare search value.
            //Replace spaces with % wildcard so that html tags do not hinder search.
            //For example: search term "Hi, user" will not match actual content "Hi.<br><br>user" while it should.
            //So we prepare search term as "Hi.%user" which will match the content.

            $search_term = trim($filters['rm_value_to_search']);
            $search_term = htmlspecialchars($search_term);
            $search_term = $wpdb->esc_like($search_term);
            $search_term = preg_replace("/[\s]+/", '%', $search_term);
            $field_value = $search_term;

            $field_search_string = "`$field_name` LIKE %s";
            $wild = '%';
            $like = $wild . $field_value . $wild;
            $where_array[] = $field_search_string;
            $argument_array[] =  $like;
        }

        switch (strtolower($filters['rm_interval'])) {
            case 'today':
                $interval_string = 'BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 day)';
                break;
            case 'week':
                $interval_string = ' >DATE_ADD(CURDATE(), INTERVAL 1-DAYOFWEEK(CURDATE()) DAY) ';
                break;
            case 'month':
                $interval_string = 'BETWEEN DATE_SUB(CURDATE(),INTERVAL (DAY(CURDATE())-1) DAY) AND LAST_DAY(NOW())';
                break;
            case 'year':
                $interval_string = 'BETWEEN DATE_FORMAT(NOW() ,"%Y-01-01") AND LAST_DAY(NOW())';
                break;
            case 'custom':
                if ($filters['rm_fromdate'] != '' || $filters['rm_dateupto'] != '') {
                    if ($filters['rm_fromdate'] == '') {
                        $interval_string = '<= \'' . $filters['rm_dateupto'] . '\'';
                    } elseif ($filters['rm_dateupto'] == '') {
                        $interval_string = '>= \'' . $filters['rm_dateupto'] . '\'';
                    } else
                        $interval_string = 'BETWEEN \'' . $filters['rm_fromdate'] . ' 00:00:00\' AND \'' . $filters['rm_dateupto'] . ' 23:59:59\'';

                    break;
                }
            //Let it fall through to 'all' case.
            case 'all':
            default:
                $interval_string = null;
        }

        if ($interval_string !== null)
        {
            $where_array[] = "(`sent_on` $interval_string)";
        }

        if (count($where_array) > 0)
        {
            $where_clause = "WHERE " . implode(" AND ", $where_array);
        }
        else
        {
            $where_clause = "WHERE %d";
            $argument_array[] =  1;
        }

        if ($selection != "count(*) as count") {
            if (!$descending)
                $format_clause = "ORDER BY `$sort_by` LIMIT " . $filter->pagination->entries_per_page . " OFFSET " . $filter->pagination->offset;
            else
                $format_clause = "ORDER BY `$sort_by` DESC LIMIT " . $filter->pagination->entries_per_page . " OFFSET " . $filter->pagination->offset;
        }

        $qry = "SELECT $selection FROM `$table_name` $where_clause $format_clause";

        $results = $wpdb->get_results($wpdb->prepare($qry,$argument_array));

        if (is_array($results) && count($results) === 0) {
            return null;
        }

        return $results;
    }

    public static function run_query($query, $result_type = 'results') {
        global $wpdb;

        if ($result_type === 'results' || $result_type === 'row' || $result_type === 'var' || $result_type === 'col') {
            $method_name = 'get_' . $result_type;

            $results = $wpdb->$method_name($query);

            return $results;
        }
    }
    
    public static function get_all_notes_for_submission($sub_id) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_all_notes_for_submission($sub_id);
        }
    }
    
    public static function remove_custom_status_from_submissions($form_id,$status_index) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::remove_custom_status_from_submissions($form_id,$status_index);
        }
    }
    
    public static function is_allowed_by_custom_status($email,$form_id) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::is_allowed_by_custom_status($email,$form_id);
        }
    }

    public static function get_submissions_payment_status($form_id, $status) {
        global $wpdb;
        $submission_ids = array();
        $table_payment_logs = RM_Table_Tech::get_table_name_for('PAYPAL_LOGS');
        $qry = "SELECT submission_id from $table_payment_logs where form_id=%d and status in ($status) ";
        $submission_ids = $wpdb->get_col($wpdb->prepare($qry, $form_id));

        return $submission_ids;
    }
    
    public static function get_submissions_by_payment_type($form_id,$type){
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_submissions_by_payment_type($form_id,$type);
        }
    }
    
    public static function get_submission_read_count($form_id,$type,$count= true) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_submission_read_count($form_id,$type,$count);
        }
    }
    
    public static function get_blocked_submission($form_id) {
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_blocked_submission($form_id);
        }
    }

    public static function get_primary_fields_id($form_id, $type) {
        global $wpdb;
        $email_fields = array();
        $primary_fields = array();

        $table_name = RM_Table_Tech::get_table_name_for('FIELDS');
        // echo "Select * from `$table_name` where form_id=$form_id and field_type='".$type."'"; die;
        $results = $wpdb->get_results($wpdb->prepare("Select * from `$table_name` where form_id=%d and field_type=%s AND `is_field_primary`=1", $form_id, esc_sql($type)));
        if (is_array($results)) {
            foreach ($results as $row) {
                $email_fields[] = $row->field_id;
            }
        }


        return $email_fields;
    }
    
    public static function get_field_by_type($form_id,$type){
          global $wpdb;
        
          $table_name = RM_Table_Tech::get_table_name_for('FIELDS');
          $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table_name` WHERE `field_type` = %s and `form_id` = %d  ORDER BY `field_id` ASC",esc_sql($type),$form_id));
          return $result;
    }
      
    public static function get_last_submission() {
        global $wpdb;
        $table_name_submission = RM_Table_Tech::get_table_name_for('SUBMISSIONS');

        $result = $wpdb->get_row("SELECT * FROM $table_name_submission ORDER BY submitted_on DESC limit 1");

        return $result;
    }
    
    //Functions used for GDPR
    public static function get_ip_from_stats($submission_id){
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('STATS');
        
        $result = $wpdb->get_row($wpdb->prepare("Select `user_ip` from `$table_name` where submission_id=%d",$submission_id));
        if ($result === null)
            return false;
        
        return $result->user_ip;
    }
    
    public static function get_ip_from_login($email){
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        $ips_val = array();
        $results = $wpdb->get_results($wpdb->prepare("Select `ip` from `$table_name` where `email`= %s GROUP BY `ip`",esc_sql($email)));
        if (!empty($results))
        {
            return $results;
        }
        return false;
    }
    
    public static function get_payment_details($submission_id){
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('PAYPAL_LOGS');
        
        $result = $wpdb->get_row($wpdb->prepare("Select * from `$table_name` where submission_id=%d",$submission_id));
        if ($result === null)
            return false;
        
        return $result;
    }
    
    public static function delete_login_log_by_email($email){
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        
        $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` WHERE `email` = %s",esc_sql($email)));
    }
    
    public static function delete_notes_by_id($submission_id)
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('NOTES');
        
        $result = $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` where `submission_id` = %d",$submission_id));
    }
    
    public static function delete_submissions_by_id($submission_id)
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        $table_name2 = RM_Table_Tech::get_table_name_for('SUBMISSION_FIELDS');
        
        $result = $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` where `submission_id` = %d",$submission_id));
        $result2 = $wpdb->query($wpdb->prepare("DELETE FROM `$table_name2` where `submission_id` = %d",$submission_id));
    }
    
    public static function delete_ip_from_stats($submission_id){
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('STATS');
        
        $result = $wpdb->get_row($wpdb->prepare("Select `user_ip` from `$table_name` where submission_id=%d",$submission_id));
        if ($result === null)
            return false;
        
        $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` WHERE `submission_id` = %d",$submission_id));
        
        $unblock_ip = new RM_Submissions();
        $unblock_ip->unblock_ip($result->user_ip);
    }
    
    public static function delete_payment_by_submissions_by_id($submission_id)
    {
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('PAYPAL_LOGS');
        
        $result = $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` where `submission_id` = %d",$submission_id));
    }
    
    public static function delete_sent_emails($email){
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('SENT_EMAILS');
        
        $wpdb->query($wpdb->prepare("DELETE FROM `$table_name` WHERE `to` = %s",esc_sql($email)));
    }
    
    public static function delete_all_tables(){
        if(defined('REGMAGIC_ADDON')) {
            RM_DBManager_Addon::delete_all_tables();
            return;
        }
        echo 'dddd';die;
        
        global $wpdb;
        
        $table_name = RM_Table_Tech::get_table_name_for('FIELDS');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('FORMS');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('FRONT_USERS');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('NOTES');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('PAYPAL_FIELDS');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('PAYPAL_LOGS');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('SENT_EMAILS');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('SESSIONS');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('STATS');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSION_FIELDS');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = $wpdb->prefix.'rm_rules';
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = $wpdb->prefix.'rm_tasks';
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = $wpdb->prefix.'rm_task_exe_log';
        $wpdb->query("DROP table `$table_name`");
    }
    
    public static function reset_all_tables()
    {
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::reset_all_tables();
        }
        
        global $wpdb;
        $count= -16;    //2 for default forms and 12 for fields
        
        $table_name = RM_Table_Tech::get_table_name_for('FIELDS');
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('FORMS');
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $forms = self::get_all('FORMS', 0, 999999, 'form_id');
        if(!empty($forms)){
            foreach($forms as $f){
                do_action('rm_form_deleted',$f->form_id);
            }
        }
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('FRONT_USERS');
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('LOGIN_LOG');
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('NOTES');
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('PAYPAL_FIELDS');
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('PAYPAL_LOGS');
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('SENT_EMAILS');
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('SESSIONS');
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('STATS');
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSION_FIELDS');
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = $wpdb->prefix.'rm_rules';
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = $wpdb->prefix.'rm_tasks';
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = $wpdb->prefix.'rm_task_exe_log';
        $count = $count + $wpdb->get_var("Select count(*) FROM `$table_name`");
        $wpdb->query("DROP table `$table_name`");
        
        $table_name = $wpdb->prefix.'options';
        $wpdb->query("DELETE FROM `$table_name` WHERE `option_name` LIKE 'rm_option_%'");
        
        RM_Activator::activate(true);
        
        return $count;
    }
    
    public static function get_mailpoet_forms()
    {
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_mailpoet_forms();
        }
    }
    
    public static function get_mailpoet_fields($form_id)
    {
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_mailpoet_fields($form_id);
        }
    }
    
    public static function get_mailpoet_lists($form_id)
    {
        if(defined('REGMAGIC_ADDON')) {
            return RM_DBManager_Addon::get_mailpoet_lists($form_id);
        }
    }
    
    public static function get_all_user_meta(){
        global $wpdb;
        $table_name = $wpdb->prefix.'usermeta';
        $results = $wpdb->get_results("SELECT DISTINCT meta_key FROM $table_name WHERE 1");
        return $results;
    }
    
    public static function add_form_published_pages($form_id, $page_id) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('FORMS');
        
        $current_pages = maybe_unserialize($wpdb->get_var($wpdb->prepare("SELECT published_pages FROM $table_name WHERE form_id = %d", $form_id)));
        
        if(is_array($current_pages)) {
            array_push($current_pages, $page_id);
        } else {
            $current_pages = array($page_id);
        }
        
        $current_pages = array_unique($current_pages);
        
        return $wpdb->update(
            $table_name,
            array('published_pages' => maybe_serialize($current_pages)),
            array('form_id' => $form_id),
            '%s'
        );
    }
    
    public static function update_form_published_pages($form_id) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('FORMS');
        
        $published_pages = maybe_unserialize($wpdb->get_var($wpdb->prepare("SELECT published_pages FROM $table_name WHERE form_id = %d", $form_id)));
        
        if(is_array($published_pages) && !empty($published_pages)) {
            foreach($published_pages as $published_page) {
                wp_update_post(array('ID' => $published_page));
            }
        }
    }
    
    public static function get_form_id_by_field_id($field_id) {
        global $wpdb;

        $table_name = RM_Table_Tech::get_table_name_for('FIELDS');
        
        return $wpdb->get_var($wpdb->prepare("SELECT form_id FROM $table_name WHERE field_id = %d", $field_id));
    }

}