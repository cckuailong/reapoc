<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1\Schema;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryParameters
{
    public function parameters()
    {
        return [
            'after' => [
                'description' => _x('Limit result set to reviews published after a given ISO8601 compliant date.', 'admin-text', 'site-reviews'),
                'format' => 'date-time',
                'type' => 'string',
            ],
            'assigned_posts' => [
                'default' => [],
                'description' => _x('Limit result set to reviews assigned to specific posts of any public post type.', 'admin-text', 'site-reviews'),
                'items'  => [
                    'type' => ['integer', 'string'],
                ],
                'type' => 'array',
            ],
            'assigned_terms' => [
                'default' => [],
                'description' => sprintf(_x('Limit result set to reviews assigned to specific terms in the %s taxonomy.', 'admin-text', 'site-reviews'), glsr()->taxonomy),
                'items'  => [
                    'type' => ['integer', 'string'],
                ],
                'type' => 'array',
            ],
            'assigned_users' => [
                'default' => [],
                'description' => _x('Limit result set to reviews assigned to specific users.', 'admin-text', 'site-reviews'),
                'items'  => [
                    'type' => ['integer', 'string'],
                ],
                'type' => 'array',
            ],
            'before' => [
                'description' => _x('Limit result set to reviews published before a given ISO8601 compliant date.', 'admin-text', 'site-reviews'),
                'format' => 'date-time',
                'type' => 'string',
            ],
            'date' => [
                'description' => _x('Limit result set to reviews published on a given ISO8601 compliant date.', 'admin-text', 'site-reviews'),
                'format' => 'date-time',
                'type' => 'string',
            ],
            'email' => [
                'description' => _x('Limit result set to reviews containing a given email address.', 'admin-text', 'site-reviews'),
                'format' => 'email',
                'type' => 'string',
            ],
            'exclude' => [
                'default' => [],
                'description' => _x('Ensure result set excludes specific review IDs.', 'admin-text', 'site-reviews'),
                'items' => ['type' => 'integer'],
                'type' => 'array',
            ],
            'include' => [
                'default' => [],
                'description' => _x('Limit result set to specific review IDs.', 'admin-text', 'site-reviews'),
                'items'  => ['type' => 'integer'],
                'type' => 'array',
            ],
            'ip_address' => [
                'description' => _x('Limit result set to reviews submitted from a given IP address.', 'admin-text', 'site-reviews'),
                'format' => 'ip',
                'type' => 'string',
            ],
            'rating' => [
                'description' => _x('Limit result set to reviews containing a given minimum rating.', 'admin-text', 'site-reviews'),
                'maximum' => Cast::toInt(glsr()->constant('MAX_RATING', Rating::class)),
                'minimum' => 0,
                'sanitize_callback' => 'absint',
                'type' => 'integer',
            ],
            'status' => [
                'description' => _x('Limit result set to reviews containing a given status.', 'admin-text', 'site-reviews'),
                'enum' => ['all', 'approved', 'pending', 'publish', 'unapproved'],
                'type' => 'string',
            ],
            'terms' => [
                'description' => _x('Limit result set to reviews submitted with terms accepted.', 'admin-text', 'site-reviews'),
                'type' => 'boolean',
            ],
            'type' => [
                'description' => _x('Limit result set to reviews containing a given review type.', 'admin-text', 'site-reviews'),
                'enum' => glsr()->retrieveAs('array', 'review_types', []),
                'type' => 'string',
            ],
            'user__in' => [
                'default' => [],
                'description' => _x('Limit result set to reviews authored by specific users.', 'admin-text', 'site-reviews'),
                'items'  => [
                    'type' => ['integer', 'string'],
                ],
                'type' => 'array',
            ],
            'user__not_in' => [
                'default' => [],
                'description' => _x('Ensure result set excludes reviews authored by specific users.', 'admin-text', 'site-reviews'),
                'items'  => [
                    'type' => ['integer', 'string'],
                ],
                'type' => 'array',
            ],
        ];
    }
}
