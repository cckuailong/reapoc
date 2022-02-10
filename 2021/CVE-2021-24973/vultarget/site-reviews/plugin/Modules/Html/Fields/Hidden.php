<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Hidden extends Field
{
    /**
     * {@inheritdoc}
     */
    public static function required($fieldLocation = null)
    {
        return [
            'is_raw' => true,
        ];
    }
}
