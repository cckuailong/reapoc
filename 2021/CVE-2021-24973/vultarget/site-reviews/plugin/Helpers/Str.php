<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;

class Str
{
    /**
     * @param string $string
     * @return string
     */
    public static function camelCase($string)
    {
        $string = ucwords(str_replace(['-', '_'], ' ', trim($string)));
        return str_replace(' ', '', $string);
    }

    /**
     * @param string|string[] $needles
     * @param string $haystack
     * @return bool
     */
    public static function contains($needles, $haystack)
    {
        $needles = array_filter(Cast::toArray($needles), Helper::class.'::isNotEmpty');
        foreach ($needles as $needle) {
            if (false !== strpos($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @param string $nameType first|first_initial|initials|last|last_initial
     * @param string $initialType period|period_space|space
     * @return string
     */
    public static function convertName($name, $nameType = '', $initialType = '')
    {
        $names = preg_split('/\W/u', $name, 0, PREG_SPLIT_NO_EMPTY);
        $firstName = array_shift($names);
        $lastName = array_pop($names);
        $initialTypes = [
            'period' => '.',
            'period_space' => '. ',
            'space' => ' ',
        ];
        $initialPunctuation = (string) Arr::get($initialTypes, $initialType, ' ');
        if ('initials' == $nameType) {
            return static::convertToInitials($name, $initialPunctuation);
        }
        $nameTypes = [
            'first' => $firstName,
            'first_initial' => static::convertToInitials($firstName).$initialPunctuation.$lastName,
            'last' => $lastName,
            'last_initial' => $firstName.' '.static::convertToInitials($lastName).$initialPunctuation,
        ];
        return trim((string) Arr::get($nameTypes, $nameType, $name));
    }

    /**
     * @param string $path
     * @param string $prefix
     * @return string
     */
    public static function convertPathToId($path, $prefix = '')
    {
        return str_replace(['[', ']'], ['-', ''], static::convertPathToName($path, $prefix));
    }

    /**
     * @param string $path
     * @param string $prefix
     * @return string
     */
    public static function convertPathToName($path, $prefix = '')
    {
        $levels = explode('.', $path);
        return array_reduce($levels, function ($result, $value) {
            return $result .= '['.$value.']';
        }, $prefix);
    }

    /**
     * @param string $name
     * @param string $initialPunctuation
     * @return string
     */
    public static function convertToInitials($name, $initialPunctuation = '')
    {
        preg_match_all('/(?<=\s|\b)\pL/u', $name, $matches);
        $result = array_reduce($matches[0], function ($carry, $word) use ($initialPunctuation) {
            $initial = mb_substr($word, 0, 1, 'UTF-8');
            $initial = mb_strtoupper($initial, 'UTF-8');
            return $carry.$initial.$initialPunctuation;
        });
        return trim($result);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function dashCase($string)
    {
        return str_replace('_', '-', static::snakeCase($string));
    }

    /**
     * @param string|string[] $needles
     * @param string $haystack
     * @return bool
     */
    public static function endsWith($needles, $haystack)
    {
        $needles = array_filter(Cast::toArray($needles), Helper::class.'::isNotEmpty');
        foreach ($needles as $needle) {
            if (substr($haystack, -strlen(Cast::toString($needle))) === $needle) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $value
     * @param string $fallback
     * @return string
     */
    public static function fallback($value, $fallback)
    {
        return is_scalar($value) && '' !== trim($value)
            ? Cast::toString($value)
            : Cast::toString($fallback);
    }

    /**
     * @return string
     */
    public static function naturalJoin(array $values)
    {
        $and = __('and', 'site-reviews');
        $values[] = implode(' '.$and.' ', array_splice($values, -2));
        return implode(', ', $values);
    }

    /**
     * @param string $string
     * @param string $prefix
     * @param string|null $trim
     * @return string
     */
    public static function prefix($string, $prefix, $trim = null)
    {
        if ('' === $string) {
            return $string;
        }
        if (null === $trim) {
            $trim = $prefix;
        }
        return $prefix.trim(static::removePrefix($string, $trim));
    }

    /**
     * @param int $length
     * @return string
     */
    public static function random($length = 8)
    {
        $text = base64_encode(wp_generate_password());
        return substr(str_replace(['/','+','='], '', $text), 0, $length);
    }

    /**
     * @param string $prefix
     * @param string $string
     * @return string
     */
    public static function removePrefix($string, $prefix)
    {
        return static::startsWith($prefix, $string)
            ? substr($string, strlen($prefix))
            : $string;
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function replaceFirst($search, $replace, $subject)
    {
        if ($search == '') {
            return $subject;
        }
        $position = strpos($subject, $search);
        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }
        return $subject;
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function replaceLast($search, $replace, $subject)
    {
        $position = strrpos($subject, $search);
        if ('' !== $search && false !== $position) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }
        return $subject;
    }

    /**
     * @param string|string[] $restrictions
     * @param string $value
     * @param string $fallback
     * @param bool $strict
     * @return string
     */
    public static function restrictTo($restrictions, $value, $fallback = '', $strict = false)
    {
        $needle = $value;
        $haystack = Cast::toArray($restrictions);
        if (true !== $strict) {
            $needle = strtolower($needle);
            $haystack = array_map('strtolower', $haystack);
        }
        return in_array($needle, $haystack)
            ? $value
            : $fallback;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function snakeCase($string)
    {
        if (!ctype_lower($string)) {
            $string = preg_replace('/\s+/u', '', $string);
            $string = preg_replace('/(.)(?=[A-Z])/u', '$1_', $string);
            $string = mb_strtolower($string, 'UTF-8');
        }
        return str_replace('-', '_', $string);
    }

    /**
     * @param string|string[] $needles
     * @param string $haystack
     * @return bool
     */
    public static function startsWith($needles, $haystack)
    {
        $needles = array_filter(Cast::toArray($needles), Helper::class.'::isNotEmpty');
        foreach ($needles as $needle) {
            if (substr($haystack, 0, strlen(Cast::toString($needle))) === $needle) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $string
     * @param string $suffix
     * @return string
     */
    public static function suffix($string, $suffix)
    {
        if (!static::endsWith($suffix, $string)) {
            return $string.$suffix;
        }
        return $string;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function titleCase($string)
    {
        $value = str_replace(['-', '_'], ' ', $string);
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * @param string $value
     * @param int $length
     * @param string $end
     * @return string
     */
    public static function truncate($value, $length, $end = '')
    {
        return mb_strwidth($value, 'UTF-8') > $length
            ? mb_substr($value, 0, $length, 'UTF-8').$end
            : $value;
    }
}
