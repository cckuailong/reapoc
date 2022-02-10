<?php
/**
 * DashboardWidget
 *
 * @package NotificationX\Admin
 */

namespace NotificationX\Admin;

use NotificationX\GetInstance;
use NotificationX\Core\Helper;
/**
 * Class for Dashboard Widget for Analytics.
 */
class DashboardWidget {
    /**
     * Instance of DashboardWidget
     *
     * @var DashboardWidget
     */
    use GetInstance;
    /**
     * Widget ID
     *
     * @constant string
     */
    const WIDGET_ID = 'nx_analytics_dashboard_widget';
    const ASSET_URL  = NOTIFICATIONX_ASSETS . 'admin/';
    const VIEWS_PATH = NOTIFICATIONX_INCLUDES . 'Admin/views/';
    /**
     * Widget Title
     *
     * @var string
     */
    protected $widget_name = null;

    /**
     * Constructor
     * Invoked automatically when object created
     */
    public function __construct(){
        if( Settings::get_instance()->get('settings.disable_dashboard_widget', false) ) {
            return;
        }
        if( ! Settings::get_instance()->get('settings.enable_analytics', true) ) {
            return;
        }
        $this->widget_name = __( 'NotificationX Analytics', 'notificationx' );
        add_action( 'wp_dashboard_setup', array( $this, 'widget_action' ) );
        add_action('admin_enqueue_scripts', [ $this, 'enqueue'] );
    }
    public function enqueue( $hook ){
        wp_register_style( 'nx-analytics-dashboard-widget', self::ASSET_URL . 'css/analytics-dashboard-widget.css', array(), false, 'all' );
    }
    /**
     * Admin Action callback
     * for wp_dashboard_setup
     *
     * @return void
     */
    public function widget_action(){
        wp_add_dashboard_widget( self::WIDGET_ID, $this->widget_name, array( $this, 'widget_output' ) );
    }
    /**
     * Get all analytics data
     *
     * @return mixed
     */
    public function analytics_counter(){
        global $wpdb;

        $results = $wpdb->get_row(
            "SELECT *, ( clicks/views ) * 100 as ctr FROM ( SELECT SUM(views) as views, SUM(clicks) as clicks FROM {$wpdb->prefix}nx_stats ) AS STATS",
            ARRAY_A
        );

        $views_link = admin_url( 'admin.php?page=nx-analytics&comparison=views' );
        $clicks_link = admin_url( 'admin.php?page=nx-analytics&comparison=clicks' );
        $ctr_link = admin_url( 'admin.php?page=nx-analytics&comparison=ctr' );

        $default = [
            'views_link'  => $views_link,
            'clicks_link' => $clicks_link,
            'ctr_link'    => $ctr_link,
            'views'       => 0,
            'clicks'      => 0,
            'ctr'         => 0,
        ];

        if( ! empty( $results ) ) {
            $_results = [];
            foreach( $results as $key => $value ) {
                if( $key === 'ctr' ) {
                    $_results[ $key ] = round( $value, 2 );
                } else {
                    $_results[ $key ] = Helper::nice_number( $value );
                }
            }
            return array_merge( $default, $_results );
        }
        return $default;
    }
    /**
     * Widget Output
     *
     * @return mixed
     */
    public function widget_output(){
        extract( $this->analytics_counter() );
        $class = 'nx-analytics-widget';
        if( file_exists( self::VIEWS_PATH . 'analytics.views.php' ) ) {
            wp_enqueue_style('nx-analytics-dashboard-widget');
            return include_once self::VIEWS_PATH . 'analytics.views.php';
        }
    }
}
