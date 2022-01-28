<?php

namespace WP_STATISTICS;

class country_page
{

    public function __construct()
    {

        if (Menus::in_page('countries')) {

            // Disable Screen Option
            add_filter('screen_options_show_screen', '__return_false');

            // Set Default All Option for DatePicker
            add_filter('wp_statistics_days_ago_request', array('\WP_STATISTICS\Helper', 'set_all_option_datepicker'));

            // Is Validate Date Request
            $DateRequest = Admin_Template::isValidDateRequest();
            if (!$DateRequest['status']) {
                wp_die($DateRequest['message']);
            }
        }
    }

    /**
     * Display Html Page
     *
     * @throws \Exception
     */
    public static function view()
    {

        // Page title
        $args['title'] = __('Top Countries', 'wp-statistics');

        // Get Current Page Url
        $args['pageName'] = Menus::get_page_slug('countries');
        $args['paged']    = Admin_Template::getCurrentPaged();

        // Get Date-Range
        $args['DateRang'] = Admin_Template::DateRange();

        // Show Template
        Admin_Template::get_template(array('layout/header', 'layout/title', 'layout/date.range', 'pages/country', 'layout/postbox.toggle', 'layout/footer'), $args);
    }

}

new country_page;