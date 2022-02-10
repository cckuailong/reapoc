<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * A rating is an evaluation on a numeric scale, such as 1 to 5 stars.
 * @see http://schema.org/Rating
 * @method static author(Organization|Organization[]|Person|Person[] $author)
 * @method static bestRating(float|float[]|int|int[]|string|string[] $bestRating)
 * @method static ratingValue(float|float[]|int|int[]|string|string[] $ratingValue)
 * @method static worstRating(float|float[]|int|int[]|string|string[] $worstRating)
 */
class Rating extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'author', 'bestRating', 'ratingValue', 'worstRating',
    ];

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'Intangible',
    ];
}
