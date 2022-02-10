<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Multilingual;

class Sanitizer
{
    const JSON_ERROR_CODES = [
        JSON_ERROR_DEPTH => 'JSON Error: The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'JSON Error: Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'JSON Error: Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'JSON Error: Syntax error',
        JSON_ERROR_UTF8 => 'JSON Error: Malformed UTF-8 characters, possibly incorrectly encoded',
        JSON_ERROR_RECURSION => 'JSON Error: One or more recursive references in the value to be encoded',
        JSON_ERROR_INF_OR_NAN => 'JSON Error: One or more NAN or INF values in the value to be encoded',
        JSON_ERROR_UNSUPPORTED_TYPE => 'JSON Error: A value of a type that cannot be encoded was given',
        JSON_ERROR_INVALID_PROPERTY_NAME => 'JSON Error: A property name that cannot be encoded was given',
        JSON_ERROR_UTF16 => 'JSON Error: Malformed UTF-16 characters, possibly incorrectly encoded',
    ];

    /**
     * @var array
     */
    public $sanitizers;

    /**
     * @var array
     */
    public $values;

    public function __construct(array $values = [], array $sanitizers = [])
    {
        $this->sanitizers = $this->buildSanitizers(Arr::consolidate($sanitizers));
        $this->values = Arr::consolidate($values);
    }

    /**
     * @return array|bool|string
     */
    public function run()
    {
        $result = $this->values;
        foreach ($this->values as $key => $value) {
            if (array_key_exists($key, $this->sanitizers)) {
                $result[$key] = call_user_func([$this, $this->sanitizers[$key]], $value);
            }
        }
        return $result;
    }

    /**
     * @param mixed $value
     * @return array
     */
    public function sanitizeArray($value)
    {
        return Arr::consolidate($value);
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    public function sanitizeArrayInt($value)
    {
        return Arr::uniqueInt(Cast::toArray($value));
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    public function sanitizeArrayString($value)
    {
        return array_filter(Cast::toArray($value), 'is_string');
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function sanitizeBool($value)
    {
        return Cast::toBool($value);
    }

    /**
     * If date is invalid then return an empty string.
     * @param mixed $value
     * @param string $fallback
     * @return string
     */
    public function sanitizeDate($value, $fallback = '')
    {
        $date = strtotime(trim(Cast::toString($value)));
        if (false !== $date) {
            return wp_date('Y-m-d H:i:s', $date);
        }
        return $fallback;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeEmail($value)
    {
        return sanitize_email(trim(Cast::toString($value)));
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeId($value)
    {
        require_once ABSPATH.WPINC.'/pluggable.php';
        $value = $this->sanitizeSlug($value);
        if (empty($value)) {
            $value = glsr()->prefix.substr(wp_hash(serialize($this->values), 'nonce'), -12, 8);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return int
     */
    public function sanitizeInt($value)
    {
        return Cast::toInt($value);
    }

    /**
     * @param mixed $value
     * @return array
     */
    public function sanitizeJson($value)
    {
        $result = '';
        if (is_scalar($value) && !Helper::isEmpty($value)) {
            $result = trim((string) $value);
            $result = htmlspecialchars_decode($result);
            $result = json_decode($result, true);
            $error = json_last_error();
            if (array_key_exists($error, static::JSON_ERROR_CODES)) {
                glsr_log()->error(static::JSON_ERROR_CODES[$error])->debug($value);
            }
        }
        return wp_unslash(Arr::consolidate($result));
    }

    /**
     * This allows lowercase alphannumeric and underscore characters
     * @param mixed $value
     * @return string
     */
    public function sanitizeKey($value)
    {
        return Str::snakeCase(sanitize_key($this->sanitizeText($value)));
    }

    /**
     * This allows lowercase alpha and underscore characters
     * @param mixed $value
     * @return string
     */
    public function sanitizeName($value)
    {
        $value = Str::snakeCase($this->sanitizeText($value));
        return preg_replace('/[^a-z_]/', '', $value);
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    public function sanitizePostIds($value)
    {
        $postIds = Cast::toArray($value);
        $postIds = array_map('\GeminiLabs\SiteReviews\Helper::getPostId', $postIds);
        return Arr::uniqueInt($postIds);
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    public function sanitizeTermIds($value)
    {
        $termIds = Cast::toArray($value);
        $termIds = array_map('\GeminiLabs\SiteReviews\Helper::getTermTaxonomyId', $termIds);
        return Arr::uniqueInt($termIds);
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    public function sanitizeUserIds($value)
    {
        $userIds = Cast::toArray($value);
        $userIds = array_map('\GeminiLabs\SiteReviews\Helper::getUserId', $userIds);
        return Arr::uniqueInt($userIds);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeSlug($value)
    {
        return sanitize_title($this->sanitizeText($value));
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeText($value)
    {
        return sanitize_text_field(trim(Cast::toString($value)));
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeTextHtml($value)
    {
        $allowedHtmlPost = wp_kses_allowed_html('post');
        $allowedHtml = [
            'a' => glsr_get($allowedHtmlPost, 'a'),
            'em' => glsr_get($allowedHtmlPost, 'em'),
            'strong' => glsr_get($allowedHtmlPost, 'strong'),
        ];
        $allowedHtml = glsr()->filterArray('sanitize/allowed-html', $allowedHtml, $this);
        return wp_kses(trim(Cast::toString($value)), $allowedHtml);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeTextMultiline($value)
    {
        return sanitize_textarea_field(trim(Cast::toString($value)));
    }

    /**
     * Returns slashed data!
     * @param mixed $value
     * @return string
     */
    public function sanitizeTextPost($value)
    {
        return wp_filter_post_kses(trim(Cast::toString($value)));
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeUrl($value)
    {
        $value = trim(Cast::toString($value));
        if (!Str::startsWith('http://, https://', $value)) {
            $value = Str::prefix($value, 'https://');
        }
        $url = esc_url_raw($value);
        if (mb_strtolower($value) === mb_strtolower($url) && filter_var($url, FILTER_VALIDATE_URL) !== false) {
            return $url;
        }
        return '';
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeUserEmail($value)
    {
        $user = wp_get_current_user();
        $value = $this->sanitizeEmail($value);
        if ($user->exists() && !glsr()->retrieveAs('bool', 'import', false)) {
            return Helper::ifEmpty($value, $user->user_email);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeUserName($value)
    {
        $user = wp_get_current_user();
        $value = $this->sanitizeText($value);
        if ($user->exists() && !glsr()->retrieveAs('bool', 'import', false)) {
            return Helper::ifEmpty($value, $user->display_name);
        }
        return $value;
    }

    /**
     * @return array
     */
    protected function buildSanitizers(array $sanitizers)
    {
        foreach ($sanitizers as $key => &$type) {
            $method = Helper::buildMethodName($type, 'sanitize');
            $type = method_exists($this, $method)
                ? $method
                : 'sanitizeText';
        }
        return $sanitizers;
    }
}
