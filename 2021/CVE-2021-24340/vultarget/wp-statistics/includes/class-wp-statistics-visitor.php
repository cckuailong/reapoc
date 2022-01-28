<?php

namespace WP_STATISTICS;

class Visitor
{
    /**
     * For each visit to account for several hits.
     *
     * @var int
     */
    public static $coefficient = 1;

    /**
     * Get Coefficient
     */
    public static function getCoefficient()
    {
        $coefficient = Option::get('coefficient', self::$coefficient);
        return (is_numeric($coefficient) and $coefficient > 0) ? $coefficient : self::$coefficient;
    }

    /**
     * Check Active Record Visitors
     *
     * @return mixed
     */
    public static function active()
    {
        return (has_filter('wp_statistics_active_visitors')) ? apply_filters('wp_statistics_active_visitors', true) : Option::get('visitors');
    }

    /**
     * Save new Visitor To DB
     *
     * @param array $visitor
     * @return INT
     */
    public static function save_visitor($visitor = array())
    {
        global $wpdb;

        # Add Filter Insert ignore
        add_filter('query', array('\WP_STATISTICS\DB', 'insert_ignore'), 10);

        # Save to WordPress Database
        $insert = $wpdb->insert(
            DB::table('visitor'),
            $visitor
        );
        if (!$insert) {
            if (!empty($wpdb->last_error)) {
                \WP_Statistics::log($wpdb->last_error);
            }
        }

        # Get Visitor ID
        $visitor_id = $wpdb->insert_id;

        # Remove ignore filter
        remove_filter('query', array('\WP_STATISTICS\DB', 'insert_ignore'), 10);

        # Do Action After Save New Visitor
        do_action('wp_statistics_save_visitor', $visitor_id, $visitor, Pages::get_page_type());

        return $visitor_id;
    }

    /**
     * Check This ip has recorded in Custom Day
     *
     * @param $ip
     * @param $date
     * @return bool
     */
    public static function exist_ip_in_day($ip, $date = false)
    {
        global $wpdb;
        $visitor = $wpdb->get_row("SELECT * FROM `" . DB::table('visitor') . "` WHERE `last_counter` = '" . ($date === false ? TimeZone::getCurrentDate('Y-m-d') : $date) . "' AND `ip` = '{$ip}'");
        return (!$visitor ? false : $visitor);
    }

    /**
     * Record Uniq Visitor Detail in DB
     *
     * @param array $arg
     * @return bool|INT
     * @throws \Exception
     */
    public static function record($arg = array())
    {
        global $wpdb;

        // Define the array of defaults
        $defaults = array(
            'location'         => GeoIP::getDefaultCountryCode(),
            'exclusion_match'  => false,
            'exclusion_reason' => '',
        );
        $args     = wp_parse_args($arg, $defaults);

        // Check User Exclusion
        if ($args['exclusion_match'] === false || $args['exclusion_reason'] == 'Honeypot') {

            // Get User IP
            $user_ip = (IP::getHashIP() != false ? IP::getHashIP() : IP::StoreIP());

            // Get User Agent
            $user_agent = UserAgent::getUserAgent();

            //Check Exist This User in Current Day
            $same_visitor = self::exist_ip_in_day($user_ip);

            // If we have a new Visitor in Day
            if (!$same_visitor) {

                // Prepare Visitor information
                $visitor = array(
                    'last_counter' => TimeZone::getCurrentDate('Y-m-d'),
                    'referred'     => Referred::get(),
                    'agent'        => $user_agent['browser'],
                    'platform'     => $user_agent['platform'],
                    'version'      => $user_agent['version'],
                    'ip'           => $user_ip,
                    'location'     => GeoIP::getCountry(IP::getIP()),
                    'user_id'      => User::get_user_id(),
                    'UAString'     => (Option::get('store_ua') == true ? UserAgent::getHttpUserAgent() : ''),
                    'hits'         => 1,
                    'honeypot'     => ($args['exclusion_reason'] == 'Honeypot' ? 1 : 0),
                );
                $visitor = apply_filters('wp_statistics_visitor_information', $visitor);

                //Save Visitor TO DB
                $visitor_id = self::save_visitor($visitor);

            } else {

                //Get Current Visitor ID
                $visitor_id = $same_visitor->ID;

                // Update Same Visitor Hits
                if ($args['exclusion_reason'] != 'Honeypot' and $args['exclusion_reason'] != 'Robot threshold') {

                    // Action Before Visitor Update
                    do_action('wp_statistics_update_visitor_hits', $visitor_id, $same_visitor);

                    // Update Visitor Count in DB
                    $wpdb->query($wpdb->prepare('UPDATE `' . DB::table('visitor') . '` SET `hits` = `hits` + %d WHERE `ID` = %d', 1, $visitor_id));
                }
            }
        }

        return (isset($visitor_id) ? $visitor_id : false);
    }

    /**
     * Save visitor relationShip
     *
     * @param $page_id
     * @param $visitor_id
     * @return int
     */
    public static function save_visitors_relationships($page_id, $visitor_id)
    {
        global $wpdb;

        // Save To DB
        $insert = $wpdb->insert(
            DB::table('visitor_relationships'),
            array(
                'visitor_id' => $visitor_id,
                'page_id'    => $page_id,
                'date'       => current_time('mysql')
            ),
            array('%d', '%d', '%s')
        );
        if (!$insert) {
            if (!empty($wpdb->last_error)) {
                \WP_Statistics::log($wpdb->last_error);
            }
        }
        $insert_id = $wpdb->insert_id;

        // Save visitor Relationship Action
        do_action('wp_statistics_save_visitor_relationship', $page_id, $visitor_id, $insert_id);

        return $insert_id;
    }

    /**
     * Get Top Visitors
     *
     * @param array $arg
     * @return array
     * @throws \Exception
     */
    public static function getTop($arg = array())
    {

        // Define the array of defaults
        $defaults = array(
            'day'      => 'today',
            'per_page' => 10,
            'paged'    => 1,
        );
        $args     = wp_parse_args($arg, $defaults);

        // Prepare time
        if ($args['day'] == 'today') {
            $sql_time = TimeZone::getCurrentDate('Y-m-d');
        } else {
            $sql_time = date('Y-m-d', strtotime($args['day']));
        }

        // Prepare Query
        $args['sql'] = "SELECT * FROM `" . DB::table('visitor') . "` WHERE last_counter = '{$sql_time}' ORDER BY hits DESC";

        // Get Visitors Data
        return self::get($args);
    }

    /**
     * Get Visitors List By Custom Query
     *
     * @param array $arg
     * @return mixed
     * @throws \Exception
     */
    public static function get($arg = array())
    {
        global $wpdb;

        // Define the array of defaults
        $defaults = array(
            'sql'      => '',
            'per_page' => 10,
            'paged'    => 1,
            'fields'   => 'all',
            'order'    => 'DESC',
            'orderby'  => 'ID'
        );
        $args     = wp_parse_args($arg, $defaults);

        // Prepare Query
        if (empty($args['sql'])) {
            $args['sql'] = "SELECT * FROM `" . DB::table('visitor') . "` ORDER BY ID DESC";
        }

        // Set Pagination
        $args['sql'] = $args['sql'] . " LIMIT " . (($args['paged'] - 1) * $args['per_page']) . ", {$args['per_page']}";

        // Send Request
        $result = $wpdb->get_results($args['sql']);

        // Get Visitor Data
        return self::PrepareData($result);
    }

    /**
     * Prepare Visitor Data
     *
     * @param array $result
     * @return array
     * @throws \Exception
     */
    public static function PrepareData($result = array())
    {

        // Prepare List
        $list = array();

        // Push to List
        foreach ($result as $items) {

            $item = array(
                'hits'     => (int)$items->hits,
                'referred' => Referred::get_referrer_link($items->referred),
                'refer'    => $items->referred,
                'date'     => date_i18n(get_option('date_format'), strtotime($items->last_counter)),
                'agent'    => $items->agent,
                'platform' => $items->platform,
                'version'  => $items->version
            );

            // Push User Data
            if ($items->user_id > 0 and User::exists($items->user_id)) {
                $user_data    = User::get($items->user_id);
                $item['user'] = array(
                    'ID'         => $items->user_id,
                    'user_login' => $user_data['user_login']
                );
            }

            // Push Browser
            $item['browser'] = array(
                'name' => $items->agent,
                'logo' => UserAgent::getBrowserLogo($items->agent),
                'link' => Menus::admin_url('overview', array('agent' => $items->agent))
            );

            // Push IP
            if (IP::IsHashIP($items->ip)) {
                $item['hash_ip'] = IP::$hash_ip_prefix;
            } else {
                $item['ip']  = array('value' => $items->ip, 'link' => Menus::admin_url('visitors', array('ip' => $items->ip)));
                $item['map'] = GeoIP::geoIPTools($items->ip);
            }

            // Push Country
            if (GeoIP::active()) {
                $item['country'] = array('location' => $items->location, 'flag' => Country::flag($items->location), 'name' => Country::getName($items->location));
            }

            // Push City
            if (GeoIP::active('city')) {
                $item['city'] = GeoIP::getCity($items->ip);
            }

            // Check If Search Word
            if (isset($items->words)) {
                $item['word'] = $items->words;
            }

            // Get What is Page
            if (Option::get('visitors_log')) {
                $item['page'] = self::get_page_by_visitor_id($items->ID);
            }

            $list[] = $item;
        }

        return $list;
    }

    /**
     * Get Page Information By visitor ID
     *
     * @param $visitor_ID
     * @return mixed
     */
    public static function get_page_by_visitor_id($visitor_ID)
    {
        global $wpdb;

        // Default Params
        $params = array('link' => '', 'title' => '');

        // Check Active Visitors Log
        if (!Option::get('visitors_log')) {
            return $params;
        }

        // Get Row
        $item = $wpdb->get_row(" SELECT " . DB::table('pages') . ".* FROM `" . DB::table('pages') . "` INNER JOIN `" . DB::table('visitor_relationships') . "` ON `" . DB::table('pages') . "`.`page_id` = `" . DB::table('visitor_relationships') . "`.`page_id` INNER JOIN `" . DB::table('visitor') . "` ON `" . DB::table('visitor_relationships') . "`.`visitor_id` = `" . DB::table('visitor') . "`.`ID` WHERE `" . DB::table('visitor') . "`.`ID` = {$visitor_ID};", ARRAY_A);
        if ($item !== null) {
            $params = Pages::get_page_info($item['id'], $item['type']);
        }

        return $params;
    }

    /**
     * Count User By Custom Filter
     *
     * @param array $args
     * @return int
     */
    public static function Count($args = array())
    {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM `" . DB::table('visitor') . "`";
        $sql .= Helper::getConditionSQL($args);
        return $wpdb->get_var($sql);
    }

    /**
     * Get List Of visitor that Registered in WordPress Users
     *
     * @return array
     */
    public static function get_users_visitor()
    {
        global $wpdb;
        $query = $wpdb->get_results("SELECT `user_id` FROM `" . DB::table('visitor') . "` WHERE `user_id` >0 AND EXISTS (SELECT `ID` FROM `{$wpdb->users}` WHERE " . DB::table('visitor') . ".user_id = {$wpdb->users}.ID) GROUP BY `user_id` ORDER BY `user_id` DESC", ARRAY_A);
        $item  = array();
        foreach ($query as $row) {
            $user_data             = User::get($row['user_id']);
            $item[$row['user_id']] = array(
                'user_login' => $user_data['user_login'],
                'user_email' => $user_data['user_email']
            );
        }

        return $item;
    }

}