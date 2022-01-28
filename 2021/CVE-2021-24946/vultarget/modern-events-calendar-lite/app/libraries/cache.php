<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Cache class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_cache
{
    protected static $instance = null;
    protected static $cache = array();

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    private function __construct()
    {
    }

    public function __clone()
    {
    }

    public function __wakeup()
    {
    }

    public static function getInstance()
    {
        // Get an instance of Class
        if(is_null(self::$instance)) self::$instance = new self();

        // Return the instance
        return self::$instance;
    }

    public static function set($key, $value)
    {
        self::$cache[$key] = $value;
    }

    public static function has($key)
    {
        return isset(self::$cache[$key]);
    }

    public static function get($key)
    {
        return (isset(self::$cache[$key]) ? self::$cache[$key] : NULL);
    }

    public static function delete($key)
    {
        if(MEC_cache::has($key))
        {
            unset(self::$cache[$key]);
            return true;
        }

        return false;
    }
}