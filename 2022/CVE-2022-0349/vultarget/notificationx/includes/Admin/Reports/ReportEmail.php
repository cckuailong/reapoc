<?php
namespace NotificationX\Admin\Reports;

use NotificationX\Admin\Settings;
use NotificationX\Core\Helper as NotificationX_Helper;
use NotificationX\GetInstance;

/**
 * This class is responsible for sending weekly email with reports
 *
 * @since 1.4.4
 */
class ReportEmail {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
    use GetInstance;

    /**
     * Get a single Instance of Analytics
     * @var Report_Email
     */
    public $settings = null;

    private static $current_timestamp = null;
    private static $current_date = null;

    /**
     * Initially Invoked by Default.
     */
    public function __construct() {
        $this->settings = Settings::get_instance()->get('settings');
        if( !Settings::get_instance()->get('settings.enable_analytics', true) || Settings::get_instance()->get('settings.disable_reporting')) {
            $this->mail_report_deactivation( 'daily_email_reporting' );
            $this->mail_report_deactivation( 'weekly_email_reporting' );
            $this->mail_report_deactivation( 'monthly_email_reporting' );
            return;
        }

        add_filter( 'cron_schedules', array( $this, 'schedules_cron' ) );
        add_action('admin_init', array( $this, 'mail_report_activation' ));
        add_action('weekly_email_reporting', array( $this, 'send_email_weekly' ));
    }

    public function reporting( $request ){
        if( ! boolval($request->get_param('disable_reporting')) ) {
            if( $request->has_param('reporting_email') ) {
                $email = $request->get_param( 'reporting_email' );
            }
            $email = $this->receiver_email_address( $email );

            if( ! empty( $email ) ) {
                if( $this->send_email_weekly( $request->get_param('reporting_frequency'), true, $email ) ) {
                    wp_send_json_success( __( 'Successfully Sent an Email', 'notificationx' ) );
                } else {
                    wp_send_json_error( __( 'Email cannot be sent for some reason.', 'notificationx' ) );
                }
            }
            wp_send_json_error( __( 'Something went wrong.', 'notificationx' ) );
        } else {
            return new \WP_Error('nx_disabled_reporting', __( 'You have to enable Reporting first.', 'notificationx' ) );
        }
    }

    private static function timestamps( $date = false ){
        if( is_null( self::$current_timestamp ) ) {
            self::$current_timestamp = current_time('timestamp');
        }
        if( $date ) {
            if( is_null( self::$current_date ) ) {
                self::$current_date = current_time('Y-m-d');
            }
            return self::$current_date;
        }
        return self::$current_timestamp;
    }

    public function create_date($count = '-7days'){
        return date('Y-m-d', strtotime($count, self::timestamps()));
    }

    public function get_stats( $start_date, $end_date = null ){
        global $wpdb;

        $extra_query = $wpdb->prepare( 'BETWEEN %s AND %s', $start_date, $end_date );

        if ( is_null( $end_date ) ) {
            $extra_query = $wpdb->prepare( ' = %s', $start_date );
        }

        $query = "SELECT MAIN.`nx_id`, MAIN.`title`, MAIN.`type`, STATS.`views`, STATS.`clicks`, STATS.`CTR` FROM ( SELECT P.`nx_id`, title, type FROM {$wpdb->prefix}nx_posts as P LEFT JOIN {$wpdb->prefix}nx_stats as S ON P.`nx_id` = S.`nx_id` GROUP BY P.nx_id ) AS MAIN INNER JOIN ( SELECT *, (clicks/views)*100 as ctr FROM ( SELECT SUM(views) as views, SUM(clicks) as clicks, nx_id FROM {$wpdb->prefix}nx_stats WHERE created_at $extra_query GROUP BY nx_id ) as VCID ) as STATS
        ON MAIN.`nx_id` = STATS.`nx_id`";

        return $wpdb->get_results( $query );
    }
    /**
     * Calculate Total NotificationX Views
     * @return int
     */
    public function get_data( $frequency = 'nx_weekly'){
        if( $frequency == 'nx_daily' ) {
            $start_date          = $this->create_date('last day');
            $end_date            = null;
            $previous_start_date = $this->create_date('last day last day');
            $previous_end_date   = null;
        }

        if( $frequency == 'nx_weekly' ) {
            $start_date          = $this->create_date('-7days');
            $end_date            = $this->create_date('last day');
            $previous_start_date = $this->create_date('-14days');
            $previous_end_date   = $this->create_date('-7days');
        }
        if( $frequency == 'nx_monthly' ) {
            $previous_start_date = $this->create_date('first day of last month last month');
            $previous_end_date   = $this->create_date('last day of last month last month');
            $start_date          = $this->create_date('first day of last month');
            $end_date            = $this->create_date('last day of last month');
        }

        $current_data = $this->get_stats( $start_date, $end_date );
        $previous_data = $this->get_stats( $previous_start_date, $previous_end_date );

        $from_date = $previous_start_date;
        $to_date   =  $frequency == 'nx_daily' ? $start_date : $end_date;

        $data = [
            'from_date' => $from_date,
            'to_date' => $to_date,
            'current_data' => $current_data,
            'previous_data' => $previous_data,
        ];

        return $this->generate_data( $data );
    }

    private function percentage( $_last, $_current ){
        return $_last > 0 ? number_format( ( ( $_current - $_last ) / $_last ) * 100, 2 ) : 0;
    }

    private function previous_data( &$_single_data, &$_data = null ){
        if( empty( $_single_data ) ) {
            return [
                'last_views' => 0,
                'percentage_views' => 0,
                'last_clicks' => 0,
                'percentage_clicks' => 0,
                'last_ctr' => 0,
                'percentage_ctr' => 0,
            ];
        }
        $_new_data = [];
        array_walk( $_single_data, function( $item , $key ) use( &$_data, &$_new_data ) {
            switch( $key ) {
                case 'views':
                    $_new_data['last_views'] = $item;
                    $_new_data['percentage_views'] = $this->percentage( $item, $_data['views'] );
                    break;
                case 'clicks':
                    $_new_data['last_clicks'] = $item;
                    $_new_data['percentage_clicks'] = $this->percentage( $item, $_data['clicks'] );
                    break;
                case 'ctr':
                    $_new_data['last_ctr'] = $item;
                    $_new_data['percentage_ctr'] = $this->percentage( $item, $_data['ctr'] );
                    break;
            }
        });

        return $_new_data;
    }

    public function generate_data( &$data ){
        if( empty( $data ) ) {
            return [];
        }

        if( (isset( $data['current_data'] ) && empty( $data['current_data'] )) || (isset( $data['previous_data'] ) && empty( $data['previous_data'] )) ) {
            return [];
        }

        $current_data = [];
        $previous_data = [];
        array_walk( $data['previous_data'], function( $single ) use ( &$previous_data ){
            $previous_data[ $single->nx_id ] = ( array ) $single;
        });
        array_walk( $data['current_data'], function( $single ) use ( &$current_data, &$previous_data, $data ){
            $_single_data = ( array ) $single;
            $_single_data['source'] = NotificationX_Helper::get_type_title( $single->type );
            $_single_data['type'] = NotificationX_Helper::get_type_title( $single->type );
            $_single_data['from_date'] = $data['from_date'];
            $_single_data['to_date'] = $data['to_date'];
            $_previous_data = [];
            if( isset( $previous_data[ $single->nx_id ] ) ) {
                $_previous_data = $this->previous_data( $previous_data[ $single->nx_id ], $_single_data );
            } else {
                $_previous_data = $this->previous_data( $_previous_data, $_single_data );
            }
            $current_data[ $single->nx_id ] = array_merge( $_single_data, $_previous_data );
        });

        return $current_data;
    }
    /**
     * Adds a custom cron schedule for Weekly.
     *
     * @param array $schedules An array of non-default cron schedules.
     * @return array Filtered array of non-default cron schedules.
     */
    function schedules_cron( $schedules = array() ) {
        $schedules['nx_weekly'] = array(
            'interval' => 604800,
            'display'  => __( 'Once Weekly', 'notificationx' )
        );
        $schedules['nx_daily'] = array(
            'interval' => 86400,
            'display'  => __( 'Once Daily', 'notificationx' )
        );
        $schedules['nx_monthly'] = array(
            'interval' => strtotime( 'first day of next month 9AM' ),
            'display'  => __( 'Once Monthly', 'notificationx' )
        );
        return $schedules;
    }

    /**
     * Set Email Receiver mail address
     * By Default, Admin Email Address
     * Admin can set Custom email from NotificationX Advanced Settings Panel
     * @return email||String
     */
    public function receiver_email_address( $email = '' ) {
        if( empty( $email ) ) {
            $email = Settings::get_instance()->get('settings.reporting_email' );
            if( empty( $email ) ) {
                $email = get_option( 'admin_email' );
            }
        }
        if( strpos( $email, ',' ) !== false ) {
            $email = str_replace( ' ', '', $email );
            $email = explode(',', $email );
        } else {
            $email = trim( $email );
        }
        return $email;
    }

    /**
     * Set Email Subject
     * By Default, subject will be "Weekly Reporting for NotificationX"
     * Admin can set Custom Subject from NotificationX Advanced Settings Panel
     * @return subject||String
     */
    public function email_subject() {
        $site_name = get_bloginfo( 'name' );
        $subject = __( "Weekly Engagement Summary of ‘{$site_name}’", 'notificationx' );
        if( isset( $this->settings['reporting_subject'] ) && ! empty( $this->settings['reporting_subject'] ) ) {
            $subject = stripcslashes( $this->settings['reporting_subject'] );
        }
        return $subject;
    }

    public function reporting_frequency(){
        $frequency = Settings::get_instance()->get('settings.reporting_frequency', 'nx_weekly');
        return $frequency;
    }

    /**
     * Enable Cron Function
     * Hook: admin_init
     */
    function mail_report_activation() {
        $day = "monday";
        if( isset( $this->settings['reporting_day'] ) ) {
            $day = $this->settings['reporting_day'];
        }

        $frequency = $this->reporting_frequency();
        if( $frequency === 'nx_weekly' ) {
            $datetime = strtotime( "next $day 9AM", current_time('timestamp') );
            $triggered = Settings::get_instance()->get("reporting.mail_sent.$frequency", false);
            if ( $triggered == 1 ) {
                $this->mail_report_deactivation( 'weekly_email_reporting' );
            }
            if( ! $triggered ) {
                $datetime = strtotime( "+1hour", current_time('timestamp') );
            }
            $this->mail_report_deactivation( 'daily_email_reporting' );
            $this->mail_report_deactivation( 'monthly_email_reporting' );
            if ( ! wp_next_scheduled ( 'weekly_email_reporting' ) ) {
                wp_schedule_event( $datetime, $frequency, 'weekly_email_reporting' );
            }
        }

    }

    /**
     * Execute Cron Function
     * Hook: admin_init
     */
    public function send_email_weekly( $frequency = 'nx_weekly', $test = false, $email = null ) {
        $data = $this->get_data( $frequency );
        if( empty( $data ) ) {
            return new \WP_Error('nx_no_reporting_data', __('No data found.', 'notificationx'));
        }
        if( isset( $this->settings['enable_analytics'] ) && ! $this->settings['enable_analytics'] ) {
            return new \WP_Error('nx_disabled_analytics', __('Analytics disabled. No data found.', 'notificationx'));
        }
        $to = is_null( $email ) ? $this->receiver_email_address() : $email;
        if( empty( $to ) ) {
            return new \WP_Error('nx_reporting_email', __('No email found.', 'notificationx'));
        }

        $subject = $this->email_subject();
        $template = new EmailTemplate();
        $message = $template->template_body( $data, $frequency );
        $headers = array( 'Content-Type: text/html; charset=UTF-8', "From: NotificationX <support@wpdeveloper.com>" );
        if( ! $test ) {
            $triggered = Settings::get_instance()->get("reporting.mail_sent.$frequency");
            $triggered = ! $triggered ? 0 : $triggered++;
            Settings::get_instance()->set("reporting.mail_sent.$frequency", $triggered);
        }
        return wp_mail( $to, $subject, $message, $headers );
    }

    /**
     * Disable Cron Function
     * Hook: plugin_deactivation
     */
    public function mail_report_deactivation( $clear_hook = 'weekly_email_reporting' ) {
        wp_clear_scheduled_hook( $clear_hook );
    }
}