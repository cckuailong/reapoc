<?php

function wpvivid_general_settings()
{
    $general_setting=WPvivid_Setting::get_setting(true, "");
    $display_backup_count = $general_setting['options']['wpvivid_common_setting']['max_backup_count'];
    $display_backup_count=intval($display_backup_count);
    if($display_backup_count > 7){
        $display_backup_count = 7;
    }
    if($general_setting['options']['wpvivid_common_setting']['estimate_backup']){
        $wpvivid_setting_estimate_backup='checked';
    }
    else{
        $wpvivid_setting_estimate_backup='';
    }
    /*if(!isset($general_setting['options']['wpvivid_common_setting']['show_tab_menu'])){
        $wpvivid_show_tab_menu='checked';
    }
    else {
        if ($general_setting['options']['wpvivid_common_setting']['show_tab_menu']) {
            $wpvivid_show_tab_menu = 'checked';
        } else {
            $wpvivid_show_tab_menu = '';
        }
    }*/
    if(!isset($general_setting['options']['wpvivid_common_setting']['show_admin_bar'])){
        $show_admin_bar = 'checked';
    }
    else{
        if($general_setting['options']['wpvivid_common_setting']['show_admin_bar']){
            $show_admin_bar = 'checked';
        }
        else{
            $show_admin_bar = '';
        }
    }
    if(!isset($general_setting['options']['wpvivid_common_setting']['domain_include'])){
        $wpvivid_domain_include = 'checked';
    }
    else{
        if($general_setting['options']['wpvivid_common_setting']['domain_include']){
            $wpvivid_domain_include = 'checked';
        }
        else{
            $wpvivid_domain_include = '';
        }
    }
    if(!isset($general_setting['options']['wpvivid_common_setting']['ismerge'])){
        $wpvivid_ismerge = 'checked';
    }
    else{
        if($general_setting['options']['wpvivid_common_setting']['ismerge'] == '1'){
            $wpvivid_ismerge = 'checked';
        }
        else{
            $wpvivid_ismerge = '';
        }
    }
    if(!isset($general_setting['options']['wpvivid_common_setting']['retain_local'])){
        $wpvivid_retain_local = '';
    }
    else{
        if($general_setting['options']['wpvivid_common_setting']['retain_local'] == '1'){
            $wpvivid_retain_local = 'checked';
        }
        else{
            $wpvivid_retain_local = '';
        }
    }

    if(!isset($general_setting['options']['wpvivid_common_setting']['uninstall_clear_folder'])){
        $uninstall_clear_folder = '';
    }
    else{
        if($general_setting['options']['wpvivid_common_setting']['uninstall_clear_folder'] == '1'){
            $uninstall_clear_folder = 'checked';
        }
        else{
            $uninstall_clear_folder = '';
        }
    }

    global $wpvivid_plugin;
    $out_of_date=$wpvivid_plugin->_get_out_of_date_info();
    ?>
    <div class="postbox schedule-tab-block">
        <div>
            <select option="setting" name="max_backup_count" id="wpvivid_max_backup_count">
                <?php
                for($i=1; $i<8;$i++){
                    if($i === $display_backup_count){
                        echo '<option selected="selected" value="' . $i . '">' . $i . '</option>';
                    }
                    else {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }
                }
                ?>
            </select><strong style="margin-right: 10px;"><?php _e('backups retained', 'wpvivid-backuprestore'); ?></strong><a href="https://docs.wpvivid.com/wpvivid-backup-pro-backup-retention.html" style="text-decoration: none;"><?php _e('Pro feature: Retain more backups', 'wpvivid-backuprestore'); ?></a>
        </div>
        <div>
            <label for="wpvivid_estimate_backup">
                <input type="checkbox" option="setting" name="estimate_backup" id="wpvivid_estimate_backup" value="1" <?php esc_attr_e($wpvivid_setting_estimate_backup); ?> />
                <span><?php _e('Calculate the size of files, folder and database before backing up', 'wpvivid-backuprestore' ); ?></span>
            </label>
        </div>
        <div>
            <label>
                <input type="checkbox" option="setting" name="show_admin_bar" <?php esc_attr_e($show_admin_bar); ?> />
                <span><?php _e('Show WPvivid backup plugin on top admin bar', 'wpvivid-backuprestore'); ?></span>
            </label>
        </div>
        <div>
            <label>
                <input type="checkbox" option="setting" name="ismerge" <?php esc_attr_e($wpvivid_ismerge); ?> />
                <span><?php _e('Merge all the backup files into single package when a backup completes. This will save great disk spaces, though takes longer time. We recommended you check the option especially on sites with insufficient server resources.', 'wpvivid-backuprestore'); ?></span>
            </label>
        </div>
        <div>
            <label>
                <input type="checkbox" option="setting" name="retain_local" <?php esc_attr_e($wpvivid_retain_local); ?> />
                <span><?php _e('Keep storing the backups in localhost after uploading to remote storage', 'wpvivid-backuprestore'); ?></span>
            </label>
        </div>
        <div>
            <label>
                <input type="checkbox" option="setting" name="uninstall_clear_folder" <?php esc_attr_e($uninstall_clear_folder); ?> />
                <span><?php _e(sprintf('Delete the /%s folder and all backups in it when deleting WPvivid Backup plugin.', $general_setting['options']['wpvivid_local_setting']['path']), 'wpvivid-backuprestore'); ?></span>
            </label>
        </div>
    </div>
    <div class="postbox schedule-tab-block">
        <div><strong><?php _e('Backup Folder', 'wpvivid-backuprestore'); ?></strong></div>
        <div class="setting-tab-block">
            <div><p><?php _e('Name your folder, this folder must be writable for creating backup files.', 'wpvivid-backuprestore' ); ?><p> </div>
            <input type="text" placeholder="wpvividbackups" option="setting" name="path" id="wpvivid_option_backup_dir" class="all-options" value="<?php esc_attr_e($general_setting['options']['wpvivid_local_setting']['path'], 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')" onpaste="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')" />
            <p><span class="wpvivid-element-space-right"><?php _e('Local storage directory:', 'wpvivid-backuprestore'); ?></span><span><?php echo WP_CONTENT_DIR.'/'; ?><span id="wpvivid_setting_local_storage_path"><?php _e($general_setting['options']['wpvivid_local_setting']['path'], 'wpvivid-backuprestore'); ?></span></span></p>
        </div>
        <div>
            <label>
                <input type="checkbox" option="setting" name="domain_include" <?php esc_attr_e($wpvivid_domain_include); ?> />
                <span><?php _e('Display domain(url) of current site in backup name. (e.g. domain_wpvivid-5ceb938b6dca9_2019-05-27-07-36_backup_all.zip)', 'wpvivid-backuprestore'); ?></span>
            </label>
        </div>
    </div>
    <div class="postbox schedule-tab-block">
        <div><strong><?php _e('Remove out-of-date backups', 'wpvivid-backuprestore'); ?></strong></div>
        <div class="setting-tab-block" style="padding-bottom: 0;">
            <fieldset>
                <label for="users_can_register">
                    <p><span class="wpvivid-element-space-right"><?php _e('Web Server Directory:', 'wpvivid-backuprestore'); ?></span><span id="wpvivid_out_of_date_local_path"><?php _e($out_of_date['web_server'], 'wpvivid-backuprestore'); ?></span></p>
                    <p><span style="margin-right: 2px;"><?php _e('Remote Storage Directory:', 'wpvivid-backuprestore'); ?></span><span id="wpvivid_out_of_date_remote_path">
                                    <?php
                                    $wpvivid_get_remote_directory = '';
                                    $wpvivid_get_remote_directory = apply_filters('wpvivid_get_remote_directory', $wpvivid_get_remote_directory);
                                    echo $wpvivid_get_remote_directory;
                                    ?>
                                </span>
                    </p>
                </label>
            </fieldset>
        </div>
        <div class="setting-tab-block" style="padding: 10px 10px 0 0;">
            <input class="button-primary" id="wpvivid_delete_out_of_backup" style="margin-right:10px;" type="submit" name="delete-out-of-backup" value="<?php esc_attr_e( 'Remove', 'wpvivid-backuprestore' ); ?>" />
            <p><?php _e('The action is irreversible! It will remove all backups are out-of-date (including local web server and remote storage) if they exist.', 'wpvivid-backuprestore'); ?> </p>
        </div>
    </div>
    <script>
        jQuery('#wpvivid_delete_out_of_backup').click(function(){
            wpvivid_delete_out_of_date_backups();
        });

        /**
         * This function will delete out of date backups.
         */
        function wpvivid_delete_out_of_date_backups(){
            var ajax_data={
                'action': 'wpvivid_clean_out_of_date_backup'
            };
            jQuery('#wpvivid_delete_out_of_backup').css({'pointer-events': 'none', 'opacity': '0.4'});
            wpvivid_post_request(ajax_data, function(data){
                jQuery('#wpvivid_delete_out_of_backup').css({'pointer-events': 'auto', 'opacity': '1'});
                try {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === "success") {
                        alert("Out of date backups have been removed.");
                        wpvivid_handle_backup_data(data);
                    }
                }
                catch(err){
                    alert(err);
                    jQuery('#wpvivid_delete_out_of_backup').css({'pointer-events': 'auto', 'opacity': '1'});
                }
            }, function(XMLHttpRequest, textStatus, errorThrown) {
                var error_message = wpvivid_output_ajaxerror('deleting out of date backups', textStatus, errorThrown);
                alert(error_message);
                jQuery('#wpvivid_delete_out_of_backup').css({'pointer-events': 'auto', 'opacity': '1'});
            });
        }
    </script>
    <?php
}

function wpvivid_email_report()
{
    $general_setting=WPvivid_Setting::get_setting(true, "");
    $setting_email_enable='';
    $setting_email_display = 'display: none;';
    if(isset($general_setting['options']['wpvivid_email_setting']['email_enable'])){
        if($general_setting['options']['wpvivid_email_setting']['email_enable']){
            $setting_email_enable='checked';
            $setting_email_display = '';
        }
    }
    $wpvivid_setting_email_always='';
    $wpvivid_setting_email_failed='';
    if(isset($general_setting['options']['wpvivid_email_setting']['always'])&&$general_setting['options']['wpvivid_email_setting']['always']) {
        $wpvivid_setting_email_always='checked';
    }
    else{
        $wpvivid_setting_email_failed='checked';
    }
    ?>
    <div class="postbox schedule-tab-block" id="wpvivid_email_report">
        <div><p><?php _e('In order to use this function, please install a <strong><a target="_blank" href="https://wpvivid.com/8-best-smtp-plugins-for-wordpress.html" style="text-decoration: none;">WordPress SMTP plugin</a></strong> of your preference and configure your SMTP server first. This is because WordPress uses the PHP Mail function to send its emails by default, which is not supported by many hosts and can cause issues if it is not set properly.', 'wpvivid-backuprestore'); ?></p>
        </div>
        <div>
            <label for="wpvivid_general_email_enable">
                <input type="checkbox" option="setting" name="email_enable" id="wpvivid_general_email_enable" value="1" <?php esc_attr_e($setting_email_enable, 'wpvivid-backuprestore'); ?> />
                <span><strong><?php _e( 'Enable email report', 'wpvivid-backuprestore' ); ?></strong></span>
            </label>
        </div>
        <div id="wpvivid_general_email_setting" style="<?php esc_attr_e($setting_email_display, 'wpvivid-backuprestore'); ?>" >
            <input type="text" placeholder="example@yourdomain.com" option="setting" name="send_to" class="regular-text" id="wpvivid_mail" value="<?php
            if(!empty($general_setting['options']['wpvivid_email_setting']['send_to'])) {
                foreach ($general_setting['options']['wpvivid_email_setting']['send_to'] as $mail) {
                    if(!empty($mail) && !is_array($mail)) {
                        _e($mail, 'wpvivid-backuprestore');
                        break;
                    }
                }
            }
            ?>" />
            <input class="button-secondary" id="wpvivid_send_email_test" style="margin-top:10px;" type="submit" name="" value="<?php esc_attr_e( 'Test Email', 'wpvivid-backuprestore' ); ?>" title="Send an email for testing mail function"/>
            <div id="wpvivid_send_email_res"></div>
            <fieldset class="setting-tab-block">
                <label >
                    <input type="radio" option="setting" name="always" value="1" <?php esc_attr_e($wpvivid_setting_email_always, 'wpvivid-backuprestore'); ?> />
                    <span><?php _e( 'Always send an email notification when a backup is complete', 'wpvivid-backuprestore' ); ?></span>
                </label><br>
                <label >
                    <input type="radio" option="setting" name="always" value="0" <?php esc_attr_e($wpvivid_setting_email_failed, 'wpvivid-backuprestore'); ?> />
                    <span><?php _e( 'Only send an email notification when a backup fails', 'wpvivid-backuprestore' ); ?></span>
                </label><br>
            </fieldset>
            <div style="margin-bottom: 10px;">
                <a href="https://wpvivid.com/wpvivid-backup-pro-email-report?utm_source=client_email_report&utm_medium=inner_link&utm_campaign=access" style="text-decoration: none;"><?php _e('Pro feature: Add another email address to get report', 'wpvivid-backuprestore'); ?></a>
            </div>
        </div>
    </div>
    <script>
        jQuery('#wpvivid_send_email_test').click(function(){
            wpvivid_email_test();
        });

        /**
         * After enabling email report feature, and test if an email address works or not
         */
        function wpvivid_email_test(){
            var mail = jQuery('#wpvivid_mail').val();
            var ajax_data = {
                'action': 'wpvivid_test_send_mail',
                'send_to': mail
            };
            wpvivid_post_request(ajax_data, function(data){
                try {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === 'success') {
                        jQuery('#wpvivid_send_email_res').html('Test succeeded.');
                    }
                    else {
                        jQuery('#wpvivid_send_email_res').html('Test failed, ' + jsonarray.error);
                    }
                }
                catch(err){
                    alert(err);
                }
            }, function(XMLHttpRequest, textStatus, errorThrown) {
                var error_message = wpvivid_output_ajaxerror('sending test mail', textStatus, errorThrown);
                alert(error_message);
            });
        }
    </script>
    <?php
}

function wpvivid_clean_junk()
{
    global $wpvivid_plugin;
    $junk_file=$wpvivid_plugin->_junk_files_info();
    ?>
    <div class="postbox schedule-tab-block" id="wpvivid_clean_junk">
        <div>
            <strong><?php _e('Web-server disk space in use by WPvivid', 'wpvivid-backuprestore'); ?></strong>
        </div>
        <div class="setting-tab-block">
            <div class="setting-tab-block">
                <span class="wpvivid-element-space-right"><?php _e('Total Size:', 'wpvivid-backuprestore'); ?></span>
                <span id="wpvivid_junk_sum_size"><?php _e($junk_file['sum_size'], 'wpvivid-backuprestore'); ?></span>
                <input class="button-secondary" id="wpvivid_calculate_size" style="margin-left:10px;" type="submit" name="Calculate-Sizes" value="<?php esc_attr_e( 'Calculate Sizes', 'wpvivid-backuprestore' ); ?>" />
            </div>
            <fieldset>
                <label for="wpvivid_junk_log">
                    <input type="checkbox" id="wpvivid_junk_log" option="junk-files" name="log" value="junk-log" />
                    <span class="wpvivid-element-space-right"><?php _e( 'logs', 'wpvivid-backuprestore' ); ?></span>
                    <span style="margin-right: 2px;"><?php _e('Path:', 'wpvivid-backuprestore' ); ?></span><span id="wpvivid_junk_log_path"><?php _e($junk_file['log_path'], 'wpvivid-backuprestore'); ?></span>
                </label>
            </fieldset>
            <fieldset>
                <label for="wpvivid_junk_backup_cache">
                    <input type="checkbox" id="wpvivid_junk_backup_cache" option="junk-files" name="backup_cache" value="junk-backup-cache" />
                    <span class="wpvivid-element-space-right"><?php _e( 'Backup Cache', 'wpvivid-backuprestore' ); ?></span>
                </label>
                <label for="wpvivid_junk_file">
                    <input type="checkbox" id="wpvivid_junk_file" option="junk-files" name="junk_files" value="junk-files" />
                    <span class="wpvivid-element-space-right"><?php _e( 'Junk', 'wpvivid-backuprestore' ); ?></span>
                    <span style="margin-right: 2px;"><?php _e('Path:', 'wpvivid-backuprestore' ); ?></span><span id="wpvivid_junk_file_path"><?php _e($junk_file['junk_path'], 'wpvivid-backuprestore'); ?></span>
                </label>
            </fieldset>
            <fieldset>
                <label for="wpvivid_junk_temporary_file">
                    <input type="checkbox" id="wpvivid_junk_temporary_file" option="junk-files" name="old_files" value="junk-temporary-files" />
                    <span class="wpvivid-element-space-right"><?php _e( 'Temporary Files', 'wpvivid-backuprestore' ); ?></span>
                    <span style="margin-right: 2px;"><?php _e('Path:', 'wpvivid-backuprestore'); ?></span><span id="wpvivid_restore_temp_file_path"><?php _e($junk_file['old_files_path'], 'wpvivid-backuprestore'); ?></span>
                    <p><?php echo __('Temporary Files are created by wpvivid when restoring a website.', 'wpvivid-backuprestore'); ?></p>
                </label>
            </fieldset>
        </div>
        <div><input class="button-primary" id="wpvivid_clean_junk_file" type="submit" name="Empty-all-files" value="<?php esc_attr_e( 'Empty', 'wpvivid-backuprestore' ); ?>" /></div>
        <div style="clear:both;"></div>
    </div>
    <script>
        jQuery('#wpvivid_calculate_size').click(function(){
            wpvivid_calculate_diskspaceused();
        });

        jQuery('#wpvivid_clean_junk_file').click(function(){
            wpvivid_clean_junk_files();
        });

        /**
         * Calculate the server disk space in use by WPvivid.
         */
        function wpvivid_calculate_diskspaceused(){
            var ajax_data={
                'action': 'wpvivid_junk_files_info'
            };
            var current_size = jQuery('#wpvivid_junk_sum_size').html();
            jQuery('#wpvivid_calculate_size').css({'pointer-events': 'none', 'opacity': '0.4'});
            jQuery('#wpvivid_clean_junk_file').css({'pointer-events': 'none', 'opacity': '0.4'});
            jQuery('#wpvivid_junk_sum_size').html("calculating...");
            wpvivid_post_request(ajax_data, function(data){
                jQuery('#wpvivid_calculate_size').css({'pointer-events': 'auto', 'opacity': '1'});
                jQuery('#wpvivid_clean_junk_file').css({'pointer-events': 'auto', 'opacity': '1'});
                try {
                    var jsonarray = jQuery.parseJSON(data);
                    if (jsonarray.result === "success") {
                        jQuery('#wpvivid_junk_sum_size').html(jsonarray.data.sum_size);
                        jQuery('#wpvivid_junk_log_path').html(jsonarray.data.log_path);
                        jQuery('#wpvivid_junk_file_path').html(jsonarray.data.junk_path);
                        jQuery('#wpvivid_restore_temp_file_path').html(jsonarray.data.old_files_path);
                    }
                }
                catch(err){
                    alert(err);
                    jQuery('#wpvivid_calculate_size').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_clean_junk_file').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_junk_sum_size').html(current_size);
                }
            }, function(XMLHttpRequest, textStatus, errorThrown) {
                var error_message = wpvivid_output_ajaxerror('calculating server disk space in use by WPvivid', textStatus, errorThrown);
                alert(error_message);
                jQuery('#wpvivid_calculate_size').css({'pointer-events': 'auto', 'opacity': '1'});
                jQuery('#wpvivid_clean_junk_file').css({'pointer-events': 'auto', 'opacity': '1'});
                jQuery('#wpvivid_junk_sum_size').html(current_size);
            });
        }

        /**
         * Clean junk files created during backups and restorations off your web server disk.
         */
        function wpvivid_clean_junk_files(){
            var descript = 'The selected item(s) will be permanently deleted. Are you sure you want to continue?';
            var ret = confirm(descript);
            if(ret === true){
                var option_data = wpvivid_ajax_data_transfer('junk-files');
                var ajax_data = {
                    'action': 'wpvivid_clean_local_storage',
                    'options': option_data
                };
                jQuery('#wpvivid_calculate_size').css({'pointer-events': 'none', 'opacity': '0.4'});
                jQuery('#wpvivid_clean_junk_file').css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function (data) {
                    jQuery('#wpvivid_calculate_size').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_clean_junk_file').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('input[option="junk-files"]').prop('checked', false);
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        alert(jsonarray.msg);
                        if (jsonarray.result === "success") {
                            jQuery('#wpvivid_junk_sum_size').html(jsonarray.data.sum_size);
                            jQuery('#wpvivid_junk_log_path').html(jsonarray.data.log_path);
                            jQuery('#wpvivid_junk_file_path').html(jsonarray.data.junk_path);
                            jQuery('#wpvivid_restore_temp_file_path').html(jsonarray.data.old_files_path);
                            jQuery('#wpvivid_loglist').html("");
                            jQuery('#wpvivid_loglist').append(jsonarray.html);
                            wpvivid_log_count = jsonarray.log_count;
                            wpvivid_display_log_page();
                        }
                    }
                    catch(err){
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    var error_message = wpvivid_output_ajaxerror('cleaning out junk files', textStatus, errorThrown);
                    alert(error_message);
                    jQuery('#wpvivid_calculate_size').css({'pointer-events': 'auto', 'opacity': '1'});
                    jQuery('#wpvivid_clean_junk_file').css({'pointer-events': 'auto', 'opacity': '1'});
                });
            }
        }
    </script>
    <?php
}

function wpvivid_export_import_settings()
{
    ?>
    <div class="postbox schedule-tab-block" id="wpvivid_export_import">
        <div class="setting-tab-block" style="padding-bottom: 0;">
            <input class="button-primary" id="wpvivid_setting_export" type="button" name="" value="<?php esc_attr_e( 'Export', 'wpvivid-backuprestore' ); ?>" />
            <p><?php echo __('Click \'Export\' button to save WPvivid settings on your local computer.', 'wpvivid-backuprestore'); ?> </p>
        </div>
        <div class="setting-tab-block" style="padding: 0 10px 0 0;">
            <input type="file" name="fileTrans" id="wpvivid_select_import_file"></br>
            <input class="button-primary" id="wpvivid_setting_import" type="button" name="" value="<?php esc_attr_e( 'Import', 'wpvivid-backuprestore' ); ?>" />
            <p><?php echo __('Importing the json file can help you set WPvivid\'s configuration on another wordpress site quickly.', 'wpvivid-backuprestore'); ?></p>
        </div>
        <div style="clear:both;"></div>
    </div>
    <script>
        jQuery('#wpvivid_setting_export').click(function(){
            wpvivid_export_settings();
        });

        jQuery('#wpvivid_setting_import').click(function(){
            wpvivid_import_settings();
        });

        function wpvivid_export_settings() {
            wpvivid_location_href=true;
            location.href =ajaxurl+'?_wpnonce='+wpvivid_ajax_object.ajax_nonce+'&action=wpvivid_export_setting&setting=1&history=1&review=0';
        }

        function wpvivid_import_settings(){
            var files = jQuery('input[name="fileTrans"]').prop('files');

            if(files.length == 0){
                alert('Choose a settings file and import it by clicking Import button.');
                return;
            }
            else{
                var reader = new FileReader();
                reader.readAsText(files[0], "UTF-8");
                reader.onload = function(evt){
                    var fileString = evt.target.result;
                    var ajax_data = {
                        'action': 'wpvivid_import_setting',
                        'data': fileString
                    };
                    wpvivid_post_request(ajax_data, function(data){
                        try {
                            var jsonarray = jQuery.parseJSON(data);
                            if (jsonarray.result === 'success') {
                                alert('The plugin settings is imported successfully.');
                                location.reload();
                            }
                            else {
                                alert('Error: ' + jsonarray.error);
                            }
                        }
                        catch(err){
                            alert(err);
                        }
                    }, function(XMLHttpRequest, textStatus, errorThrown) {
                        var error_message = wpvivid_output_ajaxerror('importing the previously-exported settings', textStatus, errorThrown);
                        jQuery('#wpvivid_display_log_content').html(error_message);
                    });
                }
            }
        }
    </script>
    <?php
}

function wpvivid_advanced_settings()
{
    $general_setting=WPvivid_Setting::get_setting(true, "");
    $wpvivid_setting_no_compress='';
    $wpvivid_setting_compress='';
    if($general_setting['options']['wpvivid_compress_setting']['no_compress']){
        $wpvivid_setting_no_compress='checked';
    }
    else{
        $wpvivid_setting_compress='checked';
    }

    if(!isset($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload'])){
        $subpackage_plugin_upload = '';
    }
    else{
        if($general_setting['options']['wpvivid_compress_setting']['subpackage_plugin_upload']){
            $subpackage_plugin_upload = 'checked';
        }
        else{
            $subpackage_plugin_upload = '';
        }
    }
    if(!isset($general_setting['options']['wpvivid_common_setting']['max_resume_count'])){
        $wpvivid_max_resume_count = WPVIVID_RESUME_RETRY_TIMES;
    }
    else{
        $wpvivid_max_resume_count = intval($general_setting['options']['wpvivid_common_setting']['max_resume_count']);
    }
    if(!isset($general_setting['options']['wpvivid_common_setting']['memory_limit'])){
        $general_setting['options']['wpvivid_common_setting']['memory_limit']=WPVIVID_MEMORY_LIMIT;
    }
    if(!isset($general_setting['options']['wpvivid_common_setting']['restore_memory_limit'])){
        $general_setting['options']['wpvivid_common_setting']['restore_memory_limit']=WPVIVID_RESTORE_MEMORY_LIMIT;
    }
    if(!isset($general_setting['options']['wpvivid_common_setting']['migrate_size'])){
        $general_setting['options']['wpvivid_common_setting']['migrate_size']=WPVIVID_MIGRATE_SIZE;
    }
    if(isset($general_setting['options']['wpvivid_common_setting']['db_connect_method'])){
        if($general_setting['options']['wpvivid_common_setting']['db_connect_method'] === 'wpdb'){
            $db_method_wpdb = 'checked';
            $db_method_pdo  = '';
        }
        else{
            $db_method_wpdb = '';
            $db_method_pdo  = 'checked';
        }
    }
    else{
        $db_method_wpdb = 'checked';
        $db_method_pdo  = '';
    }
    if(isset($general_setting['options']['wpvivid_common_setting']['restore_max_execution_time'])){
        $restore_max_execution_time = intval($general_setting['options']['wpvivid_common_setting']['restore_max_execution_time']);
    }
    else{
        $restore_max_execution_time = WPVIVID_RESTORE_MAX_EXECUTION_TIME;
    }
    ?>
    <div class="postbox schedule-tab-block setting-page-content">
        <div>
            <p><strong><?php _e('Enable the option when backup failed.', 'wpvivid-backuprestore'); ?></strong>&nbsp<?php _e('Special optimization for web hosting/shared hosting', 'wpvivid-backuprestore'); ?></p>
            <div>
                <label>
                    <input type="checkbox" option="setting" name="subpackage_plugin_upload" <?php esc_attr_e($subpackage_plugin_upload); ?> />
                    <span><strong><?php _e('Enable optimization mode for web hosting/shared hosting', 'wpvivid-backuprestore'); ?></strong></span>
                </label>
                <div>
                    <p><?php _e('Enabling this option can improve the backup success rate, but it will take more time for backup.', 'wpvivid-backuprestore'); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="postbox schedule-tab-block wpvivid-setting-addon" style="margin-bottom: 10px; padding-bottom: 0;">
        <div class="wpvivid-element-space-bottom">
            <strong><?php _e('Database access method.', 'wpvivid-backuprestore'); ?></strong>
        </div>
        <div class="wpvivid-element-space-bottom">
            <label>
                <input type="radio" option="setting" name="db_connect_method" value="wpdb" <?php esc_attr_e($db_method_wpdb); ?> />
                <span class="wpvivid-element-space-right"><strong>WPDB</strong></span><span><?php _e('WPDB option has a better compatibility, but the speed of backup and restore is slower.', 'wpvivid-backuprestore'); ?></span>
            </label>
        </div>
        <div class="wpvivid-element-space-bottom">
            <label>
                <input type="radio" option="setting" name="db_connect_method" value="pdo" <?php esc_attr_e($db_method_pdo); ?> />
                <span class="wpvivid-element-space-right"><strong>PDO</strong></span><span><?php _e('It is recommended to choose PDO option if pdo_mysql extension is installed on your server, which lets you backup and restore your site faster.', 'wpvivid-backuprestore'); ?></span>
            </label>
        </div>
    </div>
    <div class="postbox schedule-tab-block setting-page-content">
        <fieldset>
            <label>
                <input type="radio" option="setting" name="no_compress" value="1" <?php esc_attr_e($wpvivid_setting_no_compress, 'wpvivid-backuprestore'); ?> />
                <span class="wpvivid-element-space-right" title="<?php _e( 'It will cause a lower CPU Usage and is recommended in a web hosting/ shared hosting environment.', 'wpvivid-backuprestore' ); ?>"><?php _e( 'Only Archive without compressing', 'wpvivid-backuprestore' ); ?></span>
            </label>
            <label>
                <input type="radio" option="setting" name="no_compress" value="0" <?php esc_attr_e($wpvivid_setting_compress, 'wpvivid-backuprestore'); ?> />
                <span class="wpvivid-element-space-right" title="<?php _e( 'It will cause a higher CPU Usage and is recommended in a VPS/ dedicated hosting environment.', 'wpvivid-backuprestore' ); ?>"><?php _e( 'Compress and Archive', 'wpvivid-backuprestore' ); ?></span>
            </label>
            <label style="display: none;">
                <input type="radio" option="setting" name="compress_type" value="zip" checked />
                <input type="radio" option="setting" name="use_temp_file" value="1" checked />
                <input type="radio" option="setting" name="use_temp_size" value="16" checked />
            </label>
        </fieldset>
        <div style="padding-top: 10px;">
            <div><strong><?php _e('Compress Files Every', 'wpvivid-backuprestore'); ?></strong></div>
            <div class="setting-tab-block">
                <input type="text" placeholder="400" option="setting" name="max_file_size" id="wpvivid_max_zip" class="all-options" value="<?php esc_attr_e(str_replace('M', '', $general_setting['options']['wpvivid_compress_setting']['max_file_size']), 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/\D/g,'')" />MB
                <div><p><?php _e( 'Some web hosting providers limit large zip files (e.g. 200MB), and therefore splitting your backup into many parts is an ideal way to avoid hitting the limitation if you are running a big website.  Please try to adjust the value if you are encountering backup errors. If you use a value of 0 MB, any backup files won\'t be split.', 'wpvivid-backuprestore' ); ?></div></p>
            </div>
            <div><strong><?php _e('Exclude the files which are larger than', 'wpvivid-backuprestore'); ?></strong></div>
            <div class="setting-tab-block">
                <input type="text" placeholder="400" option="setting" name="exclude_file_size" id="wpvivid_ignore_large" class="all-options" value="<?php esc_attr_e($general_setting['options']['wpvivid_compress_setting']['exclude_file_size'], 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/\D/g,'')" />MB
                <div><p><?php _e( 'Using the option will ignore the file larger than the certain size in MB when backing up, \'0\' (zero) means unlimited.', 'wpvivid-backuprestore' ); ?></p></div>
            </div>
            <div><strong><?php _e('PHP script execution timeout for backup', 'wpvivid-backuprestore'); ?></strong></div>
            <div class="setting-tab-block">
                <input type="text" placeholder="600" option="setting" name="max_execution_time" id="wpvivid_option_timeout" class="all-options" value="<?php esc_attr_e($general_setting['options']['wpvivid_common_setting']['max_execution_time'], 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/\D/g,'')" />Seconds
                <div><p><?php _e( 'The time-out is not your server PHP time-out. With the execution time exhausted, our plugin will shut the process of backup down. If the progress of backup encounters a time-out, that means you have a medium or large sized website, please try to scale the value bigger.', 'wpvivid-backuprestore' ); ?></p></div>
            </div>
            <div><strong><?php _e('PHP script execution timeout for restore', 'wpvivid-backuprestore'); ?></strong></div>
            <div class="setting-tab-block">
                <input type="text" placeholder="1800" option="setting" name="restore_max_execution_time" class="all-options" value="<?php esc_attr_e($restore_max_execution_time); ?>" onkeyup="value=value.replace(/\D/g,'')" />Seconds
                <div><p><?php _e( 'The time-out is not your server PHP time-out. With the execution time exhausted, our plugin will shut the process of restore down. If the progress of restore encounters a time-out, that means you have a medium or large sized website, please try to scale the value bigger.', 'wpvivid-backuprestore' ); ?></p></div>
            </div>
            <div><strong><?php _e('PHP Memory Limit for backup', 'wpvivid-backuprestore'); ?></strong></div>
            <div class="setting-tab-block">
                <input type="text" placeholder="256" option="setting" name="memory_limit" class="all-options" value="<?php esc_attr_e(str_replace('M', '', $general_setting['options']['wpvivid_common_setting']['memory_limit']), 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/\D/g,'')" />MB
                <div><p><?php _e('Adjust this value to apply for a temporary PHP memory limit for WPvivid backup plugin to run a backup. We set this value to 256M by default. Increase the value if you encounter a memory exhausted error. Note: some web hosting providers may not support this.', 'wpvivid-backuprestore'); ?></p></div>
            </div>
            <div><strong><?php _e('PHP Memory Limit for restoration', 'wpvivid-backuprestore'); ?></strong></div>
            <div class="setting-tab-block">
                <input type="text" placeholder="256" option="setting" name="restore_memory_limit" class="all-options" value="<?php esc_attr_e(str_replace('M', '', $general_setting['options']['wpvivid_common_setting']['restore_memory_limit']), 'wpvivid-backuprestore'); ?>" onkeyup="value=value.replace(/\D/g,'')" />MB
                <div><p><?php _e('Adjust this value to apply for a temporary PHP memory limit for WPvivid backup plugin in restore process. We set this value to 256M by default. Increase the value if you encounter a memory exhausted error. Note: some web hosting providers may not support this.', 'wpvivid-backuprestore'); ?></p></div>
            </div>
            <div><strong><?php _e('Chunk Size', 'wpvivid-backuprestore'); ?></strong></div>
            <div class="setting-tab-block">
                <input type="text" placeholder="2" option="setting" name="migrate_size" class="all-options" value="<?php esc_attr_e($general_setting['options']['wpvivid_common_setting']['migrate_size']); ?>" onkeyup="value=value.replace(/\D/g,'')" />KB
                <div><p><?php _e('e.g.  if you choose a chunk size of 2MB, a 8MB file will use 4 chunks. Decreasing this value will break the ISP\'s transmission limit, for example:512KB', 'wpvivid-backuprestore'); ?></p></div>
            </div>
            <div>
                <?php
                $max_count_option = '';
                for($resume_count=3; $resume_count<10; $resume_count++){
                    if($resume_count === $wpvivid_max_resume_count){
                        $max_count_option .= '<option selected="selected" value="'.$resume_count.'">'.$resume_count.'</option>';
                    }
                    else{
                        $max_count_option .= '<option value="'.$resume_count.'">'.$resume_count.'</option>';
                    }
                }
                $max_count_select = '<select option="setting" name="max_resume_count">'.$max_count_option.'</select>';
                echo sprintf(__('<strong>Retrying </strong>%s<strong> times when encountering a time-out error</strong>', 'wpvivid-backuprestore'), $max_count_select);
                ?>
            </div>
        </div>
    </div>
    <?php
}

function wpvivid_add_setting_tab_page($setting_array){
    $setting_array['general_setting'] = array('index' => '1', 'tab_func' => 'wpvivid_settingpage_add_tab_general', 'page_func' => 'wpvivid_settingpage_add_page_general');
    $setting_array['advance_setting'] = array('index' => '2', 'tab_func' => 'wpvivid_settingpage_add_tab_advance', 'page_func' => 'wpvivid_settingpage_add_page_advance');
    return $setting_array;
}

function wpvivid_settingpage_add_tab_general(){
    ?>
    <a href="#" id="wpvivid_tab_general_setting" class="nav-tab setting-nav-tab nav-tab-active" onclick="switchsettingTabs(event,'page-general-setting')"><?php _e('General Settings', 'wpvivid-backuprestore'); ?></a>
    <?php
}

function wpvivid_settingpage_add_tab_advance(){
    ?>
    <a href="#" id="wpvivid_tab_advance_setting" class="nav-tab setting-nav-tab" onclick="switchsettingTabs(event,'page-advance-setting')"><?php _e('Advanced Settings', 'wpvivid-backuprestore'); ?></a>
    <?php
}

function wpvivid_settingpage_add_page_general(){
    ?>
    <div class="setting-tab-content wpvivid_tab_general_setting" id="page-general-setting" style="margin-top: 10px;">
        <?php do_action('wpvivid_setting_add_general_cell'); ?>
    </div>
    <?php
}

function wpvivid_settingpage_add_page_advance(){
    ?>
    <div class="setting-tab-content wpvivid_tab_advance_setting" id="page-advance-setting" style="margin-top: 10px; display: none;">
        <?php do_action('wpvivid_setting_add_advance_cell'); ?>
    </div>
    <?php
}

add_filter('wpvivid_add_setting_tab_page', 'wpvivid_add_setting_tab_page', 10);

add_action('wpvivid_setting_add_general_cell','wpvivid_general_settings',10);
add_action('wpvivid_setting_add_advance_cell','wpvivid_advanced_settings',13);
add_action('wpvivid_setting_add_general_cell','wpvivid_email_report',14);
add_action('wpvivid_setting_add_general_cell','wpvivid_clean_junk',15);
add_action('wpvivid_setting_add_general_cell','wpvivid_export_import_settings',16);
?>
