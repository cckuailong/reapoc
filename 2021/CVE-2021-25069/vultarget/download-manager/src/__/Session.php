<?php
/**
 * User: shahnuralam
 * Date: 01/11/18
 * Time: 7:08 PM
 * From v4.7.9
 * Last Updated: 10/11/2018
 */

namespace WPDM\__;


class Session
{
    static $data;
    static $deviceID = null;
    static $store;

    function __construct()
    {
        if(isset($_COOKIE['__wpdm_client']))
            $deviceID = __::sanitize_var($_COOKIE['__wpdm_client'], 'alphanum');
        else {
            $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $deviceID = md5(__::get_client_ip() . $agent);
            $deviceID = __::sanitize_var($deviceID, 'alphanum');
            if(!defined('WPDM_ACCEPT_COOKIE') || WPDM_ACCEPT_COOKIE !== false) {
                @setcookie('__wpdm_client', $deviceID, 0, "", COOKIE_DOMAIN, is_ssl(), true);
                $_COOKIE['__wpdm_client'] = $deviceID;
            }
        }
        self::$deviceID = $deviceID;

        self::$store = get_option('__wpdm_tmp_storage', 'db');

        if (self::$store === 'file') {
			$session_file = realpath(WPDM_CACHE_DIR . "/session-{$deviceID}.txt");
            if (file_exists($session_file) && substr_count($session_file, WPDM_CACHE_DIR)) {
                $data = file_get_contents($session_file);
                $data = Crypt::decrypt($data, true);
                if (!is_array($data)) $data = array();
            } else {
                $data = array();
            }

            self::$data = $data;

            register_shutdown_function(array($this, 'saveSession'));
        }
    }

    static function deviceID($deviceID = null)
    {
        if(!$deviceID) {
            $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $deviceID = md5(__::get_client_ip() . $agent);
        }
        $deviceID = __::sanitize_var($deviceID, 'alphanum');
        self::$deviceID = $deviceID;
        return self::$deviceID;
    }

    static function set($name, $value, $expire = 1800)
    {
        global $wpdb;
        if(!$name) return;
        if(!self::$deviceID) Session::deviceID();
        if (self::$store === 'file') self::$data[$name] = array('value' => $value, 'expire' => time() + $expire);
        else {
            self::clear($name);
            $wpdb->insert("{$wpdb->prefix}ahm_sessions", array('deviceID' => self::$deviceID, 'name' => $name, 'value' => maybe_serialize($value), 'expire' => time() + $expire));
        }
    }

    static function get($name)
    {
        if(!self::$deviceID) new Session();

        if (self::$store === 'file') {
            if (!isset(self::$data[$name])) return null;
            $_value = self::$data[$name];
            if (count($_value) == 0) return null;
            extract($_value);
            if (isset($expire) && $expire < time()) {
                unset(self::$data[$name]);
                $value = null;
            }
        }
        else {
            global $wpdb;
            $deviceID = self::$deviceID;
            $time = time();
            $value = $wpdb->get_var("select `value` from {$wpdb->prefix}ahm_sessions where deviceID = '{$deviceID}' and `name` = '{$name}' and expire > $time");
        }
        return maybe_unserialize($value);

    }

    static function clear($name = '')
    {
        global $wpdb;

        if(!self::$deviceID) new Session();

        if ($name == '') {
            if (self::$store === 'file') self::$data = array();
            else $wpdb->delete("{$wpdb->prefix}ahm_sessions", array('deviceID' => self::$deviceID));
        } else {
            //if(self::$store === 'cookie') setcookie($name, null, '/', time() - 3600);
            if (self::$store === 'file' && isset(self::$data[$name])) unset(self::$data[$name]);
            else $wpdb->delete("{$wpdb->prefix}ahm_sessions", array('deviceID' => self::$deviceID, 'name' => $name));
        }
    }

	static function reset()
	{
		global $wpdb;
		$wpdb->query("delete from {$wpdb->prefix}ahm_sessions where deviceID != 'alldevice'");
	}

    static function show()
    {
        wpdmprecho(self::$data);
    }

    static function saveSession()
    {
        if(!self::$deviceID) new Session();

        if (self::$store === 'file' && is_array(self::$data) && count(self::$data) > 0) {
            $data = Crypt::encrypt(self::$data);
            if (!file_exists(WPDM_CACHE_DIR))
                @mkdir(WPDM_CACHE_DIR, 0755, true);
            file_put_contents(WPDM_CACHE_DIR . 'session-' . self::$deviceID . '.txt', $data);
        }

    }

}


