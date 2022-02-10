<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class TemplateTagsDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'admin_email' => _x('The admin email from your WordPress settings', 'admin-text', 'site-reviews'),
            'review_assigned_posts' => _x('The review\'s assigned page titles', 'admin-text', 'site-reviews'),
            'review_assigned_users' => _x('The review\'s assigned user display names', 'admin-text', 'site-reviews'),
            'review_author' => _x('The review author', 'admin-text', 'site-reviews'),
            'review_categories' => _x('The review\'s assigned categories', 'admin-text', 'site-reviews'),
            'review_content' => _x('The review content', 'admin-text', 'site-reviews'),
            'review_email' => _x('The email of the review author', 'admin-text', 'site-reviews'),
            'review_ip' => _x('The IP address of the review author', 'admin-text', 'site-reviews'),
            'review_link' => _x('The link to edit/view a review', 'admin-text', 'site-reviews'),
            'review_rating' => _x('The review rating number (1-5)', 'admin-text', 'site-reviews'),
            'review_title' => _x('The review title', 'admin-text', 'site-reviews'),
            'site_title' => _x('The Site Title from your WordPress settings', 'admin-text', 'site-reviews'),
            'site_url' => _x('The Site URL from your WordPress settings', 'admin-text', 'site-reviews'),
        ];
    }
}
