<?php

namespace WP_STATISTICS;

class top_visitors_page
{

    public function __construct()
    {

        if (Menus::in_page('top-visitors')) {

            // Disable Screen Option
            add_filter('screen_options_show_screen', '__return_false');

            // Check Validate Day arg
            if (isset($_GET['day']) and TimeZone::isValidDate($_GET['day']) === false) {
                wp_die(__("Time request is not valid.", "wp-statistics"));
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
        $args['title'] = __('Top Visitors Today', 'wp-statistics');

        // Get Current Page Url
        $args['pageName'] = Menus::get_page_slug('top-visitors');
        $args['paged']    = Admin_Template::getCurrentPaged();

        // Get Day
        $args['day'] = (isset($_GET['day']) ? esc_html($_GET['day']) : TimeZone::getCurrentDate('Y-m-d'));

        //Get Total List
        $args['total'] = Visitor::Count(array('key' => 'last_counter', 'compare' => '=', 'value' => trim($args['day'])));
        $args['list']  = array();
        if ($args['total'] > 0) {
            $args['list'] = Visitor::get(array(
                'sql'      => "SELECT * FROM `" . DB::table('visitor') . "` WHERE last_counter = '" . esc_sql($args['day']) . "' ORDER BY hits DESC",
                'per_page' => Admin_Template::$item_per_page,
                'paged'    => $args['paged'],
            ));
        }

        // Create WordPress Pagination
        $args['pagination'] = '';
        if ($args['total'] > 0) {
            $args['pagination'] = Admin_Template::paginate_links(array(
                'total' => $args['total'],
                'echo'  => false
            ));
        }

        Admin_Template::get_template(array('layout/header', 'layout/title', 'pages/top-visitors', 'layout/footer'), $args);
    }

}

new top_visitors_page;