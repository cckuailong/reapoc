<?php
if (!defined('WPVIVID_PLUGIN_DIR'))
{
    die;
}

if ( ! class_exists( 'WP_List_Table' ) )
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPvivid_Staging_Free
{
    public $main_tab;
    public $end_shutdown_function;
    public $screen_ids;

    public $log;
    public $log_page;
    public $new_wp_page;
    public $ui_display;
    public $setting;

    public function __construct()
    {
        if(is_admin())
        {
            include_once WPVIVID_PLUGIN_DIR . '/includes/staging/class-wpvivid-staging-copy-db-ex.php';
            include_once WPVIVID_PLUGIN_DIR . '/includes/staging/class-wpvivid-staging-copy-files-ex.php';
            include_once WPVIVID_PLUGIN_DIR . '/includes/staging/class-wpvivid-staging-task-ex.php';
            include_once WPVIVID_PLUGIN_DIR . '/includes/staging/class-wpvivid-staging-log.php';
            include_once WPVIVID_PLUGIN_DIR . '/includes/staging/class-wpvivid-staging-log-page.php';
            include_once WPVIVID_PLUGIN_DIR . '/includes/staging/class-wpvivid-staging-create-new-wp.php';
            include_once WPVIVID_PLUGIN_DIR . '/includes/staging/class-wpvivid-staging-ui-display.php';
            include_once WPVIVID_PLUGIN_DIR . '/includes/staging/class-wpvivid-staging-setting.php';
            include_once WPVIVID_PLUGIN_DIR . '/includes/staging/class-wpvivid-staging-sites-list.php';

            $this->log=new WPvivid_Staging_Log_Free();
            $this->log_page=new WPvivid_Staging_Log_Page_Free();
            $this->ui_display=new WPvivid_Staging_UI_Display_Free();
            $this->setting=new WPvivid_Staging_Setting_Free();


            add_action('admin_enqueue_scripts',array( $this,'enqueue_styles'));
            add_action('admin_enqueue_scripts',array( $this,'enqueue_scripts'));

            add_filter('wpvivid_add_side_bar', array($this, 'add_side_bar'), 11, 2);

            add_filter('wpvivid_get_toolbar_menus',array($this,'get_toolbar_menus'),22);
            add_filter('wpvivid_get_admin_menus',array($this,'get_admin_menus'),22);
            add_filter('wpvivid_get_screen_ids',array($this,'get_screen_ids'),12);

            $this->load_ajax();
        }

        add_filter('wpvividstg_get_admin_url',array($this,'get_admin_url'),10);
        add_filter('wpvivid_add_staging_side_bar', array($this, 'wpvivid_add_staging_side_bar'), 11, 2);

        add_action( "init",array($this,'staging_site'));
    }

    public function add_side_bar($html, $show_schedule = false)
    {
        if(get_current_screen()->id=='wpvivid-backup_page_wpvivid-staging')
        {
            $wpvivid_version = WPVIVID_PLUGIN_VERSION;
            $wpvivid_version = apply_filters('wpvivid_display_pro_version', $wpvivid_version);

            $html = '<div class="postbox">
                <h2>
                    <div style="float: left; margin-right: 5px;"><span style="margin: 0; padding: 0">'.__('Current Version:', 'wpvivid-backuprestore').' '.$wpvivid_version.'</span></div>
                    <div style="float: left; margin-right: 5px;"><span style="margin: 0; padding: 0">|</span></div>
                    <div style="float: left; margin-left: 0;">
                        <span style="margin: 0; padding: 0"><a href="https://wordpress.org/plugins/wpvivid-backuprestore/#developers" target="_blank" style="text-decoration: none;">'.__('ChangeLog', 'wpvivid-backuprestore').'</a></span>
                    </div>
                    <div style="clear: both;"></div>
                </h2>
             </div>
             <div id="wpvivid_backup_schedule_part"></div>
             <div class="postbox">
                <h2><span>'.__('Troubleshooting', 'wpvivid-backuprestore').'</span></h2>
                <div class="inside">
                    <table class="widefat" cellpadding="0">
                        <tbody>
                        <tr class="alternate">
                            <td class="row-title">'.__('Read <a href="https://docs.wpvivid.com/troubleshooting-issues-wpvivid-backup-plugin.html" target="_blank">Troubleshooting page</a> for faster solutions.', 'wpvivid-backuprestore').'</td>
                        </tr>             
                        </tbody>
                    </table>
                </div>
             </div>
             <div class="postbox">
                <h2><span>'.__('How-to', 'wpvivid-backuprestore').'</span></h2>
                <div class="inside">
                    <table class="widefat" cellpadding="0">
                        <tbody>
                            <tr class="alternate"><td class="row-title"><a href="https://docs.wpvivid.com/wpvivid-backup-pro-create-staging-site.html" target="_blank">'.__('Create A Staging Site', 'wpvivid-backuprestore').'</a></td></tr>
                            <tr><td class="row-title"><a href="https://docs.wpvivid.com/wpvivid-staging-pro-create-fresh-install.html" target="_blank">'.__('Create A Fresh WordPress Install', 'wpvivid-backuprestore').'</a></td></tr>
                        </tbody>
                    </table>
                </div>
             </div>';
        }

        return $html;
    }

    public function get_admin_url($admin_url)
    {
        if(is_multisite())
        {
            $admin_url = network_admin_url();
        }
        else
        {
            $admin_url =admin_url();
        }

        return $admin_url;
    }

    public function wpvivid_add_staging_side_bar($html, $show_schedule)
    {
        $html = '<h2 style="margin-top:0.5em;">
                     <span class="dashicons dashicons-sticky wpvivid-dashicons-orange"></span>
                     <span>Troubleshooting</span>
                 </h2>
                 <div class="inside" style="padding-top:0;">
                     <ul class="" >
                        <li style="border-top:1px solid #f1f1f1;"><span class="dashicons dashicons-editor-help wpvivid-dashicons-orange" ></span>
                            <a href="https://docs.wpvivid.com/troubleshooting-issues-wpvivid-staging-pro.html"><b>Troubleshooting</b></a>
                            <small><span style="float: right;"><a href="https://wpvivid.com/troubleshooting-issues-wpvivid-staging-pro" style="text-decoration: none;" target="_blank"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                        </li>
                     </ul>
                 </div>
                 
                 <h2>
                     <span class="dashicons dashicons-book-alt wpvivid-dashicons-orange" ></span>
                     <span>Documentation</span>
                 </h2>
                 <div class="inside" style="padding-top:0;">
                     <ul class="">
                        <li style="border-top:1px solid #f1f1f1;"><span class="dashicons dashicons-migrate wpvivid-dashicons-blue"></span>
                            <a href="https://docs.wpvivid.com/wpvivid-backup-pro-create-staging-site.html"><b>Create A Staging Site</b></a>
                            <small><span style="float: right;"><a href="https://wpvivid.com/wpvivid-backup-pro-create-staging-site" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                        </li>
                        <li><span class="dashicons dashicons-migrate wpvivid-dashicons-blue"></span>
                            <a href="https://docs.wpvivid.com/wpvivid-staging-pro-publish-staging-to-live.html"><b>Publish A Staging Site</b></a>
                            <small><span style="float: right;"><a href="https://wpvivid.com/wpvivid-backup-pro-publish-staging-to-live" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                        </li>
                        <li><span class="dashicons dashicons-migrate wpvivid-dashicons-blue"></span>
                            <a href="https://docs.wpvivid.com/wpvivid-staging-pro-create-staging-site-for-wordpress-multisite.html"><b>Create A MU Staging</b></a>
                            <small><span style="float: right;"><a href="https://wpvivid.com/wpvivid-staging-pro-create-staging-site-for-wordpress-multisite" style="text-decoration: none;"><span class="dashicons dashicons-migrate wpvivid-dashicons-grey"></span></a></span></small><br>
                        </li>
                     </ul>
                 </div>';
        return $html;
    }

    public function load_ajax()
    {
        add_action('wp_ajax_wpvividstg_start_staging_free', array($this, 'start_staging'));
        add_action('wp_ajax_nopriv_wpvividstg_start_staging_free', array($this, 'start_staging'));
        add_action('wp_ajax_wpvividstg_set_restart_staging_id_free', array($this, 'set_restart_staging_id'));
        add_action('wp_ajax_wpvividstg_get_staging_progress_free', array($this, 'get_staging_progress'));
        add_action('wp_ajax_nopriv_wpvividstg_get_staging_progress_free', array($this, 'get_staging_progress'));
        add_action('wp_ajax_wpvividstg_delete_site_free', array($this, 'delete_site'));
        add_action('wp_ajax_wpvividstg_delete_cancel_staging_site_free', array($this, 'delete_cancel_staging_site'));
        add_action('wp_ajax_wpvividstg_check_staging_dir_free', array($this, 'check_staging_dir'));
        add_action('wp_ajax_wpvividstg_check_filesystem_permissions_free', array($this, 'check_filesystem_permissions'));
        //
        add_action('wp_ajax_wpvividstg_get_custom_database_tables_info_free',array($this, 'get_custom_database_tables_info'));

        add_action('wp_ajax_wpvividstg_cancel_staging_free', array($this, 'cancel_staging'));
        add_action('wp_ajax_wpvividstg_test_additional_database_connect_free', array($this, 'test_additional_database_connect'));
        add_action('wp_ajax_wpvividstg_update_staging_exclude_extension_free', array($this, 'update_staging_exclude_extension'));
        //

        add_action('wp_ajax_wpvividstg_get_custom_database_size_free', array($this, 'get_custom_database_size'));
        add_action('wp_ajax_wpvividstg_get_custom_files_size_free', array($this, 'get_custom_files_size'));
        add_action('wp_ajax_wpvividstg_get_custom_include_path_free', array($this, 'get_custom_include_path'));
        add_action('wp_ajax_wpvividstg_get_custom_exclude_path_free', array($this, 'get_custom_exclude_path'));
        add_action('wp_ajax_wpvividstg_get_custom_themes_plugins_info_free', array($this, 'get_custom_themes_plugins_info_ex'));
    }

    public function enqueue_styles()
    {
        $this->screen_ids=apply_filters('wpvivid_get_screen_ids',$this->screen_ids);
        if(get_current_screen()->id=='wpvivid-backup_page_wpvivid-staging')
        {
            wp_enqueue_style(WPVIVID_PLUGIN_SLUG.'jstree', WPVIVID_PLUGIN_DIR_URL . 'js/jstree/dist/themes/default/style.min.css', array(), WPVIVID_PLUGIN_VERSION, 'all');
            wp_enqueue_style(WPVIVID_PLUGIN_SLUG.'staging', WPVIVID_PLUGIN_DIR_URL . 'css/wpvivid-staging-custom.css', array(), WPVIVID_PLUGIN_VERSION, 'all');
        }
    }

    public function enqueue_scripts()
    {
        $this->screen_ids=apply_filters('wpvivid_get_screen_ids',$this->screen_ids);
        if(in_array(get_current_screen()->id,$this->screen_ids))
        {
            wp_enqueue_script(WPVIVID_PLUGIN_SLUG.'jstree', WPVIVID_PLUGIN_DIR_URL . 'js/jstree/dist/jstree.min.js', array('jquery'), WPVIVID_PLUGIN_VERSION, false);
            wp_enqueue_script('plupload-all');
        }
    }

    public function get_screen_ids($screen_ids)
    {
        $screen_ids[]=apply_filters('wpvivid_white_label_screen_id', 'wpvivid-backup_page_wpvivid-staging');
        return $screen_ids;
    }

    public function get_toolbar_menus($toolbar_menus)
    {
        $admin_url = apply_filters('wpvivid_get_admin_url', '');

        $menu['id']='wpvivid_admin_menu_staging';
        $menu['parent']='wpvivid_admin_menu';
        $menu['title']=__('Staging', 'wpvivid-backuprestore');
        $menu['tab']= 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-staging');
        $menu['href']=$admin_url . 'admin.php?page='.apply_filters('wpvivid_white_label_plugin_name', 'wpvivid-staging');
        $menu['capability']='administrator';
        $menu['index']=2;
        $toolbar_menus[$menu['parent']]['child'][$menu['id']]=$menu;
        return $toolbar_menus;
    }

    public function get_admin_menus($submenus)
    {
        $submenu['parent_slug']=apply_filters('wpvivid_white_label_slug', WPVIVID_PLUGIN_SLUG);
        $submenu['page_title']= apply_filters('wpvivid_white_label_display', 'WPvivid Backup');
        $submenu['menu_title']=__('Staging', 'wpvivid-backuprestore');
        $submenu['capability']='administrator';
        $submenu['menu_slug']=strtolower(sprintf('%s-staging', apply_filters('wpvivid_white_label_slug', 'wpvivid')));
        $submenu['index']=2;
        $submenu['function']=array($this, 'display_plugin_setup_page');
        $submenus[$submenu['menu_slug']]=$submenu;
        return $submenus;
    }

    public function display_plugin_setup_page()
    {
        ?>
        <?php
        $this->ui_display->init_page();
        ?>
        <?php
    }

    public function get_database_site_url()
    {
        $site_url = site_url();
        global $wpdb;
        $site_url_sql = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name = %s", 'siteurl' ) );
        foreach ( $site_url_sql as $site ){
            $site_url = $site->option_value;
        }
        return untrailingslashit($site_url);
    }

    public function get_database_home_url()
    {
        $home_url = home_url();
        global $wpdb;
        $home_url_sql = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->options WHERE option_name = %s", 'home' ) );
        foreach ( $home_url_sql as $home ){
            $home_url = $home->option_value;
        }
        return untrailingslashit($home_url);
    }

    public function get_custom_database_size(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            $ret['result']='success';

            global $wpdb;
            $tables = $wpdb->get_results('SHOW TABLE STATUS', ARRAY_A);
            if (is_null($tables)) {
                $ret['result'] = 'failed';
                $ret['error'] = 'Failed to retrieve the table information for the database. Please try again.';
                return $ret;
            }

            $db_size = 0;

            $base_table_size = 0;
            foreach ($tables as $row) {
                $base_table_size += ($row["Data_length"] + $row["Index_length"]);
            }

            $db_size = size_format($base_table_size, 2);

            $ret['database_size'] = $db_size;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public static function get_custom_path_size($type, $path, $size=0){
        if(!function_exists('get_home_path'))
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        $home_path = str_replace('\\','/', get_home_path());
        $core_file_arr = array('.htaccess', 'index', 'license.txt', 'readme.html', 'wp-activate.php', 'wp-blog-header.php', 'wp-comments-post.php', 'wp-config.php', 'wp-config-sample.php',
            'wp-cron.php', 'wp-links-opml.php', 'wp-load.php', 'wp-login.php', 'wp-mail.php', 'wp-settings.php', 'wp-signup.php', 'wp-trackback.php', 'xmlrpc.php');
        if(is_dir($path))
        {
            $handler = opendir($path);
            if($handler!==false)
            {
                while (($filename = readdir($handler)) !== false)
                {
                    if ($filename != "." && $filename != "..") {
                        if (is_dir($path . DIRECTORY_SEPARATOR . $filename))
                        {
                            if($type === 'content'){
                                if($filename !== 'plugins' && $filename !== 'themes' && $filename !== 'uploads'){
                                    $size=self::get_custom_path_size($type, $path . DIRECTORY_SEPARATOR . $filename, $size);
                                }
                            }
                            else if($type === 'core' && $home_path === $path){
                                if($filename === 'wp-admin' || $filename === 'wp-includes'){
                                    $size=self::get_custom_path_size($type, $path . DIRECTORY_SEPARATOR . $filename, $size);
                                }
                            }
                            else if($type === 'additional'){
                                if($filename !== 'wp-admin' && $filename !== 'wp-content' && $filename !== 'wp-includes'){
                                    $size=self::get_custom_path_size($type, $path . DIRECTORY_SEPARATOR . $filename, $size);
                                }
                            }
                            else{
                                $size=self::get_custom_path_size($type, $path . DIRECTORY_SEPARATOR . $filename, $size);
                            }
                        } else {
                            if($type === 'core'){
                                if($home_path === $path){
                                    if(in_array($filename, $core_file_arr)){
                                        $size+=filesize($path . DIRECTORY_SEPARATOR . $filename);
                                    }
                                }
                                else{
                                    $size+=filesize($path . DIRECTORY_SEPARATOR . $filename);
                                }
                            }
                            else if($type === 'additional'){
                                if($home_path === $path){
                                    if(!in_array($filename, $core_file_arr)){
                                        $size+=filesize($path . DIRECTORY_SEPARATOR . $filename);
                                    }
                                }
                                else{
                                    $size+=filesize($path . DIRECTORY_SEPARATOR . $filename);
                                }
                            }
                            else{
                                $size+=filesize($path . DIRECTORY_SEPARATOR . $filename);
                            }
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }

        }
        return $size;
    }

    public function get_custom_files_size(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            $upload_dir = wp_upload_dir();
            $path = $upload_dir['basedir'];
            $path = str_replace('\\','/',$path);
            $uploads_path = $path.'/';

            $content_dir = WP_CONTENT_DIR;
            $path = str_replace('\\','/',$content_dir);
            $content_path = $path.'/';

            if(!function_exists('get_home_path'))
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            $home_path = str_replace('\\','/', get_home_path());

            $themes_path = str_replace('\\','/', get_theme_root());
            $themes_path = $themes_path.'/';

            $plugins_path = str_replace('\\','/', WP_PLUGIN_DIR);
            $plugins_path = $plugins_path.'/';

            $ret['result']='success';
            $core_size = self::get_custom_path_size('core', $home_path);
            $themes_size = self::get_custom_path_size('themes', $themes_path);
            $plugins_size = self::get_custom_path_size('plugins', $plugins_path);
            $uploads_size = self::get_custom_path_size('uploads', $uploads_path);
            $content_size = self::get_custom_path_size('content', $content_path);
            $additional_size = self::get_custom_path_size('additional', $home_path);
            $ret['core_size'] = size_format($core_size, 2);
            $ret['themes_size'] = size_format($themes_size, 2);
            $ret['plugins_size'] = size_format($plugins_size, 2);
            $ret['uploads_size'] = size_format($uploads_size, 2);
            $ret['content_size'] = size_format($content_size, 2);
            $ret['additional_size'] = size_format($additional_size, 2);
            $ret['total_file_size'] = size_format($core_size+$themes_size+$plugins_size+$uploads_size+$content_size+$additional_size, 2);
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }
    
    public function get_custom_include_path(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if (isset($_POST['is_staging'])) {
                $is_staging = $_POST['is_staging'];

                $node_array = array();

                if ($_POST['tree_node']['node']['id'] == '#') {
                    $path = ABSPATH;

                    if (!empty($_POST['tree_node']['path'])) {
                        $path = $_POST['tree_node']['path'];
                    }

                    if (isset($_POST['select_prev_dir']) && $_POST['select_prev_dir'] === '1') {
                        $path = dirname($path);
                    }

                    $node_array[] = array(
                        'text' => basename($path),
                        'children' => true,
                        'id' => $path,
                        'icon' => 'dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer',
                        'state' => array(
                            'opened' => true
                        )
                    );
                } else {
                    $path = $_POST['tree_node']['node']['id'];
                }

                if (file_exists($path)) {
                    $path = trailingslashit(str_replace('\\', '/', realpath($path)));

                    if ($dh = opendir($path)) {
                        while (substr($path, -1) == '/') {
                            $path = rtrim($path, '/');
                        }

                        $skip_paths = array(".", "..");

                        $file_array = array();

                        while (($value = readdir($dh)) !== false) {
                            trailingslashit(str_replace('\\', '/', $value));

                            if (!in_array($value, $skip_paths)) {
                                if (is_dir($path . '/' . $value)) {
                                    $wp_admin_path = $is_staging == false ? ABSPATH . 'wp-admin' : $path . '/wp-admin';
                                    $wp_admin_path = str_replace('\\', '/', $wp_admin_path);

                                    $wp_include_path = $is_staging == false ? ABSPATH . 'wp-includes' : $path . '/wp-includes';
                                    $wp_include_path = str_replace('\\', '/', $wp_include_path);

                                    $content_dir = $is_staging == false ? WP_CONTENT_DIR : $path . '/wp-content';
                                    $content_dir = str_replace('\\', '/', $content_dir);
                                    $content_dir = rtrim($content_dir, '/');

                                    $exclude_dir = array($wp_admin_path, $wp_include_path, $content_dir);
                                    if (!in_array($path . '/' . $value, $exclude_dir)) {
                                        $node_array[] = array(
                                            'text' => $value,
                                            'children' => true,
                                            'id' => $path . '/' . $value,
                                            'icon' => 'dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer'
                                        );
                                    }

                                } else {
                                    $wp_admin_path = $is_staging == false ? ABSPATH : $path;
                                    $wp_admin_path = str_replace('\\', '/', $wp_admin_path);
                                    $wp_admin_path = rtrim($wp_admin_path, '/');
                                    $skip_path = rtrim($path, '/');

                                    if ($wp_admin_path == $skip_path) {
                                        continue;
                                    }
                                    $file_array[] = array(
                                        'text' => $value,
                                        'children' => false,
                                        'id' => $path . '/' . $value,
                                        'type' => 'file',
                                        'icon' => 'dashicons dashicons-media-default wpvivid-dashicons-grey wpvivid-icon-16px-nopointer'
                                    );
                                }
                            }
                        }
                        $node_array = array_merge($node_array, $file_array);
                    }
                } else {
                    $node_array = array();
                }

                $ret['nodes'] = $node_array;
                echo json_encode($ret);
                die();
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function get_custom_exclude_path(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try{
            if (isset($_POST['is_staging'])) {
                $is_staging = $_POST['is_staging'];
                $node_array = array();

                if ($_POST['tree_node']['node']['id'] == '#') {
                    $path = ABSPATH;

                    if (!empty($_POST['tree_node']['path'])) {
                        $path = $_POST['tree_node']['path'];
                    }

                    $node_array[] = array(
                        'text' => basename($path),
                        'children' => true,
                        'id' => $path,
                        'icon' => 'dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer',
                        'state' => array(
                            'opened' => true
                        )
                    );
                } else {
                    $path = $_POST['tree_node']['node']['id'];
                }

                if (file_exists($path)) {
                    $path = trailingslashit(str_replace('\\', '/', realpath($path)));

                    if ($dh = opendir($path)) {
                        while (substr($path, -1) == '/') {
                            $path = rtrim($path, '/');
                        }
                        $skip_paths = array(".", "..");

                        while (($value = readdir($dh)) !== false) {
                            trailingslashit(str_replace('\\', '/', $value));
                            if (!in_array($value, $skip_paths)) {
                                //
                                $custom_dir = $is_staging == false ? WP_CONTENT_DIR . '/' . WPVIVID_STAGING_DIR : $path . '/' . WPVIVID_STAGING_DIR;
                                $custom_dir = str_replace('\\', '/', $custom_dir);

                                $themes_dir = $is_staging == false ? get_theme_root() : $path . '/themes';
                                $themes_dir = trailingslashit(str_replace('\\', '/', $themes_dir));
                                $themes_dir = rtrim($themes_dir, '/');

                                $plugin_dir = $is_staging == false ? WP_PLUGIN_DIR : $path . '/plugins';
                                $plugin_dir = trailingslashit(str_replace('\\', '/', $plugin_dir));
                                $plugin_dir = rtrim($plugin_dir, '/');

                                if ($is_staging == false) {
                                    $upload_path = wp_upload_dir();
                                    $upload_path['basedir'] = trailingslashit(str_replace('\\', '/', $upload_path['basedir']));
                                    $upload_dir = rtrim($upload_path['basedir'], '/');
                                    $subsite_dir = rtrim($upload_path['basedir'], '/') . '/' . 'sites';
                                } else {
                                    $upload_dir = $path . '/uploads';
                                    $subsite_dir = $path . '/sites';
                                }
                                $exclude_dir = array($themes_dir, $plugin_dir, $upload_dir, $custom_dir, $subsite_dir);
                                if (is_dir($path . '/' . $value)) {
                                    if (!in_array($path . '/' . $value, $exclude_dir)) {
                                        $node['text'] = $value;
                                        $node['children'] = true;
                                        $node['id'] = $path . '/' . $value;
                                        $node['icon'] = 'dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer';
                                        $node_array[] = $node;
                                    }
                                }
                                else{
                                    $node['text'] = $value;
                                    $node['children'] = true;
                                    $node['id'] = $path . '/' . $value;
                                    $node['icon'] = 'dashicons dashicons-media-default wpvivid-dashicons-grey wpvivid-icon-16px-nopointer';
                                    $node_array[] = $node;
                                }
                            }
                        }
                    }
                }
                else {
                    $node_array = array();
                }

                $ret['nodes'] = $node_array;
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function get_custom_themes_plugins_info_ex(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try{
            if (isset($_POST['is_staging']) && !empty($_POST['is_staging']))
            {
                if ($_POST['is_staging'] == '1')
                {
                    $is_staging_site = true;
                    $staging_site_id = $_POST['id'];

                    $task = new WPvivid_Staging_Task($staging_site_id);
                    $ret = $this->get_staging_directory_info($task->get_site_path());
                } else {
                    $is_staging_site = false;
                }
            } else {
                $is_staging_site = false;
            }

            if ($is_staging_site)
            {
                $staging_option = array();
            } else {
                //$staging_option = self::wpvivid_get_staging_history();
                //if (empty($staging_option))
                //{
                $staging_option = array();
                //}
            }

            $themes_path = $is_staging_site == false ? get_theme_root() : $_POST['staging_path'] . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'themes';

            $exclude_themes_list = '';

            $themes_info = array();

            $themes = $is_staging_site == false ? wp_get_themes() : $ret['themes_list'];

            foreach ($themes as $theme)
            {
                $file = $theme->get_stylesheet();
                $themes_info[$file] = $this->get_theme_plugin_info($themes_path . DIRECTORY_SEPARATOR . $file);
                $parent=$theme->parent();
                $themes_info[$file]['parent']=$parent;
                $themes_info[$file]['parent_file']=$theme->get_template();
                $themes_info[$file]['child']=array();

                if(isset($_POST['subsite']))
                {
                    switch_to_blog($_POST['subsite']);
                    $ct = wp_get_theme();
                    if( $ct->get_stylesheet()==$file)
                    {
                        $themes_info[$file]['active'] = 1;
                    }
                    else
                    {
                        $themes_info[$file]['active'] = 0;
                    }
                    restore_current_blog();
                }
                else
                {
                    $themes_info[$file]['active'] = 1;
                }
            }

            foreach ($themes_info as $file => $info)
            {
                if($info['active']&&$info['parent']!=false)
                {
                    $themes_info[$info['parent_file']]['active']=1;
                    $themes_info[$info['parent_file']]['child'][]=$file;
                }
            }

            foreach ($themes_info as $file => $info) {
                if ($info['active'] == 1) {

                }
                else{
                    $exclude_themes_list .= '<div class="wpvivid-text-line" type="folder">
                                                <span class="dashicons dashicons-trash wpvivid-icon-16px wpvivid-remove-custom-exlcude-tree"></span><span class="dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer"></span><span class="wpvivid-text-line">'.$file.'</span>
                                              </div>';
                }
                /*if (!empty($staging_option['themes_list'])) {
                    if (in_array($file, $staging_option['themes_list'])) {
                        $checked = '';
                    }
                }*/
            }

            $exclude_plugin_list = '';
            $path = $is_staging_site == false ? WP_PLUGIN_DIR : $_POST['staging_path'] . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins';
            $plugin_info = array();

            if (!function_exists('get_plugins'))
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            $plugins = $is_staging_site == false ? get_plugins() : $ret['plugins_list'];

            if(isset($_POST['subsite']))
            {
                switch_to_blog($_POST['subsite']);
                $current   = get_option( 'active_plugins', array() );
                restore_current_blog();
            }
            else
            {
                $current   = get_option( 'active_plugins', array() );
            }


            foreach ($plugins as $key => $plugin)
            {
                $slug = dirname($key);
                if ($slug == '.')
                    continue;
                $plugin_info[$slug] = $this->get_theme_plugin_info($path . DIRECTORY_SEPARATOR . $slug);
                $plugin_info[$slug]['Name'] = $plugin['Name'];
                $plugin_info[$slug]['slug'] = $slug;

                if(isset($_POST['subsite']))
                {
                    if(in_array($key,$current))
                    {
                        $plugin_info[$slug]['active'] = 1;
                    }
                    else
                    {
                        $plugin_info[$slug]['active'] = 0;
                    }
                }
                else
                {
                    $plugin_info[$slug]['active'] = 1;
                }
            }

            foreach ($plugin_info as $slug => $info) {
                if ($info['active'] == 1) {

                }
                else{
                    $exclude_plugin_list .= '<div class="wpvivid-text-line" type="folder">
                                                <span class="dashicons dashicons-trash wpvivid-icon-16px wpvivid-remove-custom-exlcude-tree"></span><span class="dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer"></span><span class="wpvivid-text-line">'.$slug.'</span>
                                              </div>';
                }
                /*if (!empty($staging_option['plugins_list'])) {
                    if (in_array($slug, $staging_option['plugins_list'])) {
                        $checked = '';
                    }
                }*/
            }
            $ret['result'] = 'success';
            $ret['theme_list'] = $exclude_themes_list;
            $ret['plugin_list'] .= $exclude_plugin_list;
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function get_staging_site_data()
    {
        if(is_multisite())
        {
            switch_to_blog(get_main_network_id());
            $staging=get_option('wpvivid_staging_data',false);
            restore_current_blog();
        }
        else
        {
            $staging=get_option('wpvivid_staging_data',false);
        }

        return $staging;
    }

    public function get_custom_database_tables_info()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            global $wpdb;
            $db = array();
            $use_additional_db = false;
            $staging_site_id = $_POST['id'];
            if(empty($_POST['id']))
            {
                $get_site_mu_single=false;
            }
            else
            {
                $task = new WPvivid_Staging_Task($staging_site_id);
                $site_id=$task->get_site_mu_single_site_id();
                $get_site_mu_single=$task->get_site_mu_single();
            }


            if (isset($_POST['is_staging']) && !empty($_POST['is_staging']) && is_string($_POST['is_staging'])&&$_POST['is_staging'] == '1')
            {
                $base_prefix = $task->get_site_prefix();
            }
            else
            {
                $base_prefix=$wpdb->base_prefix;
            }

            if (isset($_POST['is_staging']) && !empty($_POST['is_staging']) && is_string($_POST['is_staging']))
            {
                if ($_POST['is_staging'] == '1')
                {
                    $is_staging_site = true;

                    $prefix = $task->get_site_prefix();

                    $db = $task->get_site_db_connect();
                    if ($db['use_additional_db'] !== false)
                    {
                        $use_additional_db = true;
                    } else {
                        $use_additional_db = false;
                    }
                } else {
                    $is_staging_site = false;
                    $prefix = $wpdb->get_blog_prefix(0);
                }
            } else {
                $is_staging_site = false;
                $prefix = $wpdb->get_blog_prefix(0);
            }

            $ret['result'] = 'success';
            $ret['html'] = '';
            if (empty($prefix)) {
                echo json_encode($ret);
                die();
            }

            $base_table = '';
            $woo_table = '';
            $other_table = '';
            $default_table = array($prefix . 'commentmeta', $prefix . 'comments', $prefix . 'links', $prefix . 'options', $prefix . 'postmeta', $prefix . 'posts', $prefix . 'term_relationships',
                $prefix . 'term_taxonomy', $prefix . 'termmeta', $prefix . 'terms', $prefix . 'usermeta', $prefix . 'users');
            $woo_table_arr = array($prefix.'actionscheduler_actions', $prefix.'actionscheduler_claims', $prefix.'actionscheduler_groups', $prefix.'actionscheduler_logs', $prefix.'aelia_dismissed_messages',
                $prefix.'aelia_exchange_rates_history', $prefix.'automatewoo_abandoned_carts', $prefix.'automatewoo_customer_meta', $prefix.'automatewoo_customers', $prefix.'automatewoo_events',
                $prefix.'automatewoo_guest_meta', $prefix.'automatewoo_guests', $prefix.'automatewoo_log_meta', $prefix.'automatewoo_logs', $prefix.'automatewoo_queue', $prefix.'automatewoo_queue_meta',
                $prefix.'automatewoo_unsubscribes', $prefix.'wc_admin_note_actions', $prefix.'wc_admin_notes', $prefix.'wc_am_api_activation', $prefix.'wc_am_api_resource', $prefix.'wc_am_associated_api_key',
                $prefix.'wc_am_secure_hash', $prefix.'wc_category_lookup', $prefix.'wc_customer_lookup', $prefix.'wc_download_log', $prefix.'wc_order_coupon_lookup', $prefix.'wc_order_product_lookup',
                $prefix.'wc_order_stats', $prefix.'wc_order_tax_lookup', $prefix.'wc_product_meta_lookup', $prefix.'wc_reserved_stock', $prefix.'wc_tax_rate_classes', $prefix.'wc_webhooks',
                $prefix.'woocommerce_api_keys', $prefix.'woocommerce_attribute_taxonomies', $prefix.'woocommerce_downloadable_product_permissions', $prefix.'woocommerce_log', $prefix.'woocommerce_order_itemmeta',
                $prefix.'woocommerce_order_items', $prefix.'woocommerce_payment_tokenmeta', $prefix.'woocommerce_payment_tokens', $prefix.'woocommerce_sessions', $prefix.'woocommerce_shipping_zone_locations',
                $prefix.'woocommerce_shipping_zone_methods', $prefix.'woocommerce_shipping_zones', $prefix.'woocommerce_tax_rate_locations', $prefix.'woocommerce_tax_rates');

            if ($is_staging_site) {
                $staging_option = self::wpvivid_get_push_staging_history();
                if (empty($staging_option)) {
                    $staging_option = array();
                }
                if ($use_additional_db) {
                    $handle = new wpdb($db['dbuser'], $db['dbpassword'], $db['dbname'], $db['dbhost']);
                    $tables = $handle->get_results('SHOW TABLE STATUS', ARRAY_A);
                } else {
                    $tables = $wpdb->get_results('SHOW TABLE STATUS', ARRAY_A);
                }
            } else {
                $staging_option = self::wpvivid_get_staging_history();
                if (empty($staging_option)) {
                    $staging_option = array();
                }
                $tables = $wpdb->get_results('SHOW TABLE STATUS', ARRAY_A);
            }

            if (is_null($tables)) {
                $ret['result'] = 'failed';
                $ret['error'] = 'Failed to retrieve the table information for the database. Please try again.';
                echo json_encode($ret);
                die();
            }

            $tables_info = array();
            $has_base_table = false;
            $has_woo_table = false;
            $has_other_table = false;
            $base_count = 0;
            $woo_count = 0;
            $other_count = 0;
            $base_table_all_check = true;
            $woo_table_all_check = true;
            $other_table_all_check = true;
            foreach ($tables as $row)
            {
                if (preg_match('/^(?!' . $base_prefix . ')/', $row["Name"]) == 1)
                {
                    continue;
                }

                if($get_site_mu_single)
                {
                    $site_id=$task->get_site_mu_single_site_id();

                    if(!is_main_site($site_id))
                    {
                        if ( 1 == preg_match('/^' . $prefix . '/', $row["Name"]) )
                        {
                        }
                        else if ( 1 == preg_match('/^' . $base_prefix . '\d+_/', $row["Name"]) )
                        {
                            continue;
                        }
                        else
                        {
                            if($row["Name"]==$base_prefix.'users'||$row["Name"]==$base_prefix.'usermeta')
                            {

                            }
                            else
                            {
                                continue;
                            }
                        }
                    }
                    else
                    {
                        if ( 1 == preg_match('/^' . $base_prefix . '\d+_/', $row["Name"]) )
                        {
                            continue;
                        }
                        else
                        {
                            if($row["Name"]==$base_prefix.'blogs')
                                continue;
                            if($row["Name"]==$base_prefix.'blogmeta')
                                continue;
                            if($row["Name"]==$base_prefix.'sitemeta')
                                continue;
                            if($row["Name"]==$base_prefix.'site')
                                continue;
                        }
                    }
                }


                $tables_info[$row["Name"]]["Rows"] = $row["Rows"];
                $tables_info[$row["Name"]]["Data_length"] = size_format($row["Data_length"] + $row["Index_length"], 2);

                $checked = 'checked';
                if (!empty($staging_option['database_list'])) {
                    if ($is_staging_site) {
                        $tmp_row = $row["Name"];

                        $tmp_row = str_replace($base_prefix, $wpdb->base_prefix, $tmp_row);
                        if (in_array($tmp_row, $staging_option['database_list'])) {
                            $checked = '';
                        }
                    }
                    else if (in_array($row["Name"], $staging_option['database_list'])) {
                        $checked = '';
                    }
                }

                if (in_array($row["Name"], $default_table)) {
                    if ($checked == '') {
                        $base_table_all_check = false;
                    }
                    $has_base_table = true;

                    $base_table .= '<div class="wpvivid-text-line">
                                        <input type="checkbox" option="base_db" name="Database" value="'.esc_html($row["Name"]).'" '.esc_html($checked).' />
                                        <span class="wpvivid-text-line">'.esc_html($row["Name"]).'|Rows:'.$row["Rows"].'|Size:'.$tables_info[$row["Name"]]["Data_length"].'</span>
                                    </div>';
                    $base_count++;
                } else if(in_array($row['Name'], $woo_table_arr)){
                    if ($checked == '') {
                        $woo_table_all_check = false;
                    }
                    $has_woo_table = true;

                    $woo_table .= '<div class="wpvivid-text-line">
                                        <input type="checkbox" option="woo_db" name="Database" value="'.esc_html($row["Name"]).'" '.esc_html($checked).' />
                                        <span class="wpvivid-text-line">'.esc_html($row["Name"]).'|Rows:'.$row["Rows"].'|Size:'.$tables_info[$row["Name"]]["Data_length"].'</span>
                                   </div>';
                    $woo_count++;
                }
                else {
                    if ($checked == '') {
                        $other_table_all_check = false;
                    }
                    $has_other_table = true;

                    $other_table .= '<div class="wpvivid-text-line">
                                        <input type="checkbox" option="other_db" name="Database" value="'.esc_html($row["Name"]).'" '.esc_html($checked).' />
                                        <span class="wpvivid-text-line">'.esc_html($row["Name"]).'|Rows:'.$row["Rows"].'|Size:'.$tables_info[$row["Name"]]["Data_length"].'</span>
                                     </div>';
                    $other_count++;
                }
            }

            $ret['html'] = '<div style="padding-left:4em;margin-top:1em;">
                                        <div style="border-bottom:1px solid #eee;"></div>
                                     </div>';

            $base_table_html = '';
            $woo_table_html = '';
            $other_table_html = '';
            if ($has_base_table) {
                $base_all_check = '';
                if ($base_table_all_check) {
                    $base_all_check = 'checked';
                }
                $base_table_html .= '<div style="width:30%;float:left;box-sizing:border-box;padding-right:0.5em;padding-left:4em;">
                                        <div>
                                            <p>
                                                <span class="dashicons dashicons-list-view wpvivid-dashicons-blue"></span>
                                                <span><input type="checkbox" class="wpvivid-database-table-check wpvivid-database-base-table-check" '.esc_attr($base_all_check).'></span>
                                                <span><strong>Wordpress Default Tables</strong></span>
                                            </p>
                                        </div>
                                        <div style="height:250px;border:1px solid #eee;padding:0.2em 0.5em;overflow:auto;">
                                            '.$base_table.'
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>';
            }

            if ($has_other_table) {
                $other_all_check = '';
                if ($other_table_all_check) {
                    $other_all_check = 'checked';
                }

                if($has_woo_table){
                    $other_table_width = '40%';
                }
                else{
                    $other_table_width = '70%';
                }

                $other_table_html .= '<div style="width:'.$other_table_width.'; float:left;box-sizing:border-box;padding-left:0.5em;">
                                        <div>
                                            <p>
                                                <span class="dashicons dashicons-list-view wpvivid-dashicons-green"></span>
                                                <span><input type="checkbox" class="wpvivid-database-table-check wpvivid-database-other-table-check" '.esc_attr($other_all_check).'></span>
                                                <span><strong>Other Tables</strong></span>
                                            </p>
                                        </div>
                                        <div style="height:250px;border:1px solid #eee;padding:0.2em 0.5em;overflow-y:auto;">
                                            '.$other_table.'
                                        </div>
                                     </div>';
            }

            if($has_woo_table) {
                $woo_all_check = '';
                if ($woo_table_all_check) {
                    $woo_all_check = 'checked';
                }
                $woo_table_html .= '<div style="width:30%; float:left;box-sizing:border-box;padding-left:0.5em;">
                                        <div>
										    <p><span class="dashicons dashicons-list-view wpvivid-dashicons-orange"></span>
												<span><input type="checkbox" class="wpvivid-database-table-check wpvivid-database-woo-table-check" '.esc_attr($woo_all_check).'></span>
												<span><strong>WooCommerce Tables</strong></span>
											</p>
										</div>
										<div style="height:250px;border:1px solid #eee;padding:0.2em 0.5em;overflow:auto;">
											'.$woo_table.'
                                        </div>
                                    </div>';
            }

            $ret['html'] .= $base_table_html . $other_table_html . $woo_table_html;
            $ret['tables_info'] = $tables_info;
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function wpvivid_replace_directory( $path ) {
        return preg_replace( '/[\\\\]+/', '/', $path );
    }

    public function getPath( $path, $wpcontentDir, $directory ) {
        $realPath = $this->wpvivid_replace_directory($directory->getRealPath());
        if( false === strpos( $realPath, $path ) ) {
            return false;
        }

        $path = str_replace( $wpcontentDir . '/', null, $this->wpvivid_replace_directory($directory->getRealPath()) );
        // Using strpos() for symbolic links as they could create nasty stuff in nix stuff for directory structures
        if( !$directory->isDir() ||
            strlen( $path ) < 1 ||
            (strpos( $this->wpvivid_replace_directory($directory->getRealPath()), $wpcontentDir . '/' . 'plugins' ) !== 0 &&
                strpos( $this->wpvivid_replace_directory($directory->getRealPath()), $wpcontentDir . '/' . 'themes' ) !== 0 &&
                strpos( $this->wpvivid_replace_directory($directory->getRealPath()), $wpcontentDir . '/' . 'uploads' ) !== 0 )
        ) {
            return false;
        }

        return $path;
    }

    public function wpvivid_search_staging_theme_directories($wpvivid_staging_themes_dir){
        if ( empty( $wpvivid_staging_themes_dir ) ) {
            return false;
        }
        $found_themes = array();
        $wpvivid_staging_themes_dir = (array) $wpvivid_staging_themes_dir;
        foreach ( $wpvivid_staging_themes_dir as $theme_root ) {
            $dirs = @ scandir( $theme_root );
            if ( ! $dirs ) {
                continue;
            }
            foreach ( $dirs as $dir ) {
                if ( ! is_dir( $theme_root . '/' . $dir ) || $dir[0] == '.' || $dir == 'CVS' ) {
                    continue;
                }
                if ( file_exists( $theme_root . '/' . $dir . '/style.css' ) ) {
                    $found_themes[ $dir ] = array(
                        'theme_file' => $dir . '/style.css',
                        'theme_root' => $theme_root,
                    );
                }
                else {
                    $found_theme = false;
                    $sub_dirs = @ scandir( $theme_root . '/' . $dir );
                    if ( ! $sub_dirs ) {
                        continue;
                    }
                    foreach ( $sub_dirs as $sub_dir ) {
                        if ( ! is_dir( $theme_root . '/' . $dir . '/' . $sub_dir ) || $dir[0] == '.' || $dir == 'CVS' ) {
                            continue;
                        }
                        if ( ! file_exists( $theme_root . '/' . $dir . '/' . $sub_dir . '/style.css' ) ) {
                            continue;
                        }
                        $found_themes[ $dir . '/' . $sub_dir ] = array(
                            'theme_file' => $dir . '/' . $sub_dir . '/style.css',
                            'theme_root' => $theme_root,
                        );
                        $found_theme = true;
                    }
                    if ( ! $found_theme ) {
                        $found_themes[ $dir ] = array(
                            'theme_file' => $dir . '/style.css',
                            'theme_root' => $theme_root,
                        );
                    }
                }
            }
        }
        asort( $found_themes );
        return $found_themes;
    }

    public function get_staging_themes_info($wpvivid_staging_themes_dir){
        $themes = array();
        $theme_directories = $this->wpvivid_search_staging_theme_directories($wpvivid_staging_themes_dir);
        if ( !empty( $theme_directories ) ) {
            foreach ( $theme_directories as $theme => $theme_root ) {
                $themes[ $theme ] = $theme_root['theme_root'] . '/' . $theme;
                $themes[ $theme ] = new WP_Theme( $theme, $theme_root['theme_root'] );
            }
        }
        return $themes;
    }

    public function get_staging_plugins_info($wpvivid_stating_plugins_dir){
        $wp_plugins  = array();
        $plugin_root = $wpvivid_stating_plugins_dir;
        $plugins_dir  = @ opendir( $plugin_root );
        $plugin_files = array();
        if ( $plugins_dir ) {
            while ( ( $file = readdir( $plugins_dir ) ) !== false ) {
                if ( substr( $file, 0, 1 ) == '.' ) {
                    continue;
                }
                if ( is_dir( $plugin_root . '/' . $file ) ) {
                    $plugins_subdir = @ opendir( $plugin_root . '/' . $file );
                    if ( $plugins_subdir ) {
                        while ( ( $subfile = readdir( $plugins_subdir ) ) !== false ) {
                            if ( substr( $subfile, 0, 1 ) == '.' ) {
                                continue;
                            }
                            if ( substr( $subfile, -4 ) == '.php' ) {
                                $plugin_files[] = "$file/$subfile";
                            }
                        }
                        closedir( $plugins_subdir );
                    }
                } else {
                    if ( substr( $file, -4 ) == '.php' ) {
                        $plugin_files[] = $file;
                    }
                }
            }
            closedir( $plugins_dir );
        }
        if ( !empty( $plugin_files ) ) {
            foreach ( $plugin_files as $plugin_file ) {
                if ( ! is_readable( "$plugin_root/$plugin_file" ) ) {
                    continue;
                }

                $plugin_data = get_plugin_data( "$plugin_root/$plugin_file", false, false );

                if ( empty( $plugin_data['Name'] ) ) {
                    continue;
                }

                $wp_plugins[ plugin_basename( $plugin_file ) ] = $plugin_data;
            }
        }
        return $wp_plugins;
    }

    public function get_staging_directory_info($path){
        $wpcontentDir = $path.DIRECTORY_SEPARATOR.'wp-content';
        $wpcontentDir = str_replace('\\', '/', $wpcontentDir);
        $tmp_path = str_replace('\\', '/', $path);
        if(!file_exists($wpcontentDir)){
            //return error
        }
        else {
            $directories = new \DirectoryIterator($wpcontentDir);
        }
        $wpvivid_staging_themes_dir  = '';
        $wpvivid_stating_plugins_dir = '';
        foreach ( $directories as $directory ) {
            if( false === ($path = $this->getPath( $tmp_path, $wpcontentDir, $directory )) ) {
                continue;
            }
            if($directory == 'themes'){
                $wpvivid_staging_themes_dir  = $wpcontentDir . '/' . 'themes';
            }
            if($directory == 'plugins'){
                $wpvivid_stating_plugins_dir = $wpcontentDir . '/' . 'plugins';
            }
        }
        $ret['themes_list']  = $this->get_staging_themes_info($wpvivid_staging_themes_dir);
        $ret['plugins_list'] = $this->get_staging_plugins_info($wpvivid_stating_plugins_dir);
        return $ret;
    }

    public function get_theme_plugin_info($root)
    {
        $theme_info['size']=$this->get_folder_size($root,0);
        return $theme_info;
    }

    public function get_folder_size($root,$size)
    {
        $count = 0;
        if(is_dir($root))
        {
            $handler = opendir($root);
            if($handler!==false)
            {
                while (($filename = readdir($handler)) !== false)
                {
                    if ($filename != "." && $filename != "..") {
                        $count++;

                        if (is_dir($root . DIRECTORY_SEPARATOR . $filename))
                        {
                            $size=$this->get_folder_size($root . DIRECTORY_SEPARATOR . $filename,$size);
                        } else {
                            $size+=filesize($root . DIRECTORY_SEPARATOR . $filename);
                        }
                    }
                }
                if($handler)
                    @closedir($handler);
            }

        }
        return $size;
    }

    public function staging_site()
    {
        $redirect=false;
        if(is_multisite())
        {
            switch_to_blog(get_main_network_id());
            $staging_init=get_option('wpvivid_staging_init', false);
            $staging_finish=get_option('wpvivid_staging_finish', false);
            restore_current_blog();
        }
        else
        {
            $staging_init=get_option('wpvivid_staging_init', false);
            $staging_finish=get_option('wpvivid_staging_finish', false);
        }

        if($staging_finish)
        {
            if ( function_exists( 'save_mod_rewrite_rules' ) ) {
                save_mod_rewrite_rules();
            }
            else{
                if(file_exists(ABSPATH . 'wp-admin/includes/misc.php')) {
                    require_once ABSPATH . 'wp-admin/includes/misc.php';
                }
                if ( function_exists( 'save_mod_rewrite_rules' ) ) {
                    save_mod_rewrite_rules();
                }
            }
            flush_rewrite_rules(true);
            delete_option('wpvivid_staging_finish');
            if(!$this->check_theme_exist())
            {
                $redirect=true;
            }
        }

        if($staging_init)
        {
            global $wp_rewrite;

            if($staging_init == 1){
                //create staging site
                $wp_rewrite->set_permalink_structure( null );
            }
            else{
                //push to live site
                $wp_rewrite->set_permalink_structure( $staging_init );
            }

            delete_option('wpvivid_staging_init');
        }

        $data=$this->get_staging_site_data();

        if($data!==false)
        {
            wp_enqueue_style( "wpvivid-admin-bar", WPVIVID_PLUGIN_DIR_URL . "css/wpvivid-admin-bar.css", array(), WPVIVID_PLUGIN_VERSION );
            if(!$this->is_login_page())
            {
                if(is_multisite())
                {
                    switch_to_blog(get_main_network_id());
                    $options=get_option('wpvivid_staging_options', false);
                    restore_current_blog();
                }
                else
                {
                    $options=get_option('wpvivid_staging_options',array());
                }

                $staging_not_need_login=isset($options['not_need_login']) ? $options['not_need_login'] : true;

                if(!$staging_not_need_login)
                {
                    if(!current_user_can('manage_options'))
                    {
                        $this->output_login_page();
                    }
                }
            }
        }

        if($redirect)
        {
            ?>
            <script>
                location.reload();
            </script>
            <?php
        }
    }

    public function check_theme_exist()
    {
        global $wp_theme_directories;
        $stylesheet = get_stylesheet();
        $theme_root = get_raw_theme_root( $stylesheet );
        if ( false === $theme_root ) {
            $theme_root = WP_CONTENT_DIR . '/themes';
        }
        elseif ( ! in_array( $theme_root, (array) $wp_theme_directories ) )
        {
            $theme_root = WP_CONTENT_DIR . $theme_root;
        }

        $theme_dir = $stylesheet;

        // Correct a situation where the theme is 'some-directory/some-theme' but 'some-directory' was passed in as part of the theme root instead.
        if ( ! in_array( $theme_root, (array) $wp_theme_directories ) && in_array( dirname( $theme_root ), (array) $wp_theme_directories ) ) {
            $stylesheet = basename( $theme_root ) . '/' .$theme_dir;
            $theme_root = dirname( $theme_root );
        }

        $theme_file       = $stylesheet . '/style.css';

        if( ! file_exists( $theme_root . '/' . $theme_file ) )
        {
            $themes=wp_get_themes();
            foreach ($themes as $theme)
            {
                switch_theme($theme->get_stylesheet());
                return false;
            }
        }
        return true;
    }

    public function is_login_page()
    {
        return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
    }

    public function wpvivid_logout_redirect()
    {
        $redirectTo = get_site_url();
        wp_logout();
        ?>
        <script>
            location.href='<?php echo $redirectTo; ?>';
        </script>
        <?php
    }

    public function output_login_page()
    {
        if(is_user_logged_in())
        {
            if(current_user_can( 'manage_options' ))
            {
                return false;
            }
            else
            {
                $this->wpvivid_logout_redirect();
            }
        }
        if( !isset( $_POST['log'] ) || !isset( $_POST['pwd'] ) )
        {

        }
        else
        {
            $user_data = get_user_by( 'login', $_POST['log'] );

            if( !$user_data ) {
                $user_data = get_user_by( 'email', $_POST['log'] );
            }

            if( $user_data )
            {
                if( wp_check_password( $_POST['pwd'], $user_data->user_pass, $user_data->ID ) )
                {

                    $rememberme = isset( $_POST['rememberme'] ) ? true : false;

                    wp_set_auth_cookie( $user_data->ID, $rememberme );
                    wp_set_current_user( $user_data->ID, $_POST['log'] );
                    do_action( 'wp_login', $_POST['log'], get_userdata( $user_data->ID ) );

                    $redirect_to = get_site_url() . '/wp-admin/';

                    if( !empty( $_POST['redirect_to'] ) ) {
                        $redirectTo = $_POST['redirect_to'];
                    }

                    header( 'Location:' . $redirectTo );
                }
            }
        }

        require_once( ABSPATH . 'wp-login.php' );

        ?>
        <script>
            jQuery(document).ready(function ()
            {
                jQuery('#loginform').prop('action', '');
            });
        </script>
        <?php

        die();
    }

    public function delete_site()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
            } else {
                die();
            }

            $ret = $this->_delete_site($id);

            $html = '';
            $list = get_option('wpvivid_staging_task_list', array());
            if (!empty($list)) {
                $display_list = new WPvivid_Staging_List();
                $display_list->set_parent('wpvivid_staging_list');
                $display_list->set_list($list);
                $display_list->prepare_items();
                ob_start();
                $display_list->display();
                $html = ob_get_clean();
            }
            $ret['html'] = $html;
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function wpvivid_get_staging_database_object($use_additional_db, $db_user, $db_pass, $db_name, $db_host){
        if($use_additional_db){
            return new wpdb($db_user, $db_pass, $db_name, $db_host);
        }
        else{
            global $wpdb;
            return $wpdb;
        }
    }

    public function delete_cancel_staging_site(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if (isset($_POST['staging_site_info'])) {
                $json = $_POST['staging_site_info'];
                $json = stripslashes($json);
                $staging_site_info = json_decode($json, true);
                $site_path = $staging_site_info['staging_path'];
                $use_additional_db = $staging_site_info['staging_additional_db'];
                $db_user = $staging_site_info['staging_additional_db_user'];
                $db_pass = $staging_site_info['staging_additional_db_pass'];
                $db_name = $staging_site_info['staging_additional_db_name'];
                $db_host = $staging_site_info['staging_additional_db_host'];
                if (!empty($site_path)) {
                    $home_path = untrailingslashit(ABSPATH);
                    if ($home_path != $site_path) {
                        if (file_exists($site_path)) {
                            if (!class_exists('WP_Filesystem_Base')) include_once(ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php');
                            if (!class_exists('WP_Filesystem_Direct')) include_once(ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php');

                            $fs = new WP_Filesystem_Direct(false);
                            $fs->rmdir($site_path, true);
                        }
                    }
                }

                $prefix = $staging_site_info['staging_table_prefix'];
                if (!empty($prefix)) {
                    $db = $this->wpvivid_get_staging_database_object($use_additional_db, $db_user, $db_pass, $db_name, $db_host);
                    $sql = $db->prepare("SHOW TABLES LIKE %s;", $db->esc_like($prefix) . '%');
                    $result = $db->get_results($sql, OBJECT_K);

                    if (!empty($result)) {
                        foreach ($result as $table_name => $value) {
                            $table['name'] = $table_name;
                            $db->query("DROP TABLE IF EXISTS {$table_name}");
                        }
                    }
                }

                $ret['result'] = 'success';
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function _delete_site($site_id,$unfinished=false)
    {
        try
        {
            set_time_limit(900);
            $task=new WPvivid_Staging_Task($site_id);

            if($unfinished)
            {
                if($task->is_restore()||$task->is_copy())
                {
                    $ret['result']='success';
                    return $ret;
                }
                $site_path=$task->get_path(true);
                $prefix=$task->get_db_prefix(true);
                $copy_db=new WPvivid_Staging_Copy_DB($site_id);
                $db=$copy_db->get_db_instance(true);
            }
            else
            {
                $site_path=$task->get_site_path();
                $prefix=$task->get_site_prefix();
                $db=$task->get_site_db_instance();
            }

            if(empty($site_path))
            {
                $ret['result']='success';
                $default = array();
                $tasks = get_option('wpvivid_staging_task_list', $default);
                unset($tasks[$site_id]);
                update_option('wpvivid_staging_task_list',$tasks);
                return $ret;
            }

            $home_path=untrailingslashit(ABSPATH);
            if($home_path!=$site_path)
            {
                if (file_exists($site_path))
                {
                    if (!class_exists('WP_Filesystem_Base')) include_once(ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php');
                    if (!class_exists('WP_Filesystem_Direct')) include_once(ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php');

                    $fs = new WP_Filesystem_Direct(false);
                    $fs->rmdir($site_path, true);
                }
            }

            if(empty($prefix)||empty($db))
            {
                $ret['result']='success';
                $default = array();
                $tasks = get_option('wpvivid_staging_task_list', $default);
                unset($tasks[$site_id]);
                update_option('wpvivid_staging_task_list',$tasks);
                return $ret;
            }

            $sql=$db->prepare("SHOW TABLES LIKE %s;", $db->esc_like($prefix) . '%');
            $result = $db->get_results($sql, OBJECT_K);
            if(!empty($result))
            {
                $db->query( "SET foreign_key_checks = 0" );
                foreach ($result as $table_name=>$value)
                {
                    $table['name']=$table_name;
                    $db->query( "DROP TABLE IF EXISTS {$table_name}" );
                }
                $db->query( "SET foreign_key_checks = 1" );
            }

            $default = array();
            $tasks = get_option('wpvivid_staging_task_list', $default);
            unset($tasks[$site_id]);
            update_option('wpvivid_staging_task_list',$tasks);

            $ret['result']='success';
        }
        catch (Exception $error)
        {
            $ret['result']='failed';
            $ret['error']=$error->getMessage();
        }

        return $ret;
    }

    public function check_staging_dir()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            $ret['result'] = 'success';
            if(!isset($_POST['path']) || empty($_POST['path']) || !is_string($_POST['path']))
            {
                $ret['result']='failed';
                $ret['error']='A site path is required.';
                echo json_encode($ret);
                die();
            }

            $path = sanitize_text_field($_POST['path']);

            if(!isset($_POST['table_prefix']) || empty($_POST['table_prefix']) || !is_string($_POST['table_prefix']))
            {
                $ret['result']='failed';
                $ret['error']='A table prefix is required.';
                echo json_encode($ret);
                die();
            }

            $table_prefix = sanitize_text_field($_POST['table_prefix']);

            if (isset($_POST['root_dir']) && $_POST['root_dir'] == 0)
            {
                $path = untrailingslashit(ABSPATH) . DIRECTORY_SEPARATOR. $path;
            }
            else if(isset($_POST['root_dir']) && $_POST['root_dir'] == 1)
            {
                $path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $path;
            }

            if (file_exists($path))
            {
                $ret['result'] = 'failed';
                $ret['error'] = 'A folder with the same name already exists in website\'s root directory.';
            }
            else
            {
                if (mkdir($path, 0755, true))
                {
                    rmdir($path);
                } else {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'Create directory is not allowed in ' . $path . '.Please check the directory permissions and try again';
                }
            }

            if(isset($_POST['additional_db']))
            {
                $additional_db_json = $_POST['additional_db'];
                $additional_db_json = stripslashes($additional_db_json);
                $additional_db_options = json_decode($additional_db_json, true);
                if($additional_db_options['additional_database_check'] === '1')
                {
                    $db_user = sanitize_text_field($additional_db_options['additional_database_info']['db_user']);
                    $db_pass = sanitize_text_field($additional_db_options['additional_database_info']['db_pass']);
                    $db_host = sanitize_text_field($additional_db_options['additional_database_info']['db_host']);
                    $db_name = sanitize_text_field($additional_db_options['additional_database_info']['db_name']);
                    $db = new wpdb($db_user, $db_pass, $db_name, $db_host);
                    $sql = $db->prepare("SHOW TABLES LIKE %s;", $db->esc_like($table_prefix) . '%');
                    $result = $db->get_results($sql, OBJECT_K);
                    if (!empty($result))
                    {
                        $ret['result'] = 'failed';
                        $ret['error'] = 'The table prefix already exists.';
                    }
                }
                else
                {
                    global $wpdb;
                    $sql = $wpdb->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($table_prefix) . '%');
                    $result = $wpdb->get_results($sql, OBJECT_K);
                    if (!empty($result))
                    {
                        $ret['result'] = 'failed';
                        $ret['error'] = 'The table prefix already exists.';
                    }
                }
            }
            else
            {
                global $wpdb;
                $sql = $wpdb->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($table_prefix) . '%');
                $result = $wpdb->get_results($sql, OBJECT_K);
                if (!empty($result))
                {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'The table prefix already exists.';
                }
            }
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function check_filesystem_permissions()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try{
            if(!isset($_POST['path']) || empty($_POST['path']) || !is_string($_POST['path']))
            {
                $ret['result']='failed';
                $ret['error']='A site path is required.';
                echo json_encode($ret);
                die();
            }

            $path = sanitize_text_field($_POST['path']);
            $src_path = untrailingslashit(ABSPATH);

            if(isset($_POST['root_dir'])&&$_POST['root_dir']==0)
            {
                $des_path = untrailingslashit(ABSPATH) . '/' . $path;
            }
            else if (isset($_POST['root_dir'])&&$_POST['root_dir']==1)
            {
                $des_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $path;
            }
            else
            {
                $test_dir = 'wpvividstg_testfolder';
                $des_path = untrailingslashit($path) . '/' . $test_dir;
            }

            $mk_res = mkdir($des_path,0755,true);
            if (!$mk_res)
            {
                $ret['result']='failed';
                $ret['error']='The directory where the staging site will be installed is not writable. Please set the permissions of the directory to 755 then try it again.';
                echo json_encode($ret);
                die();
            }

            $test_file_name = 'wpvividstg_test_file.txt';
            $test_file_path = $des_path.DIRECTORY_SEPARATOR.$test_file_name;
            $mk_res = fopen($test_file_path, 'wb');
            if (!$mk_res)
            {
                if(file_exists($des_path))
                    @rmdir($des_path);
                $ret['result']='failed';
                $ret['error']='The directory where the staging site will be installed is not writable. Please set the permissions of the directory to 755 then try it again.';
                echo json_encode($ret);
                die();
            }

            fclose($mk_res);
            @unlink($test_file_path);
            if(file_exists($des_path))
                @rmdir($des_path);

            $ret['result'] = 'success';
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function get_recent_post()
    {
        //set_prefix
        $post_type='post';
        $args = array(
            'orderby' => 'modified',
            'ignore_sticky_posts' => '1',
            'page_id' => 0,
            'posts_per_page' => 1,
            'post_type' => $post_type
        );

        $loop = new WP_Query( $args );
        $string = '<ul>';
        while( $loop->have_posts())
        {
            $loop->the_post();
            $string .= '<li><a href="' . get_permalink( $loop->post->ID ) . '"> ' .get_the_title( $loop->post->ID ) . '</a> ( '. get_the_modified_date() .') </li>';
        }
        $string .= '</ul>';
        $string.='<input id="wpvivid_update_post" type="button" class="button button-primary" value="Update">';
        echo $string;

    }

    public function get_staging_progress()
    {
        $task_id=get_option('wpvivid_current_running_staging_task','');
        if(empty($task_id))
        {
            $list = get_option('wpvivid_staging_task_list',array());
            if(!empty($list))
            {
                foreach ($list as $key => $value)
                {
                    if($value['status']['str'] === 'running' || $value['status']['str'] === 'ready')
                    {
                        $task_id = $value['id'];
                        update_option('wpvivid_current_running_staging_task', $task_id);
                        break;
                    }
                }
            }
            if(empty($task_id))
            {
                $ret['result']='success';
                $ret['log']='';
                $ret['continue']=0;
                echo json_encode($ret);
                die();
            }
        }

        try
        {
            $task=new WPvivid_Staging_Task($task_id);
            $b_delete=false;
            if($task->get_status()=='completed')
            {
                if($task->is_restore()){
                    $ret['completed_msg'] = 'Pushing the staging site to the live site completed successfully.';
                }
                else{
                    $ret['completed_msg'] = 'Updating the staging site completed successfully.';

                    $db_connect = $task->get_db_connect();
                    $url = $db_connect['new_site_url'];
                    $options=array();
                    $options['timeout']=30;
                    $response = wp_remote_request( $url,$options);
                    if(!is_wp_error($response) && ($response['response']['code'] == 200))
                    {
                        $this->log->OpenLogFile($task->get_log_file_name());
                        $this->log->WriteLog('Access staging site successfully.', 'notice');
                    }
                    else
                    {
                        $this->log->OpenLogFile($task->get_log_file_name());
                        $this->log->WriteLog('Access staging site failed.', 'notice');
                    }
                }
                update_option('wpvivid_current_running_staging_task','');
                $ret['continue']=0;
                $ret['completed']=1;
            }
            else if($task->get_status()=='ready')
            {
                $ret['continue']=1;
                $ret['need_restart']=1;
            }
            else if($task->get_status()=='error')
            {
                update_option('wpvivid_current_running_staging_task','');
                $ret['continue']=0;
                $ret['error']=1;
                $ret['error_msg']=$task->get_error();
                $b_delete=true;
            }
            else if($task->get_status()=='cancel')
            {
                update_option('wpvivid_current_running_staging_task','');
                $ret['continue']=0;
                $ret['need_restart']=0;
                $ret['is_cancel']=1;
                foreach ($task as $value){
                    $ret['staging_path']=$value['path']['des_path'];
                    if($value['db_connect']['des_use_additional_db']){
                        $ret['staging_additional_db']=1;
                        $ret['staging_additional_db_user']=$value['db_connect']['des_dbuser'];
                        $ret['staging_additional_db_pass']=$value['db_connect']['des_dbpassword'];
                        $ret['staging_additional_db_host']=$value['db_connect']['des_dbhost'];
                        $ret['staging_additional_db_name']=$value['db_connect']['des_dbname'];
                        $ret['staging_table_prefix']=$value['db_connect']['new_prefix'];
                    }
                    else{
                        $ret['staging_additional_db']=0;
                        $ret['staging_additional_db_user']=null;
                        $ret['staging_additional_db_pass']=null;
                        $ret['staging_additional_db_host']=null;
                        $ret['staging_additional_db_name']=null;
                        $ret['staging_table_prefix']=$value['db_connect']['new_prefix'];
                    }
                }
                update_option('wpvivid_staging_task_cancel', false);
                $b_delete=true;
            }
            else
            {
                if($task->check_timeout())
                {
                    if($task->get_status()=='ready')
                    {
                        $ret['continue']=1;
                        $ret['need_restart']=1;
                    }
                    else
                    {
                        update_option('wpvivid_current_running_staging_task','');
                        $ret['continue']=0;
                        $b_delete=true;
                    }
                }
                else
                {
                    $ret['continue']=1;
                    $ret['need_restart']=0;
                }
            }
            $staging_percent = $task->get_progress();
            $file_name=$this->log->GetSaveLogFolder(). $task->get_log_file_name().'_log.txt';
            $file =fopen($file_name,'r');
            $buffer='';
            if(!$file)
            {
                $buffer='open log file failed';
            }
            else
            {
                if(filesize($file_name)<=1*1024*1024)
                {
                    while(!feof($file))
                    {
                        $buffer .= fread($file,1024);
                    }
                }
                else
                {
                    $pos=-2;
                    $eof='';
                    $n=50;
                    $buffer_array = array();
                    while($n>0)
                    {
                        while($eof!=="\n")
                        {
                            if(!fseek($file, $pos, SEEK_END))
                            {
                                $eof=fgetc($file);
                                $pos--;
                            }
                            else
                            {
                                break;
                            }
                        }
                        $buffer_array[].=fgets($file);
                        $eof='';
                        $n--;
                    }

                    if(!empty($buffer_array))
                    {
                        $buffer_array = array_reverse($buffer_array);
                        foreach($buffer_array as $value)
                        {
                            $buffer.=$value;
                        }
                    }
                }

                fclose($file);
            }

            if($b_delete)
            {
                $this->_delete_site($task_id,true);
            }
            $ret['log']=$buffer;
            $ret['percent']=$staging_percent;
            $ret['result']='success';
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $ret['result']='failed';
            $ret['error']=$error->getMessage();
            echo json_encode($ret);
        }

        die();
    }

    public function cancel_staging()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        $task_id=get_option('wpvivid_current_running_staging_task','');
        if(empty($task_id))
        {
            $ret['result']='success';
            $ret['log']='';
            $ret['continue']=0;
            echo json_encode($ret);
            die();
        }

        try
        {
            $task=new WPvivid_Staging_Task($task_id);
            $task->cancel_staging();

            $ret['result']='success';
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $ret['result']='failed';
            $ret['error']=$error->getMessage();
            echo json_encode($ret);
        }
        die();
    }

    public function test_additional_database_connect(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if (isset($_POST['database_info']) && !empty($_POST['database_info']) && is_string($_POST['database_info'])) {
                $data = $_POST['database_info'];
                $data = stripslashes($data);
                $json = json_decode($data, true);
                $db_user = sanitize_text_field($json['db_user']);
                $db_pass = sanitize_text_field($json['db_pass']);
                $db_host = sanitize_text_field($json['db_host']);
                $db_name = sanitize_text_field($json['db_name']);

                $db = new wpdb($db_user, $db_pass, $db_name, $db_host);
                // Can not connect to mysql
                if (!empty($db->error->errors['db_connect_fail']['0'])) {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'Failed to connect to MySQL server. Please try again later.';
                    echo json_encode($ret);
                    die();
                }

                // Can not connect to database
                $db->select($db_name);
                if (!$db->ready) {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'Unable to connect to MySQL database. Please try again later.';
                    echo json_encode($ret);
                    die();
                }
                $ret['result'] = 'success';

                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function update_staging_exclude_extension(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if (isset($_POST['type']) && !empty($_POST['type']) && is_string($_POST['type']) &&
                isset($_POST['exclude_content']) && !empty($_POST['exclude_content']) && is_string($_POST['exclude_content'])) {
                $type = sanitize_text_field($_POST['type']);
                $value = sanitize_text_field($_POST['exclude_content']);

                $staging_option = self::wpvivid_get_staging_history();
                if (empty($staging_option)) {
                    $staging_option = array();
                }

                if ($type === 'upload') {
                    $staging_option['upload_extension'] = array();
                    $str_tmp = explode(',', $value);
                    for ($index = 0; $index < count($str_tmp); $index++) {
                        if (!empty($str_tmp[$index])) {
                            $staging_option['upload_extension'][] = $str_tmp[$index];
                        }
                    }
                } else if ($type === 'content') {
                    $staging_option['content_extension'] = array();
                    $str_tmp = explode(',', $value);
                    for ($index = 0; $index < count($str_tmp); $index++) {
                        if (!empty($str_tmp[$index])) {
                            $staging_option['content_extension'][] = $str_tmp[$index];
                        }
                    }
                } else if ($type === 'additional_file') {
                    $staging_option['additional_file_extension'] = array();
                    $str_tmp = explode(',', $value);
                    for ($index = 0; $index < count($str_tmp); $index++) {
                        if (!empty($str_tmp[$index])) {
                            $staging_option['additional_file_extension'][] = $str_tmp[$index];
                        }
                    }
                }

                self::wpvivid_set_staging_history($staging_option);

                $ret['result'] = 'success';
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function deal_shutdown_error($task_id)
    {
        if($this->end_shutdown_function===false)
        {
            $task=false;

            $last_error = error_get_last();
            if (!empty($last_error) && !in_array($last_error['type'], array(E_NOTICE,E_WARNING,E_USER_NOTICE,E_USER_WARNING,E_DEPRECATED), true))
            {
                $error = $last_error;
            } else {
                $error = false;
            }

            try
            {
                $task=new WPvivid_Staging_Task($task_id);
                if($error==false)
                {
                    $message='Create staging site end with a error.';
                }
                else
                {
                    $message=$error;
                }
                $task->finished_task_with_error($message);
                $this->log->WriteLog($message,'error');
            }
            catch (Exception $error)
            {
                $message = 'An Error has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
                error_log($message);
                if($task!==false)
                    $task->finished_task_with_error($message);
                $this->log->WriteLog($message,'error');
            }

            die();
        }
    }

    public function start_staging()
    {
        $this->end_shutdown_function=false;
        register_shutdown_function(array($this,'deal_staging_shutdown_error'));
        $task=false;
        try
        {
            $task_id=get_option('wpvivid_current_running_staging_task','');
            if(!empty($task_id))
            {
                $task=new WPvivid_Staging_Task($task_id);
                if($task->get_status()==='running')
                {
                    $this->end_shutdown_function=true;
                    die();
                }
                $this->log->OpenLogFile($task->get_log_file_name());
            }
            else
            {
                if(isset($_POST['path']) && isset($_POST['table_prefix']) && isset($_POST['custom_dir']) && isset($_POST['additional_db']))
                {
                    $json = $_POST['custom_dir'];
                    $json = stripslashes($json);
                    $staging_options = json_decode($json, true);

                    $additional_db_json = $_POST['additional_db'];
                    $additional_db_json = stripslashes($additional_db_json);
                    $additional_db_options = json_decode($additional_db_json, true);

                    $option['options'] = $this->set_staging_option();

                    $src_path = untrailingslashit(ABSPATH);
                    $path = sanitize_text_field($_POST['path']);
                    if(isset($_POST['root_dir'])&&$_POST['root_dir']==0)
                    {
                        $url_path=$path;

                        $new_site_url = untrailingslashit($this->get_database_site_url()). '/' . $url_path;
                        $new_home_url = untrailingslashit($this->get_database_home_url()). '/' . $url_path;
                        $des_path = untrailingslashit(ABSPATH) . '/' . $path;
                    }
                    else
                    {
                        $url_path=str_replace(ABSPATH,'',WP_CONTENT_DIR).'/' . $path;

                        $new_site_url = untrailingslashit($this->get_database_site_url()). '/' . $url_path;
                        $new_home_url = untrailingslashit($this->get_database_home_url()). '/' . $url_path;

                        $des_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $path;
                    }

                    $option['data']['path']['src_path'] = $src_path;
                    $option['data']['path']['des_path'] = $des_path;

                    $table_prefix = $_POST['table_prefix'];

                    $option['data']['restore'] = false;
                    $option['data']['copy']=false;

                    $this->set_create_staging_option($option,$staging_options,$additional_db_options,$new_site_url,$new_home_url,$table_prefix);


                    $task = new WPvivid_Staging_Task();
                    $task->set_memory_limit();
                    $task->setup_task($option);
                    $task->update_action_time('create_time');
                    $this->log->CreateLogFile($task->get_log_file_name(), 'no_folder', 'staging');
                    $this->log->WriteLog('Start creating staging site.', 'notice');
                    $this->log->WriteLogHander();
                }
            }

            $task_id=$task->get_id();
            update_option('wpvivid_current_running_staging_task',$task_id);
            register_shutdown_function(array($this,'deal_shutdown_error'),$task_id);

            $doing=$task->get_doing_task();
            if($doing===false)
            {
                $doing=$task->get_start_next_task();
            }

            $task->set_time_limit();
            if(!$task->do_task($doing))
            {
                $task->finished_task_with_error();
                $this->end_shutdown_function=true;
                die();
            }

            $doing=$task->get_start_next_task();
            if($doing==false)
            {
                $this->log->WriteLog('Creating staging site is completed.','notice');
                $task->finished_task();
            }
        }
        catch (Exception $error)
        {
            $message = 'An Error has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            error_log($message);
            if($task!==false)
                $task->finished_task_with_error($message);
            $this->log->WriteLog($message,'error');
        }

        $this->end_shutdown_function=true;
        die();
    }

    public function set_restart_staging_id()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if(isset($_POST['id']))
            {
                $task_id = $_POST['id'];
                update_option('wpvivid_current_running_staging_task', $task_id);
                $ret['result'] = 'success';
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }

    public function set_db_connect_option($new_site_url,$new_home_url,$additional_db_options,$table_prefix,$mu_single_site_id='')
    {
        global $wpdb;
        if(!empty($mu_single_site_id))
        {
            $prefix=$wpdb->get_blog_prefix($mu_single_site_id);
            $db_connect['old_prefix'] = $prefix;
            $db_connect['old_site_url'] = get_site_url($mu_single_site_id);
            $db_connect['old_home_url'] = get_home_url($mu_single_site_id);
        }
        else
        {
            $db_connect['old_prefix'] = $wpdb->base_prefix;
            $db_connect['old_site_url'] = untrailingslashit($this->get_database_site_url());
            $db_connect['old_home_url'] = untrailingslashit($this->get_database_home_url());
        }

        $db_connect['new_site_url'] = $new_site_url;
        $db_connect['new_home_url'] = $new_home_url;
        $db_connect['src_use_additional_db'] = false;
        $db_connect['des_use_additional_db'] = false;
        $db_connect['new_prefix'] = $table_prefix;
        if(isset($additional_db_options['additional_database_check']) && $additional_db_options['additional_database_check'] == '1')
        {
            /*$option['data']['db_connect']['des_use_additional_db'] = true;
            $option['data']['db_connect']['des_dbuser'] = $additional_db_options['additional_database_info']['db_user'];
            $option['data']['db_connect']['des_dbpassword'] = $additional_db_options['additional_database_info']['db_pass'];
            $option['data']['db_connect']['des_dbname'] = $additional_db_options['additional_database_info']['db_name'];
            $option['data']['db_connect']['des_dbhost'] = $additional_db_options['additional_database_info']['db_host'];*/
            $db_connect['des_use_additional_db'] = true;
            $db_connect['des_dbuser'] = $additional_db_options['additional_database_info']['db_user'];
            $db_connect['des_dbpassword'] = $additional_db_options['additional_database_info']['db_pass'];
            $db_connect['des_dbname'] = $additional_db_options['additional_database_info']['db_name'];
            $db_connect['des_dbhost'] = $additional_db_options['additional_database_info']['db_host'];
        }

        return $db_connect;
    }

    public function set_create_staging_option(&$option,$staging_options,$additional_db_options,$new_site_url,$new_home_url,$table_prefix)
    {
        global $wpdb;

        if(isset($_POST['create_new_wp']))
        {
            $option['data']['core'] = true;

            if($staging_options['themes_check'] == '1')
            {
                $option['data']['theme']['exclude_regex'] = array();
                foreach ($staging_options['themes_list'] as $theme)
                {
                    $option['data']['theme']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(get_theme_root().DIRECTORY_SEPARATOR.$theme), '/').'#';
                }
            }

            if($staging_options['plugins_check'] == '1')
            {
                $option['data']['plugins']['exclude_regex'] = array();
                foreach ($staging_options['plugins_list'] as $plugin)
                {
                    $option['data']['plugins']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.$plugin), '/').'#';
                }
            }

            $option['data']['db_connect']=$this->set_db_connect_option($new_site_url,$new_home_url,$additional_db_options,$table_prefix);
            $option['data']['create_new_wp']=true;
            //$option['data']['db']['exclude_tables'] = array();
            //$option['data']['db']['exclude_tables'][] = $wpdb->base_prefix.'hw_blocks';
            //foreach ($staging_options['database_list'] as $table)
            //{
            //    $option['data']['db']['exclude_tables'][] = $table;
            //}
        }
        else
        {
            if($staging_options['database_check'] == '1')
            {
                $option['data']['db_connect']=$this->set_db_connect_option($new_site_url,$new_home_url,$additional_db_options,$table_prefix);

                $option['data']['db']['exclude_tables'] = array();
                $option['data']['db']['exclude_tables'][] = $wpdb->base_prefix.'hw_blocks';
                foreach ($staging_options['database_list'] as $table)
                {
                    $option['data']['db']['exclude_tables'][] = $table;
                }
            }

            $option['data']['theme']['exclude_regex'] = array();
            if($staging_options['themes_check'] == '1')
            {
                foreach ($staging_options['themes_list'] as $key => $value)
                {
                    $option['data']['theme']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(get_theme_root().DIRECTORY_SEPARATOR.$key), '/').'#';
                }
                $option['data']['theme']['exclude_files_regex']=array();
                $theme_extension_tmp = array();
                if(isset($staging_options['themes_extension']) && !empty($staging_options['themes_extension']))
                {
                    $str_tmp = explode(',', $staging_options['themes_extension']);
                    for($index=0; $index<count($str_tmp); $index++)
                    {
                        if(!empty($str_tmp[$index]))
                        {
                            $option['data']['theme']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                            $theme_extension_tmp[] = $str_tmp[$index];
                        }
                    }
                    $staging_options['themes_extension'] = $theme_extension_tmp;
                }
            }

            $option['data']['plugins']['exclude_regex'] = array();
            if($staging_options['plugins_check'] == '1')
            {
                foreach ($staging_options['plugins_list'] as $key => $value)
                {
                    $option['data']['plugins']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.$key), '/').'#';
                }
                $option['data']['plugins']['exclude_files_regex']=array();
                $plugin_extension_tmp = array();
                if(isset($staging_options['plugins_extension']) && !empty($staging_options['plugins_extension']))
                {
                    $str_tmp = explode(',', $staging_options['plugins_extension']);
                    for($index=0; $index<count($str_tmp); $index++)
                    {
                        if(!empty($str_tmp[$index]))
                        {
                            $option['data']['plugins']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                            $plugin_extension_tmp[] = $str_tmp[$index];
                        }
                    }
                    $staging_options['plugins_extension'] = $plugin_extension_tmp;
                }
            }

            if($staging_options['uploads_check'] == '1')
            {
                $upload_dir = wp_upload_dir();
                $option['data']['upload']['exclude_regex'] = array();
                foreach ($staging_options['uploads_list'] as $key => $value)
                {
                    $option['data']['upload']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path($upload_dir['basedir'].DIRECTORY_SEPARATOR.$key), '/').'#';
                }
                $option['data']['upload']['exclude_files_regex']=array();
                $upload_extension_tmp = array();
                if(isset($staging_options['upload_extension']) && !empty($staging_options['upload_extension']))
                {
                    $str_tmp = explode(',', $staging_options['upload_extension']);
                    for($index=0; $index<count($str_tmp); $index++)
                    {
                        if(!empty($str_tmp[$index]))
                        {
                            $option['data']['upload']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                            $upload_extension_tmp[] = $str_tmp[$index];
                        }
                    }
                    $staging_options['upload_extension'] = $upload_extension_tmp;
                }
            }

            if($staging_options['content_check'] == '1')
            {
                $option['data']['wp-content']['exclude_regex'] = array();
                foreach ($staging_options['content_list'] as $key => $value)
                {
                    $option['data']['wp-content']['exclude_regex'][] = '#^'.preg_quote($this -> transfer_path(WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$key), '/').'#';
                }
                $option['data']['wp-content']['exclude_files_regex']=array();
                $content_extension_tmp = array();
                if(isset($staging_options['content_extension']) && !empty($staging_options['content_extension']))
                {
                    $str_tmp = explode(',', $staging_options['content_extension']);
                    for($index=0; $index<count($str_tmp); $index++)
                    {
                        if(!empty($str_tmp[$index]))
                        {
                            $option['data']['wp-content']['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                            $content_extension_tmp[] = $str_tmp[$index];
                        }
                    }
                    $staging_options['content_extension'] = $content_extension_tmp;
                }
            }

            if($staging_options['core_check'] == '1')
            {
                $option['data']['core'] = true;
            }

            if($staging_options['additional_file_check'] == '1')
            {
                $custom['exclude_regex'] = array();
                $custom['exclude_files_regex']=array();
                $additional_file_extension_tmp = array();
                if(isset($staging_options['additional_file_extension']) && !empty($staging_options['additional_file_extension']))
                {
                    $str_tmp = explode(',', $staging_options['additional_file_extension']);
                    for($index=0; $index<count($str_tmp); $index++)
                    {
                        if(!empty($str_tmp[$index]))
                        {
                            $custom['exclude_files_regex'][] = '#' . '.*\.' . $str_tmp[$index] . '$' . '#';
                            $additional_file_extension_tmp[] = $str_tmp[$index];
                        }
                    }
                    $staging_options['additional_file_extension'] = $additional_file_extension_tmp;
                }
                foreach ($staging_options['additional_file_list'] as $key => $value)
                {
                    $custom['root'] = $key;
                    $option['data']['custom'][] = $custom;
                }
            }

            self::wpvivid_set_staging_history($staging_options);
        }
    }

    public function get_table_list($prefix,&$mu_exclude_table,$task=false,$exculude_user=true)
    {

        global $wpdb;

        if($task===false)
        {
            $db=$wpdb;
        }
        else
        {
            $db=$task->get_site_db_instance();
        }

        $sql=$db->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($prefix) . '%');
        $result = $db->get_results($sql, OBJECT_K);
        foreach ($result as $table_name=>$value)
        {
            if($prefix==$db->base_prefix)
            {
                if ( 1 == preg_match('/^' . $db->base_prefix . '\d+_/', $table_name) )
                {

                }
                else
                {
                    if($table_name==$db->base_prefix.'blogs'&&$exculude_user!==false)
                        continue;
                    if($exculude_user===false)
                    {
                        if($table_name==$db->base_prefix.'users'||$table_name==$db->base_prefix.'usermeta')
                            continue;
                    }
                    $mu_exclude_table[]=$table_name;
                }
            }
            else
            {
                $mu_exclude_table[]=$table_name;
            }
        }
    }

    public function get_upload_exclude_folder($site_id,$des=false,$task=false)
    {
        if($des)
        {
            $upload_dir = wp_upload_dir();
            $dir = str_replace( ABSPATH, '', $upload_dir['basedir'] );
            $src_path=$task->get_site_path();
            $upload_basedir=$src_path.DIRECTORY_SEPARATOR.$dir;
            if ( defined( 'MULTISITE' ) )
            {
                $upload_basedir = $upload_basedir.'/sites/' . $site_id;
            } else {
                $upload_basedir = $upload_basedir.'/' . $site_id;
            }
            return $upload_basedir;
        }
        else
        {
            $upload= $this->get_site_upload_dir($site_id);
            return $upload['basedir'];
        }
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

    public function deal_staging_shutdown_error()
    {
        if($this->end_shutdown_function===false)
        {
            $last_error = error_get_last();
            if (!empty($last_error) && !in_array($last_error['type'], array(E_NOTICE,E_WARNING,E_USER_NOTICE,E_USER_WARNING,E_DEPRECATED), true))
            {
                $error = $last_error;
            } else {
                $error = false;
            }

            if ($error === false)
            {
                $error = 'Task interrupted. Please hold on. We are starting a retry.';
            } else {
                $error = 'type: '. $error['type'] . ', ' . $error['message'] . ' file:' . $error['file'] . ' line:' . $error['line'];
                error_log($error);
            }
            $this->log->WriteLog($error,'error');

            $list = get_option('wpvivid_staging_task_list',array());
            if(!empty($list))
            {
                foreach ($list as $key => $value)
                {
                    if($value['status']['str'] === 'running')
                    {
                        $list[$key]['status']['str']='ready';
                        update_option('wpvivid_staging_task_list', $list);
                        break;
                    }
                }
            }

            die();
        }
    }

    public static function wpvivid_set_staging_history($option){
        update_option('wpvivid_staging_history', $option);
    }

    public static function wpvivid_get_staging_history(){
        $options = get_option('wpvivid_staging_history', array());
        return $options;
    }

    public static function wpvivid_get_push_staging_history(){
        $options = get_option('wpvivid_push_staging_history', array());
        return $options;
    }

    private function transfer_path($path)
    {
        $path = str_replace('\\','/',$path);
        $values = explode('/',$path);
        return implode(DIRECTORY_SEPARATOR,$values);
    }

    public function set_staging_option()
    {
        $options=get_option('wpvivid_staging_options');

        if(isset($options['staging_db_insert_count']))
            $option['staging_db_insert_count']=$options['staging_db_insert_count'];
        else
            $option['staging_db_insert_count']=10000;

        if(isset($options['staging_db_replace_count']))
            $option['staging_db_replace_count']=$options['staging_db_replace_count'];
        else
            $option['staging_db_replace_count']=5000;

        if(isset($options['staging_memory_limit']))
            $option['staging_memory_limit']=$options['staging_memory_limit'];
        else
            $option['staging_memory_limit']='256M';

        if(isset($options['staging_file_copy_count']))
            $option['staging_file_copy_count']=$options['staging_file_copy_count'];
        else
            $option['staging_file_copy_count']=500;

        if(isset($options['staging_exclude_file_size'])) {
            $option['staging_exclude_file_size'] = $options['staging_exclude_file_size'];
        }
        else {
            $option['staging_exclude_file_size'] = 30;
        }

        if(isset($options['staging_max_execution_time']))
            $option['staging_max_execution_time']=$options['staging_max_execution_time'];
        else
            $option['staging_max_execution_time']=900;

        if(isset($options['staging_resume_count']))
            $option['staging_resume_count']=$options['staging_resume_count'];
        else
            $option['staging_resume_count']=6;

        if(isset($options['staging_overwrite_permalink']))
            $option['staging_overwrite_permalink']=$options['staging_overwrite_permalink'];
        else
            $option['staging_overwrite_permalink']=1;

        return $option;
    }

    public function export_setting_addon($json)
    {
        $default = array();
        $wpvivid_staging_history = get_option('wpvivid_staging_history', $default);
        $wpvivid_push_staging_history = get_option('wpvivid_push_staging_history', $default);
        $json['data']['wpvivid_staging_history'] = $wpvivid_staging_history;
        $json['data']['wpvivid_push_staging_history'] = $wpvivid_push_staging_history;
        return $json;
    }
}