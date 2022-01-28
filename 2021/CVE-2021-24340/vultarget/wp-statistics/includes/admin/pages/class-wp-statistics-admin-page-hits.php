<?php

namespace WP_STATISTICS;

class hits_page
{

    public function __construct()
    {

        // Check if in Hits Page
        if (Menus::in_page('hits')) {

            // Disable Screen Option
            add_filter('screen_options_show_screen', '__return_false');

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
        $args['title'] = __('Hit Statistics', 'wp-statistics');

        // Get Current Page Url
        $args['pageName']   = Menus::get_page_slug('hits');
        $args['pagination'] = Admin_Template::getCurrentPaged();

        // Get Date-Range
        $args['DateRang'] = Admin_Template::DateRange();

        // Get Total Visits and Visitors
        $args['total_visits']   = (Option::get('visits') ? wp_statistics_visit('total') : 0);
        $args['total_visitors'] = (Option::get('visitors') ? wp_statistics_visitor('total', null, true) : 0);

        // Show Template Page
        Admin_Template::get_template(array('layout/header', 'layout/title', 'layout/date.range', 'pages/hits', 'layout/footer'), $args);
    }

}

new hits_page;