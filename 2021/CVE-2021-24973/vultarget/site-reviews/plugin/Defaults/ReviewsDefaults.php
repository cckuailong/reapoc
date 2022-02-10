<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ReviewsDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'ip_address' => 'string',
        'offset' => 'int',
        'order' => 'string',
        'orderby' => 'string',
        'page' => 'int',
        'pagination' => 'string',
        'per_page' => 'int',
        'rating' => 'int',
        'rating_field' => 'string',
        'status' => 'string',
    ];

    /**
     * @var array
     */
    public $mapped = [
        'assigned_to' => 'assigned_posts',
        'author_id' => 'user__in',
        'category' => 'assigned_terms',
        'count' => 'per_page', // @deprecated in v4.1.0
        'display' => 'per_page',
        'exclude' => 'post__not_in',
        'include' => 'post__in',
        'user' => 'assigned_users',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'assigned_posts' => 'post-ids',
        'assigned_terms' => 'term-ids',
        'assigned_users' => 'user-ids',
        'email' => 'email',
        'post__in' => 'array-int',
        'post__not_in' => 'array-int',
        'rating_field' => 'name',
        'type' => 'key',
        'user__in' => 'user-ids',
        'user__not_in' => 'user-ids',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_posts' => '',
            'assigned_terms' => '',
            'assigned_users' => '',
            'date' => '',
            'email' => '',
            'ip_address' => '',
            'offset' => '',
            'order' => 'DESC',
            'orderby' => 'date',
            'page' => 1,
            'pagination' => false,
            'per_page' => 10,
            'post__in' => [],
            'post__not_in' => [],
            'rating' => '',
            'rating_field' => 'rating', // used for custom rating fields
            'status' => 'approved',
            'terms' => '',
            'type' => '',
            'user__in' => [],
            'user__not_in' => [],
        ];
    }
}
