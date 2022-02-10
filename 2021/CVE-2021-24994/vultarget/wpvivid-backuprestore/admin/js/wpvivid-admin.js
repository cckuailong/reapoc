var task_retry_times=0;
var running_backup_taskid='';
var tmp_current_click_backupid = '';
var m_need_update=true;
var m_restore_backup_id;
var m_backup_task_id;
var m_downloading_file_name = '';
var m_downloading_id = '';
var wpvivid_settings_changed = false;
var wpvivid_cur_log_page = 1;
var wpvivid_completed_backup = 1;
var wpvivid_prepare_backup=false;
var wpvivid_restoring=false;
var wpvivid_location_href=false;
var wpvivid_editing_storage_id = '';
var wpvivid_editing_storage_type = '';
var wpvivid_restore_download_array;
var wpvivid_restore_download_index = 0;
var wpvivid_get_download_restore_progress_retry = 0;
var wpvivid_restore_timeout = false;
var wpvivid_restore_need_download = false;
var wpvivid_display_restore_backup = false;
var wpvivid_restore_backup_type = '';
var wpvivid_display_restore_check = false;
var wpvivid_restore_sure = false;
var wpvivid_resotre_is_migrate=0;
(function ($) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
    $(document).ready(function () {
        //wpvivid_getrequest();

        wpvivid_interface_flow_control();

        $('input[option=review]').click(function(){
            var name = jQuery(this).prop('name');
            wpvivid_add_review_info(name);
        });

        $(document).on('click', '.notice-wp-cron .notice-dismiss', function(){
            var ajax_data = {
                'action': 'wpvivid_hide_wp_cron_notice'
            };
            wpvivid_post_request(ajax_data, function(res){
            }, function(XMLHttpRequest, textStatus, errorThrown) {
            });
        });
    });
    
})(jQuery);

function wpvivid_popup_tour(style) {
    var popup = document.getElementById("wpvivid_popup_tour");
    if (popup != null) {
        popup.classList.add(style);
    }
}

window.onbeforeunload = function(e) {
    if (wpvivid_settings_changed) {
        if (wpvivid_location_href){
            wpvivid_location_href = false;
        }
        else {
            return 'You are leaving the page without saving your changes, any unsaved changes on the page will be lost, are you sure you want to continue?';
        }
    }
}

/**
 * Refresh the scheduled task list as regularly as a preset interval(3-minute), to retrieve and activate the scheduled cron jobs.
 */
function wpvivid_activate_cron(){
    var next_get_time = 3 * 60 * 1000;
    wpvivid_cron_task();
    setTimeout("wpvivid_activate_cron()", next_get_time);
    setTimeout(function(){
        m_need_update=true;
    }, 10000);
}

/**
 * Send an Ajax request
 *
 * @param ajax_data         - Data in Ajax request
 * @param callback          - A callback function when the request is succeeded
 * @param error_callback    - A callback function when the request is failed
 * @param time_out          - The timeout for Ajax request
 */
function wpvivid_post_request(ajax_data, callback, error_callback, time_out){
    if(typeof time_out === 'undefined')    time_out = 30000;
    ajax_data.nonce=wpvivid_ajax_object.ajax_nonce;
    jQuery.ajax({
        type: "post",
        url: wpvivid_ajax_object.ajax_url,
        data: ajax_data,
        success: function (data) {
            callback(data);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            error_callback(XMLHttpRequest, textStatus, errorThrown);
        },
        timeout: time_out
    });
}

/**
 * Check if there are running tasks (backup and download)
 */
function wpvivid_check_runningtask(){
    var ajax_data = {
        'action': 'wpvivid_list_tasks',
        'backup_id': tmp_current_click_backupid
    };
    if(wpvivid_restoring === false) {
        wpvivid_post_request(ajax_data, function (data) {
            setTimeout(function () {
                wpvivid_manage_task();
            }, 3000);
            try {
                var jsonarray = jQuery.parseJSON(data);
                if (jsonarray.success_notice_html != false) {
                    jQuery('#wpvivid_backup_notice').show();
                    jQuery('#wpvivid_backup_notice').append(jsonarray.success_notice_html);
                }
                if(jsonarray.error_notice_html != false){
                    jQuery('#wpvivid_backup_notice').show();
                    jQuery.each(jsonarray.error_notice_html, function (index, value) {
                        jQuery('#wpvivid_backup_notice').append(value.error_msg);
                    });
                }
                if(jsonarray.backuplist_html != false) {
                    jQuery('#wpvivid_backup_list').html('');
                    jQuery('#wpvivid_backup_list').append(jsonarray.backuplist_html);
                }
                var b_has_data = false;
                if (jsonarray.backup.data.length !== 0) {
                    b_has_data = true;
                    task_retry_times = 0;
                    if (jsonarray.backup.result === 'success') {
                        wpvivid_prepare_backup = false;
                        jQuery.each(jsonarray.backup.data, function (index, value) {
                            if (value.status.str === 'ready') {
                                jQuery('#wpvivid_postbox_backup_percent').html(value.progress_html);
                                m_need_update = true;
                            }
                            else if (value.status.str === 'running') {
                                running_backup_taskid = index;
                                wpvivid_control_backup_lock();
                                jQuery('#wpvivid_postbox_backup_percent').show();
                                jQuery('#wpvivid_postbox_backup_percent').html(value.progress_html);
                                m_need_update = true;
                            }
                            else if (value.status.str === 'wait_resume') {
                                running_backup_taskid = index;
                                wpvivid_control_backup_lock();
                                jQuery('#wpvivid_postbox_backup_percent').show();
                                jQuery('#wpvivid_postbox_backup_percent').html(value.progress_html);
                                if (value.data.next_resume_time !== 'get next resume time failed.') {
                                    wpvivid_resume_backup(index, value.data.next_resume_time);
                                }
                                else {
                                    wpvivid_delete_backup_task(index);
                                }
                            }
                            else if (value.status.str === 'no_responds') {
                                running_backup_taskid = index;
                                wpvivid_control_backup_lock();
                                jQuery('#wpvivid_postbox_backup_percent').show();
                                jQuery('#wpvivid_postbox_backup_percent').html(value.progress_html);
                                m_need_update = true;
                            }
                            else if (value.status.str === 'completed') {
                                jQuery('#wpvivid_postbox_backup_percent').html(value.progress_html);
                                wpvivid_control_backup_unlock();
                                jQuery('#wpvivid_postbox_backup_percent').hide();
                                jQuery('#wpvivid_last_backup_msg').html(jsonarray.last_msg_html);
                                jQuery('#wpvivid_loglist').html("");
                                jQuery('#wpvivid_loglist').append(jsonarray.log_html);
                                wpvivid_log_count = jsonarray.log_count;
                                wpvivid_display_log_page();
                                running_backup_taskid = '';
                                m_backup_task_id = '';
                                m_need_update = true;
                            }
                            else if (value.status.str === 'error') {
                                jQuery('#wpvivid_postbox_backup_percent').html(value.progress_html);
                                wpvivid_control_backup_unlock();
                                jQuery('#wpvivid_postbox_backup_percent').hide();
                                jQuery('#wpvivid_last_backup_msg').html(jsonarray.last_msg_html);
                                jQuery('#wpvivid_loglist').html("");
                                jQuery('#wpvivid_loglist').append(jsonarray.log_html);
                                running_backup_taskid = '';
                                m_backup_task_id = '';
                                m_need_update = true;
                            }
                        });
                    }
                }
                else
                {
                    if(running_backup_taskid !== '')
                    {
                        jQuery('#wpvivid_backup_cancel_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                        jQuery('#wpvivid_backup_log_btn').css({'pointer-events': 'auto', 'opacity': '1'});
                        wpvivid_control_backup_unlock();
                        jQuery('#wpvivid_postbox_backup_percent').hide();
                        wpvivid_retrieve_backup_list();
                        wpvivid_retrieve_last_backup_message();
                        wpvivid_retrieve_log_list();
                        running_backup_taskid='';
                    }
                }
                /*if (jsonarray.download.length !== 0) {
                    if(jsonarray.download.result === 'success') {
                        b_has_data = true;
                        task_retry_times = 0;
                        var i = 0;
                        var file_name = '';
                        jQuery('#wpvivid_file_part_' + tmp_current_click_backupid).html("");
                        var b_download_finish = false;
                        jQuery.each(jsonarray.download.files, function (index, value) {
                            i++;
                            file_name = index;
                            var progress = '0%';
                            if (value.status === 'need_download') {
                                if (m_downloading_file_name === file_name) {
                                    m_need_update = true;
                                }
                                jQuery('#wpvivid_file_part_' + tmp_current_click_backupid).append(value.html);
                                //b_download_finish=true;
                            }
                            else if (value.status === 'running') {
                                if (m_downloading_file_name === file_name) {
                                    wpvivid_lock_download(tmp_current_click_backupid);
                                }
                                m_need_update = true;
                                jQuery('#wpvivid_file_part_' + tmp_current_click_backupid).append(value.html);
                                b_download_finish = false;
                            }
                            else if (value.status === 'completed') {
                                if (m_downloading_file_name === file_name) {
                                    wpvivid_unlock_download(tmp_current_click_backupid);
                                    m_downloading_id = '';
                                    m_downloading_file_name = '';
                                }
                                jQuery('#wpvivid_file_part_' + tmp_current_click_backupid).append(value.html);
                                b_download_finish = true;
                            }
                            else if (value.status === 'error') {
                                if (m_downloading_file_name === file_name) {
                                    wpvivid_unlock_download(tmp_current_click_backupid);
                                    m_downloading_id = '';
                                    m_downloading_file_name = '';
                                }
                                alert(value.error);
                                jQuery('#wpvivid_file_part_' + tmp_current_click_backupid).append(value.html);
                                b_download_finish = true;
                            }
                            else if (value.status === 'timeout') {
                                if (m_downloading_file_name === file_name) {
                                    wpvivid_unlock_download(tmp_current_click_backupid);
                                    m_downloading_id = '';
                                    m_downloading_file_name = '';
                                }
                                alert('Download timeout, please retry.');
                                jQuery('#wpvivid_file_part_' + tmp_current_click_backupid).append(value.html);
                                b_download_finish = true;
                            }
                        });
                        jQuery('#wpvivid_file_part_' + tmp_current_click_backupid).append(jsonarray.download.place_html);
                        if (b_download_finish == true) {
                            tmp_current_click_backupid = '';
                        }
                    }
                    else{
                        b_has_data = true;
                        alert(jsonarray.download.error);
                    }
                }*/
                if (!b_has_data) {
                    task_retry_times++;
                    if (task_retry_times < 5) {
                        m_need_update = true;
                    }
                }
            }
            catch(err){
                alert(err);
            }
        }, function (XMLHttpRequest, textStatus, errorThrown)
        {
            task_retry_times++;
            if (task_retry_times < 5)
            {
                setTimeout(function () {
                    m_need_update = true;
                    wpvivid_manage_task();
                }, 3000);
            }
        });
    }
}

/**
 * This function will show the log on a text box.
 *
 * @param data - The log message returned by server
 */
function wpvivid_show_log(data, content_id){
    jQuery('#'+content_id).html("");
    try {
        var jsonarray = jQuery.parseJSON(data);
        if (jsonarray.result === "success") {
            var log_data = jsonarray.data;
            while (log_data.indexOf('\n') >= 0) {
                var iLength = log_data.indexOf('\n');
                var log = log_data.substring(0, iLength);
                log_data = log_data.substring(iLength + 1);
                var insert_log = "<div style=\"clear:both;\">" + log + "</div>";
                jQuery('#'+content_id).append(insert_log);
            }
        }
        else if (jsonarray.result === "failed") {
            jQuery('#'+content_id).html(jsonarray.error);
        }
    }
    catch(err){
        alert(err);
        var div = "Reading the log failed. Please try again.";
        jQuery('#'+content_id).html(div);
    }
}

/**
 * Resume the backup task automatically in 1 minute in a timeout situation
 *
 * @param backup_id         - A unique ID for a backup
 * @param next_resume_time  - A time interval for resuming next timeout backup task
 */
function wpvivid_resume_backup(backup_id, next_resume_time){
    if(next_resume_time < 0){
        next_resume_time = 0;
    }
    next_resume_time = next_resume_time * 1000;
    setTimeout("wpvivid_cron_task()", next_resume_time);
    setTimeout(function(){
        task_retry_times = 0;
        m_need_update=true;
    }, next_resume_time);
}

/**
 * This function will retrieve the last backup message
 */
function wpvivid_retrieve_last_backup_message(){
    var ajax_data={
        'action': 'wpvivid_get_last_backup'
    };
    wpvivid_post_request(ajax_data, function(data){
        try {
            var jsonarray = jQuery.parseJSON(data);
            jQuery('#wpvivid_last_backup_msg').html(jsonarray.data);
        }
        catch(err){
            alert(err);
        }
    }, function(XMLHttpRequest, textStatus, errorThrown) {
        var error_message = wpvivid_output_ajaxerror('retrieving the last backup log', textStatus, errorThrown);
        jQuery('#wpvivid_last_backup_msg').html(error_message);
    });
}

/**
 * This function will control interface flow.
 */
function wpvivid_interface_flow_control(){
    jQuery('#wpvivid_general_email_enable').click(function(){
        if(jQuery('#wpvivid_general_email_enable').prop('checked') === true){
            jQuery('#wpvivid_general_email_setting').show();

        }
        else{
            jQuery('#wpvivid_general_email_setting').hide();
        }
    });

    jQuery("input[name='schedule-backup-files']").bind("click",function(){
        if(jQuery(this).val() === "custom"){
            jQuery('#wpvivid_choosed_folders').show();
            if(jQuery("input[name='wpvivid-schedule-custom-folders'][value='other']").prop('checked')){
                jQuery('#wpvivid_file_tree_browser').show();
            }
            else{
                jQuery('#wpvivid_file_tree_browser').hide();
            }
        }
        else{
            jQuery('#wpvivid_choosed_folders').hide();
            jQuery('#wpvivid_file_tree_browser').hide();
        }
    });

    jQuery("input[name='wpvivid-schedule-custom-folders']").bind("click",function(){
        if(jQuery("input[name='wpvivid-schedule-custom-folders'][value='other']").prop('checked')){
            jQuery('#wpvivid_file_tree_browser').show();
        }
        else{
            jQuery('#wpvivid_file_tree_browser').hide();
        }
    });

    jQuery('#settings-page input[type=checkbox]:not([option=junk-files])').on("change", function(){
        wpvivid_settings_changed = true;
    });

    jQuery('#settings-page input[type=radio]').on("change", function(){
        wpvivid_settings_changed = true;
    });

    jQuery('#settings-page input[type=text]').on("keyup", function(){
        wpvivid_settings_changed = true;
    });

    /*jQuery("#wpvivid_storage_account_block input:not([type=checkbox])").on("keyup", function(){
        wpvivid_settings_changed = true;
    });*/

    /*jQuery('#wpvivid_storage_account_block input[type=checkbox]').on("change", function(){
        wpvivid_settings_changed = true;
    });*/

    jQuery('input:radio[option=restore]').click(function() {
        jQuery('input:radio[option=restore]').each(function () {
            if (jQuery(this).prop('checked')) {
                jQuery('#wpvivid_restore_btn').css({'pointer-events': 'auto', 'opacity': '1'});
            }
        });
    });
}

/**
 * Manage backup and download tasks. Retrieve the data every 3 seconds for checking if the backup or download tasks exist or not.
 */
function wpvivid_manage_task() {
    if(m_need_update === true){
        m_need_update = false;
        wpvivid_check_runningtask();
    }
    else{
        setTimeout(function(){
            wpvivid_manage_task();
        }, 3000);
    }
}

function wpvivid_add_notice(notice_action, notice_type, notice_msg){
    var notice_id="";
    var tmp_notice_msg = "";
    if(notice_type === "Warning"){
        tmp_notice_msg = "Warning: " + notice_msg;
    }
    else if(notice_type === "Error"){
        tmp_notice_msg = "Error: " + notice_msg;
    }
    else if(notice_type === "Success"){
        tmp_notice_msg = "Success: " + notice_msg;
    }
    else if(notice_type === "Info"){
        tmp_notice_msg = notice_msg;
    }
    switch(notice_action){
        case "Backup":
            notice_id="wpvivid_backup_notice";
            break;
    }
    var bfind = false;
    $div = jQuery('#'+notice_id).children('div').children('p');
    $div.each(function (index, value) {
        if(notice_action === "Backup" && notice_type === "Success"){
            bfind = false;
            return false;
        }
        if (value.innerHTML === tmp_notice_msg) {
            bfind = true;
            return false;
        }
    });
    if (bfind === false) {
        jQuery('#'+notice_id).show();
        var div = '';
        if(notice_type === "Warning"){
            div = "<div class='notice notice-warning is-dismissible inline'><p>" + wpvividlion.warning + notice_msg + "</p>" +
                "<button type='button' class='notice-dismiss' onclick='click_dismiss_notice(this);'>" +
                "<span class='screen-reader-text'>Dismiss this notice.</span>" +
                "</button>" +
                "</div>";
        }
        else if(notice_type === "Error"){
            div = "<div class=\"notice notice-error inline\"><p>" + wpvividlion.error + notice_msg + "</p></div>";
        }
        else if(notice_type === "Success"){
            wpvivid_clear_notice('wpvivid_backup_notice');
            jQuery('#wpvivid_backup_notice').show();
            var success_msg = wpvivid_completed_backup + " backup tasks have been completed. Please switch to <a href=\"#\" onclick=\"wpvivid_click_switch_page('wrap', 'wpvivid_tab_log', true);\">Log</a> page to check the details.\n";
            div = "<div class='notice notice-success is-dismissible inline'><p>" + success_msg + "</p>" +
                "<button type='button' class='notice-dismiss' onclick='click_dismiss_notice(this);'>" +
                "<span class='screen-reader-text'>Dismiss this notice.</span>" +
                "</button>" +
                "</div>";
            wpvivid_completed_backup++;
        }
        else if(notice_type === "Info"){
            div = "<div class='notice notice-info is-dismissible inline'><p>" + notice_msg + "</p>" +
                "<button type='button' class='notice-dismiss' onclick='click_dismiss_notice(this);'>" +
                "<span class='screen-reader-text'>Dismiss this notice.</span>" +
                "</button>" +
                "</div>";
        }
        jQuery('#'+notice_id).append(div);
    }
}

function click_dismiss_notice(obj){
    wpvivid_completed_backup = 1;
    jQuery(obj).parent().remove();
}

function wpvivid_cron_task(){
    jQuery.get(wpvivid_siteurl+'/wp-cron.php');
}

function wpvivid_clear_notice(notice_id){
    var t = document.getElementById(notice_id);
    var oDiv = t.getElementsByTagName("div");
    var count = oDiv.length;
    for (count; count > 0; count--) {
        var i = count - 1;
        oDiv[i].parentNode.removeChild(oDiv[i]);
    }
    jQuery('#'+notice_id).hide();
}

function wpvivid_click_switch_page(tab, type, scroll)
{
    jQuery('.'+tab+'-tab-content:not(.' + type + ')').hide();
    jQuery('.'+tab+'-tab-content.' + type).show();
    jQuery('.'+tab+'-nav-tab:not(#' + type + ')').removeClass('nav-tab-active');
    jQuery('.'+tab+'-nav-tab#' + type).addClass('nav-tab-active');
    if(scroll == true){
        var top = jQuery('#'+type).offset().top-jQuery('#'+type).height();
        jQuery('html, body').animate({scrollTop:top}, 'slow');
    }
}

function wpvivid_close_tab(event, hide_tab, type, show_tab){
    event.stopPropagation();
    jQuery('#'+hide_tab).hide();
    if(hide_tab === 'wpvivid_tab_mainwp'){
        wpvivid_hide_mainwp_tab_page();
    }
    wpvivid_click_switch_page(type, show_tab, true);
}

function wpvivid_hide_mainwp_tab_page(){
    var ajax_data = {
        'action': 'wpvivid_hide_mainwp_tab_page'
    };
    wpvivid_post_request(ajax_data, function(res){
    }, function(XMLHttpRequest, textStatus, errorThrown) {
    });
}

/**
 * Output ajax error in a standard format.
 *
 * @param action        - The specific operation
 * @param textStatus    - The textual status message returned by the server
 * @param errorThrown   - The error message thrown by server
 *
 * @returns {string}
 */
function wpvivid_output_ajaxerror(action, textStatus, errorThrown){
    action = 'trying to establish communication with your server';
    var error_msg = "wpvivid_request: "+ textStatus + "(" + errorThrown + "): an error occurred when " + action + ". " +
        "This error may be request not reaching or server not responding. Please try again later.";
        //"This error could be caused by an unstable internet connection. Please try again later.";
    return error_msg;
}

function wpvivid_add_review_info(review){
    var ajax_data={
        'action': 'wpvivid_need_review',
        'review': review
    };
    jQuery('#wpvivid_notice_rate').hide();
    wpvivid_post_request(ajax_data, function(res){
        if(typeof res != 'undefined' && res != ''){
            var tempwindow=window.open('_blank');
            tempwindow.location=res;
        }
    }, function(XMLHttpRequest, textStatus, errorThrown) {
    });
}

function wpvivid_click_amazons3_notice(){
    var ajax_data={
        'action': 'wpvivid_amazons3_notice'
    };
    jQuery('#wpvivid_amazons3_notice').hide();
    wpvivid_post_request(ajax_data, function(res){
    }, function(XMLHttpRequest, textStatus, errorThrown) {
    });
}

function wpvivid_ajax_data_transfer(data_type){
    var json = {};
    jQuery('input:checkbox[option='+data_type+']').each(function() {
        var value = '0';
        var key = jQuery(this).prop('name');
        if(jQuery(this).prop('checked')) {
            value = '1';
        }
        else {
            value = '0';
        }
        json[key]=value;
    });
    jQuery('input:radio[option='+data_type+']').each(function() {
        if(jQuery(this).prop('checked'))
        {
            var key = jQuery(this).prop('name');
            var value = jQuery(this).prop('value');
            json[key]=value;
        }
    });
    jQuery('input:text[option='+data_type+']').each(function(){
        var obj = {};
        var key = jQuery(this).prop('name');
        var value = jQuery(this).val();
        json[key]=value;
    });
    jQuery('input:password[option='+data_type+']').each(function(){
        var obj = {};
        var key = jQuery(this).prop('name');
        var value = jQuery(this).val();
        json[key]=value;
    });
    jQuery('select[option='+data_type+']').each(function(){
        var obj = {};
        var key = jQuery(this).prop('name');
        var value = jQuery(this).val();
        json[key]=value;
    });
    return JSON.stringify(json);
}