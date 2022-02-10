<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
class WPvivid_taskmanager
{
    public static function new_backup_task($option,$type,$action='backup')
    {
        $id=uniqid('wpvivid-');
        $task['id']=$id;
        $task['action']=$action;
        $task['type']=$type;

        $task['status']['start_time']=time();
        $task['status']['run_time']=time();
        $task['status']['timeout']=time();
        $task['status']['str']='ready';
        $task['status']['resume_count']=0;

        $task['options']=$option;
        $task['options']['file_prefix']=$task['id'].'_'.date('Y-m-d-H-i',$task['status']['start_time']);
        $task['options']['log_file_name']=$id.'_backup';
        $log=new WPvivid_Log();
        $log->CreateLogFile($task['options']['log_file_name'],'no_folder','backup');
        $log->CloseFile();

        $task['data']['doing']='backup';
        $task['data']['backup']['doing']='';
        $task['data']['backup']['finished']=0;
        $task['data']['backup']['progress']=0;
        $task['data']['backup']['job_data']=array();
        $task['data']['backup']['sub_job']=array();
        $task['data']['backup']['db_size']='0';
        $task['data']['backup']['files_size']['sum']='0';
        $task['data']['upload']['doing']='';
        $task['data']['upload']['finished']=0;
        $task['data']['upload']['progress']=0;
        $task['data']['upload']['job_data']=array();
        $task['data']['upload']['sub_job']=array();
        WPvivid_Setting::update_task($id,$task);
        $ret['result']='success';
        $ret['task_id']=$task['id'];
        return $ret;
    }

    public static function get_backup_task_prefix($task_id)
    {
        $tasks=WPvivid_Setting::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task = $tasks[$task_id];
            return  $task['options']['file_prefix'];
        }
        else
        {
            return false;
        }
    }

    public static function new_upload_task($option,$type)
    {
        $id=uniqid('wpvivid-');
        $task['id']=$id;
        $task['action']='upload';
        $task['type']='Manual';
        $task['type']=$type;

        $task['status']['start_time']=time();
        $task['status']['run_time']=time();
        $task['status']['timeout']=time();
        $task['status']['str']='ready';
        $task['status']['resume_count']=0;

        $task['options']=$option;
        $task['options']['file_prefix']=$task['id'].'_'.date('Y-m-d-H-i',$task['status']['start_time']);
        $task['options']['log_file_name']=$id.'_backup';
        $log=new WPvivid_Log();
        $log->CreateLogFile($task['options']['log_file_name'],'no_folder','backup');
        $log->CloseFile();

        $task['data']['doing']='upload';
        $task['data']['doing']='backup';
        $task['data']['backup']['doing']='';
        $task['data']['backup']['finished']=1;
        $task['data']['backup']['progress']=100;
        $task['data']['backup']['job_data']=array();
        $task['data']['backup']['sub_job']=array();
        $task['data']['backup']['db_size']='0';
        $task['data']['backup']['files_size']['sum']='0';
        $task['data']['upload']['doing']='';
        $task['data']['upload']['finished']=0;
        $task['data']['upload']['progress']=0;
        $task['data']['upload']['job_data']=array();
        $task['data']['upload']['sub_job']=array();
        WPvivid_Setting::update_task($id,$task);
        $ret['result']='success';
        $ret['task_id']=$task['id'];
        return $ret;
    }

    public static function delete_ready_task()
    {
        $tasks=WPvivid_Setting::get_tasks();
        $delete_ids=array();
        foreach ($tasks as $task)
        {
            if($task['status']['str']=='ready')
            {
                $delete_ids[]=$task['id'];
            }
        }

        foreach ($delete_ids as $id)
        {
            unset($tasks[$id]);
        }
        if(!empty($delete_ids))
            WPvivid_Setting::update_option('wpvivid_task_list',$tasks);
    }

    public static function is_task_canceled($task_id)
    {
        if(self::get_task($task_id)!==false)
        {
            $file_name = self::get_task_options($task_id, 'file_prefix');
            $backup_options = self::get_task_options($task_id, 'backup_options');

            $file = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $backup_options['dir'] . DIRECTORY_SEPARATOR . $file_name . '_cancel';

            if (file_exists($file))
            {
                return true;
            }
        }
        return false;
    }

    public static function get_backup_tasks_info($action){
        $tasks=WPvivid_Setting::get_tasks();
        $ret=array();
        foreach ($tasks as $task)
        {
            if($task['action']==$action)
            {
                $ret[$task['id']]['status']=self::get_backup_tasks_status($task['id']);
                $ret[$task['id']]['is_canceled']=self::is_task_canceled($task['id']);
                $ret[$task['id']]['size']=self::get_backup_size($task['id']);
                $ret[$task['id']]['data']=self::get_backup_tasks_progress($task['id']);
            }
        }
        return $ret;
    }

    public static function get_backup_tasks_status($task_id){
        $tasks=WPvivid_Setting::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task = $tasks[$task_id];
            return $task['status'];
        }
        else
        {
            return false;
        }
    }

    public static function get_backup_size($task_id){
        $tasks=WPvivid_Setting::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task = $tasks[$task_id];

            if(array_key_exists('db_size',$task['data']['backup']))
            {
                $ret['db_size']=$task['data']['backup']['db_size'];
                $ret['files_size']=$task['data']['backup']['files_size'];
                return $ret;
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }
    }

    public static function get_backup_tasks_progress($task_id){
        $tasks=WPvivid_Setting::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task = $tasks[$task_id];
            $current_time=date("Y-m-d H:i:s");
            $create_time=date("Y-m-d H:i:s",$task['status']['start_time']);
            $time_diff=strtotime($current_time)-strtotime($create_time);
            $running_time='';
            if(date("G",$time_diff) > 0){
                $running_time .= date("G",$time_diff).' hour(s)';
            }
            if(intval(date("i",$time_diff)) > 0){
                $running_time .= intval(date("i",$time_diff)).' min(s)';
            }
            if(intval(date("s",$time_diff)) > 0){
                $running_time .= intval(date("s",$time_diff)).' second(s)';
            }
            $next_resume_time=WPvivid_Schedule::get_next_resume_time($task['id']);

            $ret['type']=$task['data']['doing'];
            $ret['progress']=$task['data'][$ret['type']]['progress'];
            $ret['doing']=$task['data'][$ret['type']]['doing'];
            if(isset($task['data'][$ret['type']]['sub_job'][$ret['doing']]['progress']))
                $ret['descript']=__($task['data'][$ret['type']]['sub_job'][$ret['doing']]['progress'], 'wpvivid-backuprestore');
            else
                $ret['descript']='';
            if(isset($task['data'][$ret['type']]['sub_job'][$ret['doing']]['upload_data']))
                $ret['upload_data']=$task['data'][$ret['type']]['sub_job'][$ret['doing']]['upload_data'];
            $task['data'][$ret['type']]['sub_job'][$ret['doing']]['upload_data']=false;
            $ret['running_time']=$running_time;
            $ret['running_stamp']=$time_diff;
            $ret['next_resume_time']=$next_resume_time;
            return $ret;
        }
        else
        {
            return false;
        }
    }

    public static function get_backup_task_status($task_id)
    {
        $tasks=WPvivid_Setting::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task = $tasks[$task_id];
            return $task['status'];
        }
        else
        {
            return false;
        }
    }

    public static function update_backup_task_status($task_id,$reset_start_time=false,$status='',$reset_timeout=false,$resume_count=false,$error='')
    {
        $tasks=WPvivid_Setting::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task = $tasks[$task_id];
            $task['status']['run_time']=time();
            if($reset_start_time)
                $task['status']['start_time']=time();
            if(!empty($status))
            {
                $task['status']['str']=$status;
            }
            if($reset_timeout)
                $task['status']['timeout']=time();
           if($resume_count!==false)
           {
               $task['status']['resume_count']=$resume_count;
           }

            if(!empty($error))
           {
               $task['status']['error']=$error;
           }
            WPvivid_Setting::update_task($task_id,$task);
            return $task;
        }
        else
        {
            return false;
        }
    }

    public static function get_backup_task_error($task_id)
    {
        $tasks=WPvivid_Setting::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task = $tasks[$task_id];
            return $task['status']['error'];
        }
        else
        {
            return false;
        }
    }

    public static function get_task_options($task_id,$option_names)
    {
        $tasks=WPvivid_Setting::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task=$tasks[$task_id];

            if(is_array($option_names))
            {
                $options=array();
                foreach ($option_names as $name)
                {
                    $options[$name]=$task['options'][$name];
                }
                return $options;
            }
            else
            {
                return $task['options'][$option_names];
            }
        }
        else
        {
            return false;
        }
    }

    public static function update_task_options($task_id,$option_name,$option)
    {
        $tasks=WPvivid_Setting::get_tasks();
        if(array_key_exists ($task_id,$tasks))
        {
            $task=$tasks[$task_id];
            $task['options'][$option_name]=$option;

            WPvivid_Setting::update_task($task_id,$task);
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function update_backup_main_task_progress($task_id,$job_name,$progress,$finished,$job_data=array())
    {
        $task=self::get_task($task_id);
        if($task!==false)
        {
            $task['status']['run_time']=time();
            $task['status']['str']='running';
            $task['data']['doing']=$job_name;
            $task['data'][$job_name]['finished']=$finished;
            $task['data'][$job_name]['progress']=$progress;
            $task['data'][$job_name]['job_data']=$job_data;
            WPvivid_Setting::update_task($task_id,$task);
        }
    }

    public static function update_backup_sub_task_progress($task_id,$job_name,$sub_job_name,$finished,$progress,$job_data=array(),$upload_data=array())
    {
        $task=self::get_task($task_id);
        if($task!==false)
        {
            $task['status']['run_time']=time();
            $task['status']['str']='running';
            $task['data']['doing']=$job_name;
            if(empty($sub_job_name))
            {
                $sub_job_name=$task['data'][$job_name]['doing'];
            }
            $task['data'][$job_name]['doing']=$sub_job_name;
            $task['data'][$job_name]['sub_job'][$sub_job_name]['finished']=$finished;
            if(!empty($progress))
                $task['data'][$job_name]['sub_job'][$sub_job_name]['progress']=$progress;
            if(!empty($job_data))
            {
                $task['data'][$job_name]['sub_job'][$sub_job_name]['job_data']=$job_data;
            }
            else
            {
                if(!isset($task['data'][$job_name]['sub_job'][$sub_job_name]['job_data']))
                {
                    $task['data'][$job_name]['sub_job'][$sub_job_name]['job_data']=array();
                }
            }
            if(!empty($upload_data)){
                $task['data'][$job_name]['sub_job'][$sub_job_name]['upload_data']=$upload_data;
            }
            else{
                if(!isset($task['data'][$job_name]['sub_job'][$sub_job_name]['upload_data'])){
                    $task['data'][$job_name]['sub_job'][$sub_job_name]['upload_data']=array();
                }
            }
            WPvivid_Setting::update_task($task_id,$task);
        }
    }

    public static function get_backup_main_task_progress($task_id,$job_name='')
    {
        $task=self::get_task($task_id);

        if(empty($job_name))
        {
            $job_name=$task['data']['doing'];
            return $job_name;
        }

        if(array_key_exists($job_name,$task['data']))
        {
            return $task['data'][$job_name];
        }
        return false;
    }

    public static function get_backup_sub_task_progress($task_id,$job_name,$sub_job_name)
    {
        $task=self::get_task($task_id);
        if(array_key_exists($job_name,$task['data']))
        {
            if(array_key_exists($sub_job_name,$task['data'][$job_name]['sub_job']))
            {
                return $task['data'][$job_name]['sub_job'][$sub_job_name];
            }
        }
        return false;
    }

    public static function update_backup_db_task_info($task_id,$db_info)
    {
        $task=self::get_task($task_id);
        $task['data']['backup']['sub_job']['backup_db']['db_info']=$db_info;
        WPvivid_Setting::update_task($task_id,$task);
    }

    public static function update_file_and_db_info($task_id,$db_size,$files_size)
    {
        $task=self::get_task($task_id);
        $task['data']['backup']['db_size']=$db_size;
        $task['data']['backup']['files_size']=$files_size;
        WPvivid_Setting::update_task($task_id,$task);
    }

    public static function update_download_cache($backup_id,$cache)
    {
        $default = array();

        $options = get_option('wpvivid_download_cache', $default);
        $options[$backup_id]['cache']=$cache;
        WPvivid_Setting::update_option('wpvivid_download_cache',$options);
    }

    public static function get_download_cache($backup_id)
    {
        $default = array();

        $options = get_option('wpvivid_download_cache', $default);

        if(array_key_exists($backup_id,$options))
        {
            return $options[$backup_id]['cache'];
        }

        return false;
    }

    public static function get_task($id)
    {
        $tasks=WPvivid_Setting::get_tasks();
        if(array_key_exists ($id,$tasks))
        {
            $task=$tasks[$id];
            return $task;
        }
        else
        {
            return false;
        }
    }

    public static function get_tasks()
    {
        $tasks=WPvivid_Setting::get_tasks();
        return $tasks;
    }

    public static function get_tasks_by_action($action)
    {
        $tasks=WPvivid_Setting::get_tasks();
        $ret=array();
        foreach ($tasks as $task)
        {
            if($task['action']==$action)
            {
                $ret[$task['id']]=$task;
            }
        }
        return $ret;
    }

    public static function is_tasks_backup_running()
    {
        $tasks=WPvivid_Setting::get_tasks();
        foreach ($tasks as $task)
        {
            if ($task['status']['str']=='running'||$task['status']['str']=='no_responds')
            {
                return true;
            }
        }
        return false;
    }

    public static function get_tasks_backup_running()
    {
        $tasks=WPvivid_Setting::get_tasks();
        $ret=array();
        foreach ($tasks as $task)
        {
            if($task['action']=='backup')
            {
                if ($task['status']['str']=='running')
                {
                    $ret[$task['id']]=$task;
                }
            }
        }
        return $ret;
    }

    public static function update_task($task)
    {
        WPvivid_Setting::update_task($task['id'],$task);
    }

    public static function delete_task($id)
    {
        WPvivid_Setting::delete_task($id);
    }

    public static function mark_task($id)
    {
        $tasks=WPvivid_Setting::get_tasks();
        if(array_key_exists ($id,$tasks))
        {
            $task=$tasks[$id];
            $task['marked']=1;
            WPvivid_Setting::update_task($id,$task);
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function delete_marked_task()
    {
        $has_marked = 0;
        $tasks=WPvivid_Setting::get_tasks();
        $delete_ids=array();
        foreach ($tasks as $task)
        {
            if(isset($task['marked']))
            {
                $delete_ids[]=$task['id'];
            }
        }
        foreach ($delete_ids as $id)
        {
            unset($tasks[$id]);
            $has_marked = 1;
        }
        WPvivid_Setting::update_option('wpvivid_task_list',$tasks);
        return $has_marked;
    }

    public static function delete_out_of_date_finished_task()
    {
        $tasks=WPvivid_Setting::get_tasks();
        $delete_ids=array();
        foreach ($tasks as $task)
        {
            if($task['status']['str']=='error'||$task['status']['str']=='completed')
            {
                if(time()-$task['status']['run_time']>60)
                {
                    $delete_ids[]=$task['id'];
                }
            }
        }

        foreach ($delete_ids as $id)
        {
            unset($tasks[$id]);
        }

        WPvivid_Setting::update_option('wpvivid_task_list',$tasks);
    }

    public static function delete_all_task()
    {
        WPvivid_Setting::delete_option('wpvivid_task_list');
    }

    public static function is_backup_task_timeout($task)
    {
        $current_time=date("Y-m-d H:i:s");
        $run_time=date("Y-m-d H:i:s",  $task['data']['run_time']);
        $running_time=strtotime($current_time)-strtotime($run_time);
        if($running_time>$task['data']['options']['max_execution_time'])
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public static function new_download_task_v2($file_name)
    {
        $default = array();
        wp_cache_delete('notoptions', 'options');
        wp_cache_delete('alloptions', 'options');
        wp_cache_delete('wpvivid_download_task_v2', 'options');

        $options = get_option('wpvivid_download_task_v2', $default);

        $task['file_name']=$file_name;
        $task['start_time']=time();
        $task['run_time']=time();
        $task['progress_text']='start download file:'.$file_name;
        $task['status']='running';
        $task['error']='';
        $options[$file_name]=$task;

        WPvivid_Setting::update_option('wpvivid_download_task_v2',$options);
        return $task;
    }

    public static function is_download_task_running_v2($file_name)
    {
        $default = array();
        $options = get_option('wpvivid_download_task_v2', $default);

        if(empty($options))
        {
            return false;
        }
        if(array_key_exists($file_name,$options))
        {
            $task=$options[$file_name];

            if($task['status'] === 'error')
            {
                return false;
            }

            if(time()-$task['run_time']>60)
            {
                return false;
            }
            else {
                return true;
            }
        }
        return false;
    }

    public static function update_download_task_v2(&$task,$progress_text,$status='',$error='')
    {
        $default = array();
        wp_cache_delete('notoptions', 'options');
        wp_cache_delete('alloptions', 'options');
        wp_cache_delete('wpvivid_download_task_v2', 'options');

        $options = get_option('wpvivid_download_task_v2', $default);

        $file_name=$task['file_name'];
        $task['run_time']=time();
        $task['progress_text']=$progress_text;
        if($status!='')
        {
            $task['status']=$status;
            if($error!='')
            {
                $task['error']=$error;
            }
        }

        $options[$file_name]=$task;

        WPvivid_Setting::update_option('wpvivid_download_task_v2',$options);
    }

    public static function get_download_task_v2($file_name)
    {
        $default = array();
        $options = get_option('wpvivid_download_task_v2', $default);

        if(empty($options))
        {
            return false;
        }
        if(array_key_exists($file_name,$options))
        {
            if(time()-$options[$file_name]['run_time']>60)
            {
                $options[$file_name]['status']='timeout';
                $options[$file_name]['error']='time out';
            }

            return $options[$file_name];
        }
        return false;
    }

    public static function delete_download_task_v2($file_name)
    {
        $default = array();
        $options = get_option('wpvivid_download_task_v2', $default);

        if(empty($options))
        {
            return false;
        }
        if(array_key_exists($file_name,$options))
        {
            unset($options[$file_name]);
            WPvivid_Setting::update_option('wpvivid_download_task_v2',$options);
            return true;
        }
        return false;
    }
}