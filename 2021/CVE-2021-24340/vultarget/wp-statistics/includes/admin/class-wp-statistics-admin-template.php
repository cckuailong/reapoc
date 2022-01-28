<?php

namespace WP_STATISTICS;

class Admin_Template
{
    /**
     * Default Pagination GET name
     *
     * @var string
     */
    public static $paginate_link_name = 'pagination-page';

    /**
     * Default Item Per Page in Pagination
     *
     * @var int
     */
    public static $item_per_page = 10;

    /**
     * Jquery UI Datepicker Format in PHP
     *
     * @var string
     */
    public static $datepicker_format = 'Y-m-d';

    /**
     * Global Request Date time in Pages
     *
     * @var string
     */
    public static $request_from_date = 'from';
    public static $request_to_date = 'to';

    /**
     * Default time Ago Days in Pages
     *
     * @var int
     */
    public static $time_ago_days = 30;

    /**
     * Get Template File
     *
     * @param $template
     * @param array $args
     */
    public static function get_template($template, $args = array())
    {

        // Push Args
        if (is_array($args) && isset($args)) :
            extract($args);
        endif;

        // Check Load single file or array list
        if (is_string($template)) {
            $template = explode(" ", $template);
        }

        // Load File
        foreach ($template as $file) {

            $template_file = WP_STATISTICS_DIR . "includes/admin/templates/" . $file . ".php";
            if (!file_exists($template_file)) {
                Helper::doing_it_wrong(__FUNCTION__, __('Template not found.', 'wp-statistics'));
                return;
            }

            // include File
            include $template_file;
        }
    }

    /**
     * Check is Validation Date time Request in Page
     *
     * @throws \Exception
     */
    public static function isValidDateRequest()
    {

        // Get Default Time Ago days
        $default_days_ago = apply_filters('wp_statistics_days_ago_request', self::$time_ago_days);

        // Check if not Request Params
        if (!isset($_GET[self::$request_from_date]) and !isset($_GET[self::$request_to_date])) {
            return array('status' => true, 'days' => TimeZone::getListDays(array('from' => TimeZone::getTimeAgo($default_days_ago))), 'type' => 'ago');
        }

        // Check if Not Exist
        if ((isset($_GET[self::$request_from_date]) and !isset($_GET[self::$request_to_date])) || (!isset($_GET[self::$request_from_date]) and isset($_GET[self::$request_to_date]))) {
            return array('status' => false, 'message' => __("Request is not valid.", "wp-statistics"));
        }

        // Check Validate DateTime
        if (TimeZone::isValidDate($_GET[self::$request_from_date]) === false || TimeZone::isValidDate($_GET[self::$request_to_date]) === false) {
            return array('status' => false, 'message' => __("Time request is not valid.", "wp-statistics"));
        }

        // Export Days Between
        $type = (self::$request_to_date == TimeZone::getCurrentDate("Y-m-d") ? 'ago' : 'between');
        return array('status' => true, 'days' => TimeZone::getListDays(array('from' => $_GET[self::$request_from_date], 'to' => $_GET[self::$request_to_date])), 'type' => $type);
    }

    /**
     * Get Current Paged
     *
     * @return float|int
     */
    public static function getCurrentPaged()
    {
        return isset($_GET[Admin_Template::$paginate_link_name]) ? abs((int)$_GET[Admin_Template::$paginate_link_name]) : 1;
    }

    /**
     * Get Current Offset
     *
     * @param bool $page
     * @param $item_per_page
     * @return float|int
     */
    public static function getCurrentOffset($page = false, $item_per_page = false)
    {
        $page          = ($page === false ? self::getCurrentPaged() : $page);
        $item_per_page = ($item_per_page === false ? Admin_Template::$item_per_page : $item_per_page);
        return ($page * $item_per_page) - $item_per_page;
    }

    /**
     * Pagination Link
     *
     * @param array $args
     * @area admin
     * @return string
     */
    public static function paginate_links($args = array())
    {

        //Prepare Arg
        $defaults        = array(
            'item_per_page' => self::$item_per_page,
            'container'     => 'pagination-wrap',
            'query_var'     => self::$paginate_link_name,
            'total'         => 0,
            'current'       => 0,
            'show_now_page' => true,
            'echo'          => false
        );
        $args            = wp_parse_args($args, $defaults);
        $total_page      = ceil($args['total'] / $args['item_per_page']);
        $args['current'] = ($args['current'] < 1 ? self::getCurrentPaged() : 1);
        $export          = '';

        //Show Pagination Ui
        if ($total_page > 1) {
            $export .= '<div class="' . $args['container'] . '">';
            $export .= paginate_links(array(
                'base'      => add_query_arg($args['query_var'], '%#%'),
                'format'    => '',
                'type'      => 'list',
                'mid_size'  => 3,
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total'     => $total_page,
                'current'   => $args['current']
            ));

            if ($args['show_now_page']) {
                $export .= '<p class="wps-page-number">' . sprintf(__('Page %1$s of %2$s', 'wp-statistics'), number_format_i18n($args['current']), number_format_i18n($total_page)) . '</p>';
            }
            $export .= '</div>';

            // Export Data
            if ($args['echo']) {
                echo $export;
            } else {
                return $export;
            }
        }
    }

    /**
     * Show WordPress DashIcons
     *
     * @param $dashicons
     * @return string
     */
    public static function icons($dashicons)
    {
        return '<span class="dashicons ' . $dashicons . '"></span>';
    }

    /**
     * Create Date Range
     *
     * @param bool $page_link
     * @return array
     * @throws \Exception
     */
    public static function DateRange($page_link = false)
    {

        // Default List Of Date Range
        $date_range = array(
            10  => __('10 Days', 'wp-statistics'),
            20  => __('20 Days', 'wp-statistics'),
            30  => __('30 Days', 'wp-statistics'),
            60  => __('2 Months', 'wp-statistics'),
            90  => __('3 Months', 'wp-statistics'),
            180 => __('6 Months', 'wp-statistics'),
            270 => __('9 Months', 'wp-statistics'),
            365 => __('1 Year', 'wp-statistics')
        );

        // Get All Date From installed plugins day
        $first_day = Helper::get_date_install_plugin();
        if ($first_day != false and !isset($date_range[(int)TimeZone::getNumberDayBetween($first_day)])) {
            $date_range[(int)TimeZone::getNumberDayBetween($first_day)] = __('All', 'wp-statistics');
        }

        // Apply_filter RageTime
        $date_range = apply_filters('wp_statistics_date_time_range', $date_range);

        // Create Link Of Date Time range
        $list = array();
        foreach ($date_range as $number_days => $title) {

            // Generate Link
            $link = add_query_arg(array('from' => TimeZone::getTimeAgo($number_days), 'to' => TimeZone::getCurrentDate("Y-m-d")), (isset($page_link) ? $page_link : remove_query_arg(array(self::$request_from_date, self::$request_to_date))));

            // Check Activate Page
            $active      = false;
            $RequestDate = self::isValidDateRequest();
            if ($RequestDate['status'] === true) {
                $RequestDateKeys = array_keys($RequestDate['days']);
                if (reset($RequestDateKeys) == TimeZone::getTimeAgo($number_days) and end($RequestDateKeys) == TimeZone::getCurrentDate("Y-m-d")) {
                    $active = true;
                }
            }

            // Push To list
            $list[$number_days] = array('title' => $title, 'link' => $link, 'active' => $active);
        }

        return array('list' => $list, 'from' => reset($RequestDateKeys), 'to' => end($RequestDateKeys));
    }

    /**
     * Unknown Column
     *
     * @return string
     */
    public static function UnknownColumn()
    {
        return '<span aria-hidden="true">—</span><span class="screen-reader-text">' . __("Unknown", 'wp-statistics') . '</span>';
    }

}