<?php

/**
 * Bootstrap functions
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */

define('SAGEPAY_SDK_PATH', dirname(__FILE__)); // Path to SagePay SDK

include_once SAGEPAY_SDK_PATH . '/constants.php';

/**
 * Autoload function for Sagepay Classes
 *
 * @param string $class
 */
function sagepayAutoloader($class)
{
    if (substr($class, 0, 7) !== 'Sagepay')
    {
        return;
    }
    $class = substr($class, 7);
    $filepath = '';
    for ($i = 0, $n = strlen($class); $i < $n; $i++)
    {
        $char = $class[$i];
        if (preg_match('/[A-Z]/', $char))
        {
            $char = '_' . strtolower($char);
        }
        $filepath .= $char;
    }

    $filename = SAGEPAY_SDK_PATH . '/classes/' . substr($filepath, 1) . '.php';
    if (file_exists($filename))
    {
        include $filename;
    }
}

spl_autoload_register('sagepayAutoloader');
