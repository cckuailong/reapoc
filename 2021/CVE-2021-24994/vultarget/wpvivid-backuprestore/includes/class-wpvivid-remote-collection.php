<?php
/**
 * Created by PhpStorm.
 * User: alienware`x
 * Date: 2019/1/22
 * Time: 9:19
 */

require_once WPVIVID_PLUGIN_DIR .'/includes/customclass/class-wpvivid-remote-default.php';
require_once WPVIVID_PLUGIN_DIR .'/includes/customclass/class-wpvivid-ftpclass.php';
require_once WPVIVID_PLUGIN_DIR. '/includes/customclass/class-wpvivid-sftpclass.php';
require_once WPVIVID_PLUGIN_DIR . '/includes/customclass/class-wpvivid-amazons3-plus.php';
require_once WPVIVID_PLUGIN_DIR . '/includes/customclass/class-wpvivid-google-drive.php';
require_once WPVIVID_PLUGIN_DIR . '/includes/customclass/class-wpvivid-dropbox.php';
require_once WPVIVID_PLUGIN_DIR . '/includes/customclass/class-wpvivid-one-drive.php';
require_once WPVIVID_PLUGIN_DIR . '/includes/customclass/class-wpvivid-s3compat.php';
require_once WPVIVID_PLUGIN_DIR . '/includes/customclass/class-wpvivid-send-to-site.php';

class WPvivid_Remote_collection
{
    private $remote_collection=array();

    public function __construct()
    {
        add_filter('wpvivid_remote_register', array($this, 'init_remotes'),10);
        $this->remote_collection=apply_filters('wpvivid_remote_register',$this->remote_collection);
        $this->load_hooks();
    }

    public function get_remote($remote)
    {
        if(is_array($remote)&&array_key_exists('type',$remote)&&array_key_exists($remote['type'],$this->remote_collection))
        {
            $class_name =$this->remote_collection[$remote['type']];

            if(class_exists($class_name))
            {
                $object = new $class_name($remote);
                return $object;
            }
        }
        $object = new $this ->remote_collection['default']();
        return  $object;
    }

    public function add_remote($remote_option)
    {
        $remote=$this->get_remote($remote_option);

        $ret=$remote->sanitize_options();

        if($ret['result']=='success')
        {
            $remote_option=$ret['options'];
            $ret=$remote->test_connect();
            if($ret['result']=='success')
            {
                $ret=array();
                $default=$remote_option['default'];
                $id=WPvivid_Setting::add_remote_options($remote_option);
                if($default==1)
                {
                    $remote_ids[]=$id;
                    $remote_ids=apply_filters('wpvivid_before_add_user_history',$remote_ids);
                    WPvivid_Setting::update_user_history('remote_selected',$remote_ids);
                    $schedule_data = WPvivid_Setting::get_option('wpvivid_schedule_setting');
                    if(!empty($schedule_data['enable'])) {
                        if ($schedule_data['enable'] == 1) {
                            $schedule_data['backup']['local'] = 0;
                            $schedule_data['backup']['remote'] = 1;
                        }
                        WPvivid_Setting::update_option('wpvivid_schedule_setting', $schedule_data);
                    }
                }
                $ret['result']=WPVIVID_SUCCESS;
            }
            else {
                $id = uniqid('wpvivid-');
                $log_file_name = $id . '_add_remote';
                $log = new WPvivid_Log();
                $log->CreateLogFile($log_file_name, 'no_folder', 'Add Remote Test Connection');
                $log->WriteLog('Remote Type: '.$remote_option['type'], 'notice');
                if(isset($ret['error'])) {
                    $log->WriteLog($ret['error'], 'notice');
                }
                $log->CloseFile();
                WPvivid_error_log::create_error_log($log->log_file);
            }
        }

        return $ret;
    }

    public function update_remote($id,$remote_option)
    {
        $remote=$this->get_remote($remote_option);

        $old_remote=WPvivid_Setting::get_remote_option($id);

        $ret=$remote->sanitize_options($old_remote['name']);
        if($ret['result']=='success')
        {
            $remote_option=$ret['options'];
            $ret=$remote->test_connect();
            if($ret['result']=='success')
            {
                $ret=array();
                WPvivid_Setting::update_remote_option($id,$remote_option);
                $ret['result']=WPVIVID_SUCCESS;
            }
        }

        return $ret;
    }

    public function init_remotes($remote_collection)
    {
        $remote_collection['default'] = 'WPvivid_Remote_Defult';
        $remote_collection['sftp']='WPvivid_SFTPClass';
        $remote_collection['ftp']='WPvivid_FTPClass';
        $remote_collection['amazons3']='WPvivid_AMAZONS3Class';
        $remote_collection[WPVIVID_REMOTE_GOOGLEDRIVE] = 'Wpvivid_Google_drive';
        $remote_collection['dropbox']='WPvivid_Dropbox';
        $remote_collection[WPVIVID_REMOTE_ONEDRIVE] = 'Wpvivid_one_drive';
        $remote_collection[WPVIVID_REMOTE_S3COMPAT] = 'Wpvivid_S3Compat';
        $remote_collection[WPVIVID_REMOTE_SEND_TO_SITE] = 'WPvivid_Send_to_site';
        return $remote_collection;
    }

    public function load_hooks()
    {
        foreach ($this->remote_collection as $class_name)
        {
            $object = new $class_name();
        }
    }
}