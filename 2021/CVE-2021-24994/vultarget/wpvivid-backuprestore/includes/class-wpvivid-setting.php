<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
class WPvivid_Setting
{
    public static function init_option()
    {
        $ret=self::get_option('wpvivid_email_setting');
        if(empty($ret))
        {
            self::set_default_email_option();
        }

        $ret=self::get_option('wpvivid_compress_setting');
        if(empty($ret))
        {
            self::set_default_compress_option();
        }

        $ret=self::get_option('wpvivid_local_setting');
        if(empty($ret))
        {
            self::set_default_local_option();
        }

        $ret=self::get_option('wpvivid_upload_setting');
        if(empty($ret))
        {
            self::set_default_upload_option();
        }

        $ret=self::get_option('wpvivid_common_setting');
        if(empty($ret))
        {
            self::set_default_common_option();
        }
    }

    public static function get_default_option($option_name)
    {
        $options=array();

        switch ($option_name)
        {
            case 'wpvivid_compress_setting':
                $options=self::set_default_compress_option();
                break;
            case 'wpvivid_local_setting':
                $options=self::set_default_local_option();
                break;
            case 'wpvivid_upload_setting':
                $options=self::set_default_upload_option();
                break;
            case 'wpvivid_common_setting':
                $options=self::set_default_common_option();
                break;
        }
        return $options;
    }

    public static function set_default_option()
    {
        self::set_default_compress_option();
        self::set_default_local_option();
        self::set_default_upload_option();
        self::set_default_common_option();
    }

    public static function set_default_compress_option()
    {
        $compress_option['compress_type']=WPVIVID_DEFAULT_COMPRESS_TYPE;
        $compress_option['max_file_size']=WPVIVID_DEFAULT_MAX_FILE_SIZE;
        $compress_option['no_compress']=WPVIVID_DEFAULT_NO_COMPRESS;
        $compress_option['use_temp_file']=WPVIVID_DEFAULT_USE_TEMP;
        $compress_option['use_temp_size']=WPVIVID_DEFAULT_USE_TEMP_SIZE;
        $compress_option['exclude_file_size']=WPVIVID_DEFAULT_EXCLUDE_FILE_SIZE;
        $compress_option['subpackage_plugin_upload']=WPVIVID_DEFAULT_SUBPACKAGE_PLUGIN_UPLOAD;
        self::update_option('wpvivid_compress_setting',$compress_option);
        return $compress_option;
    }

    public static function set_default_local_option()
    {
        $local_option['path']=WPVIVID_DEFAULT_BACKUP_DIR;
        $local_option['save_local']=1;
        self::update_option('wpvivid_local_setting',$local_option);
        return $local_option;
    }

    public static function set_default_upload_option()
    {
        $upload_option=array();
        self::update_option('wpvivid_upload_setting',$upload_option);
        return $upload_option;
    }

    public static function set_default_email_option()
    {
        $email_option['send_to']=array();
        $email_option['always']=true;
        $email_option['email_enable']=false;
        self::update_option('wpvivid_email_setting',$email_option);
        return $email_option;
    }

    public static function set_default_common_option()
    {
        $sapi_type=php_sapi_name();

        if($sapi_type=='cgi-fcgi'||$sapi_type==' fpm-fcgi')
        {
            $common_option['max_execution_time']=WPVIVID_MAX_EXECUTION_TIME_FCGI;
        }
        else
        {
            $common_option['max_execution_time']=WPVIVID_MAX_EXECUTION_TIME;
        }

        $common_option['log_save_location']=WPVIVID_DEFAULT_LOG_DIR;
        $common_option['max_backup_count']=WPVIVID_DEFAULT_BACKUP_COUNT;
        $common_option['show_admin_bar']=WPVIVID_DEFAULT_ADMIN_BAR;
        //$common_option['show_tab_menu']=WPVIVID_DEFAULT_TAB_MENU;
        $common_option['domain_include']=WPVIVID_DEFAULT_DOMAIN_INCLUDE;
        $common_option['estimate_backup']=WPVIVID_DEFAULT_ESTIMATE_BACKUP;
        $common_option['max_resume_count']=WPVIVID_RESUME_RETRY_TIMES;
        $common_option['memory_limit']=WPVIVID_MEMORY_LIMIT;
        $common_option['restore_memory_limit']=WPVIVID_RESTORE_MEMORY_LIMIT;
        $common_option['migrate_size']=WPVIVID_MIGRATE_SIZE;
        self::update_option('wpvivid_common_setting',$common_option);
        return $common_option;
    }

    public static function get_option($option_name, $default = array())
    {
        $ret = get_option($option_name, $default);
        if(empty($ret))
        {
            self::get_default_option($option_name);
        }
        return $ret;
    }

    public static function get_last_backup_message($option_name, $default = array()){
        $message = self::get_option($option_name, $default);
        $ret = array();
        if(!empty($message['id'])) {
            $ret['id'] = $message['id'];
            $ret['status'] = $message['status'];
            $ret['status']['start_time'] = date("M d, Y H:i", $ret['status']['start_time']);
            $ret['status']['run_time'] = date("M d, Y H:i", $ret['status']['run_time']);
            $ret['status']['timeout'] = date("M d, Y H:i", $ret['status']['timeout']);
            if(isset($message['options']['log_file_name']))
                $ret['log_file_name'] = $message['options']['log_file_name'];
            else
                $ret['log_file_name'] ='';
        }
        return $ret;
    }

    public static function get_backupdir()
    {
        $dir=self::get_option('wpvivid_local_setting');

        if(!isset($dir['path']))
        {
            $dir=self::set_default_local_option();
        }
        if(!is_dir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$dir['path']))
        {
            @mkdir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$dir['path'],0777,true);
            @fopen(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$dir['path'].DIRECTORY_SEPARATOR.'index.html', 'x');
            $tempfile=@fopen(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$dir['path'].DIRECTORY_SEPARATOR.'.htaccess', 'x');
            if($tempfile)
            {
                $text="deny from all";
                fwrite($tempfile,$text );
                fclose($tempfile);
            }
            else
            {
                return false;
            }

        }

        return $dir['path'];
    }

    public static function set_backupdir($dir)
    {
        if(!isset($dir['path']))
        {
            $dir=self::set_default_local_option();
        }
        else
        {
            self::update_option('wpvivid_local_setting',$dir);
        }

        if(!is_dir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$dir['path']))
        {
            @mkdir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$dir['path'],0777,true);
        }

        @fopen(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$dir['path'].'/index.html', 'x');
        $tempfile=@fopen(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$dir['path'].'/.htaccess', 'x');
        if($tempfile)
        {
            $text="deny from all";
            fwrite($tempfile,$text );
            fclose($tempfile);
        }
    }

    public static function get_save_local()
    {
        $local=self::get_option('wpvivid_local_setting');

        if(!isset($local['save_local']))
        {
            $local=self::set_default_local_option();
        }

        return $local['save_local'];
    }

    public static function update_option($option_name,$options)
    {
        update_option($option_name,$options,'no');
    }

    public static function delete_option($option_name)
    {
        delete_option($option_name);
    }

    public static function get_tasks()
    {
        $default = array();
        return $options = get_option('wpvivid_task_list', $default);
    }

    public static function update_task($id,$task)
    {
        $default = array();
        $options = get_option('wpvivid_task_list', $default);
        $options[$id]=$task;
        self::update_option('wpvivid_task_list',$options);
    }

    public static function delete_task($id)
    {
        $default = array();
        $options = get_option('wpvivid_task_list', $default);
        unset($options[$id]);
        self::update_option('wpvivid_task_list',$options);
    }

    public static function check_compress_options()
    {
        $options =self::get_option('wpvivid_compress_setting');

        if(!isset($options['compress_type'])||!isset($options['max_file_size'])||
            !isset($options['no_compress'])||!isset($options['exclude_file_size'])||
            !isset($options['use_temp_file'])||!isset($options['use_temp_size']))
        {
            self::set_default_compress_option();
        }
    }

    public static function check_local_options()
    {
        $options =self::get_option('wpvivid_local_setting');

        if(!isset($options['path'])||!isset($options['save_local']))
        {
            self::set_default_local_option();
        }

        return true;
    }

    /*public static function get_backup_options($post)
    {
        self::check_compress_options();
        self::check_local_options();

        if($post=='files+db')
        {
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_DB]=0;
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_THEMES]=0;
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_PLUGIN]=0;
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_UPLOADS]=0;
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_CONTENT]=0;
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_CORE]=0;
        }
        else if($post=='files')
        {
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_THEMES]=0;
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_PLUGIN]=0;
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_UPLOADS]=0;
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_CONTENT]=0;
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_CORE]=0;
        }
        else if($post=='db')
        {
            $backup_options['backup']['backup_type'][WPVIVID_BACKUP_TYPE_DB]=0;
        }
        else
        {
            //return false;
        }

        $backup_options['compress']=self::get_option('wpvivid_compress_setting');
        $backup_options['dir']=self::get_backupdir();
        return $backup_options;
    }*/

    public static function get_remote_option($id)
    {
        $upload_options=self::get_option('wpvivid_upload_setting');
        if(array_key_exists($id,$upload_options))
        {
            return $upload_options[$id];
        }
        else
        {
            return false;
        }
    }

    public static function get_remote_options($remote_ids=array())
    {
        if(empty($remote_ids))
        {
            $remote_ids=WPvivid_Setting::get_user_history('remote_selected');
        }

        if(empty($remote_ids))
        {
            return false;
        }

        $options=array();
        $upload_options=WPvivid_Setting::get_option('wpvivid_upload_setting');
        foreach ($remote_ids as $id)
        {
            if(array_key_exists($id,$upload_options))
            {
                $options[$id]=$upload_options[$id];
            }
        }
        if(empty($options))
            return false;
        else
            return $options;
    }

    public static function get_all_remote_options()
    {
        $upload_options=self::get_option('wpvivid_upload_setting');
        $upload_options['remote_selected']=WPvivid_Setting::get_user_history('remote_selected');
        return $upload_options;
    }

    public static function add_remote_options($remote)
    {
        $upload_options=self::get_option('wpvivid_upload_setting');
        $id=uniqid('wpvivid-remote-');

        $remote=apply_filters('wpvivid_pre_add_remote',$remote,$id);

        $upload_options[$id]=$remote;
        self::update_option('wpvivid_upload_setting',$upload_options);
        return $id;
    }

    public static function delete_remote_option($id)
    {
        do_action('wpvivid_delete_remote_token',$id);

        $upload_options=self::get_option('wpvivid_upload_setting');

        if(array_key_exists($id,$upload_options))
        {
            unset( $upload_options[$id]);

            self::update_option('wpvivid_upload_setting',$upload_options);
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function update_remote_option($remote_id,$remote)
    {
        $upload_options=self::get_option('wpvivid_upload_setting');

        if(array_key_exists($remote_id,$upload_options))
        {
            $remote=apply_filters('wpvivid_pre_add_remote',$remote,$remote_id);
            $upload_options[$remote_id]=$remote;
            self::update_option('wpvivid_upload_setting',$upload_options);
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function get_setting($all,$options_name)
    {
        $get_options=array();
        if($all==true)
        {
            $get_options[]='wpvivid_email_setting';
            $get_options[]='wpvivid_compress_setting';
            $get_options[]='wpvivid_local_setting';
            $get_options[]='wpvivid_common_setting';
            $get_options = apply_filters('wpvivid_get_setting_addon', $get_options);
        }
        else
        {
            $get_options[]=$options_name;
        }

        $ret['result']='success';
        $ret['options']=array();

        foreach ($get_options as $option_name)
        {
            $ret['options'][$option_name]=self::get_option($option_name);
        }

        return $ret;
    }

    public static function update_setting($options)
    {
        foreach ($options as $option_name=>$option)
        {
            self::update_option($option_name,$option);
        }
        $ret['result']='success';
        return $ret;
    }

    public static function export_setting_to_json($setting=true,$history=true,$review=true,$backup_list=true)
    {
        global $wpvivid_plugin;
        $json['plugin']=$wpvivid_plugin->get_plugin_name();
        $json['version']=WPVIVID_PLUGIN_VERSION;
        $json['setting']=$setting;
        $json['history']=$history;
        $json['data']['wpvivid_init']=self::get_option('wpvivid_init');

        if($setting)
        {
            $json['data']['wpvivid_schedule_setting']=self::get_option('wpvivid_schedule_setting');
            if(!empty( $json['data']['wpvivid_schedule_setting']))
            {
                if(isset($json['data']['wpvivid_schedule_setting']['backup']['backup_files']))
                    $json['data']['wpvivid_schedule_setting']['backup_type']=$json['data']['wpvivid_schedule_setting']['backup']['backup_files'];
                if(isset($json['data']['wpvivid_schedule_setting']['backup']['local']))
                {
                    if($json['data']['wpvivid_schedule_setting']['backup']['local'] == 1){
                        $json['data']['wpvivid_schedule_setting']['save_local_remote']='local';
                    }
                    else{
                        $json['data']['wpvivid_schedule_setting']['save_local_remote']='remote';
                    }
                }

                $json['data']['wpvivid_schedule_setting']['lock']=0;
                if(wp_get_schedule(WPVIVID_MAIN_SCHEDULE_EVENT))
                {
                    $recurrence = wp_get_schedule(WPVIVID_MAIN_SCHEDULE_EVENT);
                    $timestamp = wp_next_scheduled(WPVIVID_MAIN_SCHEDULE_EVENT);
                    $json['data']['wpvivid_schedule_setting']['recurrence']=$recurrence;
                    $json['data']['wpvivid_schedule_setting']['next_start']=$timestamp;
                }
            }
            else
            {
                $json['data']['wpvivid_schedule_setting']=array();
            }
            $json['data']['wpvivid_compress_setting']=self::get_option('wpvivid_compress_setting');
            $json['data']['wpvivid_local_setting']=self::get_option('wpvivid_local_setting');
            $json['data']['wpvivid_upload_setting']=self::get_option('wpvivid_upload_setting');
            $json['data']['wpvivid_common_setting']=self::get_option('wpvivid_common_setting');
            $json['data']['wpvivid_email_setting']=self::get_option('wpvivid_email_setting');
            $json['data']['wpvivid_saved_api_token']=self::get_option('wpvivid_saved_api_token');
            $json = apply_filters('wpvivid_export_setting_addon', $json);
            /*if(isset($json['data']['wpvivid_local_setting']['path'])){
                unset($json['data']['wpvivid_local_setting']['path']);
            }*/
            if(isset($json['data']['wpvivid_common_setting']['log_save_location'])){
                unset($json['data']['wpvivid_common_setting']['log_save_location']);
            }
            if(isset($json['data']['wpvivid_common_setting']['backup_prefix'])){
                unset($json['data']['wpvivid_common_setting']['backup_prefix']);
            }
        }

        if($history)
        {
            $json['data']['wpvivid_task_list']=self::get_option('wpvivid_task_list');
            $json['data']['wpvivid_last_msg']=self::get_option('wpvivid_last_msg');
            $json['data']['wpvivid_user_history']=self::get_option('wpvivid_user_history');
            $json = apply_filters('wpvivid_history_addon', $json);
        }

        if($backup_list){
            $json['data']['wpvivid_backup_list']=self::get_option('wpvivid_backup_list');
            $json = apply_filters('wpvivid_backup_list_addon', $json);
        }

        if($review)
        {
            $json['data']['wpvivid_need_review']=self::get_option('wpvivid_need_review');
            $json['data']['cron_backup_count']=self::get_option('cron_backup_count');
            $json['data']['wpvivid_review_msg']=self::get_option('wpvivid_review_msg');
            $json = apply_filters('wpvivid_review_addon', $json);
        }
        return $json;
    }

    public static function import_json_to_setting($json)
    {
        wp_cache_delete('notoptions', 'options');
        wp_cache_delete('alloptions', 'options');
        foreach ($json['data'] as $option_name=>$option)
        {
            wp_cache_delete($option_name, 'options');
            delete_option($option_name);
            self::update_option($option_name,$option);
        }
    }

    public static function set_max_backup_count($count)
    {
        $options=self::get_option('wpvivid_common_setting');
        $options['max_backup_count']=$count;
        self::update_option('wpvivid_common_setting',$options);
    }

    public static function get_max_backup_count()
    {
        $options=self::get_option('wpvivid_common_setting');
        if(isset($options['max_backup_count']))
        {
            return $options['max_backup_count'];
        }
        else
        {
            return WPVIVID_MAX_BACKUP_COUNT;
        }
    }

    public static function get_mail_setting()
    {
        return self::get_option('wpvivid_email_setting');
    }

    public static function get_admin_bar_setting(){
        $options=self::get_option('wpvivid_common_setting');
        if(isset($options['show_admin_bar']))
        {
            if($options['show_admin_bar']){
                return true;
            }
            else{
                return false;
            }
        }
        else
        {
            return true;
        }
    }

    public static function update_user_history($action,$value)
    {
        $options=self::get_option('wpvivid_user_history');
        $options[$action]=$value;
        self::update_option('wpvivid_user_history',$options);
    }

    public static function get_user_history($action)
    {
        $options=self::get_option('wpvivid_user_history');
        if(array_key_exists($action,$options))
        {
            return $options[$action];
        }
        else
        {
            return array();
        }
    }

    public static function get_retain_local_status()
    {
        $options=self::get_option('wpvivid_common_setting');
        if(isset($options['retain_local']))
        {
            if($options['retain_local']){
                return true;
            }
            else{
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public static function get_sync_data()
    {
        $data['setting']['wpvivid_compress_setting']=self::get_option('wpvivid_compress_setting');
        $data['setting']['wpvivid_local_setting']=self::get_option('wpvivid_local_setting');
        $data['setting']['wpvivid_common_setting']=self::get_option('wpvivid_common_setting');
        $data['setting']['wpvivid_email_setting']=self::get_option('wpvivid_email_setting');
        $data['setting']['cron_backup_count']=self::get_option('cron_backup_count');
        $data['schedule']=self::get_option('wpvivid_schedule_setting');
        $data['remote']['upload']=self::get_option('wpvivid_upload_setting');
        $data['remote']['history']=self::get_option('wpvivid_user_history');
        $data['last_backup_report'] = get_option('wpvivid_backup_reports');

        $data['setting_addon'] = $data['setting'];
        $data['setting_addon']['wpvivid_staging_options']=array();
        $data['backup_custom_setting']=array();
        $data['menu_capability']=array();
        $data['white_label_setting']=array();
        $data['incremental_backup_setting']=array();
        $data['schedule_addon']=array();
        $data['time_zone']=false;
        $data['is_pro']=false;
        $data['is_install']=false;
        $data['is_login']=false;
        $data['latest_version']='';
        $data['current_version']='';
        $data['dashboard_version'] = '';
        $data['addons_info'] = array();
        $data=apply_filters('wpvivid_get_wpvivid_info_addon_mainwp_ex', $data);
        return $data;
    }
}