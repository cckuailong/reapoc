<?php

if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Upload
{
    public $task_id;

    public function upload($task_id,$remote_option=null)
    {
        global $wpvivid_plugin;
        $this->task_id=$task_id;
        $task=new WPvivid_Backup_Task($task_id);
        $files=$task->get_backup_files();
        WPvivid_taskmanager::update_backup_main_task_progress($this->task_id,'upload',0,0);

        if(is_null($remote_option))
        {
            $remote_options=WPvivid_taskmanager::get_task_options($this->task_id,'remote_options');

            if(sizeof($remote_options)>1)
            {
                $result=array('result' => WPVIVID_FAILED , 'error' => 'not support multi remote storage');
                $result= apply_filters('wpvivid_upload_files_to_multi_remote',$result,$task_id);

                if($result['result']==WPVIVID_SUCCESS)
                {
                    WPvivid_taskmanager::update_backup_main_task_progress($this->task_id,'upload',100,1);
                    WPvivid_taskmanager::update_backup_task_status($task_id,false,'completed');
                    return array('result' => WPVIVID_SUCCESS);
                }
                else
                {
                    WPvivid_taskmanager::update_backup_task_status($this->task_id,false,'error',false,false,$result['error']);
                    return array('result' => WPVIVID_FAILED , 'error' => $result['error']);
                }
            }
            else
            {
                $remote_option=array_shift($remote_options);

                if(is_null($remote_option))
                {
                    return array('result' => WPVIVID_FAILED , 'error' => 'not select remote storage');
                }

                $remote=$wpvivid_plugin->remote_collection->get_remote($remote_option);

                $result=$remote->upload($this->task_id,$files,array($this,'upload_callback'));

                if($result['result']==WPVIVID_SUCCESS)
                {
                    WPvivid_taskmanager::update_backup_main_task_progress($this->task_id,'upload',100,1);
                    WPvivid_taskmanager::update_backup_task_status($task_id,false,'completed');
                    return array('result' => WPVIVID_SUCCESS);
                }
                else
                {
                    $remote ->cleanup($files);

                    WPvivid_taskmanager::update_backup_task_status($this->task_id,false,'error',false,false,$result['error']);
                    return array('result' => WPVIVID_FAILED , 'error' => $result['error']);
                }
            }
        }
        else
        {
            $remote=$wpvivid_plugin->remote_collection->get_remote($remote_option);

            $result=$remote->upload($this->task_id,$files,array($this,'upload_callback'));

            if($result['result']==WPVIVID_SUCCESS)
            {
                WPvivid_taskmanager::update_backup_main_task_progress($this->task_id,'upload',100,1);
                WPvivid_taskmanager::update_backup_task_status($task_id,false,'completed');
                return array('result' => WPVIVID_SUCCESS);
            }
            else
            {
                $remote ->cleanup($files);

                WPvivid_taskmanager::update_backup_task_status($this->task_id,false,'error',false,false,$result['error']);
                return array('result' => WPVIVID_FAILED , 'error' => $result['error']);
            }
        }
    }

    public function upload_callback($offset,$current_name,$current_size,$last_time,$last_size)
    {
        $job_data=array();
        $upload_data=array();
        $upload_data['offset']=$offset;
        $upload_data['current_name']=$current_name;
        $upload_data['current_size']=$current_size;
        $upload_data['last_time']=$last_time;
        $upload_data['last_size']=$last_size;
        $upload_data['descript']='Uploading '.$current_name;
        $v =( $offset - $last_size ) / (time() - $last_time);
        $v /= 1000;
        $v=round($v,2);

        global $wpvivid_plugin;
        $wpvivid_plugin->check_cancel_backup($this->task_id);

        $message='Uploading '.$current_name.' Total size: '.size_format($current_size,2).' Uploaded: '.size_format($offset,2).' speed:'.$v.'kb/s';
        $wpvivid_plugin->wpvivid_log->WriteLog($message,'notice');
        $progress=intval(($offset/$current_size)*100);
        WPvivid_taskmanager::update_backup_main_task_progress($this->task_id,'upload',$progress,0);
        WPvivid_taskmanager::update_backup_sub_task_progress($this->task_id,'upload','',WPVIVID_UPLOAD_UNDO,$message, $job_data, $upload_data);
    }

    public function get_backup_files($backup)
    {
        $backup_item=new WPvivid_Backup_Item($backup);

        return $backup_item->get_files();
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