<?php

namespace WP_STATISTICS;

class Pages
{
    /**
     * Check Active Record Pages
     *
     * @return mixed
     */
    public static function active()
    {
        return (has_filter('wp_statistics_active_pages')) ? apply_filters('wp_statistics_active_pages', true) : Option::get('pages');
    }

    /**
     * Get WordPress Page Type
     */
    public static function get_page_type()
    {

        //Set Default Option
        $current_page = array("type" => "unknown", "id" => 0);

        //Check Query object
        $id = get_queried_object_id();
        if (is_numeric($id) and $id > 0) {
            $current_page['id'] = $id;
        }

        //WooCommerce Product
        if (class_exists('WooCommerce')) {
            if (is_product()) {
                return wp_parse_args(array("type" => "product"), $current_page);
            }
        }

        //Home Page or Front Page
        if (is_front_page() || is_home()) {
            return wp_parse_args(array("type" => "home"), $current_page);
        }

        //attachment View
        if (is_attachment()) {
            $current_page['type'] = "attachment";
        }

        //is Archive Page
        if (is_archive()) {
            $current_page['type'] = "archive";
        }

        //Single Post Fro All Post Type
        if (is_singular()) {
            $current_page['type'] = "post";
        }

        //Single Page
        if (is_page()) {
            $current_page['type'] = "page";
        }

        //Category Page
        if (is_category()) {
            $current_page['type'] = "category";
        }

        //Tag Page
        if (is_tag()) {
            $current_page['type'] = "post_tag";
        }

        //is Custom Term From Taxonomy
        if (is_tax()) {
            $current_page['type'] = "tax";
        }

        //is Author Page
        if (is_author()) {
            $current_page['type'] = "author";
        }

        //is search page
        $search_query = filter_var(get_search_query(false), FILTER_SANITIZE_STRING);
        if (trim($search_query) != "") {
            return array("type" => "search", "id" => 0, "search_query" => $search_query);
        }

        //is 404 Page
        if (is_404()) {
            $current_page['type'] = "404";
        }

        // Add WordPress Feed
        if (is_feed()) {
            $current_page['type'] = "feed";
        }

        // Add WordPress Login Page
        if (Helper::is_login_page()) {
            $current_page['type'] = "loginpage";
        }

        return apply_filters('wp_statistics_current_page', $current_page);
    }

    /**
     * Check Track All Page WP-Statistics
     *
     * @return bool
     */
    public static function is_track_all_page()
    {
        return apply_filters('wp_statistics_track_all_pages', Option::get('track_all_pages') || is_single() || is_page() || is_front_page());
    }

    /**
     * Get Page Url
     *
     * @return bool|mixed|string
     */
    public static function get_page_uri()
    {

        // Get the site's path from the URL.
        $site_uri = parse_url(site_url(), PHP_URL_PATH);
        $site_uri_len = strlen($site_uri);

        // Get the site's path from the URL.
        $home_uri = parse_url(home_url(), PHP_URL_PATH);
        $home_uri_len = strlen($home_uri);

        // Get the current page URI.
        $page_uri = $_SERVER["REQUEST_URI"];

        /*
         * We need to check which URI is longer in case one contains the other.
         * For example home_uri might be "/site/wp" and site_uri might be "/site".
         * In that case we want to check to see if the page_uri starts with "/site/wp" before
         * we check for "/site", but in the reverse case, we need to swap the order of the check.
         */
        if ($site_uri_len > $home_uri_len) {
            if (substr($page_uri, 0, $site_uri_len) == $site_uri) {
                $page_uri = substr($page_uri, $site_uri_len);
            }

            if (substr($page_uri, 0, $home_uri_len) == $home_uri) {
                $page_uri = substr($page_uri, $home_uri_len);
            }
        } else {
            if (substr($page_uri, 0, $home_uri_len) == $home_uri) {
                $page_uri = substr($page_uri, $home_uri_len);
            }

            if (substr($page_uri, 0, $site_uri_len) == $site_uri) {
                $page_uri = substr($page_uri, $site_uri_len);
            }
        }

        //Sanitize Xss injection
        $page_uri = filter_var($page_uri, FILTER_SANITIZE_STRING);

        // If we're at the root (aka the URI is blank), let's make sure to indicate it.
        if ($page_uri == '') {
            $page_uri = '/';
        }

        return apply_filters('wp_statistics_page_uri', $page_uri);
    }

    /**
     * Sanitize Page Url For Push to Database
     */
    public static function sanitize_page_uri()
    {

        // Get Current WordPress Page
        $current_page = self::get_page_type();

        // Get the current page URI.
        $page_uri = Pages::get_page_uri();

        // Get String Search Wordpress
        if (array_key_exists("search_query", $current_page) and !empty($current_page["search_query"])) {
            $page_uri = "?s=" . $current_page['search_query'];
        }

        // Sanitize for WordPress Login Page
        if ($current_page['type'] == "loginpage") {
            $page_uri = Helper::RemoveQueryStringUrl($page_uri);
        }

        // Check Strip Url Parameter
        if (Option::get('strip_uri_parameters') and array_key_exists("search_query", $current_page) === false) {
            $temp = explode('?', $page_uri);
            if ($temp !== false) {
                $page_uri = $temp[0];
            }
        }

        // Limit the URI length to 255 characters, otherwise we may overrun the SQL field size.
        return substr($page_uri, 0, 255);
    }

    /**
     * Record Page in Database
     */
    public static function record()
    {
        global $wpdb;

        // Get Current WordPress Page
        $current_page = self::get_page_type();

        // If we didn't find a page id, we don't have anything else to do.
        if ($current_page['type'] == "unknown" || !isset($current_page['id'])) {
            return false;
        }

        // Get Page uri
        $page_uri = self::sanitize_page_uri();

        // Check if we have already been to this page today.
        $exist = $wpdb->get_row("SELECT `page_id` FROM `" . DB::table('pages') . "` WHERE `date` = '" . TimeZone::getCurrentDate('Y-m-d') . "' " . (array_key_exists("search_query", $current_page) === true ? "AND `uri` = '" . esc_sql($page_uri) . "'" : "") . "AND `type` = '{$current_page['type']}' AND `id` = {$current_page['id']}", ARRAY_A);

        // Update Exist Page
        if (null !== $exist) {

            $wpdb->query($wpdb->prepare("UPDATE `" . DB::table('pages') . "` SET `count` = `count` + 1 WHERE `date` = '" . TimeZone::getCurrentDate('Y-m-d') . "' " . (array_key_exists("search_query", $current_page) === true ? "AND `uri` = '" . esc_sql($page_uri) . "'" : "") . "AND `type` = '{$current_page['type']}' AND `id` = %d", $current_page['id']));
            $page_id = $exist['page_id'];

        } else {

            // Prepare Pages Data
            $pages = array(
                'uri' => $page_uri,
                'date' => TimeZone::getCurrentDate('Y-m-d'),
                'count' => 1,
                'id' => $current_page['id'],
                'type' => $current_page['type']
            );
            $pages = apply_filters('wp_statistics_pages_information', $pages);

            // Added to DB
            $page_id = self::save_page($pages);
        }

        return (isset($page_id) ? $page_id : false);
    }

    /**
     * Add new row to Pages Table
     *
     * @param array $page
     * @return int
     */
    public static function save_page($page = array())
    {
        global $wpdb;

        # Add Filter Insert ignore
        add_filter('query', array('\WP_STATISTICS\DB', 'insert_ignore'), 10);

        # Save to WordPress Database
        $insert = $wpdb->insert(
            DB::table('pages'),
            $page
        );
        if (!$insert) {
            if (!empty($wpdb->last_error)) {
                \WP_Statistics::log($wpdb->last_error);
            }
        }

        # Get Page ID
        $page_id = $wpdb->insert_id;

        # Remove ignore filter
        remove_filter('query', array('\WP_STATISTICS\DB', 'insert_ignore'), 10);

        # Do Action After Save New Visitor
        do_action('wp_statistics_save_page', $page_id, $page);

        return $page_id;
    }

    /**
     * Get Page information
     *
     * @param $page_id
     * @param string $type
     * @return array
     */
    public static function get_page_info($page_id, $type = 'post')
    {

        //Create Empty Object
        $arg = array();
        $defaults = array(
            'link' => '',
            'edit_link' => '',
            'object_id' => $page_id,
            'title' => '-',
            'meta' => array()
        );

        if (!empty($type)) {
            switch ($type) {
                case "product":
                case "attachment":
                case "post":
                case "page":
                    $arg = array(
                        'title' => esc_html(get_the_title($page_id)),
                        'link' => get_the_permalink($page_id),
                        'edit_link' => get_edit_post_link($page_id),
                        'meta' => array(
                            'post_type' => get_post_type($page_id)
                        )
                    );
                    break;
                case "category":
                case "post_tag":
                case "tax":
                    $term = get_term($page_id);
                    if (!is_wp_error($term) and $term !== null) {
                        $arg = array(
                            'title' => esc_html($term->name),
                            'link' => (is_wp_error(get_term_link($page_id)) === true ? '' : get_term_link($page_id)),
                            'edit_link' => get_edit_term_link($page_id),
                            'meta' => array(
                                'taxonomy' => $term->taxonomy,
                                'term_taxonomy_id' => $term->term_taxonomy_id,
                                'count' => $term->count
                            )
                        );
                    }
                    break;
                case "home":
                    $arg = array(
                        'title' => __('Home Page', 'wp-statistics'),
                        'link' => get_site_url()
                    );
                    break;
                case "author":
                    $user_info = get_userdata($page_id);
                    $arg = array(
                        'title' => ($user_info->display_name != "" ? esc_html($user_info->display_name) : esc_html($user_info->first_name . ' ' . $user_info->last_name)),
                        'link' => get_author_posts_url($page_id),
                        'edit_link' => get_edit_user_link($page_id),
                    );
                    break;
                case "feed":
                    $result['title'] = __('Feed', 'wp-statistics');
                    break;
                case "loginpage":
                    $result['title'] = __('Login Page', 'wp-statistics');
                    break;
                case "search":
                    $result['title'] = __('Search Page', 'wp-statistics');
                    break;
                case "404":
                    $result['title'] = __('404 not found', 'wp-statistics');
                    break;
                case "archive":
                    $result['title'] = __('Post Archive', 'wp-statistics');
                    break;
            }
        }

        return wp_parse_args($arg, $defaults);
    }

    /**
     * Get Top number of Hits Pages
     *
     * @param array $args
     * @return array|int|mixed
     */
    public static function getTop($args = array())
    {
        global $wpdb;

        // Define the array of defaults
        $defaults = array(
            'per_page' => 10,
            'paged' => 1,
            'from' => '',
            'to' => ''
        );
        $args = wp_parse_args($args, $defaults);

        // Date Time SQL
        $DateTimeSql = "";
        if (!empty($args['from']) and !empty($args['to'])) {
            $DateTimeSql = "WHERE (`pages`.`date` BETWEEN '{$args['from']}' AND '{$args['to']}')";
        }

        // Generate SQL
        $sql = "SELECT `pages`.`date`,`pages`.`uri`,`pages`.`id`,`pages`.`type`, SUM(`pages`.`count`) + IFNULL(`historical`.`value`, 0) AS `count_sum` FROM `" . DB::table('pages') . "` `pages` LEFT JOIN `" . DB::table('historical') . "` `historical` ON `pages`.`uri`=`historical`.`uri` AND `historical`.`category`='uri' {$DateTimeSql} GROUP BY `uri` ORDER BY `count_sum` DESC";

        // Get List Of Pages
        $list = array();
        $result = $wpdb->get_results($sql . " LIMIT " . ($args['paged'] - 1) * $args['per_page'] . "," . $args['per_page']);
        foreach ($result as $item) {

            // Lookup the post title.
            $page_info = Pages::get_page_info($item->id, $item->type);

            // Push to list
            $list[] = array(
                'title' => $page_info['title'],
                'link' => $page_info['link'],
                'str_url' => htmlentities(urldecode($item->uri), ENT_QUOTES),
                'hits_page' => Menus::admin_url('pages', array('ID' => $item->id, 'type' => $item->type)),
                'number' => number_format_i18n($item->count_sum)
            );
        }

        return $list;
    }

    /**
     * Count Number Page in DB Table
     *
     * @param string $group_by
     * @param array $args
     * @return mixed
     */
    public static function TotalCount($group_by = 'uri', $args = array())
    {
        global $wpdb;
        $where = '';

        // Date
        if (isset($args['from']) and isset($args['to']) and !empty($args['from']) and !empty($args['to'])) {
            $where .= "WHERE `date` BETWEEN '{$args['from']}' AND '{$args['to']}'";
        }

        // Return
        return $wpdb->get_var("SELECT COUNT(*) FROM `" . DB::table('pages') . "` `pages` {$where} GROUP BY `{$group_by}`");
    }

    /**
     * Get Post Type by ID
     *
     * @param $post_id
     * @return string
     */
    public static function get_post_type($post_id)
    {
        $post_type = get_post_type($post_id);
        return (in_array($post_type, array("page", "product", "attachment")) ? $post_type : "post");
    }

    /**
     * Convert Url to Page ID
     *
     * @param $uri
     * @return int
     */
    public static function uri_to_id($uri)
    {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT id FROM `" . DB::table('pages') . "` WHERE `uri` = %s and id > 0 ORDER BY date DESC", $uri);
        $result = $wpdb->get_var($sql);
        if ($result == 0) {
            $result = 0;
        }

        return $result;
    }
}