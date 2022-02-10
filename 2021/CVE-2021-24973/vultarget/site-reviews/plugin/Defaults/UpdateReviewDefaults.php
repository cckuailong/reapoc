<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class UpdateReviewDefaults extends Defaults
{
    /**
     * @var array
     */
    public $mapped = [
        'author' => 'name',
        'post_ids' => 'assigned_posts',
        'term_ids' => 'assigned_terms',
        'user_ids' => 'assigned_users',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'assigned_posts' => 'array-int',
        'assigned_terms' => 'array-int',
        'assigned_users' => 'array-int',
        'author_id' => 'int',
        'date' => 'date',
        'date_gmt' => 'date',
        'is_pinned' => 'bool',
        'rating' => 'int',
        'terms' => 'bool',
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
            'name' => '',
            'author_id' => '',
            'avatar' => '',
            'content' => '',
            'custom' => '',
            'date' => '',
            'date_gmt' => '',
            'email' => '',
            'ip_address' => '',
            'is_pinned' => '',
            'rating' => '',
            'response' => '',
            'status' => '',
            'terms' => '',
            'title' => '',
            'type' => '',
            'url' => '',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        $values = $this->normalizeStatus($values);
        return $values;
    }

    /**
     * @return array
     */
    protected function normalizeStatus(array $values)
    {
        $mapped = [
            'approved' => 'publish',
            'pending' => 'pending',
            'publish' => 'publish',
            'unapproved' => 'pending',
        ];
        $status = Str::restrictTo(array_keys($mapped), Arr::get($values, 'status'));
        if (isset($mapped[$status])) {
            $status = $mapped[$status];
        }
        $values['status'] = $status;
        return $values;
    }
}
