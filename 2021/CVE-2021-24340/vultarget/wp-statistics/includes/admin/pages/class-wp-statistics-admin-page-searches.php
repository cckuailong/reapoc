<?php

namespace WP_STATISTICS;

class searches_page
{

    public function __construct()
    {

        // Check if in Hits Page
        if (Menus::in_page('searches')) {

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
        $args['title'] = __('Search Engine Referral Statistics', 'wp-statistics');

        // Get Current Page Url
        $args['pageName']   = Menus::get_page_slug('searches');
        $args['pagination'] = Admin_Template::getCurrentPaged();

        // Get Date-Range
        $args['DateRang'] = Admin_Template::DateRange();

        // Show Template Page
        Admin_Template::get_template(array('layout/header', 'layout/title', 'layout/date.range', 'pages/search', 'layout/footer'), $args);
    }

}

new searches_page;