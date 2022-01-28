<?php

namespace WP_STATISTICS;

class UserAgent
{
    /**
     * Get User Agent
     *
     * @return mixed
     */
    public static function getHttpUserAgent()
    {
        return apply_filters('wp_statistics_user_http_agent', (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''));
    }

    /**
     * Calls the user agent parsing code.
     *
     * @return array|\string[]
     */
    public static function getUserAgent()
    {

        // Get Http User Agent
        $user_agent = self::getHttpUserAgent();

        // Get WhichBrowser Browser
        $result = new \WhichBrowser\Parser($user_agent);
        $agent  = array(
            'browser'  => (isset($result->browser->name)) ? $result->browser->name : _x('Unknown', 'Browser', 'wp-statistics'),
            'platform' => (isset($result->os->name)) ? $result->os->name : _x('Unknown', 'Platform', 'wp-statistics'),
            'version'  => (isset($result->os->version->value)) ? $result->os->version->value : _x('Unknown', 'Version', 'wp-statistics'),
        );

        return apply_filters('wp_statistics_user_agent', $agent);
    }

    /**
     * Get All Browser List For Detecting
     *
     * @param bool $all
     * @area utility
     * @return array|mixed
     */
    public static function BrowserList($all = true)
    {

        //List Of Detect Browser in WP Statistics
        $list        = array(
            "chrome"  => __("Chrome", 'wp-statistics'),
            "firefox" => __("Firefox", 'wp-statistics'),
            "msie"    => __("Internet Explorer", 'wp-statistics'),
            "edge"    => __("Edge", 'wp-statistics'),
            "opera"   => __("Opera", 'wp-statistics'),
            "safari"  => __("Safari", 'wp-statistics')
        );
        $browser_key = array_keys($list);

        //Return All Browser List
        if ($all === true) {
            return $list;
            //Return Browser Keys For detect
        } elseif ($all == "key") {
            return $browser_key;
        } else {
            //Return Custom Browser Name by key
            if (array_search(strtolower($all), $browser_key) !== false) {
                return $list[strtolower($all)];
            } else {
                return __("Unknown", 'wp-statistics');
            }
        }
    }

    /**
     * Get Browser Logo
     *
     * @param $browser
     * @return string
     */
    public static function getBrowserLogo($browser)
    {
        $name = 'unknown';
        if (array_search(strtolower($browser), self::BrowserList('key')) !== false) {
            $name = $browser;
        }

        return WP_STATISTICS_URL . 'assets/images/browser/' . $name . '.png';
    }


}