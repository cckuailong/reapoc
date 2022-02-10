<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ValidateReviewDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assign_to' => '',
            'category' => '',
            'content' => '',
            'email' => '',
            'form_id' => '',
            'ip_address' => '',
            'name' => '',
            'rating' => '0',
            'terms' => '',
            'title' => '',
        ];
    }
}
