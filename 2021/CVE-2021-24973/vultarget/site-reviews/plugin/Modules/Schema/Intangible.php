<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * A utility class that serves as the umbrella for a number of 'intangible' things such as
 * quantities, structured values, etc.
 * @see http://schema.org/Intangible
 */
class Intangible extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'Thing',
    ];
}
