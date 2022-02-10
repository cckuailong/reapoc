<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Migrate;

class OptionManager
{
    /**
     * @return array
     */
    public function all()
    {
        if ($settings = Arr::consolidate(glsr()->retrieve('settings'))) {
            return $settings;
        }
        return $this->reset();
    }

    /**
     * @param int $version
     * @return string
     */
    public static function databaseKey($version = null)
    {
        if (1 == $version) {
            return 'geminilabs_site_reviews_settings';
        }
        if (2 == $version) {
            return 'geminilabs_site_reviews-v2';
        }
        if (null === $version) {
            $version = glsr()->version('major');
        }
        return Str::snakeCase(glsr()->id.'-v'.intval($version));
    }

    /**
     * @param string $path
     * @return bool
     */
    public function delete($path)
    {
        return $this->set(Arr::remove($this->all(), $path));
    }

    /**
     * @param string $path
     * @param mixed $fallback
     * @param string $cast
     * @return mixed
     */
    public function get($path = '', $fallback = '', $cast = '')
    {
        $option = Arr::get($this->all(), $path, $fallback);
        $path = ltrim(Str::removePrefix($path, 'settings'), '.');
        if (!empty($path)) {
            $hook = 'option/'.str_replace('.', '/', $path);
            $option = glsr()->filter($hook, $option);
        }
        return Cast::to($cast, $option);
    }

    /**
     * @param string $path
     * @param array $fallback
     * @return array
     */
    public function getArray($path, $fallback = [])
    {
        return $this->get($path, $fallback, 'array');
    }

    /**
     * @param string $path
     * @param string|int|bool $fallback
     * @return bool
     */
    public function getBool($path, $fallback = false)
    {
        return $this->get($path, $fallback, 'bool');
    }

    /**
     * @param string $path
     * @param int $fallback
     * @return int
     */
    public function getInt($path, $fallback = 0)
    {
        return $this->get($path, $fallback, 'int');
    }

    /**
     * @param string $path
     * @param mixed $fallback
     * @param string $cast
     * @return mixed
     */
    public function getWP($path, $fallback = '', $cast = '')
    {
        $option = get_option($path, $fallback);
        return Cast::to($cast, Helper::ifEmpty($option, $fallback, $strict = true));
    }

    /**
     * @return bool
     */
    public function isRecaptchaEnabled()
    {
        $integration = $this->get('settings.submissions.recaptcha.integration');
        return 'all' == $integration || ('guest' == $integration && !is_user_logged_in());
    }

    /**
     * @return string
     */
    public function json()
    {
        return json_encode($this->all(), JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }

    /**
     * Restricts the provided settings keys to the defaults
     * @return array
     */
    public function normalize(array $settings = [])
    {
        $settings = shortcode_atts(glsr(DefaultsManager::class)->defaults(), Arr::flatten($settings));
        array_walk($settings, function (&$value) {
            if (is_string($value)) {
                $value = wp_kses($value, wp_kses_allowed_html('post'));
            }
        });
        return Arr::convertFromDotNotation($settings);
    }

    /**
     * @return array
     */
    public function reset()
    {
        $settings = Arr::consolidate($this->getWP(static::databaseKey(), []));
        if (empty($settings)) {
            delete_option(static::databaseKey());
            $settings = Arr::consolidate(glsr()->defaults);
            glsr(Migrate::class)->reset(); // Do this to migrate any previous version settings
        }
        glsr()->store('settings', $settings);
        return $settings;
    }

    /**
     * @param string|array $pathOrArray
     * @param mixed $value
     * @return bool
     */
    public function set($pathOrArray, $value = '')
    {
        if (is_string($pathOrArray)) {
            $pathOrArray = Arr::set($this->all(), $pathOrArray, $value);
        }
        if ($settings = Arr::consolidate($pathOrArray)) {
            $result = update_option(static::databaseKey(), $settings);
        }
        if (!empty($result)) {
            $this->reset();
            return true;
        }
        return false;
    }
}
