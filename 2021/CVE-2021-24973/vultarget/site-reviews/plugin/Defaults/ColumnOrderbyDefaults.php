<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ColumnOrderbyDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'author_email' => 'email',
            'author_name' => 'name',
            'ip_address' => 'ip_address',
            'is_pinned' => 'is_pinned',
            'rating' => 'rating',
            'type' => 'type',
        ];
    }
}
