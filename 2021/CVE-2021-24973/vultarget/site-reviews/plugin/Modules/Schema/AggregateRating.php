<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * The average rating based on multiple ratings or reviews.
 * @see http://schema.org/AggregateRating
 * @method static itemReviewed(Thing|Thing[] $itemReviewed)
 * @method static ratingCount(int|int[] $ratingCount)
 * @method static reviewCount(int|int[] $reviewCount)
 */
class AggregateRating extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'itemReviewed', 'ratingCount', 'reviewCount',
    ];

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'Rating',
    ];
}
