<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Radio extends Checkbox
{
    /**
     * {@inheritdoc}
     */
    public static function required($fieldLocation = null)
    {
        return [
            'is_multi' => true,
            'type' => 'radio',
        ];
    }
}
