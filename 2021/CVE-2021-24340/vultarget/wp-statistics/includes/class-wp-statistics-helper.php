<?php

namespace WP_STATISTICS;

use Exception;
use WP_STATISTICS;
use WP_Statistics_Mail;

class Helper
{
    /**
     * WP-Statistics WordPress Log
     *
     * @param $function
     * @param $message
     * @param $version
     */
    public static function doing_it_wrong($function, $message, $version = '')
    {
        if (empty($version)) {
            $version = WP_STATISTICS_VERSION;
        }
        $message .= ' Backtrace: ' . wp_debug_backtrace_summary();
        if (is_ajax()) {
            do_action('doing_it_wrong_run', $function, $message, $version);
            error_log("{$function} was called incorrectly. {$message}. This message was added in version {$version}.");
        } else {
            _doing_it_wrong($function, $message, $version);
        }
    }

    /**
     * Returns an array of site id's
     *
     * @return array
     */
    public static function get_wp_sites_list()
    {
        $site_list = array();
        $sites     = get_sites();
        foreach ($sites as $site) {
            $site_list[] = $site->blog_id;
        }
        return $site_list;
    }

    /**
     * What type of request is this?
     *
     * @param string $type admin, ajax, cron or frontend.
     * @return bool
     */
    public static function is_request($type)
    {
        switch ($type) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined('DOING_AJAX');
            case 'cron':
                return defined('DOING_CRON');
            case 'wp-cli':
                return defined('WP_CLI') && WP_CLI;
            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON') && !self::is_rest_request();
        }
    }

    /**
     * Returns true if the request is a non-legacy REST API request.
     *
     * @return bool
     */
    public static function is_rest_request()
    {
        if (empty($_SERVER['REQUEST_URI'])) {
            return false;
        }
        $rest_prefix = trailingslashit(rest_get_url_prefix());
        return (false !== strpos($_SERVER['REQUEST_URI'], $rest_prefix));
    }

    /**
     * Check is Login Page
     *
     * @return bool
     */
    public static function is_login_page()
    {

        // Check From global WordPress
        if (isset($GLOBALS['pagenow']) and in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
            return true;
        }

        // Check Native php
        $protocol   = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? 'http' : 'https';
        $host       = $_SERVER['HTTP_HOST'];
        $script     = $_SERVER['SCRIPT_NAME'];
        $currentURL = $protocol . '://' . $host . $script;
        $loginURL   = wp_login_url();
        if ($currentURL == $loginURL) {
            return true;
        }

        return false;
    }

    /**
     * Show Admin Wordpress Ui Notice
     *
     * @param $text
     * @param string $model
     * @param bool $close_button
     * @param bool $id
     * @param bool $echo
     * @param string $style_extra
     * @return string
     */
    public static function wp_admin_notice($text, $model = "info", $close_button = true, $id = false, $echo = true, $style_extra = 'padding:12px;')
    {
        $text = '
        <div class="notice notice-' . $model . '' . ($close_button === true ? " is-dismissible" : "") . '"' . ($id != false ? ' id="' . $id . '"' : '') . '>
           <div style="' . $style_extra . '">' . $text . '</div>
        </div>
        ';
        if ($echo) {
            echo $text;
        } else {
            return $text;
        }
    }

    /**
     * Get Screen ID
     *
     * @return string
     */
    public static function get_screen_id()
    {
        $screen    = get_current_screen();
        $screen_id = $screen ? $screen->id : '';
        return $screen_id;
    }

    /**
     * Get File Path Of Plugins File
     *
     * @param $path
     * @return string
     */
    public static function get_file_path($path)
    {
        return wp_normalize_path(path_join(WP_STATISTICS_DIR, $path));
    }

    /**
     * Check User is Used Cache Plugin
     *
     * @return array
     */
    public static function is_active_cache_plugin()
    {
        $use = array('status' => false, 'plugin' => '');

        /* Wordpress core */
        if (defined('WP_CACHE') && WP_CACHE) {
            return array('status' => true, 'plugin' => 'core');
        }

        /* WP Rocket */
        if (function_exists('get_rocket_cdn_url')) {
            return array('status' => true, 'plugin' => 'WP Rocket');
        }

        /* WP Super Cache */
        if (function_exists('wpsc_init')) {
            return array('status' => true, 'plugin' => 'WP Super Cache');
        }

        /* Comet Cache */
        if (function_exists('___wp_php_rv_initialize')) {
            return array('status' => true, 'plugin' => 'Comet Cache');
        }

        /* WP Fastest Cache */
        if (class_exists('WpFastestCache')) {
            return array('status' => true, 'plugin' => 'WP Fastest Cache');
        }

        /* Cache Enabler */
        if (defined('CE_MIN_WP')) {
            return array('status' => true, 'plugin' => 'Cache Enabler');
        }

        /* W3 Total Cache */
        if (defined('W3TC')) {
            return array('status' => true, 'plugin' => 'W3 Total Cache');
        }

        return $use;
    }

    /**
     * Get WordPress Uploads DIR
     *
     * @param string $path
     * @return mixed
     * @default For WP-Statistics Plugin is 'wp-statistics' dir
     */
    public static function get_uploads_dir($path = '')
    {
        $upload_dir = wp_upload_dir();
        return path_join($upload_dir['basedir'], $path);
    }

    /**
     * Get Robots List
     *
     * @param string $type
     * @return array|bool|string
     */
    public static function get_robots_list($type = 'list')
    {
        global $WP_Statistics;

        # Set Default
        $list = array();

        # Load From global
        if (isset($WP_Statistics->robots_list)) {
            $list = $WP_Statistics->robots_list;
        }

        # Load From file
        include WP_STATISTICS_DIR . "includes/defines/robots-list.php";
        if (isset($wps_robots_list_array)) {
            $list = $wps_robots_list_array;
        }

        return ($type == "array" ? $list : implode("\n", $list));
    }

    /**
     * Get Number Days From install this plugin
     * this method used for `ALL` Option in Time Range Pages
     */
    public static function get_date_install_plugin()
    {
        global $wpdb;

        //Create Empty default Option
        $first_day = '';

        //First Check Visitor Table , if not exist Web check Pages Table
        $list_tbl = array(
            'visitor' => array('order_by' => 'ID', 'column' => 'last_counter'),
            'pages'   => array('order_by' => 'page_id', 'column' => 'date'),
        );
        foreach ($list_tbl as $tbl => $val) {
            $first_day = $wpdb->get_var("SELECT `" . $val['column'] . "` FROM `" . WP_STATISTICS\DB::table($tbl) . "` ORDER BY `" . $val['order_by'] . "` ASC LIMIT 1");
            if (!empty($first_day)) {
                break;
            }
        }

        //Calculate hit day if range is exist
        if (empty($first_day)) {
            return false;
        } else {
            return $first_day;
        }
    }

    /**
     * Check User Is Using Gutenberg Editor
     */
    public static function is_gutenberg()
    {
        $current_screen = get_current_screen();
        return ((method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) || (function_exists('is_gutenberg_page')) && is_gutenberg_page());
    }

    /**
     * Get List WordPress Post Type
     *
     * @return array
     */
    public static function get_list_post_type()
    {
        $post_types     = array('post', 'page');
        $get_post_types = get_post_types(array('public' => true, '_builtin' => false), 'names', 'and');
        foreach ($get_post_types as $name) {
            $post_types[] = $name;
        }

        return $post_types;
    }

    /**
     * Check Url Scheme
     *
     * @param $url
     * @param array $accept
     * @return bool
     */
    public static function check_url_scheme($url, $accept = array('http', 'https'))
    {
        $scheme = @parse_url($url, PHP_URL_SCHEME);
        return in_array($scheme, $accept);
    }

    /**
     * Get WordPress Version
     *
     * @return mixed|string
     */
    public static function get_wordpress_version()
    {
        return get_bloginfo('version');
    }

    /**
     * Convert Json To Array
     *
     * @param $json
     * @return bool|mixed
     */
    public static function json_to_array($json)
    {

        // Sanitize Slash Data
        $data = wp_unslash($json);

        // Check Validate Json Data
        if (!empty($data) && is_string($data) && is_array(json_decode($data, true)) && json_last_error() == 0) {
            return json_decode($data, true);
        }

        return false;
    }

    /**
     * Standard Json Encode
     *
     * @param $array
     * @return false|string
     */
    public static function standard_json_encode($array)
    {

        //Fixed entity decode Html
        foreach ((array)$array as $key => $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $array[$key] = html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8');
        }

        return json_encode($array, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Show Site Icon by Url
     *
     * @param $url
     * @param int $size
     * @param string $style
     * @return bool|string
     */
    public static function show_site_icon($url, $size = 16, $style = '')
    {
        $url = preg_replace('/^https?:\/\//', '', $url);
        if ($url != "") {
            $img_url = "https://www.google.com/s2/favicons?domain=" . $url;
            return '<img src="' . $img_url . '" width="' . $size . '" height="' . $size . '" style="' . ($style == "" ? 'vertical-align: -3px;' : '') . '" />';
        }

        return false;
    }

    /**
     * Get Domain name from url
     * e.g : https://wp-statistics.com/add-ons/ -> wp-statistics.com
     *
     * @param $url
     * @return mixed
     */
    public static function get_domain_name($url)
    {
        //Remove protocol
        $url = preg_replace("(^https?://)", "", trim($url));
        //remove w(3)
        $url = preg_replace('#^(http(s)?://)?w{3}\.#', '$1', $url);
        //remove all Query
        $url = explode("/", $url);

        return $url[0];
    }

    /**
     * Get Site title By Url
     *
     * @param $url string e.g : wp-statistics.com
     * @return bool|string
     */
    public static function get_site_title_by_url($url)
    {

        //Get Body Page
        $html = Helper::get_html_page($url);
        if ($html === false) {
            return false;
        }

        //Get Page Title
        if (class_exists('DOMDocument')) {
            $dom = new \DOMDocument;
            @$dom->loadHTML($html);
            $title = '';
            if (isset($dom) and $dom->getElementsByTagName('title')->length > 0) {
                $title = $dom->getElementsByTagName('title')->item('0')->nodeValue;
            }
            return (wp_strip_all_tags($title) == "" ? false : wp_strip_all_tags($title));
        }

        return false;
    }

    /**
     * Get Html Body Page By Url
     *
     * @param $url string e.g : wp-statistics.com
     * @return bool
     */
    public static function get_html_page($url)
    {

        //sanitize Url
        $parse_url = wp_parse_url($url);
        $urls[]    = esc_url_raw($url);

        //Check Protocol Url
        if (!array_key_exists('scheme', $parse_url)) {
            $urls      = array();
            $url_parse = wp_parse_url($url);
            foreach (array('http://', 'https://') as $scheme) {
                $urls[] = preg_replace('/([^:])(\/{2,})/', '$1/', $scheme . path_join((isset($url_parse['host']) ? $url_parse['host'] : ''), (isset($url_parse['path']) ? $url_parse['path'] : '')));
            }
        }

        //Send Request for Get Page Html
        foreach ($urls as $page) {
            $response = wp_remote_get($page, array(
                'timeout'    => 30,
                'user-agent' => "Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.71 Safari/537.36"
            ));
            if (is_wp_error($response)) {
                continue;
            }
            $data = wp_remote_retrieve_body($response);
            if (is_wp_error($data)) {
                continue;
            }
            return (wp_strip_all_tags($data) == "" ? false : $data);
        }

        return false;
    }

    /**
     * Generate Random String
     *
     * @param $num
     * @return string
     */
    public static function random_string($num = 50)
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $num; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

    /**
     * Get Post List From custom Post Type
     *
     * @param array $args
     * @area utility
     * @return mixed
     */
    public static function get_post_list($args = array())
    {

        //Prepare Arg
        $defaults = array(
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => '-1',
            'order'          => 'ASC',
            'fields'         => 'ids'
        );
        $args     = wp_parse_args($args, $defaults);

        //Get Post List
        $query = new \WP_Query($args);
        $list  = array();
        foreach ($query->posts as $ID) {
            $list[$ID] = esc_html(get_the_title($ID));
        }

        return $list;
    }

    /**
     * Check WordPress Post is Published
     *
     * @param $ID
     * @return bool
     */
    public static function IsPostPublished($ID)
    {
        return get_post_status($ID) == 'public';
    }

    /**
     * Generate RGBA colors
     *
     * @param        $num
     * @param string $opacity
     * @param bool $quote
     * @return string
     */
    public static function GenerateRgbaColor($num, $opacity = '1', $quote = true)
    {
        $hash   = md5('color' . $num);
        $rgba   = "rgba(%s, %s, %s, %s)";
        $format = ($quote === true ? "'$rgba'" : $rgba);

        return sprintf($format,
            hexdec(substr($hash, 0, 2)),
            hexdec(substr($hash, 2, 2)),
            hexdec(substr($hash, 4, 2)),
            $opacity
        );
    }

    /**
     * Remove Query String From Url
     *
     * @param $url
     * @return bool|string
     */
    public static function RemoveQueryStringUrl($url)
    {
        return substr($url, 0, strrpos($url, "?"));
    }

    /**
     * Sort associative array
     *
     * @param $array
     * @param $subfield
     * @param int $type
     * @return void
     * @see https://stackoverflow.com/questions/1597736/how-to-sort-an-array-of-associative-arrays-by-value-of-a-given-key-in-php
     */
    public static function SortByKeyValue(&$array, $subfield, $type = SORT_DESC)
    {
        $sort_array = array();
        foreach ($array as $key => $row) {
            $sort_array[$key] = $row[$subfield];
        }
        array_multisort($sort_array, $type, $array);
    }

    /**
     * Format array for the datepicker
     *
     * @param $array_to_strip
     * @return array
     */
    public static function strip_array_indices($array_to_strip)
    {
        $NewArray = array();
        foreach ($array_to_strip as $objArrayItem) {
            $NewArray[] = $objArrayItem;
        }

        return ($NewArray);
    }

    /**
     * Set All Option For DatePicker
     *
     * @example add_filter( 'wp_statistics_days_ago_request', array( '', 'set_all_option_datepicker' ) );
     */
    public static function set_all_option_datepicker()
    {
        $first_day = Helper::get_date_install_plugin();
        return ($first_day === false ? 30 : (int)TimeZone::getNumberDayBetween($first_day));
    }

    /**
     * Url Decode
     *
     * @param $value
     * @return string
     */
    public static function getUrlDecode($value)
    {
        return utf8_decode(urldecode($value));
    }

    /**
     * Check is Assoc Array
     *
     * @param array $arr
     * @return bool
     */
    public static function isAssoc(array $arr)
    {
        if (array() === $arr) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Create Condition SQL
     *
     * @param array $args
     * @return string
     */
    public static function getConditionSQL($args = array())
    {

        // Create Empty SQL
        $sql = '';

        // Check Number Params
        if (self::isAssoc($args)) {
            $condition[] = $args;
        } else {
            $condition = $args;
        }

        // Add WHERE
        if (count($condition) > 0) {
            $sql .= ' WHERE ';
        }

        // Push To SQL
        $i = 0;
        foreach ($condition as $params) {
            if ($i > 0) {
                $sql .= ' AND ';
            }
            if ($params['compare'] == "BETWEEN") {
                $sql .= $params['key'] . " " . $params['compare'] . " " . (is_numeric($params['from']) ? $params['from'] : "'" . $params['from'] . "'") . " AND " . (is_numeric($params['to']) ? $params['to'] : "'" . $params['to'] . "'");
            } else {
                $sql .= $params['key'] . " " . $params['compare'] . " " . (is_numeric($params['value']) ? $params['value'] : "'" . $params['value'] . "'");
            }
            $i++;
        }

        return $sql;
    }

    /**
     * Send Email
     *
     * @param $to
     * @param $subject
     * @param $content
     * @param bool $email_template
     * @param array $args
     * @return bool
     */
    public static function send_mail($to, $subject, $content, $email_template = true, $args = array())
    {
        // Email Template
        if ($email_template) {
            $email_template = wp_normalize_path(WP_STATISTICS_DIR . 'includes/admin/templates/email.php');
        }

        // Email from
        $from_name  = get_bloginfo('name');
        $from_email = get_bloginfo('admin_email');
        $from       = sprintf('%s <%s>', $from_name, $from_email);

        //Template Arg
        $template_arg = array(
            'title'       => $subject,
            'logo'        => '',
            'content'     => $content,
            'site_url'    => home_url(),
            'site_title'  => get_bloginfo('name'),
            'footer_text' => '',
            'is_rtl'      => (is_rtl() ? true : false)
        );
        $arg          = wp_parse_args($args, $template_arg);

        /**
         * Send Email
         */
        try {

            WP_Statistics_Mail::init()
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($subject)
                ->setBody($content)
                ->setTemplate($email_template, $arg)
                ->send();

            return true;

        } catch (Exception $e) {
            \WP_Statistics::log($e->getMessage());

            error_log($e->getMessage());

            return false;
        }
    }

    /**
     * Send SMS With WP-SMS Plugin
     *
     * @param $to
     * @param $text
     * @return bool
     */
    public static function send_sms($to, $text)
    {
        if (function_exists('wp_sms_send')) {
            $run = wp_sms_send($to, $text);
            return (is_wp_error($run) ? false : true);
        }

        return false;
    }

    /**
     * Get List Taxonomy
     *
     * @param bool $hide_empty
     * @return array
     */
    public static function get_list_taxonomy($hide_empty = false)
    {
        $taxonomies = array('category' => __("Category", "wp-statistics"), "post_tag" => __("Tags", "wp-statistics"));
        $get_tax    = get_taxonomies(array('public' => true, '_builtin' => false), 'objects', 'and');
        foreach ($get_tax as $object) {
            $object = get_object_vars($object);
            if ($hide_empty === true) {
                $count_term_in_tax = wp_count_terms($object['name'], array('hide_empty' => false, 'parent' => 0));
                if ($count_term_in_tax > 0 and isset($object['rewrite']['slug'])) {
                    $taxonomies[$object['name']] = $object['labels']->name;
                }
            } else {
                if (isset($object['rewrite']['slug'])) {
                    $taxonomies[$object['name']] = $object['labels']->name;
                }
            }
        }

        return $taxonomies;
    }

    /**
     * Create Condition Where Time in MySql
     *
     * @param string $field : date column name in database table
     * @param string $time : Time return
     * @param array $range : an array contain two Date e.g : array('start' => 'xx-xx-xx', 'end' => 'xx-xx-xx', 'is_day' => true, 'current_date' => true)
     *
     * ---- Time Range -----
     * today
     * yesterday
     * week
     * month
     * year
     * total
     * “-x” (i.e., “-10” for the past 10 days)
     * ----------------------
     *
     * @return string|bool
     */
    public static function mysql_time_conditions($field = 'date', $time = 'total', $range = array())
    {
        global $WP_Statistics;

        //Get Current Date From WP
        $current_date = TimeZone::getCurrentDate('Y-m-d');

        //Create Field Sql
        $field_sql = function ($time) use ($current_date, $field, $WP_Statistics, $range) {
            $is_current     = array_key_exists('current_date', $range);
            $getCurrentDate = TimeZone::getCurrentDate('Y-m-d', (int)$time);
            return "`$field` " . ($is_current === true ? '=' : 'BETWEEN') . " '{$getCurrentDate}'" . ($is_current === false ? " AND '{$current_date}'" : "");
        };

        //Check Time
        switch ($time) {
            case 'today':
                $where = "`$field` = '{$current_date}'";
                break;
            case 'yesterday':
                $getCurrentDate = TimeZone::getCurrentDate('Y-m-d', -1);
                $where          = "`$field` = '{$getCurrentDate}'";
                break;
            case 'week':
                $where = $field_sql(-7);
                break;
            case 'month':
                $where = $field_sql(-30);
                break;
            case 'year':
                $where = $field_sql(-365);
                break;
            case 'total':
                $where = "";
                break;
            default:
                if (array_key_exists('is_day', $range)) {
                    //Check a day
                    if (TimeZone::isValidDate($time)) {
                        $where = "`$field` = '{$time}'";
                    } else {
                        $getCurrentDate = TimeZone::getCurrentDate('Y-m-d', $time);
                        $where          = "`$field` = '{$getCurrentDate}'";
                    }
                } elseif (array_key_exists('start', $range) and array_key_exists('end', $range)) {
                    //Check Between Two Time
                    $getCurrentDate    = TimeZone::getCurrentDate('Y-m-d', '-0', strtotime($range['start']));
                    $getCurrentEndDate = TimeZone::getCurrentDate('Y-m-d', '-0', strtotime($range['end']));
                    $where             = "`$field` BETWEEN '{$getCurrentDate}' AND '{$getCurrentEndDate}'";
                } else {
                    //Check From a Date To Now
                    $where = $field_sql($time);
                }
        }

        return $where;
    }

    /**
     * Easy U-sort Array
     *
     * @param $a
     * @param $b
     * @return bool
     */
    public static function compare_uri_hits($a, $b)
    {
        return $a[1] < $b[1];
    }

    /**
     * Return Number Posts in WordPress
     *
     * @return int
     */
    public static function getCountPosts()
    {
        $count_posts = wp_count_posts('post');

        $ret = 0;
        if (is_object($count_posts)) {
            $ret = $count_posts->publish;
        }
        return $ret;
    }

    /**
     * Get Count Pages WordPress
     *
     * @return int
     */
    public static function getCountPages()
    {
        $count_pages = wp_count_posts('page');

        $ret = 0;
        if (is_object($count_pages)) {
            $ret = $count_pages->publish;
        }
        return $ret;
    }

    /**
     * Get All WordPress Count
     *
     * @return mixed
     */
    public static function getCountComment()
    {
        global $wpdb;

        $countcomms = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = '1'");
        return $countcomms;
    }

    /**
     * Get Count Comment Spam
     *
     * @return mixed
     */
    public static function getCountSpam()
    {
        return number_format_i18n(get_option('akismet_spam_count'));
    }

    /**
     * Get Count All WordPress Users
     *
     * @return mixed
     */
    public static function getCountUsers()
    {
        $result = count_users();
        return $result['total_users'];
    }

    /**
     * Return the last date a post was published on your site.
     *
     * @return string
     */
    public static function getLastPostDate()
    {
        global $wpdb;

        $db_date     = $wpdb->get_var("SELECT post_date FROM {$wpdb->posts} WHERE post_type='post' AND post_status='publish' ORDER BY post_date DESC LIMIT 1");
        $date_format = get_option('date_format');
        return TimeZone::getCurrentDate_i18n($date_format, $db_date, false);
    }

    /**
     * Returns the average number of days between posts on your site.
     *
     * @param bool $days
     * @return float
     */
    public static function getAveragePost($days = false)
    {
        global $wpdb;

        $get_first_post = $wpdb->get_var("SELECT post_date FROM {$wpdb->posts} WHERE post_status = 'publish' ORDER BY post_date LIMIT 1");
        $get_total_post = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = 'post'");

        $days_spend = intval(
            (time() - strtotime($get_first_post)) / 86400
        ); // 86400 = 60 * 60 * 24 = number of seconds in a day

        if ($days == true) {
            if ($get_total_post == 0) {
                $get_total_post = 1;
            } // Avoid divide by zero errors.

            return round($days_spend / $get_total_post, 0);
        } else {
            if ($days_spend == 0) {
                $days_spend = 1;
            } // Avoid divide by zero errors.

            return round($get_total_post / $days_spend, 2);
        }
    }

    /**
     * Returns the average number of days between comments on your site.
     *
     * @param bool $days
     * @return float
     */
    public static function getAverageComment($days = false)
    {

        global $wpdb;

        $get_first_comment = $wpdb->get_var("SELECT comment_date FROM {$wpdb->comments} ORDER BY comment_date LIMIT 1");
        $get_total_comment = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = '1'");

        $days_spend = intval(
            (time() - strtotime($get_first_comment)) / 86400
        ); // 86400 = 60 * 60 * 24 = number of seconds in a day

        if ($days == true) {
            if ($get_total_comment == 0) {
                $get_total_comment = 1;
            } // Avoid divide by zero errors.

            return round($days_spend / $get_total_comment, 0);
        } else {
            if ($days_spend == 0) {
                $days_spend = 1;
            } // Avoid divide by zero errors.

            return round($get_total_comment / $days_spend, 2);
        }
    }

    /**
     * Returns the average number of days between user registrations on your site.
     *
     * @param bool $days
     * @return float
     */
    public static function getAverageRegisterUser($days = false)
    {

        global $wpdb;

        $get_first_user = $wpdb->get_var("SELECT `user_registered` FROM {$wpdb->users} ORDER BY user_registered LIMIT 1");
        $get_total_user = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->users}");

        $days_spend = intval(
            (time() - strtotime($get_first_user)) / 86400
        );

        if ($days == true) {
            if ($get_total_user == 0) {
                $get_total_user = 1;
            }

            return round($days_spend / $get_total_user, 0);
        } else {
            if ($days_spend == 0) {
                $days_spend = 1;
            }

            return round($get_total_user / $days_spend, 2);
        }
    }
}