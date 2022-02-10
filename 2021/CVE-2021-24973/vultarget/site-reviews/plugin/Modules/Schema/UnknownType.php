<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

class UnknownType extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'aggregateRating',
    ];

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'Thing',
    ];
}
