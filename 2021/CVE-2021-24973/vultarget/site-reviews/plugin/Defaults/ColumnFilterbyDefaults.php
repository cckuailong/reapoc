<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ColumnFilterbyDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_post' => FILTER_SANITIZE_NUMBER_INT,
            'assigned_user' => FILTER_SANITIZE_NUMBER_INT,
            'rating' => FILTER_SANITIZE_NUMBER_INT,
            'type' => FILTER_SANITIZE_STRING,
        ];
    }
}
