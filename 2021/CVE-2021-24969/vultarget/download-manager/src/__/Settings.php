<?php


namespace WPDM\__;

class Settings
{

    function __construct()
    {
    }

    function get($name, $default = ''){
        $value = get_option($name);
        $value = htmlspecialchars_decode($value);
        $value = stripslashes_deep($value);
        $value = wpdm_escs($value);
        return $value;
    }

    function __get($name)
    {
        $name = "__wpdm_".$name;
        $value = get_option($name);
        $value = maybe_unserialize($value);
        if(!is_array($value)) {
            $value = htmlspecialchars_decode($value);
            $value = stripslashes_deep($value);
            $value = wpdm_escs($value);
        }
        return $value;
    }

    function __call($name, $args = null)
    {
        $name = "__wpdm_".$name;
        $value = get_option($name);
        if($args === null) {
            $value = htmlspecialchars_decode($value);
            $value = stripslashes_deep($value);
            $value = wpdm_escs($value);
        } else {
            $value = wpdm_sanitize_var($value, $args[0]);
        }
        return $value;
    }

    static function __callStatic($name, $args = null)
    {
        $name = "__wpdm_".$name;
        $value = get_option($name);
        $value = htmlspecialchars_decode($value);
        $value = stripslashes_deep($value);
        $value = wpdm_escs($value);
        return $value;
    }


}
