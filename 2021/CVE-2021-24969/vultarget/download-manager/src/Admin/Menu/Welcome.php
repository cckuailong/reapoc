<?php


namespace WPDM\Admin\Menu;


class Welcome
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'Menu'));
        add_action('activated_plugin', array($this, 'welcomeRedirect'));

    }

    function Menu(){
        add_dashboard_page('Welcome', 'Welcome', 'read', 'wpdm-welcome', array($this, 'UI'));
    }



    function UI(){
        update_option("__wpdm_welcome", WPDM_VERSION, false);
        remove_submenu_page( 'index.php', 'wpdm-welcome' );
        include wpdm_admin_tpl_path("welcome.php", dirname(__DIR__).'/views');
    }

    function welcomeRedirect($plugin)
    {
        $wv = get_option('__wpdm_welcome');
        if($plugin=='download-manager/download-manager.php' && $wv !== WPDM_VERSION) {
            wp_redirect(admin_url('index.php?page=wpdm-welcome'));
            die();
        }
    }

}
