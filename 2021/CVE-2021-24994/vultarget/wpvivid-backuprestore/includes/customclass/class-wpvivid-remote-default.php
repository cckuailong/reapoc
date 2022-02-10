<?php
if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}

if(!defined('WPVIVID_UPLOAD_SUCCESS'))
{
    define('WPVIVID_UPLOAD_SUCCESS',1);
}

if(!defined('WPVIVID_UPLOAD_FAILED'))
{
    define('WPVIVID_UPLOAD_FAILED',2);
}

if(!defined('WPVIVID_UPLOAD_UNDO'))
{
    define('WPVIVID_UPLOAD_UNDO',0);
}

require_once WPVIVID_PLUGIN_DIR .'/includes/customclass/class-wpvivid-remote.php';
class WPvivid_Remote_Defult extends WPvivid_Remote{
    public function test_connect()
    {
        return array('result' => WPVIVID_FAILED,'error'=> 'Type incorrect.');
    }

    public function upload($task_id, $files, $callback = '')
    {
        return array('result' => WPVIVID_FAILED,'error'=> 'Type incorrect.');
    }

    public function download( $file, $local_path, $callback = '')
    {
        return array('result' => WPVIVID_FAILED,'error'=> 'Type incorrect.');
    }

    public function cleanup($files)
    {
        return array('result' => WPVIVID_FAILED,'error'=> 'Type incorrect.');
    }
}