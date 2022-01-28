<?php

namespace WP_STATISTICS\MetaBox;

use WP_STATISTICS\TimeZone;

class pages_chart
{
    /**
     * Default Number day in Search Chart
     *
     * @var int
     */
    public static $default_days_ago = 30;

    /**
     * Get Search Engine Chart
     *
     * @param array $arg
     * @return array
     * @throws \Exception
     */
    public static function get($arg = array())
    {

        // Set Default Params
        $defaults = array(
            'ago'  => 0,
            'type' => 'category',
            'ID'   => 0,
            'from' => '',
            'to'   => ''
        );
        $args     = wp_parse_args($arg, $defaults);

        // Set Default Params
        $date = $stats = array();

        // Set Params Total
        $total_in_dates = $total = 0;

        // Check Default
        if (empty($args['from']) and empty($args['to']) and $args['ago'] < 1) {
            $args['ago'] = self::$default_days_ago;
        }

        // Get time ago Days Or Between Two Days
        if ($args['ago'] > 0) {
            $days_list = TimeZone::getListDays(array('from' => TimeZone::getTimeAgo($args['ago'])));
        } else {
            $days_list = TimeZone::getListDays(array('from' => $args['from'], 'to' => $args['to']));
        }

        // Get List Of Days
        $days_time_list = array_keys($days_list);
        foreach ($days_list as $k => $v) {
            $date[]          = $v['format'];
            $total_daily[$k] = 0;
        }

        // Prepare title Hit Chart
        if ($args['ago'] > 0) {
            $count_day = $args['ago'];
        } else {
            $count_day = TimeZone::getNumberDayBetween($args['from'], $args['to']);
        }

        // Set Title
        if (end($days_time_list) == TimeZone::getCurrentDate("Y-m-d")) {
            $title = sprintf(__('Hits in the in the last %s days', 'wp-statistics'), $count_day);
        } else {
            $title = sprintf(__('Hits from %s to %s', 'wp-statistics'), $args['from'], $args['to']);
        }

        // Check Type For Custom Type and ID
        foreach ($days_time_list as $d) {
            $getStatic      = (int)wp_statistics_pages($d, null, ($args['ID'] > 0 ? $args['ID'] : -1), null, null, $args['type']);
            $stats[]        = $getStatic;
            $total_in_dates = $total_in_dates + $getStatic;
        }

        // Get Total
        $total = (int)wp_statistics_pages('total', null, ($args['ID'] > 0 ? $args['ID'] : -1), null, null, $args['type']);

        // Prepare Response
        $response = array(
            'days'        => $count_day,
            'from'        => reset($days_time_list),
            'to'          => end($days_time_list),
            'type'        => (($args['from'] != "" and $args['to'] != "" and $args['ago'] != self::$default_days_ago) ? 'between' : 'ago'),
            'title'       => $title,
            'date'        => $date,
            'stat'        => $stats,
            'total'       => (int)number_format_i18n($total),
            'total_dates' => (int)number_format_i18n($total_in_dates)
        );

        // Response
        return $response;
    }

}