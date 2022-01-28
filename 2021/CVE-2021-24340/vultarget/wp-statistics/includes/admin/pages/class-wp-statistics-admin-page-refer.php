<?php

namespace WP_STATISTICS;

class refer_page
{

    public function __construct()
    {

        if (Menus::in_page('referrers')) {

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
        global $wpdb;

        // Page title
        $args['title'] = __('Top Referring Sites', 'wp-statistics');

        // Get Current Page Url
        $args['pageName'] = Menus::get_page_slug('referrers');
        $args['paged']    = Admin_Template::getCurrentPaged();

        // Get Date-Range
        $args['DateRang'] = Admin_Template::DateRange();

        // Get Total List
        if (!isset($_GET['referr'])) {

            // Get Total
            $result = Referred::getList(array('from' => $args['DateRang']['from'], 'to' => $args['DateRang']['to']));

            // Total Number
            $args['total'] = count($result);
            $args['list']  = array();

            // Prepare List
            if ($args['total'] > 0) {
                $chunk        = array_chunk($result, Admin_Template::$item_per_page);
                $list_in_page = $chunk[$args['paged'] - 1];
                $get_urls     = array();
                foreach ($list_in_page as $items) {
                    $get_urls[$items->domain] = Referred::get_referer_from_domain($items->domain);
                }
                $list = Referred::PrepareReferData($get_urls);

                // Push Domain Rate in List
                $i = 1;
                foreach ($list as $domain_list) {
                    $args['list'][] = array_merge($domain_list, array('rate' => $i + (($args['paged'] - 1) * Admin_Template::$item_per_page)));
                    $i++;
                }
            }

        } else {

            // Get Special domain Refer List
            $args['domain']     = trim($_GET['referr']);
            $args['custom_get'] = array('referr' => $_GET['referr']);
            $args['title']      = sprintf(__('Referring site: %s', 'wp-statistics'), Referred::html_sanitize_referrer($args['domain']));
            $args['total']      = Referred::get_referer_from_domain($args['domain'], 'number', array($args['DateRang']['from'], $args['DateRang']['to']));
            $args['list']       = array();

            //Prepare List
            if ($args['total'] > 0) {
                $args['list'] = Referred::get_referer_from_domain($args['domain'], 'list', array($args['DateRang']['from'], $args['DateRang']['to']), ($args['paged'] - 1) * Admin_Template::$item_per_page . "," . Admin_Template::$item_per_page);
            }

        }

        // Create WordPress Pagination
        $args['pagination'] = '';
        if ($args['total'] > 0) {
            $args['pagination'] = Admin_Template::paginate_links(array(
                'total' => $args['total'],
                'echo'  => false
            ));
        }

        Admin_Template::get_template(array('layout/header', 'layout/title', 'layout/date.range', (isset($_GET['referr']) ? 'pages/refer.url' : 'pages/top.refer'), 'layout/footer'), $args);
    }

}

new refer_page;