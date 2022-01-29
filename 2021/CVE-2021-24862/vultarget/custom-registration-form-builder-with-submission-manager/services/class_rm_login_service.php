<?php

/* 
 * Login service to handle extended login functionality
 */

class RM_Login_Service{
    
    /*
     * Provides login stats for overall login records
     */
    public function get_login_stats(){
        $success_count= RM_DBManager::count('LOGIN_LOG',array('status'=>1));
        $total_count= RM_DBManager::count('LOGIN_LOG',1);
        if($total_count>0){
            $success_rate= absint(floor(($success_count*100)/$total_count));
        }
        else{
            $success_rate=0;
        }
        return array('total_count'=>$total_count,'success_count'=>$success_count,'success_rate'=>$success_rate);
    }
    
    /*
     * 
     */
    public function set_field_order($list){
        RM_DBManager::set_login_field_order($list);
    }
    
    /*
     * Save logn form details
     */
    public function update_form_fields($data){
        $data= json_encode($data);
        RM_DBManager::update_login_form_options('fields',$data);
    }
    
    /*
     * Get login form with fields
     */
    public function get_form(){
        $field_row= RM_DBManager::query_login_form('fields');
        if(isset($field_row[0]))
        {
            return $field_row[0]->value;
        }
        return array();
    }
    
    /*
     * Returns username and password field configuration
     */
    public function get_un_password_fields(){
        $form= $this->get_form();
        $form= json_decode($form,true);
        $fields= $form['form_fields'];
        
        $f= array();
        foreach($fields as $field){
            if($field['field_type']=='username' || $field['field_type']=='password'){
                $f[$field['field_type']]= $field;
            }
            else
                continue;
        }
        return $f;
    }
    
    /*
     * Updates form design settings
     */
    public function save_form_design($data){
        $design_row= RM_DBManager::query_login_form('design');
        $data= json_encode($data);
        if(empty($design_row)){
            RM_DBManager::insert_login_form_options('design',$data);
        }
        else{
             RM_DBManager::update_login_form_options('design',$data);
        }
    }
    
    /*
     * Get login form design options
     */
    public function get_form_design(){
        $design_row= RM_DBManager::query_login_form('design');
        if(isset($design_row[0]))
        {
            $design=  json_decode($design_row[0]->value,true);
            if(empty($design))
                return array();
            return $design;
        }
        return array();
    }
    
    public function update_redirection($data){
        $data= json_encode($data);
        RM_DBManager::update_login_form_options('redirections',$data);
    }
    
    public function get_redirections(){
        $redirections= RM_DBManager::query_login_form('redirections');
        if(isset($redirections[0]))
        {
            $redirections=  json_decode($redirections[0]->value,true);
            if(empty($redirections))
                return array();
            return $redirections;
        }
        return array();
    }
    
    public function update_validations($data){
        $data= json_encode($data);
        RM_DBManager::update_login_form_options('validations',$data);
    }
    
    public function get_validations(){
        $redirections= RM_DBManager::query_login_form('validations');
        if(isset($redirections[0]))
        {
            $redirections=  json_decode($redirections[0]->value,true);
            if(empty($redirections))
                return array();
            if(!isset($redirections['sub_error_msg']))
                $redirections['sub_error_msg'] = RM_UI_Strings::get('MSG_NOT_AUTHORIZED');
            return $redirections;
        }
        return array();
    }
    
    public function get_recovery_options(){
        $options= RM_DBManager::query_login_form('recovery');
        if(isset($options[0]))
        {
            $options=  json_decode($options[0]->value,true);
            if(empty($options))
                return array();
            
            // Add new options and their default values
            $options['recovery_page']= isset($options['recovery_page']) ? $options['recovery_page'] : '';
            $options['rec_email_label']= isset($options['rec_email_label']) ? $options['rec_email_label'] : 'Your Email';
            $options['rec_btn_label']= isset($options['rec_btn_label']) ? $options['rec_btn_label'] : 'Reset Password';
            $options['rec_link_sent_msg']= isset($options['rec_link_sent_msg']) ? $options['rec_link_sent_msg'] : 'You will soon receive an email with password recovery link. Thank you!';
            $options['rec_email_not_found_msg']= isset($options['rec_email_not_found_msg']) ? $options['rec_email_not_found_msg'] : 'Sorry, we could not find an account associated with this email.';
            $options['rec_new_pass_label']= isset($options['rec_new_pass_label']) ? $options['rec_new_pass_label'] : 'New Password';
            $options['rec_conf_pass_label']= isset($options['rec_conf_pass_label']) ? $options['rec_conf_pass_label'] : 'Repeat Password';
            $options['rec_pass_btn_label']= isset($options['rec_pass_btn_label']) ? $options['rec_pass_btn_label'] : 'Change Password';
            $options['rec_pass_match_err']= isset($options['rec_pass_match_err']) ? $options['rec_pass_match_err'] : 'Your passwords do not match. Please make sure you type same password in both input boxes.';
            $options['rec_pas_suc_message']= isset($options['rec_pas_suc_message']) ? $options['rec_pas_suc_message'] : 'Your password was changed successfully! You can now login with your new password.';
            $options['rec_invalid_reset_err']= isset($options['rec_invalid_reset_err']) ? $options['rec_invalid_reset_err'] : 'The password reset link you clicked is invalid. You can paste the security token from your email below to proceed with password reset.';
            $options['rec_tok_sub_label']= isset($options['rec_tok_sub_label']) ? $options['rec_tok_sub_label'] : 'Proceed';
            $options['rec_tok_sub_label']= isset($options['rec_tok_sub_label']) ? $options['rec_tok_sub_label'] : 'Proceed';
            $options['rec_invalid_tok_err']= isset($options['rec_invalid_tok_err']) ? $options['rec_invalid_tok_err'] : 'The security token you entered is invalid. Please make sure you copied complete text string. You can try pasting again or <a href="{{password_recovery_link}}">restart password reset process</a>';
            $options['rec_link_expiry']= isset($options['rec_link_expiry']) ? absint($options['rec_link_expiry']) : 24;  
            $options['rec_link_exp_err']= isset($options['rec_link_exp_err']) ? $options['rec_link_exp_err'] : 'Your password reset security token has expired. Please enter your email below to generate a new token.';
            $options['rec_redirect_default']= isset($options['rec_redirect_default']) ? $options['rec_redirect_default'] : 0;  
            
            return $options;
        }
        return array();
    }
    
    public function update_recovery_options($data){
        $data= json_encode($data);
        RM_DBManager::update_login_form_options('recovery',$data);
    }
    
    public function get_auth_options() {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_Login_Service_Addon();
            return $addon_service->get_auth_options($this);
        }
    }
    
    public function update_auth_options($data) {
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_Login_Service_Addon();
            return $addon_service->update_auth_options($data, $this);
        }
    }
    
    public function get_template_options(){
        $options= RM_DBManager::query_login_form('email_templates');
        if(isset($options[0]))
        {
            $options=  json_decode($options[0]->value,true);
            if(empty($options))
                return array();
            
            $options['pass_reset']= isset($options['pass_reset']) ? $options['pass_reset'] : '<div class="rm-password-request">Hello,<br><br>Someone has requested a password reset for the following account on {{site_name}}:<br><br>Username: {{username}}<br><br>If this was a mistake, ignore this email and nothing will happen.<br><br><a href="{{password_recovery_link}}">Click here to reset your password</a><br><br>If the above link does not works, you can also paste following code manually:<br><br><div class="rm-security-token">{{security_token}}</div><br>Regards.</div>';
            return $options;
        }
        return array();
    }
    
    public function update_template_options($data){
        $data= json_encode($data);
        RM_DBManager::update_login_form_options('email_templates',$data);
    }
    
    public function update_button_config($data){
        $data= json_encode($data);
        RM_DBManager::update_login_form_options('btn_config',$data);
    }
    
    public function get_button_config(){
        $options= RM_DBManager::query_login_form('btn_config');
        if(isset($options[0]))
        {
            $options=  json_decode($options[0]->value,true);
            if(empty($options))
                return array();
            return $options;
        }
        return array();
    }
    
    public function get_login_view_options(){
        $options= RM_DBManager::query_login_form('login_view');
        if(isset($options[0]))
        {
            $options=  json_decode($options[0]->value,true);
            if(empty($options))
                return array();
            return $options;
        }
        return array();
    }
    
    public function get_log_options(){
        $options= RM_DBManager::query_login_form('log_retention');
        if(isset($options[0]))
        {
            $options=  json_decode($options[0]->value,true);
            if(empty($options))
                return array();
            
            if($options['logs_retention']=='records'){
                RM_DBManager::delete_login_log_records($options['no_of_records']);
            }else if($options['logs_retention']=='days'){
                RM_DBManager::delete_login_log_days($options['no_of_days']);
            }
            
            return $options;
        }
        return array();
    }
    
    public function update_log_options($data){
        $data= json_encode($data);
        RM_DBManager::update_login_form_options('log_retention',$data);
    }
    
    public function update_login_view_options($data){
        $data= json_encode($data);
        RM_DBManager::update_login_form_options('login_view',$data);
    }
    
    public function remove_field($field_indexes){
        $form= json_decode($this->get_form(),true);
        foreach($field_indexes as $field_index){
             unset($form['form_fields'][$field_index]);
        }
        $form['form_fields']= array_values($form['form_fields']);
        $this->update_form_fields($form);
    }
    
    public function check_login($username,$password){
        $username= sanitize_text_field($username);
        $password= sanitize_text_field($password);
        
        $username_accept = 'username';
        $form_options= $this->get_form();
        if(!empty($form_options))
        {
            $options=  json_decode($form_options,true);
            foreach($options['form_fields'] as $fields_arr){
                if(!empty($fields_arr['username_accepts'])){
                    $username_accept = $fields_arr['username_accepts'];
                }
            }
        }
        if($username_accept=='username'){
            $user= get_user_by('login', $username);
        }else if($username_accept=='email'){
            $user= get_user_by('email', $username);
        }else{
            $user= get_user_by('email', $username);
            if(empty($user)){
                $user= get_user_by('login', $username);
            }
        }
        
        if(empty($user))
            return false;
       
        $correct= wp_check_password($password, $user->data->user_pass, $user->data->ID);
        if(empty($correct))
            return false;
        return true;
    }
    
    public function send_2fa_otp($user){
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_Login_Service_Addon();
            return $addon_service->send_2fa_otp($user, $this);
        }
    }
    
    public function two_fact_auth_applicable($user){
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_Login_Service_Addon();
            return $addon_service->two_fact_auth_applicable($user, $this);
        }
    }
            
   public function check_otp($otp,$user){
       if(empty($user))
           return false;
       
       $row= RM_DBManager::get('FRONT_USERS', array('email'=>$user->user_email), array('%s','%d'));
       if(!empty($row)){
           if($row[0]->otp_code==$otp)
               return true;
       }
          
       return false;
   }
   
   public function is_otp_expired($otp,$user){
       if(empty($user))
           return true;
       
       $row= RM_DBManager::check_fa_otp_expired($otp,$user->user_email);
       if(!empty($row))
           return true;
       return false;
   }
   
   public function delete_otp($otp,$username){
     $user= get_user_by('login', $username);
     RM_DBManager::delete_rows('FRONT_USERS', array('email'=>$user->user_email,'otp_code'=>$otp));
   }
   
   public function check_max_failed_login(){
       $ip= $_SERVER['REMOTE_ADDR'];
       $v_options= $this->get_validations();
       $count= RM_DBManager::count_failed_login_attempt($ip,$v_options['allowed_failed_duration'],$v_options['allowed_failed_attempts']);
       return $count;
   }
   
   public function failed_login_before_ban(){
       $ip= $_SERVER['REMOTE_ADDR'];
       $v_options= $this->get_validations();
       $count= RM_DBManager::count_failed_login_attempt($ip,$v_options['allowed_duration_before_ban'],$v_options['allowed_attempts_before_ban']);
       return $count;
   }
   
   public function ban_ip($args){
       if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_Login_Service_Addon();
            return $addon_service->ban_ip($args, $this);
        }
   }
   
   public function is_ip_banned(){
       $ip= $_SERVER['REMOTE_ADDR'];
       $rm_submissions= new RM_Submissions();
       $ip_banned= $rm_submissions->is_blocked_ip($ip);
       if(!empty($ip_banned))
           return true;
       $results= RM_DBManager::get('LOGIN_LOG', array('ip'=>$ip), array('%s'), 'results', 0, 1, '*','id',true);
       
       if(empty($results))
           return false;
       $row = (array) $results[0];   
       if($row['ban']==1 && $ip_banned){
           return true;
       }
       
       return false;
   }
    
    public function reset_login_log() {
        
        $login_logs = RM_DBManager::reset_login_log();
    }
    
    public function get_logs_to_export() {
       if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_Login_Service_Addon();
            return $addon_service->get_logs_to_export($this);
        }
   }
    
    public function create_csv($data) {
        $csv_name = 'rm_submissions' . time() . mt_rand(10, 1000000);
        $csv_path = get_temp_dir() . $csv_name . '.csv';
        $csv = fopen($csv_path, "w");
        
        if (!$csv) {
            return false;
        }
        
        //Add UTF-8 header for proper encoding of the file
        //Thanks to Kristjan Johanson.
        fputs($csv, chr(0xEF).chr(0xBB).chr(0xBF) );
        
        foreach ($data as $a) {
            if (!fputcsv($csv, $a))
                return false;
        }

        fclose($csv);
        
        return $csv_path;
    }

    public function download_file($file, $unlink = true) {
        if (ob_get_contents()) {
            ob_end_clean();
        }
        
        if (file_exists($file)) {
            $mime_type = RM_Utilities::mime_content_type($file);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime_type);
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Expires: 0');
            readfile($file);
            if ($unlink)
                @unlink($file);
            exit();
        } 
        else{
            return false;
        }

        return true;
    }
    
    public function unblock_ip_from_log($ip){
        $v_options= $this->get_validations();
        if(!empty($v_options['en_ban_ip'])){
             RM_DBManager::unblock_ip_from_login_logs($ip);
        }
       
    }
    
    public function insert_login_log($args){
        require_once plugin_dir_path(plugin_dir_path(__FILE__)) . 'external/Browser/Browser.php';
        $browser= new RM_Browser();
        $args['browser']= $browser->getBrowser();
        //$args['login_url']= $_SERVER['REQUEST_URI'];        
        global $wp;  
        $args['login_url'] = esc_url(home_url(add_query_arg(array(),$wp->request)));

        RM_DBManager::insert_login_log($args);
    }
    
    public function incorrect_otp_attempts_exceeded($user,$limit){
        if(empty($user))
            return false;

        $rows= RM_DBManager::consecutive_incorrect_otp_attempts($user->user_email,$limit);
        
        if(count($rows)<$limit)
            return false;
        if(!empty($rows)){
            foreach($rows as $row){
                if($row->status!=0 || $row->failure_reason!='incorrect_otp'){
                    return false;
                }
            }
            return true;
        }
        return false;
    }
    
    public function get_user($username){
        $user= null;
        $username_accept = 'username';
        $form_options= $this->get_form();
        if(empty($form_options))
            return null;

        $options=  json_decode($form_options,true);
        foreach($options['form_fields'] as $fields_arr){
            if(!empty($fields_arr['username_accepts'])){
                $username_accept = $fields_arr['username_accepts'];
            }
        }
        
        
        if($username_accept=='username'){
            $user= get_user_by('login', $username);
        }else if($username_accept=='email'){
            $user= get_user_by('email', $username);
        }else{
            $user= get_user_by('email', $username);
            if(empty($user)){
                $user= get_user_by('login', $username);
            }
        }
        return $user;
    }
    
    public function validate_password($pass){
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_Login_Service_Addon();
            return $addon_service->validate_password($pass, $this);
        }
        if(strlen($pass)<7){
            $error_str = __('Error: Password must not be shorter than %d characters', 'custom-registration-form-builder-with-submission-manager');
            $error_str = sprintf($error_str,7);
            return $error_str;
        }
        return;
    }
}