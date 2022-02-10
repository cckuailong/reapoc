<?php
/**
 * User: shahnuralam
 * Date: 4/11/18
 * Time: 1:10 PM
 * From v4.7.9
 * Last Updated: 10/11/2018
 */


namespace WPDM\__;

class TempStorage
{
    static $data;

    function __construct()
    {
        /*if(file_exists(WPDM_CACHE_DIR.'/temp-storage.txt')) {
            $data = file_get_contents(WPDM_CACHE_DIR . '/temp-storage.txt');
            $data = Crypt::decrypt($data);
            if(!is_array($data)) $data = array();
        } else {
            $data = array();
        }
        self::$data = $data;*/

        //register_shutdown_function(array($this, 'saveData'));
    }

    static function set($name, $value, $expire = 604800)
    {  // 604800 secs = 1 week
        //self::$data[$name] = array('value' => $value, 'expire' => time() + $expire);
        global $wpdb;
        self::kill($name);
        $wpdb->insert("{$wpdb->prefix}ahm_sessions", array('deviceID' => 'alldevice', 'name' => $name, 'value' => maybe_serialize($value), 'expire' => time() + $expire));
    }

    static function get($name)
    {
        /*if(!isset(self::$data[$name])) return null;
        $_value = self::$data[$name];
        if(count($_value) == 0) return null;
        extract($_value);
        if(isset($expire) && $expire < time()) {
            unset(self::$data[$name]);
            $value = null;
        }
        return $value;*/
        global $wpdb;
        $now = time();
        $value = $wpdb->get_var("select `value` from {$wpdb->prefix}ahm_sessions where `expire` > $now and `name` = '{$name}'");
        return maybe_unserialize($value);
    }

    static function kill($name)
    {
        global $wpdb;
        $wpdb->delete("{$wpdb->prefix}ahm_sessions", ["name" => $name]);
    }

    static function clear()
    {
	    global $wpdb;
	    $wpdb->query("delete from {$wpdb->prefix}ahm_sessions where deviceID = 'alldevice'");
    }

    function __destruct()
    {
        /*if(is_array(self::$data)) {
            foreach (self::$data as $name => $_value){
                extract($_value);
                if(!is_array($_value) || !isset($_value['expire']) || $_value['expire'] < time()) {
                    unset(self::$data[$name]);
                }
            }
            $data = Crypt::encrypt(self::$data);
            file_put_contents(WPDM_CACHE_DIR . '/temp-storage.txt', $data);
        }*/
    }

    static function saveData()
    {
        /*if(is_array(self::$data)) {
            foreach (self::$data as $name => $_value){
                extract($_value);
                if(!is_array($_value) || !isset($_value['expire']) || $_value['expire'] < time()) {
                    unset(self::$data[$name]);
                }
            }
            $data = Crypt::encrypt(self::$data);
            if(!file_exists(WPDM_CACHE_DIR))
                @mkdir(WPDM_CACHE_DIR, 0755, true);
            file_put_contents(WPDM_CACHE_DIR . '/temp-storage.txt', $data);
        }*/

    }

}

new TempStorage();
