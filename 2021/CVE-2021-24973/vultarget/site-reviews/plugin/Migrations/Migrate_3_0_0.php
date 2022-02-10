<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Migrate_3_0_0
{
    const MAPPED_SETTINGS = [
        'settings.general.notification' => 'settings.general.notifications', // array
        'settings.general.notification_email' => 'settings.general.notification_email',
        'settings.general.notification_message' => 'settings.general.notification_message',
        'settings.general.require.approval' => 'settings.general.require.approval',
        'settings.general.require.login' => 'settings.general.require.login',
        'settings.general.require.login_register' => 'settings.general.require.login_register',
        'settings.general.webhook_url' => 'settings.general.notification_slack',
        'settings.reviews-form.akismet' => 'settings.submissions.akismet',
        'settings.reviews-form.blacklist.action' => 'settings.submissions.blacklist.action',
        'settings.reviews-form.blacklist.entries' => 'settings.submissions.blacklist.entries',
        'settings.reviews-form.recaptcha.integration' => 'settings.submissions.recaptcha.integration',
        'settings.reviews-form.recaptcha.key' => 'settings.submissions.recaptcha.key',
        'settings.reviews-form.recaptcha.position' => 'settings.submissions.recaptcha.position',
        'settings.reviews-form.recaptcha.secret' => 'settings.submissions.recaptcha.secret',
        'settings.reviews-form.required' => 'settings.submissions.required', // array
        'settings.reviews.assigned_links.enabled' => 'settings.reviews.assigned_links',
        'settings.reviews.avatars.enabled' => 'settings.reviews.avatars',
        'settings.reviews.date.custom' => 'settings.reviews.date.custom',
        'settings.reviews.date.format' => 'settings.reviews.date.format',
        'settings.reviews.excerpt.enabled' => 'settings.reviews.excerpts',
        'settings.reviews.excerpt.length' => 'settings.reviews.excerpts_length',
        'settings.reviews.schema.address' => 'settings.schema.address',
        'settings.reviews.schema.description.custom' => 'settings.schema.description.custom',
        'settings.reviews.schema.description.default' => 'settings.schema.description.default',
        'settings.reviews.schema.highprice' => 'settings.schema.highprice',
        'settings.reviews.schema.image.custom' => 'settings.schema.image.custom',
        'settings.reviews.schema.image.default' => 'settings.schema.image.default',
        'settings.reviews.schema.lowprice' => 'settings.schema.lowprice',
        'settings.reviews.schema.name.custom' => 'settings.schema.name.custom',
        'settings.reviews.schema.name.default' => 'settings.schema.name.default',
        'settings.reviews.schema.pricecurrency' => 'settings.schema.pricecurrency',
        'settings.reviews.schema.pricerange' => 'settings.schema.pricerange',
        'settings.reviews.schema.telephone' => 'settings.schema.telephone',
        'settings.reviews.schema.type.custom' => 'settings.schema.type.custom',
        'settings.reviews.schema.type.default' => 'settings.schema.type.default',
        'settings.reviews.schema.url.custom' => 'settings.schema.url.custom',
        'settings.reviews.schema.url.default' => 'settings.schema.url.default',
        'version' => 'version_upgraded_from',
    ];

    /**
     * @var array
     */
    protected $newSettings;

    /**
     * @var array
     */
    protected $oldSettings;

    /**
     * @return void
     */
    public function migrateSettings()
    {
        $this->newSettings = $this->getNewSettings();
        $this->oldSettings = $this->getOldSettings();
        if (empty($this->oldSettings) || empty($this->newSettings)) {
            return;
        }
        $this->mapSettings();
        $this->migrateNotificationSettings();
        $this->migrateRecaptchaSettings();
        $this->migrateRequiredSettings();
        $oldSettings = Arr::convertFromDotNotation($this->oldSettings);
        $newSettings = Arr::convertFromDotNotation($this->newSettings);
        if (isset($oldSettings['settings']['strings']) && is_array($oldSettings['settings']['strings'])) {
            $newSettings['settings']['strings'] = $oldSettings['settings']['strings'];
        }
        update_option(OptionManager::databaseKey(3), $newSettings);
    }

    /**
     * @return bool
     */
    public function run()
    {
        $this->migrateSettings();
        return true;
    }

    /**
     * @return array
     */
    protected function getNewSettings()
    {
        return Arr::flatten(Arr::consolidate(OptionManager::databaseKey(3)));
    }

    /**
     * @return array
     */
    protected function getOldSettings()
    {
        $defaults = array_fill_keys(array_keys(static::MAPPED_SETTINGS), '');
        $settings = Arr::flatten(Arr::consolidate(get_option(OptionManager::databaseKey(2))));
        return !empty($settings)
            ? wp_parse_args($settings, $defaults)
            : [];
    }

    /**
     * @return void
     */
    public function mapSettings()
    {
        foreach (static::MAPPED_SETTINGS as $old => $new) {
            if (!empty($this->oldSettings[$old])) {
                $this->newSettings[$new] = $this->oldSettings[$old];
            }
        }
    }

    /**
     * @return void
     */
    protected function migrateNotificationSettings()
    {
        $notifications = [
            'custom' => 'custom',
            'default' => 'admin',
            'webhook' => 'slack',
        ];
        $this->newSettings['settings.general.notifications'] = [];
        foreach ($notifications as $old => $new) {
            if ($this->oldSettings['settings.general.notification'] != $old) {
                continue;
            }
            $this->newSettings['settings.general.notifications'][] = $new;
        }
    }

    /**
     * @return void
     */
    protected function migrateRecaptchaSettings()
    {
        $recaptcha = [
            'BadgePosition' => $this->oldSettings['settings.reviews-form.recaptcha.position'],
            'SecretKey' => $this->oldSettings['settings.reviews-form.recaptcha.secret'],
            'SiteKey' => $this->oldSettings['settings.reviews-form.recaptcha.key'],
        ];
        if (in_array($this->oldSettings['settings.reviews-form.recaptcha.integration'], ['custom', 'invisible-recaptcha'])) {
            $this->newSettings['settings.submissions.recaptcha.integration'] = 'all';
        }
        if ('invisible-recaptcha' == $this->oldSettings['settings.reviews-form.recaptcha.integration']) {
            $recaptcha = wp_parse_args((array) get_site_option('ic-settings', [], false), $recaptcha);
        }
        $this->newSettings['settings.submissions.recaptcha.key'] = $recaptcha['SiteKey'];
        $this->newSettings['settings.submissions.recaptcha.secret'] = $recaptcha['SecretKey'];
        $this->newSettings['settings.submissions.recaptcha.position'] = $recaptcha['BadgePosition'];
    }

    /**
     * @return void
     */
    protected function migrateRequiredSettings()
    {
        $this->newSettings['settings.submissions.required'] = array_filter((array) $this->oldSettings['settings.reviews-form.required']);
        $this->newSettings['settings.submissions.required'][] = 'rating';
        $this->newSettings['settings.submissions.required'][] = 'terms';
    }
}
