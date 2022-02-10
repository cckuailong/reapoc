<?php

if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

define('WPVIVID_BACKUP_TYPE_DB','backup_db');
define('WPVIVID_BACKUP_TYPE_THEMES','backup_themes');
define('WPVIVID_BACKUP_TYPE_PLUGIN','backup_plugin');
define('WPVIVID_BACKUP_TYPE_UPLOADS','backup_uploads');
define('WPVIVID_BACKUP_TYPE_UPLOADS_FILES','backup_uploads_files');
//define('WPVIVID_BACKUP_TYPE_UPLOADS_FILES_OTHER','backup_uploads_files_other');
define('WPVIVID_BACKUP_TYPE_CONTENT','backup_content');
define('WPVIVID_BACKUP_TYPE_CORE','backup_core');
define('WPVIVID_BACKUP_TYPE_OTHERS','backup_others');
define('WPVIVID_BACKUP_TYPE_MERGE','backup_merge');

define('WPVIVID_BACKUP_ROOT_WP_CONTENT','wp-content');
define('WPVIVID_BACKUP_ROOT_CUSTOM','custom');
define('WPVIVID_BACKUP_ROOT_WP_ROOT','root');
define('WPVIVID_BACKUP_ROOT_WP_UPLOADS','uploads');
class WPvivid_Backup_Task
{
    protected $task;

    public $backup_type_collect;

    public function __construct($task_id=false,$task=false)
    {
        if($task_id!==false)
        {
            $this->task=WPvivid_taskmanager::get_task($task_id);
        }

        if($task!==false)
        {
            $this->task=$task;
        }

        $this->backup_type_collect[WPVIVID_BACKUP_TYPE_DB]=1;
        $this->backup_type_collect[WPVIVID_BACKUP_TYPE_THEMES]=1;
        $this->backup_type_collect[WPVIVID_BACKUP_TYPE_PLUGIN]=1;
        $this->backup_type_collect[WPVIVID_BACKUP_TYPE_UPLOADS]=1;
        $this->backup_type_collect[WPVIVID_BACKUP_TYPE_UPLOADS_FILES]=1;
        //$this->backup_type_collect[WPVIVID_BACKUP_TYPE_UPLOADS_FILES_OTHER]=1;
        $this->backup_type_collect[WPVIVID_BACKUP_TYPE_CONTENT]=1;
        $this->backup_type_collect[WPVIVID_BACKUP_TYPE_CORE]=1;
        $this->backup_type_collect[WPVIVID_BACKUP_TYPE_OTHERS]=1;
        $this->backup_type_collect[WPVIVID_BACKUP_TYPE_MERGE]=1;

        add_filter('wpvivid_set_backup', array($this, 'wpvivid_set_backup'),10);
        add_filter('wpvivid_exclude_plugins',array($this,'exclude_plugins'),20);
        add_filter('wpvivid_get_backup_exclude_regex',array($this, 'get_backup_exclude_regex'),10,2);
    }

    public function get_backup_exclude_regex($exclude_regex,$backup_type)
    {
        if($backup_type==WPVIVID_BACKUP_TYPE_UPLOADS||$backup_type==WPVIVID_BACKUP_TYPE_UPLOADS_FILES)
        {
            $upload_dir = wp_upload_dir();
            $backup_data['files_root']=$this -> transfer_path($upload_dir['basedir']);
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($upload_dir['basedir']).DIRECTORY_SEPARATOR.'backwpup', '/').'#';  // BackWPup backup directory
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($upload_dir['basedir']).DIRECTORY_SEPARATOR.'ShortpixelBackups', '/').'#';//ShortpixelBackups
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($upload_dir['basedir']).DIRECTORY_SEPARATOR.'backup', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($upload_dir['basedir']).DIRECTORY_SEPARATOR.'backwpup', '/').'#';  // BackWPup backup directory
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($upload_dir['basedir']).DIRECTORY_SEPARATOR.'backup-guard', '/').'#';  // Wordpress Backup and Migrate Plugin backup directory
        }
        else if($backup_type==WPVIVID_BACKUP_TYPE_CONTENT)
        {
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.'updraft', '/').'#';   // Updraft Plus backup directory
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.'ai1wm-backups', '/').'#'; // All-in-one WP migration backup directory
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.'backups', '/').'#'; // Xcloner backup directory
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.'upgrade', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.'wpvivid', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir(), '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.'plugins', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.'cache', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.'wphb-cache', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.'backup', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.'Dropbox_Backup', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.'mysql.sql', '/').'#';//mysql
            if(defined('WPVIVID_UPLOADS_ISO_DIR'))
            {
                $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR).DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR, '/').'#';
            }
            $upload_dir = wp_upload_dir();
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($upload_dir['basedir']), '/').'$#';
            $exclude_regex[]='#^'.preg_quote($this->transfer_path(get_theme_root()), '/').'#';
        }

        return $exclude_regex;
    }

    public function exclude_plugins($exclude_plugins)
    {
        if(in_array('wpvivid-backuprestore',$exclude_plugins))
        {
            $exclude_plugins[]='wpvivid-backuprestore';
        }

        if(in_array('wp-cerber',$exclude_plugins))
        {
            $exclude_plugins[]='wp-cerber';
        }

        if(in_array('.',$exclude_plugins))
        {
            $exclude_plugins[]='.';
        }

        if(in_array('wpvivid-backup-pro',$exclude_plugins))
        {
            $exclude_plugins[]='wpvivid-backup-pro';
        }

        return $exclude_plugins;
    }

    public function get_id()
    {
        return $this->task['id'];
    }

    public function new_backup_task($options,$type,$action='backup')
    {
        $id=uniqid('wpvivid-');
        $this->task=false;
        $this->task['id']=$id;
        $this->task['action']=$action;
        $this->task['type']=$type;

        $this->task['status']['start_time']=time();
        $this->task['status']['run_time']=time();
        $this->task['status']['timeout']=time();
        $this->task['status']['str']='ready';
        $this->task['status']['resume_count']=0;

        if(isset($options['remote']))
        {
            if($options['remote']=='1')
            {
                if(isset($options['remote_options']))
                {
                    $this->task['options']['remote_options']=$options['remote_options'];
                }
                else
                {
                    $this->task['options']['remote_options']=WPvivid_Setting::get_remote_options();
                }

            }
            else {
                $this->task['options']['remote_options']=false;
            }
        }
        else
        {
            $this->task['options']['remote_options']=false;
        }

        $this->task['options']['remote_options'] = apply_filters('wpvivid_set_remote_options', $this->task['options']['remote_options'],$options);

        if(isset($options['local']))
        {
            if($options['local']=='1')
            {
                $this->task['options']['save_local']=1;
            }
            else
            {
                $this->task['options']['save_local']=0;
            }
        }
        else
        {
            $this->task['options']['save_local']=1;
        }

        if(isset($options['lock']))
        {
            $this->task['options']['lock']=$options['lock'];
        }
        else
        {
            $this->task['options']['lock']=0;
        }

        $general_setting=WPvivid_Setting::get_setting(true, "");

        if(isset($options['backup_prefix']) && !empty($options['backup_prefix']))
        {
            $backup_prefix=$options['backup_prefix'];
        }
        else
        {
            if(isset($general_setting['options']['wpvivid_common_setting']['domain_include'])&&$general_setting['options']['wpvivid_common_setting']['domain_include'])
            {
                $check_addon = apply_filters('wpvivid_check_setting_addon', 'not_addon');
                if (isset($general_setting['options']['wpvivid_common_setting']['backup_prefix']) && $check_addon == 'addon')
                {
                    $backup_prefix = $general_setting['options']['wpvivid_common_setting']['backup_prefix'];
                }
                else {
                    $home_url_prefix = get_home_url();
                    $home_url_prefix = $this->parse_url_all($home_url_prefix);
                    $backup_prefix = $home_url_prefix;
                }
            }
            else
            {
                $backup_prefix='';
            }
        }
        $this->task['options']['backup_prefix']=$backup_prefix;
        $offset=get_option('gmt_offset');
        if(empty($backup_prefix))
            $this->task['options']['file_prefix'] = $this->task['id'] . '_' . date('Y-m-d-H-i', $this->task['status']['start_time']+$offset*60*60);
        else
            $this->task['options']['file_prefix'] = $backup_prefix . '_' . $this->task['id'] . '_' . date('Y-m-d-H-i', $this->task['status']['start_time']+$offset*60*60);

        $this->task['options']['file_prefix'] = apply_filters('wpvivid_backup_file_prefix',$this->task['options']['file_prefix'],$backup_prefix,$this->task['id'],$this->task['status']['start_time']);

        if(isset($general_setting['options']['wpvivid_common_setting']['ismerge']))
        {
            if($general_setting['options']['wpvivid_common_setting']['ismerge']==1)
            {
                $this->task['options']['backup_options']['ismerge']=1;
            }
            else {
                $this->task['options']['backup_options']['ismerge']=0;
            }
        }
        else {
            $this->task['options']['backup_options']['ismerge']=1;
        }
        $this->task['options']['backup_options']['ismerge']=apply_filters('wpvivid_set_backup_ismerge',$this->task['options']['backup_options']['ismerge'],$options);

        $this->task['options']['log_file_name']=$id.'_backup';
        $log=new WPvivid_Log();
        $log->CreateLogFile($this->task['options']['log_file_name'],'no_folder','backup');
        //$log->WriteLog(get_home_path(),'test');
        $this->task['options']['backup_options']['prefix']=$this->task['options']['file_prefix'];
        $this->task['options']['backup_options']['compress']=WPvivid_Setting::get_option('wpvivid_compress_setting');
        $this->task['options']['backup_options']['dir']=WPvivid_Setting::get_backupdir();
        $this->task['options']['backup_options']['backup']=array();

        if(isset($options['backup_files']))
        {
            if($options['backup_files']=='files+db')
            {
                $this->set_backup(WPVIVID_BACKUP_TYPE_DB);
                $this->set_backup(WPVIVID_BACKUP_TYPE_THEMES);
                $this->set_backup(WPVIVID_BACKUP_TYPE_PLUGIN);
                $general_setting=WPvivid_Setting::get_setting(true, "");
                if(isset($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload']) && !empty($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload'])){
                    if($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload']){
                        $this->set_backup(WPVIVID_BACKUP_TYPE_UPLOADS_FILES);
                        //$this->set_backup(WPVIVID_BACKUP_TYPE_UPLOADS_FILES_OTHER);
                    }
                    else{
                        $this->set_backup(WPVIVID_BACKUP_TYPE_UPLOADS);
                    }
                }
                else{
                    $this->set_backup(WPVIVID_BACKUP_TYPE_UPLOADS);
                }
                $this->set_backup(WPVIVID_BACKUP_TYPE_CONTENT);
                $this->set_backup(WPVIVID_BACKUP_TYPE_CORE);
            }
            else if($options['backup_files']=='files')
            {
                $this->set_backup(WPVIVID_BACKUP_TYPE_THEMES);
                $this->set_backup(WPVIVID_BACKUP_TYPE_PLUGIN);
                $general_setting=WPvivid_Setting::get_setting(true, "");
                if(isset($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload']) && !empty($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload'])){
                    if($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload']){
                        $this->set_backup(WPVIVID_BACKUP_TYPE_UPLOADS_FILES);
                        //$this->set_backup(WPVIVID_BACKUP_TYPE_UPLOADS_FILES_OTHER);
                    }
                    else{
                        $this->set_backup(WPVIVID_BACKUP_TYPE_UPLOADS);
                    }
                }
                else{
                    $this->set_backup(WPVIVID_BACKUP_TYPE_UPLOADS);
                }
                $this->set_backup(WPVIVID_BACKUP_TYPE_CONTENT);
                $this->set_backup(WPVIVID_BACKUP_TYPE_CORE);
            }
            else if($options['backup_files']=='db')
            {
                $this->set_backup(WPVIVID_BACKUP_TYPE_DB);
            }
        }
        else
        {
            $this->task['options']['backup_options'] = apply_filters('wpvivid_set_backup_type', $this->task['options']['backup_options'],$options);
        }

        $this->task['data']['doing']='backup';
        $this->task['data']['backup']['doing']='';
        $this->task['data']['backup']['finished']=0;
        $this->task['data']['backup']['progress']=0;
        $this->task['data']['backup']['job_data']=array();
        $this->task['data']['backup']['sub_job']=array();
        $this->task['data']['backup']['db_size']='0';
        $this->task['data']['backup']['files_size']['sum']='0';
        $this->task['data']['upload']['doing']='';
        $this->task['data']['upload']['finished']=0;
        $this->task['data']['upload']['progress']=0;
        $this->task['data']['upload']['job_data']=array();
        $this->task['data']['upload']['sub_job']=array();
        WPvivid_Setting::update_task($id,$this->task);
        $ret['result']='success';
        $ret['task_id']=$this->task['id'];
        $log->CloseFile();
        return $ret;
    }

    protected function parse_url_all($url)
    {
        $parse = parse_url($url);
        //$path=str_replace('/','_',$parse['path']);
        $path = '';
        if(isset($parse['path'])) {
            $parse['path'] = str_replace('/', '_', $parse['path']);
            $path = $parse['path'];
        }
        return $parse['host'].$path;
    }

    public function set_backup($backup, $task_type='')
    {
        if(is_string($backup))
        {
            if(!function_exists('get_home_path'))
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            $backup_data['key']=$backup;
            $backup_data['result']=false;
            $backup_data['compress']=$this->task['options']['backup_options']['compress'];
            $backup_data['finished']=0;
            $backup_data['path']=WP_CONTENT_DIR.DIRECTORY_SEPARATOR. $this->task['options']['backup_options']['dir'].DIRECTORY_SEPARATOR;
            if($backup==WPVIVID_BACKUP_TYPE_DB)
            {
                //$backup_data['root_path']=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$this->task['options']['backup_options']['dir'];
                $backup_data['root_flag']=WPVIVID_BACKUP_ROOT_CUSTOM;
                $backup_data['dump_db']=1;
                $backup_data['sql_file_name']=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$this->task['options']['backup_options']['dir'].DIRECTORY_SEPARATOR.$this->get_prefix().'_backup_db.sql';
                $backup_data['json_info']['dump_db']=1;
                $backup_data['json_info']['home_url']=home_url();
                $backup_data['json_info']['file_type']='databases';
                if(is_multisite())
                {
                    $backup_data['json_info']['is_mu']=1;
                }
                $backup_data['prefix']=$this->get_prefix().'_backup_db';
            }
            else if($backup==WPVIVID_BACKUP_TYPE_THEMES)
            {
                //$backup_data['root_path']=WP_CONTENT_DIR;
                $backup_data['root_flag']=WPVIVID_BACKUP_ROOT_WP_CONTENT;
                $backup_data['prefix']=$this->get_prefix().'_backup_themes';
                $backup_data['files_root']=$this->transfer_path(get_theme_root());
                $backup_data['exclude_regex']=array();
                $backup_data['include_regex']=array();
                $backup_data['json_info']['file_type']='themes';
                $backup_data['json_info']['themes']=$this->get_themes_list();
                $this->task['options']['backup_options']['backup'][$backup_data['key']]=$backup_data;
            }
            else if($backup==WPVIVID_BACKUP_TYPE_PLUGIN)
            {
                //$backup_data['root_path']=WP_CONTENT_DIR;
                $backup_data['root_flag']=WPVIVID_BACKUP_ROOT_WP_CONTENT;
                $backup_data['prefix']=$this->get_prefix().'_backup_plugin';
                if(isset($backup_data['compress']['subpackage_plugin_upload'])&&$backup_data['compress']['subpackage_plugin_upload'])
                {
                    $backup_data['plugin_subpackage']=1;
                }
                $backup_data['files_root']=$this->transfer_path(WP_PLUGIN_DIR);

                $exclude_plugins=array();
                $exclude_plugins=apply_filters('wpvivid_exclude_plugins',$exclude_plugins);
                $exclude_regex=array();
                foreach ($exclude_plugins as $exclude_plugin)
                {
                    $exclude_regex[]='#^'.preg_quote($this -> transfer_path(WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.$exclude_plugin), '/').'#';
                }
                $backup_data['exclude_regex']=$exclude_regex;
                $backup_data['include_regex']=array();
                $backup_data['json_info']['file_type']='plugin';
                $backup_data['json_info']['plugin']=$this->get_plugins_list();
            }
            else if($backup==WPVIVID_BACKUP_TYPE_UPLOADS)
            {
                //$backup_data['root_path']=WP_CONTENT_DIR;
                $backup_data['root_flag']=WPVIVID_BACKUP_ROOT_WP_CONTENT;
                $backup_data['prefix']=$this->get_prefix().'_backup_uploads';
                $upload_dir = wp_upload_dir();
                $backup_data['files_root']=$this -> transfer_path($upload_dir['basedir']);

                $exclude_regex=array();
                $exclude_regex=apply_filters('wpvivid_get_backup_exclude_regex',$exclude_regex,WPVIVID_BACKUP_TYPE_UPLOADS);
                $backup_data['exclude_regex']=$exclude_regex;
                $backup_data['include_regex']=array();
                $backup_data['json_info']['file_type']='upload';
            }
            else if($backup==WPVIVID_BACKUP_TYPE_UPLOADS_FILES)
            {
                //$backup_data['root_path']=WP_CONTENT_DIR;
                $backup_data['root_flag']=WPVIVID_BACKUP_ROOT_WP_CONTENT;
                $backup_data['prefix']=$this->get_prefix().'_backup_uploads';
                $backup_data['uploads_subpackage']=1;
                $upload_dir = wp_upload_dir();
                $backup_data['files_root']=$this -> transfer_path($upload_dir['basedir']);
                $exclude_regex=array();
                $exclude_regex=apply_filters('wpvivid_get_backup_exclude_regex',$exclude_regex,WPVIVID_BACKUP_TYPE_UPLOADS_FILES);
                $backup_data['include_regex']=array();
                $backup_data['exclude_regex']=$exclude_regex;
                //$backup_data['include_regex'][]='#^'.preg_quote($this -> transfer_path($upload_dir['basedir']).DIRECTORY_SEPARATOR, '/').'[0-9]{4}#';
                $backup_data['json_info']['file_type']='upload';
            }
            else if($backup==WPVIVID_BACKUP_TYPE_CONTENT)
            {
                //$backup_data['root_path']=get_home_path();
                $backup_data['root_flag']=WPVIVID_BACKUP_ROOT_WP_ROOT;
                $backup_data['prefix']=$this->get_prefix().'_backup_content';
                $backup_data['files_root']=$this -> transfer_path(WP_CONTENT_DIR);
                $exclude_regex=array();
                $exclude_regex=apply_filters('wpvivid_get_backup_exclude_regex',$exclude_regex,WPVIVID_BACKUP_TYPE_CONTENT);
                $backup_data['exclude_regex']=$exclude_regex;
                $backup_data['include_regex']=array();
                $backup_data['json_info']['file_type']='wp-content';
            }
            else if($backup==WPVIVID_BACKUP_TYPE_CORE)
            {
                //$backup_data['root_path']=ABSPATH;
                $backup_data['root_flag']=WPVIVID_BACKUP_ROOT_WP_ROOT;
                $backup_data['prefix']=$this->get_prefix().'_backup_core';
                $backup_data['files_root']=$this -> transfer_path(ABSPATH);
                $backup_data['json_info']['include_path'][]='wp-includes';
                $backup_data['json_info']['include_path'][]='wp-admin';
                $backup_data['json_info']['wp_core']=1;
                $backup_data['json_info']['home_url']=home_url();
                $include_regex[]='#^'.preg_quote($this -> transfer_path(ABSPATH.DIRECTORY_SEPARATOR.'wp-admin'), '/').'#';
                $include_regex[]='#^'.preg_quote($this->transfer_path(ABSPATH.DIRECTORY_SEPARATOR.'wp-includes'), '/').'#';
                $exclude_regex[]='#^'.preg_quote($this->transfer_path(ABSPATH.DIRECTORY_SEPARATOR.'wp-admin'.DIRECTORY_SEPARATOR), '/').'pclzip-.*\.tmp#';
                $exclude_regex[]='#^'.preg_quote($this->transfer_path(ABSPATH.DIRECTORY_SEPARATOR.'wp-admin'.DIRECTORY_SEPARATOR), '/').'pclzip-.*\.gz#';
                $exclude_regex[]='#session_mm_cgi-fcgi#';
                $exclude_regex=apply_filters('wpvivid_get_backup_exclude_regex',$exclude_regex,WPVIVID_BACKUP_TYPE_CORE);
                $backup_data['exclude_regex']=$exclude_regex;
                $backup_data['include_regex']=$include_regex;
                $backup_data['json_info']['file_type']='wp-core';
            }
            else if($backup==WPVIVID_BACKUP_TYPE_MERGE)
            {
                //$backup_data['root_path']=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$this->task['options']['backup_options']['dir'];
                $backup_data['root_flag']=WPVIVID_BACKUP_ROOT_CUSTOM;
                $file_name = $this->get_prefix().'_backup_all';
                $file_name = apply_filters('wpvivid_set_incremental_backup_file_name', $file_name, $this->get_prefix(), $task_type);
                $backup_data['prefix']=$file_name;
                $backup_data['files']=array();
                $backup_data['json_info']['has_child']=1;
                foreach ($this->task['options']['backup_options']['backup'] as $backup_finished_data)
                {
                    $backup_data['files']=array_merge($backup_data['files'],$this->get_backup_file($backup_finished_data['key']));
                }
                $backup_data['json_info']['home_url']=home_url();
            }
            else
            {
                $backup_data=false;
            }
            if($backup_data!==false)
            {
                $backup_data=apply_filters('wpvivid_set_backup',$backup_data);
                $this->task['options']['backup_options']['backup'][$backup_data['key']]=$backup_data;
            }
        }
    }

    public function get_themes_list()
    {
        $themes_list=array();
        $list=wp_get_themes();
        foreach ($list as $key=>$item)
        {
            $themes_list[$key]['slug']=$key;
            $themes_list[$key]['size']=$this->get_folder_size(get_theme_root().DIRECTORY_SEPARATOR.$key,0);
        }
        return $themes_list;
    }

    public function get_plugins_list()
    {
        $plugins_list=array();

        if(!function_exists('get_plugins'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');

        $list=get_plugins();

        $exclude_plugins[]='wpvivid-backuprestore';
        $exclude_plugins[]='wp-cerber';
        $exclude_plugins[]='.';
        $exclude_plugins=apply_filters('wpvivid_exclude_plugins',$exclude_plugins);

        foreach ($list as $key=>$item)
        {
            if(in_array(dirname($key),$exclude_plugins))
            {
                continue;
            }

            $plugins_list[dirname($key)]['slug']=dirname($key);
            $plugins_list[dirname($key)]['size']=$this->get_folder_size(WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.dirname($key),0);
            //
        }
        return $plugins_list;
    }

    public function get_folder_size($root,$size)
    {
        $count = 0;
        if(is_dir($root))
        {
            $handler = opendir($root);
            if($handler!==false)
            {
                while (($filename = readdir($handler)) !== false)
                {
                    if ($filename != "." && $filename != "..") {
                        $count++;

                        if (is_dir($root . DIRECTORY_SEPARATOR . $filename))
                        {
                            $size=$this->get_folder_size($root . DIRECTORY_SEPARATOR . $filename,$size);
                        } else {
                            $size+=filesize($root . DIRECTORY_SEPARATOR . $filename);
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }

        }
        return $size;
    }

    public function wpvivid_set_backup($backup_data)
    {
        if(isset($backup_data['uploads_subpackage'])||isset($backup_data['plugin_subpackage']))
        {
            $backup_data['resume_packages'] = 1;
        }
        else if($backup_data['key']==WPVIVID_BACKUP_TYPE_MERGE)
        {
            $backup_data['resume_packages'] = 1;
        }

        return $backup_data;
    }

    public function get_need_backup_files($backup_data)
    {
        if(isset($backup_data['uploads_subpackage']))
        {
            return $backup_data['files']=$this->get_need_uploads_backup_folder($backup_data);
        }

        if(isset($backup_data['plugin_subpackage']))
        {
            return $backup_data['files']=$this->get_need_backup_folder($backup_data);
        }

        if(isset($backup_data['files'])&&!empty($backup_data['files']))
        {
            return $backup_data['files'];
        }
        else
        {
            return $this->get_file_list($backup_data['files_root'],$backup_data['exclude_regex'],$backup_data['include_regex'],$this->task['options']['backup_options']['compress']['exclude_file_size']);
        }
    }

    public function get_backup_file($key)
    {
        $files=array();
        if(array_key_exists($key,$this->task['options']['backup_options']['backup']))
        {
            $backup=$this->task['options']['backup_options']['backup'][$key];
            if($backup['finished'])
            {
                if($backup['result']!=false)
                {
                    foreach ($backup['result']['files'] as $file_data)
                    {
                        $files[]=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$this->task['options']['backup_options']['dir'].DIRECTORY_SEPARATOR.$file_data['file_name'];
                    }
                }
            }
        }
        return $files;
    }

    public function get_need_backup()
    {
        $this->task=WPvivid_taskmanager::get_task($this->task['id']);
        $backup=false;
        $i_finished_backup_count=0;
        $i_count_of_backup=sizeof($this->task['options']['backup_options']['backup']);
        foreach ($this->task['options']['backup_options']['backup'] as $key=>$backup_data)
        {
            if($backup_data['result']!==false)
            {
                $ret=$backup_data['result'];
                if($ret['result']!==WPVIVID_SUCCESS)
                {
                    return false;
                }
            }

            if( $backup_data['finished']==0)
            {
                if($backup===false)
                {
                    add_filter('wpvivid_get_need_backup_files', array($this, 'wpvivid_get_need_backup_files'),10);
                    $backup_data=apply_filters('wpvivid_get_need_backup_files',$backup_data);

                    $backup=$backup_data;
                }
            }
            else {
                $i_finished_backup_count++;
            }
        }

        if($i_count_of_backup>0)
        {
            $i_progress=intval(1/$i_count_of_backup*100);
            WPvivid_taskmanager::update_backup_main_task_progress($this->task['id'],'backup',$i_progress*$i_finished_backup_count,0);

            if($i_finished_backup_count>=$i_count_of_backup)
            {
                if($this->task['options']['backup_options']['ismerge']==1)
                {
                    if(!array_key_exists(WPVIVID_BACKUP_TYPE_MERGE,$this->task['options']['backup_options']['backup']))
                    {
                        $this->set_backup(WPVIVID_BACKUP_TYPE_MERGE, $this->task['type']);
                        WPvivid_Setting::update_task($this->task['id'],$this->task);
                        $backup=$this->task['options']['backup_options']['backup'][WPVIVID_BACKUP_TYPE_MERGE];
                    }
                }
            }
        }
        return $backup;
    }

    public function get_need_backup_files_size($backup_data)
    {
        if(isset($backup_data['files'])&&!empty($backup_data['files']))
        {
            return $backup_data['files'];
        }
        else
        {
            return $this->get_file_list($backup_data['files_root'],$backup_data['exclude_regex'],$backup_data['include_regex'],$this->task['options']['backup_options']['compress']['exclude_file_size']);
        }
    }

    public function wpvivid_get_need_backup_files($backup_data)
    {
        if(!isset($backup_data['dump_db']))
        {
            if(array_key_exists($backup_data['key'],$this->backup_type_collect))
            {
                $backup_data['files'] = $this->get_need_backup_files($backup_data);
            }
            else
            {
                if(!isset($backup_data['files']))
                {
                    $backup_data['files']=array();
                }
                $backup_data['files'] =apply_filters('wpvivid_get_custom_need_backup_files', $backup_data['files'],$backup_data,$this->task['options']['backup_options']['compress']);
            }

            $need=false;
            add_filter('wpvivid_need_backup_files_update', array($this, 'need_backup_files_update'), 10, 2);
            if(apply_filters('wpvivid_need_backup_files_update',$need,$backup_data))
            {
                $this->task['options']['backup_options']['backup'][$backup_data['key']]=$backup_data;
                WPvivid_Setting::update_task($this->task['id'],$this->task);
            }
        }
        return $backup_data;
    }

    public function need_backup_files_update($need,$backup_data)
    {
        if(isset($backup_data['uploads_subpackage'])||isset($backup_data['plugin_subpackage']))
        {
            return true;
        }
        else
        {
            return $need;
        }
    }

    public function update_backup_result($backup_data,$result)
    {
        $this->task=WPvivid_taskmanager::get_task($this->task['id']);

        if(array_key_exists($backup_data['key'],$this->task['options']['backup_options']['backup']))
        {
            $this->task['options']['backup_options']['backup'][$backup_data['key']]['finished']=1;

            add_filter('wpvivid_backup_update_result', array($this, 'wpvivid_backup_update_result'),10,2);
            $result=apply_filters('wpvivid_backup_update_result',$result,$backup_data);
            $this->task['options']['backup_options']['backup'][$backup_data['key']]['result']=$result;

            WPvivid_taskmanager::update_task_options($this->task['id'],'backup_options', $this->task['options']['backup_options']);

            if($result['result']==WPVIVID_FAILED)
            {
                WPvivid_taskmanager::update_backup_task_status($this->task['id'],false,'error',false,false,$result['error']);
                return ;
            }
        }

        $i_finished_backup_count=0;
        $i_count_of_backup=sizeof($this->task['options']['backup_options']['backup']);

        foreach ($this->task['options']['backup_options']['backup'] as $backup_data)
        {
            if( $backup_data['finished']==1)
            {
                $i_finished_backup_count++;
            }
        }

        if($i_finished_backup_count>=$i_count_of_backup)
        {
            WPvivid_taskmanager::update_backup_main_task_progress($this->task['id'],'backup',100,1);
        }
        //WPvivid_Setting::update_task($this->task['id'],$this->task);
    }

    public function wpvivid_backup_update_result($result,$backup_data)
    {
        if($result['result']==WPVIVID_SUCCESS)
        {
            $exist_files_name=array();
            foreach ($result['files'] as $temp_file_data)
            {
                $exist_files_name[$temp_file_data['file_name']]=$temp_file_data['file_name'];
            }

            if(isset($backup_data['resume_packages'])&&$backup_data['resume_packages'])
            {
                $packages_files_info = $this->get_packages_files_info($backup_data['key']);
                if ($packages_files_info !== false)
                {
                    foreach ($packages_files_info as $file_data)
                    {
                        if(!array_key_exists($file_data['file_name'],$exist_files_name))
                        {
                            $result['files'][]=$file_data;
                        }
                    }
                }
            }
        }

        return $result;
    }

    public function get_backup_result()
    {
        $ret['result']=WPVIVID_SUCCESS;
        $ret['files']=array();
        foreach ($this->task['options']['backup_options']['backup'] as $backup_data)
        {
            if($this->task['options']['backup_options']['ismerge']==1)
            {
                if(WPVIVID_BACKUP_TYPE_MERGE==$backup_data['key'])
                {
                    $ret=$backup_data['result'];
                    if($ret['result']!==WPVIVID_SUCCESS)
                    {
                        return $ret;
                    }
                }
            }
            else
            {
                $ret['files']=array_merge($ret['files'],$backup_data['result']['files']);
            }
        }

        return $ret;
    }

    private function transfer_path($path)
    {
        $path = str_replace('\\','/',$path);
        $values = explode('/',$path);
        return implode(DIRECTORY_SEPARATOR,$values);
    }

    public function get_file_list($root,$exclude_regex,$include_regex,$exclude_file_size)
    {
        $files=array();
        $this->getFileLoop($files,$root,$exclude_regex,$include_regex,$exclude_file_size);
        return $files;
    }

    public function getFileLoop(&$files,$path,$exclude_regex=array(),$include_regex=array(),$exclude_file_size=0,$include_dir = true)
    {
        $count = 0;
        if(is_dir($path))
        {
            $handler = opendir($path);
            if($handler!==false)
            {
                while (($filename = readdir($handler)) !== false)
                {
                    if ($filename != "." && $filename != "..") {
                        $count++;

                        if (is_dir($path . DIRECTORY_SEPARATOR . $filename))
                        {
                            if ($this->regex_match($exclude_regex, $path . DIRECTORY_SEPARATOR . $filename, 0)) {
                                if ($this->regex_match($include_regex, $path . DIRECTORY_SEPARATOR . $filename, 1)) {
                                    $this->getFileLoop($files, $path . DIRECTORY_SEPARATOR . $filename, $exclude_regex, $include_regex, $exclude_file_size, $include_dir);
                                }
                            }
                        } else {
                            if($this->regex_match($exclude_regex, $path . DIRECTORY_SEPARATOR . $filename, 0)){
                                if ($exclude_file_size == 0)
                                {
                                    if(is_readable($path . DIRECTORY_SEPARATOR . $filename))
                                    {
                                        $files[] = $path . DIRECTORY_SEPARATOR . $filename;
                                    }
                                } else {
                                    if(is_readable($path . DIRECTORY_SEPARATOR . $filename))
                                    {
                                        if (filesize($path . DIRECTORY_SEPARATOR . $filename) < $exclude_file_size * 1024 * 1024) {
                                            $files[] = $path . DIRECTORY_SEPARATOR . $filename;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }

        }
        if($include_dir && $count == 0){
            $files[] = $path;
        }
    }

    private function regex_match($regex_array,$string,$mode)
    {
        if(empty($regex_array))
        {
            return true;
        }

        if($mode==0)
        {
            foreach ($regex_array as $regex)
            {
                if(preg_match($regex,$string))
                {
                    return false;
                }
            }

            return true;
        }

        if($mode==1)
        {
            foreach ($regex_array as $regex)
            {
                if(preg_match($regex,$string))
                {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    public function get_need_cleanup_files($all=false)
    {
        $files=array();
        if($this->task['options']['backup_options']['ismerge']==1)
        {
            foreach ($this->task['options']['backup_options']['backup'] as $backup_finished_data)
            {
                if($all===false)
                {
                    if(WPVIVID_BACKUP_TYPE_MERGE==$backup_finished_data['key'])
                        continue;
                }

                $files=array_merge($files,$this->get_backup_file($backup_finished_data['key']));
                add_filter('wpvivid_get_need_cleanup_files', array($this, 'wpvivid_get_need_cleanup_files'),10,2);
                $files=apply_filters('wpvivid_get_need_cleanup_files',$files,$backup_finished_data);
            }
        }

        return $files;
    }

    public function wpvivid_get_need_cleanup_files($files,$backup_finished_data)
    {
        if(WPVIVID_BACKUP_TYPE_PLUGIN==$backup_finished_data['key'])
        {
            $general_setting=WPvivid_Setting::get_setting(true, "");
            if(isset($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload']) && !empty($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload'])){
                if($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload']){
                    $packages_files_info = $this->get_packages_files_info(WPVIVID_BACKUP_TYPE_PLUGIN);
                    if ($packages_files_info !== false) {
                        foreach ($packages_files_info as $file_data) {
                            $path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $this->task['options']['backup_options']['dir'] . DIRECTORY_SEPARATOR . $file_data['file_name'];
                            if (!in_array($path, $files))
                                $files[] = $path;
                        }
                    }
                }
            }
        }
        else if(WPVIVID_BACKUP_TYPE_UPLOADS_FILES==$backup_finished_data['key'])
        {
            $general_setting=WPvivid_Setting::get_setting(true, "");
            if(isset($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload']) && !empty($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload'])){
                if($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload']){
                    $packages_files_info = $this->get_packages_files_info(WPVIVID_BACKUP_TYPE_UPLOADS_FILES);
                    if ($packages_files_info !== false) {
                        foreach ($packages_files_info as $file_data) {
                            $path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $this->task['options']['backup_options']['dir'] . DIRECTORY_SEPARATOR . $file_data['file_name'];
                            global $wpvivid_plugin;
                            $wpvivid_plugin->wpvivid_log->WriteLog('test:' . $path, 'notice');
                            if (!in_array($path, $files))
                                $files[] = $path;
                        }
                    }
                }
            }
        }
        return $files;
    }

    public function get_prefix()
    {
        return  $this->task['options']['backup_options']['prefix'];
    }

    public function set_file_and_db_info($db_size,$file_size)
    {
        $this->task['data']['backup']['db_size']=$db_size;
        $this->task['data']['backup']['files_size']=$file_size;

        WPvivid_Setting::update_task($this->task['id'],$this->task);
    }

    public function get_file_info()
    {
        $file_size['sum_size']=0;
        $file_size['sum_count']=0;

        $memory_limit = ini_get('memory_limit');
        $ret['memory_limit']=$memory_limit;
        $memory_limit = trim($memory_limit);
        $memory_limit_int = (int) $memory_limit;
        $last = strtolower(substr($memory_limit, -1));

        if($last == 'g')
            $memory_limit_int = $memory_limit_int*1024*1024*1024;
        if($last == 'm')
            $memory_limit_int = $memory_limit_int*1024*1024;
        if($last == 'k')
            $memory_limit_int = $memory_limit_int*1024;

        $files=array();

        foreach ($this->task['options']['backup_options']['backup'] as $backup_data)
        {
            if(!isset($backup_data['dump_db']))
            {
                if(array_key_exists($backup_data['key'],$this->backup_type_collect))
                {
                    $backup_files = $this->get_need_backup_files_size($backup_data);
                }
                else
                {
                    $backup_data['files']=array();
                    $backup_files =apply_filters('wpvivid_get_custom_need_backup_files_size', $backup_data['files'],$backup_data,$this->task['options']['backup_options']['compress']);
                }
                $files=array_merge($backup_files,$files);
            }
        }

        foreach ($files as $file)
        {
            $size=0;
            $_file_size=filesize($file);
            if($_file_size>($memory_limit_int*0.9))
            {
                $ret['alter_big_file']=true;
                $ret['alter_files']=true;
            }
            $size+=$_file_size;
            $file_size['sum_count']++;
            $file_size['sum_size']+=$size;
        }
        return $file_size;
    }

    public function is_cancel_file_exist()
    {
        $file_name=$this->task['options']['file_prefix'];

        $file=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$this->task['options']['backup_options']['dir'].DIRECTORY_SEPARATOR.$file_name.'_cancel';

        if(file_exists($file))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function update_status($status)
    {
        $this->task['status']['str']=$status;
        WPvivid_Setting::update_task($this->task['id'],$this->task);
    }

    public function get_backup_task_info($task_id){
        $list_tasks['status']=WPvivid_taskmanager::get_backup_tasks_status($task_id);
        $list_tasks['is_canceled']=WPvivid_taskmanager::is_task_canceled($task_id);
        $list_tasks['size']=WPvivid_taskmanager::get_backup_size($task_id);
        $list_tasks['data']=WPvivid_taskmanager::get_backup_tasks_progress($task_id);
        //
        $list_tasks['task_info']['need_next_schedule']=false;
        if($list_tasks['status']['str']=='running'||$list_tasks['status']['str']=='no_responds')
        {
            if($list_tasks['data']['running_stamp']>180) {
                $list_tasks['task_info']['need_next_schedule'] = true;
            }
            else{
                $list_tasks['task_info']['need_next_schedule'] = false;
            }
        }
        //
        $general_setting=WPvivid_Setting::get_setting(true, "");
        if($general_setting['options']['wpvivid_common_setting']['estimate_backup'] == 0){
            $list_tasks['task_info']['display_estimate_backup'] = 'display: none';
        }
        else{
            $list_tasks['task_info']['display_estimate_backup'] = '';
        }
        //
        $list_tasks['task_info']['backup_percent']=$list_tasks['data']['progress'].'%';
        //
        if($list_tasks['size']['db_size'] == false){
            $list_tasks['task_info']['db_size']=0;
        }
        else{
            $list_tasks['task_info']['db_size']=$list_tasks['size']['db_size'];
        }
        if($list_tasks['size']['files_size'] == false){
            $list_tasks['task_info']['file_size']=0;
        }
        else{
            $list_tasks['task_info']['file_size']=$list_tasks['size']['files_size']['sum'];
        }
        //
        $list_tasks['task_info']['descript']='';
        $list_tasks['task_info']['css_btn_cancel']='pointer-events: auto; opacity: 1;';
        $list_tasks['task_info']['css_btn_log']='pointer-events: auto; opacity: 1;';
        $list_tasks['task_info']['total'] = 'N/A';
        $list_tasks['task_info']['upload'] = 'N/A';
        $list_tasks['task_info']['speed'] = 'N/A';
        $list_tasks['task_info']['network_connection'] = 'N/A';

        $list_tasks['task_info']['need_update_last_task']=false;
        if($list_tasks['status']['str']=='ready')
        {
            $list_tasks['task_info']['descript']=__('Ready to backup. Progress: 0%, running time: 0second.','wpvivid-backuprestore');
            $list_tasks['task_info']['css_btn_cancel']='pointer-events: none; opacity: 0.4;';
            $list_tasks['task_info']['css_btn_log']='pointer-events: none; opacity: 0.4;';
        }
        else if($list_tasks['status']['str']=='running')
        {
            if($list_tasks['is_canceled'] == false)
            {
                if($list_tasks['data']['type'] == 'upload')
                {
                    if(isset($list_tasks['data']['upload_data']) && !empty($list_tasks['data']['upload_data'])) {
                        $descript = $list_tasks['data']['upload_data']['descript'];
                        $offset = $list_tasks['data']['upload_data']['offset'];
                        $current_size = $list_tasks['data']['upload_data']['current_size'];
                        $last_time = $list_tasks['data']['upload_data']['last_time'];
                        $last_size = $list_tasks['data']['upload_data']['last_size'];
                        $speed = ($offset - $last_size) / (time() - $last_time);
                        $speed /= 1000;
                        $speed = round($speed, 2);
                        $speed .= 'kb/s';
                        if(!empty($current_size)) {
                            $list_tasks['task_info']['total'] = size_format($current_size,2);
                        }
                        if(!empty($offset)) {
                            $list_tasks['task_info']['upload'] = size_format($offset, 2);
                        }
                    }
                    else{
                        $descript = 'Start uploading.';
                        $speed = '0kb/s';
                        $list_tasks['task_info']['total'] = 'N/A';
                        $list_tasks['task_info']['upload'] = 'N/A';
                    }

                    $list_tasks['task_info']['speed'] = $speed;
                    $list_tasks['task_info']['descript'] = $descript.' '.__('Progress: ', 'wpvivid-backuprestore') . $list_tasks['task_info']['backup_percent'] . ', ' . __('running time: ', 'wpvivid-backuprestore') . $list_tasks['data']['running_time'];

                    $time_spend=time()-$list_tasks['status']['run_time'];
                    if($time_spend>30)
                    {
                        $list_tasks['task_info']['network_connection']='Retrying';
                    }
                    else
                    {
                        $list_tasks['task_info']['network_connection']='OK';
                    }
                }
                else {
                    $list_tasks['task_info']['descript'] = $list_tasks['data']['descript'] . ' '. __('Progress: ', 'wpvivid-backuprestore') . $list_tasks['task_info']['backup_percent'] . ', '. __('running time: ', 'wpvivid-backuprestore') . $list_tasks['data']['running_time'];
                }
                $list_tasks['task_info']['css_btn_cancel']='pointer-events: auto; opacity: 1;';
                $list_tasks['task_info']['css_btn_log']='pointer-events: auto; opacity: 1;';
            }
            else{
                $list_tasks['task_info']['descript']=__('The backup will be canceled after backing up the current chunk ends.','wpvivid-backuprestore');
                $list_tasks['task_info']['css_btn_cancel']='pointer-events: none; opacity: 0.4;';
                $list_tasks['task_info']['css_btn_log']='pointer-events: auto; opacity: 1;';
            }
        }
        else if($list_tasks['status']['str']=='wait_resume'){
            $list_tasks['task_info']['descript']='Task '.$task_id.' timed out, backup task will retry in '.$list_tasks['data']['next_resume_time'].' seconds, retry times: '.$list_tasks['status']['resume_count'].'.';
            $list_tasks['task_info']['css_btn_cancel']='pointer-events: auto; opacity: 1;';
            $list_tasks['task_info']['css_btn_log']='pointer-events: auto; opacity: 1;';
        }
        else if($list_tasks['status']['str']=='no_responds'){
            if($list_tasks['is_canceled'] == false){
                $list_tasks['task_info']['descript']='Task , '.$list_tasks['data']['doing'].' is not responding. Progress: '.$list_tasks['task_info']['backup_percent'].', running time: '.$list_tasks['data']['running_time'];
                $list_tasks['task_info']['css_btn_cancel']='pointer-events: auto; opacity: 1;';
                $list_tasks['task_info']['css_btn_log']='pointer-events: auto; opacity: 1;';
            }
            else{
                $list_tasks['task_info']['descript']=__('The backup will be canceled after backing up the current chunk ends.','wpvivid-backuprestore');
                $list_tasks['task_info']['css_btn_cancel']='pointer-events: none; opacity: 0.4;';
                $list_tasks['task_info']['css_btn_log']='pointer-events: auto; opacity: 1;';
            }
        }
        else if($list_tasks['status']['str']=='completed'){
            $list_tasks['task_info']['descript']='Task '.$task_id.' completed.';
            $list_tasks['task_info']['css_btn_cancel']='pointer-events: auto; opacity: 1;';
            $list_tasks['task_info']['css_btn_log']='pointer-events: auto; opacity: 1;';
            $list_tasks['task_info']['need_update_last_task']=true;
        }
        else if($list_tasks['status']['str']=='error'){
            $list_tasks['task_info']['descript']='Backup error: '.$list_tasks['status']['error'];
            $list_tasks['task_info']['css_btn_cancel']='pointer-events: auto; opacity: 1;';
            $list_tasks['task_info']['css_btn_log']='pointer-events: auto; opacity: 1;';
            $list_tasks['task_info']['need_update_last_task']=true;
        }

        return $list_tasks;
    }

    public function get_packages_info($key)
    {
        if(isset($this->task['data']['backup']['sub_job'][$key]))
        {
            if(empty($this->task['data']['backup']['sub_job'][$key]['job_data']))
            {
                return false;
            }
            else
            {
                $packages=array();
                foreach ($this->task['data']['backup']['sub_job'][$key]['job_data'] as $key=>$package)
                {
                    if($key=='files')
                    {
                        continue;
                    }

                    $packages[]=$package;
                }
                if(empty($packages))
                {
                    return false;
                }
                else
                {
                    return $packages;
                }
            }
        }
        else
        {
            return false;
        }
    }

    public function get_packages_files_info($key)
    {
        if(isset($this->task['data']['backup']['sub_job'][$key]))
        {
            if(empty($this->task['data']['backup']['sub_job'][$key]['job_data']))
            {
                return false;
            }
            else
            {
                if(isset($this->task['data']['backup']['sub_job'][$key]['job_data']['files']))
                {
                    return $this->task['data']['backup']['sub_job'][$key]['job_data']['files'];
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {
            return false;
        }
    }

    public function set_packages_info($key,$packages)
    {
        $this->task=WPvivid_taskmanager::get_task($this->task['id']);
        $job_data=array();
        foreach ($packages as $package)
        {
            $package['backup']=false;
            $job_data[basename($package['path'])]=$package;
        }
        $this->task['data']['backup']['sub_job'][$key]['job_data']=$job_data;
        WPvivid_Setting::update_task($this->task['id'],$this->task);
        return $job_data;
    }

    public function update_packages_info($key,$package,$file_data=false)
    {
        $this->task=WPvivid_taskmanager::get_task($this->task['id']);
        $this->task['data']['backup']['sub_job'][$key]['job_data'][basename($package['path'])]=$package;
        if($file_data!==false)
        {
            $this->task['data']['backup']['sub_job'][$key]['job_data']['files'][]=$file_data;
        }
        WPvivid_Setting::update_task($this->task['id'],$this->task);
    }

    public function update_sub_task_progress($key,$finished,$progress)
    {
        $this->task=WPvivid_taskmanager::get_task($this->task['id']);
        $this->task['status']['run_time']=time();
        $this->task['status']['str']='running';
        $this->task['data']['doing']='backup';
        $sub_job_name=$key;
        $this->task['data']['backup']['doing']=$key;
        $this->task['data']['backup']['sub_job'][$sub_job_name]['finished']=$finished;
        $this->task['data']['backup']['sub_job'][$sub_job_name]['progress']=$progress;
        if(!isset( $this->task['data']['backup']['sub_job'][$sub_job_name]['job_data']))
        {
            $this->task['data']['backup']['sub_job'][$sub_job_name]['job_data']=array();
        }
        WPvivid_Setting::update_task($this->task['id'],$this->task);
    }

    public function get_need_backup_folder($backup_data)
    {
        if(isset($backup_data['files'])&&empty($backup_data['files']))
        {
            return $backup_data['files'];
        }
        else
        {
            return $this->get_folder_list($backup_data['files_root'],$backup_data['exclude_regex'],$backup_data['include_regex'],$this->task['options']['backup_options']['compress']['exclude_file_size']);
        }
    }

    public function get_folder_list($root,$exclude_regex,$include_regex,$exclude_file_size)
    {
        $files=array();
        $this->getFolder($files,$root,$exclude_regex,$include_regex,$exclude_file_size);
        return $files;
    }

    public function getFolder(&$files,$path,$exclude_regex=array(),$include_regex=array(),$exclude_file_size=0,$include_dir = true)
    {
        $count = 0;
        if(is_dir($path))
        {
            $handler = opendir($path);
            if($handler!==false)
            {
                while (($filename = readdir($handler)) !== false)
                {
                    if ($filename != "." && $filename != "..")
                    {
                        $count++;

                        if (is_dir($path . DIRECTORY_SEPARATOR . $filename))
                        {
                            if ($this->regex_match($exclude_regex, $path . DIRECTORY_SEPARATOR . $filename, 0))
                            {
                                if ($this->regex_match($include_regex, $path . DIRECTORY_SEPARATOR . $filename, 1))
                                {
                                    $files[] = $path . DIRECTORY_SEPARATOR . $filename;
                                }
                            }
                        } else {
                            if($this->regex_match($exclude_regex, $path . DIRECTORY_SEPARATOR . $filename, 0)){
                                if ($exclude_file_size == 0)
                                {
                                    if(is_readable($path . DIRECTORY_SEPARATOR . $filename))
                                    {
                                        $files[] = $path . DIRECTORY_SEPARATOR . $filename;
                                    }
                                } else {
                                    if(is_readable($path . DIRECTORY_SEPARATOR . $filename))
                                    {
                                        if (filesize($path . DIRECTORY_SEPARATOR . $filename) < $exclude_file_size * 1024 * 1024)
                                        {
                                            $files[] = $path . DIRECTORY_SEPARATOR . $filename;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }
        }
        if($include_dir && $count == 0)
        {
            $files[] = $path;
        }
    }

    public function get_need_uploads_backup_folder($backup_data)
    {
        if(isset($backup_data['files'])&&empty($backup_data['files']))
        {
            return $backup_data['files'];
        }
        else
        {
            return $this->get_uploads_folder_list($backup_data['files_root'],$backup_data['exclude_regex'],$backup_data['include_regex'],$this->task['options']['backup_options']['compress']['exclude_file_size']);
        }
    }

    public function get_uploads_folder_list($root,$exclude_regex,$include_regex,$exclude_file_size)
    {
        $files=array();
        $files[] = $root;
        $this->getUploadsFolder($files,$root,$exclude_regex,$include_regex,$exclude_file_size);
        return $files;
    }

    public function getUploadsFolder(&$files,$path,$exclude_regex=array(),$include_regex=array(),$exclude_file_size=array(),$include_dir = true)
    {
        $count = 0;
        if(is_dir($path))
        {
            $handler = opendir($path);
            if($handler===false)
                return;
            while (($filename = readdir($handler)) !== false)
            {
                if ($filename != "." && $filename != "..")
                {
                    $count++;

                    if (is_dir($path . DIRECTORY_SEPARATOR . $filename))
                    {
                        if ($this->regex_match($exclude_regex, $path . DIRECTORY_SEPARATOR . $filename, 0))
                        {
                            if ($this->regex_match($include_regex, $path . DIRECTORY_SEPARATOR . $filename, 1))
                            {
                                $this->getUploadsFolder($files,$path . DIRECTORY_SEPARATOR . $filename,$exclude_regex,$include_regex,$exclude_file_size,$include_dir);
                            }
                        }
                    }
                }
            }
            if($handler)
                @closedir($handler);
        }
        if($include_dir && $count == 0)
        {
            $files[] = $path;
        }
    }

    public function add_new_backup()
    {
        $this->task=WPvivid_taskmanager::get_task($this->task['id']);
        $backup_data=array();
        $backup_data['type']=$this->task['type'];
        $status=WPvivid_taskmanager::get_backup_task_status($this->task['id']);
        $backup_data['create_time']=$status['start_time'];
        $backup_data['manual_delete']=0;
        $backup_options=WPvivid_taskmanager::get_task_options($this->task['id'],'backup_options');
        $lock=WPvivid_taskmanager::get_task_options($this->task['id'],'lock');
        $backup_data['local']['path']=$backup_options['dir'];
        $backup_data['compress']['compress_type']=$backup_options['compress']['compress_type'];
        $backup_data['save_local']=$this->task['options']['save_local'];
        if(isset($this->task['options']['backup_prefix']))
        {
            $backup_data['backup_prefix'] = $this->task['options']['backup_prefix'];
        }

        global $wpvivid_plugin;
        $backup_data['log']=$wpvivid_plugin->wpvivid_log->log_file;
        $backup_data['backup']=$this->get_backup_result();
        $backup_data['remote']=array();
        if($lock==1)
            $backup_data['lock']=1;

        $backup_list='wpvivid_backup_list';

        $backup_list=apply_filters('get_wpvivid_backup_list_name',$backup_list,$this->task['id']);

        $list = WPvivid_Setting::get_option($backup_list);
        $list[$this->task['id']]=$backup_data;
        WPvivid_Setting::update_option($backup_list,$list);
    }

    public function get_backup_files()
    {
        $files=array();
        foreach ($this->task['options']['backup_options']['backup'] as $backup_data)
        {
            if($this->task['options']['backup_options']['ismerge']==1)
            {
                if(WPVIVID_BACKUP_TYPE_MERGE==$backup_data['key'])
                {
                    if($backup_data['result']!==false)
                    {
                        $ret=$backup_data['result'];
                        if($ret['result']===WPVIVID_SUCCESS)
                        {
                            foreach ($ret['files'] as $file)
                            {
                                $files[]=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$this->task['options']['backup_options']['dir'].DIRECTORY_SEPARATOR.$file['file_name'];
                            }
                        }
                    }
                }
            }
            else
            {
                if($backup_data['result']!==false)
                {
                    $ret=$backup_data['result'];
                    if($ret['result']===WPVIVID_SUCCESS)
                    {
                        foreach ($ret['files'] as $file)
                        {
                            $files[]=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$this->task['options']['backup_options']['dir'].DIRECTORY_SEPARATOR.$file['file_name'];
                        }
                    }
                }
            }
        }

        return $files;
    }
}

class WPvivid_Backup_Item
{
    private $config;

    public function __construct($options)
    {
        $this->config=$options;
    }

    public function get_backup_type()
    {
        return $this->config['type'];
    }

    public function get_backup_path($file_name)
    {
        $path = $this->get_local_path() . $file_name;

        if (file_exists($path)) {
            return $path;
        }
        else{
            $local_setting = get_option('wpvivid_local_setting', array());
            if(!empty($local_setting))
            {
                $path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $local_setting['path'] . DIRECTORY_SEPARATOR . $file_name;
            }
            else {
                $path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'wpvividbackups' . DIRECTORY_SEPARATOR . $file_name;
            }
        }
        return $path;
    }

    public function get_files($has_dir=true)
    {
        global $wpvivid_plugin;
        $files=array();
        if(isset($this->config['backup']['files']))
        {
            //file_name
            foreach ($this->config['backup']['files'] as $file)
            {
                if($has_dir)
                    $files[]=$this->get_backup_path($file['file_name']);//$this->get_local_path().$file['file_name'];
                else
                    $files[]=$file['file_name'];
            }
        }
        else{
            if(isset($this->config['backup']['data']['meta']['files']))
            {
                foreach ($this->config['backup']['data']['meta']['files'] as $file)
                {
                    if($has_dir)
                        $files[]=$this->get_backup_path($file['file_name']);//$this->get_local_path().$file['file_name'];
                    else
                        $files[]=$file['file_name'];
                }
            }
        }
        return $files;
    }

    public function is_lock()
    {
        if(isset($this->config['lock']))
        {
            return $this->config['lock'];
        }
        else{
            return false;
        }
    }

    public function check_backup_files()
    {
        global $wpvivid_plugin;

        $b_has_data=false;
        $tmp_data=array();
        if(isset($this->config['backup']['files']))
        {
            $b_has_data = true;
            $tmp_data = $this->config['backup']['files'];
        }
        else if(isset($this->config['backup']['data']['meta']['files'])){
            $b_has_data = true;
            $tmp_data = $this->config['backup']['data']['meta']['files'];
        }

        if($b_has_data)
        {
            $b_need_download=false;
            $b_not_found=false;
            $b_test=false;
            foreach ($tmp_data as $file)
            {
                $need_download=false;
                $path=$this->get_backup_path($file['file_name']);//$this->get_local_path().$file['file_name'];
                if(file_exists($path))
                {
                    if(filesize($path) == $file['size'])
                    {
                        if($wpvivid_plugin->wpvivid_check_zip_valid())
                        {
                            $res = TRUE;
                        }
                        else{
                            $res = FALSE;
                        }
                    }
                    else {
                        $res = FALSE;
                    }
                    if ($res !== TRUE)
                    {
                        $need_download=true;
                    }
                }
                else
                {
                    $b_test=true;
                    $need_download=true;
                }

                if($need_download)
                {
                    if(empty($this->config['remote']))
                    {
                        $b_not_found=true;
                        $ret['files'][$file['file_name']]['status']='file_not_found';
                        $ret['files'][$file['file_name']]['size']=$file['size'];
                        //$ret['files'][$file['file_name']]['md5']=$file['md5'];
                    }
                    else
                    {
                        $b_need_download=true;
                        WPvivid_taskmanager::delete_download_task_v2($file['file_name']);
                        $ret['files'][$file['file_name']]['status']='need_download';
                        $ret['files'][$file['file_name']]['size']=$file['size'];
                        //$ret['files'][$file['file_name']]['md5']=$file['md5'];
                    }
                }
            }

            if($b_not_found)
            {
                $ret['result']=WPVIVID_FAILED;
                if($b_test)
                    $ret['error']='Backup files doesn\'t exist. Restore failed.';
                else
                    $ret['error']='Backup doesn\'t exist in both web server and remote storage. Restore failed.';
            }
            else if($b_need_download)
            {
                $ret['result']='need_download';
            }
            else
            {
                $ret['result']=WPVIVID_SUCCESS;
            }
        }
        else
        {
            $ret['result']=WPVIVID_FAILED;
            $ret['error']='Unknown error.';
        }

        return $ret;
    }

    public function check_migrate_file()
    {
        if(isset($this->config['backup']['files']))
        {
            $tmp_data = $this->config['backup']['files'];
            $zip=new WPvivid_ZipClass();

            foreach ($tmp_data as $file)
            {
                $path=$this->get_backup_path($file['file_name']);//$this->get_local_path().$file['file_name'];
                if(file_exists($path))
                {
                    $ret=$zip->get_json_data($path);
                    if($ret['result'] === WPVIVID_SUCCESS) {
                        $json=$ret['json_data'];
                        $json = json_decode($json, 1);
                        if (!is_null($json)) {
                            if (isset($json['home_url']) && home_url() != $json['home_url']) {
                                return 1;
                            }
                        }
                        else{
                            return 0;
                        }
                    }
                    elseif($ret['result'] === WPVIVID_FAILED){
                        return 0;
                    }
                }
            }
            return 0;
        }
        else
        {
            return 0;
        }

    }

    public function is_display_migrate_option(){
        if(isset($this->config['backup']['files']))
        {
            $tmp_data = $this->config['backup']['files'];
            $zip=new WPvivid_ZipClass();

            foreach ($tmp_data as $file)
            {
                $path=$this->get_backup_path($file['file_name']);//$this->get_local_path().$file['file_name'];
                if(file_exists($path))
                {
                    $ret=$zip->get_json_data($path);
                    if($ret['result'] === WPVIVID_SUCCESS) {
                        $json=$ret['json_data'];
                        $json = json_decode($json, 1);
                        if (!is_null($json)) {
                            if (isset($json['home_url'])){
                                return false;
                            }
                            else{
                                return true;
                            }
                        }
                        else{
                            return true;
                        }
                    }
                    elseif($ret['result'] === WPVIVID_FAILED){
                        return true;
                    }
                }
            }
            return true;
        }
        else
        {
            return true;
        }
    }

    public function get_local_path()
    {
        $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$this->config['local']['path'].DIRECTORY_SEPARATOR;
        $path=apply_filters('wpvivid_get_site_wpvivid_path',$path,$this->config['local']['path']);
        return $path;
    }

    public function get_local_url()
    {
        $url=content_url().DIRECTORY_SEPARATOR.$this->config['local']['path'].DIRECTORY_SEPARATOR;
        $url=apply_filters('wpvivid_get_site_wpvivid_url',$url,$this->config['local']['path']);
        return $url;
    }

    public function get_remote()
    {
        $remote_option=array_shift($this->config['remote']);

        if(is_null($remote_option))
        {
            return false;
        }
        else
        {
            return $remote_option;
        }
    }

    public function get_backup_packages()
    {
        $packages=array();
        $index=0;

        if(isset($this->config['backup']['files']))
        {
            $db_package=array();
            $file_added=array();
            //file_name
            foreach ($this->config['backup']['files'] as $file)
            {
                if(isset($file_added[$file['file_name']]))
                {
                    continue;
                }

                if (preg_match('/wpvivid-.*_.*_.*\.part[0-9]+\.zip$/', $file['file_name'],$matches))
                {
                    $this->get_all_part_files($file['file_name'],$this->config['backup']['files'],$packages[$index],$file_added);
                }
                else
                {
                    if($this->check_file_is_a_db_package($file['file_name']))
                    {
                        $db_package['files'][]=$file['file_name'];
                    }
                    else
                    {
                        $packages[$index]['files'][]=$file['file_name'];
                    }
                    $file_added[$file['file_name']]=1;
                }
                $index++;
            }

            $file_added=array();
            $child_packages=array();

            foreach ($packages as $key=>$package)
            {
                $files=array();

                foreach ($package['files'] as $package_files)
                {
                    $files=array_merge($files,$this->get_child_files($package_files));
                }

                if(empty($files))
                {
                    continue;
                }

                foreach ($files as $file)
                {
                    if (isset($file_added[$file['file_name']]))
                    {
                        continue;
                    }

                    if (preg_match('/wpvivid-.*_.*_.*\.part[0-9]+\.zip$/', $file['file_name'],$matches))
                    {
                        $this->get_all_part_files($file['file_name'],$files,$child_packages[$index],$file_added);
                    }
                    else
                    {
                        if($this->check_file_is_a_db_package($file['file_name']))
                        {
                            $db_package['files'][]=$file['file_name'];
                        }
                        else
                        {
                            $child_packages[$index]['files'][]=$file['file_name'];
                        }
                        $file_added[$file['file_name']]=1;
                    }
                    $index++;
                }
            }

            $packages=array_merge($packages,$child_packages);
            if(!empty($db_package))
            {
                $packages[$index]=$db_package;
            }
        }
        else if(isset($this->config['backup']['data']))
        {
            if(isset($this->config['backup']['ismerge'])&&$this->config['backup']['ismerge']==1)
            {
                $packages[$index]['option']['has_child']=1;
                //$packages[$index]['option']['root']='wp-content';
                $packages[$index]['option']['root_flag']=WPVIVID_BACKUP_ROOT_WP_CONTENT;
                foreach ($this->config['backup']['data']['meta']['files'] as $file)
                {
                    $packages[$index]['files'][]=$file['file_name'];
                }
                $index++;
            }

            foreach ($this->config['backup']['data']['type'] as $type)
            {
                if($type['type_name']=='backup_db')
                {
                    $packages[$index]['option']['dump_db']=1;
                    //$packages[$index]['option']['root']='wp-content\\'.$this->config['local']['path'];
                    $packages[$index]['option']['root_flag']=WPVIVID_BACKUP_ROOT_CUSTOM;
                }
                else if($type['type_name']=='backup_themes')
                {
                    //$packages[$index]['option']['root']='wp-content';
                    $packages[$index]['option']['root_flag']=WPVIVID_BACKUP_ROOT_WP_CONTENT;
                }
                else if($type['type_name']=='backup_plugin')
                {
                    //$packages[$index]['option']['root']='wp-content';
                    $packages[$index]['option']['root_flag']=WPVIVID_BACKUP_ROOT_WP_CONTENT;
                }
                else if($type['type_name']=='backup_uploads')
                {
                    //$packages[$index]['option']['root']='wp-content';
                    $packages[$index]['option']['root_flag']=WPVIVID_BACKUP_ROOT_WP_CONTENT;
                }
                else if($type['type_name']=='backup_content')
                {
                    //$packages[$index]['option']['root']='';
                    $packages[$index]['option']['root_flag']=WPVIVID_BACKUP_ROOT_WP_ROOT;
                }
                else if($type['type_name']=='backup_core')
                {
                    //$packages[$index]['option']['root']='';
                    $packages[$index]['option']['root_flag']=WPVIVID_BACKUP_ROOT_WP_ROOT;
                    $packages[$index]['option']['include_path'][]='wp-includes';
                    $packages[$index]['option']['include_path'][]='wp-admin';
                }

                foreach ($type['files'] as $file)
                {
                    $packages[$index]['files'][]=$file['file_name'];
                }
                $index++;
            }
        }
        return $packages;
    }

    public function check_file_is_a_db_package($file_name)
    {
        //backup_db.zip
        if (preg_match('#.*_backup_db.zip?#', $file_name, $matches))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_all_part_files($file_name,$files,&$package,&$file_added)
    {
        if (preg_match('#\.part[0-9]+\.zip$#',$file_name,$matches))
        {
            $prefix=$matches[0];
            $file_prefix=substr($file_name,0,strlen($file_name)-strlen($prefix));
            foreach ($files as $file)
            {
                if(isset($file_added[$file['file_name']]))
                {
                    continue;
                }

                if (strpos($file['file_name'], $file_prefix) !== false)
                {
                    $package['files'][]=$file['file_name'];
                    $file_added[$file['file_name']]=1;
                }
            }
        }
    }

    public function get_child_files($file_name)
    {
        $zip=new WPvivid_ZipClass();

        $path=$this->get_backup_path($file_name);//$this->get_local_path().$file_name;

        $files = array();

        $ret=$zip->get_json_data($path);

        if($ret['result'] === WPVIVID_SUCCESS) {
            $json=$ret['json_data'];
            $json = json_decode($json, 1);
            if (isset($json['has_child'])) {
                $files = $zip->list_file($path);
            }
        }
        return $files;
    }

    public function get_file_info($file_name)
    {
        $zip=new WPvivid_ZipClass();

        $path=$this->get_backup_path($file_name);//$this->get_local_path().$file_name;

        $ret=$zip->get_json_data($path);
        if($ret['result'] === WPVIVID_SUCCESS) {
            $json=$ret['json_data'];
            $json = json_decode($json, 1);
            if (is_null($json)) {
                return false;
            } else {
                return $json;
            }
        }
        elseif($ret['result'] === WPVIVID_FAILED){
            return false;
        }
    }

    static public function get_backup_file_info($file_name)
    {
        $zip=new WPvivid_ZipClass();
        $ret=$zip->get_json_data($file_name);
        if($ret['result'] === WPVIVID_SUCCESS)
        {
            $json=$ret['json_data'];
            $json = json_decode($json, 1);
            if (is_null($json)) {
                return array('result'=>WPVIVID_FAILED,'error'=>'Failed to decode json');
            } else {
                return array('result'=>WPVIVID_SUCCESS,'json_data'=>$json);
            }
        }
        elseif($ret['result'] === WPVIVID_FAILED){
            return $ret;
        }
    }

    public function get_sql_file($file_name)
    {
        $zip=new WPvivid_ZipClass();
        $path=$this->get_backup_path($file_name);//$this->get_local_path().$file_name;
        $files=$zip->list_file($path);
        return $files[0]['file_name'];
    }

    public static function get_backup_files($backup){
        $files=array();
        if(isset($backup['backup']['files'])){
            $files=$backup['backup']['files'];
        }
        else{
            if(isset($backup['backup']['ismerge'])) {
                if ($backup['backup']['ismerge'] == 1) {
                    if(isset($backup['backup']['data']['meta']['files'])){
                        $files=$backup['backup']['data']['meta']['files'];
                    }
                }
            }
        }
        asort($files);
        uasort($files, function ($a, $b) {
            $file_name_1 = $a['file_name'];
            $file_name_2 = $b['file_name'];
            $index_1 = 0;
            if(preg_match('/wpvivid-.*_.*_.*\.part.*\.zip$/', $file_name_1)) {
                if (preg_match('/part.*$/', $file_name_1, $matches)) {
                    $index_1 = $matches[0];
                    $index_1 = preg_replace("/part/","", $index_1);
                    $index_1 = preg_replace("/.zip/","", $index_1);
                }
            }
            $index_2 = 0;
            if(preg_match('/wpvivid-.*_.*_.*\.part.*\.zip$/', $file_name_2)) {
                if (preg_match('/part.*$/', $file_name_2, $matches)) {
                    $index_2 = $matches[0];
                    $index_2 = preg_replace("/part/", "", $index_2);
                    $index_2 = preg_replace("/.zip/", "", $index_2);
                }
            }
            if($index_1 !== 0 && $index_2 === 0){
                return -1;
            }
            if($index_1 === 0 && $index_2 !== 0){
                return 1;
            }
        });
        return $files;
    }

    public function get_download_backup_files($backup_id){
        $ret['result']=WPVIVID_FAILED;
        $data=array();
        $backup=WPvivid_Backuplist::get_backup_by_id($backup_id);
        if(!$backup)
        {
            $ret['error']='Backup id not found.';
            return $ret;
        }

        $files=array();
        $files = self::get_backup_files($backup);
        if(empty($files)){
            $ret['error']='Failed to get backup files.';
        }
        else{
            $ret['result']=WPVIVID_SUCCESS;
            $ret['files']=$files;
        }
        return $ret;
    }

    public function get_download_progress($backup_id, $files){
        global $wpvivid_plugin;
        $b_need_download=false;
        $b_not_found=false;
        $file_count=0;
        $file_part_num=1;
        $check_type='';
        foreach ($files as $file)
        {
            $need_download=false;
            $path=$this->get_backup_path($file['file_name']);//$this->get_local_path().$file['file_name'];
            $download_url=content_url().DIRECTORY_SEPARATOR.$this->config['local']['path'].DIRECTORY_SEPARATOR.$file['file_name'];
            if(file_exists($path)) {
                if(filesize($path) == $file['size']){
                    if($wpvivid_plugin->wpvivid_check_zip_valid()) {
                        $res = TRUE;
                    }
                    else{
                        $res = FALSE;
                    }
                }
                else{
                    $res = FALSE;
                }
                if ($res !== TRUE)
                {
                    $need_download=true;
                }
            }
            else {
                $need_download=true;
            }
            if($file_part_num < 10){
                $format_part=sprintf("%02d", $file_part_num);
            }
            else{
                $format_part=$file_part_num;
            }
            if($need_download) {
                if(empty($this->config['remote'])) {
                    $b_not_found=true;
                    $ret['result'] = WPVIVID_SUCCESS;
                    $ret['files'][$file['file_name']]['status']='file_not_found';
                }
                else{
                    $task = WPvivid_taskmanager::get_download_task_v2($file['file_name']);
                    $ret['task']=$task;
                    if ($task === false) {
                        $ret['result'] = WPVIVID_SUCCESS;
                        $ret['files'][$file['file_name']]['status']='need_download';
                        $ret['files'][$file['file_name']]['html']='<div style="float:left;margin:10px 10px 10px 0;text-align:center; width:180px;">
                                                                  <span>Part'.$format_part.'</span></br>
                                                                  <span id=\''.$backup_id.'-text-part-'.$file_part_num.'\'><a onclick="wpvivid_prepare_download(\''.$file_part_num.'\', \''.$backup_id.'\', \''.$file['file_name'].'\');" style="cursor: pointer;">Prepare to Download</a></span></br>
                                                                  <div style="width:100%;height:5px; background-color:#dcdcdc;"><div id=\''.$backup_id.'-progress-part-'.$file_part_num.'\' style="background-color:#0085ba; float:left;width:0;height:5px;"></div></div>
                                                                  <span>size:</span><span>'.$wpvivid_plugin->formatBytes($file['size']).'</span>
                                                                  </div>';
                    } else {
                        $ret['result'] = WPVIVID_SUCCESS;
                        if($task['status'] === 'running'){
                            $ret['files'][$file['file_name']]['status'] = 'running';
                            $ret['files'][$file['file_name']]['html']='<div style="float:left;margin:10px 10px 10px 0;text-align:center; width:180px;">
                                                                            <span>Part'.$format_part.'</span></br>
                                                                            <span id=\''.$backup_id.'-text-part-'.$file_part_num.'\'><a >Retriving(remote storage to web server)</a></span></br>
                                                                            <div style="width:100%;height:5px; background-color:#dcdcdc;"><div id=\''.$backup_id.'-progress-part-'.$file_part_num.'\' style="background-color:#0085ba; float:left;width:'.$task['progress_text'].'%;height:5px;"></div></div>
                                                                            <span>size:</span><span>'.$wpvivid_plugin->formatBytes($file['size']).'</span>
                                                                            </div>';
                            $ret['files'][$file['file_name']]['progress_text']=$task['progress_text'];
                        }
                        elseif($task['status'] === 'timeout'){
                            $ret['files'][$file['file_name']]['status']='timeout';
                            $ret['files'][$file['file_name']]['html']='<div style="float:left;margin:10px 10px 10px 0;text-align:center; width:180px;">
                                                                            <span>Part'.$format_part.'</span></br>
                                                                            <span id=\''.$backup_id.'-text-part-'.$file_part_num.'\'><a onclick="wpvivid_prepare_download(\''.$file_part_num.'\', \''.$backup_id.'\', \''.$file['file_name'].'\');" style="cursor: pointer;">Prepare to Download</a></span></br>
                                                                            <div style="width:100%;height:5px; background-color:#dcdcdc;"><div id=\''.$backup_id.'-progress-part-'.$file_part_num.'\' style="background-color:#0085ba; float:left;width:'.$task['progress_text'].'%;height:5px;"></div></div>
                                                                            <span>size:</span><span>'.$wpvivid_plugin->formatBytes($file['size']).'</span>
                                                                            </div>';
                            $ret['files'][$file['file_name']]['progress_text']=$task['progress_text'];
                            WPvivid_taskmanager::delete_download_task_v2($file['file_name']);
                        }
                        elseif($task['status'] === 'completed'){
                            $ret['files'][$file['file_name']]['status']='completed';
                            $ret['files'][$file['file_name']]['html']='<div style="float:left;margin:10px 10px 10px 0;text-align:center; width:180px;">
                                                                 <span>Part'.$format_part.'</span></br>
                                                                 <span id=\''.$backup_id.'-text-part-'.$file_part_num.'\'><a onclick="wpvivid_download(\''.$backup_id.'\', \''.$check_type.'\', \''.$file['file_name'].'\');" style="cursor: pointer;">Download</a></span></br>
                                                                 <div style="width:100%;height:5px; background-color:#dcdcdc;"><div id=\''.$backup_id.'-progress-part-'.$file_part_num.'\' style="background-color:#0085ba; float:left;width:100%;height:5px;"></div></div>
                                                                 <span>size:</span><span>'.$wpvivid_plugin->formatBytes($file['size']).'</span>
                                                                 </div>';
                            WPvivid_taskmanager::delete_download_task_v2($file['file_name']);
                        }
                        elseif($task['status'] === 'error'){
                            $ret['files'][$file['file_name']]['status']='error';
                            $ret['files'][$file['file_name']]['html']='<div style="float:left;margin:10px 10px 10px 0;text-align:center; width:180px;">
                                                                        <span>Part'.$format_part.'</span></br>
                                                                        <span id=\''.$backup_id.'-text-part-'.$file_part_num.'\'><a onclick="wpvivid_prepare_download(\''.$file_part_num.'\', \''.$backup_id.'\', \''.$file['file_name'].'\');" style="cursor: pointer;">Prepare to Download</a></span></br>
                                                                        <div style="width:100%;height:5px; background-color:#dcdcdc;"><div id=\''.$backup_id.'-progress-part-'.$file_part_num.'\' style="background-color:#0085ba; float:left;width:0;height:5px;"></div></div>
                                                                        <span>size:</span><span>'.$wpvivid_plugin->formatBytes($file['size']).'</span>
                                                                        </div>';
                            $ret['files'][$file['file_name']]['error'] = $task['error'];
                            WPvivid_taskmanager::delete_download_task_v2($file['file_name']);
                        }
                    }
                }
            }
            else{
                $ret['result'] = WPVIVID_SUCCESS;
                if(WPvivid_taskmanager::get_download_task_v2($file['file_name']))
                    WPvivid_taskmanager::delete_download_task_v2($file['file_name']);
                $ret['files'][$file['file_name']]['status']='completed';
                $ret['files'][$file['file_name']]['download_path']=$path;
                $ret['files'][$file['file_name']]['download_url']=$download_url;
                $ret['files'][$file['file_name']]['html']='<div style="float:left;margin:10px 10px 10px 0;text-align:center; width:180px;">
                                                                 <span>Part'.$format_part.'</span></br>
                                                                 <span id=\''.$backup_id.'-text-part-'.$file_part_num.'\'><a onclick="wpvivid_download(\''.$backup_id.'\', \''.$check_type.'\', \''.$file['file_name'].'\');" style="cursor: pointer;">Download</a></span></br>
                                                                 <div style="width:100%;height:5px; background-color:#dcdcdc;"><div id=\''.$backup_id.'-progress-part-'.$file_part_num.'\' style="background-color:#0085ba; float:left;width:100%;height:5px;"></div></div>
                                                                 <span>size:</span><span>'.$wpvivid_plugin->formatBytes($file['size']).'</span>
                                                                 </div>';
            }
            $ret['files'][$file['file_name']]['size']=$wpvivid_plugin->formatBytes($file['size']);
            $file_count++;
            $file_part_num++;
        }
        if ($file_count % 2 != 0) {
            $file_count++;
            if($file_count < 10){
                $format_part=sprintf("%02d", $file_count);
            }
            else{
                $format_part=$file_count;
            }
            $ret['place_html']='<div style="float:left;margin:10px 10px 10px 0;text-align:center; width:180px; color:#cccccc;">
                                   <span>Part'.$format_part.'</span></br>
                                   <span>Download</span></br>
                                   <div style="width:100%;height:5px; background-color:#dcdcdc;"><div style="background-color:#0085ba; float:left;width:0;height:5px;"></div></div>
                                   <span>size:</span><span>0</span>
                                   </div>';
        }
        else{
            $ret['place_html']='';
        }
        return $ret;
    }

    public function update_download_page($backup_id){
        $ret=$this->get_download_backup_files($backup_id);
        if($ret['result']==WPVIVID_SUCCESS){
            $ret=$this->get_download_progress($backup_id, $ret['files']);
            WPvivid_taskmanager::update_download_cache($backup_id,$ret);
        }
        return $ret;
    }

    public function cleanup_local_backup()
    {
        $files=array();
        $download_dir=$this->config['local']['path'];
        $file=$this->get_files(false);

        foreach ($file as $filename)
        {
            $files[] = $filename;
        }

        foreach ($files as $file)
        {
            $download_path = WP_CONTENT_DIR .DIRECTORY_SEPARATOR . $download_dir . DIRECTORY_SEPARATOR . $file;
            if (file_exists($download_path))
            {
                @unlink($download_path);
            }
            else{
                $backup_dir=WPvivid_Setting::get_backupdir();
                $download_path = WP_CONTENT_DIR .DIRECTORY_SEPARATOR . $backup_dir . DIRECTORY_SEPARATOR . $file;
                if (file_exists($download_path))
                {
                    @unlink($download_path);
                }
            }
        }
    }

    public function cleanup_remote_backup()
    {
        if(!empty($this->config['remote']))
        {
            $files=$this->get_files(false);

            foreach($this->config['remote'] as $remote)
            {
                WPvivid_downloader::delete($remote,$files);
            }
        }
    }
}

class WPvivid_Backup
{
    public $task;
    public $backup_type_report = '';
    //public $config;

    public function __construct($task_id=false,$task=false)
    {
        if($task_id!==false)
        {
            $this->task=new WPvivid_Backup_Task($task_id);
        }
        else if($task!==false)
        {
            $this->task=new WPvivid_Backup_Task(false,$task);
        }
        else
        {
            $this->task=new WPvivid_Backup_Task();
        }
        //$this->config=$config;
    }

	public function init_options($task_id)
    {
        $this->task=new WPvivid_Backup_Task($task_id);
    }

	public function backup($task_id)
    {
        $this->init_options($task_id);
        $next_backup=$this->task->get_need_backup();

        $this->backup_type_report = '';

        while($next_backup!==false)
        {
            global $wpvivid_plugin;
            $wpvivid_plugin->set_time_limit($this->task->get_id());
            $this->task->update_sub_task_progress($next_backup['key'],0, sprintf(__('Start backing up %s.', 'wpvivid-backuprestore'), $next_backup['key']));
            $wpvivid_plugin->wpvivid_log->WriteLog('Prepare to backup '.$next_backup['key'].' files.','notice');
            $this->backup_type_report .= $next_backup['key'].',';
            if(isset($next_backup['files'])) {
                $wpvivid_plugin->wpvivid_log->WriteLog('File number: ' . sizeof($next_backup['files']), 'notice');
            }
            $result = $this->_backup($next_backup);
            $wpvivid_plugin->wpvivid_log->WriteLog('Backing up '.$next_backup['key'].' completed.','notice');
            $this->task->update_sub_task_progress($next_backup['key'],1, sprintf(__('Backing up %s finished.', 'wpvivid-backuprestore'), $next_backup['key']));
            $this->task->update_backup_result($next_backup,$result);
            $wpvivid_plugin->check_cancel_backup($task_id);
            unset($next_backup);
            $next_backup=$this->task->get_need_backup();
        }

        WPvivid_Setting::update_option('wpvivid_backup_report', $this->backup_type_report);
        $this->cleanup();

        $ret=$this->task->get_backup_result();
        return $ret;
	}

    private function _backup($data)
    {
        global $wpvivid_plugin;
        $result['result']=WPVIVID_FAILED;
        $result['error']='test error';

        $is_type_db = false;
        $is_type_db = apply_filters('wpvivid_check_type_database', $is_type_db, $data);
        if($is_type_db)
        {
            include_once WPVIVID_PLUGIN_DIR .'/includes/class-wpvivid-backup-database.php';
            $wpvivid_plugin->wpvivid_log->WriteLog('Start exporting database.','notice');
            $backup_database = new WPvivid_Backup_Database();
            $result = $backup_database -> backup_database($data,$this->task->get_id());
            $wpvivid_plugin->wpvivid_log->WriteLog('Exporting database finished.','notice');
            if($result['result']==WPVIVID_SUCCESS)
            {
                $data['files']=$result['files'];
            }
            else
            {
                return $result;
            }
        }

        include_once WPVIVID_PLUGIN_DIR . '/includes/class-wpvivid-zipclass.php';

        $zip = new WPvivid_ZipClass();
        if(is_array($zip->last_error))
        {
            return $zip->last_error;
        }

        if(isset($data['resume_packages']))
        {
            global $wpvivid_plugin;
            $wpvivid_plugin->wpvivid_log->WriteLog('Start compressing '.$data['key'],'notice');

            $packages=$this->task->get_packages_info($data['key']);
            if($packages===false)
            {
                if(isset($data['plugin_subpackage']))
                {
                    $ret =$zip->get_plugin_packages($data);
                }
                else if(isset($data['uploads_subpackage']))
                {
                    $ret =$zip->get_upload_packages($data);
                }
                else
                {
                    if($data['key']==WPVIVID_BACKUP_TYPE_MERGE)
                        $ret =$zip->get_packages($data,true);
                    else
                        $ret =$zip->get_packages($data);
                }

                $packages=$this->task->set_packages_info($data['key'],$ret['packages']);
            }

            $temp_dir = $data['path'].'temp-'.$data['prefix'].DIRECTORY_SEPARATOR;
            define(PCLZIP_TEMPORARY_DIR,$temp_dir);

            $result['result']=WPVIVID_SUCCESS;
            $result['files']=array();
            foreach ($packages as $package)
            {
                $wpvivid_plugin->set_time_limit($this->task->get_id());
                if(!empty($package['files'])&&$package['backup']==false)
                {
                    if(isset($data['uploads_subpackage']))
                    {
                        $files=$zip->get_upload_files_from_cache($package['files']);
                    }
                    else
                    {
                        $files=$package['files'];
                    }

                    if(empty($files))
                        continue;

                    $zip_ret=$zip->_zip($package['path'],$files, $data,$package['json']);
                    if($zip_ret['result']==WPVIVID_SUCCESS)
                    {
                        if(isset($data['uploads_subpackage']))
                        {
                            if(file_exists($package['files']))
                            {
                                @unlink($package['files']);
                            }
                        }

                        $result['files'][] = $zip_ret['file_data'];
                        $package['backup']=true;
                        $this->task->update_packages_info($data['key'],$package,$zip_ret['file_data']);
                        if($data['key']==WPVIVID_BACKUP_TYPE_MERGE)
                        {
                            $this->cleanup_finished_package($package['files']);
                        }
                    }
                    else
                    {
                        $result=$zip_ret;
                        break;
                    }
                }else {
                    continue;
                }
            }
            $wpvivid_plugin->wpvivid_log->WriteLog('Compressing '.$data['key'].' completed','notice');
            return $result;
        }
        else
        {
            $is_additional_db = false;
            $is_additional_db = apply_filters('wpvivid_check_additional_database', $is_additional_db, $data);
            if($is_additional_db){
                $result =$zip->compress_additional_database($data);
            }
            else {
                $result =$zip->compress($data);
            }

            if($is_type_db)
            {
                foreach ($data['files'] as $sql_file)
                {
                    @unlink($sql_file);
                }
            }
        }

        return $result;
    }

    public function cleanup()
    {
        $files=$this->task->get_need_cleanup_files();

        foreach ($files as $file)
        {
            if(file_exists($file)) {
                global $wpvivid_plugin;
                $wpvivid_plugin->wpvivid_log->WriteLog('Cleaned up file, filename: '.$file,'notice');
                @unlink($file);
            }
        }
    }

    public function cleanup_finished_package($files)
    {
        foreach ($files as $file)
        {
            if(file_exists($file))
            {
                global $wpvivid_plugin;
                $wpvivid_plugin->wpvivid_log->WriteLog('Cleaned up file, filename: '.$file,'notice');
                @unlink($file);
            }
        }
    }

    public function clean_backup()
    {
        $path = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir();
        $handler=opendir($path);
        if($handler!==false)
        {
            while(($filename=readdir($handler))!==false)
            {
                if(preg_match('#'.$this->task->get_id().'#',$filename) || preg_match('#'.apply_filters('wpvivid_fix_wpvivid_free', $this->task->get_id()).'#',$filename))
                {
                    @unlink($path.DIRECTORY_SEPARATOR.$filename);
                }
            }
            @closedir($handler);
        }
    }

    public function clearcache()
    {
        $task_id=$this->task->get_prefix();
        $path = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir();
        $handler=opendir($path);
        if($handler!==false)
        {
            while(($filename=readdir($handler))!==false)
            {
                if(is_dir($path.DIRECTORY_SEPARATOR.$filename) && preg_match('#temp-'.$task_id.'#',$filename))
                {
                    $this->deldir($path.DIRECTORY_SEPARATOR.$filename,'',true);
                }
                if(is_dir($path.DIRECTORY_SEPARATOR.$filename) && preg_match('#temp-'.$task_id.'#',$filename))
                {
                    $this->deldir($path.DIRECTORY_SEPARATOR.$filename,'',true);
                }
                if(preg_match('#pclzip-.*\.tmp#', $filename)){
                    @unlink($path.DIRECTORY_SEPARATOR.$filename);
                }
                if(preg_match('#pclzip-.*\.gz#', $filename)){
                    @unlink($path.DIRECTORY_SEPARATOR.$filename);
                }
            }
            @closedir($handler);
        }

    }

    public function deldir($path,$exclude=array(),$flag = false)
    {
        if(!is_dir($path))
        {
            return ;
        }
        $handler=opendir($path);
        if(empty($handler))
            return ;
        while(($filename=readdir($handler))!==false)
        {
            if($filename != "." && $filename != "..")
            {
                if(is_dir($path.DIRECTORY_SEPARATOR.$filename)){
                    if(empty($exclude)||$this->regex_match($exclude['directory'],$path.DIRECTORY_SEPARATOR.$filename ,0)){
                        $this->deldir( $path.DIRECTORY_SEPARATOR.$filename ,$exclude, $flag);
                        @rmdir( $path.DIRECTORY_SEPARATOR.$filename );
                    }
                }else{
                    if(empty($exclude)||$this->regex_match($exclude['file'],$path.DIRECTORY_SEPARATOR.$filename ,0)){
                        @unlink($path.DIRECTORY_SEPARATOR.$filename);
                    }
                }
            }
        }
        if($handler)
            @closedir($handler);
        if($flag)
            @rmdir($path);
    }

    public function regex_match($regex_array,$string,$mode)
    {
        if(empty($regex_array))
        {
            return true;
        }

        if($mode==0)
        {
            foreach ($regex_array as $regex)
            {
                if(preg_match($regex,$string))
                {
                    return false;
                }
            }

            return true;
        }

        if($mode==1)
        {
            foreach ($regex_array as $regex)
            {
                if(preg_match($regex,$string))
                {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    public function clean_remote_backup($remotes,$files)
    {
        $remote_option=array_shift($remotes);

        if(!is_null($remote_option))
        {
            global $wpvivid_plugin;

            $remote=$wpvivid_plugin->remote_collection->get_remote($remote_option);
            $remote ->cleanup($files);
        }
    }
}