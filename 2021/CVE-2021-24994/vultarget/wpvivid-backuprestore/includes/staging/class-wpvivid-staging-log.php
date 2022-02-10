<?php

if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Staging_Log_Free
{
    public $log_file;
    public $log_file_handle;

    public function __construct()
    {
        $this->log_file_handle=false;
    }

    public function CreateLogFile($file_name,$type,$describe)
    {
        if($type=='has_folder')
        {
            $this->log_file=$file_name;
        }
        else
        {
            $this->log_file=$this->GetSaveLogFolder().$file_name.'_log.txt';
        }
        if(file_exists($this->log_file))
        {
            @unlink( $this->log_file);
        }
        $this->log_file_handle = fopen($this->log_file, 'a');
        $time =date("Y-m-d H:i:s",time());
        $text='Log created: '.$time."\n";
        $text.='Type: '.$describe."\n";
        fwrite($this->log_file_handle,$text);

        return $this->log_file;
    }

    public function OpenLogFile($file_name,$type='no_folder',$delete=0)
    {
        if($type=='has_folder')
        {
            $this->log_file=$file_name;
        }
        else
        {
            $this->log_file=$this->GetSaveLogFolder().$file_name.'_log.txt';
        }
        if($delete==1)
        {
            unlink( $this->log_file);
        }
        $this->log_file_handle = fopen($this->log_file, 'a');

        return $this->log_file;
    }

    public function WriteLog($log,$type)
    {
        if ($this->log_file_handle)
        {
            $time =date("Y-m-d H:i:s",time());
            $text='['.$time.']'.'['.$type.']'.$log."\n";
            fwrite($this->log_file_handle,$text );
        }
    }

    public function CloseFile()
    {
        if ($this->log_file_handle)
        {
            fclose($this->log_file_handle);
            $this->log_file_handle=false;
        }
    }

    public function GetSaveLogFolder()
    {
        if(!is_dir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'wpvivid_staging'))
        {
            @mkdir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'wpvivid_staging',0777,true);
            @fopen(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'wpvivid_staging'.DIRECTORY_SEPARATOR.'index.html', 'x');
            $tempfile=@fopen(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'wpvivid_staging'.DIRECTORY_SEPARATOR.'.htaccess', 'x');
            if($tempfile)
            {
                $text="deny from all";
                fwrite($tempfile,$text );
            }
        }

        return WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'wpvivid_staging'.DIRECTORY_SEPARATOR;
    }

    public function WriteLogHander()
    {
        if ($this->log_file_handle)
        {
            global $wp_version;
            global $wpdb;

            $sapi_type=php_sapi_name();
            if($sapi_type=='cgi-fcgi'||$sapi_type==' fpm-fcgi') {
                $fcgi='On';
            }
            else {
                $fcgi='Off';
            }

            $options=get_option('wpvivid_staging_options',array());

            if(isset($options['max_execution_time']))
            {
                $max_execution_time=$options['staging_max_execution_time'];
            }
            else {
                $max_execution_time=900;
            }

            $log='server info fcgi:'.$fcgi.' max execution time: '.$max_execution_time.' wp version:'.$wp_version.' php version:'.phpversion().' db version:'.$wpdb->db_version().' php ini:safe_mode:'.ini_get('safe_mode').' ';
            $log.='memory_limit:'.ini_get('memory_limit').' memory_get_usage:'.size_format(memory_get_usage(),2).' memory_get_peak_usage:'.size_format(memory_get_peak_usage(),2);
            $log.=' extensions:';
            $loaded_extensions = get_loaded_extensions();
            if(!in_array('PDO', $loaded_extensions))
            {
                $log.='PDO not enabled ';
            }
            else
            {
                $log.='PDO enabled ';
            }
            if(!in_array('curl', $loaded_extensions))
            {
                $log.='curl not enabled ';
            }
            else
            {
                $log.='curl enabled ';
            }

            if(!in_array('zlib', $loaded_extensions)) {
                $log .= 'zlib not enabled ';
            }
            else
            {
                $log.='zlib enabled ';
            }

            $log.=' ';
            if(is_multisite())
            {
                $log.=' is_multisite:1';
            }
            else
            {
                $log.=' is_multisite:0';
            }

            $time =date("Y-m-d H:i:s",time());
            $text='['.$time.']'.'[notice]'.$log."\n";
            fwrite($this->log_file_handle,$text );
        }
    }
}

class WPvivid_Staging_error_log_free
{
    public static function create_error_log($log_file_name)
    {
        $dir=dirname($log_file_name);
        $file=basename($log_file_name);
        if(!is_dir($dir.DIRECTORY_SEPARATOR.'error'))
        {
            @mkdir($dir.DIRECTORY_SEPARATOR.'error',0777,true);
            @fopen($dir.DIRECTORY_SEPARATOR.'error'.'/index.html', 'x');
            $tempfile=@fopen($dir.DIRECTORY_SEPARATOR.'error'.'/.htaccess', 'x');
            if($tempfile)
            {
                $text="deny from all";
                fwrite($tempfile,$text );
                @fclose($tempfile);
            }
        }

        if(!file_exists($log_file_name))
        {
            return ;
        }

        if(file_exists($dir.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.$file))
        {
            @unlink($dir.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.$file);
        }

        @rename($log_file_name,$dir.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.$file);

        //self::delete_oldest_error_log();
    }

    public static function create_restore_error_log($log_file_name)
    {
        $dir=dirname($log_file_name);
        if(!is_dir($dir.DIRECTORY_SEPARATOR.'wpvivid_log'.DIRECTORY_SEPARATOR.'error'))
        {
            @mkdir($dir.DIRECTORY_SEPARATOR.'wpvivid_log'.DIRECTORY_SEPARATOR.'error',0777,true);
            @fopen($dir.DIRECTORY_SEPARATOR.'wpvivid_log'.DIRECTORY_SEPARATOR.'error'.'/index.html', 'x');
            $tempfile=@fopen($dir.DIRECTORY_SEPARATOR.'wpvivid_log'.DIRECTORY_SEPARATOR.'error'.'/.htaccess', 'x');
            if($tempfile)
            {
                $text="deny from all";
                fwrite($tempfile,$text );
                @fclose($tempfile);
            }
        }
        $id = uniqid('wpvivid-');
        $file=$id.'_restore_log.txt';
        if(file_exists($dir.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.$file))
        {
            @unlink($dir.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.$file);
        }

        @copy($log_file_name,$dir.DIRECTORY_SEPARATOR.'wpvivid_log'.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.$file);
        //self::delete_oldest_error_log();
    }

    public static function delete_oldest_error_log()
    {
        $files=array();
        $log=new WPvivid_Staging_Log_Free();
        $dir=$log->GetSaveLogFolder();
        $dir=$dir.'error';
        @$handler=opendir($dir);
        if($handler===false)
            return;
        $regex='#^wpvivid.*_log.txt#';
        while(($filename=readdir($handler))!==false)
        {
            if($filename != "." && $filename != "..")
            {
                if(is_dir($dir.DIRECTORY_SEPARATOR.$filename))
                {
                    continue;
                }else{
                    if(preg_match($regex,$filename))
                    {
                        $files[$filename] = $dir.DIRECTORY_SEPARATOR.$filename;
                    }
                }
            }
        }
        if($handler)
            @closedir($handler);
        $oldest=0;
        $oldest_filename='';
        $max_count=5;
        if(sizeof($files)>$max_count)
        {
            foreach ($files as $file)
            {
                if($oldest==0)
                {
                    $oldest=filemtime($file);
                    $oldest_filename=$file;
                }
                else
                {
                    if($oldest>filemtime($file))
                    {
                        $oldest=filemtime($file);
                        $oldest_filename=$file;
                    }
                }
            }

            if($oldest_filename!='')
            {
                @unlink($oldest_filename);
            }
        }
    }

    public static function get_error_log()
    {
        $log=new WPvivid_Staging_Log_Free();
        $dir=$log->GetSaveLogFolder();
        $dir=$dir.'error';
        $files=array();
        $handler=opendir($dir);
        if($handler === false){
            return $files;
        }
        $regex='#^wpvivid.*_log.txt#';
        while(($filename=readdir($handler))!==false)
        {
            if($filename != "." && $filename != "..")
            {
                if(is_dir($dir.$filename))
                {
                    continue;
                }
                else{
                    if(preg_match($regex,$filename))
                    {
                        $files[] = $dir.DIRECTORY_SEPARATOR.$filename;
                    }
                }
            }
        }
        if($handler)
            @closedir($handler);
        return $files;
    }
}