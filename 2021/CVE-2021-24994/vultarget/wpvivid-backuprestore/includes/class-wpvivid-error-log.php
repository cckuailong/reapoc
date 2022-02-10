<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
class WPvivid_error_log
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
        $log=new WPvivid_Log();
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
        $log=new WPvivid_Log();
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