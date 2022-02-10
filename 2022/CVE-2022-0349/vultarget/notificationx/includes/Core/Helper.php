<?php

namespace NotificationX\Core;


use NotificationX\Types\TypeFactory;
/**
 * This class will provide all kind of helper methods.
 */
class Helper {
    /**
     * Get all post types
     *
     * @param array $exclude
     * @return array
     */
    public static function post_types($exclude = array()) {
        $post_types = get_post_types(array(
            'public'    => true,
            'show_ui'    => true
        ), 'objects');

        unset($post_types['attachment']);

        if (count($exclude)) {
            foreach ($exclude as $type) {
                if (isset($post_types[$type])) {
                    unset($post_types[$type]);
                }
            }
        }

        return apply_filters('nx_post_types', $post_types);
    }

    /**
     * Get all taxonomies
     *
     * @param string $post_type
     * @param array $exclude
     * @return array
     */
    public static function taxonomies($post_type = '', $exclude = array()) {
        if (empty($post_type)) {
            $taxonomies = get_taxonomies(
                array(
                    'public'       => true,
                    '_builtin'     => false
                ),
                'objects'
            );
        } else {
            $taxonomies = get_object_taxonomies($post_type, 'objects');
        }

        $data = array();
        if (is_array($taxonomies)) {
            foreach ($taxonomies as $tax_slug => $tax) {
                if (!$tax->public || !$tax->show_ui) {
                    continue;
                }
                if (in_array($tax_slug, $exclude)) {
                    continue;
                }
                $data[$tax_slug] = $tax;
            }
        }
        return apply_filters('nx_loop_taxonomies', $data, $taxonomies, $post_type);
    }

    /**
     * This function is responsible for the data sanitization
     *
     * @param array $field
     * @param string|array $value
     * @return string|array
     */
    public static function sanitize_field($field, $value) {
        if (isset($field['sanitize']) && !empty($field['sanitize'])) {
            if (function_exists($field['sanitize'])) {
                $value = call_user_func($field['sanitize'], $value);
            }
            return $value;
        }

        if (is_array($field) && isset($field['type'])) {
            switch ($field['type']) {
                case 'text':
                    $value = sanitize_text_field($value);
                    break;
                case 'textarea':
                    $value = sanitize_textarea_field($value);
                    break;
                case 'email':
                    $value = sanitize_email($value);
                    break;
                default:
                    return $value;
                    break;
            }
        } else {
            $value = sanitize_text_field($value);
        }

        return $value;
    }


    /**
     * Sorting Data
     * by their type
     *
     * @param array $value
     * @param string $key
     * @return void
     */
    public static function sortBy(&$value, $key = 'comments') {
        switch ($key) {
            case 'comments':
                return self::sorter($value, 'key', 'DESC');
                break;
            default:
                return self::sorter($value, 'timestamp', 'DESC');
                break;
        }
    }

    /**
     * This function is responsible for making an array sort by their key
     * @param array $data
     * @param string $using
     * @param string $way
     * @return array
     */
    public static function sorter($data, $using = 'time_date',  $way = 'DESC') {
        if (!is_array($data)) {
            return $data;
        }
        $new_array = [];
        if ($using === 'key') {
            if ($way !== 'ASC') {
                krsort($data);
            } else {
                ksort($data);
            }
        } else {
            foreach ($data as $key => $value) {
                if (!is_array($value)) continue;
                foreach ($value as $inner_key => $single) {
                    if ($inner_key == $using) {
                        $value['tempid'] = $key;
                        $single = self::numeric_key_gen($new_array, $single);
                        $new_array[$single] = $value;
                    }
                }
            }

            if ($way !== 'ASC') {
                krsort($new_array);
            } else {
                ksort($new_array);
            }

            if (!empty($new_array)) {
                foreach ($new_array as $array) {
                    $index = $array['tempid'];
                    unset($array['tempid']);
                    $new_data[$index] = $array;
                }
                $data = $new_data;
            }
        }

        return $data;
    }

    /**
     * This function is responsible for generate unique numeric key for a given array.
     *
     * @param array $data
     * @param integer $index
     * @return integer
     */
    protected static function numeric_key_gen($data, $index = 0) {
        if (isset($data[$index])) {
            $index += 1;
            return self::numeric_key_gen($data, $index);
        }
        return $index;
    }




    /**
     * Contact Forms Key Name filter for Name Selectbox
     * @since 1.4.*
     * @param string
     * @return boolean
     */
    public static function filter_contactform_key_names($name) {
        $validKey = true;
        $filterWords = array(
            "checkbox",
            "color",
            "date",
            "datetime-local",
            "file",
            "image",
            "month",
            "number",
            "password",
            "radio",
            "range",
            "reset",
            "submit",
            "tel",
            "time",
            "week",
            "Comment",
            "message",
            "address",
            "phone",
        );
        foreach ($filterWords as $word) {
            if (!empty($name) && stripos($name, $word) === false) {
                $validKey = true;
            } else {
                $validKey = false;
                break;
            }
        }
        return $validKey;
    }

    /**
     * Contact Forms Key Name remove special characters and meaningless words for Name Selectbox
     * @since 1.4.*
     * @param string
     * @return string
     */
    public static function rename_contactform_key_names($name) {
        $result = preg_split("/[_,\-]+/", $name);
        $returnName = ucfirst($result[0]);
        return $returnName;
    }


    /**
     * Formating Number in a Nice way
     * @since 1.2.1
     * @param int|string $n
     * @return string
     */
    public static function nice_number($n) {
        $temp_number = str_replace(",", "", $n);
        if (!empty($temp_number)) {
            $n = (0 + $temp_number);
        } else {
            $n = $n;
        }
        if (!is_numeric($n)) return 0;
        $is_neg = false;
        if ($n < 0) {
            $is_neg = true;
            $n = abs($n);
        }
        $number = 0;
        $suffix = '';
        switch (true) {
            case $n >= 1000000000000:
                $number = ($n / 1000000000000);
                $suffix = $n > 1000000000000 ? 'T+' : 'T';
                break;
            case $n >= 1000000000:
                $number = ($n / 1000000000);
                $suffix = $n > 1000000000 ? 'B+' : 'B';
                break;
            case $n >= 1000000:
                $number = ($n / 1000000);
                $suffix = $n > 1000000 ? 'M+' : 'M';
                break;
            case $n >= 1000:
                $number = ($n / 1000);
                $suffix = $n > 1000 ? 'K+' : 'K';
                break;
            default:
                $number = $n;
                break;
        }
        if (strpos($number, '.') !== false && strpos($number, '.') >= 0) {
            $number = number_format($number, 1);
        }
        return ($is_neg ? '-' : '') . $number . $suffix;
    }

    public static function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

    public static function get_theme_or_plugin_list($api_data = null) {
        $data = array();
        $new_data = array();

        $needed_key = array('slug', 'title', 'installs_count', 'active_installs_count', 'free_releases_count', 'premium_releases_count', 'total_purchases', 'total_subscriptions', 'total_renewals', 'accepted_payments', 'id', 'created', 'icon');

        if (!empty($api_data->plugins)) {
            foreach ($api_data->plugins as $single_data) {
                $type = $single_data->type;
                foreach ($needed_key as $key) {
                    if ($key == 'created') {
                        if (isset($single_data->$key)) {
                            $new_data['timestamp'] = strtotime($single_data->$key);
                        }
                        continue;
                    }
                    if (isset($single_data->$key)) {
                        $new_data[$key] = $single_data->$key;
                    }
                }
                $data[$type . 's'][$new_data['id']] = $new_data;
                $new_data = array();
            }
        }

        return $data;
    }

    public static function today_to_last_week($data) {
        if (empty($data)) {
            return array();
        }
        $new_data = array();
        $timestamp = current_time('timestamp');
        $date = date('Y-m-d', $timestamp);
        $date_7_days_back = date('Y-m-d', strtotime($date . ' -8 days'));
        $counter_7days = 0;
        $counter_todays = 0;
        foreach ($data as $single_install) {
            date('Y-m-d', strtotime($single_install->created)) > $date_7_days_back ? $counter_7days++ : $counter_7days;
            date('Y-m-d', strtotime($single_install->created)) == $date ? $counter_todays++ : $counter_todays;
        }
        return array(
            'last_week' => $counter_7days,
            'today'     => $counter_todays,
        );
    }

    public static function current_timestamp( $date = null, $timezone = 'UTC' ){
        $timezone = new \DateTimeZone( $timezone );
        $datetime = new \DateTime($date, $timezone);
        return $datetime->getTimestamp();
    }

    public static function current_time($timestamp = null) {
        $type = 'Y-m-d H:i:s';
        if (empty($timestamp)) {
            $timestamp = time();
        }

        $timezone = new \DateTimeZone('UTC');
        if (is_numeric($timestamp)) {
            $datetime = new \DateTime();
            $datetime->setTimezone($timezone);
            $datetime->setTimestamp($timestamp);
        }
        else{
            $datetime = new \DateTime($timestamp);
            $datetime->setTimezone($timezone);
        }
        return $datetime->format($type);
    }

    public static function mysql_time($timestamp = null) {
        $type = 'Y-m-d H:i:s';
        if (empty($timestamp)) {
            $timestamp = time();
        }

        if (is_numeric($timestamp)) {
            $datetime = new \DateTime();
            $datetime->setTimestamp($timestamp);
        }
        else{
            $datetime = new \DateTime($timestamp);
        }
        return $datetime->format($type);
    }

    /**
     * Generating Full Name with one letter from last name
     * @since 1.3.9
     * @param string $first_name
     * @param string $last_name
     * @return string
     */
    public static function name($first_name = '', $last_name = '') {
        $name = $first_name;
        $name .= !empty($last_name) ? ' ' . mb_substr($last_name, 0, 1) : '';
        return $name;
    }

    public static function get_type_title( $type ){
        $_type = TypeFactory::get_instance()->get($type);
        return ! empty( $_type->title ) ?  $_type->title : $type;
    }

    public static function is_plugin_installed( $plugin ){
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugins = get_plugins();
        return isset( $plugins[ $plugin ] );
    }

    public static function is_plugin_active( $plugin ) {
        return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || self::is_plugin_active_for_network( $plugin );
    }

    public static function is_plugin_active_for_network( $plugin ) {
        if ( ! is_multisite() ) {
            return false;
        }

        $plugins = get_site_option( 'active_sitewide_plugins' );
        if ( isset( $plugins[ $plugin ] ) ) {
            return true;
        }

        return false;
    }
    public static function remove_old_notice(){
        global $wp_filter;
        if( isset( $wp_filter['admin_notices']->callbacks[10] ) && is_array( $wp_filter['admin_notices']->callbacks[10] ) ) {
            foreach( $wp_filter['admin_notices']->callbacks[10] as $hash => $callbacks ) {
                if( is_array( $callbacks['function'] ) && ! empty( $callbacks['function'][0] ) && is_object( $callbacks['function'][0] ) && $callbacks['function'][0] instanceof \NotificationX_Licensing ) {
                    remove_action( 'admin_notices', $hash );
                    break;
                }
            }
        }
    }

    public static function remote_get($url, $args = array(), $raw = false) {
        $defaults = array(
            'timeout'     => 20,
            'redirection' => 5,
            'httpversion' => '1.1',
            'user-agent'  => 'NotificationX/' . NOTIFICATIONX_VERSION . '; ' . home_url(),
            'body'        => null,
            'sslverify'   => false,
            'stream'      => false,
            'filename'    => null
        );
        $args = wp_parse_args($args, $defaults);
        $request = wp_remote_get($url, $args);

        if (is_wp_error($request)) {
            return false;
        }
        if($raw){
            return $request;
        }
        $response = json_decode($request['body']);
        if (isset($response->status) && $response->status == 'fail') {
            return false;
        }
        return $response;
    }

    /**
     * Get File Modification Time or URL
     *
     * @param string $file  File relative path for Admin
     * @param boolean $url  true for URL return
     * @return void|string|integer
     */
    public static function file( $file, $url = false ){
        $base = '';
        if(defined('NX_DEBUG') && NX_DEBUG){
            if( $url ) {
                $base = NOTIFICATIONX_DEV_ASSETS;
            }
            else{
                $base = NOTIFICATIONX_DEV_ASSETS_PATH;
            }
        }
        else{
            if( $url ) {
                $base = NOTIFICATIONX_ASSETS;
            }
            else{
                $base = NOTIFICATIONX_ASSETS_PATH;
            }
        }
        return path_join($base, $file);
    }
}
