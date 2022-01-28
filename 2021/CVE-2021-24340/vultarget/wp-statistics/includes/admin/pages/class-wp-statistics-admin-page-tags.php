<?php

namespace WP_STATISTICS;

class tags_page
{

    public function __construct()
    {

        // Check if in category Page
        if (Menus::in_page('tags')) {

            // Disable Screen Option
            add_filter('screen_options_show_screen', '__return_false');

            // Is Validate Date Request
            $DateRequest = Admin_Template::isValidDateRequest();
            if (!$DateRequest['status']) {
                wp_die($DateRequest['message']);
            }

            // Check Validate int Params
            if (isset($_GET['ID']) and (!is_numeric($_GET['ID']) || ($_GET['ID'] != 0 and term_exists((int)trim($_GET['ID']), 'post_tag') == null))) {
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
        $args['title'] = __('Tag Statistics', 'wp-statistics');

        // Get Current Page Url
        $args['pageName']   = Menus::get_page_slug('tags');
        $args['pagination'] = Admin_Template::getCurrentPaged();

        // Get Date-Range
        $args['DateRang'] = Admin_Template::DateRange();

        // Create Select Box
        $args['select_box'] = array(
            'name'  => 'ID',
            'title' => __('Select Tag', 'wp-statistics')
        );

        // Get List Tag
        $terms = get_terms('post_tag', array(
            'hide_empty' => true
        ));

        // Set All Item
        $args['select_box']['list'][0] = __('All', 'wp-statistics');

        // Push To List Select
        foreach ($terms as $tag) {
            $args['select_box']['list'][$tag->term_id] = $tag->name;
        }

        // Check Active item
        $args['select_box']['active'] = ((isset($_GET['ID']) and term_exists((int)trim($_GET['ID']), 'post_tag') !== null) ? $_GET['ID'] : 0);

        // Check Number Post From Tag
        if (isset($_GET['ID']) and $_GET['ID'] > 0) {
            $this_item                  = get_tag((int)trim($_GET['ID']));
            $args['number_post_in_tag'] = $this_item->count;
        }

        // Get Top Tags By Hits
        $args['top_list'] = array();
        if (!isset($_GET['ID']) || (isset($_GET['ID']) and $_GET['ID'] == 0)) {

            // Set Type List
            $args['top_list_type'] = 'tag';
            $args['top_title']     = __('Top Tag Sorted by Hits', 'wp-statistics');

            // Push List Category
            foreach ($terms as $tag) {
                $args['top_list'][$tag->term_id] = array('ID' => $tag->term_id, 'name' => $tag->name, 'link' => add_query_arg('ID', $tag->term_id), 'count_visit' => (int)wp_statistics_pages('total', null, $tag->term_id, null, null, 'post_tag'));
            }

        } else {

            // Set Type List
            $args['top_list_type'] = 'post';
            $args['top_title']     = __('Top posts Sorted by Hits in this tag', 'wp-statistics');

            // Get Top Posts From Category
            $post_lists = Helper::get_post_list(array(
                'post_type' => 'post',
                'tag_id'    => $_GET['ID']
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
        Admin_Template::get_template(array('layout/header', 'layout/title', 'layout/date.range', 'pages/tag', 'layout/postbox.hide', 'layout/footer'), $args);
    }

}

new tags_page;