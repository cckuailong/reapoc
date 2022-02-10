<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}
class WPvivid_Staging_Task
{
    public $task;

    public function __construct($task_id=false)
    {
        if($task_id===false)
        {
            $this->create_new_task();
        }
        else
        {
            $default = array();
            $tasks = get_option('wpvivid_staging_task_list', $default);
            if(isset($tasks[$task_id]))
            {
                $this->task=$tasks[$task_id];
            }
            else
            {
                $error = 'Staging task not found.';
                throw new Exception($error);
            }
        }
    }

    public function get_id()
    {
        return $this->task['id'];
    }

    public function create_new_task()
    {
        $task_id=uniqid('wpvivid-');

        $task['id']=$task_id;

        $task['status']['start_time']=time();
        $task['status']['run_time']=time();
        $task['status']['timeout']=time();
        $task['status']['str']='ready';
        $task['status']['resume_count']=0;
        $task['job']=array();
        $task['doing']=false;
        $task['timeout_limit']=900;
        $task['log_file_name']=$task_id.'_staging';
        $log=new WPvivid_Staging_Log_Free();
        $log->CreateLogFile($task['log_file_name'],'no_folder','staging');
        $log->CloseFile();

        $this->task=$task;

        $this->update_task();
    }

    public function get_log_file_name()
    {
        return $this->task['log_file_name'];
    }

    public function set_time_limit()
    {
        $this->get_task();
        $this->task['status']['timeout']=time();
        if(isset($this->task['options']['staging_max_execution_time'])) {
            $this->task['timeout_limit'] = $this->task['options']['staging_max_execution_time'];
        }
        else{
            $this->task['timeout_limit'] = 900;
        }
        set_time_limit($this->task['timeout_limit']);
        $this->update_task();
    }

    public function update_task($task=false)
    {
        $default = array();
        $tasks = get_option('wpvivid_staging_task_list', $default);

        if($task===false)
        {
            $this->task['status']['run_time']=time();
            $tasks[$this->task['id']]=$this->task;
        }
        else
        {
            $this->task=$task;
            $this->task['status']['run_time']=time();
            $tasks[$this->task['id']]=$this->task;
        }
        update_option('wpvivid_staging_task_list',$tasks);
    }

    public function get_task()
    {
        $default = array();
        $tasks = get_option('wpvivid_staging_task_list', $default);
        $this->task=$tasks[$this->task['id']];
        return $this->task;
    }

    public function get_mu_option()
    {
        if(isset($this->task['mu']))
        {
            return $this->task['mu'];
        }
        else
        {
            return false;
        }
    }

    public function setup_task($option)
    {
        global $wpvivid_plugin;
        $this->task['options']=$option['options'];

        if($option['data']['restore'])
        {
            $this->task['options']['restore']=true;

            $this->task['path']['src_path']=$this->task['site']['path'];
            $this->task['path']['des_path']=untrailingslashit(ABSPATH);

            global $wpdb;
            $this->task['db_connect']['old_prefix']=$this->task['site']['prefix'];
            if($this->get_site_mu_single())
            {
                $this->task['db_connect']['new_prefix']=$wpdb->get_blog_prefix($this->get_site_mu_single_site_id());
            }
            else
            {
                $this->task['db_connect']['new_prefix']=$wpdb->base_prefix;
            }

            $this->task['db_connect']['temp_prefix']=$wpdb->base_prefix.'temp_';
            $this->task['db_connect']['old_site_url']=$this->task['site']['site_url'];
            $this->task['db_connect']['old_home_url']=$this->task['site']['home_url'];

            if($this->get_site_mu_single())
            {
                $this->task['db_connect']['new_site_url']=get_site_url($this->get_site_mu_single_site_id());
                $this->task['db_connect']['new_home_url']=get_home_url($this->get_site_mu_single_site_id());
            }
            else
            {
                $this->task['db_connect']['new_site_url']=untrailingslashit($wpvivid_plugin->staging->get_database_site_url());
                $this->task['db_connect']['new_home_url']=untrailingslashit($wpvivid_plugin->staging->get_database_home_url());
            }

            $this->task['db_connect']['src_use_additional_db']=$this->task['site']['db_connect']['use_additional_db'];

            if($this->task['db_connect']['src_use_additional_db'])
            {
                $this->task['db_connect']['src_dbuser']= $this->task['site']['db_connect']['dbuser'];
                $this->task['db_connect']['src_dbpassword']= $this->task['site']['db_connect']['dbpassword'];
                $this->task['db_connect']['src_dbname']=$this->task['site']['db_connect']['dbname'];
                $this->task['db_connect']['src_dbhost']=$this->task['site']['db_connect']['dbhost'];
            }

            $this->task['db_connect']['des_use_additional_db']=false;
            $this->task['permalink_structure'] = get_option( 'permalink_structure','');

            if(isset($option['data']['mu']))
            {
                $this->task['mu']= $option['data']['mu'];
            }
        }
        else
        {
            $this->task['options']['restore']=false;

            if(isset($option['data']['copy'])&&$option['data']['copy'])
            {
                $this->task['options']['copy']=true;
                $this->task['path']['src_path']=untrailingslashit(ABSPATH);
                $this->task['path']['des_path']=$this->task['site']['path'];

                global $wpdb;
                if($this->get_site_mu_single())
                {
                    $prefix=$wpdb->get_blog_prefix($this->get_site_mu_single_site_id());
                }
                else
                {
                    $prefix=$wpdb->base_prefix;
                }

                $this->task['db_connect']['old_prefix']=$prefix;
                $this->task['db_connect']['new_prefix']=$this->task['site']['prefix'];

                if($this->get_site_mu_single())
                {
                    $this->task['db_connect']['old_site_url'] = get_site_url($this->get_site_mu_single_site_id());
                    $this->task['db_connect']['old_home_url'] = get_home_url($this->get_site_mu_single_site_id());
                }
                else
                {
                    $this->task['db_connect']['old_site_url']=untrailingslashit($wpvivid_plugin->staging->get_database_site_url());
                    $this->task['db_connect']['old_home_url']=untrailingslashit($wpvivid_plugin->staging->get_database_home_url());
                }

                $this->task['db_connect']['new_site_url']=$this->task['site']['site_url'];
                $this->task['db_connect']['new_home_url']=$this->task['site']['home_url'];
                $this->task['db_connect']['src_use_additional_db']=false;
                $this->task['db_connect']['des_use_additional_db']=$this->task['site']['db_connect']['use_additional_db'];
                if($this->task['db_connect']['des_use_additional_db'])
                {
                    $this->task['db_connect']['des_dbuser']= $this->task['site']['db_connect']['dbuser'];
                    $this->task['db_connect']['des_dbpassword']= $this->task['site']['db_connect']['dbpassword'];
                    $this->task['db_connect']['des_dbname']=$this->task['site']['db_connect']['dbname'];
                    $this->task['db_connect']['des_dbhost']=$this->task['site']['db_connect']['dbhost'];
                }
                if(isset($option['data']['mu']))
                {
                    $this->task['mu']= $option['data']['mu'];
                }
            }
            else
            {
                $this->task['options']['copy']=false;
                if(isset($option['data']['db_connect']))
                    $this->task['db_connect']=$option['data']['db_connect'];

                $this->task['path']=$option['data']['path'];

                if(isset($option['data']['mu']))
                {
                    $this->task['mu']= $option['data']['mu'];
                }
            }
            $this->task['permalink_structure'] = get_option( 'permalink_structure','');
            $this->task['login_url'] = wp_login_url();
        }

        if(isset($option['data']['core']))
        {
            $this->task['job']['core']['exclude_files_regex']='#\.htaccess#';
            $this->task['job']['core']['finished']=0;
            $this->task['job']['core']['start']=0;
            $this->task['job']['core']['type']='file';
        }
        if(isset($option['data']['wp-content']))
        {
            $this->task['job']['wp-content']['exclude_regex']=$option['data']['wp-content']['exclude_regex'];
            $this->task['job']['wp-content']['exclude_files_regex']=$option['data']['wp-content']['exclude_files_regex'];
            $this->task['job']['wp-content']['finished']=0;
            $this->task['job']['wp-content']['start']=0;
            $this->task['job']['wp-content']['type']='file';
        }
        if(isset($option['data']['plugins']))
        {
            $this->task['job']['plugins']['exclude_regex']=$option['data']['plugins']['exclude_regex'];
            $this->task['job']['plugins']['finished']=0;
            $this->task['job']['plugins']['start']=0;
            $this->task['job']['plugins']['type']='file';
        }
        if(isset($option['data']['theme']))
        {
            $this->task['job']['theme']['exclude_regex']=$option['data']['theme']['exclude_regex'];
            $this->task['job']['theme']['finished']=0;
            $this->task['job']['theme']['start']=0;
            $this->task['job']['theme']['type']='file';
        }
        if(isset($option['data']['upload']))
        {
            if(isset($option['data']['upload']['include_regex']))
                $this->task['job']['upload']['include_regex']=$option['data']['upload']['include_regex'];
            $this->task['job']['upload']['exclude_regex']=$option['data']['upload']['exclude_regex'];
            $this->task['job']['upload']['exclude_files_regex']=$option['data']['upload']['exclude_files_regex'];
            $this->task['job']['upload']['finished']=0;
            $this->task['job']['upload']['start']=0;
            $this->task['job']['upload']['type']='file';
        }

        if(isset($option['data']['custom']))
        {
            foreach ($option['data']['custom'] as $custom)
            {
                $this->task['job'][$custom['root']]['root']=$custom['root'];
                $this->task['job'][$custom['root']]['exclude_regex']=$custom['exclude_regex'];
                $this->task['job'][$custom['root']]['exclude_files_regex']=$custom['exclude_files_regex'];
                $this->task['job'][$custom['root']]['finished']=0;
                $this->task['job'][$custom['root']]['start']=0;
                $this->task['job'][$custom['root']]['type']='file';
            }
        }

        if(isset($option['data']['db']))
        {
            $this->task['job']['db']['exclude_tables']=$option['data']['db']['exclude_tables'];
            $this->task['job']['db']['finished']=0;
            $this->task['job']['db']['tables']=array();
            $this->task['job']['db']['type']='db';

            $this->task['job']['db_replace']['exclude_tables']=$option['data']['db']['exclude_tables'];
            $this->task['job']['db_replace']['finished']=0;
            $this->task['job']['db_replace']['tables']=array();
            $this->task['job']['db_replace']['type']='db_replace';

            if($option['data']['restore'])
            {
                $this->task['job']['db_rename']['exclude_tables']=$option['data']['db']['exclude_tables'];
                $this->task['job']['db_rename']['finished']=0;
                $this->task['job']['db_rename']['tables']=array();
                $this->task['job']['db_rename']['type']='db_rename';
            }

        }

        if(isset($option['data']['mu_single']))
        {
            $this->task['options']['mu_single']=true;
            $this->task['options']['mu_single_upload']=$option['data']['mu_single_upload'];
            $this->task['options']['mu_single_site_id']=$option['data']['mu_single_site_id'];
        }

        if(isset($option['data']['create_new_wp']))
        {
            $this->task['options']['fresh_install']=true;
            $this->task['job']['create_new_wp']['type']='install_wordpress';
            $this->task['job']['create_new_wp']['finished']=0;
        }

        $this->update_task();
    }

    public function update_action_time($action_type)
    {
        $this->get_task();
        $this->task[$action_type]=time();
        $this->update_task();
    }

    public function is_mu_single()
    {
        if(isset($this->task['options']['mu_single']))
            return true;
        else
            return false;
    }

    public function get_mu_single_upload()
    {
        if(isset($this->task['options']['mu_single_upload']))
            return $this->task['options']['mu_single_upload'];
        else
            return false;
    }

    public function get_mu_single_site_id()
    {
        if(isset($this->task['options']['mu_single_site_id']))
            return $this->task['options']['mu_single_site_id'];
        else
            return false;
    }

    public function get_permalink_structure(){
        $this->get_task();
        return $this->task['permalink_structure'];
    }

    public function get_is_overwrite_permalink_structure(){
        $this->get_task();
        return $this->task['options']['staging_overwrite_permalink'];
    }

    public function is_tables_exclude($table,$prefix=false)
    {
        if(isset($this->task['job']['db'])&&isset($this->task['job']['db']['exclude_tables']))
        {
            $arr=$this->task['job']['db']['exclude_tables'];

            if(empty($arr))
                return false;

            if($prefix===false)
            {
                return in_array($table, $arr);
            }
            else
            {
                $og_table=substr($table, strlen($prefix));
                $old_table=$this->get_db_prefix().$og_table;
                return in_array($old_table, $arr);
            }
        }
        else
        {
            return false;
        }
    }

    public function get_doing_task()
    {
        if($this->get_status()=='error')
            return false;

        $this->get_task();
        $doing=$this->task['doing'];

        if(isset($this->task['job'][$doing]))
        {
            if($this->task['job'][$doing]['finished'])
            {
                $this->task['doing']=false;
                $this->update_task();
                return $this->task['doing'];
            }
            else
            {
                return $doing;
            }
        }
        else
        {
            return false;
        }
    }

    public function get_start_next_task()
    {
        if($this->get_status()=='error')
            return false;

        $this->get_task();
        foreach ($this->task['job'] as $key=>$job)
        {
            if($job['finished'])
                continue;
            return $key;
        }
        return false;
    }

    public function do_task($key)
    {
        global $wpvivid_plugin;
        $this->get_task();
        $wpvivid_plugin->staging->log->WriteLog('Start processing '.$key.'.','notice');
        if($key==false)
            return true;

        $cancel_status = get_option('wpvivid_staging_task_cancel', false);
        //if($this->task['status']['str']=='cancel')
        if($cancel_status)
        {
            return false;
        }

        $job=$this->task['job'][$key];
        $this->task['doing']=$key;
        $this->task['status']['str']='running';
        $this->update_task();
        $this->flush();
        if($job['type']=='file')
        {
            $wpvivid_plugin->staging->log->WriteLog('Prepare to copy '.$key.' files.','notice');
            $task_id=$this->task['id'];
            $file=new WPvivid_Staging_Copy_Files($task_id);
            return $file->do_copy_file($key);
        }
        else if($job['type']=='db')
        {
            $wpvivid_plugin->staging->log->WriteLog('Prepare to copy database.','notice');
            $task_id=$this->task['id'];
            $file=new WPvivid_Staging_Copy_DB($task_id);
            return $file->do_copy_db();
        }
        else if($job['type']=='db_replace')
        {
            $wpvivid_plugin->staging->log->WriteLog('Prepare to replace database.','notice');
            $task_id=$this->task['id'];
            $file=new WPvivid_Staging_Copy_DB($task_id);
            return $file->do_replace_db();
        }
        else if($job['type']=='db_rename')
        {
            $wpvivid_plugin->staging->log->WriteLog('Prepare to rename tables.','notice');
            $task_id=$this->task['id'];
            $file=new WPvivid_Staging_Copy_DB($task_id);
            return $file->do_rename_db();
        }
        else if($job['type']=='install_wordpress')
        {
            $wpvivid_plugin->staging->log->WriteLog('Prepare to install wordpress.','notice');
            $task_id=$this->task['id'];
            $install=new WPvivid_Staging_Install_Wordpress_Free($task_id);
            return $install->do_install_wordpress();
        }
        return false;
    }

    private function flush()
    {
        $ret['result']='success';
        $ret['task_id']=$this->task['id'];
        $json=json_encode($ret);
        if(!headers_sent())
        {
            header('Content-Length: '.strlen($json));
            header('Connection: close');
            header('Content-Encoding: none');
        }

        if (session_id())
            session_write_close();
        echo $json;

        if(function_exists('fastcgi_finish_request'))
        {
            fastcgi_finish_request();
        }
        else
        {
            ob_flush();
            flush();
        }
    }

    public function get_start($key)
    {
        $this->get_task();

        if($key=='db'||$key=='db_replace'||$key=='db_rename')
        {
            foreach ($this->task['job'][$key]['tables'] as $table)
            {
                if($table['finished']==0)
                {
                    return $table;
                }
            }
            return false;
        }
        else
        {
            return $this->task['job'][$key]['start'];
        }
    }

    public function update_start($key,$start)
    {
        $this->get_task();
        $this->task['job'][$key]['start']=$start;
        $this->update_task();
    }

    public function get_path($des=true)
    {
        $this->get_task();
        if($des)
        {
            return $this->task['path']['des_path'];
        }
        else
        {
            return $this->task['path']['src_path'];
        }
    }

    public function get_db_connect()
    {
        $this->get_task();
        return $this->task['db_connect'];
    }

    public function get_db_prefix($new=false)
    {
        $this->get_task();
        if($new)
        {
            return $this->task['db_connect']['new_prefix'];
        }
        else
        {
            return $this->task['db_connect']['old_prefix'];
        }
    }

    public function get_temp_prefix()
    {
        $this->get_task();
        if($this->is_restore())
            return $this->task['db_connect']['temp_prefix'];
        else
            return $this->task['db_connect']['new_prefix'];
    }

    public function get_site_url($new=false)
    {
        $this->get_task();
        if($new)
        {
            return $this->task['db_connect']['new_site_url'];
        }
        else
        {
            return $this->task['db_connect']['old_site_url'];
        }
    }

    public function get_home_url($new=false)
    {
        $this->get_task();
        if($new)
        {
            return $this->task['db_connect']['new_home_url'];
        }
        else
        {
            return $this->task['db_connect']['old_home_url'];
        }
    }

    public function get_job_option($key,$option_name)
    {
        $this->get_task();
        if(isset($this->task['job'][$key])&&isset($this->task['job'][$key][$option_name]))
        {
            return $this->task['job'][$key][$option_name];
        }
        else
        {
            return false;
        }
    }

    public function update_job_finished($key)
    {
        $this->get_task();
        $this->task['job'][$key]['finished']=1;
        $this->task['status']['str']='ready';
        $this->task['status']['resume_count']=0;
        $this->task['doing']=false;
        $this->update_task();
    }

    public function get_tables($key)
    {
        $this->get_task();
        return $this->task['job'][$key]['tables'];
    }

    public function update_tables($key,$tables)
    {
        $this->get_task();
        $this->task['job'][$key]['tables']=$tables;
        $this->update_task();
    }

    public function update_table($key,$table)
    {
        $this->get_task();
        $this->task['job'][$key]['tables'][$table['name']]=$table;
        $this->update_task();
    }

    public function update_table_finished($key,$table)
    {
        $this->get_task();
        $this->task['job'][$key]['tables'][$table['name']]=$table;
        $this->task['status']['str']='ready';
        $this->update_task();
    }

    public function finished_task()
    {
        $this->get_task();
        $default = array();
        $tasks = get_option('wpvivid_staging_task_list', $default);
        $this->task['status']['run_time']=time();
        $this->task['status']['str']='completed';
        if($this->is_restore()|| $this->task['options']['copy']==true)
        {
        }
        else {
            if(isset($this->task['options']['fresh_install']))
            {
                $this->task['site']['fresh_install']=true;
            }
            if(isset($this->task['options']['mu_single']))
            {
                $this->task['site']['mu_single']=true;
                $this->task['site']['mu_single_site_id']=$this->task['options']['mu_single_site_id'];
            }
            $this->task['site']['path']=$this->task['path']['des_path'];
            $this->task['site']['site_url']=$this->task['db_connect']['new_site_url'];
            $this->task['site']['home_url']=$this->task['db_connect']['new_home_url'];
            $this->task['site']['prefix']=$this->task['db_connect']['new_prefix'];

            $this->task['site']['db_connect']['use_additional_db']=$this->task['db_connect']['des_use_additional_db'];

            if($this->task['site']['db_connect']['use_additional_db'])
            {
                $this->task['site']['db_connect']['dbuser']=$this->task['db_connect']['des_dbuser'];
                $this->task['site']['db_connect']['dbpassword']=$this->task['db_connect']['des_dbpassword'];
                $this->task['site']['db_connect']['dbname']=$this->task['db_connect']['des_dbname'];
                $this->task['site']['db_connect']['dbhost']=$this->task['db_connect']['des_dbhost'];
            }

            if(isset($this->task['mu'])&&is_multisite())
            {
                $this->task['site']['path_current_site']=$this->task['mu']['path_current_site'];
                $this->task['site']['main_site_id']=$this->task['mu']['main_site_id'];
            }
        }


        $this->task['job']=array();
        $this->task['doing']=false;
        $tasks[$this->task['id']]=$this->task;
        update_option('wpvivid_staging_task_list',$tasks);
    }

    public function get_site_mu_single()
    {
        if(isset($this->task['site']['mu_single']))
            return $this->task['site']['mu_single'];
        else
            return false;
    }

    public function get_site_mu_single_site_id()
    {
        if(isset($this->task['site']['mu_single']))
            return $this->task['site']['mu_single_site_id'];
        else
            return false;
    }

    public function get_site_path()
    {
        if(isset($this->task['site']))
            return $this->task['site']['path'];
        else
            return false;
    }

    public function get_site_db_connect()
    {
        if(isset($this->task['site']))
            return $this->task['site']['db_connect'];
        else
            return false;
    }

    public function get_site_db_instance()
    {
        if(isset($this->task['site']))
        {
            $db=$this->get_site_db_connect();
            if($db['use_additional_db']===false)
            {
                global $wpdb;
                return $wpdb;
            }
            else {
                return new wpdb($db['dbuser'],$db['dbpassword'],$db['dbname'],$db['dbhost']);
            }
        }
        else
        {
            return false;
        }
    }

    public function get_site_prefix()
    {
        if(isset($this->task['site']))
            return $this->task['site']['prefix'];
        else
            return false;
    }

    public function finished_task_with_error($error='')
    {
        global $wpvivid_plugin;
        $this->get_task();
        if(empty($error))
        {
            $cancel_status = get_option('wpvivid_staging_task_cancel', false);
            //if($this->task['status']['str']=='cancel')
            if($cancel_status)
            {
                $default = array();
                $tasks = get_option('wpvivid_staging_task_list', $default);
                $this->task['status']['run_time']=time();
                $this->task['status']['str']='error';
                $this->task['status']['error']='task canceled';
                $tasks[$this->task['id']]=$this->task;
                update_option('wpvivid_staging_task_list',$tasks);
            }
            else
            {
                $default = array();
                $error = $this->get_error();
                $tasks = get_option('wpvivid_staging_task_list', $default);
                $this->task['status']['run_time']=time();
                $this->task['status']['str']='error';
                $this->task['status']['error']=$error;
                $tasks[$this->task['id']]=$this->task;
                update_option('wpvivid_staging_task_list',$tasks);
                $wpvivid_plugin->staging->log->WriteLog('Error: '.$this->task['status']['error'],'error');
                WPvivid_Staging_error_log_free::create_error_log($wpvivid_plugin->staging->log->log_file);
            }
        }
        else
        {
            $default = array();
            $tasks = get_option('wpvivid_staging_task_list', $default);
            $this->task['status']['run_time']=time();
            $this->task['status']['str']='error';
            $this->task['status']['error']=$error;
            $tasks[$this->task['id']]=$this->task;
            update_option('wpvivid_staging_task_list',$tasks);
            $wpvivid_plugin->staging->log->WriteLog('Error: '.$this->task['status']['error'],'error');
            WPvivid_Staging_error_log_free::create_error_log($wpvivid_plugin->staging->log->log_file);
        }
    }

    public function set_error($error)
    {
        $this->get_task();
        $this->task['status']['str']='error';
        $this->task['status']['error']=$error;
        $this->update_task();
    }

    public function get_error()
    {
        $this->get_task();
        if($this->task['status']['str']=='error')
        {
            return $this->task['status']['error'];
        }
        else
        {
            return '';
        }
    }

    public function get_status()
    {
        $this->get_task();
        $cancel_status = get_option('wpvivid_staging_task_cancel', false);
        if($cancel_status)
        {
            return 'cancel';
        }
        else {
            return $this->task['status']['str'];
        }
    }

    public function check_timeout()
    {
        $this->get_task();

        $time_spend = time() - $this->task['status']['timeout'];
        $limit=$this->task['timeout_limit'];
        $max_resume_count=$this->task['options']['staging_resume_count'];

        if ($time_spend >= $limit)
        {
            $this->task['status']['resume_count']++;

            global $wpvivid_plugin;
            $wpvivid_plugin->staging->log->OpenLogFile($this->get_log_file_name());
            $wpvivid_plugin->staging->log->WriteLog('Task time out. Resumption times: '.$this->task['status']['resume_count'],'notice');

            if($this->task['status']['resume_count']>$max_resume_count)
            {
                $wpvivid_plugin->staging->log->WriteLog('Task time out.','error');
                $this->task['status']['str']='error';
                $this->task['status']['error']='task time out.';
            }
            else
            {
                $this->task['status']['str']='ready';
            }

            $this->update_task();
            return true;
        }
        else
        {
            $no_response_time=time()-$this->task['status']['run_time'];
            if($no_response_time>180)
            {
                $next_timeout_time = $limit-$time_spend;
                global $wpvivid_plugin;
                $wpvivid_plugin->staging->log->OpenLogFile($this->get_log_file_name());
                $wpvivid_plugin->staging->log->WriteLog('Task is not responding and will time out in '.$next_timeout_time,'notice');
                $this->task['status']['str']='no_reponse';
                $this->update_task();
            }
        }

        return false;
    }

    public function set_memory_limit()
    {
        if(isset($this->task['options']['staging_memory_limit']))
            $memory_limit=$this->task['options']['staging_memory_limit'];
        else
            $memory_limit='256M';
        @ini_set('memory_limit', $memory_limit);
    }

    public function get_exclude_file_size()
    {
        if(isset($this->task['options']['staging_exclude_file_size'])) {
            $exclude_file_size = $this->task['options']['staging_exclude_file_size'];
        }
        else {
            $exclude_file_size = 30;
        }
        return $exclude_file_size;
    }

    public function get_files_copy_count()
    {
        if(isset($this->task['options']['staging_file_copy_count']))
            $files_copy_count=$this->task['options']['staging_file_copy_count'];
        else
            $files_copy_count=500;
        return $files_copy_count;
    }

    public function get_db_insert_count()
    {
        if(isset($this->task['options']['staging_db_insert_count']))
            $db_insert_count=$this->task['options']['staging_db_insert_count'];
        else
            $db_insert_count=10000;
        return $db_insert_count;
    }

    public function get_db_replace_count()
    {
        if(isset($this->task['options']['staging_db_replace_count']))
            $db_replace_count=$this->task['options']['staging_db_replace_count'];
        else
            $db_replace_count=5000;
        return $db_replace_count;
    }

    public function is_restore()
    {
        return $this->task['options']['restore'];
    }

    public function is_copy()
    {
        return $this->task['options']['copy'];
    }

    public function cancel_staging()
    {
        $this->get_task();
        //$default = array();
        //$tasks = get_option('wpvivid_staging_task_list', $default);
        if( $this->task['status']['str']=='running' || $this->task['status']['str']=='ready' )
        {
            //$this->task['status']['str']='cancel';
            update_option('wpvivid_staging_task_cancel', true);
        }

        //$tasks[$this->task['id']]=$this->task;
        //update_option('wpvivid_staging_task_list',$tasks);
    }

    public function update_calc_db_size($key)
    {
        $this->get_task();
        $size = 0;
        if($key=='db'||$key=='db_replace'||$key=='db_rename')
        {
            $size = count($this->task['job'][$key]['tables']);
        }
        $this->task['job'][$key]['copy_size']=$size;
        $this->update_task();
    }

    public function update_calc_db_finish_size($key)
    {
        $this->get_task();
        if($key=='db'||$key=='db_replace'||$key=='db_rename')
        {
            if(!isset($this->task['job'][$key]['finish_size'])){
                $this->task['job'][$key]['finish_size'] = 1;
            }
            else{
                $this->task['job'][$key]['finish_size']++;
            }
        }
        $this->update_task();
    }

    public function get_progress()
    {
        $job_count=sizeof($this->task['job']);

        if($job_count>0)
        {
            $job_finished=0;
            foreach ($this->task['job'] as $job)
            {
                if($job['type']=='db'||$job['type']=='db_replace'||$job['type']=='db_rename'){
                    if(isset($this->task['job'][$job['type']]['finish_size']) && $this->task['job'][$job['type']]['finish_size'] != 0 &&
                        isset($this->task['job'][$job['type']]['copy_size']) && $this->task['job'][$job['type']]['copy_size'] != 0) {
                        $percent_db = floatval($this->task['job'][$job['type']]['finish_size'] / $this->task['job'][$job['type']]['copy_size']);
                        $job_finished = floatval($job_finished + $percent_db);
                    }
                }
                else if($job['finished'])
                {
                    $job_finished++;
                }
            }
            $progress=intval(($job_finished/$job_count)*100);
            if($progress == 0){
                $progress = 5;
            }
            return $progress;
        }
        else
        {
            return 100;
        }
    }

    public function get_mu_sites($args=array())
    {
        global $wpdb;
        $db=$this->get_site_db_connect();

        if($db['use_additional_db']===false)
        {
            $old_prefix=$wpdb->base_prefix;
            $wpdb->set_prefix($this->get_site_prefix());
            $subsites=get_sites($args);
            $wpdb->set_prefix($old_prefix);

        }
        else
        {
            $old_wpdb=$wpdb;
            $wpdb=new wpdb($db['dbuser'],$db['dbpassword'],$db['dbname'],$db['dbhost']);
            $wpdb->set_prefix($this->get_site_prefix());
            $subsites=get_sites($args);
            $wpdb=$old_wpdb;
        }

        /*
        if($db['use_additional_db']===false)
        {
            $db_instance=$wpdb;
        }
        else
        {
            $db_instance=new wpdb($db['dbuser'],$db['dbpassword'],$db['dbname'],$db['dbhost']);
        }
        $sql='SELECT * FROM '.$this->get_site_prefix().'blogs';
        $subsites=$db_instance->get_results($sql,OBJECT_K);
        */

        return $subsites;
    }

    public function get_mu_path_current_site()
    {
        if(isset($this->task['site']['path_current_site']))
        {
            return $this->task['site']['path_current_site'];
        }
        else
        {
            return false;
        }
    }

    public function get_mu_main_site_id()
    {
        if(isset($this->task['site']['main_site_id']))
        {
            return $this->task['site']['main_site_id'];
        }
        else
        {
            return false;
        }
    }

    public function set_push_staging_history($option)
    {
        global $wpdb;
        $site_prefix=$this->get_site_prefix();
        foreach ($option['database_list'] as $index => $table)
        {
            $option['database_list'][$index] = str_replace($site_prefix, $wpdb->base_prefix, $table);
        }
        $this->task['push_staging_history'] = $option;
    }

    public function get_push_staging_history()
    {
        $option = $this->task['push_staging_history'];
        return $option;
    }
}