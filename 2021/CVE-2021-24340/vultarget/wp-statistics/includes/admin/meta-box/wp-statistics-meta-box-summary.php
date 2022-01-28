<?php

namespace WP_STATISTICS\MetaBox;

use WP_STATISTICS\Helper;
use WP_STATISTICS\Menus;
use WP_STATISTICS\Option;
use WP_STATISTICS\SearchEngine;
use WP_STATISTICS\TimeZone;

class summary
{
    /**
     * Get Summary Meta Box Data
     *
     * @param array $args
     * @return array
     * @throws \Exception
     */
    public static function get($args = array())
    {
        return self::getSummaryHits(array('user-online', 'visitors', 'visits', 'search-engine', 'timezone'));
    }

    /**
     * Summary Meta Box Lang
     *
     * @return array
     */
    public static function lang()
    {
        return array(
            'search_engine'     => __('Search Engine Referrals', 'wp-statistics'),
            'current_time_date' => __('Current Time and Date', 'wp-statistics'),
            'adjustment'        => __('(Adjustment)', 'wp-statistics')
        );
    }

    /**
     * Get Summary Hits in WP-Statistics
     *
     * @param array $component
     * @return array
     * @throws \Exception
     */
    public static function getSummaryHits($component = array())
    {
        $data = array();

        // Get first Day Install Plugin
        $first_day_install_plugin = Helper::get_date_install_plugin();
        if (!$first_day_install_plugin) {
            $first_day_install_plugin = 365;
        }

        // User Online
        if (in_array('user-online', $component)) {
            if (Option::get('useronline')) {
                $data['user_online'] = array(
                    'value' => wp_statistics_useronline(),
                    'link'  => Menus::admin_url('online')
                );
            }
        }

        // Get Visitors
        if (in_array('visitors', $component)) {
            if (Option::get('visitors')) {
                $data['visitors'] = array();

                // Today
                $data['visitors']['today'] = array(
                    'link'  => Menus::admin_url('visitors', array('from' => TimeZone::getTimeAgo(0), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visitor('today', null, true))
                );

                // Yesterday
                $data['visitors']['yesterday'] = array(
                    'link'  => Menus::admin_url('visitors', array('from' => TimeZone::getTimeAgo(1), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visitor('yesterday', null, true))
                );

                // Week
                $data['visitors']['week'] = array(
                    'link'  => Menus::admin_url('visitors', array('from' => TimeZone::getTimeAgo(7), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visitor('week', null, true))
                );

                // Month
                $data['visitors']['month'] = array(
                    'link'  => Menus::admin_url('visitors', array('from' => TimeZone::getTimeAgo(30), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visitor('month', null, true))
                );

                // Year
                $data['visitors']['year'] = array(
                    'link'  => Menus::admin_url('visitors', array('from' => TimeZone::getTimeAgo(365), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visitor('year', null, true))
                );

                // Total
                $data['visitors']['total'] = array(
                    'link'  => Menus::admin_url('visitors', array('from' => TimeZone::getTimeAgo(TimeZone::getNumberDayBetween($first_day_install_plugin)), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visitor('total', null, true))
                );

            }
        }

        // Get Visits
        if (in_array('visits', $component)) {
            if (Option::get('visits')) {
                $data['visits'] = array();

                // Today
                $data['visits']['today'] = array(
                    'link'  => Menus::admin_url('hits', array('from' => TimeZone::getTimeAgo(0), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visit('today'))
                );

                // Yesterday
                $data['visits']['yesterday'] = array(
                    'link'  => Menus::admin_url('hits', array('from' => TimeZone::getTimeAgo(1), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visit('yesterday'))
                );

                // Week
                $data['visits']['week'] = array(
                    'link'  => Menus::admin_url('hits', array('from' => TimeZone::getTimeAgo(7), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visit('week'))
                );

                // Month
                $data['visits']['month'] = array(
                    'link'  => Menus::admin_url('hits', array('from' => TimeZone::getTimeAgo(30), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visit('month'))
                );

                // Year
                $data['visits']['year'] = array(
                    'link'  => Menus::admin_url('hits', array('from' => TimeZone::getTimeAgo(365), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visit('year'))
                );

                // Total
                $data['visits']['total'] = array(
                    'link'  => Menus::admin_url('hits', array('from' => TimeZone::getTimeAgo(TimeZone::getNumberDayBetween($first_day_install_plugin)), 'to' => TimeZone::getCurrentDate("Y-m-d"))),
                    'value' => number_format_i18n(wp_statistics_visit('total'))
                );
            }
        }

        // Get Search Engine Detail
        if (in_array('search-engine', $component)) {
            $data['search-engine'] = array();
            $total_today           = 0;
            $total_yesterday       = 0;
            foreach (SearchEngine::getList() as $key => $value) {

                // Get Statistics
                $today     = wp_statistics_searchengine($value['tag'], 'today');
                $yesterday = wp_statistics_searchengine($value['tag'], 'yesterday');

                // Push to List
                $data['search-engine'][$key] = array(
                    'name'      => __($value['name'], 'wp-statistics'),
                    'logo'      => $value['logo_url'],
                    'today'     => number_format_i18n($today),
                    'yesterday' => number_format_i18n($yesterday)
                );

                // Sum Search engine
                $total_today     += $today;
                $total_yesterday += $yesterday;
            }
            $data['search-engine-total'] = array(
                'today'     => number_format_i18n($total_today),
                'yesterday' => number_format_i18n($total_yesterday),
                'total'     => number_format_i18n(wp_statistics_searchengine('all')),
            );
        }

        // Get Current Date and Time
        if (in_array('timezone', $component)) {
            $data['timezone'] = array(
                'option-link' => admin_url('options-general.php'),
                'date'        => TimeZone::getCurrentDate_i18n(get_option('date_format')),
                'time'        => TimeZone::getCurrentDate_i18n(get_option('time_format'))
            );
        }

        // Get Hits chartJs (20 Day Ago)
        if (in_array('hit-chart', $component)) {
            $data['hits-chart'] = hits::HitsChart((isset($component['days']) ? array('ago' => $component['days']) : array('ago' => 20)));
        }

        return $data;
    }

}