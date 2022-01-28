<?php

namespace ProfilePress\Core\Classes;

use WP_Session;

/**
 * This is a wrapper class for WP_Session / PHP $_SESSION
 *
 * @copyright   Copyright (c) 2015, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class PPRESS_Session
{
    /**
     * Holds our session data
     *
     * @var array
     * @access private
     *
     */
    private $session;

    /**
     * Whether to use PHP $_SESSION or WP_Session
     *
     * @var bool
     * @access private
     *
     */
    private $use_php_sessions = false;

    /**
     * Session index prefix
     *
     * @var string
     * @access private
     * @since 2.3
     */
    private $prefix = '';

    /**
     * Get things started
     *
     * Defines our WP_Session constants, includes the necessary libraries and
     * retrieves the WP Session instance
     */
    public function __construct()
    {
        $this->use_php_sessions = $this->use_php_sessions();

        if ($this->use_php_sessions) {

            if (is_multisite()) {
                $this->prefix = '_' . get_current_blog_id();
            }

            // Use PHP SESSION (must be enabled via the PPWP_USE_PHP_SESSIONS constant)
            add_action('init', array($this, 'maybe_start_session'), -2);

        } else {

            if ( ! $this->should_start_session()) {
                return;
            }

            // Use WP_Session (default)

            if ( ! defined('WP_SESSION_COOKIE')) {
                define('WP_SESSION_COOKIE', 'ppwp_wp_session');
            }

            if ( ! class_exists('Recursive_ArrayAccess')) {
                require_once PROFILEPRESS_SRC . 'lib/wp_session/class-recursive-arrayaccess.php';
            }

            if ( ! class_exists('WP_Session')) {
                require_once PROFILEPRESS_SRC . 'lib/wp_session/class-wp-session.php';
                require_once PROFILEPRESS_SRC . 'lib/wp_session/wp-session.php';
            }
        }

        if (empty($this->session) && ! $this->use_php_sessions) {
            add_action('plugins_loaded', array($this, 'init'), -1);
        } else {
            add_action('init', array($this, 'init'), -1);
        }
    }

    /**
     * Setup the WP_Session instance
     *
     *
     * @return void
     */
    public function init()
    {
        if ($this->use_php_sessions) {
            $this->session = isset($_SESSION['ppwp' . $this->prefix]) && is_array($_SESSION['ppwp' . $this->prefix]) ? $_SESSION['ppwp' . $this->prefix] : array();
        } else {
            $this->session = WP_Session::get_instance();
        }

        return $this->session;
    }

    /**
     * Retrieve a session variable
     *
     * @param string $key Session key
     *
     * @return mixed Session variable
     */
    public function get($key)
    {
        $key    = sanitize_key($key);
        $return = false;

        if (isset($this->session[$key]) && ! empty($this->session[$key])) {

            preg_match('/[oO]\s*:\s*\d+\s*:\s*"\s*(?!(?i)(stdClass))/', $this->session[$key], $matches);
            if ( ! empty($matches)) {
                $this->set($key, null);

                return false;
            }

            if (is_numeric($this->session[$key])) {
                $return = $this->session[$key];
            } else {

                $maybe_json = json_decode($this->session[$key]);

                // Since json_last_error is PHP 5.3+, we have to rely on a `null` value for failing to parse JSON.
                if (is_null($maybe_json)) {
                    $is_serialized = is_serialized($this->session[$key]);
                    if ($is_serialized) {
                        $value = @unserialize($this->session[$key]);
                        $this->set($key, (array)$value);
                        $return = $value;
                    } else {
                        $return = $this->session[$key];
                    }
                } else {
                    $return = json_decode($this->session[$key], true);
                }
            }
        }

        return $return;
    }

    /**
     * Set a session variable
     *
     * @param string $key Session key
     * @param int|string|array $value Session variable
     *
     * @return mixed Session variable
     */
    public function set($key, $value)
    {
        $key = sanitize_key($key);

        if (is_array($value)) {
            $this->session[$key] = wp_json_encode($value);
        } else {
            $this->session[$key] = esc_attr($value);
        }

        if ($this->use_php_sessions) {

            $_SESSION['ppwp' . $this->prefix] = $this->session;
        }

        return $this->session[$key];
    }

    /**
     * Starts a new session if one hasn't started yet.
     *
     * @return boolean
     *
     * Checks to see if the server supports PHP sessions
     *
     * @return boolean $ret True if we are using PHP sessions, false otherwise
     * @author Daniel J Griffiths
     * @since 2.1
     */
    public function use_php_sessions()
    {
        $ret = false;

        // Attempt to detect if the server supports PHP sessions
        if (function_exists('session_start')) {

            $this->set('ppwp_use_php_sessions', 1);

            if ($this->get('ppwp_use_php_sessions')) {
                $ret = true;
            }
        }

        return $ret;
    }

    /**
     * Determines if we should start sessions
     *
     * @return bool
     * @since  2.5.11
     */
    public function should_start_session()
    {
        $start_session = true;

        if ( ! empty($_SERVER['REQUEST_URI'])) {

            $blacklist = $this->get_blacklist();
            $uri       = ltrim($_SERVER['REQUEST_URI'], '/');
            $uri       = untrailingslashit($uri);

            if (in_array($uri, $blacklist)) {
                $start_session = false;
            }

            if (false !== strpos($uri, 'feed=')) {
                $start_session = false;
            }

            if (is_admin() && false === strpos($uri, 'wp-admin/admin-ajax.php')) {
                // We do not want to start sessions in the admin unless we're processing an ajax request
                $start_session = false;
            }

            if (false !== strpos($uri, 'wp_scrape_key')) {
                // Starting sessions while saving the file editor can break the save process, so don't start
                $start_session = false;
            }
        }

        return $start_session;
    }

    /**
     * Retrieve the URI blacklist
     *
     * These are the URIs where we never start sessions
     *
     * @return array
     * @since  2.5.11
     */
    public function get_blacklist()
    {
        $blacklist = apply_filters('ppwp_session_start_uri_blacklist', array(
            'feed',
            'feed/rss',
            'feed/rss2',
            'feed/rdf',
            'feed/atom',
            'comments/feed'
        ));

        // Look to see if WordPress is in a sub folder or this is a network site that uses sub folders
        $folder = str_replace(network_home_url(), '', get_site_url());

        if ( ! empty($folder)) {
            foreach ($blacklist as $path) {
                $blacklist[] = $folder . '/' . $path;
            }
        }

        return $blacklist;
    }

    /**
     * Starts a new session if one hasn't started yet.
     */
    public function maybe_start_session()
    {
        if ( ! $this->should_start_session()) {
            return;
        }

        if ( ! session_id() && ! headers_sent()) {
            session_start();
        }
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
