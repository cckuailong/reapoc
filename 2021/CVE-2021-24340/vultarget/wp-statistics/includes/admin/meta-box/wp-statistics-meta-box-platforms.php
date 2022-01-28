<?php

namespace WP_STATISTICS\MetaBox;

use WP_STATISTICS\DB;
use WP_STATISTICS\Helper;
use WP_STATISTICS\Menus;
use WP_STATISTICS\TimeZone;

class platforms
{
    /**
     * Get Platforms Chart
     *
     * @param array $arg
     * @return array
     * @throws \Exception
     */
    public static function get($arg = array())
    {
        global $wpdb;

        // Set Default Params
        $defaults = array(
            'ago'    => 0,
            'from'   => '',
            'to'     => '',
            'order'  => '',
            'number' => 10 // Get Max number of platform
        );
        $args     = wp_parse_args($arg, $defaults);

        // Check Default
        if (empty($args['from']) and empty($args['to']) and $args['ago'] < 1) {
            $args['ago'] = 'all';
        }

        // Prepare Count Day
        if (!empty($args['from']) and !empty($args['to'])) {
            $count_day = TimeZone::getNumberDayBetween($args['from'], $args['to']);
        } else {
            if (is_numeric($args['ago']) and $args['ago'] > 0) {
                $count_day = $args['ago'];
            } else {
                $first_day = Helper::get_date_install_plugin();
                $count_day = (int)TimeZone::getNumberDayBetween($first_day);
            }
        }

        // Get time ago Days Or Between Two Days
        if (!empty($args['from']) and !empty($args['to'])) {
            $days_list = TimeZone::getListDays(array('from' => $args['from'], 'to' => $args['to']));
        } else {
            if (is_numeric($args['ago']) and $args['ago'] > 0) {
                $days_list = TimeZone::getListDays(array('from' => TimeZone::getTimeAgo($args['ago'])));
            } else {
                $days_list = TimeZone::getListDays(array('from' => TimeZone::getTimeAgo($count_day)));
            }
        }

        // Get List Of Days
        $days_time_list = array_keys($days_list);
        foreach ($days_list as $k => $v) {
            $date[]          = $v['format'];
            $total_daily[$k] = 0;
        }

        // Set Default Value
        $total       = $count = 0;
        $lists_value = $lists_name = array();

        // Get List All Platforms
        $list = $wpdb->get_results("SELECT platform, COUNT(*) as count FROM " . DB::table('visitor') . " WHERE `last_counter` BETWEEN '" . reset($days_time_list) . "' AND '" . end($days_time_list) . "' GROUP BY platform " . ($args['order'] != "" ? 'ORDER BY `count` ' . $args['order'] : ''), ARRAY_A);

        // Sort By Count
        Helper::SortByKeyValue($list, 'count');

        // Get Last 10 Version that Max number
        $platforms = array_slice($list, 0, $args['number']);

        // Push to array
        foreach ($platforms as $l) {

            if (trim($l['platform']) != "") {

                // Sanitize Version name
                $lists_name[] = $l['platform'];

                // Get List Count
                $lists_value[] = (int)$l['count'];

                // Add to Total
                $total += $l['count'];
            }
        }

        // Set Title
        if (end($days_time_list) == TimeZone::getCurrentDate("Y-m-d")) {
            $title = sprintf(__('%s Statistics in the last %s days', 'wp-statistics'), __('Platforms', 'wp-statistics'), $count_day);
        } else {
            $title = sprintf(__('%s Statistics from %s to %s', 'wp-statistics'), __('Platforms', 'wp-statistics'), $args['from'], $args['to']);
        }

        // Prepare Response
        $response = array(
            'days'           => $count_day,
            'from'           => reset($days_time_list),
            'to'             => end($days_time_list),
            'type'           => (($args['from'] != "" and $args['to'] != "") ? 'between' : 'ago'),
            'title'          => $title,
            'platform_name'  => $lists_name,
            'platform_value' => $lists_value,
            'info'           => array(
                'visitor_page' => Menus::admin_url('visitors')
            ),
            'total'          => $total
        );

        // Check For No Data Meta Box
        if (count(array_filter($lists_value)) < 1 and !isset($args['no-data'])) {
            $response['no_data'] = 1;
        }

        // Response
        return $response;
    }

}