<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Translation;

class Settings
{
    /**
     * @var array
     */
    public $settings;

    /**
     * @param string $id
     * @return string
     */
    public function buildFields($id)
    {
        $this->settings = glsr(DefaultsManager::class)->settings();
        $method = Helper::buildMethodName($id, 'getTemplateDataFor');
        $data = !method_exists($this, $method)
            ? $this->getTemplateData($id)
            : $this->$method($id);
        return glsr(Template::class)->build('pages/settings/'.$id, $data);
    }

    /**
     * @return string
     */
    protected function getFieldDefault(array $field)
    {
        return Arr::get($field, 'default');
    }

    /**
     * @return string
     */
    protected function getFieldNameForDependsOn($path)
    {
        $fieldName = Str::convertPathToName($path, OptionManager::databaseKey());
        return $this->isMultiDependency($path)
            ? Str::suffix($fieldName, '[]')
            : $fieldName;
    }

    /**
     * @return array
     */
    protected function getSettingFields($path)
    {
        return array_filter($this->settings, function ($key) use ($path) {
            return Str::startsWith($path, $key);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @return string
     */
    protected function getSettingRows(array $fields)
    {
        $rows = '';
        foreach ($fields as $name => $field) {
            $field = wp_parse_args($field, [
                'name' => $name,
            ]);
            $rows .= new SettingField($this->normalize($field));
        }
        return $rows;
    }

    /**
     * @param string $id
     * @return array
     */
    protected function getTemplateData($id)
    {
        $fields = $this->getSettingFields($this->normalizeSettingPath($id));
        return [
            'context' => [
                'rows' => $this->getSettingRows($fields),
            ],
        ];
    }

    /**
     * @param string $id
     * @return array
     */
    protected function getTemplateDataForAddons($id)
    {
        $fields = $this->getSettingFields($this->normalizeSettingPath($id));
        $settings = Arr::convertFromDotNotation($fields);
        $settingKeys = array_keys($settings['settings']['addons']);
        $results = [];
        foreach ($settingKeys as $key) {
            $addonFields = array_filter($fields, function ($path) use ($key) {
                return Str::startsWith('settings.addons.'.$key, $path);
            }, ARRAY_FILTER_USE_KEY);
            $results[$key] = $this->getSettingRows($addonFields);
        }
        ksort($results);
        return [
            'settings' => $results,
        ];
    }

    /**
     * @param string $id
     * @return array
     */
    protected function getTemplateDataForLicenses($id)
    {
        $fields = $this->getSettingFields($this->normalizeSettingPath($id));
        foreach ($fields as $key => &$field) {
            if (!empty(glsr(OptionManager::class)->get($key))) {
                $field['type'] = 'password'; // mask saved licenses
            }
        }
        ksort($fields);
        return [
            'context' => [
                'rows' => $this->getSettingRows($fields),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getTemplateDataForTranslations()
    {
        $translations = glsr(Translation::class)->renderAll();
        $class = empty($translations)
            ? 'glsr-hidden'
            : '';
        return [
            'context' => [
                'class' => $class,
                'database_key' => OptionManager::databaseKey(),
                'translations' => $translations,
            ],
        ];
    }

    /**
     * @param string $path
     * @param string|array $expectedValue
     * @return bool
     */
    protected function isFieldHidden($path, $expectedValue)
    {
        $optionValue = glsr(OptionManager::class)->get(
            $path,
            Arr::get(glsr()->defaults, $path)
        );
        if (is_array($expectedValue)) {
            return is_array($optionValue)
                ? 0 === count(array_intersect($optionValue, $expectedValue))
                : !in_array($optionValue, $expectedValue);
        }
        return $optionValue != $expectedValue;
    }

    /**
     * @return bool
     */
    protected function isMultiDependency($path)
    {
        if (isset($this->settings[$path])) {
            $field = $this->settings[$path];
            return ('checkbox' == $field['type'] && !empty($field['options']))
                || !empty($field['multiple']);
        }
        return false;
    }

    /**
     * @return array
     */
    protected function normalize(array $field)
    {
        $field = $this->normalizeDependsOn($field);
        $field = $this->normalizeLabelAndLegend($field);
        $field = $this->normalizeValue($field);
        return $field;
    }

    /**
     * @return array
     */
    protected function normalizeDependsOn(array $field)
    {
        if (!empty($field['depends_on']) && is_array($field['depends_on'])) {
            $isFieldHidden = false;
            $conditions = [];
            foreach ($field['depends_on'] as $path => $value) {
                $conditions[] = [
                    'name' => $this->getFieldNameForDependsOn($path),
                    'value' => $value,
                ];
                if ($this->isFieldHidden($path, $value)) {
                    $isFieldHidden = true;
                }
            }
            $field['data-depends'] = json_encode($conditions, JSON_HEX_APOS | JSON_HEX_QUOT);
            $field['is_hidden'] = $isFieldHidden;
        }
        return $field;
    }

    /**
     * @return array
     */
    protected function normalizeLabelAndLegend(array $field)
    {
        if (!empty($field['label'])) {
            $field['legend'] = $field['label'];
            unset($field['label']);
        } else {
            $field['is_valid'] = false;
            glsr_log()->warning('Setting field is missing a label')->debug($field);
        }
        return $field;
    }

    /**
     * @return array
     */
    protected function normalizeValue(array $field)
    {
        if (!isset($field['value'])) {
            $field['value'] = glsr(OptionManager::class)->get(
                $field['name'],
                $this->getFieldDefault($field)
            );
        }
        return $field;
    }

    /**
     * @return string
     */
    protected function normalizeSettingPath($path)
    {
        return Str::prefix(rtrim($path, '.'), 'settings.');
    }
}
