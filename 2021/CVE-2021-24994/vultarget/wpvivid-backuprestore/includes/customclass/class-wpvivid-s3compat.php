<?php
if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
if(!defined('WPVIVID_REMOTE_S3COMPAT')){
    define('WPVIVID_REMOTE_S3COMPAT','s3compat');
}
if(!defined('WPVIVID_S3COMPAT_DEFAULT_FOLDER'))
    define('WPVIVID_S3COMPAT_DEFAULT_FOLDER','/wpvivid_backup');
if(!defined('WPVIVID_S3COMPAT_NEED_PHP_VERSION'))
    define('WPVIVID_S3COMPAT_NEED_PHP_VERSION','5.3.9');
require_once WPVIVID_PLUGIN_DIR . '/includes/customclass/class-wpvivid-remote.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
class Wpvivid_S3Compat extends WPvivid_Remote{
    private $options;
    private $bucket;
    private $region;

    private $upload_chunk_size = 5242880; // All parts except the last part must be no smaller than 5MB
    private $download_chunk_size = 2097152;

    public function __construct($options = array())
    {
        if(empty($options)){
            add_action('wpvivid_add_storage_tab',array($this,'wpvivid_add_storage_tab_s3compat'), 14);
            add_action('wpvivid_add_storage_page',array($this,'wpvivid_add_storage_page_s3compat'), 14);
            add_action('wpvivid_edit_remote_page',array($this,'wpvivid_edit_storage_page_s3compat'), 14);
            add_filter('wpvivid_remote_pic',array($this,'wpvivid_remote_pic_s3compat'),11);
            add_filter('wpvivid_get_out_of_date_remote',array($this,'wpvivid_get_out_of_date_s3compat'),10,2);
            add_filter('wpvivid_storage_provider_tran',array($this,'wpvivid_storage_provider_s3compat'),10);

        }else{
            $this -> options = $options;
        }
    }

    public function getClient(){
        $res = $this -> compare_php_version();
        if($res['result'] == WPVIVID_FAILED)
            return $res;

        if(isset($this->options['s3directory']))
        {
            $path_temp = str_replace('s3generic://','',$this->options['s3directory'].$this -> options['path']);
            if (preg_match("#^/*([^/]+)/(.*)$#", $path_temp, $bmatches))
            {
                $this->bucket = $bmatches[1];
            } else {
                $this->bucket = $path_temp;
            }
            $this->options['path']=ltrim($this -> options['path'],'/');
            $endpoint_temp = str_replace('https://','',$this->options['endpoint']);
            $explodes = explode('.',$endpoint_temp);
            $this -> region = $explodes[0];
            $this -> options['endpoint'] = 'https://'.trailingslashit($endpoint_temp);
        }
        else
        {
            $endpoint_temp = str_replace('https://','',$this->options['endpoint']);
            $explodes = explode('.',$endpoint_temp);
            $this -> region = $explodes[0];
            $this -> options['endpoint'] = 'https://'.trailingslashit($endpoint_temp);
            $this -> bucket=$this->options['bucket'];
        }

        if(isset($this->options['is_encrypt']) && $this->options['is_encrypt'] == 1){
            $secret = base64_decode($this->options['secret']);
        }
        else {
            $secret = $this->options['secret'];
        }
        include_once WPVIVID_PLUGIN_DIR.'/vendor/autoload.php';
        $s3compat = S3Client::factory(
            array(
                'credentials' => array(
                    'key' => $this -> options['access'],
                    'secret' => $secret,
                ),
                'version' => 'latest',
                'region'  => $this -> region,
                'endpoint' => $this -> options['endpoint'],
            )
        );
        return $s3compat;
    }

    public function test_connect()
    {
        $s3compat = $this -> getClient();
        if(is_array($s3compat) && $s3compat['result'] == WPVIVID_FAILED)
        {
            return $s3compat;
        }

        $temp_file = md5(rand());

        try
        {
            $result = $s3compat->putObject(
                array(
                    'Bucket'=>$this->bucket,
                    'Key' =>  $this->options['path'].'/'.$temp_file,
                    'Body' => $temp_file,
                )
            );
            $etag = $result->get('ETag');
            if(!isset($etag))
            {
                return array('result'=>WPVIVID_FAILED,'error'=>'We successfully accessed the bucket, but create test file failed.');
            }
            $result = $s3compat->deleteObject(array(
                'Bucket' => $this -> bucket,
                'Key'    => $this -> options['path'].'/'.$temp_file,
            ));
            if(empty($result))
            {
                return array('result'=>WPVIVID_FAILED,'error'=>'We successfully accessed the bucket, and create test file succeed, but delete test file failed.');
            }
        }
        catch(S3Exception $e)
        {
            return array('result' => WPVIVID_FAILED,'error' => $e -> getAwsErrorCode().$e -> getMessage());
        }
        catch(Exception $e)
        {
            return array('result' => WPVIVID_FAILED,'error' => $e -> getMessage());
        }
        return array('result' => WPVIVID_SUCCESS);
    }

    public function upload($task_id, $files, $callback = '')
    {
        global $wpvivid_plugin;
        $s3compat = $this -> getClient();
        if(is_array($s3compat) && $s3compat['result'] == WPVIVID_FAILED)
        {
            return $s3compat;
        }

        $upload_job=WPvivid_taskmanager::get_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_S3COMPAT);
        if(empty($upload_job))
        {
            $job_data=array();
            foreach ($files as $file)
            {
                $file_data['size']=filesize($file);
                $file_data['uploaded']=0;
                $job_data[basename($file)]=$file_data;
            }
            WPvivid_taskmanager::update_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_S3COMPAT,WPVIVID_UPLOAD_UNDO,'Start uploading',$job_data);
            $upload_job=WPvivid_taskmanager::get_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_S3COMPAT);
        }

        foreach ($files as $file)
        {
            if(is_array($upload_job['job_data'])&&array_key_exists(basename($file),$upload_job['job_data']))
            {
                if($upload_job['job_data'][basename($file)]['uploaded']==1)
                    continue;
            }
            $this->last_time = time();
            $this->last_size = 0;
            $wpvivid_plugin->wpvivid_log->WriteLog('Start uploading '.basename($file),'notice');
            $wpvivid_plugin->set_time_limit($task_id);
            if(!file_exists($file)){
                $wpvivid_plugin->wpvivid_log->WriteLog('Uploading '.basename($file).' failed.','notice');
                return array('result' =>WPVIVID_FAILED,'error' =>$file.' not found. The file might has been moved, renamed or deleted. Please reload the list and verify the file exists.');
            }
            $result = $this->_put($task_id,$s3compat,$file,$callback);
            if($result['result'] !==WPVIVID_SUCCESS)
            {
                $wpvivid_plugin->wpvivid_log->WriteLog('Uploading '.basename($file).' failed.','notice');
                return $result;
            }
            $upload_job['job_data'][basename($file)]['uploaded']=1;
            $wpvivid_plugin->wpvivid_log->WriteLog('Finished uploading '.basename($file),'notice');
            WPvivid_taskmanager::update_backup_sub_task_progress($task_id,'upload',WPVIVID_REMOTE_S3COMPAT,WPVIVID_UPLOAD_SUCCESS,'Uploading '.basename($file).' completed.',$upload_job['job_data']);
        }
        return array('result' => WPVIVID_SUCCESS);
    }
    public function _put($task_id,$s3compat,$file,$callback){
        $path = $this->options['path'].'/'.basename($file);
        $this->current_file_size = filesize($file);
        $this->current_file_name = basename($file);

        try
        {
            if($this->current_file_size > $this->upload_chunk_size)
            {
                $result = $s3compat ->createMultipartUpload(array(
                    'Bucket'       => $this -> bucket,
                    'Key'          => $path,
                ));
                if(!isset($result['UploadId']))
                    return array('result' => WPVIVID_FAILED, 'error' => 'Creating upload task failed. Please try again.');

                $uploadId = $result['UploadId'];
                $fh = fopen($file,'rb');
                $partNumber = 1;
                $parts = array();
                $offset = 0;
                while(!feof($fh))
                {
                    $data = fread($fh,$this -> upload_chunk_size);
                    $result = $this -> _upload_loop($s3compat,$uploadId,$path,$data,$partNumber,$parts);
                    if($result['result'] === WPVIVID_FAILED)
                        break;

                    $partNumber ++;
                    $offset += $this -> upload_chunk_size;
                    if((time() - $this -> last_time) >3)
                    {
                        if(is_callable($callback))
                        {
                            call_user_func_array($callback,array(min($offset,$this -> current_file_size),$this -> current_file_name,
                                $this->current_file_size,$this -> last_time,$this -> last_size));
                        }
                        $this -> last_size = $offset;
                        $this -> last_time = time();
                    }
                }
                fclose($fh);

                if($result['result'] === WPVIVID_SUCCESS)
                {
                    $ret = $s3compat ->completeMultipartUpload
                    (
                        array(
                            'Bucket' => $this -> bucket,
                            'Key' => $path,
                            'Parts' => $parts,
                            'UploadId' => $uploadId,
                        )
                    );
                    if(!isset($ret['Location']))
                    {
                        $result = array('result' => WPVIVID_FAILED, 'error' => 'Merging multipart failed. File name: '.$this -> current_file_name);
                    }
                }
            }
            else {
                $res = $s3compat ->putObject(
                    array(
                        'Bucket'=>$this -> bucket,
                        'Key' =>  $path,
                        'SourceFile' => $file,
                    )
                );
                $etag = $res -> get('ETag');
                if(isset($etag))
                {
                    $result = array('result' => WPVIVID_SUCCESS);
                }else {
                    $result = array('result' => WPVIVID_FAILED , 'error' => 'upload '.$this -> current_file_name.' failed.');
                }
            }
        }
        catch(S3Exception $e)
        {
            return array('result' => WPVIVID_FAILED,'error' => $e -> getAwsErrorCode().$e -> getMessage());
        }
        catch(Exception $e)
        {
            return array('result' => WPVIVID_FAILED,'error' => $e -> getMessage());
        }
        return $result;
    }
    public function _upload_loop($s3compat,$uploadId,$path,$data,$partNumber,&$parts){
        for($i =0;$i <WPVIVID_REMOTE_CONNECT_RETRY_TIMES;$i ++)
        {
            $ret = $s3compat ->uploadPart(array(
                'Bucket'     => $this ->bucket,
                'Key'        => $path,
                'UploadId'   => $uploadId,
                'PartNumber' => $partNumber,
                'Body'       => $data,
            ));
            if(isset($ret['ETag']))
            {
                $parts[] = array(
                    'ETag' => $ret['ETag'],
                    'PartNumber' => $partNumber,
                );
                return array('result' => WPVIVID_SUCCESS);
            }
        }
        return array('result' => WPVIVID_FAILED,'error' =>'Multipart upload failed. File name: '.$this -> current_file_name);
    }

    public function download($file, $local_path, $callback = '')
    {
        try {
            global $wpvivid_plugin;
            $this->current_file_name = $file['file_name'];
            $this->current_file_size = $file['size'];
            $file_path = trailingslashit($local_path) . $this->current_file_name;
            $wpvivid_plugin->wpvivid_download_log->WriteLog('Get s3compat client.','notice');
            $s3compat = $this->getClient();
            if (is_array($s3compat) && $s3compat['result'] == WPVIVID_FAILED) {
                return $s3compat;
            }

            $start_offset = file_exists($file_path) ? filesize($file_path) : 0;
            $wpvivid_plugin->wpvivid_download_log->WriteLog('Create local file.','notice');
            $fh = fopen($file_path, 'a');
            $wpvivid_plugin->wpvivid_download_log->WriteLog('Downloading file ' . $file['file_name'] . ', Size: ' . $file['size'] ,'notice');
            while ($start_offset < $this->current_file_size)
            {
                $last_byte = min($start_offset + $this->download_chunk_size - 1, $this->current_file_size - 1);
                $range = "bytes=$start_offset-$last_byte";
                $response = $this->_download_loop($s3compat, $range, $fh);
                if ($response['result'] === WPVIVID_FAILED)
                {
                    return $response;
                }

                clearstatcache();
                $state = stat($file_path);
                $start_offset = $state['size'];
                if ((time() - $this->last_time) > 3)
                {
                    if (is_callable($callback)) {
                        call_user_func_array($callback, array($start_offset, $this->current_file_name,
                            $this->current_file_size, $this->last_time, $this->last_size));
                    }
                    $this->last_size = $start_offset;
                    $this->last_time = time();
                }
            }
            @fclose($fh);

            if(filesize($file_path) == $file['size'])
            {
                if($wpvivid_plugin->wpvivid_check_zip_valid())
                {
                    $res = TRUE;
                }
                else{
                    $res = FALSE;
                }
            }
            else{
                $res = FALSE;
            }

            if ($res !== TRUE) {
                @unlink($file_path);
                return array('result' => WPVIVID_FAILED, 'error' => 'Downloading ' . $file['file_name'] . ' failed. ' . $file['file_name'] . ' might be deleted or network doesn\'t work properly. Please verify the file and confirm the network connection and try again later.');
            }

            return array('result' => WPVIVID_SUCCESS);
        }
        catch (S3Exception $e) {
            return array('result' => WPVIVID_FAILED, 'error' => $e->getAwsErrorCode() . $e->getMessage());
        }
        catch (Exception $error){
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            return array('result'=>WPVIVID_FAILED, 'error'=>$message);
        }
    }

    public function _download_loop($s3compat,$range,$fh){
        try{
            for($i =0;$i <WPVIVID_REMOTE_CONNECT_RETRY_TIMES;$i ++){
                $response = $s3compat -> getObject(array(
                    'Bucket' => $this -> bucket,
                    'Key'    => $this -> options['path'].'/'.$this -> current_file_name,
                    'Range'  => $range
                ));
                if(isset($response['Body']) && fwrite($fh,$response['Body'])) {
                    return array('result' => WPVIVID_SUCCESS);
                }
            }
            return array('result'=>WPVIVID_FAILED, 'error' => 'download '.$this -> current_file_name.' failed.');
        }catch(S3Exception $e){
            return array('result' => WPVIVID_FAILED,'error' => $e -> getAwsErrorCode().$e -> getMessage());
        }catch(Exception $e){
            return array('result' => WPVIVID_FAILED,'error' => $e -> getMessage());
        }
    }

    public function cleanup($files)
    {
        $s3compat = $this -> getClient();
        if(is_array($s3compat) && $s3compat['result'] == WPVIVID_FAILED){
            return $s3compat;
        }

        $keys = array();
        foreach ($files as $file){
            $keys[] = array('Key' => $this -> options['path'].'/'.basename($file));
        }
        try{
            $result = $s3compat -> deleteObjects(array(
                // Bucket is required
                'Bucket' => $this -> bucket,
                // Objects is required
                'Objects' => $keys
            ));
        }catch (S3Exception $e){}catch (Exception $e){}
        return array('result'=>WPVIVID_SUCCESS);
    }

    public function wpvivid_add_storage_tab_s3compat(){
        ?>
        <div class="storage-providers" remote_type="s3compat" onclick="select_remote_storage(event, 'storage_account_s3compat');">
            <img src="<?php echo esc_url(WPVIVID_PLUGIN_URL.'/admin/partials/images/storage-digitalocean.png'); ?>" style="vertical-align:middle;"/><?php _e('DigitalOcean Spaces', 'wpvivid-backuprestore'); ?>
        </div>
        <?php
    }

    public function wpvivid_add_storage_page_s3compat(){
        ?>
        <div id="storage_account_s3compat"  class="storage-account-page" style="display:none;">
            <div style="padding: 0 10px 10px 0;">
                <strong><?php _e('Enter Your DigitalOcean Spaces Account', 'wpvivid-backuprestore'); ?></strong>
            </div>
            <table class="wp-list-table widefat plugins" style="width:100%;">
                <tbody>
                <form>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="s3compat" name="name" placeholder="<?php esc_attr_e('Enter a unique alias: e.g. DOS-001', 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_]/g,'')" />
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
                            <input type="text" class="regular-text" autocomplete="off" option="s3compat" name="access" placeholder="<?php esc_attr_e('DigitalOcean Spaces access key', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter your DigitalOcean Spaces access key', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="password" class="regular-text" autocomplete="new-password" option="s3compat" name="secret" placeholder="<?php esc_attr_e('DigitalOcean Spaces secret key', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter your DigitalOcean Spaces secret key', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="s3compat" name="bucket" placeholder="<?php esc_attr_e('Space Name(e.g. test)', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><span><?php _e('Enter an existed Space to create a custom backup storage directory.', 'wpvivid-backuprestore'); ?></span></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="s3compat" name="path" placeholder="<?php esc_attr_e('Custom Path', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><span><?php _e('Customize the directory where you want to store backups within the Space.', 'wpvivid-backuprestore'); ?></span></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="s3compat" name="endpoint" placeholder="<?php esc_attr_e('region.digitaloceanspaces.com', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the DigitalOcean Endpoint for the storage', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-select">
                            <label>
                                <input type="checkbox" option="s3compat" name="default" checked /><?php _e('Set as the default remote storage.', 'wpvivid-backuprestore'); ?>
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
                            <i><?php _e('Click the button to connect to DigitalOcean Spaces storage and add it to the storage list below.', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function wpvivid_edit_storage_page_s3compat()
    {
        ?>
        <div id="remote_storage_edit_s3compat" class="postbox storage-account-block remote-storage-edit" style="display:none;">
            <div style="padding: 0 10px 10px 0;">
                <strong><?php _e('Enter Your DigitalOcean Spaces Account', 'wpvivid-backuprestore'); ?></strong>
            </div>
            <table class="wp-list-table widefat plugins" style="width:100%;">
                <tbody>
                <form>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="edit-s3compat" name="name" placeholder="<?php esc_attr_e('Enter a unique alias: e.g. DOS-001', 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/[^a-zA-Z0-9\-_]/g,'')" />
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
                            <input type="text" class="regular-text" autocomplete="off" option="edit-s3compat" name="access" placeholder="<?php esc_attr_e('DigitalOcean Spaces access key', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter your DigitalOcean Spaces access key', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="password" class="regular-text" autocomplete="new-password" option="edit-s3compat" name="secret" placeholder="<?php esc_attr_e('DigitalOcean Spaces secret key', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter your DigitalOcean Spaces secret key', 'wpvivid-backuprestore'); ?></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="edit-s3compat" name="bucket" placeholder="<?php esc_attr_e('Space Name(e.g. test)', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><span><?php _e('Enter an existed Space to create a custom backup storage directory.', 'wpvivid-backuprestore'); ?></span></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="edit-s3compat" name="path" placeholder="<?php esc_attr_e('Custom Path', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><span><?php _e('Customize the directory where you want to store backups within the Space.', 'wpvivid-backuprestore'); ?></span></i>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="plugin-title column-primary">
                        <div class="wpvivid-storage-form">
                            <input type="text" class="regular-text" autocomplete="off" option="edit-s3compat" name="endpoint" placeholder="<?php esc_attr_e('region.digitaloceanspaces.com', 'wpvivid-backuprestore'); ?>" />
                        </div>
                    </td>
                    <td class="column-description desc">
                        <div class="wpvivid-storage-form-desc">
                            <i><?php _e('Enter the DigitalOcean Endpoint for the storage', 'wpvivid-backuprestore'); ?></i>
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
        <script>
            jQuery("input:text[option=edit-s3compat][name=s3directory]").keyup(function(){
                var value = jQuery(this).val();
                if(value == ''){
                    value = '*';
                }
                value = value + '/wpvivid_backup';
                jQuery('#wpvivid_edit_dos_root_path').html(value);
            });
        </script>
        <?php
    }

    public function wpvivid_remote_pic_s3compat($remote){
        $remote['s3compat']['default_pic'] = '/admin/partials/images/storage-digitalocean(gray).png';
        $remote['s3compat']['selected_pic'] = '/admin/partials/images/storage-digitalocean.png';
        $remote['s3compat']['title'] = 'DigitalOcean Spaces';
        return $remote;
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

        if(!isset($this->options['access']))
        {
            $ret['error']="Warning: The access key for S3-Compatible is required.";
            return $ret;
        }

        $this->options['access']=sanitize_text_field($this->options['access']);

        if(empty($this->options['access']))
        {
            $ret['error']="Warning: The access key for S3-Compatible is required.";
            return $ret;
        }

        if(!isset($this->options['secret']))
        {
            $ret['error']="Warning: The storage secret key is required.";
            return $ret;
        }

        $this->options['secret']=sanitize_text_field($this->options['secret']);

        if(empty($this->options['secret']))
        {
            $ret['error']="Warning: The storage secret key is required.";
            return $ret;
        }

        if(empty($this->options['bucket']))
        {
            $ret['error']="Warning: A Digital Space is required.";
            return $ret;
        }

        if(!isset($this->options['path']))
        {
            $ret['error']="Warning: A directory name is required.";
            return $ret;
        }

        $this->options['path']=sanitize_text_field($this->options['path']);

        if(empty($this->options['path'])){
            $ret['error']="Warning: A directory name is required.";
            return $ret;
        }

        if(!isset($this->options['endpoint']))
        {
            $ret['error']="Warning: The end-point is required.";
            return $ret;
        }

        $this->options['endpoint']=sanitize_text_field($this->options['endpoint']);

        $ret['result']=WPVIVID_SUCCESS;
        $ret['options']=$this->options;
        return $ret;
    }

    public function wpvivid_get_out_of_date_s3compat($out_of_date_remote, $remote)
    {
        if($remote['type'] == WPVIVID_REMOTE_S3COMPAT)
        {
            if(isset($remote['s3directory']))
                $out_of_date_remote = $remote['s3directory'].$remote['path'];
            else
                $out_of_date_remote = $remote['path'];
        }
        return $out_of_date_remote;
    }

    public function wpvivid_storage_provider_s3compat($storage_type)
    {
        if($storage_type == WPVIVID_REMOTE_S3COMPAT){
            $storage_type = 'DigitalOcean Spaces';
        }
        return $storage_type;
    }
    private function compare_php_version(){
        if(version_compare(WPVIVID_GOOGLE_NEED_PHP_VERSION,phpversion()) > 0){
            return array('result' => WPVIVID_FAILED,error => 'The required PHP version is higher than '.WPVIVID_S3COMPAT_NEED_PHP_VERSION.'. After updating your PHP version, please try again.');
        }
        return array('result' => WPVIVID_SUCCESS);
    }


}