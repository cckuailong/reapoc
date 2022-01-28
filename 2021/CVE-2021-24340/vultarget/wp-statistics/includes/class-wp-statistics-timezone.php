<?php

namespace WP_STATISTICS;

class TimeZone
{
    /**
     * Get Current timeStamp
     *
     * @return bool|string
     */
    public static function getCurrentTimestamp()
    {
        return apply_filters('wp_statistics_current_timestamp', self::getCurrentDate('U'));
    }

    /**
     * Set WordPress TimeZone offset
     */
    public static function set_timezone()
    {
        if (get_option('timezone_string')) {
            return timezone_offset_get(timezone_open(get_option('timezone_string')), new \DateTime());
        } elseif (get_option('gmt_offset')) {
            return get_option('gmt_offset') * 60 * 60;
        }

        return 0;
    }

    /**
     * Adds the timezone offset to the given time string
     *
     * @param $timestring
     *
     * @return int
     */
    public static function strtotimetz($timestring)
    {
        return strtotime($timestring) + self::set_timezone();
    }

    /**
     * Adds current time to timezone offset
     *
     * @return int
     */
    public static function timetz()
    {
        return time() + self::set_timezone();
    }

    /**
     * Returns a date string in the desired format with a passed in timestamp.
     *
     * @param $format
     * @param $timestamp
     * @return bool|string
     */
    public static function getLocalDate($format, $timestamp)
    {
        return date($format, $timestamp + self::set_timezone());
    }

    /**
     * @param string $format
     * @param null $strtotime
     * @param null $relative
     *
     * @return bool|string
     */
    public static function getCurrentDate($format = 'Y-m-d H:i:s', $strtotime = null, $relative = null)
    {
        if ($strtotime) {
            if ($relative) {
                return date($format, strtotime("{$strtotime} day", $relative) + self::set_timezone());
            } else {
                return date($format, strtotime("{$strtotime} day") + self::set_timezone());
            }
        } else {
            return date($format, time() + self::set_timezone());
        }
    }

    /**
     * Returns a date string in the desired format.
     *
     * @param string $format
     * @param null $strtotime
     * @param null $relative
     *
     * @return bool|string
     */
    public static function getRealCurrentDate($format = 'Y-m-d H:i:s', $strtotime = null, $relative = null)
    {
        if ($strtotime) {
            if ($relative) {
                return date($format, strtotime("{$strtotime} day", $relative));
            } else {
                return date($format, strtotime("{$strtotime} day"));
            }
        } else {
            return date($format, time());
        }
    }

    /**
     * Returns an internationalized date string in the desired format.
     *
     * @param string $format
     * @param null $strtotime
     * @param string $day
     *
     * @return string
     */
    public static function getCurrentDate_i18n($format = 'Y-m-d H:i:s', $strtotime = null, $day = ' day')
    {
        if ($strtotime) {
            return date_i18n($format, strtotime("{$strtotime}{$day}") + self::set_timezone());
        } else {
            return date_i18n($format, time() + self::set_timezone());
        }
    }

    /**
     * Check is Valid date
     *
     * @param $date
     * @return bool
     */
    public static function isValidDate($date)
    {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date) and strtotime($date) != false) {
            return true;
        }
        return false;
    }

    /**
     * Get List Of days from ago Days
     *
     * @param int $ago_days
     * @param string $format
     * @return false|string
     */
    public static function getTimeAgo($ago_days = 1, $format = 'Y-m-d')
    {
        return date($format, strtotime("- " . $ago_days . " day", self::getCurrentTimestamp()));
    }

    /**
     * Get Number Days From Two Days
     *
     * @param $from
     * @param bool $to
     * @return float
     * @example 2019-05-18, 2019-05-22 -> 5 days
     */
    public static function getNumberDayBetween($from, $to = false)
    {
        $to        = ($to === false ? self::getCurrentTimestamp() : strtotime($to));
        $from      = strtotime($from);
        $date_diff = $to - $from;

        return ceil($date_diff / (60 * 60 * 24));
    }

    /**
     * Get List Of Two Days
     *
     * @param array $args
     * @return array
     * @throws \Exception
     */
    public static function getListDays($args = array())
    {

        // Get Default
        $defaults = array(
            'from'   => '',
            'to'     => false,
            'format' => "M j"
        );
        $args     = wp_parse_args($args, $defaults);
        $list     = array();

        // Check Now Date
        $args['to'] = ($args['to'] === false ? self::getCurrentDate() : $args['to']);

        // Get List Of Day
        $period = new \DatePeriod(new \DateTime($args['from']), new \DateInterval('P1D'), new \DateTime(date('Y-m-d', strtotime("+1 day", strtotime($args['to'])))));
        foreach ($period as $key => $value) {
            $list[$value->format('Y-m-d')] = array(
                'timestamp' => $value->format('U'),
                'format'    => $value->format(apply_filters('wp_statistics_request_days_format', $args['format']))
            );
        }

        return $list;
    }

}