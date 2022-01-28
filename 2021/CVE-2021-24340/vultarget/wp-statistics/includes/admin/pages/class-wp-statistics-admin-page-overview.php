<?php

namespace WP_STATISTICS;

class log_page
{
    /**
     * Overview ADS API
     *
     * @var string
     */
    public static $overview_ads_API = 'https://wp-statistics.com/wp-json/ads/overview';

    /**
     * OverView Page Action
     */
    public function __construct()
    {

        // Load Meta Box List
        add_action('load-' . Menus::get_action_menu_slug('overview'), array($this, 'meta_box_init'));

        // Prepare OverView ADS
        add_action('load-' . Menus::get_action_menu_slug('overview'), array($this, 'overview_page_ads'));

        // Set default hidden Meta Box
        add_filter('default_hidden_meta_boxes', array($this, 'default_hidden_meta_boxes'), 10, 2);
    }

    /**
     * Define Meta Box
     */
    public function meta_box_init()
    {

        foreach (apply_filters('wp_statistics_overview_meta_box_list', Meta_Box::getList()) as $meta_key => $meta_box) {
            if (Option::check_option_require($meta_box) === true and ((isset($meta_box['disable_overview']) and $meta_box['disable_overview'] === false) || !isset($meta_box['disable_overview']))) {
                add_meta_box(Meta_Box::getMetaBoxKey($meta_key), $meta_box['name'], Meta_Box::LoadMetaBox($meta_key), Menus::get_action_menu_slug('overview'), $meta_box['place'], $control_callback = null, array('widget' => $meta_key));
            }
        }

    }

    /**
     * Display Html Page
     */
    public static function view()
    {
        $args['overview_page_slug'] = Menus::get_action_menu_slug('overview');
        Admin_Template::get_template(array('layout/header', 'layout/title', 'pages/overview', 'layout/footer'), $args);
    }

    /**
     * OverView Page Ads
     */
    public function overview_page_ads()
    {

        // Check Active Ads in OverView Page
        if (apply_filters('wp_statistics_ads_overview_page_show', true) === false) {
            return;
        }

        // Get Overview Ads
        $get_overview_ads = get_option('wp_statistics_overview_page_ads', false);

        // Check Expire or not exist
        if ($get_overview_ads === false || (is_array($get_overview_ads) and (current_time('timestamp') >= ($get_overview_ads['timestamp'] + WEEK_IN_SECONDS)))) {

            // Check Exist
            $overview_ads = ($get_overview_ads === false ? array() : $get_overview_ads);

            // Get New Ads from API
            $request = wp_remote_get(self::$overview_ads_API, array('timeout' => 30));
            if (is_wp_error($request)) {
                return;
            }

            // Get Json Data
            $data = json_decode(wp_remote_retrieve_body($request), true);

            // Set new Timestamp
            $overview_ads['timestamp'] = current_time('timestamp');

            // Set Ads
            $overview_ads['ads'] = (empty($data) ? array('status' => 'no', 'ID' => 'none') : $data);

            // Set Last Viewed
            $overview_ads['view'] = (isset($get_overview_ads['view']) ? $get_overview_ads['view'] : '');

            // Set Option
            update_option('wp_statistics_overview_page_ads', $overview_ads, 'no');
        }
    }

    /**
     * OverView Default Hidden Meta Box
     *
     * @param $hidden | array list of default hidden meta box
     * @param $screen | WordPress `global $current_screen`
     * @return mixed
     */
    public function default_hidden_meta_boxes($hidden, $screen)
    {
        if ($screen->id == Menus::get_action_menu_slug('overview')) {
            foreach (apply_filters('wp_statistics_overview_meta_box_list', Meta_Box::getList()) as $meta_key => $meta_box) {
                if (isset($meta_box['hidden_overview']) and $meta_box['hidden_overview'] === true) {
                    $hidden[] = Meta_Box::getMetaBoxKey($meta_key);
                }
            }
        }
        return $hidden;
    }
}

new log_page();