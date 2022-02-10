<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Number extends Field
{
    /**
     * {@inheritdoc}
     */
    public static function defaults($fieldLocation = null)
    {
        $classes = [
            'metabox' => '',
            'setting' => 'small-text',
            'widget' => 'small-text',
        ];
        return [
            'class' => Arr::get($classes, $fieldLocation),
        ];
    }
}
