<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Text extends Field
{
    /**
     * {@inheritdoc}
     */
    public static function defaults($fieldLocation = null)
    {
        $classes = [
            'metabox' => '',
            'setting' => 'regular-text',
            'widget' => 'widefat',
        ];
        return [
            'class' => Arr::get($classes, $fieldLocation),
        ];
    }
}
