<?php

namespace WPDM\Admin;

use WPDM\__\__;
use WPDM\__\Email;
use WPDM\Admin\Menu\AddOns;
use WPDM\Admin\Menu\Categories;
use WPDM\Admin\Menu\Packages;
use WPDM\Admin\Menu\Settings;
use WPDM\Admin\Menu\Stats;
use WPDM\Admin\Menu\Templates;
use WPDM\Admin\Menu\Welcome;

class AdminController {

    function __construct() {
        new Welcome();
        new Packages();
        new Categories();
        new Templates();
        new AddOns();
        new Stats();
        new Settings();
        new DashboardWidgets();
        $this->actions();
        $this->filters();
    }

    function actions() {
        add_action('init', array($this, 'registerScripts'), 1);
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'), 1);
        add_action('admin_init', array($this, 'metaBoxes'), 0);
        add_action('admin_init', array(new Email(), 'preview'));
        add_action('admin_head', array($this, 'adminHead'));
        add_action('admin_footer', array($this, 'adminFooter'));

	    add_action("admin_notices", [$this, 'notices']);
        add_action("wp_ajax_wpdm_remove_admin_notice", [$this, 'removeNotices']);
        add_action("wp_ajax_hide_wpdmpro_notice", [$this, 'hideProNotice']);

        add_filter("user_row_actions", [$this, 'usersRowAction'], 10, 2);


    }

    function filters(){
        add_filter("plugin_row_meta", [$this, 'pluginRowMeta'], 10, 4);
    }

    function pluginRowMeta($plugin_meta, $plugin_file, $plugin_data, $status){
        if($plugin_file === 'download-manager/download-manager.php') {
            $plugin_meta[] = "<strong><a href='https://wordpress.org/plugins/download-manager/#developers' target='_blank'>" . __("Changelog", "download-manager") . "</a></strong>";
            $plugin_meta[] = "<a href='https://www.wpdownloadmanager.com/docs/' target='_blank'>" . __("Docs", "download-manager") . "</a>";
            $plugin_meta[] = "<a href='https://www.wpdownloadmanager.com/support/' target='_blank'>" . __("Support Forum", "download-manager") . "</a>";
        }
        return $plugin_meta;
    }

    function registerScripts(){
        wp_register_script('wpdm-admin-bootstrap', WPDM_BASE_URL.'assets/bootstrap3/js/bootstrap.min.js', array('jquery'));
        //wp_register_script('wpdm-bootstrap', WPDM_BASE_URL.'assets/bootstrap/js/bootstrap.min.js', array('jquery'));
        wp_register_style('wpdm-admin-bootstrap', WPDM_BASE_URL.'assets/bootstrap3/css/bootstrap.css');
        //wp_register_style('wpdm-bootstrap', WPDM_BASE_URL.'assets/bootstrap/css/bootstrap.min.css');
        //wp_register_style('wpdm-font-awesome', WPDM_BASE_URL . 'assets/fontawesome/css/all.css');
        wp_register_style('wpdm-font-awesome', WPDM_FONTAWESOME_URL);
        //wp_register_style('wpdm-front3', WPDM_BASE_URL . 'assets/css/front3.css');
        //wp_register_style('wpdm-front', WPDM_BASE_URL . 'assets/css/front.css');
    }

    /**
     * Enqueue admin scripts & styles
     */
    function enqueueScripts() {

        global $pagenow;
        $allow_bscss = array('profile.php', 'user-edit.php',  'upload.php');
        $wpdm_pages = array( 'settings', 'emails', 'wpdm-stats', 'templates', 'importable-files', 'wpdm-addons', 'orders', 'pp-license');
        if (wpdm_query_var('post_type') === 'wpdmpro' || get_post_type() === 'wpdmpro' || in_array(wpdm_query_var('page'), $wpdm_pages) || ($pagenow == 'index.php' && wpdm_query_var('page') == '') || in_array($pagenow, $allow_bscss)) {
            //wpdmdd("OK");
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-form');
            wp_enqueue_script('jquery-ui-core');
            //wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-ui-slider');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-timepicker', WPDM_BASE_URL . 'assets/js/jquery-ui-timepicker-addon.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider'));
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_media();

            wp_enqueue_script('select2', WPDM_BASE_URL.'assets/select2/js/select2.min.js', array('jquery'));
            wp_enqueue_style('select2-css', WPDM_BASE_URL.'assets/select2/css/select2.min.css');
            wp_enqueue_style('jqui-css', WPDM_BASE_URL.'assets/jqui/theme/jquery-ui.css');

            wp_enqueue_script('wpdm-admin-bootstrap' );

            wp_enqueue_script('wpdm-vue', WPDM_BASE_URL.'assets/js/vue.min.js');
            wp_enqueue_script('wpdm-admin', WPDM_BASE_URL.'assets/js/wpdm-admin.js', array('jquery'));


            wp_enqueue_style( 'wpdm-font-awesome' );
            wp_enqueue_style( 'wpdm-admin-bootstrap' );

            //wp_enqueue_style('wpdm-bootstrap-theme', plugins_url('/download-manager/assets/css/front.css'));
            wp_enqueue_style('wpdm-admin-styles', WPDM_BASE_URL.'assets/css/admin-styles.css', 9999);

            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );


        }

        wp_enqueue_style('wpdm-gutenberg-styles',WPDM_BASE_URL.'assets/css/gutenberg-styles.css', 9999);
    }

    function pageHeader($title, $icon, $menus = [], $actions = [], $params = [])
    {
       include wpdm_admin_tpl_path("page-header.php", __DIR__.'/views');
    }


    function usersRowAction($actions, $user)
    {
        $actions[] = "<a href='edit.php?post_type=wpdmpro&page=wpdm-stats&type=history&filter=1&user_ids[0]={$user->ID}'>".__( 'Download History', 'download-manager' )."</a>";
        return $actions;
    }

    function notices()
    {
        $class = '';
        if(!(int)get_option('__wpdm_hide_admin_notice', 0)) {
            $message = '<b>Congratulation! You are using Download Manager ' . WPDM_VERSION . '</b><br/>This is a major update. You must update all Download Manager add-ons too, to keep them compatible with Download Manager ' . WPDM_VERSION;
            printf('<div id="wpdmvnotice" class="notice notice-success  is-dismissible"><p>%1$s</p></div>', $message);
        }
    }

    function removeNotices()
    {
        __::isAuthentic('__rnnonce', WPDM_PUB_NONCE, WPDM_ADMIN_CAP);
        update_option('__wpdm_hide_admin_notice', 1, false);
        wp_send_json(['success' => true]);
    }


    function adminHead() {
        remove_submenu_page('index.php', 'wpdm-welcome');
        ?>
        <script type="text/javascript">
            var wpdmConfig = {
              siteURL: '<?php echo site_url(); ?>'
            };
            jQuery(function () {


                jQuery('#TB_closeWindowButton').click(function () {
                    tb_remove();
                });

                jQuery('body').on('click', '#wpdmvnotice .notice-dismiss', function (){
                    jQuery.post(ajaxurl, {action: 'wpdm_remove_admin_notice', __rnnonce: '<?php echo wp_create_nonce(WPDM_PUB_NONCE) ?>'});
                });

            });
        </script>
        <?php

    }


    function metaBoxes() {
        global $ServerDirBrowser;
        if(get_post_type(wpdm_query_var('post')) != 'wpdmpro' && wpdm_query_var('post_type') != 'wpdmpro') return;

        $meta_boxes = array(
            'wpdm-settings' => array('title' => __( "Package Settings" , "download-manager" ), 'callback' => array($this, 'packageSettings'), 'position' => 'normal', 'priority' => 'low'),
            'wpdm-upload-file' => array('title' => __( "Attach File" , "download-manager" ), 'callback' => array($this, 'uploadFiles'), 'position' => 'side', 'priority' => 'core'),
        );


        $meta_boxes = apply_filters("wpdm_meta_box", $meta_boxes);
        foreach ($meta_boxes as $id => $meta_box) {
            extract($meta_box);
            if (!isset($position))
                $position = 'normal';
            if (!isset($priority))
                $priority = 'core';
            add_meta_box($id, $title, $callback, 'wpdmpro', $position, $priority);
        }
    }


    function packageSettings($post) {
        include wpdm_admin_tpl_path("metaboxes/package-settings.php" );
    }

    function uploadFiles($post) {
        include wpdm_admin_tpl_path("metaboxes/attach-file.php" );
    }

	function hideProNotice()
	{
        __::isAuthentic('__wpnnonce', WPDM_PUB_NONCE, WPDM_ADMIN_CAP);
		update_option("__hide_wpdmpro_notice", strtotime('+15 days'));
		die();
	}

	function adminFooter()
	{
        $noticeresume = (int)get_option('__hide_wpdmpro_notice');
		if ($noticeresume < time()) {
			?>
            <div id="wpdmpro-intro" style="display: none">

                <div class="xcard" id="wpdmpro_notice">
                    <div class="xcontent">
                        <a href="#" id="wnxclose">Dismiss</a>
                        <h2>Thanks for choosing WordPress Download Manager</h2>
                        You may check the pro version for multi-file package, front-end administration, email lock and 100+ other awesome features
                    </div>
                    <div class="xbtn">
                        <a target="_blank" href="https://www.wpdownloadmanager.com/pricing/"
                           class="btx">
                            Show Me
                        </a>
                    </div>
                </div>
            </div>
            <style>
                #wpdmpro_notice {
                    padding: 20px;
                    box-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
                    margin: 20px 0;
                    width: calc(100% - 40px);
                    max-width: calc(100% - 40px);
                    border: 0;
                    display: flex;
                    grid-template-columns: 1fr 150px;
                    background: #ffffff;
                }

                #wpdmpro_notice .xcontent{
                    flex: 4;
                    position: relative;
                    background: url('<?php echo  WPDM_BASE_URL ?>assets/images/wpdm-logo.png') left center no-repeat;
                    background-size: contain;
                    padding-left: 60px
                }

                #wpdmpro_notice .xbtn {
                    width: 200px;
                    min-width: 200px;
                    align-content: center;
                    display: grid;
                }

                #wpdmpro_notice h2 {
                    margin: 5px 0 5px;
                }

                #wpdmpro_notice .xbtn .btx {
                    padding: 15px 20px;
                    color: #ffffff;
                    font-weight: 600;
                    text-decoration: none;
                    display: block;
                    text-align: center;
                    position: relative;
                    border-radius: 5px;
                    background: linear-gradient(135deg, #518dff 0%, #22c1ff 100%);
                    border: 0;
                    transition: all ease-in-out 300ms;
                    box-shadow: 0 0 10px rgba(0, 100, 255, 0.57);
                    margin: 0 5px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }

                #wpdmpro_notice .xbtn .btx:hover {
                    transition: all ease-in-out 300ms;
                    box-shadow: 0 0 15px rgba(0, 100, 255, 0.8);
                    opacity: 0.85;
                    transform: scale(1.01);
                }

                #wnxclose {
                    position: absolute;
                    text-decoration: none;
                    color: #fff;
                    background: linear-gradient(135deg, #ff7432 19%, #f81c7f 90%);
                    font-size: 9px;
                    padding: 3px 10px;
                    top: 0;
                    z-index: 999;
                    left: 0;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    margin-top: -31px;
                    opacity: 0;
                    border-radius: 500px;
                    transition: all ease-in-out 500ms;
                }

                #wpdmpro_notice:hover #wnxclose {
                    opacity: 1;
                    transition: all ease-in-out 500ms;
                }
            </style>
            <script>
                jQuery(function ($) {
                    if (pagenow === 'dashboard') {
                        $('#dashboard-widgets-wrap').before($('#wpdmpro-intro').html());
                    }
                    if (pagenow === 'edit-wpdmpro') {
                        $('.wp-header-end').before($('#wpdmpro-intro').html());
                    }
                    $('#wnxclose').on('click', function (e) {
                        e.preventDefault();
                        $('#wpdmpro_notice').fadeOut();
                        $.get(ajaxurl, {__wpnnonce: '<?php echo  wp_create_nonce(WPDM_PUB_NONCE)?>', action: 'hide_wpdmpro_notice'});
                    });
                });
            </script>
			<?php
		}
	}

}
