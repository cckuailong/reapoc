<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;

class Arr
{
    /**
     * @return bool
     */
    public static function compare(array $arr1, array $arr2)
    {
        sort($arr1);
        sort($arr2);
        return $arr1 == $arr2;
    }

    /**
     * Returns an empty array by default if value is scalar
     * @param mixed $value
     * @return array
     */
    public static function consolidate($value, array $fallback = [])
    {
        if ($value instanceof Arguments) {
            return $value->toArray();
        }
        if (is_object($value)) {
            $values = get_object_vars($value);
            $value = Helper::ifEmpty($values, (array) $value, $strict = true);
        }
        return is_array($value) ? $value : $fallback;
    }

    /**
     * @return array
     */
    public static function convertFromDotNotation(array $array)
    {
        $results = [];
        foreach ($array as $path => $value) {
            $results = static::set($results, $path, $value);
        }
        return $results;
    }

    /**
     * @param mixed $value
     * @param mixed $callback
     * @return array
     */
    public static function convertFromString($value, $callback = null)
    {
        if (is_scalar($value)) {
            $value = array_map('trim', explode(',', Cast::toString($value)));
        }
        $callback = Helper::ifEmpty(Cast::toString($callback), Helper::class.'::isNotEmpty');
        return static::reindex(array_filter((array) $value, $callback));
    }

    /**
     * @param bool $flattenValue
     * @param string $prefix
     * @return array
     */
    public static function flatten(array $array, $flattenValue = false, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = ltrim($prefix.'.'.$key, '.');
            if (static::isIndexedAndFlat($value)) {
                $value = Helper::ifTrue(!$flattenValue, $value, function () use ($value) {
                    return '['.implode(', ', $value).']';
                });
            } elseif (is_array($value)) {
                $result = array_merge($result, static::flatten($value, $flattenValue, $newKey));
                continue;
            }
            $result[$newKey] = $value;
        }
        return $result;
    }

    /**
     * Get a value from an array of values using a dot-notation path as reference.
     * @param mixed $data
     * @param string|int $path
     * @param mixed $fallback
     * @return mixed
     */
    public static function get($data, $path = '', $fallback = '')
    {
        $data = static::consolidate($data);
        $keys = explode('.', $path);
        $result = $fallback;
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                return $fallback;
            }
            if (is_object($data[$key])) {
                $result = $data[$key];
                $data = static::consolidate($result);
                continue;
            }
            $result = $data[$key];
            $data = $result;
        }
        return $result;
    }

    /**
     * @param string|int $key
     * @return array
     */
    public static function insertAfter($key, array $array, array $insert)
    {
        return static::insert($array, $insert, $key, 'after');
    }

    /**
     * @param string|int $key
     * @return array
     */
    public static function insertBefore($key, array $array, array $insert)
    {
        return static::insert($array, $insert, $key, 'before');
    }

    /**
     * @param string|int $key
     * @param string $position
     * @return array
     */
    public static function insert(array $array, array $insert, $key, $position = 'before')
    {
        $keyPosition = array_search($key, array_keys($array));
        if (false !== $keyPosition) {
            $keyPosition = Cast::toInt($keyPosition);
            if ('after' == $position) {
                ++$keyPosition;
            }
            $result = array_slice($array, 0, $keyPosition);
            $result = array_merge($result, $insert);
            return array_merge($result, array_slice($array, $keyPosition));
        }
        return array_merge($array, $insert);
    }

    /**
     * @param mixed $array
     * @return bool
     */
    public static function isIndexedAndFlat($array)
    {
        if (!is_array($array) || array_filter($array, 'is_array')) {
            return false;
        }
        return wp_is_numeric_array($array);
    }

    /**
     * @param bool $prefixed
     * @return array
     */
    public static function prefixKeys(array $values, $prefix = '_', $prefixed = true)
    {
        $trim = Helper::ifTrue($prefixed, $prefix, '');
        $prefixed = [];
        foreach ($values as $key => $value) {
            $key = trim($key);
            if (0 === strpos($key, $prefix)) {
                $key = substr($key, strlen($prefix));
            }
            $prefixed[$trim.$key] = $value;
        }
        return $prefixed;
    }

    /**
     * @param array $array
     * @param mixed $value
     * @param mixed $key
     * @return array
     */
    public static function prepend($array, $value, $key = null)
    {
        if (!is_null($key)) {
            return [$key => $value] + $array;
        }
        array_unshift($array, $value);
        return $array;
    }

    /**
     * @param mixed $array
     * @return array
     */
    public static function reindex($array)
    {
        return static::isIndexedAndFlat($array) ? array_values($array) : $array;
    }

    /**
     * Unset a value from an array of values using a dot-notation path as reference.
     * @param mixed $data
     * @param string $path
     * @return array
     */
    public static function remove($data, $path = '')
    {
        $data = static::consolidate($data);
        $keys = explode('.', $path);
        $last = array_pop($keys);
        $pointer = &$data;
        foreach ($keys as $key) {
            if (is_array(static::get($pointer, $key))) {
                $pointer = &$pointer[$key];
            }
        }
        unset($pointer[$last]);
        return $data;
    }

    /**
     * @return array
     */
    public static function removeEmptyValues(array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (Helper::isEmpty($value)) {
                continue;
            }
            $result[$key] = Helper::ifTrue(!is_array($value), $value, function () use ($value) {
                return static::removeEmptyValues($value);
            });
        }
        return $result;
    }

    /**
     * Search a multidimensional array by key value
     * @param mixed $needle
     * @param array $haystack
     * @param int|string $key
     * @return array|false
     */
    public static function searchByKey($needle, $haystack, $key)
    {
        if (!is_array($haystack) || array_diff_key($haystack, array_filter($haystack, 'is_array'))) {
            return false;
        }
        $index = array_search($needle, wp_list_pluck($haystack, $key));
        if (false !== $index) {
            return $haystack[$index];
        }
        return false;
    }

    /**
     * Set a value to an array of values using a dot-notation path as reference.
     * @param mixed $data
     * @param string $path
     * @param mixed $value
     * @return array
     */
    public static function set($data, $path, $value)
    {
        $token = strtok($path, '.');
        $ref = &$data;
        while (false !== $token) {
            if (is_object($ref)) {
                $ref = &$ref->$token;
            } else {
                $ref = static::consolidate($ref);
                $ref = &$ref[$token];
            }
            $token = strtok('.');
        }
        $ref = $value;
        return $data;
    }

    /**
     * @return array
     */
    public static function unique(array $values)
    {
        return Helper::ifTrue(!static::isIndexedAndFlat($values), $values, function () use ($values) {
            return array_filter(array_unique($values)); // we do not want to reindex the array!
        });
    }

    /**
     * @param array|string $values
     * @return array
     */
    public static function uniqueInt($values)
    {
        $values = array_filter(static::convertFromString($values), 'is_numeric');
        return static::reindex(static::unique(array_values(array_map('absint', $values))));
    }

    /**
     * @return array
     */
    public static function unprefixKeys(array $values, $prefix = '_')
    {
        return static::prefixKeys($values, $prefix, false);
    }
}
