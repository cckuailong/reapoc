<?php
/**
 * Centralizing User related database operations
 * Still many functions are in DBManager class. Eventually all the User related db operations will be performed from this class.
 */
class RM_User_Repository {
     
     /**
      * 
      * @param type $options (array with form_id and group by clause)
      * @return array (list of users)
      */                
     public function get_users_for_front($options)
     {        
        global $wpdb;
        $table_name = RM_Table_Tech::get_table_name_for('SUBMISSIONS');
        $wp_user_table = RM_Table_Tech::get_table_name_for('WP_USERS');
        $qry = "";
        $users = array();
        $limit= isset($options['limit']) ? $options['limit'] : 12;
        
        //First filter list wp users
        if (!empty($options['timerange']))
            $time_interval = $options['timerange'];
        else
             $time_interval = 'all';
       
        $form_query="";
        //check if form_id given
        if(isset($options['form_id']) && !empty($options['form_id'])):
            $form_query= " form_id=". (int) $options['form_id'];
        endif;

        // Limit result set
        $limit_query= " limit $limit ";
        if(!empty($options['page_number'])):
            $page_number= $options['page_number']-1;
            $offset = $limit*$page_number;
            $limit_query .= " OFFSET $offset";
        endif;

        // Order by clause
        $order_by = " ORDER BY `submitted_on` desc ";
        
        $qry = "SELECT distinct `user_email` from $table_name";
        if(!empty($form_query)){
            $qry .= ' WHERE ';
        }
        
        if(!empty($time_interval)){
            $time_query='';
            switch($time_interval){
                case 'today' : $time_query= ' DATE(`submitted_on`) = CURDATE() '; break;
                case 'month' : $time_query= ' MONTH(submitted_on) = MONTH(CURRENT_DATE()) AND YEAR(submitted_on) = YEAR(CURRENT_DATE()) '; break;
                case 'year' : $time_query= '  YEAR(submitted_on) = YEAR(CURRENT_DATE())  '; break;
                case 'week' : $time_query= ' YEARWEEK(`submitted_on`, 1) = YEARWEEK(CURDATE(), 1) '; break;
                case 'all'  : $time_query= ''; break;
            }
            
            if(empty($form_query) && !empty($time_query)){
                $time_query = ' WHERE '.$time_query;
            }
            else if(!empty($time_query))
            {
                $time_query= ' AND '.$time_query;
            }
     
        }
        else
        {
            $time_query = ' ';
        }
        
        
        
       $qry = $qry.$form_query.$time_query.$order_by.$limit_query;
        
      
        $emails = $wpdb->get_col($qry);
        if (is_array($emails))
        {
            foreach ($emails as $email)
            {
                $user = get_user_by('email', $email);
                if ($user)
                    $users[] = $user; 
                else
                    $users[]= (object) array('ID'=>'','display_name'=>'','user_email'=>$email);
            }
            
            return $users;
        }
                 
         return null;
     }

}

?>