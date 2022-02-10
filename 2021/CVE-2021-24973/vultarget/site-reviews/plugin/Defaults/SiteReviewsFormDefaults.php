<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class SiteReviewsFormDefaults extends Defaults
{
    /**
     * @var array
     */
    public $guarded = [
        'description',
        'title',
    ];

    /**
     * @var array
     */
    public $mapped = [
        'assign_to' => 'assigned_posts',
        'category' => 'assigned_terms',
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
            'description' => '',
            'excluded' => '',
            'hide' => '',
            'id' => '',
            'title' => '',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        foreach ($this->mapped as $old => $new) {
            $value = Helper::ifTrue('assign_to' === $old, 'custom', 'glsr_custom');
            if ($value === Arr::get($values, $old)) {
                $values[$old] = Arr::get($values, $new);
            }
        }
        return parent::normalize($values);
    }
}
