<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

if ( ! class_exists( 'WP_List_Table' ) )
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPvivid_Staging_Install_Wordpress_Free
{
    public $task;

    public $db_src_instance;
    public $db_des_instance;
    public $new_site_url;
    public $new_home_url;

    public function __construct($task_id)
    {
        $this->task=new WPvivid_Staging_Task($task_id);
    }

    public function do_install_wordpress()
    {
        global $wpvivid_plugin,$wpdb,$current_user;

        $this->db_src_instance=false;
        $this->db_des_instance=false;
        $this->new_site_url=$this->task->get_site_url(true);
        $this->new_home_url=$this->task->get_home_url(true);


        $wpvivid_plugin->staging->log->WriteLog('Start install new wordpress.','notice');

        if (!function_exists('wp_install'))
        {
            require ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        $username=$current_user->user_login;
        $email=$current_user->user_email;
        $title= get_option('blogname');
        $userpassword=$current_user->user_pass;
        $prefix=$this->task->get_db_prefix(true);
        $old_prefix=$this->task->get_db_prefix();
        $permalink_structure = $this->task->get_permalink_structure();
        $is_overwrite_permalink_structure = $this->task->get_is_overwrite_permalink_structure();

        $data['id']=$this->task->get_id();
        $data['name']=$this->task->get_path(true);
        $data['prefix']= $prefix;
        $admin_url = apply_filters('wpvividstg_get_admin_url', '');
        $admin_url .= 'admin.php?page='.apply_filters('wpvivid_white_label_slug', 'WPvivid');
        $data['parent_admin_url']=$admin_url;
        $data['live_site_url']=home_url();
        $data['live_site_staging_url']=apply_filters('wpvividstg_get_admin_url', '').'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'WPvivid_Staging');
        $data=serialize($data);

        $old_wpdb=$wpdb;

        $wpdb=$this->get_db_instance(true);

        $wpdb->set_prefix($prefix);
        $result = @wp_install($title, $username, $email, 0, '', md5(rand()));
        $wpvivid_plugin->staging->log->WriteLog(json_encode($result), 'notice');
        $user_id = $result['user_id'];

        $db_field = 'ID';

        if ( ! $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $wpdb->users WHERE $db_field = %s LIMIT 1",
                $user_id
            )
        ) )
        {
            $wpvivid_plugin->staging->log->WriteLog('User not create ,so we create it.', 'notice');
            $user_id       = wp_create_user( $username, md5(rand()), $email );
            update_user_option( $user_id, 'default_password_nag', true, true );
        }

        $query = $wpdb->prepare("UPDATE {$prefix}users SET user_pass = %s, user_activation_key = '' WHERE ID = %d LIMIT 1", array($userpassword, $user_id));
        $wpvivid_plugin->staging->log->WriteLog($query, 'notice');
        $wpdb->query($query);

        $update_query ="UPDATE {$prefix}options SET option_value = '{$this->new_site_url}' WHERE option_name = 'siteurl'";
        $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
        if ($wpdb->get_results($update_query)===false)
        {
            $error=$wpdb->last_error;
            $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
        }

        $update_query ="UPDATE {$prefix}options SET option_value = '{$this->new_home_url}' WHERE option_name = 'home'";
        $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
        if ($wpdb->get_results($update_query)===false)
        {
            $error=$wpdb->last_error;
            $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
        }

        $update_query ="UPDATE {$prefix}options SET option_name='{$prefix}user_roles' WHERE option_name='{$old_prefix}user_roles'";
        $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
        if ($wpdb->get_results($update_query)===false)
        {
            $error=$wpdb->last_error;
            $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
        }

        $update_query=$wpdb->prepare("INSERT INTO {$prefix}options (option_name,option_value) VALUES ('wpvivid_staging_finish',%d)", 1);
        $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
        if ($wpdb->get_results($update_query)===false)
        {
            $error=$wpdb->last_error;
            $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
        }

        if($is_overwrite_permalink_structure == 0)
        {
            $update_query = "INSERT INTO {$prefix}options (option_name,option_value) VALUES ('wpvivid_staging_init','{$permalink_structure}')";

            $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
            if ($wpdb->get_results($update_query) === false)
            {
                $error = $wpdb->last_error;
                $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
            }
        }

        $update_query = $wpdb->prepare("INSERT INTO {$prefix}options (option_name,option_value) VALUES ('wpvivid_staging_data',%s)", $data);
        $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
        if ($wpdb->get_results($update_query)===false)
        {
            $error=$wpdb->last_error;
            $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
        }

        $current   = array();
        $current[] = 'wpvivid-staging/wpvivid-staging.php';
        sort( $current );
        $value=serialize($current);
        $update_query = $wpdb->prepare("UPDATE {$prefix}options SET option_value=%s WHERE option_name='active_plugins'" , $value);
        $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
        if ($wpdb->get_results($update_query)===false)
        {
            $error=$wpdb->last_error;
            $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
        }

        $wpdb=$old_wpdb;
        $wpvivid_plugin->staging->log->WriteLog('prefix:'.$old_prefix,'notice');
        $wpdb->set_prefix($old_prefix);

        $des_path=$this->task->get_path();
        $path=$des_path.DIRECTORY_SEPARATOR.'wp-config.php';
        $data=file_get_contents($path);
        if( $data === false )
        {
            $wpvivid_plugin->staging->log->WriteLog('wp-config.php not found in '.$path,'notice');
        }
        else
        {
            preg_match( "/define\s*\(\s*['\"]MULTISITE['\"]\s*,\s*(.*)\s*\);/", $data, $matches );
            if( !empty( $matches[1] ) )
            {
                $wpvivid_plugin->staging->log->WriteLog('MULTISITE found in wp-config.php','notice');
                $pattern = "/define\s*\(\s*['\"]MULTISITE['\"]\s*,\s*(.*)\s*\);.*/";
                $replace = "define('MULTISITE',false); //";
                $data = preg_replace( array($pattern), $replace, $data );
                if( null === ($data) )
                {
                    $wpvivid_plugin->staging->log->WriteLog('MULTISITE not replace in wp-config.php','notice');
                }
            }
            file_put_contents($path,$data);
        }

        $wpvivid_plugin->staging->log->WriteLog('finished install new wordpress.','notice');
        $this->task->update_job_finished('create_new_wp');



        return true;
    }

    public function get_db_instance($des=false)
    {
        $db=$this->task->get_db_connect();
        if($des)
        {
            if( $this->db_des_instance===false)
            {
                if($db['des_use_additional_db']===false)
                {
                    global $wpdb;
                    $this->db_des_instance=$wpdb;
                    return $this->db_des_instance;
                }
                else
                {
                    $this->db_des_instance=new wpdb($db['des_dbuser'],$db['des_dbpassword'],$db['des_dbname'],$db['des_dbhost']);
                    return $this->db_des_instance;
                }
            }
            else
            {
                return $this->db_des_instance;
            }
        }
        else
        {
            if( $this->db_src_instance===false)
            {
                if($db['src_use_additional_db']===false)
                {
                    global $wpdb;
                    $this->db_src_instance=$wpdb;
                    return  $this->db_src_instance;
                }
                else
                {
                    $this->db_src_instance=new wpdb($db['src_dbuser'],$db['src_dbpassword'],$db['src_dbname'],$db['src_dbhost']);
                    return $this->db_src_instance;
                }
            }
            else
            {
                return $this->db_src_instance;
            }
        }
    }
}