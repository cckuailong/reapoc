<?php

if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
include_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-zipclass.php';
include_once plugin_dir_path( dirname( __FILE__ ) ) .'includes/class-wpvivid-backup.php';
class WPvivid_RestoreSite
{

    public function restore($option,$files)
    {
        global $wpvivid_plugin;
        if(isset($option['has_child']))
        {
            $backup=$wpvivid_plugin->restore_data->get_backup_data();
            $backup_item=new WPvivid_Backup_Item($backup);
            $root_path = $wpvivid_plugin->get_backup_folder();
            //$root_path=$backup_item->get_local_path();

            if(!file_exists($root_path))
            {
                @mkdir($root_path);
            }
            $wpvivid_plugin->restore_data->write_log('extract root:'.$root_path,'notice');
            $zip = new WPvivid_ZipClass();
            $all_files = array();
            foreach ($files as $file)
            {
                $all_files[] =$root_path.$file;
            }

            if(isset($option['extract_child_files']))
            {
                return $zip -> extract_ex($all_files,untrailingslashit($root_path),$option['extract_child_files']);
            }
            else
            {
                return $zip -> extract($all_files,untrailingslashit($root_path));
            }
        }
        else
        {
            $backup=$wpvivid_plugin->restore_data->get_backup_data();
            $backup_item=new WPvivid_Backup_Item($backup);
            $local_path = $wpvivid_plugin->get_backup_folder();
            //$local_path=$backup_item->get_local_path();

            $is_type_db = false;
            $is_type_db = apply_filters('wpvivid_check_type_database', $is_type_db, $option);
            if($is_type_db)
            {
                $path = $local_path.WPVIVID_DEFAULT_ROLLBACK_DIR.DIRECTORY_SEPARATOR.'wpvivid_old_database';
                if(file_exists($path))
                {
                    @mkdir($path);
                }

                $zip = new WPvivid_ZipClass();
                $all_files = array();
                foreach ($files as $file)
                {
                    $all_files[] = $local_path.$file;
                }

                $ret= $zip -> extract($all_files,$path);

                unset($zip);
            }
            else {
                $root_path = '';
                if (isset($option['root']))
                {
                    $root_path = $this->transfer_path(get_home_path() . $option['root']);
                }
                else if (isset($option['root_flag']))
                {
                    if ($option['root_flag'] == WPVIVID_BACKUP_ROOT_WP_CONTENT)
                    {
                        $root_path = $this->transfer_path(WP_CONTENT_DIR);
                    }
                    else if ($option['root_flag'] == WPVIVID_BACKUP_ROOT_CUSTOM)
                    {
                        $root_path = $this->transfer_path(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . WPvivid_Setting::get_backupdir());
                    }
                    else if ($option['root_flag'] == WPVIVID_BACKUP_ROOT_WP_ROOT)
                    {
                        $root_path = $this->transfer_path(ABSPATH);
                    }
                    else if($option['root_flag'] == WPVIVID_BACKUP_ROOT_WP_UPLOADS)
                    {
                        $upload_dir = wp_upload_dir();
                        $upload_path = $upload_dir['basedir'];

                        $root_path = $this->transfer_path($upload_path);
                    }
                    else if($option['root_flag'] == 'wpvivid_mu_upload')
                    {
                        if($option['overwrite'])
                        {
                            $upload_dir =$this->get_site_upload_dir($option['overwrite_site']);
                        }
                        else
                        {
                            $upload_dir =$this->get_site_upload_dir($option['site_id']);
                        }
                        $root_path=$upload_dir['basedir'];
                        $wpvivid_plugin->restore_data->write_log('restore root path:' .$root_path, 'notice');
                    }
                }

                $root_path = rtrim($root_path, '/');
                $root_path = rtrim($root_path, DIRECTORY_SEPARATOR);
                //$wpvivid_plugin->restore_data->write_log('root_path:' . $root_path.' root_flag:'.$option['root_flag'], 'notice');
                $exclude_path[] = $this->transfer_path(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . WPvivid_Setting::get_backupdir());

                if (isset($option['include_path'])) {
                    $include_path = $option['include_path'];
                } else {
                    $include_path = array();
                }

                $zip = new WPvivid_ZipClass();
                $all_files = array();
                foreach ($files as $file) {
                    $all_files[] = $local_path. $file;
                }

                $wpvivid_plugin->restore_data->write_log('restore from files:' . json_encode($all_files), 'notice');

                if (isset($option['wp_core']) && isset($option['is_migrate'])&&is_multisite())
                {
                    @rename(get_home_path() . '.htaccess', get_home_path() . '.htaccess_old');
                }
                $ret = $zip->extract($all_files, $root_path, $option);

                if (isset($option['file_type'])) {
                    if ($option['file_type'] == 'themes') {
                        if (isset($option['remove_themes'])) {
                            foreach ($option['remove_themes'] as $slug => $themes) {
                                if (empty($slug))
                                    continue;
                                $wpvivid_plugin->restore_data->write_log('remove ' . get_theme_root() . DIRECTORY_SEPARATOR . $slug, 'notice');
                                //$this->delTree(get_theme_root() . DIRECTORY_SEPARATOR . $slug);
                            }
                        }
                    } else if ($option['file_type'] == 'plugin') {
                        if (isset($option['remove_plugin'])) {
                            foreach ($option['remove_plugin'] as $slug => $plugin) {
                                if (empty($slug))
                                    continue;
                                $wpvivid_plugin->restore_data->write_log('remove ' . WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $slug, 'notice');
                                //$this->delTree(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $slug);
                            }
                        }
                    }
                }


                unset($zip);

                if (isset($option['wp_core']) && isset($option['is_migrate'])&&is_multisite())
                {
                    @rename(get_home_path() . '.htaccess_old', get_home_path() . '.htaccess');
                }
                if (isset($option['wp_core']) && isset($option['is_migrate']))
                {
                    if ($option['is_migrate'] == 1&&!is_multisite())
                    {
                        if (function_exists('save_mod_rewrite_rules'))
                        {
                            if (file_exists(get_home_path() . '.htaccess'))
                            {
                                $htaccess_data = file_get_contents(get_home_path() . '.htaccess');
                                $line = '';
                                if (preg_match('#AddHandler application/x-httpd-php.*#', $htaccess_data, $matcher))
                                {
                                    $line = PHP_EOL . $matcher[0];

                                    if (preg_match('#<IfModule mod_suphp.c>#', $htaccess_data, $matcher)) {
                                        $line .= PHP_EOL . '<IfModule mod_suphp.c>';
                                        if (preg_match('#suPHP_ConfigPath .*#', $htaccess_data, $matcher)) {
                                            $line .= PHP_EOL . $matcher[0];
                                        }
                                        $line .= PHP_EOL . '</IfModule>';
                                    }
                                    $wpvivid_plugin->restore_data->write_log('find php selector:' . $line, 'notice');
                                }
                                else if (preg_match('#AddHandler application/x-httpd-ea-php.*#', $htaccess_data, $matcher))
                                {
                                    $line_temp = PHP_EOL . $matcher[0];

                                    if (preg_match('#<IfModule mime_module>#', $htaccess_data, $matcher))
                                    {
                                        $line .= PHP_EOL . '<IfModule mime_module>';
                                        $line .= $line_temp.PHP_EOL;
                                        $line .= PHP_EOL . '</IfModule>';
                                    }
                                    $wpvivid_plugin->restore_data->write_log('find php selector:' . $line, 'notice');
                                }
                                @rename(get_home_path() . '.htaccess', get_home_path() . '.htaccess_old');
                                save_mod_rewrite_rules();
                                if (!empty($line))
                                    file_put_contents(get_home_path() . '.htaccess', $line, FILE_APPEND);
                            }
                            if(file_exists(get_home_path() . '.user.ini'))
                            {
                                @rename(get_home_path() . '.user.ini', get_home_path() . '.user.ini_old');
                                save_mod_rewrite_rules();
                            }
                        }
                        WPvivid_Setting::update_option('wpvivid_migrate_status', 'completed');
                    }
                }
            }
            return $ret;
        }
    }

    public function delTree($dir)
    {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file)
        {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    private function transfer_path($path)
    {
        $path = str_replace('\\','/',$path);
        $values = explode('/',$path);
        return implode(DIRECTORY_SEPARATOR,$values);
    }

    public function get_site_upload_dir($site_id, $time = null, $create_dir = true, $refresh_cache = false)
    {
        static $cache = array(), $tested_paths = array();

        $key = sprintf( '%d-%s',$site_id, (string) $time );

        if ( $refresh_cache || empty( $cache[ $key ] ) ) {
            $cache[ $key ] = $this->_wp_upload_dir( $site_id,$time );
        }

        /**
         * Filters the uploads directory data.
         *
         * @since 2.0.0
         *
         * @param array $uploads Array of upload directory data with keys of 'path',
         *                       'url', 'subdir, 'basedir', and 'error'.
         */
        $uploads = apply_filters( 'upload_dir', $cache[ $key ] );

        if ( $create_dir ) {
            $path = $uploads['path'];

            if ( array_key_exists( $path, $tested_paths ) ) {
                $uploads['error'] = $tested_paths[ $path ];
            } else {
                if ( ! wp_mkdir_p( $path ) ) {
                    if ( 0 === strpos( $uploads['basedir'], ABSPATH ) ) {
                        $error_path = str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir'];
                    } else {
                        $error_path = basename( $uploads['basedir'] ) . $uploads['subdir'];
                    }

                    $uploads['error'] = sprintf(
                    /* translators: %s: directory path */
                        __( 'Unable to create directory %s. Is its parent directory writable by the server?' ),
                        esc_html( $error_path )
                    );
                }

                $tested_paths[ $path ] = $uploads['error'];
            }
        }

        return $uploads;
    }

    public function _wp_upload_dir($site_id, $time = null ) {
        $siteurl     = get_option( 'siteurl' );
        $upload_path = trim( get_option( 'upload_path' ) );

        if ( empty( $upload_path ) || 'wp-content/uploads' == $upload_path ) {
            $dir = WP_CONTENT_DIR . '/uploads';
        } elseif ( 0 !== strpos( $upload_path, ABSPATH ) ) {
            // $dir is absolute, $upload_path is (maybe) relative to ABSPATH
            $dir = path_join( ABSPATH, $upload_path );
        } else {
            $dir = $upload_path;
        }

        if ( ! $url = get_option( 'upload_url_path' ) ) {
            if ( empty( $upload_path ) || ( 'wp-content/uploads' == $upload_path ) || ( $upload_path == $dir ) ) {
                $url = WP_CONTENT_URL . '/uploads';
            } else {
                $url = trailingslashit( $siteurl ) . $upload_path;
            }
        }

        /*
         * Honor the value of UPLOADS. This happens as long as ms-files rewriting is disabled.
         * We also sometimes obey UPLOADS when rewriting is enabled -- see the next block.
         */
        if ( defined( 'UPLOADS' ) && ! ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) ) {
            $dir = ABSPATH . UPLOADS;
            $url = trailingslashit( $siteurl ) . UPLOADS;
        }

        // If multisite (and if not the main site in a post-MU network)
        if ( is_multisite() && ! ( is_main_network() && is_main_site($site_id) && defined( 'MULTISITE' ) ) ) {
            if ( ! get_site_option( 'ms_files_rewriting' ) ) {
                /*
                 * If ms-files rewriting is disabled (networks created post-3.5), it is fairly
                 * straightforward: Append sites/%d if we're not on the main site (for post-MU
                 * networks). (The extra directory prevents a four-digit ID from conflicting with
                 * a year-based directory for the main site. But if a MU-era network has disabled
                 * ms-files rewriting manually, they don't need the extra directory, as they never
                 * had wp-content/uploads for the main site.)
                 */

                if ( defined( 'MULTISITE' ) ) {
                    $ms_dir = '/sites/' . $site_id;
                } else {
                    $ms_dir = '/' . $site_id;
                }

                $dir .= $ms_dir;
                $url .= $ms_dir;
            } elseif ( defined( 'UPLOADS' ) && ! ms_is_switched() ) {
                /*
                 * Handle the old-form ms-files.php rewriting if the network still has that enabled.
                 * When ms-files rewriting is enabled, then we only listen to UPLOADS when:
                 * 1) We are not on the main site in a post-MU network, as wp-content/uploads is used
                 *    there, and
                 * 2) We are not switched, as ms_upload_constants() hardcodes these constants to reflect
                 *    the original blog ID.
                 *
                 * Rather than UPLOADS, we actually use BLOGUPLOADDIR if it is set, as it is absolute.
                 * (And it will be set, see ms_upload_constants().) Otherwise, UPLOADS can be used, as
                 * as it is relative to ABSPATH. For the final piece: when UPLOADS is used with ms-files
                 * rewriting in multisite, the resulting URL is /files. (#WP22702 for background.)
                 */

                if ( defined( 'BLOGUPLOADDIR' ) ) {
                    $dir = untrailingslashit( BLOGUPLOADDIR );
                } else {
                    $dir = ABSPATH . UPLOADS;
                }
                $url = trailingslashit( $siteurl ) . 'files';
            }
        }

        $basedir = $dir;
        $baseurl = $url;

        $subdir = '';
        if ( get_option( 'uploads_use_yearmonth_folders' ) ) {
            // Generate the yearly and monthly dirs
            if ( ! $time ) {
                $time = current_time( 'mysql' );
            }
            $y      = substr( $time, 0, 4 );
            $m      = substr( $time, 5, 2 );
            $subdir = "/$y/$m";
        }

        $dir .= $subdir;
        $url .= $subdir;

        return array(
            'path'    => $dir,
            'url'     => $url,
            'subdir'  => $subdir,
            'basedir' => $basedir,
            'baseurl' => $baseurl,
            'error'   => false,
        );
    }
}