<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PostTypeColumnDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'cb' => '',
            'title' => '',
            'category' => '',
            'assigned_posts' => _x('Assigned Posts', 'siteadmin-text', 'site-reviews'),
            'assigned_users' => _x('Assigned Users', 'admin-text', 'site-reviews'),
            'author_name' => _x('Name', 'admin-text', 'site-reviews'),
            'author_email' => _x('Email', 'admin-text', 'site-reviews'),
            'ip_address' => _x('IP Address', 'admin-text', 'site-reviews'),
            'response' => _x('Response', 'admin-text', 'site-reviews'),
            'type' => _x('Type', 'admin-text', 'site-reviews'),
            'rating' => _x('Rating', 'admin-text', 'site-reviews'),
            'is_pinned' => _x('Pinned', 'admin-text', 'site-reviews'),
            'date' => '',
        ];
    }
}
