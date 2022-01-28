<?php

namespace WP_STATISTICS\MetaBox;

use WP_STATISTICS\DB;
use WP_STATISTICS\Exclusion;
use WP_STATISTICS\Option;
use WP_STATISTICS\TimeZone;

class exclusions
{
    /**
     * Default Number day in exclusions Chart
     *
     * @var int
     */
    public static $default_days_ago = 30;

    /**
     * Show Chart Hit
     *
     * @param array $args
     * @return array
     * @throws \Exception
     */
    public static function get($args = array())
    {
        global $wpdb;

        // Set Default Params
        $defaults = array(
            'ago'  => 0,
            'from' => '',
            'to'   => ''
        );
        $args     = wp_parse_args($args, $defaults);

        // Prepare Default
        $date = array();

        // Get time ago Days Or Between Two Days
        if (!empty($args['from']) and !empty($args['to'])) {
            $count_day = TimeZone::getNumberDayBetween($args['from'], $args['to']);
            $days_list = TimeZone::getListDays(array('from' => $args['from'], 'to' => $args['to']));
        } else {
            if (is_numeric($args['ago']) and $args['ago'] > 0) {
                $count_day = $args['ago'];
            } else {
                $count_day = self::$default_days_ago;
            }
            $days_list = TimeZone::getListDays(array('from' => TimeZone::getTimeAgo($count_day)));
        }

        // Get List Of Days
        $days_time_list = array_keys($days_list);
        foreach ($days_list as $k => $v) {
            $date[] = $v['format'];
        }

        // Set Title
        if (end($days_time_list) == TimeZone::getCurrentDate("Y-m-d")) {
            $title = sprintf(__('Exclusions in the last %s days', 'wp-statistics'), $count_day);
        } else {
            $title = sprintf(__('Exclusions from %s to %s', 'wp-statistics'), $args['from'], $args['to']);
        }

        // Push Basic Chart Data
        $data = array(
            'days'  => $count_day,
            'from'  => reset($days_time_list),
            'to'    => end($days_time_list),
            'type'  => (($args['from'] != "" and $args['to'] != "" and $args['ago'] != self::$default_days_ago) ? 'between' : 'ago'),
            'title' => $title,
            'date'  => $date
        );

        // Set List Of Data
        $exclusive_list     = Exclusion::exclusion_list();
        $data['exclusions'] = $exclusive_list;
        foreach ($exclusive_list as $key => $name) {
            $total_item = 0;
            $list_item  = array();
            foreach ($days_time_list as $d) {
                $total_item += $list_item[] = (int)$wpdb->get_var("SELECT `count` FROM " . DB::table('exclusions') . " WHERE `reason` = '{$key}' AND date = '{$d}'");
            }
            $data['value'][$key] = $list_item;
            $data['total'][$key] = $total_item;
        }

        // Response
        return $data;
    }

}