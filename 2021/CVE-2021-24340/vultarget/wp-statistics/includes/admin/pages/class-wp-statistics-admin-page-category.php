<?php

namespace WP_STATISTICS;

class category_page
{

    public function __construct()
    {

        // Check if in category Page
        if (Menus::in_page('categories')) {

            // Disable Screen Option
            add_filter('screen_options_show_screen', '__return_false');

            // Is Validate Date Request
            $DateRequest = Admin_Template::isValidDateRequest();
            if (!$DateRequest['status']) {
                wp_die($DateRequest['message']);
            }

            // Check Validate int Params
            if (isset($_GET['ID']) and (!is_numeric($_GET['ID']) || ($_GET['ID'] != 0 and term_exists((int)trim($_GET['ID']), 'category') == null))) {
                wp_die(__("Request is not valid.", "wp-statistics"));
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
        $args['title'] = __('Category Statistics', 'wp-statistics');

        // Get Current Page Url
        $args['pageName']   = Menus::get_page_slug('categories');
        $args['pagination'] = Admin_Template::getCurrentPaged();

        // Get Date-Range
        $args['DateRang'] = Admin_Template::DateRange();

        // Create Select Box
        $args['select_box'] = array(
            'name'  => 'ID',
            'title' => __('Select Category', 'wp-statistics')
        );

        // Get List Category
        $terms = get_terms('category', array(
            'hide_empty' => true,
        ));

        // Set All Item
        $args['select_box']['list'][0] = __('All', 'wp-statistics');

        // Push Every Category To List
        foreach ($terms as $category) {
            $args['select_box']['list'][$category->term_id] = $category->name;
        }

        // Check Activate Item
        $args['select_box']['active'] = ((isset($_GET['ID']) and term_exists((int)trim($_GET['ID']), 'category') !== null) ? $_GET['ID'] : 0);

        // Check Number Post From Category
        if (isset($_GET['ID']) and $_GET['ID'] > 0) {
            $this_item                       = get_category((int)trim($_GET['ID']));
            $args['number_post_in_category'] = $this_item->count;
        }

        // Get Top Categories By Hits
        $args['top_list'] = array();
        if (!isset($_GET['ID']) || (isset($_GET['ID']) and $_GET['ID'] == 0)) {

            // Set Type List
            $args['top_list_type'] = 'category';
            $args['top_title']     = __('Top Category Sorted by Hits', 'wp-statistics');

            // Push List Category
            foreach ($terms as $category) {
                $args['top_list'][$category->term_id] = array('ID' => $category->term_id, 'name' => $category->name, 'link' => add_query_arg('ID', $category->term_id), 'count_visit' => (int)wp_statistics_pages('total', null, $category->term_id, null, null, 'category'));
            }

        } else {

            // Set Type List
            $args['top_list_type'] = 'post';
            $args['top_title']     = __('Top posts Sorted by Hits in this category', 'wp-statistics');

            // Get Top Posts From Category
            $post_lists = Helper::get_post_list(array(
                'post_type'    => 'post',
                'category__in' => $_GET['ID']
            ));
            foreach ($post_lists as $post_id => $post_title) {
                $args['top_list'][$post_id] = array('ID' => $post_id, 'name' => $post_title, 'link' => Menus::admin_url('pages', array('ID' => $post_id)), 'count_visit' => (int)wp_statistics_pages('total', null, $post_id, null, null, 'post'));
            }

        }

        // Sort By Visit Count
        Helper::SortByKeyValue($args['top_list'], 'count_visit');

        // Get Only 5 Item
        if (count($args['top_list']) > 5) {
            $args['top_list'] = array_chunk($args['top_list'], 5);
            $args['top_list'] = $args['top_list'][0];
        }

        // Show Template Page
        Admin_Template::get_template(array('layout/header', 'layout/title', 'layout/date.range', 'pages/category', 'layout/postbox.hide', 'layout/footer'), $args);
    }

}

new category_page;