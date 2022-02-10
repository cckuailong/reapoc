<?php

if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class Wpvivid_BackupUploader
{
    public function __construct()
    {
        add_action('wp_ajax_wpvivid_get_file_id',array($this,'get_file_id'));
        add_action('wp_ajax_wpvivid_upload_files',array($this,'upload_files'));
        add_action('wp_ajax_wpvivid_upload_files_finish',array($this,'upload_files_finish'));

        add_action('wp_ajax_wpvivid_rescan_local_folder',array($this,'rescan_local_folder_set_backup'));
        add_action('wp_ajax_wpvivid_get_backup_count',array($this,'get_backup_count'));
        add_action('wpvivid_rebuild_backup_list', array($this, 'wpvivid_rebuild_backup_list'), 10);
    }

    function get_file_id()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        if(isset($_POST['file_name']))
        {
            if(preg_match('/wpvivid-.*_.*_.*\.zip$/',$_POST['file_name']))
            {
                if(preg_match('/wpvivid-(.*?)_/',$_POST['file_name'],$matches))
                {
                    $id= $matches[0];
                    $id=substr($id,0,strlen($id)-1);
                    if(WPvivid_Backuplist::get_backup_by_id($id)===false)
                    {
                        $ret['result']=WPVIVID_SUCCESS;
                        $ret['id']=$id;
                    }
                    else
                    {
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']='The uploading backup already exists in Backups list.';
                    }
                }
                else
                {
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']=$_POST['file_name'] . ' is not created by WPvivid backup plugin.';
                }
            }
            else
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']=$_POST['file_name'] . ' is not created by WPvivid backup plugin.';
            }
        }
        else
        {
            $ret['result']=WPVIVID_FAILED;
            $ret['error']='Failed to post file name.';
        }

        echo json_encode($ret);
        die();
    }

    function check_file_is_a_wpvivid_backup($file_name,&$backup_id)
    {
        if(preg_match('/wpvivid-.*_.*_.*\.zip$/',$file_name))
        {
            if(preg_match('/wpvivid-(.*?)_/',$file_name,$matches))
            {
                $id= $matches[0];
                $id=substr($id,0,strlen($id)-1);


                if(WPvivid_Backuplist::get_backup_by_id($id)===false)
                {
                    $backup_id=$id;
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    function upload_files()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        $options['test_form'] =true;
        $options['action'] ='wpvivid_upload_files';
        $options['test_type'] = false;
        $options['ext'] = 'zip';
        $options['type'] = 'application/zip';

        add_filter('upload_dir', array($this, 'upload_dir'));

        $status = wp_handle_upload($_FILES['async-upload'],$options);

        remove_filter('upload_dir', array($this, 'upload_dir'));

        if (isset($status['error']))
        {
            echo json_encode(array('result'=>WPVIVID_FAILED, 'error' => $status['error']));
            exit;
        }

        $file_name=basename($_POST['name']);

        if (isset($_POST['chunks']) && isset($_POST['chunk']))
        {
            $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR;
            rename($status['file'],$path.$file_name.'_'.$_POST['chunk'].'.tmp');
            $status['file'] = $path.$file_name.'_'.$_POST['chunk'].'.tmp';
            if($_POST['chunk'] == $_POST['chunks']-1)
            {
                $file_handle = fopen($path.$file_name, 'wb');
                if ($file_handle)
                {
                    for ($i=0; $i<$_POST['chunks']; $i++)
                    {
                        $chunks_handle=fopen($path.$file_name.'_'.$i.'.tmp','rb');
                        if($chunks_handle)
                        {
                            while ($line = fread($chunks_handle, 1048576*2))
                            {
                                fwrite($file_handle, $line);
                            }
                            fclose($chunks_handle);
                            @unlink($path.$file_name.'_'.$i.'.tmp');
                        }
                    }
                    fclose($file_handle);
                }
            }
        }
        echo json_encode(array('result'=>WPVIVID_SUCCESS));
        die();
    }

    function upload_files_finish()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $ret['html']=false;
        if(isset($_POST['files']))
        {
            $files =stripslashes($_POST['files']);
            $files =json_decode($files,true);
            if(is_null($files))
            {
                die();
            }

            $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR;

            $backup_data['result']='success';
            $backup_data['files']=array();
            if(preg_match('/wpvivid-.*_.*_.*\.zip$/',$files[0]['name']))
            {
                if(preg_match('/wpvivid-(.*?)_/',$files[0]['name'],$matches_id))
                {
                    if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}/',$files[0]['name'],$matches))
                    {
                        $backup_time=$matches[0];
                        $time_array=explode('-',$backup_time);
                        if(sizeof($time_array)>4)
                            $time=$time_array[0].'-'.$time_array[1].'-'.$time_array[2].' '.$time_array[3].':'.$time_array[4];
                        else
                            $time=$backup_time;
                        $time=strtotime($time);
                    }
                    else
                    {
                        $time=time();
                    }
                    $id= $matches_id[0];
                    $id=substr($id,0,strlen($id)-1);
                    $unlinked_file = '';
                    $check_result=true;
                    foreach ($files as $file)
                    {
                        $res=$this->check_is_a_wpvivid_backup($path.$file['name']);
                        if($res === true)
                        {
                            $add_file['file_name']=$file['name'];
                            $add_file['size']=filesize($path.$file['name']);
                            $backup_data['files'][]=$add_file;
                        }
                        else
                        {
                            $check_result=false;
                            $unlinked_file .= 'file name: '.$file['name'].', error: '.$res;
                        }
                    }
                    if($check_result === true){
                        WPvivid_Backuplist::add_new_upload_backup($id,$backup_data,$time,'');
                        $html = '';
                        $html = apply_filters('wpvivid_add_backup_list', $html);
                        $ret['result']=WPVIVID_SUCCESS;
                        $ret['html'] = $html;
                    }
                    else{
                        foreach ($files as $file) {
                            $this->clean_tmp_files($path, $file['name']);
                            @unlink($path . $file['name']);
                        }
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']='Upload file failed.';
                        $ret['unlinked']=$unlinked_file;
                    }
                }
                else
                {
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']='The backup is not created by WPvivid backup plugin.';
                }
            }
            else
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']='The backup is not created by WPvivid backup plugin.';
            }
        }
        else{
            $ret['result']=WPVIVID_FAILED;
            $ret['error']='Failed to post file name.';
        }
        echo json_encode($ret);
        die();
    }

    function clean_tmp_files($path, $filename){
        $handler=opendir($path);
        if($handler===false)
            return;
        while(($file=readdir($handler))!==false) {
            if (!is_dir($path.$file) && preg_match('/wpvivid-.*_.*_.*\.tmp$/', $file)) {
                $iPos = strrpos($file, '_');
                $file_temp = substr($file, 0, $iPos);
                if($file_temp === $filename) {
                    @unlink($path.$file);
                }
            }
        }
        @closedir($handler);
    }

    function wpvivid_check_remove_update_backup($path){
        $backup_list = WPvivid_Setting::get_option('wpvivid_backup_list');
        $remove_backup_array = array();
        $update_backup_array = array();
        $tmp_file_array = array();
        $remote_backup_list=WPvivid_Backuplist::get_has_remote_backuplist();
        foreach ($backup_list as $key => $value){
            if(!in_array($key, $remote_backup_list)) {
                $need_remove = true;
                $need_update = false;
                if (is_dir($path)) {
                    $handler = opendir($path);
                    if($handler===false)
                        return true;
                    while (($filename = readdir($handler)) !== false) {
                        if ($filename != "." && $filename != "..") {
                            if (!is_dir($path . $filename)) {
                                if ($this->check_wpvivid_file_info($filename, $backup_id, $need_update)) {
                                    if ($key === $backup_id) {
                                        $need_remove = false;
                                    }
                                    if ($need_update) {
                                        if ($this->check_is_a_wpvivid_backup($path . $filename) === true) {
                                            if (!in_array($filename, $tmp_file_array)) {
                                                $add_file['file_name'] = $filename;
                                                $add_file['size'] = filesize($path . $filename);
                                                $tmp_file_array[] = $filename;
                                                $update_backup_array[$backup_id]['files'][] = $add_file;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($handler) {
                        @closedir($handler);
                    }
                }
                if ($need_remove) {
                    $remove_backup_array[] = $key;
                }
            }
        }
        $this->wpvivid_remove_update_local_backup_list($remove_backup_array, $update_backup_array);
        return true;
    }

    function check_wpvivid_file_info($file_name, &$backup_id, &$need_update=false){
        if(preg_match('/wpvivid-.*_.*_.*\.zip$/',$file_name))
        {
            if(preg_match('/wpvivid-(.*?)_/',$file_name,$matches))
            {
                $id= $matches[0];
                $id=substr($id,0,strlen($id)-1);
                $backup_id=$id;

                if(WPvivid_Backuplist::get_backup_by_id($id)===false)
                {
                    $need_update = false;
                    return true;
                }
                else
                {
                    $need_update = true;
                    return true;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    function wpvivid_remove_update_local_backup_list($remove_backup_array, $update_backup_array){
        $backup_list = WPvivid_Setting::get_option('wpvivid_backup_list');
        foreach ($remove_backup_array as $remove_backup_id){
            unset($backup_list[$remove_backup_id]);
        }
        foreach ($update_backup_array as $update_backup_id => $data){
            $backup_list[$update_backup_id]['backup']['files'] = $data['files'];
        }
        WPvivid_Setting::update_option('wpvivid_backup_list', $backup_list);
    }

    function _rescan_local_folder_set_backup(){
        $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR;

        $this->wpvivid_check_remove_update_backup($path);

        $backups=array();
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

                        if (is_dir($path  . $filename))
                        {
                            continue;
                        } else {
                            if($this->check_file_is_a_wpvivid_backup($filename,$backup_id))
                            {
                                if($this->zip_check_sum($path . $filename))
                                {
                                    if($this->check_is_a_wpvivid_backup($path.$filename) === true)
                                        $backups[$backup_id]['files'][]=$filename;
                                }
                            }
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }
        }
        else{
            $ret['result']=WPVIVID_FAILED;
            $ret['error']='Failed to get local storage directory.';
        }
        if(!empty($backups))
        {
            foreach ($backups as $backup_id =>$backup)
            {
                $backup_data['result']='success';
                $backup_data['files']=array();
                if(empty($backup['files']))
                    continue;
                $time=false;
                foreach ($backup['files'] as $file)
                {
                    if($time===false)
                    {
                        if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}/',$file,$matches))
                        {
                            $backup_time=$matches[0];
                            $time_array=explode('-',$backup_time);
                            if(sizeof($time_array)>4)
                                $time=$time_array[0].'-'.$time_array[1].'-'.$time_array[2].' '.$time_array[3].':'.$time_array[4];
                            else
                                $time=$backup_time;
                            $time=strtotime($time);
                        }
                        else
                        {
                            $time=time();
                        }
                    }

                    $add_file['file_name']=$file;
                    $add_file['size']=filesize($path.$file);
                    $backup_data['files'][]=$add_file;
                }

                WPvivid_Backuplist::add_new_upload_backup($backup_id,$backup_data,$time,'');
            }
        }
        $ret['result']=WPVIVID_SUCCESS;
        $html = '';
        $tour = true;
        $html = apply_filters('wpvivid_add_backup_list', $html, 'wpvivid_backup_list', $tour);
        $ret['html'] = $html;
        return $ret;
    }

    function rescan_local_folder_set_backup()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $ret = $this->_rescan_local_folder_set_backup();
        echo json_encode($ret);
        die();
    }

    public function wpvivid_rebuild_backup_list(){
        $this->_rescan_local_folder_set_backup();
    }

    static function rescan_local_folder()
    {
        $backupdir=WPvivid_Setting::get_backupdir();
        ?>
        <div style="padding-top: 10px;">
            <span><?php _e('Tips: Click the button below to scan all uploaded or received backups in directory', 'wpvivid-backuprestore'); ?>&nbsp<?php echo WP_CONTENT_DIR.'/'.$backupdir; ?></span>
        </div>
        <div style="padding-top: 10px;">
            <input type="submit" class="button-primary" value="<?php esc_attr_e('Scan uploaded backup or received backup', 'wpvivid-backuprestore'); ?>" onclick="wpvivid_rescan_local_folder();" />
        </div>
        <script type="text/javascript">
            function wpvivid_rescan_local_folder()
            {
                var ajax_data = {
                    'action': 'wpvivid_rescan_local_folder'
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        if(jsonarray.html !== false){
                            jQuery('#wpvivid_backup_list').html('');
                            jQuery('#wpvivid_backup_list').append(jsonarray.html);
                            wpvivid_popup_tour('show');
                        }
                    }
                    catch(err) {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('scanning backup list', textStatus, errorThrown);
                    alert(error_message);
                });
            }
        </script>
        <?php
    }

    function get_backup_count()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $backuplist=WPvivid_Backuplist::get_backuplist();
        echo sizeof($backuplist);
        die();
    }

    static function upload_meta_box()
    {
        ?>
        <div id="wpvivid_plupload-upload-ui" class="hide-if-no-js">
            <div id="drag-drop-area">
                <div class="drag-drop-inside">
                    <p class="drag-drop-info"><?php _e('Drop files here', 'wpvivid-backuprestore'); ?></p>
                    <p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
                    <p class="drag-drop-buttons"><input id="wpvivid_select_file_button" type="button" value="<?php esc_attr_e('Select Files', 'wpvivid-backuprestore'); ?>" class="button" /></p>
                </div>
            </div>
        </div>
        <div id="wpvivid_upload_file_list" class="hide-if-no-js" style="padding-top: 10px; padding-bottom: 10px;"></div>
        <input type="submit" class="button-primary" id="wpvivid_upload_submit_btn" value="Upload" onclick="wpvivid_submit_upload();" />
        <?php
        $chunk_size = min(wp_max_upload_size()-1024, 1048576*2);
        $plupload_init = array(
            'runtimes'            => 'html5,silverlight,flash,html4',
            'browse_button'       => 'wpvivid_select_file_button',
            'container'           => 'wpvivid_plupload-upload-ui',
            'drop_element'        => 'drag-drop-area',
            'file_data_name'      => 'async-upload',
            'max_retries'		    => 3,
            'multiple_queues'     => true,
            'max_file_size'       => '10Gb',
            'chunk_size'        => $chunk_size.'b',
            'url'                 => admin_url('admin-ajax.php'),
            'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
            'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
            'multipart'           => true,
            'urlstream_upload'    => true,
            // additional post data to send to our ajax hook
            'multipart_params'    => array(
                '_ajax_nonce' => wp_create_nonce('wpvivid_ajax'),
                'action'      => 'wpvivid_upload_files',            // the ajax action name
            ),
        );

        if (is_file(ABSPATH.WPINC.'/js/plupload/Moxie.swf')) {
            $plupload_init['flash_swf_url'] = includes_url('js/plupload/Moxie.swf');
        } else {
            $plupload_init['flash_swf_url'] = includes_url('js/plupload/plupload.flash.swf');
        }

        if (is_file(ABSPATH.WPINC.'/js/plupload/Moxie.xap')) {
            $plupload_init['silverlight_xap_url'] = includes_url('js/plupload/Moxie.xap');
        } else {
            $plupload_init['silverlight_xap_url'] = includes_url('js/plupload/plupload.silverlight.swf');
        }

        // we should probably not apply this filter, plugins may expect wp's media uploader...
        $plupload_init = apply_filters('plupload_init', $plupload_init);
        $upload_file_image = includes_url( '/images/media/archive.png' );
        ?>


        <script type="text/javascript">

            var uploader;
            jQuery(document).ready(function($)
            {
                // create the uploader and pass the config from above
                jQuery('#wpvivid_upload_submit_btn').hide();
                uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

                // checks if browser supports drag and drop upload, makes some css adjustments if necessary
                uploader.bind('Init', function(up)
                {
                    var uploaddiv = $('#wpvivid_plupload-upload-ui');

                    if(up.features.dragdrop){
                        uploaddiv.addClass('drag-drop');
                        $('#drag-drop-area')
                            .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                            .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

                    }else{
                        uploaddiv.removeClass('drag-drop');
                        $('#drag-drop-area').unbind('.wp-uploader');
                    }
                });
                uploader.init();
                // a file was added in the queue
                var wpvivid_upload_id='';

                function wpvivid_check_plupload_added_files(up,files)
                {
                    if(wpvivid_upload_id==='')
                    {
                        var file=files[0];

                        var ajax_data = {
                            'action': 'wpvivid_get_file_id',
                            'file_name':file.name
                        };
                        wpvivid_post_request(ajax_data, function (data)
                        {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.result === "success")
                            {
                                wpvivid_upload_id=jsonarray.id;
                                wpvivid_check_plupload_added_files(up,files);
                            }
                            else if(jsonarray.result === "failed") {
                                uploader.removeFile(file);
                                alert(jsonarray.error);
                            }
                        }, function (XMLHttpRequest, textStatus, errorThrown)
                        {
                            var error_message = wpvivid_output_ajaxerror('uploading backups', textStatus, errorThrown);
                            uploader.removeFile(file);
                            alert(error_message);
                        });
                    }
                    else
                    {
                        var repeat_files = '';
                        plupload.each(files, function(file)
                        {
                            var brepeat=false;
                            var file_list = jQuery('#wpvivid_upload_file_list span');
                            file_list.each(function (index, value) {
                                if (value.innerHTML === file.name) {
                                    brepeat=true;
                                }
                            });
                            if(!brepeat) {
                                var wpvivid_file_regex = new RegExp(wpvivid_upload_id + '_.*_.*\\.zip$');
                                if (wpvivid_file_regex.test(file.name)) {
                                    jQuery('#wpvivid_upload_file_list').append(
                                        '<div id="' + file.id + '" style="width: 100%; height: 36px; background: #fff; margin-bottom: 1px;">' +
                                        '<img src=" <?php echo $upload_file_image; ?> " alt="" style="float: left; margin: 2px 10px 0 3px; max-width: 40px; max-height: 32px;">' +
                                        '<div style="line-height: 36px; float: left; margin-left: 5px;"><span>' + file.name + '</span></div>' +
                                        '<div class="fileprogress" style="line-height: 36px; float: right; margin-right: 5px;"></div>' +
                                        '</div>' +
                                        '<div style="clear: both;"></div>'
                                    );
                                    jQuery('#wpvivid_upload_submit_btn').show();
                                    jQuery("#wpvivid_upload_submit_btn").prop('disabled', false);
                                }
                                else {
                                    alert(file.name + " is not belong to the backup package uploaded.");
                                    uploader.removeFile(file);
                                }
                            }
                            else{
                                if(repeat_files === ''){
                                    repeat_files += file.name;
                                }
                                else{
                                    repeat_files += ', ' + file.name;
                                }
                            }
                        });
                        if(repeat_files !== ''){
                            alert(repeat_files + " already exists in upload list.");
                            repeat_files = '';
                        }
                    }
                }

                uploader.bind('FilesAdded', wpvivid_check_plupload_added_files);

                uploader.bind('Error', function(up, error)
                {
                    alert('Upload ' + error.file.name +' error, error code: ' + error.code + ', ' + error.message);
                    console.log(error);
                });

                uploader.bind('FileUploaded', function(up, file, response)
                {
                    var jsonarray = jQuery.parseJSON(response.response);
                    if(jsonarray.result == 'failed'){
                        alert('upload ' + file.name + ' failed, ' + jsonarray.error);
                    }
                });

                uploader.bind('UploadProgress', function(up, file)
                {
                    jQuery('#' + file.id + " .fileprogress").html(file.percent + "%");
                });

                uploader.bind('UploadComplete',function(up, files)
                {
                    jQuery('#wpvivid_upload_file_list').html("");
                    wpvivid_upload_id='';
                    jQuery('#wpvivid_upload_submit_btn').hide();
                    jQuery("#wpvivid_select_file_button").prop('disabled', false);
                    var ajax_data = {
                        'action': 'wpvivid_upload_files_finish',
                        'files':JSON.stringify(files)
                    };
                    wpvivid_post_request(ajax_data, function (data)
                    {
                        try {
                            var jsonarray = jQuery.parseJSON(data);
                            if(jsonarray.result === 'success') {
                                if (jsonarray.html !== false) {
                                    var unlinked = '';
                                    if(typeof jsonarray.unlinked !== 'undefined'){
                                        unlinked = jsonarray.unlinked;
                                    }
                                    alert('The upload has completed. ' + unlinked);
                                    jQuery('#wpvivid_backup_list').html('');
                                    jQuery('#wpvivid_backup_list').append(jsonarray.html);
                                    wpvivid_click_switch_page('backup', 'wpvivid_tab_backup', true);
                                }
                            }
                            else if(jsonarray.result === 'failed'){
                                var unlinked = '';
                                if(typeof jsonarray.unlinked !== 'undefined'){
                                    unlinked = jsonarray.unlinked;
                                }
                                alert(jsonarray.error + ' ' + unlinked);
                            }
                        }
                        catch(err) {
                            alert(err);
                        }
                    }, function (XMLHttpRequest, textStatus, errorThrown)
                    {
                        var error_message = wpvivid_output_ajaxerror('refreshing backup list', textStatus, errorThrown);
                        alert(error_message);
                    });
                    plupload.each(files, function(file)
                    {
                        if(typeof file === 'undefined')
                        {

                        }
                        else
                        {
                            uploader.removeFile(file.id);
                        }
                    });
                })
            });

            function wpvivid_submit_upload()
            {
                var backup_max_count='<?php
                    $general_setting=WPvivid_Setting::get_setting(true, "");
                    $display_backup_count = $general_setting['options']['wpvivid_common_setting']['max_backup_count'];
                    echo intval($display_backup_count);
                    ?>';
                var ajax_data = {
                    'action': 'wpvivid_get_backup_count'
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    var backuplist_count = data;
                    if(parseInt(backuplist_count) >= parseInt(backup_max_count)){
                        alert("The backup retention limit for localhost(web server) is reached, please either increase the retention from WPvivid General Settings, or manually delete some old backups.");
                    }
                    else {
                        jQuery("#wpvivid_upload_submit_btn").prop('disabled', true);
                        jQuery("#wpvivid_select_file_button").prop('disabled', true);
                        uploader.refresh();
                        uploader.start();
                    }

                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('uploading backups', textStatus, errorThrown);
                    alert(error_message);
                });
            }
        </script>
        <?php
    }

    public function upload_dir($uploads)
    {
        $uploads['path'] = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir();
        return $uploads;
    }

    private function check_is_a_wpvivid_backup($file_name)
    {
        $ret=WPvivid_Backup_Item::get_backup_file_info($file_name);
        if($ret['result'] === WPVIVID_SUCCESS){
            return true;
        }
        elseif($ret['result'] === WPVIVID_FAILED){
            return $ret['error'];
        }
    }

    private function zip_check_sum($file_name)
    {
        return true;
    }
}