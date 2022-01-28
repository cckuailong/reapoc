<?php

namespace WP_STATISTICS;

class Hits
{
    /**
     * Rest-APi Hit Record Params Key
     *
     * @var string
     */
    public static $rest_hits_key = 'wp_statistics_hit_rest';

    /**
     * Rest-Api Hit Data
     *
     * @var object
     */
    public $rest_hits;

    /**
     * WP_Statistics Hits Class.
     *
     * @throws \Exception
     */
    public function __construct()
    {

        # Sanitize Hit Data if Has Rest-Api Process
        if (self::is_rest_hit()) {

            # Get Hit Data
            $this->rest_hits = (object)self::rest_params();

            # Filter Data
            add_filter('wp_statistics_user_agent', array($this, 'set_user_agent'));
            add_filter('wp_statistics_user_referer', array($this, 'set_user_referer'));
            add_filter('wp_statistics_user_ip', array($this, 'set_user_ip'));
            add_filter('wp_statistics_hash_ip', array($this, 'set_hash_ip'));
            add_filter('wp_statistics_exclusion', array($this, 'set_exclusion'));
            add_filter('wp_statistics_user_http_agent', array($this, 'set_user_http_agent'));
            add_filter('wp_statistics_current_timestamp', array($this, 'set_current_timestamp'));
            add_filter('wp_statistics_current_page', array($this, 'set_current_page'));
            add_filter('wp_statistics_page_uri', array($this, 'set_page_uri'));
            add_filter('wp_statistics_user_id', array($this, 'set_user_id'));
            add_filter('wp_statistics_track_all_pages', array($this, 'set_track_all'));
        }

        # Record Login Page Hits
        if (!Option::get('exclude_loginpage')) {
            add_action('init', array($this, 'record_login_page_hits'));
        }

        # Record WordPress Front Page Hits
        add_action('wp', array($this, 'record_wp_hits'));
    }

    /**
     * Set User Agent
     *
     * @param $agent
     * @return array
     */
    public function set_user_agent($agent)
    {

        if (isset($this->rest_hits->browser) and isset($this->rest_hits->platform) and isset($this->rest_hits->version)) {
            return array(
                'browser'  => $this->rest_hits->browser,
                'platform' => $this->rest_hits->platform,
                'version'  => $this->rest_hits->version,
            );
        }

        return $agent;
    }

    /**
     * Set User Referer
     *
     * @param $referred
     * @return array
     */
    public function set_user_referer($referred)
    {
        return isset($this->rest_hits->referred) ? $this->rest_hits->referred : $referred;
    }

    /**
     * Set User IP
     *
     * @param $ip
     * @return string
     */
    public function set_user_ip($ip)
    {
        return isset($this->rest_hits->ip) ? $this->rest_hits->ip : $ip;
    }

    /**
     * Set Hash IP
     *
     * @param $hash_ip
     * @return mixed
     */
    public function set_hash_ip($hash_ip)
    {
        if (isset($this->rest_hits->ua) and trim($this->rest_hits->ua) != "") {
            $key = $this->rest_hits->ua;
        } else {
            $key = 'Unknown';
        }
        return $hash_ip = '#hash#' . sha1($this->rest_hits->ip . $key);
    }

    /**
     * Set Exclusion
     *
     * @param $exclude
     * @return array
     */
    public function set_exclusion($exclude)
    {

        if (isset($this->rest_hits->exclusion_match) and isset($this->rest_hits->exclusion_reason)) {
            return array(
                'exclusion_match' => $this->rest_hits->exclusion_match == 'yes',
                'exclusion_reason' => $this->rest_hits->exclusion_reason,
            );
        }

        return $exclude;
    }

    /**
     * Set User Http Agent
     *
     * @param $http_agent
     * @return string
     */
    public function set_user_http_agent($http_agent)
    {
        return isset($this->rest_hits->ua) ? $this->rest_hits->ua : $http_agent;
    }

    /**
     * Set Current timeStamp
     *
     * @param $timestamp
     * @return mixed
     */
    public function set_current_timestamp($timestamp)
    {
        return isset($this->rest_hits->timestamp) ? $this->rest_hits->timestamp : $timestamp;
    }

    /**
     * Set is track All Pages
     *
     * @param $track_all
     * @return mixed
     */
    public function set_track_all($track_all)
    {
        if (isset($this->rest_hits->track_all) and $this->rest_hits->track_all == 1) {
            $track_all = true;
        }

        return $track_all;
    }

    /**
     * Set Current Page
     *
     * @param $current_page
     * @return array
     */
    public function set_current_page($current_page)
    {

        if (isset($this->rest_hits->current_page_type) and isset($this->rest_hits->current_page_id)) {
            return array(
                'type'         => $this->rest_hits->current_page_type,
                'id'           => $this->rest_hits->current_page_id,
                'search_query' => isset($this->rest_hits->search_query) ? $this->rest_hits->search_query : ''
            );
        }

        return $current_page;
    }

    /**
     * Set Page Uri
     *
     * @param $page_uri
     * @return string
     */
    public function set_page_uri($page_uri)
    {
        return isset($this->rest_hits->page_uri) ? $this->rest_hits->page_uri : $page_uri;
    }

    /**
     * Set Current User ID
     *
     * @param $user_id
     * @return int
     */
    public function set_user_id($user_id)
    {
        return isset($this->rest_hits->user_id) ? $this->rest_hits->user_id : $user_id;
    }

    /**
     * Check If Record Hits in Rest-Api Request
     *
     * @return bool
     */
    public static function is_rest_hit()
    {
        return (Helper::is_rest_request() and isset($_REQUEST[self::$rest_hits_key]));
    }

    /**
     * Get Params Value in Rest-APi Request Hit
     *
     * @param $params
     * @return Mixed
     */
    public static function rest_params($params = false)
    {
        $data = array();
        if (Helper::is_rest_request() and isset($_REQUEST[Hits::$rest_hits_key])) {
            foreach ($_REQUEST as $key => $value) {
                if (!in_array($key, array('_', '_wpnonce'))) {
                    $data[$key] = $value;
                }
            }

            return ($params === false ? $data : (isset($data[$params]) ? $data[$params] : false));
        }

        return $data;
    }

    /**
     * Get Visitor information and Record To DB
     *
     * @throws \Exception
     */
    public static function record()
    {

        # Check Exclusion This Hits
        $exclusion = Exclusion::check();

        # Record Hits Exclusion
        if ($exclusion['exclusion_match'] === true) {
            Exclusion::record($exclusion);
        }

        # Record User Visits
        if (Visit::active() and $exclusion['exclusion_match'] === false) {
            Visit::record();
        }

        # Record Visitor Detail
        if (Visitor::active()) {
            $visitor_id = Visitor::record($exclusion);
        }

        # Record Search Engine
        if (isset($visitor_id) and $visitor_id > 0 and $exclusion['exclusion_match'] === false) {
            SearchEngine::record(array('visitor_id' => $visitor_id));
        }

        # Record Pages
        if (Pages::active() and $exclusion['exclusion_match'] === false and Pages::is_track_all_page() === true) {
            $page_id = Pages::record();
        }

        # Record Visitor Relation Ship
        if (isset($visitor_id) and $visitor_id > 0 and isset($page_id) and $page_id > 0 and Option::get('visitors_log')) {
            Visitor::save_visitors_relationships($page_id, $visitor_id);
        }

        # Record User Online
        if (UserOnline::active() and ($exclusion['exclusion_match'] === false || Option::get('all_online'))) {
            UserOnline::record();
        }

    }

    /**
     * Record Hits in Login Page
     *
     * @throws \Exception
     */
    public static function record_login_page_hits()
    {
        if (Helper::is_login_page()) {
            self::record();
        }
    }

    /**
     * Record WordPress Frontend Hits
     *
     * @throws \Exception
     */
    public static function record_wp_hits()
    {
        if (!Option::get('use_cache_plugin')) {
            Hits::record();
        }
    }
}

new Hits;