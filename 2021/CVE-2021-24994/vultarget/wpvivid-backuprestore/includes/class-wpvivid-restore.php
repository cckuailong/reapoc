<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}

include_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-restore-database.php';
include_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-restore-site.php';
include_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-log.php';
include_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-zipclass.php';

class WPvivid_Restore
{
    public function restore()
    {
        $general_setting=WPvivid_Setting::get_setting(true, "");
        if(isset($general_setting['options']['wpvivid_common_setting']['restore_max_execution_time'])){
            $restore_max_execution_time = intval($general_setting['options']['wpvivid_common_setting']['restore_max_execution_time']);
        }
        else{
            $restore_max_execution_time = WPVIVID_RESTORE_MAX_EXECUTION_TIME;
        }
        @set_time_limit($restore_max_execution_time);

        global $wpvivid_plugin;

        $next_task=$wpvivid_plugin->restore_data->get_next_restore_task();

        if($next_task===false)
        {
            $wpvivid_plugin->restore_data->write_log('Restore task completed.','notice');
            $wpvivid_plugin->restore_data->update_status(WPVIVID_RESTORE_COMPLETED);
            return array('result'=>WPVIVID_SUCCESS);
        }
        else if($next_task===WPVIVID_RESTORE_RUNNING)
        {
            $wpvivid_plugin->restore_data->write_log('A restore task is already running.','error');
            return array('result'=>WPVIVID_FAILED,'error'=> 'A restore task is already running.');
        }
        else
        {
            $result = $this -> execute_restore($next_task);
            $wpvivid_plugin->restore_data->update_sub_task($next_task['index'],$result);

            if($result['result'] != WPVIVID_SUCCESS)
            {
                $wpvivid_plugin->restore_data->update_error($result['error']);
                $wpvivid_plugin->restore_data->write_log($result['error'],'error');
                return array('result'=>WPVIVID_FAILED,'error'=>$result['error']);
            }
            else {
                $wpvivid_plugin->restore_data->update_status(WPVIVID_RESTORE_WAIT);
                return array('result'=> WPVIVID_SUCCESS);
            }
        }
    }

    function execute_restore($restore_task)
    {
        global $wpvivid_plugin;

        $backup=$wpvivid_plugin->restore_data->get_backup_data();
        $backup_item=new WPvivid_Backup_Item($backup);
        $json=$backup_item->get_file_info($restore_task['files'][0]);
        $option=array();
        if($json!==false)
        {
            $option=$json;
        }
        $option=array_merge($option,$restore_task['option']);
        if(isset($restore_task['reset']))
        {
            $wpvivid_plugin->restore_data->write_log('Start resetting '.$restore_task['reset'],'notice');
            $ret= $this->reset_restore($restore_task);
            $wpvivid_plugin->restore_data->write_log('Finished resetting '.$restore_task['reset'],'notice');
            return $ret;
        }

        $is_type_db = false;
        $is_type_db = apply_filters('wpvivid_check_type_database', $is_type_db, $option);
        if($is_type_db)
        {
            $restore_site = new WPvivid_RestoreSite();
            $wpvivid_plugin->restore_data->write_log('Start restoring '.$restore_task['files'][0],'notice');
            $ret= $restore_site -> restore($option,$restore_task['files']);
            if($ret['result']==WPVIVID_SUCCESS)
            {
                if(isset($option['is_crypt'])&&$option['is_crypt']=='1')
                {
                    $sql_file = $backup_item->get_sql_file($restore_task['files'][0]);
                    $local_path= $wpvivid_plugin->get_backup_folder(); //$backup_item->get_local_path();
                    $ret=$this->restore_crypt_db($sql_file,$restore_task,$option,$local_path);
                    return $ret;
                }
                $path = $wpvivid_plugin->get_backup_folder().WPVIVID_DEFAULT_ROLLBACK_DIR.DIRECTORY_SEPARATOR.'wpvivid_old_database'.DIRECTORY_SEPARATOR;
                //$path = $backup_item->get_local_path().WPVIVID_DEFAULT_ROLLBACK_DIR.DIRECTORY_SEPARATOR.'wpvivid_old_database'.DIRECTORY_SEPARATOR;
                $sql_file = $backup_item->get_sql_file($restore_task['files'][0]);
                $wpvivid_plugin->restore_data->write_log('sql file: '.$sql_file,'notice');
                $restore_db=new WPvivid_RestoreDB();
                $check_is_remove = false;
                $check_is_remove = apply_filters('wpvivid_check_remove_restore_database', $check_is_remove, $option);
                if(!$check_is_remove)
                {
                    $ret = $restore_db->restore($path, $sql_file, $option);
                    $wpvivid_plugin->restore_data->write_log('Finished restoring '.$restore_task['files'][0],'notice');
                    $wpvivid_plugin->restore_data->update_need_unzip_file($restore_task['index'],$restore_task['files']);
                }
                else{
                    $wpvivid_plugin->restore_data->write_log('Remove file: '.$path.$sql_file, 'notice');
                    $wpvivid_plugin->restore_data->update_need_unzip_file($restore_task['index'],$restore_task['files']);
                    $ret['result']=WPVIVID_SUCCESS;
                }
                return $ret;
            }
            else{
                return $ret;
            }
        }
        else
        {
            $restore_site = new WPvivid_RestoreSite();

            $files=$wpvivid_plugin->restore_data->get_need_unzip_file($restore_task);
            $json=$backup_item->get_file_info($files[0]);
            $option=array();
            if($json!==false)
            {
                $option=$json;
            }
            $option=array_merge($option,$restore_task['option']);
            $wpvivid_plugin->restore_data->write_log('Start restoring '.$files[0],'notice');
            $ret= $restore_site -> restore($option,$files);
            $wpvivid_plugin->restore_data->update_need_unzip_file($restore_task['index'],$files);
            $wpvivid_plugin->restore_data->write_log('Finished restoring '.$files[0],'notice');
            return $ret;
        }
    }

    public function restore_crypt_db($file,$restore_task,$option,$local_path)
    {
        $general_setting=WPvivid_Setting::get_setting(true, "");
        if(isset($general_setting['options']['wpvivid_common_setting']['encrypt_db'])&&$general_setting['options']['wpvivid_common_setting']['encrypt_db'] == '1')
        {
            global $wpvivid_plugin;
            $wpvivid_plugin->restore_data->write_log('Encrypted database detected. Start decrypting database.','notice');

            $general_setting=WPvivid_Setting::get_setting(true, "");
            $password=$general_setting['options']['wpvivid_common_setting']['encrypt_db_password'];
            if(empty($password))
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']='Failed to decrypt backup. A password was not set in the plugin settings.';
                return $ret;
            }

            $crypt=new WPvivid_Crypt_File($password);
            $path=$local_path.WPVIVID_DEFAULT_ROLLBACK_DIR.DIRECTORY_SEPARATOR.'wpvivid_old_database';

            $ret=$crypt->decrypt($path.DIRECTORY_SEPARATOR.$file);
            if($ret['result']=='success')
            {
                $zip = new WPvivid_ZipClass();
                $all_files = array();
                $all_files[] = $ret['file_path'];
                $file_path=$ret['file_path'];

                $ret= $zip -> extract($all_files,$path);
                if($ret['result']!=='success')
                {
                    $ret['error']='Failed to unzip the file. Maybe the password is incorrect. Please check your password and try again.';
                    return $ret;
                }

                $wpvivid_plugin->restore_data->write_log('Decrypting database successfully. Start restoring database.','notice');

                $files = $zip->list_file($file_path);
                unset($zip);
                $sql_file=$files[0]['file_name'];

                $wpvivid_plugin->restore_data->write_log('sql file: '.$sql_file,'notice');
                $restore_db=new WPvivid_RestoreDB();
                $check_is_remove = false;
                $check_is_remove = apply_filters('wpvivid_check_remove_restore_database', $check_is_remove, $option);
                if(!$check_is_remove)
                {
                    $ret = $restore_db->restore($path.DIRECTORY_SEPARATOR, $sql_file, $option);
                    @unlink($file_path);
                    $wpvivid_plugin->restore_data->write_log('Finished restoring '.$restore_task['files'][0],'notice');
                    $wpvivid_plugin->restore_data->update_need_unzip_file($restore_task['index'],$restore_task['files']);
                }
                else{
                    @unlink($file_path);
                    $wpvivid_plugin->restore_data->write_log('Remove file: '.$path.$sql_file, 'notice');
                    $wpvivid_plugin->restore_data->update_need_unzip_file($restore_task['index'],$restore_task['files']);
                    $ret['result']=WPVIVID_SUCCESS;
                }
                return $ret;
            }
            else
            {
                return $ret;
            }
        }
        else
        {
            $ret['result']=WPVIVID_FAILED;
            $ret['error']='Failed to decrypt backup. A password was not set in the plugin settings.';
            return $ret;
        }
    }

    public function reset_restore($restore_task)
    {
        $ret['result']=WPVIVID_SUCCESS;

        if($restore_task['reset']=='themes')
        {
            return $this->delete_themes();
        }
        else if($restore_task['reset']=='deactivate_plugins')
        {
            return $this->deactivate_plugins();
        }
        else if($restore_task['reset']=='plugins')
        {
            return $this->delete_plugins();
        }
        else if($restore_task['reset']=='uploads')
        {
            return $this->delete_uploads();
        }
        else if($restore_task['reset']=='wp_content')
        {
            return $this->delete_wp_content();
        }
        else if($restore_task['reset']=='mu_plugins')
        {
            return $this->delete_mu_plugins();
        }
        else  if($restore_task['reset']=='tables')
        {
            return $this->delete_tables();
        }

        return $ret;
    }

    public function delete_themes()
    {
        global $wpvivid_plugin;

        if (!function_exists('delete_theme'))
        {
            require_once ABSPATH . 'wp-admin/includes/theme.php';
        }

        if (!function_exists('request_filesystem_credentials'))
        {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $all_themes = wp_get_themes(array('errors' => null));

        $wpvivid_plugin->restore_data->write_log('Deleting all themes','notice');

        foreach ($all_themes as $theme_slug => $theme_details)
        {
            delete_theme($theme_slug);
        }

        update_option('template', '');
        update_option('stylesheet', '');
        update_option('current_theme', '');

        $ret['result']=WPVIVID_SUCCESS;
        return $ret;
    }

    public function deactivate_plugins()
    {
        global $wpvivid_plugin;

        if (!function_exists('get_plugins'))
        {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if (!function_exists('request_filesystem_credentials'))
        {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $wpvivid_plugin->restore_data->write_log('Deactivating all plugins','notice');

        $active_plugins = (array) get_option('active_plugins', array());

        $wpvivid_backup_pro='wpvivid-backup-pro/wpvivid-backup-pro.php';
        $wpvivid_backup='wpvivid-backuprestore/wpvivid-backuprestore.php';
        $wpvivid_dashboard='wpvividdashboard/wpvividdashboard.php';

        if (($key = array_search($wpvivid_backup_pro, $active_plugins)) !== false)
        {
            unset($active_plugins[$key]);
        }

        if (($key = array_search($wpvivid_backup, $active_plugins)) !== false)
        {
            unset($active_plugins[$key]);
        }

        if (($key = array_search($wpvivid_dashboard, $active_plugins)) !== false)
        {
            unset($active_plugins[$key]);
        }

        if (!empty($active_plugins))
        {
            deactivate_plugins($active_plugins, true, false);
        }

        $ret['result']=WPVIVID_SUCCESS;
        return $ret;
    }

    public function delete_plugins()
    {
        global $wpvivid_plugin;

        if (!function_exists('get_plugins'))
        {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if (!function_exists('request_filesystem_credentials'))
        {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $wpvivid_backup_pro='wpvivid-backup-pro/wpvivid-backup-pro.php';
        $wpvivid_backup='wpvivid-backuprestore/wpvivid-backuprestore.php';
        $wpvivid_dashboard='wpvividdashboard/wpvividdashboard.php';

        $wpvivid_plugin->restore_data->write_log('Deleting all plugins','notice');

        $all_plugins = get_plugins();
        unset($all_plugins[$wpvivid_backup_pro]);
        unset($all_plugins[$wpvivid_backup]);
        unset($all_plugins[$wpvivid_dashboard]);

        if (!empty($all_plugins))
        {
            delete_plugins(array_keys($all_plugins));
        }

        $ret['result']=WPVIVID_SUCCESS;
        return $ret;
    }

    public function delete_uploads()
    {
        global $wpvivid_plugin;

        $upload_dir = wp_get_upload_dir();

        $wpvivid_plugin->restore_data->write_log('Deleting uploads','notice');

        $this->delete_folder($upload_dir['basedir'], $upload_dir['basedir']);

        $ret['result']=WPVIVID_SUCCESS;
        return $ret;
    }

    public function delete_folder($folder, $base_folder)
    {
        $files = array_diff(scandir($folder), array('.', '..'));

        foreach ($files as $file)
        {
            if (is_dir($folder . DIRECTORY_SEPARATOR . $file))
            {
                $this->delete_folder($folder . DIRECTORY_SEPARATOR . $file, $base_folder);
            } else {
                @unlink($folder . DIRECTORY_SEPARATOR . $file);
            }
        } // foreach

        if ($folder != $base_folder)
        {
            $tmp = @rmdir($folder);
            return $tmp;
        } else {
            return true;
        }
    }

    public function delete_wp_content()
    {
        global $wpvivid_plugin;

        $wpvivid_plugin->restore_data->write_log('Deleting wp_content','notice');

        $wp_content_dir = trailingslashit(WP_CONTENT_DIR);

        $wpvivid_backup=WPvivid_Setting::get_backupdir();

        $whitelisted_folders = array('mu-plugins', 'plugins', 'themes', 'uploads',$wpvivid_backup);

        $dirs = glob($wp_content_dir . '*', GLOB_ONLYDIR);
        foreach ($dirs as $dir)
        {
            if (false == in_array(basename($dir), $whitelisted_folders))
            {
                $this->delete_folder($dir, $dir);
                @rmdir($dir);
            }
        }

        $ret['result']=WPVIVID_SUCCESS;
        return $ret;
    }

    public function delete_mu_plugins()
    {
        global $wpvivid_plugin;

        $wpvivid_plugin->restore_data->write_log('Deleting mu_plugins','notice');

        $ret['result']=WPVIVID_SUCCESS;

        $mu_plugins = get_mu_plugins();

        if(empty($mu_plugins))
        {
            return $ret;
        }

        $this->delete_folder(WPMU_PLUGIN_DIR, WPMU_PLUGIN_DIR);

        return $ret;
    }

    public function delete_tables()
    {
        global $wpvivid_plugin,$wpdb;

        $wpvivid_plugin->restore_data->write_log('Deleting tables','notice');

        $tables = $this->get_tables();

        foreach ($tables as $table_name)
        {
            $wpdb->query('SET foreign_key_checks = 0');
            $wpdb->query('DROP TABLE IF EXISTS ' . $table_name);
            $wpvivid_plugin->restore_data->write_log('DROP TABLE:'.$table_name,'notice');
        }

        $ret['result']=WPVIVID_SUCCESS;
        return $ret;
    }

    public function get_tables()
    {
        global $wpdb;
        $tables = array();
        $core_tables=array();
        $core_tables[]='commentmeta';
        $core_tables[]='comments';
        $core_tables[]='links';
        $core_tables[]='options';
        $core_tables[]='postmeta';
        $core_tables[]='posts';
        $core_tables[]='term_relationships';
        $core_tables[]='term_taxonomy';
        $core_tables[]='termmeta';
        $core_tables[]='terms';
        $core_tables[]='usermeta';
        $core_tables[]='users';
        $core_tables[]='blogs';
        $core_tables[]='blogmeta';
        $core_tables[]='site';
        $core_tables[]='sitemeta';

        $sql=$wpdb->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($wpdb->base_prefix) . '%');

        $result = $wpdb->get_results($sql, OBJECT_K);
        if(!empty($result))
        {
            foreach ($result as $table_name=>$value)
            {
                if(in_array(substr($table_name, strlen($wpdb->base_prefix)),$core_tables))
                {
                    continue;
                }
                else
                {
                    $tables[]=$table_name;
                }
            }
        }
        return $tables;
    }
}