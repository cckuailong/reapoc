<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PostStatusLabelsDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'Pending' => _x('Unapproved', 'admin-text', 'site-reviews'),
            'Pending Review' => _x('Unapproved', 'admin-text', 'site-reviews'),
            'Privately Published' => _x('Privately Approved', 'admin-text', 'site-reviews'),
            'Publish' => _x('Approve', 'admin-text', 'site-reviews'),
            'Published' => _x('Approved', 'admin-text', 'site-reviews'),
            'Save as Pending' => _x('Save as Unapproved', 'admin-text', 'site-reviews'),
        ];
    }
}
