<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helpers\Arr;

class DefaultsManager
{
    /**
     * @return array
     */
    public function defaults()
    {
        $settings = $this->settings();
        $defaults = (array) array_combine(array_keys($settings), wp_list_pluck($settings, 'default'));
        return wp_parse_args($defaults, [
            'version' => '',
            'version_upgraded_from' => '0.0.0',
        ]);
    }

    /**
     * @return array
     */
    public function get()
    {
        return Arr::convertFromDotNotation($this->defaults());
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function pluck($path)
    {
        $settings = Arr::convertFromDotNotation($this->settings());
        return Arr::get($settings, $path);
    }

    /**
     * @return array
     */
    public function set()
    {
        $settings = glsr(OptionManager::class)->all();
        $currentSettings = Arr::removeEmptyValues($settings);
        $defaultSettings = array_replace_recursive($this->get(), $currentSettings);
        $updatedSettings = array_replace_recursive($settings, $defaultSettings);
        update_option(OptionManager::databaseKey(), $updatedSettings);
        return $defaultSettings;
    }

    /**
     * @return array
     */
    public function settings()
    {
        static $settings;
        if (empty($settings)) {
            $settings = glsr()->filterArray('addon/settings', glsr()->config('settings'));
            $settings = $this->normalize($settings);
        }
        return $settings;
    }

    /**
     * @return array
     */
    protected function normalize(array $settings)
    {
        array_walk($settings, function (&$setting) {
            if (isset($setting['default'])) {
                return;
            }
            $setting['default'] = '';
        });
        return $settings;
    }
}
