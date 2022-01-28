<?php

namespace WP_STATISTICS;

class exclusions_page
{

    public function __construct()
    {

        // Check if in Hits Page
        if (Menus::in_page('exclusions')) {

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
        global $wpdb;

        // Page title
        $args['title'] = __('Exclusions Statistics', 'wp-statistics');

        // Get Current Page Url
        $args['pageName']   = Menus::get_page_slug('exclusions');
        $args['pagination'] = Admin_Template::getCurrentPaged();

        // Get Date-Range
        $args['DateRang'] = Admin_Template::DateRange();

        // Get Total Exclusions
        $args['total_exclusions'] = $wpdb->get_var("SELECT SUM(count) FROM `" . DB::table('exclusions') . "`");

        // Show Template Page
        Admin_Template::get_template(array('layout/header', 'layout/title', 'layout/date.range', 'pages/exclusions', 'layout/footer'), $args);
    }

}

new exclusions_page;