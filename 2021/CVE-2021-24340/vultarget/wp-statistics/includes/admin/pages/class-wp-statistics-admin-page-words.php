<?php

namespace WP_STATISTICS;

class words_page
{

    public function __construct()
    {

        if (Menus::in_page('words')) {

            // Disable Screen Option
            add_filter('screen_options_show_screen', '__return_false');
        }
    }

    /**
     * Set All Option For DatePicker
     */
    public function set_all_option_datepicker()
    {
        $first_day = Helper::get_date_install_plugin();
        return ($first_day === false ? 30 : (int)TimeZone::getNumberDayBetween($first_day));
    }

    /**
     * Display Html Page
     *
     * @throws \Exception
     */
    public static function view()
    {

        // Page title
        $args['title'] = __('Latest Search Words', 'wp-statistics');

        // Get Current Page Url
        $args['pageName'] = Menus::get_page_slug('referrers');
        $args['paged']    = Admin_Template::getCurrentPaged();

        //Get List Search Engine
        $search_engines               = SearchEngine::getList();
        $args['search_engine']['all'] = array('title' => __('All', 'wp-statistics'), 'count' => wp_statistics_searchword('all', 'total'), 'active' => (isset($_GET['referred']) ? false : true), 'link' => Menus::admin_url('words'));
        foreach ($search_engines as $key => $se) {
            $args['search_engine'][$se['tag']] = array('title' => $se['translated'], 'count' => wp_statistics_searchword($key, 'total'), 'active' => ((isset($_GET['referred']) and $_GET['referred'] == $se['tag']) ? true : false), 'link' => add_query_arg('referred', $se['tag'], Menus::admin_url('words')));
        }

        // Get Current View
        $CurrentView = array_filter($args['search_engine'], function ($val, $key) {
            return $val['active'] === true;
        }, ARRAY_FILTER_USE_BOTH);

        //Get Total List
        $args['total'] = $CurrentView[key($CurrentView)]['count'];
        $args['list']  = array();
        if ($args['total'] > 0) {
            $args['list'] = SearchEngine::getLastSearchWord(array('search_engine' => key($CurrentView), 'limit' => ($args['paged'] - 1) * Admin_Template::$item_per_page . "," . Admin_Template::$item_per_page));
        }

        // Create WordPress Pagination
        $args['pagination'] = '';
        if ($args['total'] > 0) {
            $args['pagination'] = Admin_Template::paginate_links(array(
                'total' => $args['total'],
                'echo'  => false
            ));
        }

        Admin_Template::get_template(array('layout/header', 'layout/title', 'pages/words', 'layout/footer'), $args);
    }

}

new words_page;