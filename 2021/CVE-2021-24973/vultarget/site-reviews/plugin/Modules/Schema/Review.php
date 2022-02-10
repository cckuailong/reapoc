<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * A review of an item - for example, of a restaurant, movie, or store.
 * @see http://schema.org/Review
 * @method static itemReviewed(Thing|Thing[] $itemReviewed)
 * @method static reviewBody(string|string[] $reviewBody)
 * @method static reviewRating(Rating|Rating[] $reviewRating)
 */
class Review extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'itemReviewed', 'reviewBody', 'reviewRating',
    ];

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'CreativeWork',
    ];
}
