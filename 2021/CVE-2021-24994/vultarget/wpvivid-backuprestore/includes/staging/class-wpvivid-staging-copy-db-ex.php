<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}
class WPvivid_Staging_Copy_DB
{
    public $task;

    public $db_src_instance;
    public $db_des_instance;

    public $old_prefix;
    public $replace_prefix;
    public $new_prefix;

    public $old_site_url;
    public $old_home_url;

    public $new_site_url;
    public $new_home_url;

    public $replacing_table;

    public $placeholder;
    public $path_site;


    public function __construct($task_id)
    {
        $this->task=new WPvivid_Staging_Task($task_id);

        $this->db_src_instance=false;
        $this->db_des_instance=false;
        $this->placeholder=array();

        add_filter('wpvivid_restore_db_skip_replace_tables', array($this, 'skip_tables'),10,2);
        add_filter('wpvivid_restore_db_skip_replace_rows', array($this, 'skip_rows'),10,3);
    }

    public function skip_tables($skip_table,$table_name)
    {
        $skip_tables[]='adrotate_stats';
        $skip_tables[]='login_security_solution_fail';
        $skip_tables[]='icl_strings';
        $skip_tables[]='icl_string_positions';
        $skip_tables[]='icl_string_translations';
        $skip_tables[]='icl_languages_translations';
        $skip_tables[]='slim_stats';
        $skip_tables[]='slim_stats_archive';
        $skip_tables[]='es_online';
        $skip_tables[]='ahm_download_stats';
        $skip_tables[]='woocommerce_order_items';
        $skip_tables[]='woocommerce_sessions';
        $skip_tables[]='redirection_404';
        $skip_tables[]='redirection_logs';
        $skip_tables[]='wbz404_logs';
        $skip_tables[]='wbz404_redirects';
        $skip_tables[]='Counterize';
        $skip_tables[]='Counterize_UserAgents';
        $skip_tables[]='Counterize_Referers';
        $skip_tables[]='et_bloom_stats';
        $skip_tables[]='term_relationships';
        $skip_tables[]='lbakut_activity_log';
        $skip_tables[]='simple_feed_stats';
        $skip_tables[]='svisitor_stat';
        $skip_tables[]='itsec_log';
        $skip_tables[]='relevanssi_log';
        $skip_tables[]='wysija_email_user_stat';
        $skip_tables[]='wponlinebackup_generations';
        $skip_tables[]='blc_instances';
        $skip_tables[]='wp_rp_tags';
        $skip_tables[]='statpress';
        $skip_tables[]='wfHits';
        $skip_tables[]='wp_wfFileMods';
        $skip_tables[]='tts_trafficstats';
        $skip_tables[]='tts_referrer_stats';
        $skip_tables[]='dmsguestbook';
        $skip_tables[]='relevanssi';
        $skip_tables[]='wfFileMods';
        $skip_tables[]='learnpress_sessions';
        $skip_tables[]='icl_string_pages';
        $skip_tables[]='webarx_event_log';
        $skip_tables[]='duplicator_packages';
        $skip_tables[]='wsal_metadata';
        $skip_tables[]='wsal_occurrences';
        if(in_array(substr($table_name, strlen($this->new_prefix)),$skip_tables))
        {
            $skip_table=true;
        }
        else
        {
            $skip_table=false;
        }

        return $skip_table;
    }

    public function skip_rows($skip_rows,$table_name,$column_name)
    {
        $row['table_name']='posts';
        $row['column_name']='guid';
        $rows[]=$row;
        $row['table_name']='options';
        $row['column_name']='mainwp_child_subpages';
        $rows[]=$row;
        foreach ($rows as $row)
        {
            if($column_name==$row['column_name']&&$table_name==$this->new_prefix.$row['table_name'])
            {
                $skip_rows=true;
                break;
            }
        }

        return $skip_rows;
    }

    public function do_copy_db()
    {
        global $wpvivid_plugin;

        $this->db_src_instance=false;
        $this->db_des_instance=false;

        if($this->task->is_restore())
            $this->new_prefix=$this->task->get_temp_prefix();
        else
            $this->new_prefix=$this->task->get_db_prefix(true);
        $this->old_prefix=$this->task->get_db_prefix();

        if(!$this->init_copy_tables_list())
            return false;
        $table=$this->task->get_start('db');
        $this->task->update_calc_db_size('db');
        while($table&&!$table['finished'])
        {
            $ret=$this->copy_table($table,$this->task->get_db_insert_count());

            if($ret['result']=='failed')
            {
                $this->task->set_error($ret['error']);
                return false;
            }

            if(!$table['finished'])
            {
                $wpvivid_plugin->staging->log->WriteLog('Copying '.$this->task->get_db_insert_count().' queries is completed.','notice');
                $wpvivid_plugin->staging->log->WriteLog('The next copying table info: '.json_encode($table),'notice');
            }

            $this->task->update_table('db',$table);
        }

        if($this->task->get_start('db')===false)
        {
            $wpvivid_plugin->staging->log->WriteLog('Copying '.$table['name'].' is completed.','notice');
            $this->task->update_calc_db_finish_size('db');
            $wpvivid_plugin->staging->log->WriteLog('Database copying is completed.','notice');
            $this->task->update_job_finished('db');
        }
        else
        {
            $wpvivid_plugin->staging->log->WriteLog('Copying '.$table['name'].' is completed.','notice');
            $this->task->update_table_finished('db',$table);
            $this->task->update_calc_db_finish_size('db');
        }

        return true;
    }

    public function do_replace_db()
    {
        global $wpvivid_plugin;

        $this->replace_prefix=$this->task->get_temp_prefix();
        $this->new_prefix=$this->task->get_db_prefix(true);
        $this->old_prefix=$this->task->get_db_prefix();

        $this->old_site_url= $this->task->get_site_url();
        $this->old_home_url= $this->task->get_home_url();

        $this->new_site_url=$this->task->get_site_url(true);
        $this->new_home_url=$this->task->get_home_url(true);

        if(!$this->init_replace_tables_list())
            return false;

        $table=$this->task->get_start('db_replace');
        $this->task->update_calc_db_size('db_replace');
        while($table&&!$table['finished'])
        {
            $ret=$this->replace_table($table,$this->task->get_db_replace_count());
            if($ret['result']=='failed')
            {
                $this->task->set_error($ret['error']);
                return false;
            }

            if(!$table['finished'])
            {
                $wpvivid_plugin->staging->log->WriteLog($this->task->get_db_replace_count().' queries of '.$table['name'].' is replaced.','notice');
                $wpvivid_plugin->staging->log->WriteLog('The next replacing table info: '.json_encode($table),'notice');
            }

            $this->task->update_table('db_replace',$table);
        }

        $wpvivid_plugin->staging->log->WriteLog('Replacing '.$table['name'].' is completed.','notice');
        $this->task->update_table_finished('db_replace',$table);
        $this->task->update_calc_db_finish_size('db_replace');

        if($this->task->get_start('db_replace')===false)
        {
            if(!$this->task->is_restore())
            {
                $this->set_staging_site_data();
            }
            //$this->set_staging_site_data();
            $wpvivid_plugin->staging->log->WriteLog('Replacing database is completed.','notice');
            $this->task->update_job_finished('db_replace');
        }

        return true;
    }

    public function do_rename_db()
    {
        global $wpvivid_plugin;

        $this->replace_prefix=$this->task->get_temp_prefix();
        $this->new_prefix=$this->task->get_db_prefix(true);
        $this->old_prefix=$this->task->get_db_prefix();

        $this->old_site_url= $this->task->get_site_url();
        $this->old_home_url= $this->task->get_home_url();

        $this->new_site_url=$this->task->get_site_url(true);
        $this->new_home_url=$this->task->get_home_url(true);

        if(!$this->init_rename_tables_list())
            return false;

        $table=$this->task->get_start('db_rename');
        $this->task->update_calc_db_size('db_rename');
        while($table)
        {
            $ret= $this->rename_table($table);
            if($ret['result']=='failed')
            {
                $this->task->set_error($ret['error']);
                return false;
            }

            $wpvivid_plugin->staging->log->WriteLog('Rename '.$table['name'].' is completed.','notice');
            $this->task->update_table_finished('db_rename',$table);
            $table=$this->task->get_start('db_rename');
            $this->task->update_calc_db_finish_size('db_rename');
        }

        $this->set_staging_site_data();
        $wpvivid_plugin->staging->log->WriteLog('Rename tables is completed.','notice');
        $this->task->update_job_finished('db_rename');

        return true;
    }

    public function set_staging_site_data()
    {
        global $wpvivid_plugin;

        $db=$this->get_db_instance(true);

        $prefix=$this->new_prefix;

        $query=$db->prepare("UPDATE {$prefix}options SET option_value = %s WHERE option_name = 'siteurl' or option_name='home'",$this->new_site_url);

        if ($db->get_results($query)===false)
        {
            $error=$db->last_error;
            $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
        }

        $update_query=$db->prepare("UPDATE {$prefix}options SET option_value = %s WHERE option_name = 'rewrite_rules'", '');
        $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
        if ($db->get_results($update_query)===false)
        {
            $error=$db->last_error;
            $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
        }

        $update_query=$db->prepare("INSERT INTO {$prefix}options (option_name,option_value) VALUES ('wpvivid_staging_finish',%d)", 1);
        $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
        if ($db->get_results($update_query)===false)
        {
            $error=$db->last_error;
            $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
        }

        $is_overwrite_permalink_structure = $this->task->get_is_overwrite_permalink_structure();
        if($is_overwrite_permalink_structure == 0)
        {
            if(!$this->task->is_restore()){
                $update_query = $db->prepare("INSERT INTO {$prefix}options (option_name,option_value) VALUES ('wpvivid_staging_init',%d)", 1);
            }
            else{
                $permalink_structure = $this->task->get_permalink_structure();
                $update_query = $db->prepare("INSERT INTO {$prefix}options (option_name,option_value) VALUES ('wpvivid_staging_init',%s)", $permalink_structure);
            }
            $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
            if ($db->get_results($update_query) === false) {
                $error = $db->last_error;
                $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
            }
        }

        if($this->task->is_restore())
        {
            delete_option('wpvivid_staging_data');
            update_option('blog_public','1');

            $push_staging_history = $this->task->get_push_staging_history();
            update_option('wpvivid_push_staging_history', $push_staging_history);

            if($this->task->get_site_mu_single())
            {
                switch_to_blog( $this->task->get_site_mu_single_site_id());
                delete_option('wpvivid_staging_data');
                delete_option('wpvivid_staging_finish');
                delete_option('wpvivid_staging_init');
                restore_current_blog();
            }
        }
        else
        {
            $data['id']=$this->task->get_id();
            $data['name']=$this->task->get_path(true);
            $data['prefix']= $prefix;
            $admin_url = apply_filters('wpvividstg_get_admin_url', '');
            $admin_url .= 'admin.php?page='.apply_filters('wpvivid_white_label_slug', 'WPvivid');
            $data['parent_admin_url']=$admin_url;
            $data['live_site_url']=home_url();
            $data['live_site_staging_url']=apply_filters('wpvividstg_get_admin_url', '').'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'WPvivid_Staging');
            $data=serialize($data);
            $update_query = $db->prepare("INSERT INTO {$prefix}options (option_name,option_value) VALUES ('wpvivid_staging_data',%s)", $data);
            $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
            if ($db->get_results($update_query)===false)
            {
                $error=$db->last_error;
                $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
            }

            $update_query =$db->prepare("UPDATE {$prefix}options SET option_value = %s WHERE option_name = 'blog_public'", '0');
            $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
            if ($db->get_results($update_query)===false)
            {
                $error=$db->last_error;
                $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
            }
        }

        if(!$this->task->is_restore())
        {
            $permalink = get_option( 'permalink_structure','');

            $update_query = $db->prepare("UPDATE {$prefix}options SET option_value = %s WHERE option_name = 'permalink_structure'", $permalink);
            $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
            if ($db->get_results($update_query)===false)
            {
                $error=$db->last_error;
                $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
            }
        }


        $update_query =$db->prepare("UPDATE {$prefix}options SET option_value = %s WHERE option_name = 'upload_path'", "");
        $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
        if ($db->get_results($update_query)===false)
        {
            $error=$db->last_error;
            $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
        }

        if($this->task->is_mu_single()&&!$this->task->is_restore())
        {
            if($this->task->is_mu_single())
            {
                switch_to_blog($this->task->get_mu_single_site_id());
                $current   = get_option( 'active_plugins', array() );
                restore_current_blog();
            }
            else
            {
                $current   = get_option('active_plugins',array());
            }

            if(!in_array('wpvivid-staging/wpvivid-staging.php',$current))
                $current[] = 'wpvivid-staging/wpvivid-staging.php';
            sort( $current );
            $value=serialize($current);
            $update_query = $db->prepare("UPDATE {$prefix}options SET option_value=%s WHERE option_name='active_plugins'" , $value);
            $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
            if ($db->get_results($update_query)===false)
            {
                $error=$db->last_error;
                $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
            }
        }
    }

    public function init_replace_tables_list()
    {
        if(empty($this->task->get_tables('db_replace')))
        {
            global $wpvivid_plugin;
            $wpvivid_plugin->staging->log->WriteLog('Retrieve the tables required to replace.','notice');

            $tables=array();

            $db=$this->get_db_instance(true);

            $copyed_tables=$this->task->get_tables('db');

            foreach ($copyed_tables as $table)
            {
                global $wpdb;
                if($this->task->is_mu_single()&&($table['name']==$wpdb->base_prefix.'users'||$table['name']==$wpdb->base_prefix.'usermeta'))
                {
                    $og_table_name=$this->str_replace_limit($wpdb->base_prefix,'',$table['name'], 1);
                    $new_table_name=$this->replace_prefix.$og_table_name;
                }
                else
                {
                    $og_table_name=$this->str_replace_limit($this->old_prefix,'',$table['name'], 1);
                    $new_table_name=$this->replace_prefix.$og_table_name;
                }
                //$og_table_name=$this->str_replace_limit($this->old_prefix,'',$table['name'], 1);
                //$new_table_name=$this->replace_prefix.$og_table_name;
                if(!$this->task->is_tables_exclude($new_table_name,$this->replace_prefix))
                {
                    $table['name']=$new_table_name;
                    $table['start']=0;
                    $table['finished']=0;
                    $tables[$new_table_name]=$table;
                }
            }

            /*
            $sql=$db->prepare("SHOW TABLES LIKE %s;", $db->esc_like($this->replace_prefix) . '%');

            $result = $db->get_results($sql, OBJECT_K);

            if($result===false)
            {
                $error='Failed to retrieve database tables, error:'.$db->last_error;
                $wpvivid_staging->log->WriteLog($error,'error');
                $this->task->set_error($error);
                return false;
            }
            if(empty($result))
            {
                $error='Tables not found in database.';
                $wpvivid_staging->log->WriteLog($error,'error');
                $this->task->set_error($error);
                return false;
            }

            foreach ($result as $table_name=>$value)
            {
                if(!$this->task->is_tables_exclude($table_name,$this->replace_prefix))
                {
                    $table['name']=$table_name;
                    $table['start']=0;
                    $table['finished']=0;
                    $tables[$table_name]=$table;
                }
            }*/
            //$wpvivid_staging->log->WriteLog(json_encode($tables),'test');
            $this->task->update_tables('db_replace',$tables);
        }
        return true;
    }

    public function init_copy_tables_list()
    {
        if(empty($this->task->get_tables('db')))
        {
            global $wpvivid_plugin;
            $wpvivid_plugin->staging->log->WriteLog('Retrieve the tables required to copy.','notice');

            $tables=array();
            $db=$this->get_db_instance(false);

            $sql=$db->prepare("SHOW TABLES LIKE %s;", $db->esc_like($this->old_prefix) . '%');

            $result = $db->get_results($sql, OBJECT_K);

            if($result===false)
            {
                $error='Failed to retrieve database tables, error:'.$db->last_error;
                $wpvivid_plugin->staging->log->WriteLog($error,'error');
                $this->task->set_error($error);
                return false;
            }
            if(empty($result))
            {
                $error='Tables not found in database.';
                $wpvivid_plugin->staging->log->WriteLog($error,'error');
                $this->task->set_error($error);
                return false;
            }

            foreach ($result as $table_name=>$value)
            {
                if(!$this->task->is_tables_exclude($table_name))
                {
                    $table['name']=$table_name;
                    $table['create']=0;
                    $table['start']=0;
                    $table['finished']=0;
                    $tables[$table_name]=$table;
                }
            }

            if($this->task->is_mu_single())
            {
                global $wpdb;
                $sql=$db->prepare("SHOW TABLES LIKE %s;", $db->esc_like($wpdb->base_prefix) . '%');
                $result = $db->get_results($sql, OBJECT_K);
                foreach ($result as $table_name=>$value)
                {
                    if(!$this->task->is_tables_exclude($table_name))
                    {
                        $table['name']=$table_name;
                        $table['create']=0;
                        $table['start']=0;
                        $table['finished']=0;
                        $tables[$table_name]=$table;
                    }
                }
            }

            global $wpdb;
            $all_tables = (array) $wpdb->get_results( "SHOW FULL TABLES", ARRAY_N );
            if(!empty($all_tables) && !empty($tables)){
                foreach ($tables as $table_name => $table){
                    foreach ($all_tables as $table_arr){
                        if($table_name === $table_arr[0] && $table_arr[1] === 'VIEW'){
                            unset($tables[$table_name]);
                        }
                    }
                }
            }

            $this->task->update_tables('db',$tables);
        }
        return true;
    }

    public function init_rename_tables_list()
    {
        if(empty($this->task->get_tables('db_rename')))
        {
            global $wpvivid_plugin;
            $wpvivid_plugin->staging->log->WriteLog('Retrieve the tables required to rename.','notice');

            $tables=array();

            $db=$this->get_db_instance(true);

            $sql=$db->prepare("SHOW TABLES LIKE %s;", $db->esc_like($this->replace_prefix) . '%');

            $result = $db->get_results($sql, OBJECT_K);

            if($result===false)
            {
                $error='Failed to retrieve database tables, error:'.$db->last_error;
                $wpvivid_plugin->staging->log->WriteLog($error,'error');
                $this->task->set_error($error);
                return false;
            }
            if(empty($result))
            {
                $error='Tables not found in database.';
                $wpvivid_plugin->staging->log->WriteLog($error,'error');
                $this->task->set_error($error);
                return false;
            }

            foreach ($result as $table_name=>$value)
            {
                $table['name']=$table_name;
                $table['start']=0;
                $table['finished']=0;
                $tables[$table_name]=$table;
            }

            $this->task->update_tables('db_rename',$tables);
        }
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

    public function is_same_database()
    {
        $db=$this->task->get_db_connect();
        if( $db['des_use_additional_db']||$db['src_use_additional_db'])
        {
            return false;
        }
        else
        {
            return true;
        }

    }

    public function copy_table(&$table,$count)
    {
        $ret['result']='success';
        if($table==false)
        {
            return $ret;
        }

        if($table['create']==0)
        {
            $ret=$this->create_table($table['name']);
            if($ret['result']=='success')
            {
                $table['create']=1;
            }
            else
            {
                return $ret;
            }
        }

        return $this->copy_table_data($table,$count);
    }

    function str_replace_limit($search, $replace, $subject, $limit=-1)
    {

        if (is_array($search)) {
            foreach ($search as $k=>$v) {
                $search[$k] = '`' . preg_quote($search[$k],'`') . '`';
            }
        }
        else {
            $search = '`' . preg_quote($search,'`') . '`';
        }

        return preg_replace($search, $replace, $subject, $limit);
    }

    public function create_table($table_name)
    {
        global $wpvivid_plugin,$wpdb;

        if($this->task->is_mu_single()&&($table_name==$wpdb->base_prefix.'users'||$table_name==$wpdb->base_prefix.'usermeta'))
        {
            $og_table_name=$this->str_replace_limit($wpdb->base_prefix,'',$table_name, 1);
            $new_table_name=$this->new_prefix.$og_table_name;
        }
        else
        {
            $og_table_name=$this->str_replace_limit($this->old_prefix,'',$table_name, 1);
            $new_table_name=$this->new_prefix.$og_table_name;
        }

        $new_db=$this->get_db_instance(true);
        $old_db=$this->get_db_instance();

        $query = $old_db->prepare( 'SHOW TABLES LIKE %s', $new_db->esc_like( $new_table_name ) );

        if ( $new_db->get_var( $query ) == $new_table_name )
        {
            $new_db->query('SET foreign_key_checks = 0');
            $new_db->query("DROP TABLE IF EXISTS {$new_table_name}");
        }

        $result = $old_db->get_results( "SHOW CREATE TABLE `{$table_name}`", ARRAY_A );
        if( isset($result[0]['Create Table']))
        {
            $query=$result[0]['Create Table'];

            $query = str_replace( "CREATE TABLE `{$table_name}`", "CREATE TABLE `{$new_table_name}`", $query );

            $query = preg_replace_callback( "/CONSTRAINT\s`(\w+)`/", function()
            {
                $new="CONSTRAINT `" . uniqid() . "`";
                return $new;
            }, $query );

            $query = preg_replace_callback( "/REFERENCES\s`(\w+)`/", function($matches)
            {
                $new=str_replace($this->old_prefix,$this->new_prefix,$matches[0]);
                return $new;
            }, $query );

            $new_db->query('SET FOREIGN_KEY_CHECKS=0;');

            if( false === $new_db->query( $query ) )
            {
                $error='Failed to create a table. Error:'.$new_db->last_error.', query:'.$query;
                $wpvivid_plugin->staging->log->WriteLog($error,'error');
                $ret['result']='failed';
                $ret['error']=$error;
            }
            else
            {
                $ret['result']='success';
            }
        }
        else
        {
            $error='Failed to retrieve the table structure. Table name: '.$table_name;
            $wpvivid_plugin->staging->log->WriteLog($error,'error');
            $ret['result']='failed';
            $ret['error']=$error;
        }
        return $ret;
    }

    public function copy_table_data(&$table,$count)
    {
        $ret['result']='success';

        $new_db=$this->get_db_instance(true);
        $old_db=$this->get_db_instance();

        global $wpdb;
        if($this->task->is_mu_single()&&($table['name']==$wpdb->base_prefix.'users'||$table['name']==$wpdb->base_prefix.'usermeta'))
        {
            $og_table_name=$this->str_replace_limit($wpdb->base_prefix,'',$table['name'], 1);
            $new_table_name=$this->new_prefix.$og_table_name;
        }
        else
        {
            $og_table_name=$this->str_replace_limit($this->old_prefix,'',$table['name'], 1);
            $new_table_name=$this->new_prefix.$og_table_name;
        }

        $old_table_name=$table['name'];

        $sum =$old_db->get_var("SELECT COUNT(1) FROM `{$old_table_name}`");
        if($sum==0)
        {
            $table['finished']=1;
            return $ret;
        }


        $limit = " LIMIT {$count} OFFSET {$table['start']}";

        $new_db->query('SET FOREIGN_KEY_CHECKS=0;');

        if($this->is_same_database())
        {
            $select =  "SELECT * FROM `{$old_table_name}` {$limit}";
            if($new_db->query( "INSERT INTO `{$new_table_name}` ".$select )===false)
            {
                $error='Failed to insert '.$new_table_name.', error: '.$new_db->last_error;
                global $wpvivid_plugin;
                $wpvivid_plugin->staging->log->WriteLog($error,'warning');

                $start =$new_db->get_var("SELECT COUNT(1) FROM `{$new_table_name}`");
                if($start===false)
                {
                    $ret['result']='failed';
                    $ret['error']=$error;
                    return $ret;
                }
                global $wpvivid_plugin;
                $wpvivid_plugin->staging->log->WriteLog('new start offset '.$start,'warning');
                $limit = " LIMIT {$count} OFFSET {$start}";
                $select =  "SELECT * FROM `{$old_table_name}` {$limit}";
                if($new_db->query( "INSERT INTO `{$new_table_name}` ".$select )===false)
                {
                    $error='Failed to insert '.$new_table_name.', error: '.$new_db->last_error;
                    global $wpvivid_plugin;
                    $wpvivid_plugin->staging->log->WriteLog($error,'error');
                    $ret['result']='failed';
                    $ret['error']=$error;
                    return $ret;
                }
                else
                {
                    $table['start']=$start;
                }
            }
        }
        else
        {
            $rows = $old_db->get_results( "SELECT * FROM `{$old_table_name}` {$limit}", ARRAY_A );

            foreach ( $rows as $row )
            {
                if($new_db->insert($new_table_name,$row)===false)
                {
                    //global $wpvivid_staging;
                    //$error='Failed to insert '.$new_table_name.', error: '.$new_db->last_error;
                }
            }
        }

        $table['start'] += $count;

        if( $table['start'] > $sum )
        {
            $table['finished']=1;
        }

        return $ret;
    }

    public function replace_table(&$table,$count)
    {
        $ret['result']='success';

        global $wpvivid_plugin;
        $this->replacing_table=$table['name'];
        $db=$this->get_db_instance(true);
        if(substr($table['name'], strlen($this->replace_prefix))=='usermeta')
        {
            if($this->old_prefix!=$this->new_prefix)
            {
                $update_query ='UPDATE '.$table['name'].' SET meta_key=REPLACE(meta_key,"'.$this->old_prefix.'","'.$this->new_prefix.'") WHERE meta_key LIKE "'.str_replace('_','\_',$this->old_prefix).'%";';
                $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
                $wpvivid_plugin->staging->log->WriteLog('The length of UPDATE statement: '.strlen($update_query), 'notice');
                if ($db->get_results($update_query)===false)
                {
                    $error=$db->last_error;
                    $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
                }
                $table['finished']=1;

                if($this->task->is_mu_single())
                {
                    global $wpdb;
                    $update_query ='UPDATE '.$table['name'].' SET meta_key=REPLACE(meta_key,"'.$wpdb->base_prefix.'","'.$this->new_prefix.'") WHERE meta_key LIKE "'.str_replace('_','\_',$wpdb->base_prefix).'%";';
                    $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
                    $wpvivid_plugin->staging->log->WriteLog('The length of UPDATE statement: '.strlen($update_query), 'notice');
                    if ($db->get_results($update_query)===false)
                    {
                        $error=$db->last_error;
                        $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
                    }
                }
                return $ret;
            }
        }

        if(is_multisite())
        {
            if(substr($table['name'], strlen($this->replace_prefix))=='blogs')
            {
                $wpvivid_plugin->staging->log->WriteLog('update mu blogs', 'notice');

                if((preg_match('#^https?://([^/]+)#i', $this->new_home_url, $matches) || preg_match('#^https?://([^/]+)#i', $this->new_site_url, $matches)) && (preg_match('#^https?://([^/]+)#i', $this->old_home_url, $old_matches) || preg_match('#^https?://([^/]+)#i', $this->old_site_url, $old_matches)))
                {
                    $new_string = strtolower($matches[1]);
                    $old_string = strtolower($old_matches[1]);

                    $query = 'SELECT * FROM `'.$table['name'].'`';
                    $result=$db->get_results($query,ARRAY_A);
                    if($result && sizeof($result)>0)
                    {
                        $rows = $result;
                        $mu_option=$this->task->get_mu_option();
                        $wpvivid_plugin->staging->log->WriteLog(json_encode($mu_option), 'notice');
                        foreach ($rows as $row)
                        {
                            $update=array();
                            $where=array();

                            $old_domain_data = $row['domain'];
                            $new_domain_data=str_replace($old_string,$new_string,$old_domain_data);

                            $temp_where='`blog_id` = "' . $row['blog_id'] . '"';
                            if (is_callable(array($db, 'remove_placeholder_escape')))
                                $temp_where = $db->remove_placeholder_escape($temp_where);
                            $where[] = $temp_where;
                            $update[] = '`domain` = "' . $new_domain_data . '"';

                            $new_path_data=$mu_option['site'][$row['blog_id']]['path_site'];
                            $update[] = '`path` = "' . $new_path_data . '"';

                            if(!empty($update)&&!empty($where))
                            {
                                $update_query = 'UPDATE `'.$table['name'].'` SET '.implode(', ', $update).' WHERE '.implode(' AND ', array_filter($where)).';';
                                $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
                                $db->get_results($update_query);
                            }
                        }
                    }
                }
            }
        }

        $skip_table=false;
        if(apply_filters('wpvivid_restore_db_skip_replace_tables',$skip_table,$table['name']))
        {
            $wpvivid_plugin->staging->log->WriteLog('Ignore table '.$table['name'], 'Warning');
            $table['finished']=1;
            return $ret;
        }

        $sum =$db->get_var("SELECT COUNT(1) FROM `{$table['name']}`");

        if($sum>0)
        {
            $query='DESCRIBE `'.$table['name'].'`';
            $result=$db->get_results($query,ARRAY_A);
            if($result===false)
            {
                $error=$db->last_error;
                $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
                $table['finished']=1;
                return $ret;
            }
            $columns=array();
            foreach ($result as $data)
            {
                $column['Field']=$data['Field'];
                if($data['Key']=='PRI')
                    $column['PRI']=1;
                else
                    $column['PRI']=0;

                if($data['Type']=='mediumblob')
                {
                    $column['skip']=1;
                }
                $columns[]=$column;
            }
            $update_query='';

            $start_row=$table['start'];
            $wpvivid_plugin->staging->log->WriteLog('Start replacing the table prefix from '.$start_row. ' row.', 'notice');
            $end_row=$count;
            $query = 'SELECT * FROM `'.$table['name'].'` LIMIT '.$start_row.', '.$end_row;
            $result=$db->get_results($query,ARRAY_A);
            if($result && sizeof($result)>0)
            {
                $rows = $result;
                foreach ($rows as $row)
                {
                    if( isset( $row['option_value'] ) && strlen( $row['option_value'] ) >= 5000000 )
                    {
                        continue;
                    }
                    $update=array();
                    $where=array();
                    foreach ($columns as $column)
                    {
                        if(isset($column['skip']))
                        {
                            $wpvivid_plugin->staging->log->WriteLog('Skip MEDIUMBLOB data.', 'notice');
                            continue;
                        }
                        if($column['Field']=='option_name'&&$row[$column['Field']]=='mainwp_child_subpages')
                        {
                            break;
                        }
                        $old_data = $row[$column['Field']];
                        $size = strlen( $old_data );
                        if( $size >= 5000000 )
                        {
                            continue;
                        }
                        if($column['PRI']==1)
                        {
                            $db->escape_by_ref($old_data);
                            $temp_where='`'.$column['Field'].'` = "' . $old_data . '"';
                            if (is_callable(array($db, 'remove_placeholder_escape')))
                                $temp_where = $db->remove_placeholder_escape($temp_where);
                            $where[] = $temp_where;
                        }
                        $skip_row=false;
                        if(apply_filters('wpvivid_restore_db_skip_replace_rows',$skip_row,$table['name'],$column['Field']))
                        {
                            continue;
                        }
                        $new_data=$this->replace_row_data($old_data);
                        if($new_data==$old_data)
                            continue;
                        $db->escape_by_ref($new_data);
                        if (is_callable(array($db, 'remove_placeholder_escape')))
                            $new_data = $db->remove_placeholder_escape($new_data);
                        $update[] = '`'.$column['Field'].'` = "' . $new_data . '"';
                    }
                    if(!empty($update)&&!empty($where))
                    {
                        $temp_query = 'UPDATE `'.$table['name'].'` SET '.implode(', ', $update).' WHERE '.implode(' AND ', array_filter($where)).';';
                        $update_query=$temp_query;

                        if ($db->get_results($update_query)===false)
                        {
                            $error=$db->last_error;
                            $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
                        }
                        $update_query='';
                    }
                }
            }
            if(!empty($update_query))
            {
                $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
                if ($db->get_results($update_query)===false)
                {
                    $error=$db->last_error;
                    $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
                }
            }
        }
        $wpvivid_plugin->staging->log->WriteLog('Replacing database tables is completed.', 'notice');
        $table['start'] += $count;
        if( $table['start'] > $sum )
        {
            $table['finished']=1;
        }
        if($table['finished'])
        {
            if(substr($table['name'], strlen($this->replace_prefix))=='options')
            {
                $update_query ='UPDATE '.$table['name'].' SET option_name="'.$this->new_prefix.'user_roles" WHERE option_name="'.$this->old_prefix.'user_roles";';
                $wpvivid_plugin->staging->log->WriteLog($update_query, 'notice');
                $wpvivid_plugin->staging->log->WriteLog('The length of UPDATE statement: '.strlen($update_query), 'notice');
                if ($db->get_results($update_query)===false)
                {
                    $error=$db->last_error;
                    $wpvivid_plugin->staging->log->WriteLog($error, 'Warning');
                }
            }
        }
        return $ret;
    }

    public function replace_row_data($old_data)
    {
        $unserialize_data = @unserialize($old_data);
        if($unserialize_data===false)
        {
            $old_data=$this->replace_string_v2($old_data);
        }
        else
        {
            $old_data=$this->replace_serialize_data($unserialize_data);
            $old_data=serialize($old_data);
        }

        return $old_data;
    }

    private function replace_serialize_data($data,$serialized = false)
    {
        if(is_serialized( $data ) && ( $serialize_data = @unserialize( $data ) ) !== false)
        {
            $data=$this->replace_serialize_data($serialize_data,true);
        }
        else if(is_array($data))
        {
            foreach ($data as $key => $value)
            {
                $data[$key]=$this->replace_serialize_data($value);
            }
        }
        else if(is_object($data))
        {
            $temp = $data; // new $data_class();
            if (is_a($data, '__PHP_Incomplete_Class'))
            {

            }
            else
            {
                $props = get_object_vars($data);
                foreach ($props as $key => $value)
                {
                    if (strpos($key, "\0")===0)
                        continue;
                    $temp->$key = $this->replace_serialize_data($value);
                }
            }
            $data = $temp;
            unset($temp);
        }
        else if(is_string($data))
        {
            $data=$this->replace_string_v2($data);
        }
        if($serialized)
            $data=serialize($data);
        return $data;
    }

    public function get_mix_link($url)
    {
        if (0 === stripos($url, 'https://'))
        {
            $mix_link = 'http://'.substr($url, 8);
        } elseif (0 === stripos($url, 'http://')) {
            $mix_link = 'https://'.substr($url, 7);
        }
        else
        {
            $mix_link=false;
        }
        return $mix_link;
    }

    public function get_remove_http_link($url)
    {
        if (0 === stripos($url, 'https://'))
        {
            $mix_link = '//'.substr($url, 8);
        } elseif (0 === stripos($url, 'http://')) {
            $mix_link = '//'.substr($url, 7);
        }
        else
        {
            $mix_link=false;
        }
        return $mix_link;
    }

    public function get_remove_http_link_ex($url)
    {
        if (0 === stripos($url, 'https://'))
        {
            $mix_link = '\/\/'.substr($url, 8);
        } elseif (0 === stripos($url, 'http://')) {
            $mix_link = '\/\/'.substr($url, 7);
        }
        else
        {
            $mix_link=false;
        }
        return $mix_link;
    }

    public function replace_string_v2($old_string)
    {
        if(!is_string($old_string))
        {
            return $old_string;
        }

        $from=array();
        $to=array();

        $new_url_use_https=false;
        if (0 === stripos($this->new_site_url, 'https://')|| stripos($this->new_site_url, 'https:\/\/'))
        {
            $new_url_use_https=true;
        }
        else if (0 === stripos($this->new_site_url, 'http://')|| stripos($this->new_site_url, 'http:\/\/'))
        {
            $new_url_use_https=false;
        }

        $prefix=$this->replace_prefix;

        if($this->old_site_url!=$this->new_site_url)
        {
            if(substr($this->replacing_table, strlen($prefix))=='posts'||substr($this->replacing_table, strlen($prefix))=='postmeta'||substr($this->replacing_table, strlen($prefix))=='options')
            {
                $remove_http_link=$this->get_remove_http_link($this->old_site_url);
                if($remove_http_link!==false)
                {
                    $new_remove_http_link=$this->get_remove_http_link($this->new_site_url);
                    $from[]=$remove_http_link;
                    $to[]=$new_remove_http_link;

                    if($new_url_use_https)
                    {
                        $from[]='http:'.$new_remove_http_link;
                        $to[]='https:'.$new_remove_http_link;
                    }
                    else
                    {
                        $from[]='https:'.$new_remove_http_link;
                        $to[]='http:'.$new_remove_http_link;
                    }

                    $quote_old_site_url=$this->get_http_link_at_quote($remove_http_link);
                    $quote_new_site_url=$this->get_http_link_at_quote($new_remove_http_link);
                    $from[]=$quote_old_site_url;
                    $to[]=$quote_new_site_url;
                    if($new_url_use_https)
                    {
                        $from[]='http:'.$quote_new_site_url;
                        $to[]='https:'.$quote_new_site_url;
                    }
                    else
                    {
                        $from[]='https:'.$quote_new_site_url;
                        $to[]='http:'.$quote_new_site_url;
                    }
                }
                else
                {
                    $remove_http_link=$this->get_remove_http_link_ex($this->old_site_url);
                    if($remove_http_link!==false)
                    {
                        $new_remove_http_link=$this->get_remove_http_link_ex($this->new_site_url);
                        $from[]=$remove_http_link;
                        $to[]=$new_remove_http_link;

                        if($new_url_use_https)
                        {
                            $from[]='http:'.$new_remove_http_link;
                            $to[]='https:'.$new_remove_http_link;
                        }
                        else
                        {
                            $from[]='https:'.$new_remove_http_link;
                            $to[]='http:'.$new_remove_http_link;
                        }
                    }
                }
            }
            else
            {
                $from[]=$this->old_site_url;
                $to[]=$this->new_site_url;
            }
        }


        if($this->old_home_url!=$this->old_site_url&&$this->old_home_url!=$this->new_home_url)
        {
            if(substr($this->replacing_table, strlen($prefix))=='posts'||substr($this->replacing_table, strlen($prefix))=='postmeta'||substr($this->replacing_table, strlen($prefix))=='options')
            {
                $remove_http_link=$this->get_remove_http_link($this->old_home_url);
                if($remove_http_link!==false)
                {
                    $new_remove_http_link=$this->get_remove_http_link($this->new_home_url);
                    $from[]=$remove_http_link;
                    $to[]=$new_remove_http_link;

                    if($new_url_use_https)
                    {
                        $from[]='http:'.$new_remove_http_link;
                        $to[]='https:'.$new_remove_http_link;
                    }
                    else
                    {
                        $from[]='https:'.$new_remove_http_link;
                        $to[]='http:'.$new_remove_http_link;
                    }

                    $quote_old_site_url=$this->get_http_link_at_quote($remove_http_link);
                    $quote_new_site_url=$this->get_http_link_at_quote($new_remove_http_link);
                    $from[]=$quote_old_site_url;
                    $to[]=$quote_new_site_url;
                    if($new_url_use_https)
                    {
                        $from[]='http:'.$quote_new_site_url;
                        $to[]='https:'.$quote_new_site_url;
                    }
                    else
                    {
                        $from[]='https:'.$quote_new_site_url;
                        $to[]='http:'.$quote_new_site_url;
                    }
                }
                else
                {
                    $remove_http_link=$this->get_remove_http_link_ex($this->old_home_url);
                    if($remove_http_link!==false)
                    {
                        $new_remove_http_link=$this->get_remove_http_link_ex($this->new_home_url);
                        $from[]=$remove_http_link;
                        $to[]=$new_remove_http_link;

                        if($new_url_use_https)
                        {
                            $from[]='http:'.$new_remove_http_link;
                            $to[]='https:'.$new_remove_http_link;
                        }
                        else
                        {
                            $from[]='https:'.$new_remove_http_link;
                            $to[]='http:'.$new_remove_http_link;
                        }
                    }
                }
            }
            else
            {
                $from[]=$this->old_home_url;
                $to[]=$this->new_home_url;
            }
        }


        if(!empty($from)&&!empty($to))
        {
            $old_string=str_replace($from,$to,$old_string);
        }

        return $old_string;
    }

    public function replace_string($old_string)
    {
        if(!is_string($old_string))
        {
            return $old_string;
        }

        $from=array();
        $to=array();

        if($this->old_site_url!=$this->new_site_url)
        {
            $remove_http_link=$this->get_remove_http_link($this->old_site_url);
            $new_remove_http_link=$this->get_remove_http_link($this->new_site_url);
            if(strpos($new_remove_http_link,$remove_http_link)!==false)
            {
                return $this->replace_string_ex($old_string);
            }
        }

        if($this->old_site_url!=$this->new_site_url)
        {
            $from[]=$this->old_site_url;
            $to[]=$this->new_site_url;
            $old_mix_link=$this->get_mix_link($this->old_site_url);
            if($old_mix_link!==false)
            {
                $from[]=$old_mix_link;
                $to[]=$this->new_site_url;
            }
            if(substr($this->replacing_table, strlen($this->new_prefix))=='posts'||substr($this->replacing_table, strlen($this->new_prefix))=='postmeta'||substr($this->replacing_table, strlen($this->new_prefix))=='options')
            {
                $remove_http_link=$this->get_remove_http_link($this->old_site_url);
                if($remove_http_link!==false)
                {
                    $new_remove_http_link=$this->get_remove_http_link($this->new_site_url);
                    $from[]=$remove_http_link;
                    $to[]=$new_remove_http_link;
                    $quote_old_site_url=$this->get_http_link_at_quote($remove_http_link);
                    $quote_new_site_url=$this->get_http_link_at_quote($new_remove_http_link);
                    $from[]=$quote_old_site_url;
                    $to[]=$quote_new_site_url;
                }

                $remove_http_link=$this->get_remove_http_link_ex($this->old_site_url);
                if($remove_http_link!==false)
                {
                    $new_remove_http_link=$this->get_remove_http_link_ex($this->new_site_url);
                    $from[]=$remove_http_link;
                    $to[]=$new_remove_http_link;
                    $quote_old_site_url=$this->get_http_link_at_quote($remove_http_link);
                    $quote_new_site_url=$this->get_http_link_at_quote($new_remove_http_link);
                    $from[]=$quote_old_site_url;
                    $to[]=$quote_new_site_url;
                }
            }
            $quote_old_site_url=$this->get_http_link_at_quote($this->old_site_url);
            $quote_new_site_url=$this->get_http_link_at_quote($this->new_site_url);
            $from[]=$quote_old_site_url;
            $to[]=$quote_new_site_url;
        }

        if($this->old_home_url!=$this->new_home_url)
        {
            $from[]=$this->old_home_url;
            $to[]=$this->new_home_url;

            $old_mix_link=$this->get_mix_link($this->old_home_url);
            if($old_mix_link!==false)
            {
                $from[]=$old_mix_link;
                $to[]=$this->new_home_url;
            }
        }

        if(!empty($from)&&!empty($to))
        {
            $old_string=str_replace($from,$to,$old_string);
        }


        return $old_string;
    }

    public function replace_string_ex($old_string)
    {
        if(!is_string($old_string))
        {
            return $old_string;
        }

        $from=array();
        $place_holder=array();
        $to=array();

        if($this->old_site_url!=$this->new_site_url)
        {
            $remove_http_link=$this->get_remove_http_link($this->old_site_url);
            if($remove_http_link!==false)
            {
                $new_remove_http_link=$this->get_remove_http_link($this->new_site_url);
                $from[]=$remove_http_link;
                //$place_holder[]=$this->get_place_holder(0);
                $to[]=$new_remove_http_link;

                $quote_old_site_url=$this->get_http_link_at_quote($remove_http_link);
                $quote_new_site_url=$this->get_http_link_at_quote($new_remove_http_link);
                $from[]=$quote_old_site_url;
                //$place_holder[]=$this->get_place_holder(3);
                $to[]=$quote_new_site_url;
            }

            $old_mix_link=$this->get_mix_link($this->old_site_url);
            if($old_mix_link!==false)
            {
                $from[]=$old_mix_link;
                //$place_holder[]=$this->get_place_holder(1);
                $to[]=$this->new_site_url;
            }
            if(substr($this->replacing_table, strlen($this->new_prefix))=='posts'||substr($this->replacing_table, strlen($this->new_prefix))=='postmeta'||substr($this->replacing_table, strlen($this->new_prefix))=='options')
            {
                $remove_http_link=$this->get_remove_http_link_ex($this->old_site_url);
                if($remove_http_link!==false)
                {
                    $new_remove_http_link=$this->get_remove_http_link_ex($this->new_site_url);
                    $from[]=$remove_http_link;
                    //$place_holder[]=$this->get_place_holder(2);
                    $to[]=$new_remove_http_link;

                    $quote_old_site_url=$this->get_http_link_at_quote($remove_http_link);
                    $quote_new_site_url=$this->get_http_link_at_quote($new_remove_http_link);
                    $from[]=$quote_old_site_url;
                    //$place_holder[]=$this->get_place_holder(3);
                    $to[]=$quote_new_site_url;
                }
            }
            $quote_old_site_url=$this->get_http_link_at_quote($this->old_site_url);
            $quote_new_site_url=$this->get_http_link_at_quote($this->new_site_url);
            $from[]=$quote_old_site_url;
            //$place_holder[]=$this->get_place_holder(3);
            $to[]=$quote_new_site_url;
        }

        if($this->old_home_url!=$this->new_home_url&&$this->old_home_url!=$this->old_site_url)
        {
            $from[]=$this->old_home_url;
            //$place_holder[]=$this->get_place_holder(4);
            $to[]=$this->new_home_url;

            $old_mix_link=$this->get_mix_link($this->old_home_url);
            if($old_mix_link!==false)
            {
                $from[]=$old_mix_link;
                //$place_holder[]=$this->get_place_holder(5);
                $to[]=$this->new_home_url;
            }
        }

        if(!empty($from)&&!empty($to))
        {
            //$old_string=str_replace($from,$place_holder,$old_string);
            $old_string=str_replace($from,$to,$old_string);
        }


        return $old_string;
    }

    public function get_place_holder($index)
    {
        if ( empty($this->placeholder) )
        {
            for($i=0;$i<6;$i++)
            {
                // If ext/hash is not present, compat.php's hash_hmac() does not support sha256.
                $algo = function_exists( 'hash' ) ? 'sha256' : 'sha1';
                // Old WP installs may not have AUTH_SALT defined.
                $salt = defined( 'AUTH_SALT' ) && AUTH_SALT ? AUTH_SALT : (string) rand();

                $this->placeholder[$i] = '{' . hash_hmac( $algo, uniqid( $salt, true ), $salt ) . '}';
            }
        }

        return $this->placeholder[$index];
    }

    public function get_http_link_at_quote($url)
    {
        return str_replace('/','\/',$url);
    }

    private function rename_table(&$table)
    {
        global $wpvivid_plugin;
        $table_name=$table['name'];
        $tasks=array();
        $og_table=$this->new_prefix.substr($table_name, strlen($this->replace_prefix));
        $db=$this->get_db_instance(true);
        $db->query('SET FOREIGN_KEY_CHECKS=0;');

        if(substr($table_name, strlen($this->replace_prefix))=='options')
        {
            $tasks = get_option('wpvivid_staging_task_list', array());
        }

        $wpvivid_plugin->staging->log->WriteLog('DROP TABLE '.$og_table,'notice');
        if($db->query("DROP TABLE IF EXISTS {$og_table}")===false)
        {
            $wpvivid_plugin->staging->log->WriteLog('Failed to drop table '.$og_table.', error:'.$db->last_error,'notice');
        }
        $wpvivid_plugin->staging->log->WriteLog('Rename a table named '.$table_name.' to '.$og_table.' ','notice');

        if ($db->query("RENAME TABLE {$table_name} TO {$og_table}")===false)
        {
            $wpvivid_plugin->staging->log->WriteLog('Failed to rename a table named '.$table_name.' to '.$og_table.', error: '.$db->last_error,'notice');
        }

        if(substr($table_name, strlen($this->replace_prefix))=='options')
        {
            wp_cache_delete ( 'alloptions', 'options' );
            update_option('wpvivid_staging_task_list',$tasks);
        }

        $table['finished']=1;

        $ret['result']='success';
        return $ret;
    }
}