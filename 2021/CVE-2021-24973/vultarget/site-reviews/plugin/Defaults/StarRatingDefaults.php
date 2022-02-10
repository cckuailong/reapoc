<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Arr;

class StarRatingDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'args' => 'array',
        'count' => 'int',
        'prefix' => 'string',
        'rating' => 'float',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'args' => [],
            'count' => 0,
            'prefix' => glsr()->isAdmin() ? '' : 'glsr-',
            'rating' => 0,
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        $values['rating'] = sprintf('%g', Arr::get($values,'rating', 0));
        return $values;
    }
}
