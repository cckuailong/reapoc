<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}
class WPvivid_Staging_Copy_Files
{
    public $task;
    public $cache_file;
    public $cache_file_name;

    public function __construct($task_id)
    {
        $this->task=new WPvivid_Staging_Task($task_id);
        $this->cache_file_name=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.'wpvivid_staging'.DIRECTORY_SEPARATOR.$this->task->get_id().'_staging_cache.txt';
        $this->cache_file=false;

    }

    public function do_copy_file($key)
    {
        global $wpvivid_plugin;

        $src_path=$des_path='';
        $wpvivid_plugin->staging->log->WriteLog('Retrieve the files required to copy.','notice');
        $list=$this->get_copy_dir_list($key,$src_path,$des_path);
        if(!file_exists($this->cache_file_name))
        {
            $wpvivid_plugin->staging->log->WriteLog('Create a cache file.','notice');
            $this->create_cache_file($list);
        }

        $start=$this->task->get_start($key);
        $wpvivid_plugin->staging->log->WriteLog('Copying files starts from: '.$start,'notice');
        $wpvivid_plugin->staging->log->WriteLog('Copying files from '.$src_path.' to '.$des_path,'notice');

        while($this->copy_files($start,$this->task->get_files_copy_count(),$src_path,$des_path))
        {
            $wpvivid_plugin->staging->log->WriteLog('The count of copied files: '.$this->task->get_files_copy_count(),'notice');
            $wpvivid_plugin->staging->log->WriteLog('The next copying files starts from:'.$start,'notice');
            $this->task->update_start($key,$start);
        }
        $wpvivid_plugin->staging->log->WriteLog('Copying '.$key.' files is completed.','notice');

        if($key=='core')
        {
            $this->check_wp_config();

            if(is_multisite()&&$this->task->is_restore())
            {

            }
            else
            {
                $this->change_wp_config();
            }

            if(is_multisite()&&!$this->task->is_restore())
            {
                $this->change_htaccess();
            }
        }
        $this->task->update_job_finished($key);

        $this->clean_up();

        return true;
    }

    public function check_wp_config()
    {
        $des_path=$this->task->get_path();
        $des=$des_path.DIRECTORY_SEPARATOR.'wp-config.php';
        if(file_exists($des))
        {
            return;
        }
        else
        {
            if ( file_exists( ABSPATH . 'wp-config.php' ) )
            {
                $src=ABSPATH . 'wp-config.php';
            }
            else if ( @file_exists( dirname( ABSPATH ) . '/wp-config.php' ))
            {
                $src=dirname( ABSPATH ) . '/wp-config.php';
            }
            else
            {
                global $wpvivid_plugin;
                $wpvivid_plugin->staging->log->WriteLog('not found wp-config.php file','notice');
                return ;
            }
            if(copy($src,$des))
            {
                @chmod($des,0755);
            }
        }
    }

    public function change_wp_config()
    {
        global $wpvivid_plugin;

        $des_path=$this->task->get_path();
        $path=$des_path.DIRECTORY_SEPARATOR.'wp-config.php';
        $data=file_get_contents($path);
        if( $data === false )
        {
            $wpvivid_plugin->staging->log->WriteLog('wp-config.php not found in '.$path,'notice');
            return false;
        }

        $pattern     = '/\$table_prefix\s*=\s*(.*)/';
        $replacement = '$table_prefix = \'' . $this->task->get_db_prefix(true) . '\';';
        $data     = preg_replace( $pattern, $replacement, $data );


        if( $data===null )
        {
            $wpvivid_plugin->staging->log->WriteLog('table_prefix not found in wp-config.php','notice');
            return false;
        }

        preg_match( "/define\s*\(\s*['\"]WP_HOME['\"]\s*,\s*(.*)\s*\);/", $data, $matches );
        if( !empty( $matches[1] ) )
        {
            $wpvivid_plugin->staging->log->WriteLog('WP_HOME found in wp-config.php','notice');
            $pattern = "/define\s*\(\s*['\"]WP_HOME['\"]\s*,\s*(.*)\s*\);.*/";
            $replace = "define('WP_HOME','" . $this->task->get_home_url(true) . "'); //";
            $data = preg_replace( array($pattern), $replace, $data );
            if( null === ($data) )
            {
                $wpvivid_plugin->staging->log->WriteLog('WP_HOME not replace in wp-config.php','notice');
                return false;
            }
        }

        preg_match( "/define\s*\(\s*['\"]WP_SITEURL['\"]\s*,\s*(.*)\s*\);/", $data, $matches );
        if( !empty( $matches[1] ) )
        {
            $wpvivid_plugin->staging->log->WriteLog('WP_SITEURL found in wp-config.php','notice');
            $pattern = "/define\s*\(\s*['\"]WP_SITEURL['\"]\s*,\s*(.*)\s*\);.*/";
            $replace = "define('WP_SITEURL','" . $this->task->get_site_url(true) . "'); //";
            $data = preg_replace( array($pattern), $replace, $data );
            if( null === ($data) )
            {
                $wpvivid_plugin->staging->log->WriteLog('WP_SITEURL not replace in wp-config.php','notice');
                return false;
            }
        }

        if(is_multisite()&&!$this->task->is_restore())
        {
            preg_match( "/define\s*\(\s*['\"]PATH_CURRENT_SITE['\"]\s*,\s*(.*)\s*\);/", $data, $matches );
            if( !empty( $matches[1] ) )
            {
                $mu_option=$this->task->get_mu_option();
                $wpvivid_plugin->staging->log->WriteLog('PATH_CURRENT_SITE found in wp-config.php','notice');
                $pattern = "/define\s*\(\s*['\"]PATH_CURRENT_SITE['\"]\s*,\s*(.*)\s*\);.*/";
                $replace = "define('PATH_CURRENT_SITE','" .$mu_option['path_current_site'] . "'); //";
                $data = preg_replace( array($pattern), $replace, $data );
                if( null === ($data) )
                {
                    $wpvivid_plugin->staging->log->WriteLog('PATH_CURRENT_SITE not replace in wp-config.php','notice');
                    return false;
                }
            }

            if($this->task->is_mu_single())
            {
                preg_match( "/define\s*\(\s*['\"]WP_ALLOW_MULTISITE['\"]\s*,\s*(.*)\s*\);/", $data, $matches );
                if( !empty( $matches[1] ) )
                {
                    $wpvivid_plugin->staging->log->WriteLog('WP_ALLOW_MULTISITE found in wp-config.php','notice');
                    $pattern = "/define\s*\(\s*['\"]WP_ALLOW_MULTISITE['\"]\s*,\s*(.*)\s*\);.*/";
                    $replace = "define('WP_ALLOW_MULTISITE',false); //";
                    $data = preg_replace( array($pattern), $replace, $data );
                    if( null === ($data) )
                    {
                        $wpvivid_plugin->staging->log->WriteLog('WP_ALLOW_MULTISITE not replace in wp-config.php','notice');
                        return false;
                    }
                }

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
                        return false;
                    }
                }

                preg_match( "/define\s*\(\s*['\"]UPLOADS['\"]\s*,\s*(.*)\s*\);/", $data, $matches );
                if( !empty( $matches[1] ) )
                {
                    $wpvivid_plugin->staging->log->WriteLog('UPLOADS found in wp-config.php','notice');
                    $pattern = "/define\s*\(\s*['\"]UPLOADS['\"]\s*,\s*(.*)\s*\);.*/";
                    $replace = "define('UPLOADS','".$this->task->get_mu_single_upload()."'); //";
                    $data = preg_replace( array($pattern), $replace, $data );
                    if( null === ($data) )
                    {
                        $wpvivid_plugin->staging->log->WriteLog('MULTISITE not replace in wp-config.php','notice');
                        return false;
                    }
                }
                else
                {
                    preg_match("/if\s*\(\s*\s*!\s*defined\s*\(\s*['\"]ABSPATH['\"]\s*(.*)\s*\)\s*\)/", $data, $matches);
                    if (!empty($matches[0]))
                    {
                        $matches[0];
                        $pattern = "/if\s*\(\s*\s*!\s*defined\s*\(\s*['\"]ABSPATH['\"]\s*(.*)\s*\)\s*\)/";
                        $replace = "define('UPLOADS', '".$this->task->get_mu_single_upload()."'); \n".
                            "if ( ! defined( 'ABSPATH' ) )";
                        $data = preg_replace( array($pattern), $replace, $data );
                        if (null === ($data))
                        {
                            $wpvivid_plugin->staging->log->WriteLog('UPLOADS not replace in wp-config.php','notice');
                            return false;
                        }
                    }
                }
            }
        }

        $db=$this->task->get_db_connect();

        if($this->task->is_restore())
        {
            $wpvivid_plugin->staging->log->WriteLog('Edit wp-config.php','notice');
            if( $db['src_use_additional_db'])
            {

                $pattern     = "/define\s*\(\s*'DB_NAME'\s*,\s*(.*)\s*\);.*/";
                $replacement = "define( 'DB_NAME', '".DB_NAME."');";
                $data     = preg_replace( $pattern, $replacement, $data );

                $pattern     = "/define\s*\(\s*'DB_USER'\s*,\s*(.*)\s*\);.*/";
                $replacement = "define( 'DB_USER', '".DB_USER."');";
                $data     = preg_replace( $pattern, $replacement, $data );

                $pattern     = "/define\s*\(\s*'DB_PASSWORD'\s*,\s*(.*)\s*\);.*/";
                $replacement = "define( 'DB_PASSWORD', '".DB_PASSWORD."');";
                $data     = preg_replace( $pattern, $replacement, $data );

                $pattern     = "/define\s*\(\s*'DB_HOST'\s*,\s*(.*)\s*\);.*/";
                $replacement = "define( 'DB_HOST', '".DB_HOST."');";
                $data     = preg_replace( $pattern, $replacement, $data );
            }
        }
        else
        {
            if( $db['des_use_additional_db'])
            {
                $pattern     = "/define\s*\(\s*'DB_NAME'\s*,\s*(.*)\s*\);.*/";
                $replacement = "define( 'DB_NAME', '{$db['des_dbname']}');";
                $data     = preg_replace( $pattern, $replacement, $data );

                $pattern     = "/define\s*\(\s*'DB_USER'\s*,\s*(.*)\s*\);.*/";
                $replacement = "define( 'DB_USER', '{$db['des_dbuser']}');";
                $data     = preg_replace( $pattern, $replacement, $data );

                $pattern     = "/define\s*\(\s*'DB_PASSWORD'\s*,\s*(.*)\s*\);.*/";
                $replacement = "define( 'DB_PASSWORD', '{$db['des_dbpassword']}');";
                $data     = preg_replace( $pattern, $replacement, $data );

                $pattern     = "/define\s*\(\s*'DB_HOST'\s*,\s*(.*)\s*\);.*/";
                $replacement = "define( 'DB_HOST', '{$db['des_dbhost']}');";
                $data     = preg_replace( $pattern, $replacement, $data );
            }
        }

        file_put_contents($path,$data);

        $wpvivid_plugin->staging->log->WriteLog('Replacing table_prefix in wp-config.php is completed.','notice');
        return true;
    }

    public function change_htaccess()
    {
        global $wpvivid_plugin;
        $des_path=$this->task->get_path();
        $path=$des_path.DIRECTORY_SEPARATOR.'.htaccess';
        if(file_exists($path))
        {
            if(is_multisite()&&!$this->task->is_restore())
            {
                $mu_option=$this->task->get_mu_option();
                $data=file_get_contents($path);
                //$data = str_replace(PATH_CURRENT_SITE,$mu_option['path_current_site'],$data);
                preg_match( "/RewriteBase\s*(.*)\s*/", $data, $matches );
                if( !empty( $matches[1] ) )
                {
                    $new_rewrite_base = $mu_option['path_current_site'];
                    $wpvivid_plugin->staging->log->WriteLog('RewriteBase found in .htaccess','notice');
                    $pattern = "/RewriteBase\s*(.*)\s*.*/";
                    $replace = "RewriteBase $new_rewrite_base";
                    $data = preg_replace( array($pattern), $replace, $data );
                    if( null === ($data) )
                    {
                        $wpvivid_plugin->staging->log->WriteLog('WP_HOME not replace in wp-config.php','notice');
                    }
                }
                file_put_contents($path,$data);
            }
        }
    }

    public function get_copy_dir_list($key,&$src_path,&$des_path)
    {
        $list=array();
        if($key=='core')
        {
            $src_path=$this->task->get_path(false);
            $des_path=$this->task->get_path(true);

            $dir_info['root']=$this -> transfer_path($src_path);
            $dir_info['recursive']=false;
            if($this->task->is_restore()&&is_multisite())
            {
                $exclude_files_regex[]='#.htaccess#';
                $exclude_files_regex[]='#wp-config.php#';
                $dir_info['exclude_files_regex']=$exclude_files_regex;
            }
            $list[]=$dir_info;
            $dir_info['root']=$src_path.DIRECTORY_SEPARATOR.'wp-admin';
            $dir_info['recursive']=true;
            $list[]=$dir_info;
            $dir_info['root']=$src_path.DIRECTORY_SEPARATOR.'wp-includes';
            $list[]=$dir_info;
        }
        else if($key=='wp-content')
        {
            $des_path=$this->get_content_dir(true);
            $src_path=untrailingslashit($this->get_content_dir());
            $dir_info['root']=$this -> transfer_path($src_path);
            $dir_info['recursive']=true;
            $exclude_regex=$this->task->get_job_option($key,'exclude_regex');
            $exclude_files_regex=$this->task->get_job_option($key,'exclude_files_regex');
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_content_dir()).DIRECTORY_SEPARATOR.'updraft', '/').'#';   // Updraft Plus backup directory
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_content_dir()).DIRECTORY_SEPARATOR.'ai1wm-backups', '/').'#'; // All-in-one WP migration backup directory
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_content_dir()).DIRECTORY_SEPARATOR.'backups', '/').'#'; // Xcloner backup directory
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_content_dir()).DIRECTORY_SEPARATOR.'upgrade', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_content_dir()).DIRECTORY_SEPARATOR.'wpvivid', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_content_dir()).DIRECTORY_SEPARATOR.'wpvivid_staging', '/').'#';
            //$exclude_regex[]='#^'.preg_quote($this->transfer_path($this->get_content_dir()), '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_content_dir()).DIRECTORY_SEPARATOR.'cache', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_content_dir()).DIRECTORY_SEPARATOR.'w3tc-config', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_content_dir()).DIRECTORY_SEPARATOR.'Dropbox_Backup', '/').'#';
            $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_upload_dir()), '/').'#';
            $exclude_regex[]='#^'.preg_quote($this->transfer_path($this->get_theme_dir()), '/').'#';
            $exclude_regex[]='#^'.preg_quote($this->transfer_path($this->get_plugin_dir()), '/').'#';

            //$self_dir = str_replace($src_path, '', $this -> transfer_path($des_path));
            //$self_dir = str_replace('wp-content', '', $self_dir);
            //$self_dir = str_replace('\\', '', $self_dir);
            //$exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_content_dir()).DIRECTORY_SEPARATOR.$self_dir, '/').'#';
            $staging_list = get_option('wpvivid_staging_task_list',array());
            if(!empty($staging_list))
            {
                foreach ($staging_list as $key => $value)
                {
                    $exclude_regex[]='#^'.preg_quote($this -> transfer_path($value['path']['des_path']), '/').'$#';
                }
            }

            $dir_info['exclude_regex']=$exclude_regex;
            $dir_info['exclude_files_regex']=$exclude_files_regex;
            $list[]=$dir_info;
        }
        else if($key=='plugins')
        {
            $des_path=$this->get_plugin_dir(true);
            $src_path=untrailingslashit($this->get_plugin_dir());

            $dir_info['root']=$this -> transfer_path($src_path);
            $exclude_regex=$this->task->get_job_option($key,'exclude_regex');

            if($this->task->is_restore())
            {
                $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_plugin_dir().DIRECTORY_SEPARATOR.'wpvivid-backuprestore'), '/').'#';
                $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_plugin_dir().DIRECTORY_SEPARATOR.'wpvivid-staging'), '/').'#';
                $exclude_regex[]='#^'.preg_quote($this -> transfer_path($this->get_plugin_dir().DIRECTORY_SEPARATOR.'wpvivid-backup-pro'), '/').'#';
            }

            $dir_info['exclude_regex']=$exclude_regex;

            $dir_info['recursive']=true;
            $list[]=$dir_info;
        }
        else if($key=='theme')
        {
            $des_path=$this->get_theme_dir(true);
            $src_path=$this->get_theme_dir();
            $dir_info['root']=$this -> transfer_path($src_path);
            $dir_info['exclude_regex']=$this->task->get_job_option($key,'exclude_regex');
            $dir_info['recursive']=true;
            $list[]=$dir_info;
        }
        else if($key=='upload')
        {
            $des_path=$this->get_upload_dir(true);
            $src_path=$this->get_upload_dir();
            $dir_info['root']=$this -> transfer_path($src_path);
            $dir_info['exclude_regex']=$this->task->get_job_option($key,'exclude_regex');
            $exclude_files_regex=$this->task->get_job_option($key,'exclude_files_regex');
            if($this->task->get_job_option($key,'include_regex'))
            {
                $dir_info['include_regex']=$this->task->get_job_option($key,'include_regex');
            }
            $dir_info['exclude_files_regex']=$exclude_files_regex;

            $dir_info['recursive']=true;
            $list[]=$dir_info;
        }
        else
        {
            $src_path=$this->task->get_path(false);
            $des_path=$this->task->get_path();
            $path=$this->task->get_job_option($key,'root');
            $dir_info['root']=$this -> transfer_path($src_path.DIRECTORY_SEPARATOR.$path);
            $dir_info['exclude_regex']=$this->task->get_job_option($key,'exclude_regex');
            $exclude_files_regex=$this->task->get_job_option($key,'exclude_files_regex');
            $dir_info['exclude_files_regex']=$exclude_files_regex;
            $dir_info['recursive']=true;
            $list[]=$dir_info;
        }

        $src_path=$this -> transfer_path($src_path);
        $des_path=$this -> transfer_path($des_path);

        return $list;
    }

    public function get_content_dir($des=false)
    {
        $dir = str_replace( ABSPATH, '', WP_CONTENT_DIR );
        $src_path=$this->task->get_path($des);
        return $src_path.DIRECTORY_SEPARATOR.$dir;
    }

    public function get_upload_dir($des=false)
    {
        $upload_dir = wp_upload_dir();
        $dir = str_replace( ABSPATH, '', $upload_dir['basedir'] );
        $src_path=$this->task->get_path($des);
        return $src_path.DIRECTORY_SEPARATOR.$dir;
    }

    public function get_theme_dir($des=false)
    {
        $dir = str_replace( ABSPATH, '',get_theme_root() );
        $src_path=$this->task->get_path($des);
        return $src_path.DIRECTORY_SEPARATOR.$dir;
    }

    public function get_plugin_dir($des=false)
    {
        $dir = str_replace( ABSPATH, '',WP_PLUGIN_DIR );
        $src_path=$this->task->get_path($des);
        return $src_path.DIRECTORY_SEPARATOR.$dir;
    }

    private function transfer_path($path)
    {
        $path = str_replace('\\','/',$path);
        $values = explode('/',$path);
        return implode(DIRECTORY_SEPARATOR,$values);
    }

    public function create_cache_file($list)
    {
        if(file_exists($this->cache_file_name))
            @unlink($this->cache_file_name);
        $this->cache_file=fopen($this->cache_file_name,'a');
        foreach ($list as $item)
        {
            $exclude_regex=array();
            $exclude_files_regex=array();
            if(isset($item['exclude_regex'])&&$item['exclude_regex']!=false)
            {
                $exclude_regex=$item['exclude_regex'];
            }
            if(isset($item['exclude_files_regex'])&&$item['exclude_files_regex']!=false)
            {
                $exclude_files_regex=$item['exclude_files_regex'];
            }
            //
            if(isset($item['include_regex'])&&$item['include_regex']!=false)
            {
                $include_regex=$item['include_regex'];
            }
            else
            {
                $include_regex=array();
            }

            $this->create_cache_from_folder($item['root'],$item['recursive'],$exclude_regex,$exclude_files_regex,$include_regex);
        }
    }

    public function copy_files(&$start,$count,$src_path,$des_path)
    {
        global $wpvivid_plugin;
        $file = new SplFileObject($this->cache_file_name);

        if($start==0)
            $file->seek($start);
        else
            $file->seek($start-1);

        $file->setFlags( \SplFileObject::SKIP_EMPTY | \SplFileObject::READ_AHEAD );

        for ( $i = 0; $i < $count; $i++ )
        {
            if( $file->eof() )
            {
                return false;
            }
            $src = $file->fgets();

            $src=trim($src,PHP_EOL);

            if(empty($src))
                continue;

            $start++;

            if(!file_exists($src))
            {
                continue;
            }
            $src=$this -> transfer_path($src);
            $des=str_replace($src_path,$des_path,$src);

            if(is_dir($src))
            {
                @mkdir($des,0755,true);
            }
            else
            {
                if(copy($src,$des))
                {
                    @chmod($des,0755);
                }
                else
                {
                    $wpvivid_plugin->staging->log->WriteLog('Failed to copy files from '.$src.' to '.$des.'.','warning');
                }
            }
        }

        $file = null;
        return true;
    }

    public function create_cache_from_folder($folder,$recursive=false,$exclude_regex=array(),$exclude_files_regex=array(),$include_regex=array())
    {
        $this->getFolder($folder,$recursive,$exclude_regex,$exclude_files_regex,$include_regex);
    }

    public function getFolder($path,$recursive,$exclude_regex,$exclude_files_regex,$include_regex)
    {
        if($this->cache_file==false)
            $this->cache_file=fopen($this->cache_file_name,'a');

        if(is_dir($path))
        {
            $line = $path.PHP_EOL;
            fwrite($this->cache_file, $line);

            $handler = opendir($path);
            if($handler!==false)
            {
                while (($filename = readdir($handler)) !== false)
                {
                    if ($filename != "." && $filename != "..")
                    {
                        if (is_dir($path . DIRECTORY_SEPARATOR . $filename))
                        {
                            if($recursive&&$this->regex_match($exclude_regex, $path . DIRECTORY_SEPARATOR . $filename, 0))
                            {
                                if(!empty($include_regex))
                                {
                                    if($recursive&&$this->regex_match($include_regex, $path . DIRECTORY_SEPARATOR . $filename, 1))
                                    {
                                        $this->getFolder($path . DIRECTORY_SEPARATOR . $filename,$recursive,$exclude_regex,$exclude_files_regex,$include_regex);
                                    }
                                }
                                else
                                {
                                    $this->getFolder($path . DIRECTORY_SEPARATOR . $filename,$recursive,$exclude_regex,$exclude_files_regex,$include_regex);
                                }
                            }
                        } else {

                            if($this->regex_match($exclude_files_regex, $filename, 0))
                            {
                                if ($this->regex_match($exclude_regex, $path . DIRECTORY_SEPARATOR . $filename, 0))
                                {
                                    if(is_readable($path . DIRECTORY_SEPARATOR . $filename))
                                    {
                                        if (filesize($path . DIRECTORY_SEPARATOR . $filename) < $this->task->get_exclude_file_size() * 1024 * 1024 || $this->task->get_exclude_file_size() === 0)
                                        {
                                            $line = $path . DIRECTORY_SEPARATOR . $filename.PHP_EOL;
                                            fwrite($this->cache_file, $line);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }
        }
    }

    private function regex_match($regex_array,$string,$mode)
    {
        if(empty($regex_array))
        {
            return true;
        }

        if($mode==0)
        {
            foreach ($regex_array as $regex)
            {
                if(preg_match($regex,$string))
                {
                    return false;
                }
            }

            return true;
        }

        if($mode==1)
        {
            foreach ($regex_array as $regex)
            {
                if(preg_match($regex,$string))
                {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    public function clean_up()
    {
        if($this->cache_file)
            fclose($this->cache_file);
        @unlink($this->cache_file_name);
    }
}