<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PermissionDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'addons' => 'activate_plugins',
            'documentation' => [
                'faq' => 'edit_others_posts',
                'functions' => 'manage_options',
                'hooks' => 'edit_others_posts',
                'index' => 'edit_posts',
                'support' => 'edit_others_posts',
            ],
            'settings' => 'manage_options',
            'tools' => [
                'console' => 'edit_others_posts',
                'general' => 'edit_others_posts',
                'index' => 'edit_posts',
                'sync' => 'manage_options',
                'system-info' => 'edit_posts',
            ],
        ];
    }
}
