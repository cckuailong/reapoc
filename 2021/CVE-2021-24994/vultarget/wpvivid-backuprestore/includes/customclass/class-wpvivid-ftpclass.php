<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}

if(!defined('WPVIVID_REMOTE_FTP'))
    define('WPVIVID_REMOTE_FTP','ftp');

require_once WPVIVID_PLUGIN_DIR .'/includes/customclass/class-wpvivid-remote.php';
class WPvivid_FTPClass extends WPvivid_Remote{
    private $time_out = 20;
    private $callback;
    private $options=array();

    public function __construct($options=array())
    {
        if(empty($options))
        {
            add_action('wpvivid_add_storage_tab',array($this,'wpvivid_add_storage_tab_ftp'), 15);
            add_action('wpvivid_add_storage_page',array($this,'wpvivid_add_storage_page_ftp'), 15);
            add_action('wpvivid_edit_remote_page',array($this,'wpvivid_edit_storage_page_ftp'), 15);
            add_filter('wpvivid_remote_pic',array($this,'wpvivid_remote_pic_ftp'),9);
            add_filter('wpvivid_get_out_of_date_remote',array($this,'wpvivid_get_out_of_date_ftp'),10,2);
            add_filter('wpvivid_storage_provider_tran',array($this,'wpvivid_storage_provider_ftp'),10);

        }else{
            $this->options = $options;
        }
    }

    public function wpvivid_add_storage_tab_ftp()
    {
        ?>
        <div class="storage-providers" remote_type="ftp" onclick="select_remote_storage(event, 'storage_account_ftp');">
            <img src="<?php echo esc_url(WPVIVID_PLUGIN_URL.'/admin/partials/images/storage-ftp.png'); ?>" style="vertical-align:middle;"/><?php _e('FTP', 'wpvivid-backuprestore'); ?>
        </div>
        <?php
    }

    public function wpvivid_add_storage_page_ftp()
    {
        ?>
        <div id="storage_account_ftp" class="storage-account-page" style="display:none;">
            <div style="padding: 0 10px 10px 0;"><strong><?php _e('Enter Your FTP Account', 'wpvivid-backuprestore'); ?></strong></div>
            <table class="wp-list-table widefat plugins" style="width:100%;">
                <tbody>
                <form>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" autocomplete="off" option="ftp" name="name" placeholder="<?php esc_attr_e('Enter an unique alias: e.g. FTP-001', 'wpvivid-backuprestore'); ?>" class="regular-text" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_]/g,'')" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('A name to help you identify the storage if you have multiple remote storage connected.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" autocomplete="off" option="ftp" name="server" placeholder="<?php esc_attr_e('FTP server (server\'s port 21)','wpvivid-backuprestore'); ?>" class="regular-text"/>
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i style="margin-right: 10px;"><?php _e('Enter the FTP server.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" autocomplete="off" class="regular-text" value="21" readonly="readonly" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <a href="https://docs.wpvivid.com/wpvivid-backup-pro-ftp-change-ftp-default-port.html"><?php _e('Pro feature: Change the FTP default port number', 'wpvivid-backuprestore'); ?></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="ftp" name="username" placeholder="<?php esc_attr_e('FTP login', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter your FTP server user name.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="password" class="regular-text" autocomplete="new-password" option="ftp" name="password" placeholder="<?php esc_attr_e('FTP password', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the FTP server password.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" autocomplete="off" option="ftp" name="path" placeholder="<?php esc_attr_e('Absolute path must exist(e.g. /home/username)', 'wpvivid-backuprestore'); ?>" class="regular-text"/>
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter an absolute path and a custom subdirectory (optional) for holding the backups of current website. For example, /home/username/customfolder', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-select">
                            <label>
                                <input type="checkbox" option="ftp" name="default" checked /><?php _e('Set as the default remote storage.', 'wpvivid-backuprestore'); ?>
                            </label>
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Once checked, all this sites backups sent to a remote storage destination will be uploaded to this storage by default.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-select">
                            <label>
                                <input type="checkbox" option="ftp" name="passive" checked /><?php _e('Uncheck this to enable FTP active mode.', 'wpvivid-backuprestore'); ?>
                            </label>
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Uncheck the option to use FTP active mode when transferring files. Make sure the FTP server you are configuring supports the active FTP mode.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                </form>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input class="button-primary" type="submit" option="add-remote" value="<?php esc_attr_e('Test and Add', 'wpvivid-backuprestore'); ?>">
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Click the button to connect to FTP server and add it to the storage list below.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function wpvivid_edit_storage_page_ftp()
    {
        ?>
        <div id="remote_storage_edit_ftp" class="postbox storage-account-block remote-storage-edit" style="display:none;">
            <div style="padding: 0 10px 10px 0;"><strong><?php _e('Enter Your FTP Account', 'wpvivid-backuprestore'); ?></strong></div>
            <table class="wp-list-table widefat plugins" style="width:100%;">
                <tbody>
                <form>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" autocomplete="off" option="edit-ftp" name="name" placeholder="<?php esc_attr_e('Enter an unique alias: e.g. FTP-001', 'wpvivid-backuprestore'); ?>" class="regular-text" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_]/g,'')" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('A name to help you identify the storage if you have multiple remote storage connected.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" autocomplete="off" option="edit-ftp" name="server" placeholder="<?php esc_attr_e('FTP server (server\'s port 21)', 'wpvivid-backuprestore'); ?>" class="regular-text"/>
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i style="margin-right: 10px;"><?php _e('Enter the FTP server.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="edit-ftp" name="username" placeholder="<?php esc_attr_e('FTP login', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter your FTP server user name.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="password" class="regular-text" autocomplete="new-password" option="edit-ftp" name="password" placeholder="<?php esc_attr_e('FTP password', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the FTP server password.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" autocomplete="off" option="edit-ftp" name="path" placeholder="<?php esc_attr_e('Absolute path must exist(e.g. /home/username)', 'wpvivid-backuprestore'); ?>" class="regular-text"/>
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter an absolute path and a custom subdirectory (optional) for holding the backups of current website. For example, /home/username/customfolder', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-select">
                            <label>
                                <input type="checkbox" option="edit-ftp" name="passive" checked /><?php _e('Uncheck this to enable FTP active mode.', 'wpvivid-backuprestore'); ?>
                            </label>
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Uncheck the option to use FTP active mode when transferring files. Make sure the FTP server you are configuring supports the active FTP mode.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                </form>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input class="button-primary" type="submit" option="edit-remote" value="<?php esc_attr_e('Save Changes', 'wpvivid-backuprestore'); ?>">
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Click the button to save the changes.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function wpvivid_remote_pic_ftp($remote){
        $remote['ftp']['default_pic'] = '/admin/partials/images/storage-ftp(gray).png';
        $remote['ftp']['selected_pic'] = '/admin/partials/images/storage-ftp.png';
        $remote['ftp']['title'] = 'FTP';
        return $remote;
    }

    public function test_connect()
    {
        $passive =$this->options['passive'];
        $host = $this->options['host'];
        $username = $this->options['username'];
        if(isset($this->options['is_encrypt']) && $this->options['is_encrypt'] == 1){
            $password = base64_decode($this->options['password']);
        }
        else {
            $password = $this->options['password'];
        }
        $path = $this->options['path'];
        $port = empty($this->options['port'])?21:$this->options['port'];
        $conn = $this -> do_connect($host,$username,$password,$port);
        if(is_array($conn) && array_key_exists('result',$conn))
            return $conn;
        ftp_pasv($conn,$passive);
        return $this->do_chdir($conn,$path);
    }


    public function sanitize_options($skip_name='')
    {
        $ret['result']=WPVIVID_FAILED;
        if(!isset($this->options['name']))
        {
            $ret['error']=__('Warning: An alias for remote storage is required.','wpvivid-backuprestore');
            return $ret;
        }

        $this->options['name']=sanitize_text_field($this->options['name']);

        if(empty($this->options['name']))
        {
            $ret['error']=__('Warning: An alias for remote storage is required.','wpvivid-backuprestore');
            return $ret;
        }

        $remoteslist=WPvivid_Setting::get_all_remote_options();
        foreach ($remoteslist as $key=>$value)
        {
            if(isset($value['name'])&&$value['name'] == $this->options['name']&&$skip_name!=$value['name'])
            {
                $ret['error']="Warning: The alias already exists in storage list.";
                return $ret;
            }
        }

        $this->options['server']=sanitize_text_field($this->options['server']);

        if(empty($this->options['server']))
        {
            $ret['error']="Warning: The FTP server is required.";
            return $ret;
        }
        $res = explode(':',$this -> options['server']);
        if(sizeof($res) > 1){
            $this ->options['host'] = $res[0];
            if($res[1] != 21){
                $ret['error']='Currently, only port 21 is supported.';
                return $ret;
            }

        }else{
            $this -> options['host'] = $res[0];
        }


        if(!isset($this->options['username']))
        {
            $ret['error']="Warning: The FTP login is required.";
            return $ret;
        }

        $this->options['username']=sanitize_text_field($this->options['username']);

        if(empty($this->options['username']))
        {
            $ret['error']="Warning: The FTP login is required.";
            return $ret;
        }

        if(!isset($this->options['password'])||empty($this->options['password']))
        {
            $ret['error']="Warning: The FTP password is required.";
            return $ret;
        }

        $this->options['password']=sanitize_text_field($this->options['password']);

        if(empty($this->options['password']))
        {
            $ret['error']="Warning: The FTP password is required.";
            return $ret;
        }

        if(!isset($this->options['path'])||empty($this->options['path']))
        {
            $ret['error']="Warning: The storage path is required.";
            return $ret;
        }

        $this->options['path']=sanitize_text_field($this->options['path']);

        if(empty($this->options['path']))
        {
            $ret['error']="Warning: The storage path is required.";
            return $ret;
        }

        if($this->options['path']=='/')
        {
            $ret['error']="Warning: Root directory is forbidden to set to '/'.";
            return $ret;
        }

        $ret['result']=WPVIVID_SUCCESS;
        $ret['options']=$this->options;
        return $ret;
    }

    public function do_connect($server,$username,$password,$port = 21)
    {
        $conn = ftp_connect( $server, $port, $this ->time_out );

        if($conn)
        {
            if(ftp_login($conn,$username,$password))
            {
                return $conn;
            }
            else
            {
                return array('result'=>WPVIVID_FAILED,'error'=>'Login failed. You have entered the incorrect credential(s). Please try again.');
            }
        }
        else{
            return array('result'=>WPVIVID_FAILED,'error'=>'Login failed. The connection has timed out. Please try again later.');
        }
	}
    public function do_chdir($conn,$path){
        @ftp_chdir($conn,'/');
        if(!@ftp_chdir($conn,$path))
        {
            $parts = explode('/',$path);
            foreach($parts as $part){
                if($part !== '') {
                    if (!@ftp_chdir($conn, $part)) {
                        if (!ftp_mkdir($conn, $part)) {
                            return array('result' => WPVIVID_FAILED, 'error' => 'Failed to create a backup. Make sure you have sufficient privileges to perform the operation.');
                        }

                        if (!@ftp_chdir($conn, $part)) {
                            return array('result' => WPVIVID_FAILED, 'error' => 'Failed to create a backup. Make sure you have sufficient privileges to perform the operation.');
                        }
                    }
                }
            }

            /*if ( ! ftp_mkdir( $conn, $path ) )
            {
                return array('result'=>WPVIVID_FAILED,'error'=>'Failed to create a backup. Make sure you have sufficient privileges to perform the operation.');
            }
            if (!@ftp_chdir($conn,$path))
            {
                return array('result'=>WPVIVID_FAILED,'error'=>'Failed to create a backup. Make sure you have sufficient privileges to perform the operation.');
            }*/
        }
        $temp_file = md5(rand());
        $temp_path = trailingslashit(WP_CONTENT_DIR).WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.$temp_file;
        file_put_contents($temp_path,print_r($temp_file,true));
        if(! ftp_put($conn,trailingslashit($path).$temp_file,$temp_path,FTP_BINARY)){
            return array('result'=>WPVIVID_FAILED,'error'=>'No privilege to create files in this remote storage directory.');
        }
        @unlink($temp_path);
        @ftp_delete($conn,trailingslashit($path).$temp_file);
        return array('result'=>WPVIVID_SUCCESS);
    }

	public function upload($task_id,$files,$callback = '')
    {
        global $wpvivid_plugin;
        $this -> callback = $callback;

        $passive =$this->options['passive'];
        $host = $this->options['host'];
        $username = $this->options['username'];
        if(isset($this->options['is_encrypt']) && $this->options['is_encrypt'] == 1){
            $password = base64_decode($this->options['password']);
        }
        else {
            $password = $this->options['password'];
        }
        $path = $this->options['path'];
        $port = empty($this->options['port'])?21:$this->options['port'];

        $upload_job=WPvivid_taskmanager::get_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_FTP);
        if(empty($upload_job))
        {
            $job_data=array();
            foreach ($files as $file)
            {
                if(!file_exists($file))
                    return array('result'=>WPVIVID_FAILED,'error'=>$file.' not found. The file might has been moved, renamed or deleted. Please back it up again.');
                $file_data['size']=filesize($file);
                $file_data['uploaded']=0;
                $job_data[basename($file)]=$file_data;
            }
            WPvivid_taskmanager::update_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_FTP,WPVIVID_UPLOAD_UNDO,'Start uploading.',$job_data);
            $upload_job=WPvivid_taskmanager::get_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_FTP);
        }
        $wpvivid_plugin->wpvivid_log->WriteLog('Connecting to server '.$host,'notice');
        $conn = $this -> do_connect($host,$username,$password,$port);
		if(is_array($conn) && array_key_exists('result',$conn))
			return $conn;
        ftp_pasv($conn,$passive);
        $wpvivid_plugin->wpvivid_log->WriteLog('chdir '.$path,'notice');
		$str = $this -> do_chdir($conn , $path);
		if($str['result'] !== WPVIVID_SUCCESS)
			return $str;

		$flag = true;
		$error = '';
		foreach ($files as $key => $file)
		{
            if(is_array($upload_job['job_data']) && array_key_exists(basename($file),$upload_job['job_data']))
            {
                if($upload_job['job_data'][basename($file)]['uploaded']==1)
                    continue;
            }
            $this ->last_time = time();
            $this -> last_size = 0;
            $wpvivid_plugin->wpvivid_log->WriteLog('Start uploading '.basename($file),'notice');
			$remote_file = trailingslashit($path).basename($file);
            if(!file_exists($file))
                return array('result'=>WPVIVID_FAILED,'error'=>$file.' not found. The file might has been moved, renamed or deleted. Please back it up again.');

            $wpvivid_plugin->set_time_limit($task_id);

            for($i =0;$i <WPVIVID_REMOTE_CONNECT_RETRY_TIMES;$i ++)
			{
                $this -> current_file_name = basename($file);
                $this -> current_file_size = filesize($file);
                $this -> last_time = time();
                $this -> last_size = 0;
                $local_handle = fopen($file,'rb');
                if(!$local_handle)
                {
                    return array('result'=>WPVIVID_FAILED,'error'=>'Failed to open '.$this->current_file_name.'.');
                }
                $status = ftp_nb_fput($conn,$remote_file,$local_handle,FTP_BINARY,0);
                while ($status == FTP_MOREDATA)
                {
                    $status = ftp_nb_continue($conn);
                    if((time() - $this -> last_time) >3)
                    {
                        if(is_callable($callback)){
                            call_user_func_array($callback,array(ftell($local_handle),$this -> current_file_name,
                                $this->current_file_size,$this -> last_time,$this -> last_size));
                        }
                        $this -> last_size = ftell($local_handle);
                        $this -> last_time = time();
                    }
                }
                if ($status != FTP_FINISHED)
                {
                    return array('result'=>WPVIVID_FAILED,'error'=>'Uploading '.$remote_file.' to FTP server failed. '.$remote_file.' might be deleted or network doesn\'t work properly. Please verify the file and confirm the network connection and try again later.');
                }

                if($status == FTP_FINISHED)
                {
                    $wpvivid_plugin->wpvivid_log->WriteLog('Finished uploading '.basename($file),'notice');
                    $upload_job['job_data'][basename($file)]['uploaded']=1;
                    WPvivid_taskmanager::update_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_FTP,WPVIVID_UPLOAD_SUCCESS,'Uploading '.basename($file).' completed.',$upload_job['job_data']);
                    break;
                }

                if($status != FTP_FINISHED && $i == (WPVIVID_REMOTE_CONNECT_RETRY_TIMES - 1))
                {
                   $flag = false;
                   $error = 'Uploading '.basename($file).' to FTP server failed. '.basename($file).' might be deleted or network doesn\'t work properly. Please verify the file and confirm the network connection and try again later.';
                   break 2;
                }
                sleep(WPVIVID_REMOTE_CONNECT_RETRY_INTERVAL);
            }
		}

		if($flag){
            return array('result'=>WPVIVID_SUCCESS);
        }else{
            return array('result'=>WPVIVID_FAILED,'error'=>$error);
        }
	}

    public function download($file,$local_path,$callback = '')
    {
        try {
            global $wpvivid_plugin;
            $passive = $this->options['passive'];
            $host = $this->options['host'];
            $username = $this->options['username'];
            if(isset($this->options['is_encrypt']) && $this->options['is_encrypt'] == 1){
                $password = base64_decode($this->options['password']);
            }
            else {
                $password = $this->options['password'];
            }
            $path = $this->options['path'];
            $port = empty($this->options['port']) ? 21 : $this->options['port'];

            $local_path = trailingslashit($local_path) . $file['file_name'];
            $remote_file = trailingslashit($path) . $file['file_name'];

            $this->current_file_name = $file['file_name'];
            $this->current_file_size = $file['size'];

            $wpvivid_plugin->wpvivid_download_log->WriteLog('Connecting FTP server.','notice');
            $conn = $this->do_connect($host, $username, $password, $port);
            if (is_array($conn) && array_key_exists('result', $conn)) {
                return $conn;
            }

            ftp_pasv($conn, $passive);
            $wpvivid_plugin->wpvivid_download_log->WriteLog('Create local file.','notice');
            $local_handle = fopen($local_path, 'ab');
            if (!$local_handle) {
                return array('result' => WPVIVID_FAILED, 'error' => 'Unable to create the local file. Please make sure the folder is writable and try again.');
            }

            $stat = fstat($local_handle);
            $offset = $stat['size'];
            $wpvivid_plugin->wpvivid_download_log->WriteLog('Downloading file ' . $file['file_name'] . ', Size: ' . $file['size'] ,'notice');
            $status = ftp_nb_fget($conn, $local_handle, $remote_file, FTP_BINARY, $offset);
            while ($status == FTP_MOREDATA) {
                $status = ftp_nb_continue($conn);
                if ((time() - $this->last_time) > 3) {
                    if (is_callable($callback)) {
                        call_user_func_array($callback, array(ftell($local_handle), $this->current_file_name,
                            $this->current_file_size, $this->last_time, $this->last_size));
                    }
                    $this->last_size = ftell($local_handle);
                    $this->last_time = time();
                }
            }

            if(filesize($local_path) == $file['size']){
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

            if ($status != FTP_FINISHED || $res !== TRUE) {
                @unlink($local_path);
                return array('result' => WPVIVID_FAILED, 'error' => 'Downloading ' . $remote_file . ' failed. ' . $remote_file . ' might be deleted or network doesn\'t work properly. Please verify the file and confirm the network connection and try again later.');
            }

            ftp_close($conn);
            fclose($local_handle);
            return array('result' => WPVIVID_SUCCESS);
        }
        catch (Exception $error){
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            return array('result'=>WPVIVID_FAILED, 'error'=>$message);
        }
    }

	public function cleanup($file){
        $host = $this->options['host'];
        $username = $this->options['username'];
        if(isset($this->options['is_encrypt']) && $this->options['is_encrypt'] == 1){
            $password = base64_decode($this->options['password']);
        }
        else {
            $password = $this->options['password'];
        }
        $path = $this->options['path'];
        $port = empty($this->options['port'])?21:$this->options['port'];

        $conn = $this -> do_connect($host,$username,$password,$port);
        if(is_array($conn) && array_key_exists('result',$conn))
            return $conn;

        foreach ($file as $value){
            @ftp_delete($conn,trailingslashit($path).$value);
        }
        return array('result'=>WPVIVID_SUCCESS);
	}

	public function init_remotes($remote_collection){
        $remote_collection[WPVIVID_REMOTE_FTP] = 'WPvivid_FTPClass';
        return $remote_collection;
    }

    public function wpvivid_get_out_of_date_ftp($out_of_date_remote, $remote)
    {
        if($remote['type'] == WPVIVID_REMOTE_FTP){
            $out_of_date_remote = $remote['path'];
        }
        return $out_of_date_remote;
    }

    public function wpvivid_storage_provider_ftp($storage_type)
    {
        if($storage_type == WPVIVID_REMOTE_FTP){
            $storage_type = 'FTP';
        }
        return $storage_type;
    }
}