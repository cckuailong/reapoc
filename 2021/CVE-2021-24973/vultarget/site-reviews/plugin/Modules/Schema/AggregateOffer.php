<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * When a single product is associated with multiple offers (for example, the same pair of shoes is
 * offered by different merchants), then AggregateOffer can be used.
 * @see http://schema.org/AggregateOffer
 * @method static highPrice(float|float[]|int|int[]|string|string[] $highPrice)
 * @method static lowPrice(float|float[]|int|int[]|string|string[] $lowPrice)
 * @method static offerCount(int|int[] $offerCount)
 * @method static offers(Offer|Offer[] $offers)
 */
class AggregateOffer extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'highPrice', 'lowPrice', 'offerCount', 'offers',
    ];

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'Offer',
    ];
}
