<?php

if(!function_exists('prsv_input_fetch'))
{
    /**
     * Fetch data from all INPUT_* vars
     *
     * @param string $name name
     * @param
     * @return mixed|bool
     */
    function prsv_input_fetch($type, string $name = null, $default = false)
    {
       $global = array();

       switch($type)
       {
           case INPUT_POST:     $global = $_POST;break;
           case INPUT_GET:      $global = $_GET;break;
           case INPUT_REQUEST:  $global = $_REQUEST;break;
           case INPUT_COOKIE:   $global = $_COOKIE;break;
           case INPUT_SERVER:   $global = $_SERVER;break;
           case INPUT_SESSION:  $global = $_SESSION;break;
           case INPUT_ENV:      $global = $_ENV;break;
       }

       if(is_null($name))
       {
           return $global;
       }

       if(!array_key_exists($name, $global))
       {
           return $default;
       }

       $var = $global[$name];

       switch(gettype($var))
       {
           case "boolean": $var = boolval($var);break;
           case "double":  $var = doubleval($var);break;
           case "float":   $var = floatval($var);break;
           case "string":  $var = sanitize_text_field($var);break;
           case "integer": $var = intval($var);break;
       }

       if(is_array($global[$name]))
       {
           return $global[$name];
       }

       return $var;
    }
}

if(!function_exists('prsv_input_post'))
{
    /**
     * Fetch data from POST
     *
     * @param string $name      name
     * @param mixed  $default   default value
     *
     * @return mixed|bool
     */
    function prsv_input_post($name = null, $default = false)
    {
        return prsv_input_fetch(INPUT_POST,$name, $default);
    }
}


if(!function_exists('prsv_input_get'))
{
    /**
     * Fetch data from GET
     *
     * @param string $name      name
     * @param mixed  $default   default value
     *
     * @return mixed|bool
     */
    function prsv_input_get($name, $default = false)
    {
        return prsv_input_fetch(INPUT_GET, $name, $default);
    }
}


if(!function_exists('prsv_input_request'))
{
    /**
     * Fetch data from REQUEST
     *
     * @param string $name      name
     * @param mixed  $default   default value
     *
     * @return mixed|bool
     */
    function prsv_input_request($name, $default = false)
    {
        return prsv_input_fetch(INPUT_REQUEST,$name, $default);
    }
}


if(!function_exists('prsv_input_get_post'))
{
    /**
     * Fetch data from GET or POST
     *
     * @param string $name      name
     * @param mixed  $default   default value
     *
     * @return mixed|bool
     */
    function prsv_input_get_post($name, $default = false)
    {
        $res = prsv_input_get($name, $default);

        if(!$res){
            $res = prsv_input_post($name, $default);
        }

        return $res;
    }
}

if(!function_exists('prsv_input_session'))
{
    /**
     * Fetch data from SESSION
     *
     * @param string $name      name
     * @param mixed  $default   default value
     *
     * @return mixed|bool
     */
    function prsv_input_session($name, $default = false)
    {
        return prsv_input_fetch(INPUT_SESSION,$name, $default);
    }
}

if(!function_exists('prsv_input_cookie'))
{
    /**
     * Fetch data from COOKIE
     *
     * @param string $name      name
     * @param mixed  $default   default value
     *
     * @return mixed|bool
     */
    function prsv_input_cookie($name, $default = false)
    {
        return prsv_input_fetch(INPUT_COOKIE,$name, $default);
    }
}


if(!function_exists('prsv_input_server'))
{
    /**
     * Fetch data from SERVER
     *
     * @param string $name      name
     * @param mixed  $default   default value
     *
     * @return mixed|bool
     */
    function prsv_input_server($name, $default = false)
    {
        return prsv_input_fetch(INPUT_SERVER,$name, $default);
    }
}


if(!function_exists('prsv_input_env'))
{
    /**
     * Fetch data from ENV
     *
     * @param string $name      name
     * @param mixed  $default   default value
     *
     * @return mixed|bool
     */
    function prsv_input_env($name, $default = false)
    {
        return prsv_input_fetch(INPUT_ENV,$name, $default);
    }
}
