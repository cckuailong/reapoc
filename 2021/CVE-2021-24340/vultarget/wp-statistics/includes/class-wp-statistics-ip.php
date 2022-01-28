<?php

namespace WP_STATISTICS;

use IPTools\Range;

class IP
{
    /**
     * Default User IP
     *
     * @var string
     */
    public static $default_ip = '127.0.0.1';

    /**
     * Default Private SubNets
     *
     * @var array
     */
    public static $private_SubNets = array('10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', '127.0.0.1/24', 'fc00::/7');

    /**
     * List Of Common $_SERVER for get Users IP
     *
     * @var array
     */
    public static $ip_methods_server = array('REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_X_REAL_IP', 'HTTP_X_CLUSTER_CLIENT_IP');

    /**
     * Default $_SERVER for Get User Real IP
     *
     * @var string
     */
    public static $default_ip_method = 'REMOTE_ADDR';

    /**
     * Hash IP Prefix
     *
     * @var string
     */
    public static $hash_ip_prefix = '#hash#';

    /**
     * Returns the current IP address of the remote client.
     *
     * @return bool|string
     */
    public static function getIP()
    {

        // Set Default
        $ip = false;

        // Get User IP Methods
        $ip_method = self::getIPMethod();

        // Check isset $_SERVER
        if (isset($_SERVER[$ip_method])) {
            $ip = sanitize_text_field($_SERVER[$ip_method]);
        }

        /**
         * This Filter Used For Custom $_SERVER String
         * @see https://wp-statistics.com/sanitize-user-ip/
         */
        $ip = apply_filters('wp_statistics_sanitize_user_ip', $ip);

        // Sanitize For HTTP_X_FORWARDED
        foreach (explode(',', $ip) as $user_ip) {
            $user_ip = trim($user_ip);
            if (self::isIP($user_ip) != false) {
                $ip = $user_ip;
            }
        }

        // If no valid ip address has been found, use default ip.
        if (false === $ip) {
            $ip = self::$default_ip;
        }

        return apply_filters('wp_statistics_user_ip', $ip);
    }

    /**
     * Generate hash string
     *
     * @param bool $ip
     * @return bool
     */
    public static function getHashIP($ip = false)
    {

        // Check Enabled Options
        if (Option::get('hash_ips') == true) {
            return apply_filters('wp_statistics_hash_ip', self::$hash_ip_prefix . sha1(($ip === false ? self::getIP() : $ip) . (UserAgent::getHttpUserAgent() == '' ? 'Unknown' : UserAgent::getHttpUserAgent())));
        }

        return false;
    }

    /**
     * Check IP is Hashed
     *
     * @param $ip
     * @return bool
     */
    public static function IsHashIP($ip)
    {
        return (substr($ip, 0, strlen(self::$hash_ip_prefix)) == self::$hash_ip_prefix);
    }

    /**
     * Store User IP To Database
     */
    public static function StoreIP()
    {

        //Get User ip
        $user_ip = self::getIP();

        // use 127.0.0.1 If no valid ip address has been found.
        if (false === $user_ip) {
            return self::$default_ip;
        }

        // If the anonymize IP enabled for GDPR.
        if (Option::get('anonymize_ips') == true) {
            $user_ip = substr($user_ip, 0, strrpos($user_ip, '.')) . '.0';
        }

        return $user_ip;
    }

    /**
     * Check IP Has The Custom IP Range List
     *
     * @param $ip
     * @param array $range
     * @return bool
     * @throws \Exception
     */
    public static function CheckIPRange($range = array(), $ip = false)
    {

        // Get User IP
        $ip = ($ip === false ? IP::getIP() : $ip);

        // Get Range OF This IP
        try {
            $ip = new \IPTools\IP($ip);
        } catch (\Exception $e) {
            \WP_Statistics::log($e->getMessage());
            $ip = new \IPTools\IP(self::$default_ip);
        }

        // Check List
        foreach ($range as $list) {
            try {
                $contains_ip = Range::parse($list)->contains($ip);
            } catch (\Exception $e) {
                \WP_Statistics::log($e->getMessage());
                $contains_ip = false;
            }

            if ($contains_ip) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check Validation IP
     *
     * @param $ip
     * @return bool
     */
    public static function isIP($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * what is Method $_SERVER for get User Real IP
     */
    public static function getIPMethod()
    {
        $ip_method = Option::get('ip_method');
        return ($ip_method != false ? $ip_method : self::$default_ip_method);
    }

    /**
     * Check IP contain Special Character
     *
     * @param $ip
     * @return bool
     */
    public static function check_sanitize_ip($ip)
    {
        $preg = preg_replace('/[^0-9- .:]/', '', $ip);
        return $preg == $ip;
    }

    /**
     * Update All Hash String For Hash IP
     */
    public static function Update_HashIP_Visitor()
    {
        global $wpdb;

        // Get the rows from the Visitors table.
        $result = $wpdb->get_results("SELECT DISTINCT ip FROM " . DB::table('visitor'));
        foreach ($result as $row) {
            if (IP::IsHashIP($row->ip)) {
                $wpdb->update(
                    DB::table('visitor'),
                    array('ip' => IP::$hash_ip_prefix . sha1($row->ip . Helper::random_string()),),
                    array('ip' => $row->ip)
                );
            }
        }
    }

}