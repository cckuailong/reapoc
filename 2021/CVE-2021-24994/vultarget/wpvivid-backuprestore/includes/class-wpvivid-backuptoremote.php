<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}

require_once WPVIVID_PLUGIN_DIR . '/includes/customclass/class-wpvivid-ftpclass.php';
require_once WPVIVID_PLUGIN_DIR . '/includes/customclass/class-wpvivid-sftpclass.php';
require_once WPVIVID_PLUGIN_DIR . '/includes/customclass/class-wpvivid-amazons3-plus.php';
class WPvivid_Backup_Remote
{

	public function backup_with_ftp($data = array())
    {
		$host = $data['options']['host'];
		$username = $data['options']['username'];
		$password = $data['options']['password'];
		$path = $data['options']['path'];
        $passive = $data['options']['passive'];
		$port = empty($data['options']['port'])?21:$data['options']['port'];

		$ftpclass = new WPvivid_FTPClass();
		$res = $ftpclass -> upload($host,$username,$password,$path,$data['files'],$data['task_id'],$passive,$port,$data['log']);
        return $res;
	}

	public function backup_with_sftp($data)
    {
	    if(empty($data['port']))
	        $data['options']['port'] = 22;
	    $host = $data['options']['host'];
	    $username = $data['options']['username'];
	    $password = $data['options']['password'];
	    $path = $data['options']['path'];
        $port = $data['options']['port'];
        $scp = $data['options']['scp'];

        $sftpclass = new WPvivid_SFTPClass();
		$result = $sftpclass -> upload($host,$username,$password,$path,$data['files'],$data['task_id'],$port,$scp,$data['log']);
        return $result;
	}

	public function backup_with_amazonS3($data = array())
    {
		$files = $data['files'];
		$access = $data['options']['access'];
		$secret = $data['options']['secret'];
		$s3Path = $data['options']['s3Path'];
		$region = $data['options']['region'];
		$amazonS3 = new WPvivid_AMAZONS3Class();
		$amazonS3 ->init($access,$secret,$region);
        $res =  $amazonS3 -> upload($files,$s3Path,$data['task_id'],$data['log']);
		return $res;
	}
}