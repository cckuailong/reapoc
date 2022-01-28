<?php

namespace WP_STATISTICS;

class Frontend
{

    public function __construct()
    {

        # Enable ShortCode in Widget
        add_filter('widget_text', 'do_shortcode');

        # Add the honey trap code in the footer.
        add_action('wp_footer', array($this, 'add_honeypot'));

        # Enqueue scripts & styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        # Add inline Rest Request
        add_action('wp_head', array($this, 'add_inline_rest_js'));

        # Add Html Comment in head
        if (!Option::get('use_cache_plugin')) {
            add_action('wp_head', array($this, 'html_comment'));
        }

        # Check to show hits in posts/pages
        if (Option::get('show_hits')) {
            add_filter('the_content', array($this, 'show_hits'));
        }
    }

    /*
     * Create Comment support Wappalyzer
     */
    public function html_comment()
    {
        echo '<!-- Analytics by WP-Statistics v' . WP_STATISTICS_VERSION . ' - ' . WP_STATISTICS_SITE . ' -->' . "\n";
    }

    /**
     * Footer Action
     */
    public function add_honeypot()
    {
        if (Option::get('use_honeypot') && Option::get('honeypot_postid') > 0) {
            $post_url = get_permalink(Option::get('honeypot_postid'));
            echo '<a href="' . $post_url . '" style="display: none;">&nbsp;</a>';
        }
    }

    /**
     * Enqueue Scripts
     */
    public function enqueue_scripts()
    {

        // Load Admin Bar Css
        if (AdminBar::show_admin_bar() and is_admin_bar_showing()) {
            wp_enqueue_style('wp-statistics', WP_STATISTICS_URL . 'assets/css/frontend.min.css', true, WP_STATISTICS_VERSION);
        }
    }

    /*
     * Inline Js
     */
    public function add_inline_rest_js()
    {
        if (Option::get('use_cache_plugin')) {

            // Wp-Statistics HTML comment
            $this->html_comment();

            // Prepare Params
            $params = array_merge(array(
                '_'                  => time(),
                '_wpnonce'           => wp_create_nonce('wp_rest'),
                Hits::$rest_hits_key => 'yes',
            ), self::set_default_params());

            // Return Script
            echo '<script>var WP_Statistics_http = new XMLHttpRequest();WP_Statistics_http.open(\'GET\', \'' . add_query_arg($params, get_rest_url(null, RestAPI::$namespace . '/' . Api\v2\Hit::$endpoint)) . '\', true);WP_Statistics_http.setRequestHeader("Content-Type", "application/json;charset=UTF-8");WP_Statistics_http.send(null);</script>' . "\n";
        }
    }

    /*
     * Set Default Params Rest Api
     */
    static public function set_default_params()
    {

        // Create Empty Params Object
        $params = array();

        //Set UserAgent [browser|platform|version]
        $params = wp_parse_args($params, UserAgent::getUserAgent());

        //Set Referred
        $params['referred'] = Referred::get();

        //Set IP
        $params['ip'] = esc_html(IP::getIP());

        //exclude
        $exclude                  = Exclusion::check();
        $params['exclusion_match'] = ($exclude['exclusion_match'] === true ? 'yes' : 'no');
        $params['exclusion_reason'] = (string)$exclude['exclusion_reason'];

        //User Agent String
        $params['ua'] = esc_html(UserAgent::getHttpUserAgent());

        //track all page
        $params['track_all'] = (Pages::is_track_all_page() === true ? 1 : 0);

        //timestamp
        $params['timestamp'] = Timezone::getCurrentTimestamp();

        //Set Page Type
        $get_page_type               = Pages::get_page_type();
        $params['current_page_type'] = $get_page_type['type'];
        $params['current_page_id']   = $get_page_type['id'];
        $params['search_query']      = (isset($get_page_type['search_query']) ? esc_html($get_page_type['search_query']) : '');

        //page url
        $params['page_uri'] = Pages::get_page_uri();

        //Get User id
        $params['user_id'] = User::get_user_id();

        //return Json Data
        return $params;
    }

    /**
     * Show Hits in After WordPress the_content
     *
     * @param $content
     * @return string
     */
    public function show_hits($content)
    {

        // Get post ID
        $post_id = get_the_ID();

        // Check post ID
        if (!$post_id) {
            return $content;
        }

        // Get post hits
        $hits      = wp_statistics_pages('total', "", $post_id);
        $hits_html = '<p>' . sprintf(__('Hits: %s', 'wp-statistics'), $hits) . '</p>';

        // Check hits position
        if (Option::get('display_hits_position') == 'before_content') {
            return $hits_html . $content;
        } elseif (Option::get('display_hits_position') == 'after_content') {
            return $content . $hits_html;
        } else {
            return $content;
        }
    }
}

new Frontend;