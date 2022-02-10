<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class SiteReviewsDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'display' => 'int',
        'hide' => 'array',
        'page' => 'int',
        'rating' => 'int',
        'schema' => 'bool',
    ];

    /**
     * @var string[]
     */
    public $guarded = [
        'fallback',
        'title',
    ];

    /**
     * @var array
     */
    public $mapped = [
        'assigned_to' => 'assigned_posts',
        'category' => 'assigned_terms',
        'count' => 'display', // @deprecated in v4.1.0
        'per_page' => 'display',
        'user' => 'assigned_users',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'id' => 'id',
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
            'class' => '',
            'display' => 5,
            'fallback' => '',
            'hide' => [],
            'id' => '',
            'offset' => '',
            'page' => 1,
            'pagination' => false,
            'rating' => 0,
            'rating_field' => 'rating', // used for custom rating fields
            'schema' => false,
            'terms' => '',
            'title' => '',
            'type' => 'local',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        foreach ($this->mapped as $old => $new) {
            $value = Helper::ifTrue('assigned_to' === $old, 'custom', 'glsr_custom');
            if ($value === Arr::get($values, $old)) {
                $values[$old] = Arr::get($values, $new);
            }
        }
        return parent::normalize($values);
    }
}
