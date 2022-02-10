<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');

/**
 * Storage of validation methods.
 *
 * @category  Payment
 * @package   Sagepay
 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepayValid
{

    /**
     * Checks if a field is not empty.
     *
     * @param string $input Description
     *
     * @return  boolean
     */
    public static function notEmpty($input)
    {
        return !empty($input) || is_string($input) && strlen($input) > 0;
    }

    /**
     * Checks a field against a regular expression.
     *
     * @param   string  $input      value
     * @param   string  $regexp regular expression to match (including delimiters)
     *
     * @return  boolean
     */
    public static function regex($input, $regexp)
    {
        return (bool) preg_match($regexp, (string) $input);
    }

    /**
     * Checks that a field is greater or equal with minimum required.
     *
     * @param   string  $input  value
     * @param   integer $length minimum length required
     *
     * @return  boolean
     */
    public static function minLength($input, $length)
    {
        return strlen($input) >= $length;
    }

    /**
     * Checks that a field is less or equal with maximum required
     *
     * @param   string  $input  value
     * @param   integer $length maximum length required
     *
     * @return  boolean
     */
    public static function maxLength($input, $length)
    {
        return strlen($input) <= $length;
    }

    /**
     * Checks that a number is within a range.
     *
     * @param   string  $input number to check
     * @param   integer $minValue    minimum value
     * @param   integer $maxValue    maximum value
     *
     * @return  boolean
     */
    public static function range($input, $minValue, $maxValue)
    {
        return ($input >= $minValue && $input <= $maxValue);
    }

    /**
     * Checks that a field have exact length.
     *
     * @param   string          $input  value
     * @param   integer|array   $length exact length required, or array of valid lengths
     *
     * @return  boolean
     */
    public static function exactLength($input, $length)
    {
        if (is_array($length))
        {
            foreach ($length as $specificLength)
            {
                if (strlen($input) === $specificLength)
                {
                    return TRUE;
                }
            }
            return FALSE;
        }

        return strlen($input) === $length;
    }

    /**
     * Checks that a field is same as required.
     *
     * @param   string  $input      original value
     * @param   string  $expected   expected value
     *
     * @return  boolean
     */
    public static function equals($input, $expected)
    {
        return ($input === $expected);
    }

    /**
     * Check an email address for valid format.
     *
     * @param   string  $input  email address
     *
     * @return  boolean
     */
    public static function email($input)
    {
        if ($input === '')
        {
            return true;
        }
        return (bool) filter_var($input, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check an URL for valid format.
     *
     * @param   string  $input  URL
     *
     * @return  boolean
     */
    static public function url($input)
    {
        return (bool) filter_var($input, FILTER_VALIDATE_URL);
    }

    /**
     * Validates a credit card number with luhn checksum
     *
     * @uses    SagepayValid::luhn
     * @param   integer         $input credit card number
     *
     * @return  boolean
     */
    public static function creditCard($input)
    {
        $input = preg_replace('/\D+/', '', $input);
        return self::luhn($input);
    }

    /**
     * Validate a number against the Luhn checksum
     *
     * @link http://en.wikipedia.org/wiki/Luhn_algorithm
     * @param   string  $input number to check
     *
     * @return  boolean
     */
    public static function luhn($input)
    {
        if (!ctype_digit($input))
        {
            return FALSE;
        }

        $checksum = '';

        foreach (str_split(strrev($input)) as $i => $d)
        {
            $checksum .= $i % 2 !== 0 ? $d * 2 : $d;
        }

        return array_sum(str_split($checksum)) % 10 === 0;
    }

    /**
     * Checks whether a string consists of digits only.
     *
     * @param   string  $input    input string
     *
     * @return  boolean
     */
    public static function digit($input)
    {
        return (is_int($input) && $input >= 0) || ctype_digit($input);
    }

    /**
     * Checks whether a string is a valid number.
     *
     * @param   string  $input    input string
     *
     * @return  boolean
     */
    public static function numeric($input)
    {
        return (bool) preg_match('/^-?+(?=.*[0-9])[0-9]*+[,\.]?+[0-9]*+$/D', $input);
    }

    /**
     * Checks if a string is a proper decimal format.
     *
     * @param   string  $input    number to check
     * @param   integer $places number of decimal places
     *
     * @return  boolean
     */
    public static function decimal($input, $places = 2)
    {
        return (bool) preg_match('/^[+-]?[0-9]+[,\.][0-9]{' . ((int) $places) . '}$/D', $input);
    }

}
