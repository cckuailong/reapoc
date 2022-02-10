<?php

/**
 * User: shahnuralam
 * Date: 5/6/17
 * Time: 7:58 PM
 */
namespace WPDM\Admin;

class DashboardWidgets
{

    function __construct()
    {
        add_action('wp_dashboard_setup', array($this, 'addDashboardWidget'));
    }

    function overview()
    {
        include wpdm_admin_tpl_path('dashboard-widgets/overview.php');
    }

    function socialOverview()
    {
        include wpdm_admin_tpl_path('dashboard-widgets/social.php');
    }



    function addDashboardWidget()
    {
        wp_add_dashboard_widget('wpdm_overview', __( "Download Manager Overview" , "download-manager" ), array($this, 'overview'));
        wp_add_dashboard_widget('wpdm_social_overview', __( "Social Overview" , "download-manager" ), array($this, 'socialOverview'));

    }

}

