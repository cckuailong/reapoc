<?php

namespace WP_STATISTICS;

class Schedule
{

    public function __construct()
    {

        // Define New Cron Schedules Time in WordPress
        add_filter('cron_schedules', array($this, 'define_schedules_time'));

        //Run This Method Only Admin Area
        if (is_admin()) {

            //Disable Run to Ajax
            if (!Helper::is_request('ajax')) {

                // Add the GeoIP update schedule if it doesn't exist and it should be.
                if (!wp_next_scheduled('wp_statistics_geoip_hook') && Option::get('schedule_geoip') && Option::get('geoip')) {
                    wp_schedule_event(time(), 'daily', 'wp_statistics_geoip_hook');
                }

                // Remove the GeoIP update schedule if it does exist and it should shouldn't.
                if (wp_next_scheduled('wp_statistics_geoip_hook') && (!Option::get('schedule_geoip') || !Option::get('geoip'))) {
                    wp_unschedule_event(wp_next_scheduled('wp_statistics_geoip_hook'), 'wp_statistics_geoip_hook');
                }

                //Construct Event
                add_action('wp_statistics_geoip_hook', array($this, 'geoip_event'));
            }

        } else {

            // Add the report schedule if it doesn't exist and is enabled.
            if (!wp_next_scheduled('wp_statistics_report_hook') && Option::get('stats_report')) {
                wp_schedule_event(time(), Option::get('time_report'), 'wp_statistics_report_hook');
            }

            // Remove the report schedule if it does exist and is disabled.
            if (wp_next_scheduled('wp_statistics_report_hook') && !Option::get('stats_report')) {
                wp_unschedule_event(wp_next_scheduled('wp_statistics_report_hook'), 'wp_statistics_report_hook');
            }

            // Add the referrerspam update schedule if it doesn't exist and it should be.
            if (!wp_next_scheduled('wp_statistics_referrerspam_hook') && Option::get('schedule_referrerspam')) {
                wp_schedule_event(time(), 'weekly', 'wp_statistics_referrerspam_hook');
            }

            // Remove the referrerspam update schedule if it does exist and it should shouldn't.
            if (wp_next_scheduled('wp_statistics_referrerspam_hook') && !Option::get('schedule_referrerspam')) {
                wp_unschedule_event(wp_next_scheduled('wp_statistics_referrerspam_hook'), 'wp_statistics_referrerspam_hook');
            }

            // Add the database maintenance schedule if it doesn't exist and it should be.
            if (!wp_next_scheduled('wp_statistics_dbmaint_hook') && Option::get('schedule_dbmaint')) {
                wp_schedule_event(time(), 'daily', 'wp_statistics_dbmaint_hook');
            }

            // Remove the database maintenance schedule if it does exist and it shouldn't.
            if (wp_next_scheduled('wp_statistics_dbmaint_hook') && (!Option::get('schedule_dbmaint'))) {
                wp_unschedule_event(wp_next_scheduled('wp_statistics_dbmaint_hook'), 'wp_statistics_dbmaint_hook');
            }

            // Add the visitor database maintenance schedule if it doesn't exist and it should be.
            if (!wp_next_scheduled('wp_statistics_dbmaint_visitor_hook') && Option::get('schedule_dbmaint_visitor')) {
                wp_schedule_event(time(), 'daily', 'wp_statistics_dbmaint_visitor_hook');
            }

            // Remove the visitor database maintenance schedule if it does exist and it shouldn't.
            if (wp_next_scheduled('wp_statistics_dbmaint_visitor_hook') && (!Option::get('schedule_dbmaint_visitor'))) {
                wp_unschedule_event(wp_next_scheduled('wp_statistics_dbmaint_visitor_hook'), 'wp_statistics_dbmaint_visitor_hook');
            }

            // Remove the add visit row schedule if it does exist and it shouldn't.
            if (wp_next_scheduled('wp_statistics_add_visit_hook') && (!Option::get('visits'))) {
                wp_unschedule_event(wp_next_scheduled('wp_statistics_add_visit_hook'), 'wp_statistics_add_visit_hook');
            }

            // Add the add visit table row schedule if it does exist and it should.
            if (!wp_next_scheduled('wp_statistics_add_visit_hook') && Option::get('visits')) {
                wp_schedule_event(time(), 'daily', 'wp_statistics_add_visit_hook');
            }

            //After construct
            add_action('wp_statistics_add_visit_hook', array($this, 'add_visit_event'));
            add_action('wp_statistics_dbmaint_hook', array($this, 'dbmaint_event'));
            add_action('wp_statistics_dbmaint_visitor_hook', array($this, 'dbmaint_visitor_event'));
            add_action('wp_statistics_report_hook', array($this, 'send_report'));
        }

    }

    /**
     * Define New Cron Schedules Time in WordPress
     *
     * @param array $schedules
     * @return mixed
     */
    static function define_schedules_time($schedules)
    {

        // Adds once weekly to the existing schedules.
        $WP_Statistics_schedules = array(
            'weekly' => array(
                'interval' => 604800,
                'display' => __('Once Weekly'),
            ),
            'biweekly' => array(
                'interval' => 1209600,
                'display' => __('Once Every 2 Weeks'),
            ),
            '4weeks' => array(
                'interval' => 2419200,
                'display' => __('Once Every 4 Weeks'),
            )
        );
        foreach ($WP_Statistics_schedules as $key => $val) {
            if (!array_key_exists($key, $schedules)) {
                $schedules[$key] = $val;
            }
        }

        return $schedules;
    }

    /**
     * adds a record for tomorrow to the visit table to avoid a race condition.
     */
    public function add_visit_event()
    {
        global $wpdb;

        $insert = $wpdb->insert(
            DB::table('visit'),
            array(
                'last_visit' => TimeZone::getCurrentDate('Y-m-d H:i:s', '+1'),
                'last_counter' => TimeZone::getCurrentDate('Y-m-d', '+1'),
                'visit' => 0,
            )
        );
        if (!$insert) {
            if (!empty($wpdb->last_error)) {
                \WP_Statistics::log($wpdb->last_error);
            }
        }
    }

    /**
     * Updates the GeoIP database from MaxMind.
     */
    public function geoip_event()
    {

        // Max-mind updates the geo-ip database on the first Tuesday of the month, to make sure we don't update before they post
        $this_update = strtotime(__('First Tuesday of this month', 'wp-statistics')) + (86400 * 2);
        $last_update = Option::get('last_geoip_dl');

        $is_require_update = false;
        foreach (GeoIP::$library as $geo_ip => $value) {
            $file_path = GeoIP::get_geo_ip_path($geo_ip);
            if (file_exists($file_path)) {
                if ($last_update < $this_update) {
                    $is_require_update = true;
                }
            }
        }

        if ($is_require_update === true) {
            Option::update('update_geoip', true);
        }
    }

    /**
     * Purges old records on a schedule based on age.
     */
    public function dbmaint_event()
    {
        $purge_days = intval(Option::get('schedule_dbmaint_days', false));
        Purge::purge_data($purge_days);
    }

    /**
     * Purges visitors with more than a defined number of hits in a day.
     */
    public function dbmaint_visitor_event()
    {
        $purge_hits = intval(Option::get('schedule_dbmaint_visitor_hits', false));
        Purge::purge_visitor_hits($purge_hits);
    }

    /**
     * Send Wp-Statistics Report
     */
    public function send_report()
    {
        // apply Filter ShortCode for email content
        $final_text_report = Option::get('content_report');
        $final_text_report = do_shortcode($final_text_report);

        // Type Send Report
        $type = Option::get('send_report');

        // If Email
        if ($type == 'mail') {

            /**
             * Filter for email template content
             * @usage wp-statistics-advanced-reporting
             */
            $email_content = apply_filters('wp_statistics_final_text_report_email', $final_text_report);

            /**
             * Filter for enable/disable sending email by template.
             */
            $email_template = apply_filters('wp_statistics_report_email_template', true);

            /**
             * Email receivers
             */
            $email_receivers = Option::getEmailNotification();

            /**
             * Send Email
             */
            $result_email = Helper::send_mail(
                $email_receivers,
                __('Statistical reporting', 'wp-statistics'),
                $email_content,
                $email_template
            );

            /**
             * Fire actions after sending email
             */
            do_action('wp_statistics_after_report_email', $result_email, $email_receivers, $email_content);

        }

        // If SMS
        if ($type == 'sms') {
            Helper::send_sms(array(get_option('wp_admin_mobile')), $final_text_report);
        }
    }

}

new Schedule;
