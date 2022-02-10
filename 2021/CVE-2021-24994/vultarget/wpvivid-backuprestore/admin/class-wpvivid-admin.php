<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpvivid.com
 * @since      0.9.1
 *
 * @package    WPvivid
 * @subpackage WPvivid/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPvivid
 * @subpackage WPvivid/admin
 * @author     wpvivid team
 */
if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
class WPvivid_Admin {

    /**
     * The ID of this plugin.
     *
     * 
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * 
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    private $screen_ids;

    private $toolbar_menus;

    private $submenus;
    /**
     * Initialize the class and set its properties.
     *
     * 
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        add_filter('wpvivid_get_screen_ids',array($this,'get_screen_ids'),10);
        add_filter('wpvivid_get_toolbar_menus',array($this,'get_toolbar_menus'),10);
        add_filter('wpvivid_get_admin_menus',array($this,'get_admin_menus'),10);
        add_filter('wpvivid_add_side_bar', array($this, 'wpvivid_add_side_bar'), 10, 2);

        add_action('wpvivid_before_setup_page',array($this,'migrate_notice'));
        add_action('wpvivid_before_setup_page',array($this,'show_add_my_review'));
        add_action('wpvivid_before_setup_page',array($this,'check_extensions'));
        add_action('wpvivid_before_setup_page',array($this,'check_amazons3'));
        add_action('wpvivid_before_setup_page',array($this,'check_dropbox'));
        add_action('wpvivid_before_setup_page',array($this,'init_js_var'));

        add_filter('wpvivid_add_log_tab_page', array($this, 'add_log_tab_page'), 10);

        add_action('admin_notices', array($this, 'check_wpvivid_pro_version'));

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function add_log_tab_page($setting_array)
    {
        $setting_array['backup_log_page'] = array('index' => '1', 'tab_func' =>  array($this, 'wpvivid_add_tab_log'), 'page_func' => array($this, 'wpvivid_add_page_log'));
        //$setting_array['read_log_page'] = array('index' => '2', 'tab_func' =>  array($this, 'wpvivid_add_tab_read_log'), 'page_func' => array($this, 'wpvivid_add_page_read_log'));
        return $setting_array;
    }

    public function get_screen_ids($screen_ids)
    {
        $screen_ids[]='toplevel_page_'.$this->plugin_name;
        $screen_ids[]='wpvivid-backup_page_wpvivid-transfer';
        $screen_ids[]='wpvivid-backup_page_wpvivid-setting';
        $screen_ids[]='wpvivid-backup_page_wpvivid-schedule';
        $screen_ids[]='wpvivid-backup_page_wpvivid-remote';
        $screen_ids[]='wpvivid-backup_page_wpvivid-website';
        $screen_ids[]='wpvivid-backup_page_wpvivid-log';
        $screen_ids[]='wpvivid-backup_page_wpvivid-key';
        $screen_ids[]='wpvivid-backup_page_wpvivid-mainwp';
        $screen_ids[]='wpvivid-backup_page_wpvivid_premium';
        return $screen_ids;
    }

    public function get_toolbar_menus($toolbar_menus)
    {
        $menu['id']='wpvivid_admin_menu';
        $menu['title']='WPvivid Backup';
        $toolbar_menus[$menu['id']]=$menu;

        $admin_url = admin_url();

        $menu['id']='wpvivid_admin_menu_backup';
        $menu['parent']='wpvivid_admin_menu';
        $menu['title']=__('Backup & Restore', 'wpvivid-backuprestore');
        $menu['tab']='admin.php?page=WPvivid&tab-backup';
        $menu['href']=$admin_url . 'admin.php?page=WPvivid&tab-backup';
        $menu['capability']='administrator';
        $menu['index']=1;
        $toolbar_menus[$menu['parent']]['child'][$menu['id']]=$menu;

        return $toolbar_menus;
    }

    public function get_admin_menus($submenus)
    {
        $submenu['parent_slug']=$this->plugin_name;
        $submenu['page_title']='WPvivid Backup';
        $submenu['menu_title']=__('Backup & Restore', 'wpvivid-backuprestore');
        $submenu['capability']='administrator';
        $submenu['menu_slug']=$this->plugin_name;
        $submenu['function']=array($this, 'display_plugin_setup_page');
        $submenu['index']=1;
        $submenus[$submenu['menu_slug']]=$submenu;

        $submenu['parent_slug']=$this->plugin_name;
        $submenu['page_title']='WPvivid Backup';
        $submenu['menu_title']=__('Settings', 'wpvivid-backuprestore');
        $submenu['capability']='administrator';
        $submenu['menu_slug']='wpvivid-setting';
        $submenu['function']=array($this, 'display_plugin_setup_page');
        $submenu['index']=5;
        $submenus[$submenu['menu_slug']]=$submenu;

        return $submenus;
    }

    public function wpvivid_add_side_bar($html, $show_schedule = false){
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
                        <tr>
                            <td class="row-title">'.__('Adjust <a href="https://docs.wpvivid.com/wpvivid-backup-free-advanced-settings.html" target="_blank">Advanced Settings</a> for higher task success rate.', 'wpvivid-backuprestore').'</td>
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
                            <tr class="alternate"><td class="row-title"><a href="https://docs.wpvivid.com/wpvivid-backup-free-general-settings.html" target="_blank">'.__('WPvivid Backup Settings', 'wpvivid-backuprestore').'</a></td></tr>
                            <tr><td class="row-title"><a href="https://docs.wpvivid.com/get-started-create-manual-backup.html" target="_blank">'.__('Create a Manual Backup', 'wpvivid-backuprestore').'</a></td></tr>
                            <tr class="alternate"><td class="row-title"><a href="https://docs.wpvivid.com/get-started-restore-site.html" target="_blank">'.__('Restore Your Site from a Backup', 'wpvivid-backuprestore').'</a></td></tr>
                            <tr><td class="row-title"><a href="https://docs.wpvivid.com/get-started-transfer-site.html" target="_blank">'.__('Migrate WordPress', 'wpvivid-backuprestore').'</a></td></tr>
                        </tbody>
                    </table>
                </div>
             </div>';
        return $html;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * 
     */
    public function enqueue_styles()
    {
        $this->screen_ids=apply_filters('wpvivid_get_screen_ids',$this->screen_ids);
        if(in_array(get_current_screen()->id,$this->screen_ids))
        {
            wp_enqueue_style($this->plugin_name, WPVIVID_PLUGIN_DIR_URL . 'css/wpvivid-admin.css', array(), $this->version, 'all');
            do_action('wpvivid_do_enqueue_styles');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * 
     */
    public function enqueue_scripts()
    {
        $this->screen_ids=apply_filters('wpvivid_get_screen_ids',$this->screen_ids);

        if(in_array(get_current_screen()->id,$this->screen_ids))
        {
            wp_enqueue_script($this->plugin_name, WPVIVID_PLUGIN_DIR_URL . 'js/wpvivid-admin.js', array('jquery'), $this->version, false);
            wp_localize_script($this->plugin_name, 'wpvivid_ajax_object', array('ajax_url' => admin_url('admin-ajax.php'),'ajax_nonce'=>wp_create_nonce('wpvivid_ajax')));

            wp_localize_script($this->plugin_name, 'wpvividlion', array(
                'warning' => __('Warning:', 'wpvivid-backuprestore'),
                'error' => __('Error:', 'wpvivid-backuprestore'),
                'remotealias' => __('Warning: An alias for remote storage is required.', 'wpvivid-backuprestore'),
                'remoteexist' => __('Warning: The alias already exists in storage list.', 'wpvivid-backuprestore'),
                'backup_calc_timeout' => __('Calculating the size of files, folder and database timed out. If you continue to receive this error, please go to the plugin settings, uncheck \'Calculate the size of files, folder and database before backing up\', save changes, then try again.', 'wpvivid-backuprestore'),
                'restore_step1' => __('Step One: In the backup list, click the \'Restore\' button on the backup you want to restore. This will bring up the restore tab', 'wpvivid-backuprestore'),
                'restore_step2' => __('Step Two: Choose an option to complete restore, if any', 'wpvivid-backuprestore'),
                'restore_step3' => __('Step Three: Click \'Restore\' button', 'wpvivid-backuprestore'),
                'get_key_step1' => __('1. Visit Key tab page of WPvivid backup plugin of destination site.', 'wpvivid-backuprestore'),
                'get_key_step2' => __('2. Generate a key by clicking Generate button and copy it.', 'wpvivid-backuprestore'),
                'get_key_step3' => __('3. Go back to this page and paste the key in key box below. Lastly, click Save button.', 'wpvivid-backuprestore'),
            ));

            wp_enqueue_script('plupload-all');
            do_action('wpvivid_do_enqueue_scripts');
        }
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * 
     */
    public function add_plugin_admin_menu()
    {

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
        $menu['page_title']= 'WPvivid Backup';
        $menu['menu_title']= 'WPvivid Backup';
        $menu['capability']='administrator';
        $menu['menu_slug']= $this->plugin_name;
        $menu['function']=array($this, 'display_plugin_setup_page');
        $menu['icon_url']='dashicons-cloud';
        $menu['position']=100;
        $menu = apply_filters('wpvivid_get_main_admin_menus', $menu);
        add_menu_page( $menu['page_title'],$menu['menu_title'], $menu['capability'], $menu['menu_slug'], $menu['function'], $menu['icon_url'], $menu['position']);
        $this->submenus = apply_filters('wpvivid_get_admin_menus', $this->submenus);
        usort($this->submenus, function ($a, $b) {
            if ($a['index'] == $b['index'])
                return 0;

            if ($a['index'] > $b['index'])
                return 1;
            else
                return -1;
        });
        foreach ($this->submenus as $submenu) {
            add_submenu_page(
                $submenu['parent_slug'],
                $submenu['page_title'],
                $submenu['menu_title'],
                $submenu['capability'],
                $submenu['menu_slug'],
                $submenu['function']);
        }
    }

    function add_toolbar_items($wp_admin_bar)
    {
        if (is_multisite())
        {
            if(!is_network_admin())
            {
                return ;
            }
        }
        
        global $wpvivid_plugin;
        if(is_admin())
        {
            $show_admin_bar = $wpvivid_plugin->get_admin_bar_setting();
            if ($show_admin_bar === true)
            {
                $this->toolbar_menus = apply_filters('wpvivid_get_toolbar_menus', $this->toolbar_menus);
                foreach ($this->toolbar_menus as $menu)
                {
                    $wp_admin_bar->add_menu(array(
                        'id' => $menu['id'],
                        'title' => $menu['title']
                    ));
                    if (isset($menu['child']))
                    {
                        usort($menu['child'], function ($a, $b)
                        {
                            if($a['index']==$b['index'])
                                return 0;

                            if($a['index']>$b['index'])
                                return 1;
                            else
                                return -1;
                        });
                        foreach ($menu['child'] as $child_menu) {
                            if(isset($child_menu['capability']) && current_user_can($child_menu['capability'])) {
                                $wp_admin_bar->add_menu(array(
                                    'id' => $child_menu['id'],
                                    'parent' => $menu['id'],
                                    'title' => $child_menu['title'],
                                    'href' => $child_menu['href']
                                ));
                            }
                        }
                    }
                }
            }
        }
    }

    public function add_action_links( $links )
    {
        $active_plugins = get_option('active_plugins');
        if(!function_exists('get_plugins'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $plugins=get_plugins();
        $pro_wpvivid_slug='wpvivid-backup-pro/wpvivid-backup-pro.php';
        $is_active_pro=false;
        if(!empty($plugins))
        {
            if(isset($plugins[$pro_wpvivid_slug]))
            {
                if(in_array($pro_wpvivid_slug, $active_plugins))
                {
                    $is_active_pro=true;
                }
            }
        }

        if($is_active_pro)
        {
            $settings_link = array(
                '<a href="' . admin_url( 'admin.php?page=' . strtolower(sprintf('%s-dashboard', apply_filters('wpvivid_white_label_slug', 'wpvivid'))) ) . '">' . __('Settings', $this->plugin_name) . '</a>',
            );
        }
        else
        {
            $settings_link = array(
                '<a href="' . admin_url( 'admin.php?page=' . apply_filters('wpvivid_white_label_slug', $this->plugin_name) ) . '">' . __('Settings', $this->plugin_name) . '</a>',
            );
        }

        return array_merge(  $settings_link, $links );
    }

    public static function wpvivid_get_siteurl(){
        $wpvivid_siteurl = array();
        $wpvivid_siteurl['home_url'] = home_url();
        $wpvivid_siteurl['plug_url'] = plugins_url();
        $wpvivid_siteurl['site_url'] = get_option( 'siteurl' );
        return $wpvivid_siteurl;
    }

    /**
     * Render the settings page for this plugin.
     *
     * 
     */
    public function display_plugin_setup_page()
    {
        do_action('wpvivid_before_setup_page');

        add_action('wpvivid_display_page',array($this,'display'));

        do_action('wpvivid_display_page');
    }

    public function migrate_notice()
    {
        $migrate_notice=false;
        $migrate_status=WPvivid_Setting::get_option('wpvivid_migrate_status');
        if(!empty($migrate_status) && $migrate_status == 'completed')
        {
            $migrate_notice=true;
            echo '<div class="notice notice-warning is-dismissible"><p>'.__('Migration is complete and htaccess file is replaced. In order to successfully complete the migration, you\'d better reinstall 301 redirect plugin, firewall and security plugin, and caching plugin if they exist.').'</p></div>';
            WPvivid_Setting::delete_option('wpvivid_migrate_status');
        }
        $restore = new WPvivid_restore_data();
        if ($restore->has_restore())
        {
            $restore_status = $restore->get_restore_status();
            if ($restore_status === WPVIVID_RESTORE_COMPLETED)
            {
                $restore->clean_restore_data();
                do_action('wpvivid_rebuild_backup_list');
                $need_review=WPvivid_Setting::get_option('wpvivid_need_review');
                if($need_review=='not')
                {
                    WPvivid_Setting::update_option('wpvivid_need_review','show');
                    $msg = __('Cheers! WPvivid Backup plugin has restored successfully your website. If you found WPvivid Backup plugin helpful, a 5-star rating would be highly appreciated, which motivates us to keep providing new features.', 'wpvivid-backuprestore');
                    WPvivid_Setting::update_option('wpvivid_review_msg',$msg);
                }
                else{
                    if(!$migrate_notice)
                    {
                        echo '<div class="notice notice-success is-dismissible"><p>'.__('Restore completed successfully.').'</p></div>';
                    }
                }
            }
        }
    }

    public function display()
    {
        include_once('partials/wpvivid-admin-display.php');
    }

    public static function wpvivid_get_page_request()
    {
        $request_page='wpvivid_tab_general';

        if(isset($_REQUEST['wpvivid-remote-page-mainwp'])){
            $request_page='wpvivid_tab_remote_storage';
        }
        if(isset($_REQUEST['tab-backup']))
        {
            $request_page='wpvivid_tab_general';
        }
        else if(isset($_REQUEST['tab-schedule']))
        {
            $request_page='wpvivid_tab_schedule';
        }
        else if(isset($_REQUEST['tab-transfer']))
        {
            $request_page='wpvivid_tab_migrate';
        }
        else if(isset($_REQUEST['tab-remote-storage']))
        {
            $request_page='wpvivid_tab_remote_storage';
        }
        else if(isset($_REQUEST['tab-settings']))
        {
            $request_page='wpvivid_tab_setting';
        }
        else if(isset($_REQUEST['tab-website-info']))
        {
            $request_page='wpvivid_tab_debug';
        }
        else if(isset($_REQUEST['tab-logs']))
        {
            $request_page='wpvivid_tab_log';
        }
        else if(isset($_REQUEST['tab-key']))
        {
            $request_page='wpvivid_tab_key';
        }
        else if(isset($_REQUEST['tab-mainwp']))
        {
            $request_page='wpvivid_tab_mainwp';
        }
        else if(isset($_REQUEST['page'])&&$_REQUEST['page']=='wpvivid-pro')
        {
            $request_page='wpvivid_tab_pro';
        }
        else if(isset($_REQUEST['page'])&&$_REQUEST['page']=='wpvivid-setting')
        {
            $request_page='wpvivid_tab_setting';
        }

        $request_page=apply_filters('wpvivid_set_page_request',$request_page);

        return $request_page;
    }

    public static function show_add_my_review()
    {
        $review = WPvivid_Setting::get_option('wpvivid_need_review');
        $review_msg = WPvivid_Setting::get_option('wpvivid_review_msg');
        if (empty($review))
        {
            WPvivid_Setting::update_option('wpvivid_need_review', 'not');
        } else {
            if ($review == 'not')
            {
            }
            else if ($review == 'show')
            {
                if(!empty($review_msg))
                {
                    echo '<div class="notice notice-info is-dismissible" id="wpvivid_notice_rate">
                    <p>' . $review_msg . '</p>
                    <div style="padding-bottom: 10px;">
                    <span><input type="button" class="button-primary" option="review" name="rate-now" value="'.esc_attr('Rate Us', 'wpvivid-backuprestore').'" /></span>
                    <span><input type="button" class="button-secondary" option="review" name="ask-later" value="'.esc_attr('Maybe Later', 'wpvivid-backuprestore').'" /></span>
                    <span><input type="button" class="button-secondary" option="review" name="never-ask" value="'.esc_attr('Never', 'wpvivid-backuprestore').'" /></span>
                    <span><input type="button" class="button-secondary" option="review" name="already-done" value="'.esc_attr('Already Done', 'wpvivid-backuprestore').'" /></span>
                    </div>
                    </div>';
                }
            } else if ($review == 'do_not_ask')
            {
            } else
                {
                if (time() > $review)
                {
                    if(!empty($review_msg))
                    {
                        echo '<div class="notice notice-info is-dismissible" id="wpvivid_notice_rate">
                        <p>' . $review_msg . '</p>
                        <div style="padding-bottom: 10px;">
                        <span><input type="button" class="button-primary" option="review" name="rate-now" value="'.esc_attr('Rate Us', 'wpvivid-backuprestore').'" /></span>    
                        <span><input type="button" class="button-secondary" option="review" name="ask-later" value="'.esc_attr('Maybe Later', 'wpvivid-backuprestore').'" /></span>
                        <span><input type="button" class="button-secondary" option="review" name="never-ask" value="'.esc_attr('Never', 'wpvivid-backuprestore').'" /></span>
                        <span><input type="button" class="button-secondary" option="review" name="already-done" value="'.esc_attr('Already Done', 'wpvivid-backuprestore').'" /></span>
                        </div>
                        </div>';
                    }
                }
            }
        }
    }

    public function check_amazons3()
    {
        $remoteslist=WPvivid_Setting::get_all_remote_options();
        $need_amazons3_notice = false;
        if(isset($remoteslist) && !empty($remoteslist))
        {
            foreach ($remoteslist as $remote_id => $value)
            {
                if($remote_id === 'remote_selected')
                {
                    continue;
                }
                if($value['type'] == 'amazons3' && isset($value['s3Path']))
                {
                    $need_amazons3_notice = true;
                }
                if($value['type'] == 's3compat' && isset($value['s3directory']))
                {
                    $need_amazons3_notice = true;
                }
            }
        }
        if($need_amazons3_notice)
        {
            $amazons3_notice = WPvivid_Setting::get_option('wpvivid_amazons3_notice', 'not init');
            if($amazons3_notice === 'not init')
            {
                $notice_message = __('As Amazon S3 and DigitalOcean Space have upgraded their connection methods, please delete the previous connections and re-add your Amazon S3/DigitalOcean Space accounts to make sure the connections work.', 'wpvivid-backuprestore');
                echo '<div class="notice notice-warning" id="wpvivid_amazons3_notice">
                        <p>' . $notice_message . '</p>
                        <div style="padding-bottom: 10px;">
                        <span><input type="button" class="button-secondary" value="I Understand" onclick="wpvivid_click_amazons3_notice();" /></span>
                        </div>
                        </div>';
            }
        }
    }

    public function check_dropbox()
    {
        if (is_multisite())
        {
            if(!is_network_admin())
            {
                return ;
            }
        }

        if(!function_exists('get_plugins'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $pro_wpvivid_slug='wpvivid-backup-pro/wpvivid-backup-pro.php';
        if (is_multisite())
        {
            $active_plugins = array();
            //network active
            $mu_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
            if(!empty($mu_active_plugins)){
                foreach ($mu_active_plugins as $plugin_name => $data){
                    $active_plugins[] = $plugin_name;
                }
            }
            $plugins=get_mu_plugins();
            if(count($plugins) == 0 || !isset($plugins[$pro_wpvivid_slug])){
                $plugins=get_plugins();
            }
        }
        else
        {
            $active_plugins = get_option('active_plugins');
            $plugins=get_plugins();
        }

        if(!empty($plugins))
        {
            if(isset($plugins[$pro_wpvivid_slug]))
            {
                if(in_array($pro_wpvivid_slug, $active_plugins))
                {
                    return;
                }
            }

            $remoteslist=WPvivid_Setting::get_all_remote_options();
            $need_dropbox_notice = false;
            if(isset($remoteslist) && !empty($remoteslist))
            {
                foreach ($remoteslist as $remote_id => $value)
                {
                    if($remote_id === 'remote_selected')
                    {
                        continue;
                    }
                    if($value['type'] == 'dropbox' && !isset($value['refresh_token']))
                    {
                        $need_dropbox_notice = true;
                    }
                }
            }
            if($need_dropbox_notice)
            {
                $notice_message = __('Because Dropbox has upgraded their API on September 30, 2021, the new API is no longer compatible with the previous app\'s settings. Please re-add your Dropbox storage to ensure that it works properly.', 'wpvivid-backuprestore');
                echo '<div class="notice notice-warning">
                                    <p>' . $notice_message . '</p>
                               </div>';
            }
        }
    }

    public function check_extensions()
    {
        $common_setting = WPvivid_Setting::get_setting(false, 'wpvivid_common_setting');
        $db_connect_method = isset($common_setting['options']['wpvivid_common_setting']['db_connect_method']) ? $common_setting['options']['wpvivid_common_setting']['db_connect_method'] : 'wpdb';
        $need_php_extensions = array();
        $need_extensions_count = 0;
        $extensions=get_loaded_extensions();
        if(!function_exists("curl_init")){
            $need_php_extensions[$need_extensions_count] = 'curl';
            $need_extensions_count++;
        }
        if(!class_exists('PDO')){
            $need_php_extensions[$need_extensions_count] = 'PDO';
            $need_extensions_count++;
        }
        if(!function_exists("gzopen"))
        {
            $need_php_extensions[$need_extensions_count] = 'zlib';
            $need_extensions_count++;
        }
        if(!array_search('pdo_mysql',$extensions) && $db_connect_method === 'pdo')
        {
            $need_php_extensions[$need_extensions_count] = 'pdo_mysql';
            $need_extensions_count++;
        }
        if(!empty($need_php_extensions)){
            $msg = '';
            $figure = 0;
            foreach ($need_php_extensions as $extension){
                $figure++;
                if($figure == 1){
                    $msg .= $extension;
                }
                else if($figure < $need_extensions_count) {
                    $msg .= ', '.$extension;
                }
                else if($figure == $need_extensions_count){
                    $msg .= ' and '.$extension;
                }
            }
            if($figure == 1){
                echo '<div class="notice notice-error"><p>'.sprintf(__('The %s extension is not detected. Please install the extension first.', 'wpvivid-backuprestore'), $msg).'</p></div>';
            }
            else{
                echo '<div class="notice notice-error"><p>'.sprintf(__('The %s extensions are not detected. Please install the extensions first.', 'wpvivid-backuprestore'), $msg).'</p></div>';
            }
        }

        if (!class_exists('PclZip')) include_once(ABSPATH.'/wp-admin/includes/class-pclzip.php');
        if (!class_exists('PclZip')) {
            echo '<div class="notice notice-error"><p>'.__('Class PclZip is not detected. Please update or reinstall your WordPress.', 'wpvivid-backuprestore').'</p></div>';
        }

        $hide_notice = get_option('wpvivid_hide_wp_cron_notice', false);
        if(defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON && $hide_notice === false){
            echo '<div class="notice notice-error notice-wp-cron is-dismissible"><p>'.__('In order to execute the scheduled backups properly, please set the DISABLE_WP_CRON constant to false. If you are using an external cron system, simply click \'X\' to dismiss this message.', 'wpvivid-backuprestore').'</p></div>';
        }
    }

    public function check_wpvivid_pro_version()
    {
        if (is_multisite())
        {
            if(!is_network_admin())
            {
                return ;
            }
        }

        if(!function_exists('get_plugins'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $pro_wpvivid_slug='wpvivid-backup-pro/wpvivid-backup-pro.php';
        if (is_multisite())
        {
            $active_plugins = array();
            //network active
            $mu_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
            if(!empty($mu_active_plugins)){
                foreach ($mu_active_plugins as $plugin_name => $data){
                    $active_plugins[] = $plugin_name;
                }
            }
            $plugins=get_mu_plugins();
            if(count($plugins) == 0 || !isset($plugins[$pro_wpvivid_slug])){
                $plugins=get_plugins();
            }
        }
        else
        {
            $active_plugins = get_option('active_plugins');
            $plugins=get_plugins();
        }

        if(!empty($plugins))
        {
            if(isset($plugins[$pro_wpvivid_slug]))
            {
                if(in_array($pro_wpvivid_slug, $active_plugins))
                {
                    if(version_compare('2.0.23',$plugins[$pro_wpvivid_slug]['Version'],'>'))
                    {
                        ?>
                        <div class="notice notice-warning" style="padding: 11px 15px;">
                            <?php echo sprintf(__('We detected that you are using a lower version of %s Pro, please update it to 2.0.23 or higher to ensure backing up to Google Drive works properly.', 'wpvivid'), apply_filters('wpvivid_white_label_display', 'WPvivid Backup Plugin')); ?>
                        </div>
                        <?php
                    }
                }
            }
        }
    }

    public function init_js_var()
    {
        global $wpvivid_plugin;

        $loglist=$wpvivid_plugin->get_log_list_ex();
        $remoteslist=WPvivid_Setting::get_all_remote_options();
        $default_remote_storage='';
        foreach ($remoteslist['remote_selected'] as $value)
        {
            $default_remote_storage=$value;
        }
        ?>
        <script>
            var wpvivid_siteurl = '<?php
                $wpvivid_siteurl = array();
                $wpvivid_siteurl=WPvivid_Admin::wpvivid_get_siteurl();
                echo esc_url($wpvivid_siteurl['site_url']);
                ?>';
            var wpvivid_plugurl =  '<?php
                echo WPVIVID_PLUGIN_URL;
                ?>';
            var wpvivid_log_count = '<?php
                _e(sizeof($loglist['log_list']['file']), 'wpvivid-backuprestore');
                ?>';
            var wpvivid_log_array = '<?php
                _e(json_encode($loglist), 'wpvivid-backuprestore');
                ?>';
            var wpvivid_page_request = '<?php
                $page_request = WPvivid_Admin::wpvivid_get_page_request();
                _e($page_request, 'wpvivid-backuprestore');
                ?>';
            var wpvivid_default_remote_storage = '<?php
                _e($default_remote_storage, 'wpvivid-backuprestore');
                ?>';
        </script>
        <?php
    }

    public function wpvivid_add_default_tab_page($page_array){
        $page_array['backup_restore'] = array('index' => '1', 'tab_func' => array($this, 'wpvivid_add_tab_backup_restore'), 'page_func' => array($this, 'wpvivid_add_page_backup'));
        $page_array['schedule'] = array('index' => '2', 'tab_func' => array($this, 'wpvivid_add_tab_schedule'), 'page_func' => array($this, 'wpvivid_add_page_schedule'));
        $page_array['remote_storage'] = array('index' => '4', 'tab_func' => array($this, 'wpvivid_add_tab_remote_storage'), 'page_func' => array($this, 'wpvivid_add_page_remote_storage'));
        $page_array['setting'] = array('index' => '5', 'tab_func' => array($this, 'wpvivid_add_tab_setting'), 'page_func' => array($this, 'wpvivid_add_page_setting'));
        $page_array['website_info'] = array('index' => '6', 'tab_func' => array($this, 'wpvivid_add_tab_website_info'), 'page_func' => array($this, 'wpvivid_add_page_website_info'));
        $page_array['log'] = array('index' => '7', 'tab_func' => array($this, 'wpvivid_add_tab_log_ex'), 'page_func' => array($this, 'wpvivid_add_page_log_ex'));
        $page_array['read_log'] = array('index' => '29', 'tab_func' => array($this, 'wpvivid_add_tab_read_log'), 'page_func' => array($this, 'wpvivid_add_page_read_log'));
        $page_array['premium'] = array('index' => '10', 'tab_func' => array($this, 'wpvivid_add_tab_premium'), 'page_func' => array($this, 'wpvivid_add_page_premium'));
        $hide_mwp_tab_page = get_option('wpvivid_hide_mwp_tab_page_v1', false);
        if($hide_mwp_tab_page === false) {
            $page_array['mwp'] = array('index' => '30', 'tab_func' => array($this, 'wpvivid_add_tab_mwp'), 'page_func' => array($this, 'wpvivid_add_page_mwp'));
        }
        return $page_array;
    }

    public function wpvivid_add_tab_backup_restore(){
        ?>
        <a href="#" id="wpvivid_tab_general" class="nav-tab wrap-nav-tab nav-tab-active" onclick="switchTabs(event,'general-page')"><?php _e('Backup & Restore', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function wpvivid_add_tab_schedule(){
        ?>
        <a href="#" id="wpvivid_tab_schedule" class="nav-tab wrap-nav-tab" onclick="switchTabs(event,'schedule-page')"><?php _e('Schedule', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function wpvivid_add_tab_remote_storage(){
        ?>
        <a href="#" id="wpvivid_tab_remote_storage" class="nav-tab wrap-nav-tab" onclick="switchTabs(event,'storage-page')"><?php _e('Remote Storage', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function wpvivid_add_tab_setting(){
        ?>
        <a href="#" id="wpvivid_tab_setting" class="nav-tab wrap-nav-tab" onclick="switchTabs(event,'settings-page')"><?php _e('Settings', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function wpvivid_add_tab_website_info(){
        ?>
        <a href="#" id="wpvivid_tab_debug" class="nav-tab wrap-nav-tab" onclick="switchTabs(event,'debug-page')"><?php _e('Debug', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function wpvivid_add_tab_log(){
        ?>
        <a href="#" id="wpvivid_tab_log" class="nav-tab log-nav-tab nav-tab-active" onclick="switchlogTabs(event,'logs-page')"><?php _e('Backup Logs', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function wpvivid_add_tab_read_log(){
        ?>
        <a href="#" id="wpvivid_tab_read_log" class="nav-tab wrap-nav-tab delete" onclick="switchTabs(event,'log-read-page')" style="display: none;">
            <div style="margin-right: 15px;"><?php _e('Log', 'wpvivid-backuprestore'); ?></div>
            <div class="nav-tab-delete-img">
                <img src="<?php echo esc_url( WPVIVID_PLUGIN_URL.'/admin/partials/images/delete-tab.png' ); ?>" style="vertical-align:middle; cursor:pointer;" onclick="wpvivid_close_tab(event, 'wpvivid_tab_read_log', 'wrap', 'wpvivid_tab_log');" />
            </div>
        </a>
        <?php
    }

    public function wpvivid_add_tab_mwp(){
        ?>
        <a href="#" id="wpvivid_tab_mainwp" class="nav-tab wrap-nav-tab delete" onclick="switchTabs(event, 'mwp-page')">
            <div style="margin-right: 15px;"><?php _e('MainWP', 'wpvivid-backuprestore'); ?></div>
            <div class="nav-tab-delete-img">
                <img src="<?php echo esc_url(WPVIVID_PLUGIN_URL.'/admin/partials/images/delete-tab.png'); ?>" style="vertical-align:middle; cursor:pointer;" onclick="wpvivid_close_tab(event, 'wpvivid_tab_mainwp', 'wrap', 'wpvivid_tab_general');" />
            </div>
        </a>
        <?php
    }

    public function wpvivid_add_tab_premium(){
        ?>
        <a href="#" id="wpvivid_tab_premium" class="nav-tab wrap-nav-tab" onclick="switchTabs(event,'premium-page')"><?php _e('Premium', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function wpvivid_add_page_backup()
    {
        ?>
        <div id="general-page" class="wrap-tab-content wpvivid_tab_general" name="tab-backup" style="width:100%;">
            <div class="meta-box-sortables ui-sortable">
                <?php
                do_action('wpvivid_backuppage_add_module');
                ?>
                <h2 class="nav-tab-wrapper" id="wpvivid_backup_tab" style="padding-bottom:0!important;">
                <?php
                $backuplist_array = array();
                $backuplist_array = apply_filters('wpvivid_backuppage_load_backuplist', $backuplist_array);
                foreach ($backuplist_array as $list_name) {
                    add_action('wpvivid_backuppage_add_tab', $list_name['tab_func'], $list_name['index']);
                    add_action('wpvivid_backuppage_add_page', $list_name['page_func'], $list_name['index']);
                }
                do_action('wpvivid_backuppage_add_tab');
                ?>
                </h2>
                <?php  do_action('wpvivid_backuppage_add_page'); ?>
            </div>
        </div>
        <script>
            <?php do_action('wpvivid_backup_do_js'); ?>
        </script>
        <?php
    }

    public function wpvivid_add_page_schedule()
    {
        ?>
        <div id="schedule-page" class="wrap-tab-content wpvivid_tab_schedule" name="tab-schedule" style="display: none;">
            <div>
                <table class="widefat">
                    <tbody>
                    <?php do_action('wpvivid_schedule_add_cell'); ?>
                    <tfoot>
                    <tr>
                        <th class="row-title"><input class="button-primary storage-account-button" id="wpvivid_schedule_save" type="submit" name="" value="<?php esc_attr_e( 'Save Changes', 'wpvivid-backuprestore' ); ?>" /></th>
                        <th></th>
                    </tr>
                    </tfoot>
                    </tbody>
                </table>
            </div>
        </div>
        <script>
            jQuery('#wpvivid_schedule_save').click(function(){
                wpvivid_set_schedule();
                wpvivid_settings_changed = false;
            });

            function wpvivid_set_schedule()
            {
                var schedule_data = wpvivid_ajax_data_transfer('schedule');
                var ajax_data = {
                    'action': 'wpvivid_set_schedule',
                    'schedule': schedule_data
                };
                jQuery('#wpvivid_schedule_save').css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function (data) {
                    try {
                        var jsonarray = jQuery.parseJSON(data);

                        jQuery('#wpvivid_schedule_save').css({'pointer-events': 'auto', 'opacity': '1'});
                        if (jsonarray.result === 'success') {
                            location.reload();
                        }
                        else {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err) {
                        alert(err);
                        jQuery('#wpvivid_schedule_save').css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery('#wpvivid_schedule_save').css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('changing schedule', textStatus, errorThrown);
                    alert(error_message);
                });
            }
        </script>
        <?php
    }

    public function wpvivid_add_page_remote_storage()
    {
        ?>
        <div id="storage-page" class="wrap-tab-content wpvivid_tab_remote_storage" name="tab-storage" style="display:none;">
            <div>
                <div class="storage-content" id="storage-brand-2" style="">
                    <div class="postbox">
                        <?php do_action('wpvivid_add_storage_tab'); ?>
                    </div>
                    <div class="postbox storage-account-block" id="wpvivid_storage_account_block">
                        <?php do_action('wpvivid_add_storage_page'); ?>
                    </div>
                    <h2 class="nav-tab-wrapper" style="padding-bottom:0!important;">
                        <?php do_action('wpvivid_storage_add_tab'); ?>
                    </h2>
                    <?php do_action('wpvivid_storage_add_page'); ?>
                </div>
            </div>
        </div>
        <?php
    }

    public function wpvivid_add_page_setting()
    {
        ?>
        <div id="settings-page" class="wrap-tab-content wpvivid_tab_setting" name="tab-setting" style="display:none;">
            <div>
                <h2 class="nav-tab-wrapper" style="padding-bottom:0!important;">
                    <?php
                    $setting_array = array();
                    $setting_array = apply_filters('wpvivid_add_setting_tab_page', $setting_array);
                    foreach ($setting_array as $setting_name) {
                        add_action('wpvivid_settingpage_add_tab', $setting_name['tab_func'], $setting_name['index']);
                        add_action('wpvivid_settingpage_add_page', $setting_name['page_func'], $setting_name['index']);
                    }
                    do_action('wpvivid_settingpage_add_tab');
                    ?>
                </h2>
                <?php do_action('wpvivid_settingpage_add_page'); ?>
                <div><input class="button-primary" id="wpvivid_setting_general_save" type="submit" value="<?php esc_attr_e( 'Save Changes', 'wpvivid-backuprestore' ); ?>" /></div>
            </div>
        </div>
        <script>
            jQuery('#wpvivid_setting_general_save').click(function(){
                wpvivid_set_general_settings();
                wpvivid_settings_changed = false;
            });

            function wpvivid_set_general_settings()
            {
                var setting_data = wpvivid_ajax_data_transfer('setting');
                var ajax_data = {
                    'action': 'wpvivid_set_general_setting',
                    'setting': setting_data
                };
                jQuery('#wpvivid_setting_general_save').css({'pointer-events': 'none', 'opacity': '0.4'});
                wpvivid_post_request(ajax_data, function (data) {
                    try {
                        var jsonarray = jQuery.parseJSON(data);

                        jQuery('#wpvivid_setting_general_save').css({'pointer-events': 'auto', 'opacity': '1'});
                        if (jsonarray.result === 'success') {
                            location.reload();
                        }
                        else {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err) {
                        alert(err);
                        jQuery('#wpvivid_setting_general_save').css({'pointer-events': 'auto', 'opacity': '1'});
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    jQuery('#wpvivid_setting_general_save').css({'pointer-events': 'auto', 'opacity': '1'});
                    var error_message = wpvivid_output_ajaxerror('changing base settings', textStatus, errorThrown);
                    alert(error_message);
                });
            }
        </script>
        <?php
    }

    public function wpvivid_add_tab_log_ex(){
        ?>
        <a href="#" id="wpvivid_tab_log_ex" class="nav-tab wrap-nav-tab" onclick="switchTabs(event,'logs-page-ex')"><?php _e('Logs', 'wpvivid-backuprestore'); ?></a>
        <?php
    }

    public function wpvivid_add_page_log_ex()
    {
        ?>
         <div id="logs-page-ex" class="wrap-tab-content wpvivid_tab_log" name="tab-logs" style="display:none;">
             <div>
                 <h2 class="nav-tab-wrapper" style="padding-bottom:0!important;">
                     <?php
                     $setting_array = array();
                     $setting_array = apply_filters('wpvivid_add_log_tab_page', $setting_array);
                     foreach ($setting_array as $setting_name) {
                         add_action('wpvivid_logpage_add_tab', $setting_name['tab_func'], $setting_name['index']);
                         add_action('wpvivid_logpage_add_page', $setting_name['page_func'], $setting_name['index']);
                     }
                     do_action('wpvivid_logpage_add_tab');
                     ?>
                 </h2>
                 <?php do_action('wpvivid_logpage_add_page'); ?>
             </div>
         </div>
        <?php
    }

    public function wpvivid_add_page_website_info()
    {
        ?>
        <div id="debug-page" class="wrap-tab-content wpvivid_tab_debug" name="tab-debug" style="display:none;">
            <table class="widefat">
                <div style="padding: 0 0 20px 10px;"><?php _e('There are two ways available to send us the debug information. The first one is recommended.', 'wpvivid-backuprestore'); ?></div>
                <div style="padding-left: 10px;">
                    <strong><?php _e('Method 1.'); ?></strong> <?php _e('If you have configured SMTP on your site, enter your email address and click the button below to send us the relevant information (website info and errors logs) when you are encountering errors. This will help us figure out what happened. Once the issue is resolved, we will inform you by your email address.', 'wpvivid-backuprestore'); ?>
                </div>
                <div style="padding:10px 10px 0">
                    <span class="wpvivid-element-space-right"><?php echo __('WPvivid support email:', 'wpvivid-backuprestore'); ?></span><input type="text" id="wpvivid_support_mail" value="support@wpvivid.com" readonly />
                    <span class="wpvivid-element-space-right"><?php _e('Your email:', 'wpvivid-backuprestore'); ?></span><input type="text" id="wpvivid_user_mail" />
                </div>
                <div style="padding:10px 10px 0">
                    <div style="float: left;">
                        <div class="wpvivid-element-space-bottom wpvivid-text-space-right wpvivid-debug-text-fix" style="float: left;">
                            <?php _e('I am using:', 'wpvivid-backuprestore'); ?>
                        </div>
                        <div class="wpvivid-element-space-bottom wpvivid-text-space-right" style="float: left;">
                            <select id="wpvivid_debug_type">
                                <option selected="selected" value="sharehost">share hosting</option>
                                <option value="vps">VPS hosting</option>
                            </select>
                        </div>
                        <div style="clear: both;"></div>
                    </div>
                    <div id="wpvivid_debug_host" style="float: left;">
                        <div class="wpvivid-element-space-bottom wpvivid-text-space-right wpvivid-debug-text-fix" style="float: left;">
                            <?php _e('My web hosting provider is:', 'wpvivid-backuprestore'); ?>
                        </div>
                        <div class="wpvivid-element-space-bottom wpvivid-text-space-right" style="float: left;">
                            <input type="text" id="wpvivid_host_provider"/></div>
                        <div style="clear: both;"></div>
                    </div>
                    <div style="clear: both;"></div>
                </div>
                <div style="padding:0 10px;">
                    <textarea id="wpvivid_debug_comment" class="wp-editor-area" style="width:100%; height: 200px;" autocomplete="off" cols="60" placeholder="<?php esc_attr_e('Please describe your problem here.', 'wpvivid-backuprestore'); ?>" ></textarea>
                </div>
                <div class="schedule-tab-block">
                    <input class="button-primary" type="submit" value="<?php esc_attr_e( 'Send Debug Information to Us', 'wpvivid-backuprestore' ); ?>" onclick="wpvivid_click_send_debug_info();" />
                </div>
                <div style="clear:both;"></div>
                <div style="padding-left: 10px;">
                    <strong><?php _e('Method 2.'); ?></strong> <?php _e('If you didn’t configure SMTP on your site, click the button below to download the relevant information (website info and error logs) to your PC when you are encountering some errors. Sending the files to us will help us diagnose what happened.', 'wpvivid-backuprestore'); ?>
                </div>
                <div class="schedule-tab-block">
                    <input class="button-primary" id="wpvivid_download_website_info" type="submit" name="download-website-info" value="<?php esc_attr_e( 'Download', 'wpvivid-backuprestore' ); ?>" />
                </div>
                <thead class="website-info-head">
                <tr>
                    <th class="row-title" style="min-width: 260px;"><?php _e( 'Website Info Key', 'wpvivid-backuprestore' ); ?></th>
                    <th><?php _e( 'Website Info Value', 'wpvivid-backuprestore' ); ?></th>
                </tr>
                </thead>
                <tbody class="wpvivid-websiteinfo-list" id="wpvivid_websiteinfo_list">
                <?php
                global $wpvivid_plugin;
                $website_info=$wpvivid_plugin->get_website_info();
                if(!empty($website_info['data'])){
                    foreach ($website_info['data'] as $key=>$value) { ?>
                        <?php
                        $website_value='';
                        if (is_array($value)) {
                            foreach ($value as $arr_value) {
                                if (empty($website_value)) {
                                    $website_value = $website_value . $arr_value;
                                } else {
                                    $website_value = $website_value . ', ' . $arr_value;
                                }
                            }
                        }
                        else{
                            if($value === true || $value === false){
                                if($value === true) {
                                    $website_value = 'true';
                                }
                                else{
                                    $website_value = 'false';
                                }
                            }
                            else {
                                $website_value = $value;
                            }
                        }
                        ?>
                        <tr>
                            <td class="row-title tablelistcolumn"><label for="tablecell"><?php _e($key, 'wpvivid-backuprestore'); ?></label></td>
                            <td class="tablelistcolumn"><?php _e($website_value, 'wpvivid-backuprestore'); ?></td>
                        </tr>
                    <?php }} ?>
                </tbody>
            </table>
        </div>
        <script>
            jQuery('#wpvivid_download_website_info').click(function(){
                wpvivid_download_website_info();
            });

            /**
             * Download the relevant website info and error logs to your PC for debugging purposes.
             */
            function wpvivid_download_website_info(){
                wpvivid_location_href=true;
                location.href =ajaxurl+'?_wpnonce='+wpvivid_ajax_object.ajax_nonce+'&action=wpvivid_create_debug_package';
            }

            jQuery("#wpvivid_debug_type").change(function()
            {
                if(jQuery(this).val()=='sharehost')
                {
                    jQuery("#wpvivid_debug_host").show();
                }
                else
                {
                    jQuery("#wpvivid_debug_host").hide();
                }
            });

            function wpvivid_click_send_debug_info(){
                var wpvivid_user_mail = jQuery('#wpvivid_user_mail').val();
                var server_type = jQuery('#wpvivid_debug_type').val();
                var host_provider = jQuery('#wpvivid_host_provider').val();
                var comment = jQuery('#wpvivid_debug_comment').val();

                var ajax_data = {
                    'action': 'wpvivid_send_debug_info',
                    'user_mail': wpvivid_user_mail,
                    'server_type':server_type,
                    'host_provider':host_provider,
                    'comment':comment
                };
                wpvivid_post_request(ajax_data, function (data) {
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === "success") {
                            alert("Send succeeded.");
                        }
                        else {
                            alert(jsonarray.error);
                        }
                    }
                    catch (err) {
                        alert(err);
                    }
                }, function (XMLHttpRequest, textStatus, errorThrown) {
                    var error_message = wpvivid_output_ajaxerror('sending debug information', textStatus, errorThrown);
                    alert(error_message);
                });
            }
        </script>
        <?php
    }

    public function wpvivid_add_page_log()
    {
        global $wpvivid_plugin;
        $display_log_count=array(0=>"10",1=>"20",2=>"30",3=>"40",4=>"50");
        $max_log_diaplay=20;
        $loglist=$wpvivid_plugin->get_log_list_ex();
        ?>
        <div id="logs-page" class="log-tab-content wpvivid_tab_log" name="tab-logs">
            <table class="wp-list-table widefat plugins">
                <thead class="log-head">
                <tr>
                    <th class="row-title"><?php _e( 'Date', 'wpvivid-backuprestore' ); ?></th>
                    <th><?php _e( 'Log Type', 'wpvivid-backuprestore' ); ?></th>
                    <th><?php _e( 'Log File Name', 'wpvivid-backuprestore' ); ?></th>
                    <th><?php _e( 'Action', 'wpvivid-backuprestore' ); ?></th>
                </tr>
                </thead>
                <tbody class="wpvivid-loglist" id="wpvivid_loglist">
                <?php
                $html = '';
                $html = apply_filters('wpvivid_get_log_list', $html);
                echo $html['html'];
                ?>
                </tbody>
            </table>
            <div style="padding-top: 10px; text-align: center;">
                <input class="button-secondary log-page" id="wpvivid_pre_log_page" type="submit" value="<?php esc_attr_e( ' < Pre page ', 'wpvivid-backuprestore' ); ?>" />
                <div style="font-size: 12px; display: inline-block; padding-left: 10px;">
                                <span id="wpvivid_log_page_info" style="line-height: 35px;">
                                    <?php
                                    $current_page=1;
                                    $max_page=ceil(sizeof($loglist['log_list']['file'])/$max_log_diaplay);
                                    if($max_page == 0) $max_page = 1;
                                    echo $current_page.' / '.$max_page;
                                    ?>
                                </span>
                </div>
                <input class="button-secondary log-page" id="wpvivid_next_log_page" type="submit" value="<?php esc_attr_e( ' Next page > ', 'wpvivid-backuprestore' ); ?>" />
                <div style="float: right;">
                    <select name="" id="wpvivid_display_log_count">
                        <?php
                        foreach ($display_log_count as $value){
                            if($value == $max_log_diaplay){
                                echo '<option selected="selected" value="' . $value . '">' . $value . '</option>';
                            }
                            else {
                                echo '<option value="' . $value . '">' . $value . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <script>
            jQuery('#wpvivid_display_log_count').on("change", function(){
                wpvivid_display_log_page();
            });

            jQuery('#wpvivid_pre_log_page').click(function(){
                wpvivid_pre_log_page();
            });

            jQuery('#wpvivid_next_log_page').click(function(){
                wpvivid_next_log_page();
            });

            function wpvivid_pre_log_page(){
                if(wpvivid_cur_log_page > 1){
                    wpvivid_cur_log_page--;
                }
                wpvivid_display_log_page();
            }

            function wpvivid_next_log_page(){
                var display_count = jQuery("#wpvivid_display_log_count option:selected").val();
                var max_pages=Math.ceil(wpvivid_log_count/display_count);
                if(wpvivid_cur_log_page < max_pages){
                    wpvivid_cur_log_page++;
                }
                wpvivid_display_log_page();
            }

            function wpvivid_display_log_page(){
                var display_count = jQuery("#wpvivid_display_log_count option:selected").val();
                var max_pages=Math.ceil(wpvivid_log_count/display_count);
                if(max_pages == 0) max_pages = 1;
                jQuery('#wpvivid_log_page_info').html(wpvivid_cur_log_page+ " / "+max_pages);

                var begin = (wpvivid_cur_log_page - 1) * display_count;
                var end = parseInt(begin) + parseInt(display_count);
                jQuery("#wpvivid_loglist tr").hide();
                jQuery('#wpvivid_loglist tr').each(function(i){
                    if (i >= begin && i < end)
                    {
                        jQuery(this).show();
                    }
                });
            }

            function wpvivid_retrieve_log_list()
            {
                var ajax_data = {
                    'action': 'wpvivid_get_log_list'
                };
                wpvivid_post_request(ajax_data, function(data){
                    try {
                        var jsonarray = jQuery.parseJSON(data);
                        if (jsonarray.result === "success") {
                            jQuery('#wpvivid_loglist').html("");
                            jQuery('#wpvivid_loglist').append(jsonarray.html);
                            wpvivid_log_count = jsonarray.log_count;
                            wpvivid_display_log_page();
                        }
                    }
                    catch(err){
                        alert(err);
                    }
                }, function(XMLHttpRequest, textStatus, errorThrown) {
                    setTimeout(function () {
                        wpvivid_retrieve_log_list();
                    }, 3000);
                });
            }
        </script>
        <?php
    }

    public function wpvivid_add_page_read_log()
    {
        ?>
        <div id="log-read-page" class="wrap-tab-content wpvivid_tab_read_log" style="display:none;">
            <div class="postbox restore_log" id="wpvivid_read_log_content">
                <div></div>
            </div>
        </div>
        <?php
    }

    public function wpvivid_add_page_mwp()
    {
        ?>
        <div id="mwp-page" class="wrap-tab-content wpvivid_tab_mainwp" name="tab-mwp" style="display:none;">
            <div style="padding: 10px; background-color: #fff;">
                <div style="margin-bottom: 10px;">
                    <?php echo __('If you are a MainWP user, you can set up and control WPvivid Backup Free and Pro for every child site directly from your MainWP dashboard, using our WPvivid Backup for MainWP extension.', 'wpvivid-backuprestore'); ?>
                </div>
                <div style="margin-bottom: 10px;">
                    <input type="button" class="button-primary" id="wpvivid_download_mainwp_extension" value="<?php esc_attr_e('Download WPvivid Backup for MainWP', 'wpvivid-backuprestore'); ?>" />
                </div>
                <div style="margin-bottom: 10px;">
                    <?php _e('1. Create and download backups for a specific child site', 'wpvivid-backuprestore'); ?>
                </div>
                <div style="margin-bottom: 10px;">
                    <?php _e('2. Set backup schedules for all child sites', 'wpvivid-backuprestore'); ?>
                </div>
                <div style="margin-bottom: 10px;">
                    <?php
                    echo __('3. Set WPvivid Backup Free and Pro settings for all child sites', 'wpvivid-backuprestore');
                    ?>
                </div>
                <div style="margin-bottom: 10px;">
                    <?php
                    echo __('4. Install, claim and update WPvivid Backup Pro for child sites in bulk', 'wpvivid-backuprestore');
                    ?>
                </div>
                <div>
                    <?php
                    echo __('5. Set up remote storage for child sites in bulk (for WPvivid Backup Pro only)', 'wpvivid-backuprestore');
                    ?>
                </div>
            </div>
        </div>
        <script>
            jQuery('#wpvivid_download_mainwp_extension').click(function(){
                var tempwindow=window.open('_blank');
                tempwindow.location='https://wordpress.org/plugins/wpvivid-backup-mainwp';
            });
            jQuery('#wpvivid_ask_for_discount').click(function(){
                var tempwindow=window.open('_blank');
                tempwindow.location='https://wpvivid.com/wpvivid-backup-for-mainwp';
            });
        </script>
        <?php
    }

    public function wpvivid_add_page_premium(){
        ?>
        <div id="premium-page" class="wrap-tab-content wpvivid_tab_premium" name="tab-premium" style="display: none;">
            <table class="wp-list-table widefat plugins" style="border-collapse: collapse;">
                <thead>
                <tr class="backup-list-head" style="border-bottom: 0;">
                    <th><?php _e('Features', 'wpvivid-backuprestore'); ?></th>
                    <th style="text-align:center;"><?php _e('Blogger', 'wpvivid-backuprestore'); ?></th>
                    <th style="text-align:center;"><?php _e('Freelancer', 'wpvivid-backuprestore'); ?></th>
                    <th style="text-align:center;"><?php _e('Small Business', 'wpvivid-backuprestore'); ?></th>
                    <th style="text-align:center;"><?php _e('Ultimate', 'wpvivid-backuprestore'); ?></th>
                </tr>
                </thead>
                <tbody class="wpvivid-backuplist">
                <tr style="">
                    <td>
                        <p><strong><?php _e('Domains', 'wpvivid-backuprestore'); ?></strong></p>
                        <p><strong><?php _e('Backup & Migration Pro', 'wpvivid-backuprestore'); ?></strong></p>
                        <p><strong><?php _e('Image Optimization Pro (Unlimited/domain)', 'wpvivid-backuprestore'); ?></strong></p>
                        <p><strong><?php _e('Mulitsite Support', 'wpvivid-backuprestore'); ?></strong></p>
                        <p><strong><?php _e('Staging Pro', 'wpvivid-backuprestore'); ?></strong></p>
                        <p><strong><?php _e('White Label', 'wpvivid-backuprestore'); ?></strong></p>
                        <p><strong><?php _e('Roles & Capabilities', 'wpvivid-backuprestore'); ?></strong></p>
                    </td>
                    <td style="text-align:center;">
                        <p><span style="color: #81d742;"><?php _e('2 domains', 'wpvivid-backuprestore'); ?></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #f1f1f1;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #f1f1f1;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #f1f1f1;border-radius: 50%;display: inline-block;"></span></p>
                    </td>
                    <td style="text-align:center;">
                        <p><span style="color: #81d742;"><?php _e('Up to 10 domains', 'wpvivid-backuprestore'); ?></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                    </td>
                    <td style="text-align:center;">
                        <p><span style="color: #81d742;"><?php _e('Up to 50 domains', 'wpvivid-backuprestore'); ?></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                    </td>
                    <td style="text-align:center;">
                        <p><span style="color: #81d742;"><?php _e('Unlimited', 'wpvivid-backuprestore'); ?></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                        <p><span style="height: 12px;width: 12px;background-color: #81d742;border-radius: 50%;display: inline-block;"></span></p>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <th><?php _e('*No credit card needed. Trial starts with the Free Trial plan with 2 sites. You can choose a plan at the end of the trial.', 'wpvivid-backuprestore'); ?></th>
                    <th colspan="4" style="text-align:center;"><p style="margin-top: 6px;"><a href="https://wpvivid.com/pricing" class="page-title-action"><?php _e('START 14-DAY FREE TRIAL', 'wpvivid-backuprestore'); ?></a></p></th>
                </tr>
                </tfoot>
            </table>
        </div>
        <?php
    }
}