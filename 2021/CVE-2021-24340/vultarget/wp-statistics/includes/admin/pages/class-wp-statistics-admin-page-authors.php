<?php

namespace WP_STATISTICS;

class authors_page
{

    public function __construct()
    {

        // Check if in category Page
        if (Menus::in_page('authors')) {

            // Disable Screen Option
            add_filter('screen_options_show_screen', '__return_false');

            // Is Validate Date Request
            $DateRequest = Admin_Template::isValidDateRequest();
            if (!$DateRequest['status']) {
                wp_die($DateRequest['message']);
            }

            // Check Validate int Params
            if (isset($_GET['ID']) and (!is_numeric($_GET['ID']) || ($_GET['ID'] != 0 and User::exists((int)trim($_GET['ID'])) === false))) {
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
        $args['title'] = __('Author Statistics', 'wp-statistics');

        // Get Current Page Url
        $args['pageName']   = Menus::get_page_slug('authors');
        $args['pagination'] = Admin_Template::getCurrentPaged();

        // Get Date-Range
        $args['DateRang'] = Admin_Template::DateRange();

        // Create Select Box
        $args['select_box'] = array(
            'name'  => 'ID',
            'title' => __('Select Author', 'wp-statistics')
        );

        // Get List Authors
        $users = get_users((User::Access('manage') ? array('role__in' => array('author', 'administrator')) : array('role__in' => 'author')));

        // Set All Item
        $args['select_box']['list'][0] = __('All', 'wp-statistics');

        // Push to List User
        foreach ($users as $user) {
            $args['select_box']['list'][$user->ID] = User::get_name($user->ID);
        }

        // Check Active User
        $args['select_box']['active'] = ((isset($_GET['ID']) and User::exists($_GET['ID']) === true) ? $_GET['ID'] : 0);

        // Check Number Post From a author
        if (isset($_GET['ID']) and $_GET['ID'] > 0) {
            $args['number_post_from_user'] = count_user_posts((int)trim($_GET['ID']));
        }

        // Get Top Categories By Hits
        $args['top_list'] = array();
        if (!isset($_GET['ID']) || (isset($_GET['ID']) and $_GET['ID'] == 0)) {

            // Set Type List
            $args['top_list_type'] = 'user';
            $args['top_title']     = __('Top Author Sorted by Hits', 'wp-statistics');

            // Push List Category
            foreach ($users as $user) {
                $args['top_list'][$user->ID] = array('ID' => $user->ID, 'name' => User::get_name($user->ID), 'link' => add_query_arg('ID', $user->ID), 'count_visit' => (int)wp_statistics_pages('total', null, $user->ID, null, null, 'author'));
            }

        } else {

            // Set Type List
            $args['top_list_type'] = 'post';
            $args['top_title']     = __('Top posts Sorted by Hits from the author', 'wp-statistics');

            // Get Top Posts From Category
            $post_lists = Helper::get_post_list(array(
                'post_type' => 'post',
                'author'    => $_GET['ID']
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
        Admin_Template::get_template(array('layout/header', 'layout/title', 'layout/date.range', 'pages/author', 'layout/postbox.hide', 'layout/footer'), $args);
    }

}

new authors_page;