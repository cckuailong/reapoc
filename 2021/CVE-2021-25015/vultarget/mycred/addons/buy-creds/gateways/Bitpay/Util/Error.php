<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Util;

class Error
{
    /**
     * Generates a backtrace and returns an array of associative
     * arrays or prints to stdout. The possible returned elements
     * are as follows: function, line, file, class, object, type
     * and args.
     * (PHP 4 >= 4.3.0, PHP 5)
     *
     * @param bool
     * @param bool
     * @param int
     * @return array|void
     */
    final public function backtrace($print = false, $options = false, $limit = 0)
    {
        if ($print == true) {
            return debug_print_backtrace($options, $limit);
        } else {
            return debug_backtrace($options, $limit);
        }
    }

    /**
     * Get the last occurred error and returns an associative
     * array describing the last error with keys "type", "message",
     * "file" and "line". If the error has been caused by a PHP
     * internal function then the "message" begins with its name.
     * Returns NULL if there hasn't been an error yet.
     * (PHP 5 >= 5.2.0)
     *
     * @param void
     * @return array
     */
    final public function last()
    {
        return error_get_last();
    }

    /**
     * Send an error message to the defined error handling
     * routines.  Returns true on success or false on failure.
     * The possible values for $message_type are: 0 = system log,
     * 1 = email to $destination, 2 = depricated, 3 = appended
     * to file $destination, 4 = sent to SAPI log handler.
     * (PHP 4, PHP 5)
     *
     * @param string
     * @param int
     * @param string
     * @param string
     */
    final public function log($message, $message_type = 0, $destination = '', $extra_headers = '')
    {
        return error_log((string) $message, $message_type = 0, $destination = '', $extra_headers = '');
    }

    /**
     * Sets which PHP errors are reported or returns the old
     * error_reporting level or the current level if no level
     * parameter is given.
     * (PHP 4, PHP 5)
     *
     * @param bool
     * @return int
     */
    final public function reporting($level = false)
    {
        if ($level !== false) {
            return error_reporting($level);
        } else {
            return error_reporting();
        }
    }

    /**
     * Sets or restores either the error or exception handler
     * based on the $type and $action parameters.
     * (PHP 4 >= 4.0.1, PHP 5)
     *
     * @param string
     * @param string
     * @param mixed
     * @param int
     * return mixed
     */
    final public function handler($type = 'error', $action = 'restore', $callable_handler = false, $error_types = null)
    {
        if (empty($error_types)) {
            $error_types = E_ALL | E_STRICT;
        }
        switch (strtolower($type)) {
            case 'error':
                switch (strtolower($action)) {
                    case 'restore':
                        return restore_error_handler();
                        break;
                    case 'set':
                        return set_error_handler($callable_handler, $error_types);
                        break;
                    default:
                        return false;
                }
                break;
            case 'exception':
                switch (strtolower($action)) {
                    case 'restore':
                        return restore_exception_handler();
                        break;
                    case 'set':
                        return set_exception_handler($callable_handler);
                        break;
                    default:
                        return false;
                }
                break;
            default:
                return false;
        }
    }

    /**
     * Generates a user-level error/warning/notice message.
     * This function returns FALSE if wrong $error_type is
     * specified, TRUE otherwise. The $error_msg param is
     * limited to 1024 bytes.
     * (PHP 4 >= 4.0.1, PHP 5)
     *
     * @param string
     * @param int
     * @return bool
     */
    final public function raise($error_msg, $error_type = E_USER_NOTICE)
    {
        return trigger_error($error_msg, $error_type);
    }
}
