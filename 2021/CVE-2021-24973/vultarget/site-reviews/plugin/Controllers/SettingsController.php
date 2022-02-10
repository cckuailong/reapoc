<?php

namespace GeminiLabs\SiteReviews\Controllers;

use Exception;
use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Multilingual;
use GeminiLabs\SiteReviews\Modules\Notice;

class SettingsController extends Controller
{
    /**
     * @param mixed $input
     * @return array
     * @callback register_setting
     */
    public function callbackRegisterSettings($input)
    {
        $settings = Arr::consolidate($input);
        if (1 === count($settings) && array_key_exists('settings', $settings)) {
            $options = array_replace_recursive(glsr(OptionManager::class)->all(), $input);
            $options = $this->sanitizeGeneral($input, $options);
            $options = $this->sanitizeLicenses($input, $options);
            $options = $this->sanitizeSubmissions($input, $options);
            $options = $this->sanitizeTranslations($input, $options);
            $options = glsr()->filterArray('settings/callback', $options, $settings);
            if (filter_input(INPUT_POST, 'option_page') == glsr()->id.'-settings') {
                glsr(Notice::class)->addSuccess(_x('Settings updated.', 'admin-text', 'site-reviews'));
            }
            return $options;
        }
        return $input;
    }

    /**
     * @return void
     * @action admin_init
     */
    public function registerSettings()
    {
        register_setting(glsr()->id.'-settings', OptionManager::databaseKey(), [
            'sanitize_callback' => [$this, 'callbackRegisterSettings'],
        ]);
    }

    /**
     * @return array
     */
    protected function sanitizeGeneral(array $input, array $options)
    {
        $key = 'settings.general';
        $inputForm = Arr::get($input, $key);
        if (!$this->hasMultilingualIntegration(Arr::get($inputForm, 'multilingual'))) {
            $options = Arr::set($options, $key.'.multilingual', '');
        }
        if ('' == trim(Arr::get($inputForm, 'notification_message'))) {
            $defaultValue = Arr::get(glsr()->defaults, $key.'.notification_message');
            $options = Arr::set($options, $key.'.notification_message', $defaultValue);
        }
        $defaultValue = Arr::get($inputForm, 'notifications', []);
        $options = Arr::set($options, $key.'.notifications', $defaultValue);
        return $options;
    }

    /**
     * @return array
     */
    protected function sanitizeLicenses(array $input, array $options)
    {
        $key = 'settings.licenses';
        $licenses = Arr::consolidate(Arr::get($input, $key));
        foreach ($licenses as $slug => &$license) {
            if (empty($license)) {
                continue;
            }
            $license = $this->verifyLicense($license, $slug);
        }
        $options = Arr::set($options, $key, $licenses);
        return $options;
    }

    /**
     * @return array
     */
    protected function sanitizeSubmissions(array $input, array $options)
    {
        $key = 'settings.submissions';
        $inputForm = Arr::get($input, $key);
        $multiFields = ['limit_assignments', 'required'];
        foreach ($multiFields as $name) {
            $defaultValue = Arr::get($inputForm, $name, []);
            $options = Arr::set($options, $key.'.'.$name, $defaultValue);
        }
        return $options;
    }

    /**
     * @return array
     */
    protected function sanitizeTranslations(array $input, array $options)
    {
        $key = 'settings.strings';
        $inputForm = Arr::consolidate(Arr::get($input, $key));
        if (!empty($inputForm)) {
            $options = Arr::set($options, $key, array_values(array_filter($inputForm)));
            $allowedTags = [
                'a' => ['class' => [], 'href' => [], 'target' => []],
                'span' => ['class' => []],
            ];
            array_walk($options['settings']['strings'], function (&$string) use ($allowedTags) {
                if (isset($string['s2'])) {
                    $string['s2'] = wp_kses($string['s2'], $allowedTags);
                }
                if (isset($string['p2'])) {
                    $string['p2'] = wp_kses($string['p2'], $allowedTags);
                }
            });
        }
        return $options;
    }

    /**
     * @param string $integrationSlug
     * @return bool
     */
    protected function hasMultilingualIntegration($integrationSlug)
    {
        $integration = glsr(Multilingual::class)->getIntegration($integrationSlug);
        if (!$integration) {
            return false;
        }
        if (!$integration->isActive()) {
            glsr(Notice::class)->addError(sprintf(
                _x('Please install/activate the %s plugin to enable integration.', 'admin-text', 'site-reviews'),
                $integration->pluginName
            ));
            return false;
        } elseif (!$integration->isSupported()) {
            glsr(Notice::class)->addError(sprintf(
                _x('Please update the %s plugin to v%s or greater to enable integration.', 'admin-text', 'site-reviews'),
                $integration->pluginName,
                $integration->supportedVersion
            ));
            return false;
        }
        return true;
    }

    /**
     * @param string $license
     * @param string $slug
     * @return string
     */
    protected function verifyLicense($license, $slug)
    {
        try {
            $addon = glsr($slug); // use addon alias to get the class
            $updater = new Updater($addon->update_url, $addon->file, [
                'license' => $license,
                'testedTo' => $addon->testedTo,
            ]);
            if (!$updater->isLicenseValid()) {
                throw new Exception('Invalid license: '.$license.' ('.$addon->id.')');
            }
        } catch (Exception $e) {
            $license = '';
            glsr_log()->error($e->getMessage());
            $error = _x('The license you entered is either invalid or has not yet been activated.', 'admin-text', 'site-reviews');
            $message = sprintf(_x('To activate your license, please visit the %s page on your Nifty Plugins account and click the "Manage Sites" button to activate it for your website.', 'admin-text', 'site-reviews'),
                sprintf('<a href="https://niftyplugins.com/account/license-keys/" target="_blank">%s</a>', _x('License Keys', 'admin-text', 'site-reviews'))
            );
            glsr(Notice::class)->addError(sprintf('<strong>%s</strong><br>%s', $error, $message));
        }
        return $license;
    }
}
