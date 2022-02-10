<?php

if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

class WPvivid_Migrate
{
    public function __construct()
    {
        add_filter('wpvivid_add_tab_page', array($this, 'wpvivid_add_migrate_tab_page'));
        add_action('wp_ajax_wpvivid_generate_url',array( $this,'generate_url'));
        add_action('wp_ajax_wpvivid_send_backup_to_site',array( $this,'send_backup_to_site'));
        add_action('wp_ajax_wpvivid_migrate_now',array( $this,'migrate_now'));
        add_filter('wpvivid_backuppage_load_backuplist', array($this, 'wpvivid_backuppage_load_backuplist'));

        add_action('wp_ajax_wpvivid_export_download_backup',array( $this,'export_download_backup'));
        add_action('wp_ajax_wpvivid_list_upload_tasks',array( $this,'list_tasks'));
        add_action('wp_ajax_wpvivid_test_connect_site',array( $this,'test_connect_site'));
        add_action('wp_ajax_wpvivid_delete_transfer_key',array($this, 'delete_transfer_key'));

        add_filter('wpvivid_put_transfer_key', array($this, 'wpvivid_put_transfer_key'));
        add_action('wpvivid_handle_backup_failed',array($this,'wpvivid_handle_backup_failed'),9);

        add_action('wpvivid_rescan_backup_list', array($this, 'wpvivid_rescan_backup_list'));
        add_action('wpvivid_handle_upload_succeed',array($this,'wpvivid_deal_upload_succeed'),11);

        add_filter('wpvivid_add_migrate_type', array($this, 'wpvivid_add_migrate_type'), 11, 2);
        add_filter('wpvivid_migrate_descript', array($this, 'wpvivid_migrate_descript'));
        add_filter('wpvivid_migrate_part_type', array($this, 'wpvivid_migrate_part_type'));
        add_filter('wpvivid_migrate_part_exec', array($this, 'wpvivid_migrate_part_exec'));
        add_filter('wpvivid_migrate_part_note', array($this, 'wpvivid_migrate_part_note'));
        add_filter('wpvivid_migrate_part_tip', array($this, 'wpvivid_migrate_part_tip'));

        add_filter('wpvivid_load_migrate_js', array($this, 'wpvivid_load_migrate_js'));
        add_action('wpvivid_add_migrate_js', array($this, 'wpvivid_add_migrate_js'));
    }

    public function wpvivid_add_migrate_tab_page($page_array){
        $page_array['migrate'] = array('index' => '3', 'tab_func' => array($this, 'wpvivid_add_tab_migrate'), 'page_func' => array($this, 'wpvivid_add_page_migrate'));
        $page_array['key'] = array('index' => '8', 'tab_func' => array($this, 'wpvivid_add_tab_key'), 'page_func' => array($this, 'wpvivid_add_page_key'));
        return $page_array;
    }

    public function wpvivid_add_tab_migrate()
    {
        ?>
        <a href="#" id="wpvivid_tab_migrate" class="nav-tab wrap-nav-tab" onclick="switchTabs(event,'migrate-page')"><?php _e('Auto-Migration', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function wpvivid_add_tab_key(){
        ?>
        <a href="#" id="wpvivid_tab_key" class="nav-tab wrap-nav-tab" onclick="switchTabs(event,'key-page')"><?php _e('Key', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function wpvivid_load_migrate_js($html){
        do_action('wpvivid_add_migrate_js');
        return $html;
    }

    public function wpvivid_add_migrate_js(){
        ?>
        <script>
            var wpvivid_home_url = '<?php
                $wpvivid_siteurl = array();
                $wpvivid_siteurl=WPvivid_Admin::wpvivid_get_siteurl();
                echo esc_url($wpvivid_siteurl['home_url']);
                ?>';

            jQuery('input:radio[option=migrate][name=transfer]').click(function(){
                var value = jQuery(this).prop('value');
                if(value === 'transfer'){
                    jQuery('#wpvivid_transfer_btn').show();
                    jQuery('#wpvivid_export_download_btn').hide();
                }
                else if(value === 'export'){
                    jQuery('#wpvivid_transfer_btn').hide();
                    jQuery('#wpvivid_export_download_btn').show();
                }
            });
            //wpvivid_edit_url_button
            jQuery('#wpvivid_add_remote_site_url').show();
            jQuery('#wpvivid_upload_backup_percent').hide();

            var wpvivid_transfer_id = '';



            function wpvivid_control_transfer_lock(){
                jQuery('#wpvivid_quickbackup_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                jQuery('#wpvivid_transfer_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                jQuery("#wpvivid_delete_key_button").css({'pointer-events': 'none', 'opacity': '0.4'});
            }

            function wpvivid_control_transfer_unlock(){
                jQuery('#wpvivid_quickbackup_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                jQuery('#wpvivid_transfer_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                jQuery("#wpvivid_delete_key_button").css({'pointer-events': 'auto', 'opacity': '1'});
            }



            function wpvivid_click_export_backup()
            {
                var option_data = wpvivid_ajax_data_transfer('migrate');
                var ajax_data = {
                    'action': 'wpvivid_export_download_backup',
                    'backup_options':option_data
                };
                migrate_task_need_update=true;
                jQuery('#wpvivid_export_download_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function (data)
                {
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('test generate url', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            var wpvivid_display_get_key = false;



            function wpvivid_transfer_cancel_flow()
            {
                jQuery('#wpvivid_transfer_cancel_btn').click(function(){
                    wpvivid_cancel_transfer();
                });
            }

            function wpvivid_cancel_transfer()
            {
                var ajax_data= {
                    'action': 'wpvivid_backup_cancel',
                    'task_id': wpvivid_transfer_id
                };
                jQuery('#wpvivid_transfer_cancel_btn').css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function(data){
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        jQuery('#wpvivid_upload_current_doing').html(jsonarray.msg);
                    }
                    catch(err){
                        alert(err);
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown) {
                    jQuery('#wpvivid_transfer_cancel_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('cancelling the backup', textStatus, errorThrown);
                    wpvivid_add_notice('Backup', 'Error', error_message);
                });
            }

            var migrate_task_need_update=true;
            var task_recheck_times=0;
            function wpvivid_check_upload_runningtask()
            {
                var ajax_data = {
                    'action': 'wpvivid_list_upload_tasks',
                };
                if(wpvivid_restoring === false) {
                    wpvivid_post_request(ajax_data, function (data) {
                        setTimeout(function () {
                            wpvivid_manage_upload_task();
                        }, 3000);
                        try {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.transfer_succeed_notice != false) {
                                jQuery('#wpvivid_backup_notice').show();
                                jQuery('#wpvivid_backup_notice').append(jsonarray.transfer_succeed_notice);
                            }
                            if (jsonarray.transfer_error_notice != false) {
                                jQuery('#wpvivid_backup_notice').show();
                                jQuery.each(jsonarray.transfer_error_notice, function (index, value) {
                                    jQuery('#wpvivid_backup_notice').append(value.error_msg);
                                });
                            }
                            var b_need_show = false;
                            if (jsonarray.transfer.data.length !== 0) {
                                b_need_show = true;
                                task_recheck_times = 0;
                                if (jsonarray.transfer.result === 'success') {
                                    jQuery.each(jsonarray.transfer.data, function (index, value) {
                                        if (value.status.str === 'ready') {
                                            wpvivid_control_transfer_lock();
                                            jQuery('#wpvivid_upload_backup_percent').show();
                                            jQuery('#wpvivid_upload_backup_percent').html(value.progress_html);
                                            migrate_task_need_update = true;
                                        }
                                        else if (value.status.str === 'running') {
                                            wpvivid_control_transfer_lock();
                                            jQuery('#wpvivid_upload_backup_percent').show();
                                            jQuery('#wpvivid_upload_backup_percent').html(value.progress_html);
                                            migrate_task_need_update = true;
                                        }
                                        else if (value.status.str === 'wait_resume') {
                                            wpvivid_control_transfer_lock();
                                            jQuery('#wpvivid_upload_backup_percent').show();
                                            jQuery('#wpvivid_upload_backup_percent').html(value.progress_html);
                                            if (value.data.next_resume_time !== 'get next resume time failed.') {
                                                wpvivid_resume_transfer(index, value.data.next_resume_time);
                                            }
                                            else {
                                                wpvivid_delete_backup_task(index);
                                            }
                                        }
                                        else if (value.status.str === 'no_responds') {
                                            wpvivid_control_transfer_lock();
                                            jQuery('#wpvivid_upload_backup_percent').show();
                                            jQuery('#wpvivid_upload_backup_percent').html(value.progress_html);
                                            migrate_task_need_update = true;
                                        }
                                        else if (value.status.str === 'completed') {
                                            wpvivid_control_transfer_unlock();
                                            jQuery('#wpvivid_upload_backup_percent').html(value.progress_html);
                                            jQuery('#wpvivid_upload_backup_percent').hide();
                                            migrate_task_need_update = true;
                                            alert('Transfer succeeded. Please scan the backup list on the destination site to display the backup, then restore the backup.');
                                        }
                                        else if (value.status.str === 'error') {
                                            wpvivid_control_transfer_unlock();
                                            jQuery('#wpvivid_upload_backup_percent').html(value.progress_html);
                                            jQuery('#wpvivid_upload_backup_percent').hide();
                                            migrate_task_need_update = true;
                                        }
                                    });
                                }
                                wpvivid_transfer_cancel_flow();
                            }
                            else{
                                if(wpvivid_transfer_id != '') {
                                    jQuery('#wpvivid_transfer_cancel_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                                    wpvivid_control_transfer_unlock();
                                    jQuery('#wpvivid_upload_backup_percent').hide();
                                    wpvivid_transfer_id = '';
                                }
                            }
                        }
                        catch (err) {
                            alert(err);
                        }
                        if (!b_need_show) {
                            task_recheck_times++;
                            if (task_recheck_times < 5) {
                                migrate_task_need_update = true;
                            }
                        }

                    }, function (XMLHttpRequest, textStatus, errorThrown) {
                        migrate_task_need_update = true;
                        setTimeout(function () {
                            wpvivid_manage_upload_task();
                        }, 3000);
                    });
                }
            }

            function wpvivid_resume_transfer(backup_id, next_resume_time){
                if(next_resume_time < 0){
                    next_resume_time = 0;
                }
                next_resume_time = next_resume_time * 1000;
                setTimeout("wpvivid_activate_migrate_cron()", next_resume_time);
                setTimeout(function(){
                    task_recheck_times = 0;
                    migrate_task_need_update=true;
                }, next_resume_time);
            }

            function wpvivid_manage_upload_task()
            {
                if(migrate_task_need_update){
                    migrate_task_need_update=false;
                    wpvivid_check_upload_runningtask();
                }
                else {
                    setTimeout(function () {
                        wpvivid_manage_upload_task();
                    }, 3000);
                }
            }

            function wpvivid_activate_migrate_cron(){
                var next_get_time = 3 * 60 * 1000;
                jQuery.get(wpvivid_home_url+'/wp-cron.php');
                setTimeout("wpvivid_activate_migrate_cron()", next_get_time);
                setTimeout(function(){
                    migrate_task_need_update=true;
                }, 10000);
            }

            function switchmigrateTabs(evt,contentName,storage_page_id) {
                // Declare all variables
                var i, tabcontent, tablinks;

                // Get all elements with class="table-list-content" and hide them
                tabcontent = document.getElementsByClassName("migrate-tab-content");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }

                // Get all elements with class="table-nav-tab" and remove the class "nav-tab-active"
                tablinks = document.getElementsByClassName("migrate-nav-tab");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" nav-tab-active", "");
                }

                // Show the current tab, and add an "storage-menu-active" class to the button that opened the tab
                document.getElementById(contentName).style.display = "block";
                evt.currentTarget.className += " nav-tab-active";

                var top = jQuery('#'+storage_page_id).offset().top-jQuery('#'+storage_page_id).height();
                jQuery('html, body').animate({scrollTop:top}, 'slow');
            }

            jQuery(document).ready(function ()
            {
                <?php
                $default_task_type = array();
                $default_task_type = apply_filters('wpvivid_get_task_type', $default_task_type);
                if(empty($default_task_type)){
                ?>
                wpvivid_activate_migrate_cron();
                wpvivid_manage_upload_task();
                <?php
                }
                ?>
            });
        </script>
        <?php
    }


    public function wpvivid_add_page_migrate(){
        $migrate_descript = '';
        $migrate_key = '';
        $migrate_part_type = '';
        $migrate_part_exec = '';
        $migrate_part_note = '';
        $migrate_part_tip = '';
        ?>
        <div id="migrate-page" class="wrap-tab-content wpvivid_tab_migrate" name="tab-migrate" style="display: none;">
            <div class="postbox wpvivid-element-space-bottom" style="padding: 10px;">
                <?php
                echo apply_filters('wpvivid_migrate_descript', $migrate_descript);
                echo apply_filters('wpvivid_put_transfer_key', $migrate_key);
                ?>
            </div>

            <div class="postbox wpvivid-element-space-bottom" id="wpvivid_upload_backup_percent" style="display: none;">
                <div class="action-progress-bar" id="wpvivid_upload_progress_bar">
                    <div class="action-progress-bar-percent" id="wpvivid_upload_progress_bar_percent" style="height:24px;width:0"></div>
                </div>
                <div style="margin-left:10px; float: left; width:100%;"><p id="wpvivid_upload_current_doing"></p></div>
                <div style="clear: both;"></div>
                <div>
                    <div id="wpvivid_transfer_cancel" class="backup-log-btn"><input class="button-primary" id="wpvivid_transfer_cancel_btn" type="submit" value="<?php esc_attr_e( 'Cancel', 'wpvivid-backuprestore' ); ?>"  /></div>
                </div>
            </div>

            <div style="padding: 0 0 10px 0;">

                <?php echo apply_filters('wpvivid_migrate_part_type', $migrate_part_type); ?>

                <?php echo apply_filters('wpvivid_migrate_part_note', $migrate_part_note); ?>

                <div style="padding: 0 0 10px 0;">
                    <?php echo apply_filters('wpvivid_migrate_part_exec', $migrate_part_exec); ?>
                </div>
                <div style="clear: both;"></div>
                <div style="padding: 10px 0 10px 0;">
                    <?php echo apply_filters('wpvivid_migrate_part_tip', $migrate_part_tip); ?>
                </div>
            </div>
        </div>
        <?php
        $js = '';
        apply_filters('wpvivid_load_migrate_js', $js);
        ?>
        <?php
    }

    public function wpvivid_add_page_key(){
        ?>
        <div id="key-page" class="wrap-tab-content wpvivid_tab_key" name="tab-key" style="display: none;">
            <div style="padding: 0 0 0 10px">
                <div style="padding: 0 0 10px 0">
                    <span><?php _e('In order to allow another site to send a backup to this site, please generate a key below. Once the key is generated, this site is ready to receive a backup from another site. Then, please copy and paste the key in sending site and save it.', 'wpvivid-backuprestore'); ?></span>
                </div>
                <strong><?php _e('The key will expire in ', 'wpvivid-backuprestore'); ?></strong>
                <select id="wpvivid_generate_url_expires" style="margin-bottom: 2px;">
                    <option value="2 hour">2 hours</option>
                    <option selected="selected" value="8 hour">8 hours</option>
                    <option value="24 hour">24 hours</option>
                    <!--<option value="Never">Never</option>-->
                </select>
                <p><?php _e('Tips: For security reason, please choose an appropriate expiration time for the key.', 'wpvivid-backuprestore'); ?></p>
                <div>
                    <input class="button-primary" id="wpvivid_generate_url" type="submit" value="<?php esc_attr_e( 'Generate', 'wpvivid-backuprestore' ); ?>" onclick="wpvivid_click_generate_url();" />
                </div>
                <div id="wpvivid_test_generate_url" style="padding-top: 10px;">
                    <textarea id="wpvivid_test_remote_site_url_text" style="width: 100%; height: 140px;"></textarea>
                </div>
            </div>
        </div>
        <script>
            jQuery("#wpvivid_test_remote_site_url_text").focus(function() {
                jQuery(this).select();
                jQuery(this).mouseup(function() {
                    jQuery(this).unbind("mouseup");
                    return false;
                });
            });
            function wpvivid_click_generate_url()
            {
                //
                var expires=jQuery('#wpvivid_generate_url_expires').val();
                var ajax_data = {
                    'action': 'wpvivid_generate_url',
                    'expires':expires
                };
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery('#wpvivid_test_remote_site_url_text').val(data);
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror('generating key', textStatus, errorThrown);
                    alert(error_message);
                });
            }
        </script>
        <?php
    }

    public function test_connect_site()
    {
        if(isset($_POST['url']))
        {
            global $wpvivid_plugin;
            $wpvivid_plugin->ajax_check_security();

            $url=strtok($_POST['url'],'?');

            if (filter_var($url, FILTER_VALIDATE_URL) === FALSE)
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']='The key is invalid.';
                echo json_encode($ret);
                die();
            }

            if($url==home_url())
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']='The key generated by this site cannot be added into this site.';
                echo json_encode($ret);
                die();
            }

            $query=parse_url ($_POST['url'],PHP_URL_QUERY);
            if($query===null)
            {
                $query=strtok('?');
            }
            parse_str($query,$query_arr);
            $token=$query_arr['token'];
            $expires=$query_arr['expires'];
            $domain=$query_arr['domain'];

            if ($expires != 0 && time() > $expires) {
                $ret['result'] = 'failed';
                $ret['error'] = 'The key has expired.';
                echo json_encode($ret);
                die();
            }

            $json['test_connect']=1;
            $json=json_encode($json);
            $crypt=new WPvivid_crypt(base64_decode($token));
            $data=$crypt->encrypt_message($json);
            if($data===false)
            {
                $ret['result'] = 'failed';
                $ret['error'] = 'Data encryption failed.';
                echo json_encode($ret);
                die();
            }
            $data=base64_encode($data);
            
            $args['body']=array('wpvivid_content'=>$data,'wpvivid_action'=>'send_to_site_connect');
            $args['timeout']=30;
            $response=wp_remote_post($url,$args);

            if ( is_wp_error( $response ) )
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']= $response->get_error_message();
            }
            else
            {
                if($response['response']['code']==200)
                {
                    $res=json_decode($response['body'],1);
                    if($res!=null)
                    {
                        if($res['result']==WPVIVID_SUCCESS)
                        {
                            $ret['result']=WPVIVID_SUCCESS;

                            $options=WPvivid_Setting::get_option('wpvivid_saved_api_token');

                            $options[$url]['token']=$token;
                            $options[$url]['url']=$url;
                            $options[$url]['expires']=$expires;
                            $options[$url]['domain']=$domain;

                            delete_option('wpvivid_saved_api_token');
                            WPvivid_Setting::update_option('wpvivid_saved_api_token',$options);

                            $html='';
                            $i=0;
                            foreach ($options as $key=>$site)
                            {
                                $check_status='';
                                if($key==$url)
                                {
                                    $check_status='checked';
                                }

                                if($site['expires']>time())
                                {
                                    $date=date("l, F d, Y H:i", $site['expires']);
                                }
                                else
                                {
                                    $date='Token has expired';
                                }

                                $i++;
                                $html = apply_filters('wpvivid_put_transfer_key', $html);
                            }
                            $ret['html']= $html;

                        }
                        else
                        {
                            $ret['result']=WPVIVID_FAILED;
                            $ret['error']= $res['error'];
                        }
                    }
                    else
                    {
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']= $response['body'];
                        //$ret['error']= 'failed to parse returned data. Unable to retrieve the correct authorization data via HTTP request.';
                    }
                }
                else
                {
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']= 'upload error '.$response['response']['code'].' '.$response['body'];
                    //$response['body']
                }
            }

            echo json_encode($ret);
        }
        die();
    }

    public function delete_transfer_key()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        $ret['result']=WPVIVID_SUCCESS;
        delete_option('wpvivid_saved_api_token');
        $html='';
        $html = apply_filters('wpvivid_put_transfer_key', $html);
        $ret['html']=$html;
        echo json_encode($ret);
        die();
    }

    public function send_backup_to_site()
    {
        try {
            global $wpvivid_plugin;
            $wpvivid_plugin->ajax_check_security();

            $options = WPvivid_Setting::get_option('wpvivid_saved_api_token');

            if (empty($options)) {
                $ret['result'] = 'failed';
                $ret['error'] = 'A key is required.';
                echo json_encode($ret);
                die();
            }

            $url = '';
            foreach ($options as $key => $value) {
                $url = $value['url'];
            }

            if ($url === '') {
                $ret['result'] = 'failed';
                $ret['error'] = 'The key is invalid.';
                echo json_encode($ret);
                die();
            }

            if ($options[$url]['expires'] != 0 && $options[$url]['expires'] < time()) {
                $ret['result'] = 'failed';
                $ret['error'] = 'The key has expired.';
                echo json_encode($ret);
                die();
            }

            $json['test_connect']=1;
            $json=json_encode($json);
            $crypt=new WPvivid_crypt(base64_decode($options[$url]['token']));
            $data=$crypt->encrypt_message($json);
            $data=base64_encode($data);
            $args['body']=array('wpvivid_content'=>$data,'wpvivid_action'=>'send_to_site_connect');
            $response=wp_remote_post($url,$args);

            if ( is_wp_error( $response ) )
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']= $response->get_error_message();
                echo json_encode($ret);
                die();
            }
            else
            {
                if($response['response']['code']==200) {
                    $res=json_decode($response['body'],1);
                    if($res!=null) {
                        if($res['result']==WPVIVID_SUCCESS) {
                        }
                        else {
                            $ret['result']=WPVIVID_FAILED;
                            $ret['error']= $res['error'];
                            echo json_encode($ret);
                            die();
                        }
                    }
                    else {
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']= 'failed to parse returned data, unable to establish connection with the target site.';
                        $ret['response']=$response;
                        echo json_encode($ret);
                        die();
                    }
                }
                else {
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']= 'upload error '.$response['response']['code'].' '.$response['body'];
                    echo json_encode($ret);
                    die();
                }
            }

            if (WPvivid_taskmanager::is_tasks_backup_running()) {
                $ret['result'] = 'failed';
                $ret['error'] = __('A task is already running. Please wait until the running task is complete, and try again.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }

            $remote_option['url'] = $options[$url]['url'];
            $remote_option['token'] = $options[$url]['token'];
            $remote_option['type'] = WPVIVID_REMOTE_SEND_TO_SITE;
            $remote_options['temp'] = $remote_option;

            $backup_options = stripslashes($_POST['backup_options']);
            $backup_options = json_decode($backup_options, true);
            $backup['backup_files'] = $backup_options['transfer_type'];
            $backup['local'] = 0;
            $backup['remote'] = 1;
            $backup['ismerge'] = 1;
            $backup['lock'] = 0;
            $backup['remote_options'] = $remote_options;

            $backup_task = new WPvivid_Backup_Task();
            $ret = $backup_task->new_backup_task($backup, 'Manual', 'transfer');

            $task_id = $ret['task_id'];

            global $wpvivid_plugin;
            $wpvivid_plugin->check_backup($task_id, $backup);
            echo json_encode($ret);
            die();
        }
        catch (Exception $e){
            $ret['result'] = 'failed';
            $ret['error'] = $e->getMessage();
            echo json_encode($ret);
            die();
        }
    }

    public function migrate_now()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        if (!isset($_POST['task_id'])||empty($_POST['task_id'])||!is_string($_POST['task_id']))
        {
            $ret['result']='failed';
            $ret['error']=__('Error occurred while parsing the request data. Please try to run backup again.', 'wpvivid-backuprestore');
            echo json_encode($ret);
            die();
        }
        $task_id=sanitize_key($_POST['task_id']);

        //flush buffer
        $wpvivid_plugin->flush($task_id);
        $wpvivid_plugin->backup($task_id);
        die();
    }

    function export_download_backup()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $schedule_options=WPvivid_Schedule::get_schedule();
        if(empty($schedule_options))
        {
            die();
        }
        $backup_options = stripslashes($_POST['backup_options']);
        $backup_options = json_decode($backup_options, true);
        $backup['backup_files']= $backup_options['transfer_type'];
        $backup['local']=1;
        $backup['remote']=0;
        $backup['ismerge']=1;
        $backup['lock']=0;
        //$backup['remote_options']='';

        $backup_task=new WPvivid_Backup_Task();
        $task=$backup_task->new_backup_task($backup,'Manual', 'export');

        $task_id=$task['task_id'];
        //add_action('wpvivid_handle_upload_succeed',array($this,'wpvivid_deal_upload_succeed'),11);
        $wpvivid_plugin->check_backup($task_id,$backup['backup_files']);
        $wpvivid_plugin->flush($task_id);
        $wpvivid_plugin->backup($task_id);
        //}
        die();
    }

    function wpvivid_handle_backup_failed($task)
    {
        global $wpvivid_plugin;
        if($task['action'] === 'transfer') {
            $backup_error_array = WPvivid_Setting::get_option('wpvivid_transfer_error_array');
            if (empty($backup_error_array)) {
                $backup_error_array = array();
            }
            if (!array_key_exists($task['id'], $backup_error_array['bu_error'])) {
                $backup_error_array['bu_error']['task_id'] = $task['id'];
                $backup_error_array['bu_error']['error_msg'] = $task['status']['error'];
                WPvivid_Setting::update_option('wpvivid_transfer_error_array', $backup_error_array);
            }
            $backup=new WPvivid_Backup($task['id']);
            $backup->clean_backup();
            $wpvivid_plugin->wpvivid_log->WriteLog('Upload failed. Delete task '.$task['id'], 'notice');
            WPvivid_Backuplist::delete_backup($task['id']);
        }
    }

    public function wpvivid_deal_upload_succeed($task)
    {
        global $wpvivid_plugin;
        if($task['action'] === 'transfer')
        {
            $backup_success_count = WPvivid_Setting::get_option('wpvivid_transfer_success_count');
            if (empty($backup_success_count))
            {
                $backup_success_count = 0;
            }
            $backup_success_count++;
            WPvivid_Setting::update_option('wpvivid_transfer_success_count', $backup_success_count);

            $wpvivid_plugin->wpvivid_log->WriteLog('Upload finished. Delete task '.$task['id'], 'notice');
            WPvivid_Backuplist::delete_backup($task['id']);
        }
    }

    public function generate_url()
    {
        include_once WPVIVID_PLUGIN_DIR . '/vendor/autoload.php';

        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $expires=time()+3600;

        if(isset($_POST['expires']))
        {
            if($_POST['expires']=='1 month')
            {
                $expires=time()+2592000;
            }
            else if($_POST['expires']=='1 day')
            {
                $expires=time()+86400;
            }
            else if($_POST['expires']=='2 hour')
            {
                $expires=time()+7200;
            }
            else if($_POST['expires']=='8 hour')
            {
                $expires=time()+28800;
            }
            else if($_POST['expires']=='24 hour')
            {
                $expires=time()+86400;
            }
            else if($_POST['expires']=='Never')
            {
                $expires=0;
            }
        }

        $key_size = 2048;
        $rsa = new Crypt_RSA();
        $keys = $rsa->createKey($key_size);
        $options['public_key']=base64_encode($keys['publickey']);
        $options['private_key']=base64_encode($keys['privatekey']);
        $options['expires']=$expires;
        $options['domain']=home_url();

        WPvivid_Setting::update_option('wpvivid_api_token',$options);

        $url= $options['domain'];
        $url=$url.'?domain='.$options['domain'].'&token='.$options['public_key'].'&expires='.$expires;
        echo $url;
        die();
    }

    public function wpvivid_put_transfer_key($html){
        $html='<div id="wpvivid_transfer_key">';
        $options=WPvivid_Setting::get_option('wpvivid_saved_api_token');
        if(empty($options)){
            $html .= '<div style="padding: 0 0 10px 0;"><strong>'.__('Please paste the key below.', 'wpvivid-backuprestore').'</strong><a href="#" style="margin-left: 5px; text-decoration: none;" onclick="wpvivid_click_how_to_get_key();">'.__('How to get a site key?', 'wpvivid-backuprestore').'</a></div>
            <div id="wpvivid_how_to_get_key"></div>
            <div class="wpvivid-element-space-bottom"><textarea type="text" id="wpvivid_transfer_key_text" onKeyUp="wpvivid_check_key(this.value)" style="width: 100%; height: 140px;"/></textarea></div>
            <div><input class="button-primary" id="wpvivid_save_url_button" type="submit" value="'.esc_attr( 'Save', 'wpvivid-backuprestore' ).'" onclick="wpvivid_click_save_site_url();" /></div>';
        }
        else{
            foreach ($options as $key => $value)
            {
                $token = $value['token'];
                $source_dir=home_url();
                $target_dir=$value['domain'];
                $expires=$value['expires'];

                if ($expires != 0 && time() > $expires) {
                    $key_status='The key has expired. Please delete it first and generate a new one.';
                }
                else{
                    $time_diff = $expires - time();
                    $key_status = 'The key will expire in: '.date("H:i:s",$time_diff).'. Once the key expires, you need to generate a new key.';
                }
            }
            $html .= '<div style="padding: 0 0 10px 0;">
                        <span>Key:</span>
                        <input type="text" id="wpvivid_send_remote_site_url_text" value="'.$token.'" readonly="readonly" />
                        <input class="button-primary" id="wpvivid_delete_key_button" type="submit" value="'.esc_attr( 'Delete', 'wpvivid-backuprestore' ).'" onclick="wpvivid_click_delete_transfer_key();" />
                       </div>
                       <div class="wpvivid-element-space-bottom">'.$key_status.'</div>
                       <div>The connection is ok. Now you can transfer the site <strong>'.$source_dir.'</strong> to the site <strong>'.$target_dir.'</strong></div>';
        }
        $html.='</div>
        <script>
         var source_site = \''.admin_url('admin-ajax.php').'\';
        function wpvivid_check_key(value){
                var pos = value.indexOf(\'?\');
                var site_url = value.substring(0, pos);
                if(site_url == source_site){
                    alert(\'The key generated by this site cannot be added into this site.\');
                    jQuery(\'#wpvivid_save_url_button\').prop(\'disabled\', true);
                }
                else{
                    jQuery("#wpvivid_save_url_button").prop(\'disabled\', false);
                }
            }

            function wpvivid_click_save_site_url()
            {
                var url= jQuery(\'#wpvivid_transfer_key_text\').val();
                var ajax_data = {
                    \'action\': \'wpvivid_test_connect_site\',
                    \'url\':url
                };

                jQuery("#wpvivid_save_url_button").prop(\'disabled\', true);
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery("#wpvivid_save_url_button").prop(\'disabled\', false);
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if(jsonarray.result===\'success\')
                        {
                            jQuery(\'#wpvivid_transfer_key\').html(jsonarray.html);
                        }
                        else
                        {
                            alert(jsonarray.error);
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    jQuery("#wpvivid_save_url_button").prop(\'disabled\', false);
                    var error_message = wpvivid_output_ajaxerror(\'saving key\', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function wpvivid_click_delete_transfer_key()
            {
                var ajax_data = {
                    \'action\': \'wpvivid_delete_transfer_key\'
                };

                jQuery("#wpvivid_delete_key_button").css({\'pointer-events\': \'none\', \'opacity\': \'0.4\'});
                wpvivid_post_request(ajax_data, function (data)
                {
                    jQuery("#wpvivid_delete_key_button").css({\'pointer-events\': \'none\', \'opacity\': \'0.4\'});
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if(jsonarray.result===\'success\')
                        {
                            jQuery(\'#wpvivid_transfer_key\').html(jsonarray.html);
                        }
                    }
                    catch(err)
                    {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    jQuery("#wpvivid_delete_key_button").css({\'pointer-events\': \'auto\', \'opacity\': \'1\'});
                    var error_message = wpvivid_output_ajaxerror(\'deleting key\', textStatus, errorThrown);
                    alert(error_message);
                });
            }

            function click_dismiss_key_notice(obj){
                wpvivid_display_get_key = false;
                jQuery(obj).parent().remove();
            }

            function wpvivid_click_how_to_get_key(){
                if(!wpvivid_display_get_key) {
                    wpvivid_display_get_key = true;
                    var div = "<div class=\'notice notice-info is-dismissible inline\'>" +
                        "<p>" + wpvividlion.get_key_step1 + "</p>" +
                        "<p>" + wpvividlion.get_key_step2 + "</p>" +
                        "<p>" + wpvividlion.get_key_step3 + "</p>" +
                        "<button type=\'button\' class=\'notice-dismiss\' onclick=\'click_dismiss_key_notice(this);\'>" +
                        "<span class=\'screen-reader-text\'>Dismiss this notice.</span>" +
                        "</button>" +
                        "</div>";
                    jQuery(\'#wpvivid_how_to_get_key\').append(div);
                }
            }
        </script>';
        return $html;
    }

    public function wpvivid_migrate_descript($html){
        $html .= '<div style="padding: 0 0 10px 0;">
                    '.__('The feature can help you transfer a Wordpress site to a new domain(site). It would be a convenient way to migrate your WP site from dev environment to live server or from old server to the new.', 'wpvivid-backuprestore').'
                  </div>';
        return $html;
    }

    public function wpvivid_migrate_part_type($html){
        $migrate_type = '';
        $type_name = 'transfer_type';
        $html = '<div class="postbox quicktransfer">
                    <div class="wpvivid-element-space-bottom">
                        <h2 style="padding: 0;"><span>'.__( 'Choose the content you want to transfer', 'wpvivid-backuprestore').'</span></h2>
                    </div>
                    <div class="quickstart-archive-block">
                        <fieldset>
                            <legend class="screen-reader-text"><span>input type="radio"</span></legend>
                                '.apply_filters('wpvivid_add_migrate_type', $migrate_type, $type_name).'
                        </fieldset>
                    </div>
                </div>';
        return $html;
    }

    public function wpvivid_migrate_part_exec($html){
        $html = '';
        $html .= '<div id="wpvivid_transfer_btn" style="float: left;">
                        <input class="button-primary quicktransfer-btn" type="submit" value="'.esc_attr( 'Clone then Transfer', 'wpvivid-backuprestore').'" onclick="wpvivid_click_send_backup();" />
                    </div>
                    <script>
                    function wpvivid_click_send_backup()
            {
                //send_to_remote
                var option_data = wpvivid_ajax_data_transfer(\'migrate\');
                var ajax_data = {
                    \'action\': \'wpvivid_send_backup_to_site\',
                    \'backup_options\':option_data
                };
                migrate_task_need_update=true;
                wpvivid_clear_notice(\'wpvivid_backup_notice\');
                wpvivid_control_transfer_lock();
                wpvivid_post_request(ajax_data, function (data)
                {
                    try
                    {
                        var jsonarray = jQuery.parseJSON(data);
                        if(jsonarray.result===\'failed\')
                        {
                            wpvivid_delete_transfer_ready_task(jsonarray.error);
                        }
                        else{
                            wpvivid_transfer_id = jsonarray.task_id;
                            wpvivid_migrate_now(jsonarray.task_id);
                        }
                    }
                    catch(err)
                    {
                        wpvivid_delete_transfer_ready_task(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown)
                {
                    var error_message = wpvivid_output_ajaxerror(\'trying to establish communication with your server\', textStatus, errorThrown);
                    wpvivid_delete_transfer_ready_task(error_message);
                });
            }

            function wpvivid_migrate_now(task_id){
                var ajax_data = {
                    \'action\': \'wpvivid_migrate_now\',
                    \'task_id\': task_id
                };
                task_recheck_times = 0;
                migrate_task_need_update=true;
                wpvivid_post_request(ajax_data, function(data){
                }, function(XMLHttpRequest, textStatus, errorThrown) {
                });
            }

            function wpvivid_delete_transfer_ready_task(error){
                var ajax_data={
                    \'action\': \'wpvivid_delete_ready_task\'
                };
                wpvivid_post_request(ajax_data, function (data) {
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === \'success\') {
                            wpvivid_add_notice(\'Backup\', \'Error\', error);
                            wpvivid_control_transfer_unlock();
                            jQuery(\'#wpvivid_upload_backup_percent\').hide();
                        }
                    }
                    catch(err){
                        wpvivid_add_notice(\'Backup\', \'Error\', err);
                        wpvivid_control_transfer_unlock();
                        jQuery(\'#wpvivid_upload_backup_percent\').hide();
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    setTimeout(function () {
                        wpvivid_delete_transfer_ready_task(error);
                    }, 3000);
                });
            }
        </script>';
        return $html;
    }

    public function wpvivid_migrate_part_note($html){
        $html .= '<p>'.__('Note: ', 'wpvivid-backuprestore').'</p>
                <p>'.__('1. In order to successfully complete the migration, you\'d better deactivate <a href="https://wpvivid.com/best-redirect-plugins.html" target="_blank" style="text-decoration: none;">301 redirect plugin</a>, <a href="https://wpvivid.com/8-best-wordpress-firewall-plugins.html" target="_blank" style="text-decoration: none;">firewall and security plugin</a>, and <a href="https://wpvivid.com/best-free-wordpress-caching-plugins.html" target="_blank" style="text-decoration: none;">caching plugin</a> (if they exist) before transferring website.', 'wpvivid-backuprestore').'</p>
                <p>'.__('2. Please migrate website with the manual way when using <strong>Local by Flywheel</strong> environment.', 'wpvivid-backuprestore').'</p>';
        return $html;
    }

    public function wpvivid_migrate_part_tip($html){
        $backupdir=WPvivid_Setting::get_backupdir();
        $html .= '<p>'.__('<strong>Tips: </strong>Some web hosts may restrict the connection between the two sites, so you may get a 403 error or unstable connection issue when performing auto migration. In that case, it is recommended to manually transfer the site.', 'wpvivid-backuprestore').'</p>
                    <p><strong>'.__('How to migrate Wordpress site manually to a new domain(site) with WPvivid backup plugin?', 'wpvivid-backuprestore').'</strong></p>
                    <p>'.__('1. Download a backup in backups list to your computer.', 'wpvivid-backuprestore').'</p>
                    <p>'.__('2. Upload the backup to destination site. There are two ways available to use:', 'wpvivid-backuprestore').'</p>
                    <p style="margin-left: 20px;">'.__('2.1 Upload the backup to the upload section of WPvivid backup plugin in destination site.', 'wpvivid-backuprestore').'</p>
                    <p style="margin-left: 20px;">'.sprintf(__('2.2 Upload the backup with FTP client to backup directory %s in destination site, then click <strong>Scan uploaded backup or received backup</strong> button.', 'wpvivid-backuprestore'), WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$backupdir).'</p>
                    <p>'.__('3. Once done, the backup appears in backups list. Then, restore the backup.', 'wpvivid-backuprestore').'</p>';
        return $html;
    }

    public function wpvivid_add_migrate_type($html, $name_type){
        $html .= '<label>
                    <input type="radio" option="migrate" name="'.$name_type.'" value="files+db" checked />
                    <span>'.__( 'Database + Files (WordPress Files)', 'wpvivid-backuprestore' ).'</span>
                  </label><br>
                  <label>
                    <input type="radio" option="migrate" name="'.$name_type.'" value="files" />
                    <span>'.__( 'WordPress Files (Exclude Database)', 'wpvivid-backuprestore' ).'</span>
                  </label><br>
                  <label>
                    <input type="radio" option="migrate" name="'.$name_type.'" value="db" />
                    <span>'.__( 'Only Database', 'wpvivid-backuprestore' ).'</span>
                  </label><br>
                  <label>
                   <div style="float: left;">
                        <input type="radio" disabled />
                        <span class="wpvivid-element-space-right" style="color: #ddd;">'.__('Choose what to migrate', 'wpvivid-backuprestore').'</span>
                    </div>
                    <span class="wpvivid-feature-pro">
                        <a href="https://docs.wpvivid.com/custom-migration-overview.html" style="text-decoration: none;">'.__('Pro feature: learn more', 'wpvivid-backuprestore').'</a>
                    </span>
                  </label><br>';
        return $html;
    }

    public function list_tasks()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        $tasks=WPvivid_Setting::get_tasks();
        $ret=array();
        $list_tasks=array();
        foreach ($tasks as $task)
        {
            if($task['action']=='transfer')
            {
                $backup=new WPvivid_Backup_Task($task['id']);
                $list_tasks[$task['id']]=$backup->get_backup_task_info($task['id']);
                if($list_tasks[$task['id']]['task_info']['need_next_schedule']===true){
                    $timestamp = wp_next_scheduled(WPVIVID_TASK_MONITOR_EVENT,array($task['id']));

                    if($timestamp===false)
                    {
                        $wpvivid_plugin->add_monitor_event($task['id'],20);
                    }
                }
                if($list_tasks[$task['id']]['task_info']['need_update_last_task']===true){
                    $task_msg = WPvivid_taskmanager::get_task($task['id']);
                    $wpvivid_plugin->update_last_backup_task($task_msg);
                }
                //<div id="wpvivid_estimate_backup_info" style="float:left;">
                //                            <div class="backup-basic-info"><span class="wpvivid-element-space-right">' . __('Database Size:', 'wpvivid-backuprestore') . '</span><span>' . $list_tasks[$task['id']]['task_info']['db_size'] . '</span></div>
                //                            <div class="backup-basic-info"><span class="wpvivid-element-space-right">' . __('File Size:', 'wpvivid-backuprestore') . '</span><span>' . $list_tasks[$task['id']]['task_info']['file_size'] . '</span></div>
                //                         </div>
                $list_tasks[$task['id']]['progress_html'] = '<div class="action-progress-bar" id="wpvivid_upload_progress_bar">
                            <div class="action-progress-bar-percent" id="wpvivid_upload_progress_bar_percent" style="height:24px;width:' . $list_tasks[$task['id']]['task_info']['backup_percent'] . '"></div>
                         </div>
                         <div id="wpvivid_estimate_upload_info" style="float: left;"> 
                            <div class="backup-basic-info"><span class="wpvivid-element-space-right">' . __('Total Size:', 'wpvivid-backuprestore') . '</span><span>' . $list_tasks[$task['id']]['task_info']['total'] . '</span></div>
                            <div class="backup-basic-info"><span class="wpvivid-element-space-right">' . __('Uploaded:', 'wpvivid-backuprestore') . '</span><span>' . $list_tasks[$task['id']]['task_info']['upload'] . '</span></div>
                            <div class="backup-basic-info"><span class="wpvivid-element-space-right">' . __('Speed:', 'wpvivid-backuprestore') . '</span><span>' . $list_tasks[$task['id']]['task_info']['speed'] . '</span></div>
                         </div>
                         <div style="float: left;">
                            <div class="backup-basic-info"><span class="wpvivid-element-space-right">' . __('Network Connection:', 'wpvivid-backuprestore') . '</span><span>' . $list_tasks[$task['id']]['task_info']['network_connection'] . '</span></div>
                         </div>
                         <div style="clear:both;"></div>
                         <div style="margin-left:10px; float: left; width:100%;"><p id="wpvivid_upload_current_doing">' . $list_tasks[$task['id']]['task_info']['descript'] . '</p></div>
                         <div style="clear: both;"></div>
                         <div>
                            <div id="wpvivid_transfer_cancel" class="backup-log-btn"><input class="button-primary" id="wpvivid_transfer_cancel_btn" type="submit" value="'.esc_attr( 'Cancel', 'wpvivid-backuprestore' ).'"  /></div>
                         </div>';
            }
        }
        WPvivid_taskmanager::delete_marked_task();

        $backup_success_count=WPvivid_Setting::get_option('wpvivid_transfer_success_count');
        if(!empty($backup_success_count)){
            $notice_msg = __('Transfer succeeded. Please scan the backup list on the destination site to display the backup, then restore the backup.', 'wpvivid-backuprestore');
            $success_notice_html='<div class="notice notice-success is-dismissible inline"><p>'.$notice_msg.'</p>
                                    <button type="button" class="notice-dismiss" onclick="click_dismiss_notice(this);">
                                    <span class="screen-reader-text">Dismiss this notice.</span>
                                    </button>
                                    </div>';
            WPvivid_Setting::delete_option('wpvivid_transfer_success_count');
        }
        else {
            $success_notice_html = false;
        }
        $ret['transfer_succeed_notice'] = $success_notice_html;

        $backup_error_array=WPvivid_Setting::get_option('wpvivid_transfer_error_array');
        if(!empty($backup_error_array)){
            $error_notice_html = array();
            foreach ($backup_error_array as $key => $value){
                $notice_msg = 'Transfer failed, '.$value['error_msg'];
                $error_notice_html['bu_error']['task_id']=$value['task_id'];
                $error_notice_html['bu_error']['error_msg']='<div class="notice notice-error inline"><p>'.$notice_msg.'</p></div>';
            }
            WPvivid_Setting::delete_option('wpvivid_transfer_error_array');
        }
        else{
            $error_notice_html = false;
        }
        $ret['transfer_error_notice'] = $error_notice_html;

        $ret['transfer']['result']='success';
        $ret['transfer']['data']=$list_tasks;

        if(!empty($task_ids))
        {
            foreach ($task_ids as $id)
            {
                WPvivid_Setting::delete_task($id);
            }
        }

        echo json_encode($ret);
        die();
    }

    function wpvivid_rescan_backup_list(){
        ?>
        <div style="padding: 0 0 10px 0;">
            <?php
            Wpvivid_BackupUploader::rescan_local_folder();
            ?>
        </div>
        <?php
    }

    public function wpvivid_backuppage_load_backuplist($backuplist_array){
        $backuplist_array['list_upload'] = array('index' => '2', 'tab_func' => array($this, 'wpvivid_add_tab_upload'), 'page_func' => array($this, 'wpvivid_add_page_upload'));
        return $backuplist_array;
    }

    function wpvivid_add_tab_upload(){
        ?>
        <a href="#" id="wpvivid_tab_upload" class="nav-tab backup-nav-tab" onclick="switchrestoreTabs(event,'page-upload')"><?php _e('Upload', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    function wpvivid_add_page_upload(){
        $backupdir=WPvivid_Setting::get_backupdir();
        ?>
        <div class="backup-tab-content wpvivid_tab_upload" id="page-upload" style="display:none;">
            <div style="padding: 10px 0 10px 0;">
                <div style="padding-bottom: 10px;">
                    <span><?php echo sprintf(__('The backups will be uploaded to %s directory.', 'wpvivid-backuprestore'), WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$backupdir); ?></span>
                </div>
                <div style="padding-bottom: 10px;">
                    <span><?php echo __('Note: The files you want to upload must be a backup created by WPvivid backup plugin. Make sure that uploading every part of a backup to the directory if the backup is split into many parts', 'wpvivid-backuprestore'); ?></span>
                </div>
                <?php
                Wpvivid_BackupUploader::upload_meta_box();
                ?>
            </div>
        </div>
        <?php
    }
}