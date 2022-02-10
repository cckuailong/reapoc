<?php
if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
abstract class WPvivid_Remote{
    public $current_file_name = '';
    public $current_file_size = '';
    public $last_time = 0;
    public $last_size = 0;

    public $object;
    public $remote;

    abstract public function test_connect();
    abstract public function upload($task_id,$files,$callback = '');  // $files = array();
    abstract public function download($file,$local_path,$callback = ''); // $file = array('file_name' => ,'size' =>,'md5' =>)
    abstract public function cleanup($files);  // $files = array();

    public function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . '' . $units[$pow];
    }
}