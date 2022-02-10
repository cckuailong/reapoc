<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
require_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-zipclass.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-backup-database.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-backup-site.php';
require_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-setting.php';
class WPvivid_RestoreDB
{
    private $support_engines;
    private $support_charsets;
    private $support_collates;

    private $default_engines;
    private $default_charsets;
    private $default_collates;
    private $old_prefix;
    private $old_base_prefix;
    private $new_prefix;


    private $old_site_url;
    private $old_home_url;
    private $old_content_url;
    private $old_upload_url;

    private $new_site_url;
    private $new_home_url;
    private $new_content_url;
    private $new_upload_url;

    private $current_setting;
    //private $db;
    //private $skip_query;

    private $replacing_table;

    private $db_method;

    private $is_mu;

    public function restore($path,$sql_file,$options)
    {
        add_filter('wpvivid_restore_db_skip_replace_tables', array($this, 'skip_tables'),10,2);
        add_filter('wpvivid_restore_db_skip_replace_rows', array($this, 'skip_rows'),10,3);

        include_once WPVIVID_PLUGIN_DIR . '/includes/class-wpvivid-restore-db-method.php';
        $this->db_method=new WPvivid_Restore_DB_Method();
        $this->db_method->set_skip_query(0);

        global $wpvivid_plugin;
        if(file_exists($path.$sql_file)) {
            if(!isset($options['skip_backup_old_database']))
            {
                $wpvivid_plugin->restore_data->write_log('Backing up current site\'s database.','notice');
                $backup_database = new WPvivid_Backup_Database();
                $old_sql_file=$path.'old_database.sql';
                $data['sql_file_name']=$old_sql_file;
                $result = $backup_database ->backup_database($data);
                if($result['result'] == WPVIVID_FAILED)
                {
                    $wpvivid_plugin->restore_data->write_log('Backing up current site\'s database failed.error:'.$result['error'],'warning');
                    return $result;
                }
            }

            $is_additional_db = false;
            $is_additional_db = apply_filters('wpvivid_check_additional_database', $is_additional_db, $options);
            if($is_additional_db){
                $result = $this->execute_extra_sql_file($path . $sql_file, $options);
            }
            else{
                $this->current_setting = WPvivid_Setting::export_setting_to_json();
                $ret=$this->db_method->connect_db();
                if($ret['result']==WPVIVID_FAILED)
                {
                    return $ret;
                }

                $this->db_method->test_db();
                $this->db_method->check_max_allow_packet();
                $this->db_method->init_sql_mode();

                $result = $this->execute_sql_file($path . $sql_file, $options);

                $this->enable_plugins();

                $this->wpvivid_fix_siteurl_home();

                unset($this->db_method);
                //do_action('wpvivid_restore_database_finish',$options);
            }
            return $result;
        }
        else {
            return array('result'=>'failed','error'=>'Database\'s .sql file not found. Please try again.');
        }
    }

    private function wpvivid_fix_siteurl_home(){
        global $wpvivid_plugin;
        $option_table = $this->new_prefix.'options';
        if($this->old_site_url!=$this->new_site_url)
        {
            //siteurl
            $update_query ='UPDATE '.$option_table.' SET option_value="'.$this->new_site_url.'" WHERE option_name="siteurl";';
            $wpvivid_plugin->restore_data->write_log($update_query, 'notice');
            $wpvivid_plugin->restore_data->write_log('update query len:'.strlen($update_query), 'notice');
            $this->execute_sql($update_query);
        }

        if($this->old_home_url!=$this->new_home_url)
        {
            //home
            $update_query ='UPDATE '.$option_table.' SET option_value="'.$this->new_home_url.'" WHERE option_name="home";';
            $wpvivid_plugin->restore_data->write_log($update_query, 'notice');
            $wpvivid_plugin->restore_data->write_log('update query len:'.strlen($update_query), 'notice');
            $this->execute_sql($update_query);
        }
    }

    private function execute_extra_sql_file($file, $options){
        global $wpvivid_plugin;
        $wpvivid_plugin->restore_data->write_log('Start import additional sql file.','notice');
        $dbhost = '';
        $dbuser = '';
        $dbpassword = '';
        $dbname = '';
        foreach ($options['additional_database'] as $db_name => $db_info){
            if($options['database'] === $db_name){
                $dbhost = $db_info['db_host'];
                $dbuser = $db_info['db_user'];
                $dbpassword = $db_info['db_pass'];
                $dbname = $db_info['db_name'];
                break;
            }
        }

        $restore_extra_db=new WPvivid_Restore_DB_Extra($dbhost, $dbuser, $dbpassword, $dbname);
        $ret = $restore_extra_db->execute_extra_sql_file($file, $options);
        return $ret;
    }

    private function enable_plugins()
    {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $plugin_list=array();
        $plugin_list[]='wpvivid-backuprestore/wpvivid-backuprestore.php';
        $plugin_list=apply_filters('wpvivid_enable_plugins_list',$plugin_list);

        if(is_multisite())
            activate_plugins($plugin_list,'',true,true);
        else
            activate_plugins($plugin_list,'',false,true);
    }

    private function execute_sql_file($file,$option)
    {
        global $wpdb,$wpvivid_plugin;

        $wpvivid_plugin->restore_data->write_log('Start import sql file.','notice');

        $this->support_engines=array();
        $this->support_charsets=array();
        $this->support_collates=array();
        $this->default_engines=array();
        $this->default_charsets=array();
        $this->default_collates=array();

        $this->is_mu=false;
        if(isset($option['is_mu']))
        {
            $this->is_mu=true;
        }

        if(isset($option['default_engine']))
        {
            $this->default_engines=$option['default_engine'];
        }
        else
        {
            $this->default_engines[]='MyISAM';
        }

        if(isset($option['default_charsets']))
        {
            $this->default_charsets=$option['default_charsets'];
        }
        else
        {
            $this->default_charsets[]=DB_CHARSET;
        }

        if(isset($option['default_collations']))
        {
            $this->default_collates=$option['default_collations'];
        }
        else
        {
            $this->default_collates[]=DB_COLLATE;
        }

        if($this->is_mu&&isset($option['site_id']))
        {
            $this->old_prefix=$option['blog_prefix'];

            $wpvivid_plugin->restore_data->write_log('old site prefix:'.$this->old_prefix,'notice');
            $this->old_site_url=$option['site_url'];
            $wpvivid_plugin->restore_data->write_log('old site url:'.$this->old_site_url,'notice');
            $this->old_home_url=$option['home_url'];
            $wpvivid_plugin->restore_data->write_log('old home url:'.$this->old_home_url,'notice');
            $this->old_content_url='';
            $this->old_upload_url='';

            if($option['overwrite'])
            {
                $this->new_prefix=$wpdb->get_blog_prefix($option['overwrite_site']);
                $this->new_site_url= untrailingslashit(get_site_url($option['overwrite_site']));
                $this->new_home_url=untrailingslashit(get_home_url($option['overwrite_site']));
                $this->new_content_url=untrailingslashit(content_url());
                $upload_dir  = wp_upload_dir();
                $this->new_upload_url=untrailingslashit($upload_dir['baseurl']);
            }
            else
            {
                $this->new_prefix=$wpdb->get_blog_prefix($option['site_id']);
                $this->new_site_url= untrailingslashit(get_site_url($option['site_id']));
                $this->new_home_url=untrailingslashit(get_home_url($option['site_id']));
                $this->new_content_url=untrailingslashit(content_url());
                $upload_dir  = wp_upload_dir();
                $this->new_upload_url=untrailingslashit($upload_dir['baseurl']);
            }

        }
        else
        {
            $this->old_prefix='';
            $this->old_base_prefix='';
            if(isset($option['mu_migrate']))
            {
                $this->old_base_prefix=$option['base_prefix'];
            }
            $this->new_prefix=$wpdb->base_prefix;

            $this->old_site_url='';
            $this->old_home_url='';
            $this->old_content_url='';
            $this->old_upload_url='';

            $this->new_site_url= untrailingslashit(site_url());

            $this->new_home_url=untrailingslashit(home_url());

            $this->new_content_url=untrailingslashit(content_url());

            $upload_dir  = wp_upload_dir();
            $this->new_upload_url=untrailingslashit($upload_dir['baseurl']);
        }


        $wpdb->query('SET FOREIGN_KEY_CHECKS=0;');

        $result = $wpdb->get_results("SHOW ENGINES", OBJECT_K);
        foreach ($result as $key=>$value)
        {
            $this->support_engines[]=$key;
        }

        $result = $wpdb->get_results("SHOW CHARACTER SET", OBJECT_K);
        foreach ($result as $key=>$value)
        {
            $this->support_charsets[]=$key;
        }

        $result = $wpdb->get_results("SHOW COLLATION", OBJECT_K);
        foreach ($result as $key=>$value)
        {
            $this->support_collates[$key]=$value;
        }

        $sql_handle = fopen($file,'r');
        if($sql_handle===false)
        {
            $ret['result']=WPVIVID_FAILED;
            $ret['error']='file not found. file name:'.$file;
            return $ret;
        }
        $line_num = 0;
        $query='';

        $current_table='';
        $current_old_table='';
        while(!feof($sql_handle))
        {
            $line = fgets($sql_handle);
            $line_num ++;
            $startWith = substr(trim($line), 0 ,2);
            $startWithEx = substr(trim($line), 0 ,3);
            $endWith = substr(trim($line), -1 ,1);
            $line = rtrim($line);
            if (empty($line) || $startWith == '--' || ($startWith == '/*'&&$startWithEx!='/*!') || $startWith == '//')
            {
               if ($endWith == ';' && preg_match('- # -',$line))
               {
                    $matcher = array();
                    if(empty($this -> site_url) && preg_match('# site_url: (.*?) #',$line,$matcher))
                    {
                        if(empty( $this->old_site_url))
                        {
                            $this->old_site_url = $matcher[1];
                            $wpvivid_plugin->restore_data->write_log('old site url:'.$this->old_site_url,'notice');
                        }

                    }
                    if(empty($this -> home_url) && preg_match('# home_url: (.*?) #',$line,$matcher))
                    {
                        if(empty( $this->old_home_url))
                        {
                            $this->old_home_url = $matcher[1];
                            $wpvivid_plugin->restore_data->write_log('old home url:'.$this->old_home_url,'notice');
                        }
                    }
                    if(empty($this -> content_url) && preg_match('# content_url: (.*?) #',$line,$matcher))
                    {
                        if(empty( $this->old_content_url))
                        {
                            $this->old_content_url = $matcher[1];
                            $wpvivid_plugin->restore_data->write_log('old content url:'.$this->old_content_url,'notice');
                        }
                    }
                    if(empty($this -> upload_url) && preg_match('# upload_url: (.*?) #',$line,$matcher))
                    {
                        if(empty( $this->old_upload_url))
                        {
                            $this->old_upload_url = $matcher[1];
                            $wpvivid_plugin->restore_data->write_log('old upload url:'.$this->old_upload_url,'notice');
                        }

                    }
                    if(empty($this -> table_prefix) && preg_match('# table_prefix: (.*?) #',$line,$matcher))
                    {
                        if(empty( $this->old_prefix))
                        {
                            $this->old_prefix = $matcher[1];
                            $wpvivid_plugin->restore_data->write_log('old site prefix:'.$this->old_prefix,'notice');
                        }
                    }
                }
                continue;
            }

            $query = $query . $line;
            if ($endWith == ';')
            {
                if (preg_match('#^\\s*CREATE TABLE#', $query))
                {
                    $current_table=$this->create_table($query,$current_old_table);
                }else if(preg_match('#^\\s*LOCK TABLES#',$query))
                {
                    $this->lock_table($query);
                }
                else if(preg_match('#^\\s*INSERT INTO#', $query))
                {
                    $this->insert($query);
                }
                else if(preg_match('#^\\s*DROP TABLE #', $query))
                {
                    if($this->old_prefix!=$this->new_prefix||(!empty($this->old_site_url)&&$this->old_site_url!=$this->new_site_url))
                    {
                        if(isset($option['is_migrate']))
                        {
                            if(!empty($current_table))
                            {
                                $this->replace_row($current_table);
                            }
                        }
                    }
                    $this->drop_table($query);
                }
                else if(preg_match('#\/*!#', $query))
                {
                    if ($this->replace_table_execute_sql($query,$current_old_table)===false)
                    {
                        $wpvivid_plugin->restore_data->write_log('Restore ' . basename($file) . ' error at line ' . $line_num . ',' . PHP_EOL . 'errorinfo: [' . implode('][', $this->db_method->errorInfo()) . ']', 'Warning');
                        $query = '';
                        continue;
                    }
                }
                else
                {
                    if ($this->db_method->execute_sql($query)===false)
                    {
                        $wpvivid_plugin->restore_data->write_log('Restore ' . basename($file) . ' error at line ' . $line_num . ',' . PHP_EOL . 'errorinfo: [' . implode('][', $this->db_method->errorInfo()) . ']', 'Warning');
                        $query = '';
                        continue;
                    }
                }
                $query = '';
            }
        }

        if($this->old_prefix!=$this->new_prefix||(!empty($this->old_site_url)&&$this->old_site_url!=$this->new_site_url))
        {
            if(isset($option['is_migrate']))
            {
                if(!empty($current_table))
                {
                    $this->replace_row($current_table);
                }
            }
        }

        WPvivid_Setting::import_json_to_setting($this->current_setting);
        do_action('wpvivid_reset_schedule');
        do_action('wpvivid_do_after_restore_db');

        fclose($sql_handle);
        $ret['result']=WPVIVID_SUCCESS;
        return $ret;
    }

    private function lock_table($query)
    {
        global $wpvivid_plugin;
        if(!empty($this->old_prefix)&&$this->old_prefix!=$this->new_prefix)
        {
            if (preg_match('/^\s*LOCK TABLES +\`?([^\`]*)\`?/i', $query, $matches))
            {
                $table_name = $matches[1];

                if(!empty($this->old_base_prefix)&&(substr($table_name,strlen($this->old_base_prefix))=='users'||substr($table_name,strlen($this->old_base_prefix))=='usermeta'))
                {
                    $new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_base_prefix));
                }
                else
                {
                    $new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_prefix));
                }

                //$new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_prefix));
                $wpvivid_plugin->restore_data->write_log('lock replace table:'.$table_name.' to :'.$new_table_name,'notice');
                $query=str_replace($table_name,$new_table_name,$query);
            }
        }
        $this->execute_sql($query);
    }

    private function replace_table_execute_sql($query,$table_name)
    {
        global $wpvivid_plugin;
        if(!empty($table_name))
        {
            $new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_prefix));
            $query=str_replace($table_name,$new_table_name,$query);
        }

        $this->execute_sql($query);
    }

    private function create_table($query,&$current_old_table)
    {
        global $wpvivid_plugin;
        $table_name='';
        if (preg_match('/^\s*CREATE TABLE +\`?([^\`]*)\`?/i', $query, $matches))
        {
            $table_name = $matches[1];
            $current_old_table=$table_name;
        }

        if(!empty($this->old_prefix)&&$this->old_prefix!=$this->new_prefix)
        {
            if(!empty($this->old_base_prefix)&&(substr($table_name,strlen($this->old_base_prefix))=='users'||substr($table_name,strlen($this->old_base_prefix))=='usermeta'))
            {
                $new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_base_prefix));
            }
            else
            {
                $new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_prefix));
            }

            $query=str_replace($table_name,$new_table_name,$query);
            $wpvivid_plugin->restore_data->write_log('Create table '.$new_table_name,'notice');
            $table_name=$new_table_name;
        }
        else
        {
            $wpvivid_plugin->restore_data->write_log('Create table '.$table_name,'notice');
        }

        if (preg_match('/ENGINE=([^\s;]+)/', $query, $matches))
        {
            $engine = $matches[1];
            $replace_engine=true;
            foreach ($this->support_engines as $support_engine)
            {
                if(strtolower($engine)==strtolower($support_engine))
                {
                    $replace_engine=false;
                    break;
                }
            }

            if($replace_engine!==false)
            {
                if(!empty($this->default_engines))
                    $replace_engine=$this->default_engines[0];
            }

            if($replace_engine!==false)
            {
                $wpvivid_plugin->restore_data->write_log('create table replace engine:'.$engine.' to :'.$replace_engine,'notice');
                $query=str_replace("ENGINE=$engine", "ENGINE=$replace_engine", $query);
            }
        }


        if (preg_match('/CHARSET ([^\s;]+)/', $query, $matches)||preg_match('/CHARSET=([^\s;]+)/', $query, $matches))
        {
            $charset = $matches[1];
            $replace_charset=true;
            foreach ($this->support_charsets as $support_charset)
            {
                if(strtolower($charset)==strtolower($support_charset))
                {
                    $replace_charset=false;
                    break;
                }
            }

            if($replace_charset)
            {
                $replace_charset=$this->default_charsets[0];
            }

            if($replace_charset!==false)
            {
                $wpvivid_plugin->restore_data->write_log('create table replace charset:'.$charset.' to :'.$replace_charset,'notice');
                $query=str_replace("CHARSET=$charset", "CHARSET=$replace_charset", $query);
                $query=str_replace("CHARSET $charset", "CHARSET=$replace_charset", $query);
                $charset=$replace_charset;
            }

            $collate='';

            if (preg_match('/ COLLATE ([a-zA-Z0-9._-]+)/i', $query, $matches))
            {
                $collate = $matches[1];
            }
            else if(preg_match('/ COLLATE=([a-zA-Z0-9._-]+)/i', $query, $matches))
            {
                $collate = $matches[1];
            }

            if(!empty($collate))
            {
                $replace_collate=true;
                foreach ($this->support_collates as $key=>$support_collate)
                {
                    if(strtolower($charset)==strtolower($support_collate->Charset)&&strtolower($collate)==strtolower($key))
                    {
                        $replace_collate=false;
                        break;
                    }
                }

                if($replace_collate)
                {
                    $replace_collate=false;
                    foreach ($this->support_collates as $key=>$support_collate)
                    {
                        if(strtolower($charset)==strtolower($support_collate->Charset))
                        {
                            if($support_collate->Default=='Yes')
                            {
                                $replace_collate=$key;
                            }
                        }
                    }

                    if($replace_collate==false)
                    {
                        foreach ($this->support_collates as $key=>$support_collate)
                        {
                            if(strtolower($charset)==strtolower($support_collate->Charset))
                            {
                                $replace_collate=$key;
                                break;
                            }
                        }
                    }
                }

                if($replace_collate!==false)
                {
                    $wpvivid_plugin->restore_data->write_log('create table replace collate:'.$collate.' to :'.$replace_collate,'notice');
                    $query=str_replace("COLLATE $collate", "COLLATE $replace_collate", $query);
                    $query=str_replace("COLLATE=$collate", "COLLATE=$replace_collate", $query);
                }
            }
        }
        else
        {
            if (preg_match('/ COLLATE ([a-zA-Z0-9._-]+)/i', $query, $matches))
            {
                $collate = $matches[1];
            }
            else if(preg_match('/ COLLATE=([a-zA-Z0-9._-]+)/i', $query, $matches))
            {
                $collate = $matches[1];
            }

            if(!empty($collate))
            {
                $replace_collate=true;
                foreach ($this->support_collates as $key=>$support_collate)
                {
                    if(strtolower($collate)==strtolower($key))
                    {
                        $replace_collate=false;
                        break;
                    }
                }

                if($replace_collate)
                {
                    $replace_collate=false;
                    foreach ($this->support_collates as $key=>$support_collate)
                    {
                        if(strtolower($this->default_charsets[0])==strtolower($support_collate->Charset))
                        {
                            if($support_collate->Default=='Yes')
                            {
                                $replace_collate=$key;
                            }
                        }
                    }

                    if($replace_collate==false)
                    {
                        foreach ($this->support_collates as $key=>$support_collate)
                        {
                            if(strtolower($this->default_charsets[0])==strtolower($support_collate->Charset))
                            {
                                $replace_collate=$key;
                                break;
                            }
                        }
                    }
                }

                if($replace_collate!==false)
                {
                    $wpvivid_plugin->restore_data->write_log('create table replace collate:'.$collate.' to :'.$replace_collate,'notice');
                    $query=str_replace("COLLATE $collate", "COLLATE $replace_collate", $query);
                    $query=str_replace("COLLATE=$collate", "COLLATE=$replace_collate", $query);
                }
            }
        }

        /*
        $charset='';
        if (preg_match('/CHARSET=([^\s;]+)/', $query, $matches))
        {
            $charset = $matches[1];
            $replace_charset=true;
            foreach ($this->support_charsets as $support_charset)
            {
                if(strtolower($charset)==strtolower($support_charset))
                {
                    $replace_charset=false;
                    break;
                }
            }

            if($replace_charset!==false)
            {
                if(!empty($this->default_charsets))
                    $replace_charset=$this->default_charsets[0];
            }

            if($replace_charset!==false)
            {
                $wpvivid_plugin->restore_data->write_log('create table replace charset:'.$charset.' to :'.$replace_charset,'notice');
                $query=str_replace("CHARSET=$charset", "CHARSET=$replace_charset", $query);
                $charset=$replace_charset;
            }
        }

        if (preg_match('/ COLLATE ([a-zA-Z0-9._-]+)/i', $query, $matches))
        {
            $collate = $matches[1];
            $replace_collate=true;
            foreach ($this->support_collates as $key=>$support_collate)
            {
                if(strtolower($collate)==strtolower($key))
                {
                    $replace_collate=false;
                    break;
                }
            }

            if($replace_collate!==false)
            {
                if(!empty($charset))
                {
                    foreach ($this->support_collates as $key=>$support_collate)
                    {
                        if(strtolower($charset)==strtolower($support_collate->Charset))
                        {
                            $replace_collate=$key;
                            break;
                        }
                    }
                }
                else
                {
                    if(!empty($this->default_collates))
                    {
                        $replace_collate=$this->default_collates[0];
                        if(empty($replace_collate))
                        {
                            foreach ($this->support_collates as $key=>$support_collate)
                            {
                                if(strtolower($this->default_charsets[0])==strtolower($support_collate->Charset))
                                {
                                    $replace_collate=$key;
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            if($replace_collate!==false)
            {
                $wpvivid_plugin->restore_data->write_log('create table replace collate:'.$collate.' to :'.$replace_collate,'notice');
                $query=str_replace("COLLATE $collate", "COLLATE $replace_collate", $query);
            }
        }
        else if(preg_match('/ COLLATE=([a-zA-Z0-9._-]+)/i', $query, $matches))
        {
            $collate = $matches[1];
            $replace_collate=true;
            foreach ($this->support_collates as $key=>$support_collate)
            {
                if(strtolower($collate)==strtolower($key))
                {
                    $replace_collate=false;
                    break;
                }
            }

            if($replace_collate!==false)
            {
                if(!empty($charset))
                {
                    foreach ($this->support_collates as $key=>$support_collate)
                    {
                        if(strtolower($charset)==strtolower($support_collate->Charset))
                        {
                            $replace_collate=$key;
                            break;
                        }
                    }
                }
                else
                {
                    if(!empty($this->default_collates))
                    {
                        $replace_collate=$this->default_collates[0];
                        if(empty($replace_collate))
                        {
                            foreach ($this->support_collates as $key=>$support_collate)
                            {
                                if(strtolower($this->default_charsets[0])==strtolower($support_collate->Charset))
                                {
                                    $replace_collate=$key;
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            if($replace_collate!==false)
            {
                $wpvivid_plugin->restore_data->write_log('create table replace collate:'.$collate.' to :'.$replace_collate,'notice');
                $query=str_replace("COLLATE=$collate", "COLLATE=$replace_collate", $query);
            }
        }
        */

        if(preg_match('/\/\*!.*\*\//', $query, $matches))
        {
            $annotation_content = $matches[0];
            $query = str_replace($annotation_content, '', $query);
        }

        $this->execute_sql($query);

        return $table_name;
    }

    private function insert($query)
    {
        global $wpvivid_plugin;
        if(!empty($this->old_prefix)&&$this->old_prefix!=$this->new_prefix)
        {
            if (preg_match('/^\s*INSERT INTO +\`?([^\`]*)\`?/i', $query, $matches))
            {
                $table_name = $matches[1];

                if(!empty($this->old_base_prefix)&&(substr($table_name,strlen($this->old_base_prefix))=='users'||substr($table_name,strlen($this->old_base_prefix))=='usermeta'))
                {
                    $new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_base_prefix));
                }
                else
                {
                    $new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_prefix));
                }
                //$new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_prefix));
                $query=str_replace($table_name,$new_table_name,$query);
            }
        }
        //$query=str_replace('INSERT INTO', 'INSERT IGNORE INTO', $query);

        $pos_mainwp_child=strpos($query,'mainwp_child_');
        $pos_ws_menu_editor_pro=strpos($query,'ws_menu_editor_pro');
        if($pos_mainwp_child!==false && $pos_ws_menu_editor_pro === false)
        {
            $wpvivid_plugin->restore_data->write_log('skip insert item: '.$query,'notice');
        }
        else{
            $this->execute_sql($query);
        }
    }

    private function drop_table($query)
    {
        global $wpvivid_plugin;
        if(!empty($this->old_prefix)&&$this->old_prefix!=$this->new_prefix)
        {
            if (preg_match('/^\s*DROP TABLE IF EXISTS +\`?([^\`]*)\`?\s*;/i', $query, $matches))
            {
                $table_name = $matches[1];

                if(!empty($this->old_base_prefix)&&(substr($table_name,strlen($this->old_base_prefix))=='users'||substr($table_name,strlen($this->old_base_prefix))=='usermeta'))
                {
                    $new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_base_prefix));
                    $wpvivid_plugin->restore_data->write_log('find user table:'.$table_name.' to :'.$new_table_name,'notice');
                }
                else
                {
                    $new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_prefix));
                }

                //$new_table_name=$this->new_prefix.substr($table_name,strlen($this->old_prefix));
                $query=str_replace($table_name,$new_table_name,$query);
            }
        }
        $wpvivid_plugin->restore_data->write_log('Drop table if exist','notice');
        $this->execute_sql($query);
    }

    private function replace_row($table_name)
    {
        global $wpdb,$wpvivid_plugin;

        $this->replacing_table=$table_name;

        $wpvivid_plugin->restore_data->write_log('Dumping table '.$table_name.' is complete. Start replacing row(s).', 'notice');

        if(substr($table_name, strlen($this->new_prefix))=='options')
        {
            //WPvivid_Setting::import_json_to_setting($this->current_setting);
            //WPvivid_Schedule::reset_schedule();
            //do_action('wpvivid_reset_schedule');
            if($this->old_prefix!=$this->new_prefix)
            {
                $update_query ='UPDATE '.$table_name.' SET option_name="'.$this->new_prefix.'user_roles" WHERE option_name="'.$this->old_prefix.'user_roles";';

                $wpvivid_plugin->restore_data->write_log($update_query, 'notice');
                $wpvivid_plugin->restore_data->write_log('update query len:'.strlen($update_query), 'notice');
                $this->execute_sql($update_query);
            }
        }

        if(substr($table_name, strlen($this->new_prefix))=='usermeta')
        {
            if($this->old_prefix!=$this->new_prefix)
            {
                $update_query ='UPDATE '.$table_name.' SET meta_key=REPLACE(meta_key,"'.$this->old_prefix.'","'.$this->new_prefix.'") WHERE meta_key LIKE "'.str_replace('_','\_',$this->old_prefix).'%";';

                $wpvivid_plugin->restore_data->write_log($update_query, 'notice');
                $wpvivid_plugin->restore_data->write_log('update query len:'.strlen($update_query), 'notice');
                $this->execute_sql($update_query);
                return ;
            }
        }

        if(!empty($this->old_base_prefix)&&substr($table_name,strlen($this->old_base_prefix))=='usermeta')
        {
            if($this->old_base_prefix!=$this->new_prefix)
            {
                $update_query ='UPDATE '.$table_name.' SET meta_key=REPLACE(meta_key,"'.$this->old_base_prefix.'","'.$this->new_prefix.'") WHERE meta_key LIKE "'.str_replace('_','\_',$this->old_base_prefix).'%";';

                $wpvivid_plugin->restore_data->write_log($update_query, 'notice');
                $wpvivid_plugin->restore_data->write_log('update query len:'.strlen($update_query), 'notice');
                $this->execute_sql($update_query);
                return ;
            }
        }

        if($this->old_site_url==$this->new_site_url)
            return ;

        if($this->is_mu)
        {
            if(substr($table_name, strlen($this->new_prefix))=='blogs')
            {
                $wpvivid_plugin->restore_data->write_log('update mu blogs', 'notice');

                if((preg_match('#^https?://([^/]+)#i', $this->new_home_url, $matches) || preg_match('#^https?://([^/]+)#i', $this->new_site_url, $matches)) && (preg_match('#^https?://([^/]+)#i', $this->old_home_url, $old_matches) || preg_match('#^https?://([^/]+)#i', $this->old_site_url, $old_matches)))
                {
                    $new_string = strtolower($matches[1]);
                    $old_string = strtolower($old_matches[1]);
                    $new_path='';
                    $old_path='';

                    if(defined( 'PATH_CURRENT_SITE' ))
                    {
                        $new_path=PATH_CURRENT_SITE;
                    }

                    $query = 'SELECT * FROM `'.$table_name.'`';
                    $result=$this->db_method->query($query,ARRAY_A);
                    if($result && sizeof($result)>0)
                    {
                        $rows = $result;
                        foreach ($rows as $row)
                        {
                            $update=array();
                            $where=array();

                            if($row['blog_id']==1)
                            {
                                $old_path=$row['path'];
                            }

                            $old_domain_data = $row['domain'];
                            $new_domain_data=str_replace($old_string,$new_string,$old_domain_data);

                            $temp_where='`blog_id` = "' . $row['blog_id'] . '"';
                            if (is_callable(array($wpdb, 'remove_placeholder_escape')))
                                $temp_where = $wpdb->remove_placeholder_escape($temp_where);
                            $where[] = $temp_where;
                            $update[] = '`domain` = "' . $new_domain_data . '"';

                            if(!empty($old_path)&&!empty($new_path))
                            {
                                $old_path_data= $row['path'];
                                $new_path_data=$this->str_replace_first($old_path,$new_path,$old_path_data);
                                $update[] = '`path` = "' . $new_path_data . '"';
                            }

                            if(!empty($update)&&!empty($where))
                            {
                                $update_query = 'UPDATE `'.$table_name.'` SET '.implode(', ', $update).' WHERE '.implode(' AND ', array_filter($where)).';';
                                $wpvivid_plugin->restore_data->write_log($update_query, 'notice');
                                $this->execute_sql($update_query);
                            }
                        }
                    }
                }
            }
        }


        $skip_table=false;
        if(apply_filters('wpvivid_restore_db_skip_replace_tables',$skip_table,$table_name))
        {
            $wpvivid_plugin->restore_data->write_log('Skip table '.$table_name, 'Warning');
            return ;
        }

        $query = 'SELECT COUNT(*) FROM `'.$table_name.'`';

        $result=$this->db_method->query($query,ARRAY_N);
        if($result && sizeof($result)>0)
        {
            $count=$result[0][0];
            $wpvivid_plugin->restore_data->write_log('Count of rows in '.$table_name.': '.$count, 'notice');
            if($count==0)
            {
                return ;
            }

            $query='DESCRIBE `'.$table_name.'`';
            $result=$this->db_method->query($query,ARRAY_A);
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
            $page=5000;

            $update_query='';

            $start_row=0;
            for ($current_row = $start_row; $current_row <= $count; $current_row += $page)
            {
                $wpvivid_plugin->restore_data->write_log('Replace the row in '.$current_row. ' line.', 'notice');
                $query = 'SELECT * FROM `'.$table_name.'` LIMIT '.$current_row.', '.$page;

                $result=$this->db_method->query($query,ARRAY_A);
                if($result && sizeof($result)>0)
                {
                    $rows = $result;
                    foreach ($rows as $row)
                    {
                        $update=array();
                        $where=array();
                        foreach ($columns as $column)
                        {
                            if(isset($column['skip']))
                            {
                                $wpvivid_plugin->restore_data->write_log('skip mediumblob type data', 'notice');
                                continue;
                            }

                            $old_data = $row[$column['Field']];
                            if($column['PRI']==1)
                            {
                                $wpdb->escape_by_ref($old_data);
                                $temp_where='`'.$column['Field'].'` = "' . $old_data . '"';
                                if (is_callable(array($wpdb, 'remove_placeholder_escape')))
                                    $temp_where = $wpdb->remove_placeholder_escape($temp_where);
                                $where[] = $temp_where;
                            }

                            $skip_row=false;
                            if(apply_filters('wpvivid_restore_db_skip_replace_rows',$skip_row,$table_name,$column['Field']))
                            {
                                continue;
                            }
                            $new_data=$this->replace_row_data($old_data);
                            if($new_data==$old_data)
                                continue;

                            $wpdb->escape_by_ref($new_data);
                            if (is_callable(array($wpdb, 'remove_placeholder_escape')))
                                $new_data = $wpdb->remove_placeholder_escape($new_data);
                            $update[] = '`'.$column['Field'].'` = "' . $new_data . '"';
                        }

                        if(!empty($update)&&!empty($where))
                        {
                            $temp_query = 'UPDATE `'.$table_name.'` SET '.implode(', ', $update).' WHERE '.implode(' AND ', array_filter($where)).';';
                            $type=$this->db_method->get_type();

                            if($type=='pdo_mysql')
                            {
                                if($update_query=='')
                                {
                                    $update_query=$temp_query;
                                    if(strlen($update_query)>$this->db_method->get_max_allow_packet())
                                    {
                                        $wpvivid_plugin->restore_data->write_log('update replace rows', 'notice');
                                        $this->execute_sql($update_query);

                                        $update_query='';
                                    }
                                }
                                else if(strlen($temp_query)+strlen($update_query)>$this->db_method->get_max_allow_packet())
                                {
                                    $wpvivid_plugin->restore_data->write_log('update replace rows', 'notice');
                                    $this->execute_sql($update_query);

                                    $update_query='';
                                }
                                else
                                {
                                    $update_query.=$temp_query;
                                }
                            }
                            else
                            {
                                $update_query=$temp_query;
                                //$wpvivid_plugin->restore_data->write_log('update replace rows', 'notice');
                                $this->execute_sql($update_query);
                                $update_query='';
                            }

                        }
                        //return;
                    }
                }
            }

            if(!empty($update_query))
            {
                $wpvivid_plugin->restore_data->write_log('update replace rows', 'notice');
                $this->execute_sql($update_query);
            }
        }
        $wpvivid_plugin->restore_data->write_log('finish replace rows', 'notice');
    }

    public function str_replace_first($from, $to, $content)
    {
        $from = '/'.preg_quote($from, '/').'/';

        return preg_replace($from, $to, $content, 1);
    }

    private function replace_row_data($old_data)
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
            /*if(is_array($unserialize_data))
            {
                $temp_data = array();
                foreach ($unserialize_data as $key => $value)
                {
                    $temp_data[$key]=$this->replace_string($value);
                }

                $old_data = $temp_data;
                unset($temp_data);
                $old_data=$this->replace_serialize_data($unserialize_data);
                $old_data=serialize($old_data);
            }
            else if(is_object($unserialize_data))
            {
                $temp_data = $unserialize_data;
                $props = get_object_vars($unserialize_data);
                foreach ($props as $key => $value)
                {
                    $temp_data->$key =$this->replace_string($value);
                }
                $old_data = $temp_data;
                unset($temp_data);
                $old_data=serialize($old_data);
            }*/
        }

        return $old_data;
    }

    private function replace_serialize_data($data)
    {
        if(is_string($data))
        {
            $serialize_data =@unserialize($data);
            if($serialize_data===false)
            {
                $data=$this->replace_string_v2($data);
            }
            else
            {
                $data=serialize($this->replace_serialize_data($serialize_data));
            }
        }
        else if(is_array($data))
        {
            foreach ($data as $key => $value)
            {
                if(is_string($value))
                {
                    $data[$key]=$this->replace_string_v2($value);
                }
                else if(is_array($value))
                {
                    $data[$key]=$this->replace_serialize_data($value);
                }
                else if(is_object($value))
                {
                    if (is_a($value, '__PHP_Incomplete_Class'))
                    {
                        //
                    }
                    else
                    {
                        $data[$key]=$this->replace_serialize_data($value);
                    }
                }
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
                    if(is_string($value))
                    {
                        $temp->$key =$this->replace_string_v2($value);
                    }
                    else if(is_array($value))
                    {
                        $temp->$key=$this->replace_serialize_data($value);
                    }
                    else if(is_object($value))
                    {
                        $temp->$key=$this->replace_serialize_data($value);
                    }
                }
            }
            $data = $temp;
            unset($temp);
        }

        return $data;
    }

    private function get_mix_link($url)
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

    private function get_remove_http_slash_link($url)
    {
        if (0 === stripos($url, 'https://'))
        {
            $mix_link = substr($url, 8);
        } elseif (0 === stripos($url, 'http://')) {
            $mix_link = substr($url, 7);
        }
        else
        {
            $mix_link=false;
        }
        return $mix_link;
    }

    private function get_remove_http_link($url)
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

    private function get_remove_http_link_ex($url)
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

    private function get_http_link_at_quote($url)
    {
        return str_replace('/','\/',$url);
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

        if($this->old_site_url!=$this->new_site_url)
        {
            if(substr($this->replacing_table, strlen($this->new_prefix))=='posts'||substr($this->replacing_table, strlen($this->new_prefix))=='postmeta'||substr($this->replacing_table, strlen($this->new_prefix))=='options')
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

                $tmp_old_site_url = str_replace(':', '%3A', $this->old_site_url);
                $tmp_old_site_url = str_replace('/', '%2F', $tmp_old_site_url);

                $tmp_new_site_url = str_replace(':', '%3A', $this->new_site_url);
                $tmp_new_site_url = str_replace('/', '%2F', $tmp_new_site_url);

                $from[]=$tmp_old_site_url;
                $to[]=$tmp_new_site_url;
            }
            else
            {
                $from[]=$this->old_site_url;
                $to[]=$this->new_site_url;

                $from[]=str_replace('/', '\/', $this->old_site_url);
                $to[]=str_replace('/', '\/', $this->new_site_url);

                $tmp_old_site_url = str_replace(':', '%3A', $this->old_site_url);
                $tmp_old_site_url = str_replace('/', '%2F', $tmp_old_site_url);

                $tmp_new_site_url = str_replace(':', '%3A', $this->new_site_url);
                $tmp_new_site_url = str_replace('/', '%2F', $tmp_new_site_url);

                $from[]=$tmp_old_site_url;
                $to[]=$tmp_new_site_url;
            }
        }


        if($this->old_home_url!=$this->old_site_url&&$this->old_home_url!=$this->new_home_url)
        {
            if(substr($this->replacing_table, strlen($this->new_prefix))=='posts'||substr($this->replacing_table, strlen($this->new_prefix))=='postmeta'||substr($this->replacing_table, strlen($this->new_prefix))=='options')
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

    private function replace_string($old_string)
    {
        if(!is_string($old_string))
        {
            return $old_string;
        }

        $from=array();
        $to=array();

        if($this->old_site_url!=$this->new_site_url)
        {
            $quote_old_site_url=$this->get_http_link_at_quote($this->old_site_url);
            $quote_new_site_url=$this->get_http_link_at_quote($this->new_site_url);
            $from[]=$quote_old_site_url;
            $to[]=$quote_new_site_url;
            //$old_string=str_replace($quote_old_site_url,$quote_new_site_url,$old_string);
        }


        if($this->old_site_url!=$this->new_site_url)
        {
            $remove_http_link=$this->get_remove_http_link($this->old_site_url);
            $new_remove_http_link=$this->get_remove_http_link($this->new_site_url);
            if($remove_http_link!==false&&$new_remove_http_link!==false&&strpos($new_remove_http_link,$remove_http_link)!==false)
            {
                return $this->replace_string_ex($old_string);
            }
        }

        if($this->old_site_url!=$this->new_site_url)
        {
            $old_string=str_replace($this->old_site_url,$this->new_site_url,$old_string);
            $old_mix_link=$this->get_mix_link($this->old_site_url);
            if($old_mix_link!==false)
            {
                $from[]=$old_mix_link;
                $to[]=$this->new_site_url;
                //$old_string=str_replace($old_mix_link,$this->new_site_url,$old_string);
            }
            if(substr($this->replacing_table, strlen($this->new_prefix))=='posts'||substr($this->replacing_table, strlen($this->new_prefix))=='postmeta'||substr($this->replacing_table, strlen($this->new_prefix))=='options')
            {
                $remove_http_link=$this->get_remove_http_link($this->old_site_url);
                if($remove_http_link!==false)
                {
                    $new_remove_http_link=$this->get_remove_http_link($this->new_site_url);
                    $from[]=$remove_http_link;
                    $to[]=$new_remove_http_link;
                    //$old_string=str_replace($remove_http_link,$new_remove_http_link,$old_string);
                }

                $remove_http_link=$this->get_remove_http_link_ex($this->old_site_url);
                if($remove_http_link!==false)
                {
                    $new_remove_http_link=$this->get_remove_http_link_ex($this->new_site_url);
                    $from[]=$remove_http_link;
                    $to[]=$new_remove_http_link;
                    //$old_string=str_replace($remove_http_link,$new_remove_http_link,$old_string);
                }
            }
        }

        if($this->old_home_url!=$this->new_home_url)
        {
            //$old_string=str_replace($this->old_home_url,$this->new_home_url,$old_string);
            $from[]=$this->old_home_url;
            $to[]=$this->new_home_url;
            $old_mix_link=$this->get_mix_link($this->old_home_url);
            if($old_mix_link!==false)
            {
                $from[]=$old_mix_link;
                $to[]=$this->new_home_url;
                //$old_string=str_replace($old_mix_link,$this->new_home_url,$old_string);
            }
        }

        if(!empty($from)&&!empty($to))
            $old_string=str_replace($from,$to,$old_string);

        return $old_string;
    }

    private function replace_string_ex($old_string)
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
            if($remove_http_link!==false)
            {
                $new_remove_http_link=$this->get_remove_http_link($this->new_site_url);
                $from[]=$remove_http_link;
                $to[]=$new_remove_http_link;
                //$old_string=str_replace($remove_http_link,$new_remove_http_link,$old_string);
            }

            $new_mix_link=$this->get_mix_link($this->old_site_url);
            if($new_mix_link!==false)
            {
                $from[]=$new_mix_link;
                $to[]=$this->new_site_url;
                //$old_string=str_replace($new_mix_link,$this->new_site_url,$old_string);
            }
            if(substr($this->replacing_table, strlen($this->new_prefix))=='posts'||substr($this->replacing_table, strlen($this->new_prefix))=='postmeta'||substr($this->replacing_table, strlen($this->new_prefix))=='options')
            {
                $remove_http_link=$this->get_remove_http_link_ex($this->old_site_url);
                if($remove_http_link!==false)
                {
                    $new_remove_http_link=$this->get_remove_http_link_ex($this->new_site_url);
                    $from[]=$remove_http_link;
                    $to[]=$new_remove_http_link;
                    //$old_string=str_replace($remove_http_link,$new_remove_http_link,$old_string);
                }
            }

            $quote_old_site_url=$this->get_http_link_at_quote($this->old_site_url);
            $quote_new_site_url=$this->get_http_link_at_quote($this->new_site_url);
            $from[]=$quote_old_site_url;
            $to[]=$quote_new_site_url;
        }

        if($this->old_home_url!=$this->new_home_url&&$this->old_home_url!=$this->old_site_url)
        {
            //$old_string=str_replace($this->old_home_url,$this->new_home_url,$old_string);
            $from[]=$this->old_home_url;
            $to[]=$this->new_home_url;
            $new_mix_link=$this->get_mix_link($this->new_home_url);
            if($new_mix_link!==false)
            {
                $from[]=$new_mix_link;
                $to[]=$this->new_home_url;
                //$old_string=str_replace($new_mix_link,$this->new_home_url,$old_string);
            }
        }

        if(!empty($from)&&!empty($to))
            $old_string=str_replace($from,$to,$old_string);

        return $old_string;
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
        $skip_tables[]='simple_history_contexts';
        $skip_tables[]='simple_history';
        $skip_tables[]='wffilemods';
        //
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

    public function check_max_allow_packet_ex()
    {
        $max_all_packet_warning=false;
        include_once WPVIVID_PLUGIN_DIR . '/includes/class-wpvivid-restore-db-method.php';
        $this->db_method=new WPvivid_Restore_DB_Method();

        $this->db_method->set_skip_query(0);

        $ret=$this->db_method->connect_db();
        if($ret['result']==WPVIVID_SUCCESS)
        {
            $max_allowed_packet = $this->db_method->query("SELECT @@session.max_allowed_packet;",ARRAY_N);
            if($max_allowed_packet)
            {
                if(is_array($max_allowed_packet)&&isset($max_allowed_packet[0])&&isset($max_allowed_packet[0][0]))
                {
                    if($max_allowed_packet[0][0]<16777216){
                        $max_all_packet_warning = 'max_allowed_packet = '.size_format($max_allowed_packet[0][0]).' is too small. The recommended value is 16M or higher. Too small value could lead to a failure when importing a larger database.';
                    }
                }
            }
        }
        return $max_all_packet_warning;
    }

    private function execute_sql($query)
    {
        return $this->db_method->execute_sql($query);
    }

}