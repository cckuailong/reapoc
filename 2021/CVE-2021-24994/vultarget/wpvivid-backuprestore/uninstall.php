<?php

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

function wpvivid_clear_free_dir($directory){
    if(file_exists($directory)){
        if($dir_handle=@opendir($directory)){
            while($filename=readdir($dir_handle)){
                if($filename!='.' && $filename!='..'){
                    $subFile=$directory."/".$filename;
                    if(is_dir($subFile)){
                        wpvivid_clear_free_dir($subFile);
                    }
                    if(is_file($subFile)){
                        unlink($subFile);
                    }
                }
            }
            closedir($dir_handle);
            rmdir($directory);
        }
    }
}

$wpvivid_common_setting = get_option('wpvivid_common_setting', array());
if(!empty($wpvivid_common_setting)){
    if(isset($wpvivid_common_setting['uninstall_clear_folder']) && $wpvivid_common_setting['uninstall_clear_folder']){
        $wpvivid_local_setting = get_option('wpvivid_local_setting', array());
        if(isset($wpvivid_local_setting['path'])){
            if($wpvivid_local_setting['path'] !== 'wpvividbackups'){
                wpvivid_clear_free_dir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'wpvividbackups');
            }
            wpvivid_clear_free_dir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$wpvivid_local_setting['path']);
        }
        else{
            wpvivid_clear_free_dir(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'wpvividbackups');
        }
    }
}

delete_option('wpvivid_schedule_setting');
delete_option('wpvivid_email_setting');
delete_option('wpvivid_compress_setting');
delete_option('wpvivid_local_setting');
delete_option('wpvivid_upload_setting');
delete_option('wpvivid_common_setting');
delete_option('wpvivid_backup_list');
delete_option('wpvivid_task_list');
delete_option('wpvivid_init');
delete_option('wpvivid_remote_init');
delete_option('wpvivid_last_msg');
delete_option('wpvivid_download_cache');
delete_option('wpvivid_download_task');
delete_option('wpvivid_user_history');
delete_option('wpvivid_saved_api_token');
delete_option('wpvivid_import_list_cache');
delete_option('wpvivid_importer_task_list');
delete_option('wpvivid_list_cache');
delete_option('wpvivid_exporter_task_list');
delete_option('wpvivid_need_review');
delete_option('wpvivid_review_msg');
delete_option('wpvivid_migrate_status');
delete_option('clean_task');
delete_option('cron_backup_count');
delete_option('wpvivid_backup_success_count');
delete_option('wpvivid_backup_error_array');
delete_option('wpvivid_amazons3_notice');
delete_option('wpvivid_hide_mwp_tab_page_v1');
delete_option('wpvivid_hide_wp_cron_notice');
delete_option('wpvivid_transfer_error_array');
delete_option('wpvivid_transfer_success_count');
delete_option('wpvivid_api_token');
delete_option('wpvivid_download_task_v2');
delete_option('wpvivid_export_list');
delete_option('wpvivid_backup_report');

$options=get_option('wpvivid_staging_options',array());
$staging_keep_setting=isset($options['staging_keep_setting']) ? $options['staging_keep_setting'] : true;
if($staging_keep_setting)
{

}
else
{
    delete_option('wpvivid_staging_task_list');
    delete_option('wpvivid_staging_task_cancel');
    delete_option('wpvivid_staging_options');
    delete_option('wpvivid_staging_history');
    delete_option('wpvivid_staging_list');
}

define('WPVIVID_MAIN_SCHEDULE_EVENT','wpvivid_main_schedule_event');

if(wp_get_schedule(WPVIVID_MAIN_SCHEDULE_EVENT))
{
    wp_clear_scheduled_hook(WPVIVID_MAIN_SCHEDULE_EVENT);
    $timestamp = wp_next_scheduled(WPVIVID_MAIN_SCHEDULE_EVENT);
    wp_unschedule_event($timestamp,WPVIVID_MAIN_SCHEDULE_EVENT);
}
