<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * A particular physical business or branch of an organization. Examples of LocalBusiness include a
 * restaurant, a particular branch of a restaurant chain, a branch of a bank, a medical practice, a
 * club, a bowling alley, etc.
 * @see http://schema.org/LocalBusiness
 * @method static branchOf(Organization|Organization[] $branchOf)
 * @method static currenciesAccepted(string|string[] $currenciesAccepted)
 * @method static openingHours(string|string[] $openingHours)
 * @method static paymentAccepted(string|string[] $paymentAccepted)
 * @method static priceRange(string|string[] $priceRange)
 */
class LocalBusiness extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'branchOf', 'currenciesAccepted', 'openingHours', 'paymentAccepted', 'priceRange',
    ];

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'Organization', 'Place',
    ];
}
