<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
if(!defined('WPVIVID_REMOTE_SFTP'))
    define('WPVIVID_REMOTE_SFTP','sftp');
require_once WPVIVID_PLUGIN_DIR .'/includes/customclass/class-wpvivid-remote.php';

class WPvivid_SFTPClass extends WPvivid_Remote{
    private $package_size = 10;
    private $timeout = 20;
    private $error_str=false;
    private $callback;
    private $options=array();

    public function __construct($options=array())
    {
        if(empty($options))
        {
            add_action('wpvivid_add_storage_tab',array($this,'wpvivid_add_storage_tab_sftp'), 16);
            add_action('wpvivid_add_storage_page',array($this,'wpvivid_add_storage_page_sftp'), 16);
            add_action('wpvivid_edit_remote_page',array($this,'wpvivid_edit_storage_page_sftp'), 16);
            add_filter('wpvivid_remote_pic',array($this,'wpvivid_remote_pic_sftp'),10);
            add_filter('wpvivid_get_out_of_date_remote',array($this,'wpvivid_get_out_of_date_sftp'),10,2);
            add_filter('wpvivid_storage_provider_tran',array($this,'wpvivid_storage_provider_sftp'),10);
        }
        else
        {
            $this->options=$options;
        }
    }

    public function wpvivid_add_storage_tab_sftp()
    {
        ?>
        <div class="storage-providers" remote_type="sftp" onclick="select_remote_storage(event, 'storage_account_sftp');">
            <img src="<?php echo esc_url(WPVIVID_PLUGIN_URL.'/admin/partials/images/storage-sftp.png'); ?>" style="vertical-align:middle;"/><?php _e('SFTP', 'wpvivid-backuprestore'); ?>
        </div>
        <?php
    }

    public function wpvivid_add_storage_page_sftp()
    {
        ?>
        <div id="storage_account_sftp" class="storage-account-page" style="display:none;">
            <div style="padding: 0 10px 10px 0;">
                <strong><?php _e('Enter Your SFTP Account', 'wpvivid-backuprestore'); ?></strong>
            </div>
            <table class="wp-list-table widefat plugins" style="width:100%;">
                <tbody>
                <form>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="sftp" name="name" placeholder="<?php esc_attr_e('Enter a unique alias: e.g. SFTP-001', 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_]/g,'')" />
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
                            <input type="text" class="regular-text" autocomplete="off" option="sftp" name="host" placeholder="<?php esc_attr_e('Server Address', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the server address.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="sftp" name="username" placeholder="<?php esc_attr_e('User Name', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the user name.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="password" class="regular-text" autocomplete="new-password" option="sftp" name="password" placeholder="<?php esc_attr_e('User Password', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the user password.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="sftp" name="port" placeholder="<?php esc_attr_e('Port', 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/\D/g,'')" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the server port.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="sftp" name="path" placeholder="<?php esc_attr_e('Absolute path must exist(e.g. /var)', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter an absolute path and a custom subdirectory (optional) for holding the backups of current website. For example, /var/customfolder/', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-select">
                            <label>
                                <input type="checkbox" option="sftp" name="default" checked /><?php _e('Set as the default remote storage.', 'wpvivid-backuprestore'); ?>
                            </label>
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Once checked, all this sites backups sent to a remote storage destination will be uploaded to this storage by default.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                </form>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input class="button-primary" option="add-remote" type="submit" value="<?php esc_attr_e('Test and Add', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Click the button to connect to SFTP server and add it to the storage list below.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function wpvivid_edit_storage_page_sftp()
    {
        ?>
        <div id="remote_storage_edit_sftp" class="postbox storage-account-block remote-storage-edit" style="display:none;">
            <div style="padding: 0 10px 10px 0;">
                <strong><?php _e('Enter Your SFTP Account', 'wpvivid-backuprestore'); ?></strong>
            </div>
            <table class="wp-list-table widefat plugins" style="width:100%;">
                <tbody>
                <form>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="edit-sftp" name="name" placeholder="<?php esc_attr_e('Enter a unique alias: e.g. SFTP-001', 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_]/g,'')" />
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
                            <input type="text" class="regular-text" autocomplete="off" option="edit-sftp" name="host" placeholder="<?php esc_attr_e('Server Address', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the server address.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="edit-sftp" name="username" placeholder="<?php esc_attr_e('User Name', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the user name.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="password" class="regular-text" autocomplete="new-password" option="edit-sftp" name="password" placeholder="<?php esc_attr_e('User Password', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the user password.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="edit-sftp" name="port" placeholder="<?php esc_attr_e('Port', 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/\D/g,'')" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the server port.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="edit-sftp" name="path" placeholder="<?php esc_attr_e('Absolute path must exist(e.g. /var)', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter an absolute path and a custom subdirectory (optional) for holding the backups of current website. For example, /var/customfolder/', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                </form>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input class="button-primary" option="edit-remote" type="submit" value="<?php esc_attr_e('Save Changes', 'wpvivid-backuprestore'); ?>" />
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

    public function wpvivid_remote_pic_sftp($remote)
    {
        $remote['sftp']['default_pic'] = '/admin/partials/images/storage-sftp(gray).png';
        $remote['sftp']['selected_pic'] = '/admin/partials/images/storage-sftp.png';
        $remote['sftp']['title'] = 'SFTP';
        return $remote;
    }

    public function test_connect()
    {
        $host = $this->options['host'];
        $username = $this->options['username'];
        if(isset($this->options['is_encrypt']) && $this->options['is_encrypt'] == 1){
            $password = base64_decode($this->options['password']);
        }
        else {
            $password = $this->options['password'];
        }
        $path = $this->options['path'];

        $port = empty($this->options['port'])?22:$this->options['port'];

        $conn = $this->do_connect($host,$username,$password,$port);
        if(!is_subclass_of($conn,'Net_SSH2'))
        {
            return $conn;
        }
        $str = $this->do_chdir($conn,$path);
        if($str['result'] == WPVIVID_SUCCESS)
        {
            if($conn->put(trailingslashit($path) . 'testfile', 'test data', NET_SFTP_STRING))
            {
                $this -> _delete($conn ,trailingslashit($path) . 'testfile');
                return array('result'=>WPVIVID_SUCCESS);
            }
            return array('result'=>WPVIVID_FAILED,'error'=>'Failed to create a test file. Please try again later.');
        }else{
            return $str;
        }
    }

    public function sanitize_options($skip_name='')
    {
        $ret['result']=WPVIVID_FAILED;
        if(!isset($this->options['name']))
        {
            $ret['error']="Warning: An alias for remote storage is required.";
            return $ret;
        }

        $this->options['name']=sanitize_text_field($this->options['name']);

        if(empty($this->options['name']))
        {
            $ret['error']="Warning: An alias for remote storage is required.";
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

        if(!isset($this->options['host']))
        {
            $ret['error']="Warning: The IP Address is required.";
            return $ret;
        }

        $this->options['host']=sanitize_text_field($this->options['host']);

        if(empty($this->options['host']))
        {
            $ret['error']="Warning: The IP Address is required.";
            return $ret;
        }

        if(!isset($this->options['username']))
        {
            $ret['error']="Warning: The username is required.";
            return $ret;
        }

        $this->options['username']=sanitize_text_field($this->options['username']);

        if(empty($this->options['username']))
        {
            $ret['error']="Warning: The username is required.";
            return $ret;
        }

        if(!isset($this->options['password'])||empty($this->options['password']))
        {
            $ret['error']="Warning: The password is required.";
            return $ret;
        }

        //$this->options['password']=sanitize_text_field($this->options['password']);

        if(empty($this->options['password']))
        {
            $ret['error']="Warning: The password is required.";
            return $ret;
        }

        if(!isset($this->options['port']))
        {
            $ret['error']="Warning: The port number is required.";
            return $ret;
        }

        $this->options['port']=sanitize_text_field($this->options['port']);

        if(empty($this->options['port']))
        {
            $ret['error']="Warning: The port number is required.";
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

        $ret['result']=WPVIVID_SUCCESS;
        $ret['options']=$this->options;
        return $ret;
    }

	function do_connect($host,$username,$password,$port)
    {
        include_once WPVIVID_PLUGIN_DIR . '/includes/customclass/class-wpvivid-extend-sftp.php';
        $conn = new WPvivid_Net_SFTP($host,$port,$this -> timeout);
        $conn -> setTimeout($this->timeout);
        $ret = $conn->login($username,$password);
        if(!$ret)
        {
            return array('result'=>WPVIVID_FAILED,'error'=>'Login failed. You have entered the incorrect credential(s). Please try again.');
        }

		return $conn;
	}

	function do_chdir($conn,$path)
    {
        @$conn->mkdir($path);
        // See if the directory now exists
        if (!$conn->chdir($path))
        {
            @$conn->disconnect();
            return array('result'=>WPVIVID_FAILED,'error'=>'Failed to create a backup. Make sure you have sufficient privileges to perform the operation.');
        }

		return array('result'=>WPVIVID_SUCCESS);
	}

	function _delete($conn , $file)
    {
        $result = $conn ->delete($file , true);
		return $result;
	}

    public function upload($task_id,$files,$callback='')
    {
        global $wpvivid_plugin;
        $this -> callback = $callback;
        if(empty($this->options['port']))
            $this->options['port'] = 22;
        $host = $this->options['host'];
        $username = $this->options['username'];
        if(isset($this->options['is_encrypt']) && $this->options['is_encrypt'] == 1){
            $password = base64_decode($this->options['password']);
        }
        else {
            $password = $this->options['password'];
        }
        $path = $this->options['path'];
        $port = $this->options['port'];

        $upload_job=WPvivid_taskmanager::get_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_SFTP);

        if(empty($upload_job))
        {
            $job_data=array();
            foreach ($files as $file)
            {
                $file_data['size']=filesize($file);
                $file_data['uploaded']=0;
                $job_data[basename($file)]=$file_data;
            }
            WPvivid_taskmanager::update_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_SFTP,WPVIVID_UPLOAD_UNDO,'Start uploading',$job_data);
            $upload_job=WPvivid_taskmanager::get_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_SFTP);
        }

        $wpvivid_plugin->wpvivid_log->WriteLog('Connecting to server '.$host,'notice');
        $conn = $this->do_connect($host,$username,$password,$port);

        if(is_array($conn) && $conn['result'] ==WPVIVID_FAILED)
        {
            return $conn;
        }
        $wpvivid_plugin->wpvivid_log->WriteLog('chdir '.$path,'notice');
		$str = $this->do_chdir($conn,$path);
		if($str['result'] == WPVIVID_FAILED)
		    return $str;

		foreach ($files as $key => $file)
		{
		    if(is_array($upload_job['job_data']) &&array_key_exists(basename($file),$upload_job['job_data']))
            {
                if($upload_job['job_data'][basename($file)]['uploaded']==1)
                    continue;
            }
            $wpvivid_plugin->wpvivid_log->WriteLog('Start uploading '.basename($file),'notice');
		    $this -> last_time = time();
		    $this -> last_size = 0;

			if(!file_exists($file))
				return array('result'=>WPVIVID_FAILED,'error'=>$file.' not found. The file might has been moved, renamed or deleted. Please back it up again.');

            $wpvivid_plugin->set_time_limit($task_id);

			for($i =0;$i <WPVIVID_REMOTE_CONNECT_RETRY_TIMES;$i ++)
			{
                $this -> last_time = time();
                $this->current_file_name=basename($file);
                $this -> current_file_size = filesize($file);

                WPvivid_taskmanager::update_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_SFTP,WPVIVID_UPLOAD_UNDO,'Start uploading '.basename($file).'.',$upload_job['job_data']);

                $result = $conn->put(trailingslashit($path) . basename($file), $file, NET_SFTP_LOCAL_FILE| NET_SFTP_RESUME_START, -1, -1, array($this , 'upload_callback'));

                if($result)
                {
                    $wpvivid_plugin->wpvivid_log->WriteLog('Finished uploading '.basename($file),'notice');
                    $upload_job['job_data'][basename($file)]['uploaded']=1;
                    WPvivid_taskmanager::update_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_SFTP,WPVIVID_UPLOAD_SUCCESS,'Uploading '.basename($file).' completed.',$upload_job['job_data']);
                    break;
                }

                if(!$result && $i == (WPVIVID_REMOTE_CONNECT_RETRY_TIMES - 1))
                {
                    $conn -> disconnect();
                    return array('result'=>WPVIVID_FAILED,'error'=>'Uploading '.$file.' to SFTP server failed. '.$file.' might be deleted or network doesn\'t work properly. Please verify the file and confirm the network connection and try again later.');
                }
                sleep(WPVIVID_REMOTE_CONNECT_RETRY_INTERVAL);
            }
		}
		$conn -> disconnect();
		return array('result'=>WPVIVID_SUCCESS);
	}

    public function download($file,$local_path,$callback = '')
    {
        try {
            global $wpvivid_plugin;
            $this->callback = $callback;
            $this->current_file_name = $file['file_name'];
            $this->current_file_size = $file['size'];

            $host = $this->options['host'];
            $username = $this->options['username'];
            if(isset($this->options['is_encrypt']) && $this->options['is_encrypt'] == 1){
                $password = base64_decode($this->options['password']);
            }
            else {
                $password = $this->options['password'];
            }
            $path = $this->options['path'];
            $port = empty($this->options['port']) ? 22 : $this->options['port'];
            $local_path = trailingslashit($local_path) . $file['file_name'];
            $file_size = $file['size'];
            $remote_file_name = trailingslashit($path) . $file['file_name'];

            $wpvivid_plugin->wpvivid_download_log->WriteLog('Connecting SFTP server.','notice');
            $conn = $this->do_connect($host, $username, $password, $port);
            $progress = 0;
            if (!is_subclass_of($conn, 'Net_SSH2')) {
                return $conn;
            }
            $wpvivid_plugin->wpvivid_download_log->WriteLog('Create local file.','notice');
            $local_file = fopen($local_path, 'ab');
            if (!$local_file) {
                return array('result' => WPVIVID_FAILED, 'error' => 'Unable to create the local file. Please make sure the folder is writable and try again.');
            }
            $stat = fstat($local_file);
            $offset = $stat['size'];
            $progress = floor(($offset / $file_size) * 100);

            $wpvivid_plugin->wpvivid_download_log->WriteLog('Downloading file ' . $file['file_name'] . ', Size: ' . $file['size'] ,'notice');
            $result = $conn->get($remote_file_name, $local_file, $offset, -1, array($this, 'download_callback'));
            @fclose($local_file);

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

            if ($result && $res) {
                return array('result' => WPVIVID_SUCCESS);
            } else {
                return array('result' => WPVIVID_FAILED, 'error' => 'Downloading ' . $remote_file_name . ' failed. ' . $remote_file_name . ' might be deleted or network doesn\'t work properly. Please verify the file and confirm the network connection and try again later.');
            }
        }
        catch (Exception $error){
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            return array('result'=>WPVIVID_FAILED, 'error'=>$message);
        }
    }

    public function delete($remote,$files){
        $host = $remote['options']['host'];
        $username = $remote['options']['username'];
        if(isset($remote['options']['is_encrypt']) && $remote['options']['is_encrypt'] == 1){
            $password = base64_decode($remote['options']['password']);
        }
        else {
            $password = $remote['options']['password'];
        }
        $path = $remote['options']['path'];
        $port = empty($remote['options']['port'])?22:$remote['options']['port'];

	    $conn = $this->do_connect($host,$username,$password,$port);
	    if(!is_subclass_of($conn,'Net_SSH2')){
		    return $conn;
	    }
	    foreach ($files as $file)
	    {
            $file=trailingslashit($path).$file;
            $this -> _delete($conn , $file);
        }
	    return array('result'=>WPVIVID_SUCCESS);
    }
    public function get_last_error()
    {
        if($this->error_str===false)
        {
            $this->error_str='connection time out.';
        }
        return $this->error_str;
    }
    public function upload_callback($offset){
        if((time() - $this -> last_time) >3)
        {
            if(is_callable($this -> callback)){
                call_user_func_array($this -> callback,array($offset,$this -> current_file_name,
                    $this->current_file_size,$this -> last_time,$this -> last_size));
            }
            $this -> last_size = $offset;
            $this -> last_time = time();
        }
    }
    public function download_callback($offset){
        if((time() - $this -> last_time) >3){
            if(is_callable($this -> callback)){
                call_user_func_array($this -> callback,array($offset,$this -> current_file_name,
                    $this->current_file_size,$this -> last_time,$this -> last_size));
            }
            $this -> last_size = $offset;
            $this -> last_time = time();
        }
    }

    public function cleanup($files)
    {
        $remote['options'] = $this -> options;
        return $this -> delete($remote,$files);
    }

    public function wpvivid_get_out_of_date_sftp($out_of_date_remote, $remote)
    {
        if($remote['type'] == WPVIVID_REMOTE_SFTP){
            $out_of_date_remote = $remote['path'];
        }
        return $out_of_date_remote;
    }

    public function wpvivid_storage_provider_sftp($storage_type)
    {
        if($storage_type == WPVIVID_REMOTE_SFTP){
            $storage_type = 'SFTP';
        }
        return $storage_type;
    }
}