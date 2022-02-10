<?php

namespace Cloudipsp\Helper;

use DateTime;
use InvalidArgumentException;

class ValidationHelper
{
    /**
     * Validation required params not empty
     * @param $params
     * @param $required
     * @param $dateFormat
     * @return bool
     */
    public static function validateRequiredParams($params, $required, $dateFormat = 'Y-m-d')
    {

        foreach ($required as $key => $param) {
            if (is_array($param))
                self::validateRequiredParams($params[$key], $param);
            if (!array_key_exists($key, $params)) {
                throw new InvalidArgumentException(sprintf('Required param "%s" is missing', $key));
            }
            if (array_key_exists($key, $required) && empty($param)) {
                throw new InvalidArgumentException(sprintf('Required param "%s" is empty', $key));
            }
            switch ($param) {
                case 'integer':
                    self::validateInteger($params[$key], $key);
                    break;
                case 'string':
                    self::validateString($params[$key], $key);
                    break;
                case 'date':
                    self::validateDate($params[$key], $key, $dateFormat);
                    break;
                case 'ccnumber':
                    self::validateCCard($params[$key]);
                    break;
                case 'ip':
                    self::validateIP($params[$key]);
                    break;
            }

        }

        return true;
    }

    /**
     * Helper method for validating URLs
     * @param      $url
     * @throws InvalidArgumentException
     */
    public static function validateURL($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException(sprintf("%s is not a fully qualified URL", $url));
        }
    }

    /**
     * Helper method for validating date
     * @param $date
     * @param $key
     * @param $format
     * @return bool
     */
    public static function validateDate($date, $key, $format)
    {
        $d = DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) == $date) {
            return true;
        } else {
            throw new InvalidArgumentException(sprintf('Date %s is incorrect! Accepted format is: %s', $key, $format));
        }
    }

    /**
     * @param $param
     * @param string $key
     */
    public static function validateInteger($param, $key = '')
    {
        if (trim($param) != null && !is_numeric($param)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid numeric', $key));
        }
    }

    /**
     * @param $param
     * @param string $key
     */
    public static function validateString($param, $key = '')
    {
        if ($key !== 'order_id' && $param != null && !is_string($param)) {
            throw new InvalidArgumentException(sprintf('%s is not a valid string', $key));
        }
        if ($key === 'order_id' && $param == null) {
            throw new InvalidArgumentException(sprintf('%s is not a valid string', $key));
        }
    }

    /**
     * Luhn algorithm
     * @param $number
     * @return bool
     */
    protected static function validateCCard($number)
    {
        $checksum = 0;
        for ($i = (2 - (strlen($number) % 2)); $i <= strlen($number); $i += 2) {
            $checksum += (int)($number[$i - 1]);
        }
        // Analyze odd digits in even length strings or even digits in odd length strings.
        for ($i = (strlen($number) % 2) + 1; $i < strlen($number); $i += 2) {
            $digit = (int)($number[$i - 1]) * 2;
            if ($digit < 10) {
                $checksum += $digit;
            } else {
                $checksum += ($digit - 9);
            }
        }
        if (($checksum % 10) == 0) {
            return true;
        } else {
            throw new InvalidArgumentException(sprintf('\'%s\' is not a valid credit card number', $number));
        }
    }

    /**
     * @param $ip
     * @return bool
     */
    protected static function validateIP($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        } else {
            throw new InvalidArgumentException(sprintf('\'%s\' is not a valid ip', $ip));
        }
    }
}