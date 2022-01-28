<?php

namespace WP_STATISTICS;

class UserOnline
{
    /**
     * Check Users Online Option name
     *
     * @var string
     */
    public static $check_user_online_opt = 'wp_statistics_check_user_online';

    /**
     * Default User Reset Time User Online
     *
     * @var int
     */
    public static $reset_user_time = 120; # Second

    /**
     * UserOnline constructor.
     */
    public function __construct()
    {

        # Reset User Online Count
        add_action('wp_loaded', array($this, 'reset_user_online'));
    }

    /**
     * Check Active User Online System
     *
     * @return mixed
     */
    public static function active()
    {
        /**
         * Disable/Enable User Online for Custom request
         *
         * @example add_filter('wp_statistics_active_user_online', function(){ if( is_page() ) { return false; } });
         */
        return (has_filter('wp_statistics_active_user_online')) ? apply_filters('wp_statistics_active_user_online', true) : Option::get('useronline');
    }

    /**
     * Reset Online User Process By Option time
     *
     * @return string
     */
    public function reset_user_online()
    {
        global $wpdb;

        //Check User Online is Active in this Wordpress
        if (self::active()) {

            //Get Not timestamp
            $now = TimeZone::getCurrentTimestamp();

            // Set the default seconds a user needs to visit the site before they are considered offline.
            $reset_time = self::$reset_user_time;

            // Get the user set value for seconds to check for users online.
            if (Option::get('check_online')) {
                $reset_time = Option::get('check_online');
            }

            // We want to delete users that are over the number of seconds set by the admin.
            $time_diff = $now - $reset_time;

            //Last check Time
            $wps_run = get_option(self::$check_user_online_opt);
            if (isset($wps_run) and is_numeric($wps_run)) {
                if (($wps_run + $reset_time) > $now) {
                    return;
                }
            }

            // Call the deletion query.
            $wpdb->query("DELETE FROM `" . DB::table('useronline') . "` WHERE timestamp < {$time_diff}");

            //Update Last run this Action
            update_option(self::$check_user_online_opt, $now);
        }
    }

    /**
     * Record Users Online
     *
     * @param array $args
     * @throws \Exception
     */
    public static function record($args = array())
    {

        # Get User IP
        $user_ip = (IP::getHashIP() != false ? IP::getHashIP() : IP::StoreIP());

        # Check Current Use Exist online list
        $user_online = self::is_ip_online($user_ip);

        # Check Users Exist in Online list
        if ($user_online === false) {

            # Added New Online User
            self::add_user_online($args);

        } else {

            # Update current User Time
            self::update_user_online();
        }
    }

    /**
     * Check IP is online
     *
     * @param bool $user_ip
     * @return bool
     */
    public static function is_ip_online($user_ip = false)
    {
        global $wpdb;
        $user_online = $wpdb->query("SELECT * FROM `" . DB::table('useronline') . "` WHERE `ip` = '{$user_ip}'");
        return (!$user_online ? false : $user_online);
    }

    /**
     * Add User Online to Database
     *
     * @param array $args
     * @throws \Exception
     */
    public static function add_user_online($args = array())
    {
        global $wpdb;

        // Get Current Page
        $current_page = Pages::get_page_type();

        // Get User Agent
        $user_agent = UserAgent::getUserAgent();

        //Prepare User online Data
        $user_online = array(
            'ip'        => IP::getHashIP() ? IP::getHashIP() : IP::StoreIP(),
            'timestamp' => TimeZone::getCurrentTimestamp(),
            'created'   => TimeZone::getCurrentTimestamp(),
            'date'      => TimeZone::getCurrentDate(),
            'referred'  => Referred::get(),
            'agent'     => $user_agent['browser'],
            'platform'  => $user_agent['platform'],
            'version'   => $user_agent['version'],
            'location'  => GeoIP::getCountry(IP::getIP()),
            'user_id'   => User::get_user_id(),
            'page_id'   => $current_page['id'],
            'type'      => $current_page['type']
        );
        $user_online = apply_filters('wp_statistics_user_online_information', wp_parse_args($args, $user_online));

        # Insert the user in to the database.
        $insert = $wpdb->insert(
            DB::table('useronline'),
            $user_online
        );
        if (!$insert) {
            if (!empty($wpdb->last_error)) {
                \WP_Statistics::log($wpdb->last_error);
            }
        }

        # Get User Online ID
        $user_online_id = $wpdb->insert_id;

        # Action After Save User Online
        do_action('wp_statistics_save_user_online', $user_online_id, $user_online);
    }

    /**
     * Update User Online
     */
    public static function update_user_online()
    {
        global $wpdb;

        // Get Current Page
        $current_page = Pages::get_page_type();

        // Get Current User ID
        $user_id = User::get_user_id();

        //Prepare User online Update data
        $user_online = array(
            'timestamp' => TimeZone::getCurrentTimestamp(),
            'date'      => TimeZone::getCurrentDate(),
            'referred'  => Referred::get(),
            'user_id'   => $user_id,
            'page_id'   => $current_page['id'],
            'type'      => $current_page['type']
        );
        $user_online = apply_filters('wp_statistics_update_user_online_data', $user_online);

        # Update the database with the new information.
        $wpdb->update(DB::table('useronline'), $user_online, array('ip' => IP::getHashIP() ? IP::getHashIP() : IP::StoreIP()));

        # Action After Update User Online
        do_action('wp_statistics_update_user_online', $user_id, $user_online);
    }

    /**
     * Get User Online List By Custom Query
     *
     * @param array $arg
     * @return array
     * @throws \Exception
     */
    public static function get($arg = array())
    {
        global $wpdb;

        // Define the array of defaults
        $defaults = array(
            'sql'      => '',
            'per_page' => 10,
            'offset'   => 0,
            'fields'   => 'all',
            'order'    => 'DESC',
            'orderby'  => 'ID'
        );
        $args     = wp_parse_args($arg, $defaults);

        // Prepare SQL
        $SQL = "SELECT";

        // Check Fields
        if ($args['fields'] == "count") {
            $SQL .= " COUNT(*)";
        } elseif ($args['fields'] == "all") {
            $SQL .= " *";
        } else {
            $SQL .= $args['fields'];
        }
        $SQL .= " FROM `" . DB::table('useronline') . "`";

        // Check Count
        if ($args['fields'] == "count") {
            return $wpdb->get_var($SQL);
        }

        // Prepare Query
        if (empty($args['sql'])) {
            $args['sql'] = "SELECT * FROM `" . DB::table('useronline') . "` ORDER BY ID DESC";
        }

        // Set Pagination
        $args['sql'] = $args['sql'] . " LIMIT {$args['offset']}, {$args['per_page']}";

        // Send Request
        $result = $wpdb->get_results($args['sql']);

        // Get List
        $list = array();
        foreach ($result as $items) {

            $item = array(
                'referred' => Referred::get_referrer_link($items->referred),
                'agent'    => $items->agent,
                'platform' => $items->platform,
                'version'  => $items->version,
            );

            // Add User information
            if ($items->user_id > 0 and User::exists($items->user_id)) {
                $user_data    = User::get($items->user_id);
                $item['user'] = array(
                    'ID'         => $items->user_id,
                    'user_email' => $user_data['user_email'],
                    'user_login' => $user_data['user_login'],
                    'name'       => User::get_name($items->user_id)
                );
            }

            // Page info
            $item['page'] = Pages::get_page_info($items->page_id, $items->type);

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

            // Online For Time
            $time_diff = ($items->timestamp - $items->created);
            if ($time_diff > 3600) {
                $item['online_for'] = date("H:i:s", ($items->timestamp - $items->created));
            } else if ($time_diff > 60) {
                $item['online_for'] = "00:" . date("i:s", ($items->timestamp - $items->created));
            } else {
                $item['online_for'] = "00:00:" . date("s", ($items->timestamp - $items->created));
            }

            $list[] = $item;
        }

        return $list;
    }


}