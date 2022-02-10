<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}

class WPvivid_downloader
{
    private $task;

    public function ready_download($download_info)
    {
        $backup=WPvivid_Backuplist::get_backup_by_id($download_info['backup_id']);
        if(!$backup)
        {
            return false;
        }

        $file_info=false;

        if(isset($backup['backup']['files']))
        {
            foreach ($backup['backup']['files'] as $file)
            {
                if ($file['file_name'] == $download_info['file_name'])
                {
                    $file_info= $file;
                    break;
                }
            }
        }
        else if ($backup['backup']['ismerge'] == 1)
        {
            $backup_files = $backup['backup']['data']['meta']['files'];
            foreach ($backup_files as $file)
            {
                if ($file['file_name'] == $download_info['file_name'])
                {
                    $file_info = $file;
                    break;
                }
            }
        } else {
            foreach ($backup['backup']['data']['type'] as $type)
            {
                $backup_files = $type['files'];
                foreach ($backup_files as $file) {
                    if ($file['file_name'] == $download_info['file_name'])
                    {
                        $file_info = $file;
                        break;
                    }
                }
            }
        }

        if($file_info==false)
        {
            return false;
        }

        $backup_dir = WPvivid_Setting::get_backupdir();
        $local_path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$backup_dir.DIRECTORY_SEPARATOR;
        //$local_path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$backup['local']['path'].DIRECTORY_SEPARATOR;
        $need_download_files=array();

        $local_file=$local_path.$file_info['file_name'];
        if(file_exists($local_file))
        {
            if(filesize($local_file)!=$file_info['size'])
            {
                if(filesize($local_file)>$file_info['size'])
                {
                    @unlink($local_file);
                }
                $need_download_files[$file_info['file_name']]=$file_info;
            }
        }
        else {
            $need_download_files[$file_info['file_name']]=$file_info;
        }


        if(empty($need_download_files))
        {
            delete_option('wpvivid_download_cache');
        }
        else
        {
            if(WPvivid_taskmanager::is_download_task_running_v2($download_info['file_name']))
            {
                global $wpvivid_plugin;
                $wpvivid_plugin->wpvivid_log->WriteLog('has a downloading task,exit download.','test');
                return false;
            }
            else
            {
                WPvivid_taskmanager::delete_download_task_v2($download_info['file_name']);
                $task=WPvivid_taskmanager::new_download_task_v2($download_info['file_name']);
            }
        }

        foreach ($need_download_files as $file)
        {
            $ret=$this->download_ex($task,$backup['remote'],$file,$local_path);
            if($ret['result']==WPVIVID_FAILED)
            {
                return false;
            }
        }

        return true;
    }

    public function download_ex(&$task,$remotes,$file,$local_path)
    {
        $this->task=$task;

        $remote_option=array_shift($remotes);

        if(is_null($remote_option))
        {
            return array('result' => WPVIVID_FAILED ,'error'=>'Retrieving the cloud storage information failed while downloading backups. Please try again later.');
        }

        global $wpvivid_plugin;

        $remote=$wpvivid_plugin->remote_collection->get_remote($remote_option);

        $ret=$remote->download($file,$local_path,array($this,'download_callback_v2'));

        if($ret['result']==WPVIVID_SUCCESS)
        {
            $progress=100;
            $wpvivid_plugin->wpvivid_download_log->WriteLog('Download completed.', 'notice');
            WPvivid_taskmanager::update_download_task_v2( $task,$progress,'completed');
            return $ret;
        }
        else
        {
            $progress=0;
            $message=$ret['error'];
            if($wpvivid_plugin->wpvivid_download_log)
            {
                $wpvivid_plugin->wpvivid_download_log->WriteLog('Download failed, ' . $message ,'error');
                $wpvivid_plugin->wpvivid_download_log->CloseFile();
                WPvivid_error_log::create_error_log($wpvivid_plugin->wpvivid_download_log->log_file);
            }
            else {
                $id = uniqid('wpvivid-');
                $log_file_name = $id . '_download';
                $log = new WPvivid_Log();
                $log->CreateLogFile($log_file_name, 'no_folder', 'download');
                $log->WriteLog($message, 'notice');
                $log->CloseFile();
                WPvivid_error_log::create_error_log($log->log_file);
            }
            WPvivid_taskmanager::update_download_task_v2($task,$progress,'error',$message);
            return $ret;
        }
    }

    public function download_callback_v2($offset,$current_name,$current_size,$last_time,$last_size)
    {
        global $wpvivid_plugin;
        $progress= floor(($offset/$current_size)* 100) ;
        $text='Total size:'.size_format($current_size,2).' downloaded:'.size_format($offset,2);
        $this->task['download_descript']=$text;
        $wpvivid_plugin->wpvivid_download_log->WriteLog('Total Size: '.$current_size.', Downloaded Size: '.$offset ,'notice');
        WPvivid_taskmanager::update_download_task_v2( $this->task,$progress,'running');
    }

    public static function delete($remote , $files)
    {
        global $wpvivid_plugin;

        @set_time_limit(60);

        $remote=$wpvivid_plugin->remote_collection->get_remote($remote);

        $result =$remote->cleanup($files);

        return $result;
    }
}