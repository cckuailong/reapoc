<?php

if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Isolate_Files
{
    public function __construct()
    {

    }

    public function check_folder()
    {
        if(!is_dir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR))
        {
            @mkdir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR,0777,true);
        }
    }

    public function isolate_files($files)
    {
        $upload_dir=wp_upload_dir();
        $root_path=$upload_dir['basedir'].DIRECTORY_SEPARATOR;

        if(!is_dir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR))
        {
            @mkdir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR,0777,true);
        }

        $iso_dir=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR.DIRECTORY_SEPARATOR;

        foreach ($files as $file)
        {
            $from=$root_path.$file;

            $to=$iso_dir.$file;

            if(file_exists($from))
            {
                if (!is_dir(dirname($to)))
                {
                    mkdir(dirname($to), 0777, true);
                }
                @rename($from,$to);
            }
        }
        $ret['result']='success';
        return $ret;
    }

    public function init_isolate_task()
    {
        $task['start_time']=time();
        $task['running_time']=time();
        $task['status']='running';
        $task['progress']=0;
        $task['offset']=0;

        update_option('init_isolate_task',$task);
    }

    public function get_isolate_task_offset()
    {
        $task=get_option('scan_unused_files_task',array());
        if(empty($task))
        {
            return false;
        }

        if($task['status']=='finished')
        {
            return false;
        }

        return $task['offset'];
    }

    public function update_isolate_task($offset,$status='running',$progress=0)
    {
        $task=get_option('scan_unused_files_task',array());

        $task['running_time']=time();
        $task['status']=$status;
        $task['progress']=$progress;
        $task['offset']=$offset;

        update_option('scan_unused_files_task',$task);
    }

    public function get_isolate_folder()
    {
        $root=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR;
        $ret=$this->get_folder_list($root);
        return $ret;
    }

    public function get_isolate_files($search='',$folder_ex='',$count=0)
    {
        $root=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR;
        $ret=$this->get_folder_list($root,$search);

        if(empty($folder_ex))
        {
            $result=$ret['files'];
            foreach ($ret['folders'] as $folder)
            {
                $files=array();
                $this->scan_list_uploaded_files($files,$root.DIRECTORY_SEPARATOR.$folder,$root,$folder,$search);
                $result=array_merge($result,$files);
            }
        }
        else if($folder_ex=='.')
        {
            $result=$ret['files'];

            if($count>0&&sizeof($result)>$count)
            {
                $result=array_slice($result, 0, $count);
            }
            return $result;
        }
        else
        {
            $files=array();
            $this->scan_list_uploaded_files($files,$root.DIRECTORY_SEPARATOR.$folder_ex,$root,$folder_ex,$search);
            $result=$files;
        }

        if($count>0&&sizeof($result)>$count)
        {
            $result=array_slice($result, 0, $count);
        }

        return $result;
    }

    private function get_folder_list($root_path,$search='')
    {
        $result['folders']=array();
        $result['folders'][]='root';
        $result['files']=array();
        if(!file_exists($root_path)){
            @mkdir($root_path, 0755, true);
        }
        $handler = opendir($root_path);
        if($handler!==false)
        {
            while (($filename = readdir($handler)) !== false)
            {
                if ($filename != "." && $filename != "..")
                {
                    if (is_dir($root_path . DIRECTORY_SEPARATOR . $filename))
                    {
                        if(preg_match('#^\d{4}$#',$filename))
                        {
                            $result['folders']=array_merge( $result['folders'],$this->get_sub_folder($root_path . DIRECTORY_SEPARATOR . $filename,$filename));
                        }
                        else
                        {
                            $ret=scandir($root_path . DIRECTORY_SEPARATOR . $filename);
                            if($ret!==false&&count($ret)!=2)
                            {
                                $result['folders'][]=$filename;
                            }

                        }

                    } else {

                        if($filename=='.htaccess'||$filename=='index.php')
                        {
                            continue;
                        }

                        if(empty($search))
                        {
                            $file['path']=$filename;
                            $file['folder']='.';
                            $result['files'][] = $file;
                        }
                        else if(preg_match('#'.$search.'#',$filename))
                        {
                            $file['path']=$filename;
                            $file['folder']='.';
                            $result['files'][] = $file;
                        }
                    }
                }
            }
            if($handler)
                @closedir($handler);
        }

        return $result;
    }

    private function get_sub_folder($path,$root)
    {
        $folders=array();
        $handler = opendir($path);
        if($handler!==false)
        {
            while (($filename = readdir($handler)) !== false)
            {
                if ($filename != "." && $filename != "..")
                {
                    if (is_dir($path . DIRECTORY_SEPARATOR . $filename))
                    {
                        $ret=scandir($path . DIRECTORY_SEPARATOR . $filename);
                        if($ret!==false&&count($ret)!=2)
                        {
                            $folders[]=$root.DIRECTORY_SEPARATOR.$filename;
                        }
                    }
                }
            }
            if($handler)
                @closedir($handler);
        }
        return $folders;
    }

    private function scan_list_uploaded_files( &$files,$path,$root,$folder,$search='')
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
                            $this->scan_list_uploaded_files($files, $path . DIRECTORY_SEPARATOR . $filename,$root,$folder,$search);
                        } else {
                            if(empty($search))
                            {
                                $file['path']=str_replace($root . DIRECTORY_SEPARATOR,'',$path . DIRECTORY_SEPARATOR . $filename);
                                $file['folder']=$folder;
                                $files[] = $file;
                            }
                            else if(preg_match('#'.$search.'#',$filename))
                            {
                                $file['path']=str_replace($root . DIRECTORY_SEPARATOR,'',$path . DIRECTORY_SEPARATOR . $filename);
                                $file['folder']=$folder;
                                $files[] = $file;
                            }
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }
        }

        return $files;
    }

    public function delete_files($files)
    {
        $root=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR;
        $delete_media_when_delete_file=get_option('wpvivid_uc_delete_media_when_delete_file',false);
        foreach ($files as $file)
        {
            @unlink($root.DIRECTORY_SEPARATOR.$file);

            if($delete_media_when_delete_file)
            {
                $attachment_id=$this->find_media_id_from_file($file);
                if($attachment_id)
                {
                    wp_delete_attachment( $attachment_id, true );
                }
            }
        }



    }

    public function delete_files_ex($files)
    {
        $root=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR;
        $delete_media_when_delete_file=get_option('wpvivid_uc_delete_media_when_delete_file',false);
        foreach ($files as $file)
        {
            @unlink($root.DIRECTORY_SEPARATOR.$file['path']);

            if($delete_media_when_delete_file)
            {
                $attachment_id=$this->find_media_id_from_file($file['path']);
                if($attachment_id)
                {
                    wp_delete_attachment( $attachment_id, true );
                }
            }
        }
    }

    public function find_media_id_from_file( $file )
    {
        global $wpdb;

        $file=basename($file);

        $sql = "SELECT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_wp_attachment_metadata'
			AND meta_value LIKE '%$file%'";

        $ret = $wpdb->get_var( $sql );

        if(!$ret)
        {
            $sql = $wpdb->prepare( "SELECT post_id
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_wp_attached_file'
			AND meta_value = %s", $file
            );
            $ret = $wpdb->get_var( $sql );
        }
        return $ret;
    }

    public function restore_files($files)
    {
        $upload_dir=wp_upload_dir();

        $root_path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR.DIRECTORY_SEPARATOR;
        $upload_path=$upload_dir['basedir'].DIRECTORY_SEPARATOR;

        foreach ($files as $file)
        {
            $from=$root_path.$file;

            $to=$upload_path.$file;

            if(file_exists($from))
            {
                if (!is_dir(dirname($to)))
                {
                    mkdir(dirname($to), 0777, true);
                }
                @rename($from,$to);
            }
        }
        $ret['result']='success';
        return $ret;
    }

    public function restore_files_ex($files)
    {
        $upload_dir=wp_upload_dir();

        $root_path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPVIVID_UPLOADS_ISO_DIR.DIRECTORY_SEPARATOR;
        $upload_path=$upload_dir['basedir'].DIRECTORY_SEPARATOR;

        foreach ($files as $file)
        {
            $from=$root_path.$file['path'];

            $to=$upload_path.$file['path'];

            if(file_exists($from))
            {
                if (!is_dir(dirname($to)))
                {
                    mkdir(dirname($to), 0777, true);
                }
                @rename($from,$to);
            }
        }
        $ret['result']='success';
        return $ret;
    }
}