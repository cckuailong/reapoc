<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\Vectorface\Whip\Whip;

class Helper
{
    /**
     * @param array|string $name
     * @param string $path
     * @return string
     */
    public static function buildClassName($name, $path = '')
    {
        if (is_array($name)) {
            $name = implode('-', $name);
        }
        $className = Str::camelCase($name);
        $path = ltrim(str_replace(__NAMESPACE__, '', $path), '\\');
        return !empty($path)
            ? __NAMESPACE__.'\\'.$path.'\\'.$className
            : $className;
    }

    /**
     * @param string $name
     * @param string $prefix
     * @return string
     */
    public static function buildMethodName($name, $prefix = '')
    {
        return lcfirst(Str::camelCase($prefix.'-'.$name));
    }

    /**
     * @param string $name
     * @return string
     */
    public static function buildPropertyName($name)
    {
        return static::buildMethodName($name);
    }

    /**
     * @param int|string $version1
     * @param int|string $version2
     * @param string $operator
     * @return bool
     */
    public static function compareVersions($version1, $version2, $operator = '=')
    {
        $version1 = implode('.', array_pad(explode('.', $version1), 3, 0));
        $version2 = implode('.', array_pad(explode('.', $version2), 3, 0));
        return version_compare($version1, $version2, $operator);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function filterInput($key, array $request = [])
    {
        if (isset($request[$key])) {
            return $request[$key];
        }
        $variable = filter_input(INPUT_POST, $key);
        if (is_null($variable) && isset($_POST[$key])) {
            $variable = $_POST[$key];
        }
        return $variable;
    }

    /**
     * @param string $key
     * @return array
     */
    public static function filterInputArray($key)
    {
        $variable = filter_input(INPUT_POST, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (empty($variable) && !empty($_POST[$key]) && is_array($_POST[$key])) {
            $variable = $_POST[$key];
        }
        return Cast::toArray($variable);
    }

    /**
     * @return string
     */
    public static function getIpAddress()
    {
        $whitelist = [];
        $isUsingCloudflare = !empty(filter_input(INPUT_SERVER, 'CF-Connecting-IP'));
        if (glsr()->filterBool('whip/whitelist/cloudflare', $isUsingCloudflare)) {
            $cloudflareIps = glsr(Cache::class)->getCloudflareIps();
            $whitelist[Whip::CLOUDFLARE_HEADERS] = [Whip::IPV4 => $cloudflareIps['v4']];
            if (defined('AF_INET6')) {
                $whitelist[Whip::CLOUDFLARE_HEADERS][Whip::IPV6] = $cloudflareIps['v6'];
            }
        }
        $whitelist = glsr()->filterArray('whip/whitelist', $whitelist);
        $methods = glsr()->filterInt('whip/methods', Whip::ALL_METHODS);
        $whip = new Whip($methods, $whitelist);
        glsr()->action('whip', $whip);
        if (false !== ($clientAddress = $whip->getValidIpAddress())) {
            return (string) $clientAddress;
        }
        glsr_log()->error('Unable to detect IP address, please see the FAQ page for a possible solution.');
        return 'unknown';
    }

    /**
     * @param string $fromUrl
     * @param int $fallback
     * @return int
     */
    public static function getPageNumber($fromUrl = null, $fallback = 1)
    {
        $pagedQueryVar = glsr()->constant('PAGED_QUERY_VAR');
        $pageNum = empty($fromUrl)
            ? filter_input(INPUT_GET, $pagedQueryVar, FILTER_VALIDATE_INT)
            : filter_var(Url::query($fromUrl, $pagedQueryVar), FILTER_VALIDATE_INT);
        if (empty($pageNum)) {
            $pageNum = (int) $fallback;
        }
        return max(1, $pageNum);
    }

    /**
     * @param mixed $post
     * @return int
     */
    public static function getPostId($post)
    {
        if (is_numeric($post) || $post instanceof \WP_Post) {
            $post = get_post($post);
        }
        if ($post instanceof \WP_Post) {
            return $post->ID;
        }
        if ('parent_id' == $post) {
            return (int) wp_get_post_parent_id(intval(get_the_ID()));
        }
        if ('post_id' == $post) {
            return (int) get_the_ID();
        }
        if (is_string($post)) {
            $parts = explode(':', $post);
            if (2 === count($parts)) {
                $posts = get_posts([
                    'fields' => 'ids',
                    'post_name__in' => [$parts[1]],
                    'post_type' => $parts[0],
                    'posts_per_page' => 1,
                ]);
                return Cast::toInt(Arr::get($posts, 0));
            }
        }
        return 0;
    }

    /**
     * @param mixed $term
     * @return int
     */
    public static function getTermTaxonomyId($term)
    {
        if ($term instanceof \WP_Term) {
            return $term->term_id;
        }
        if (is_numeric($term)) {
            $term = Cast::toInt($term);
        }
        $tt = term_exists($term, glsr()->taxonomy);
        return Cast::toInt(Arr::get($tt, 'term_id'));
    }

    /**
     * @param mixed $user
     * @return int
     */
    public static function getUserId($user)
    {
        if ($user instanceof \WP_User) {
            return $user->ID;
        }
        if ('user_id' === $user) {
            return get_current_user_id();
        }
        if (is_numeric($user)) {
            $user = get_user_by('ID', $user);
            return Cast::toInt(Arr::get($user, 'ID'));
        }
        if (is_string($user)) {
            $user = get_user_by('login', $user);
            return Cast::toInt(Arr::get($user, 'ID'));
        }
        return 0;
    }

    /**
     * @param mixed $value
     * @param mixed $fallback
     * @return mixed
     */
    public static function ifEmpty($value, $fallback, $strict = false)
    {
        $isEmpty = $strict ? empty($value) : static::isEmpty($value);
        return $isEmpty ? $fallback : $value;
    }

    /**
     * @param bool $condition
     * @param mixed $ifTrue
     * @param mixed $ifFalse
     * @return mixed
     */
    public static function ifTrue($condition, $ifTrue, $ifFalse = null)
    {
        return $condition ? static::runClosure($ifTrue) : static::runClosure($ifFalse);
    }

    /**
     * @param mixed $value
     * @param string|int $min
     * @param string|int $max
     * @return bool
     */
    public static function inRange($value, $min, $max)
    {
        $inRange = filter_var($value, FILTER_VALIDATE_INT, ['options' => [
            'min_range' => intval($min),
            'max_range' => intval($max),
        ]]);
        return false !== $inRange;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isEmpty($value)
    {
        if (is_string($value)) {
            return trim($value) === '';
        }
        return !is_numeric($value) && !is_bool($value) && empty($value);
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     * @return bool
     */
    public static function isGreaterThan($value, $compareWithValue)
    {
        return static::compareVersions($value, $compareWithValue, '>');
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     * @return bool
     */
    public static function isGreaterThanOrEqual($value, $compareWithValue)
    {
        return static::compareVersions($value, $compareWithValue, '>=');
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     * @return bool
     */
    public static function isLessThan($value, $compareWithValue)
    {
        return static::compareVersions($value, $compareWithValue, '<');
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     * @return bool
     */
    public static function isLessThanOrEqual($value, $compareWithValue)
    {
        return static::compareVersions($value, $compareWithValue, '<=');
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isNotEmpty($value)
    {
        return !static::isEmpty($value);
    }

    /**
     * @param string $url
     * @param int $maxRedirects
     * @return int|false
     */
    public static function remoteStatusCheck($url, $maxRedirects = 0)
    {
        $headers = get_headers($url, 0, stream_context_create([
            'http' => [
                'ignore_errors' => 1,
                'max_redirects' => $maxRedirects,
                'method' => 'HEAD',
            ],
        ]));
        if (false !== $headers) {
            return Cast::toInt(substr($headers[0], 9, 3));
        }
        return false;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public static function runClosure($value)
    {
        if ($value instanceof \Closure || (is_array($value) && is_callable($value))) {
            return call_user_func($value);
        }
        return $value;
    }
}
