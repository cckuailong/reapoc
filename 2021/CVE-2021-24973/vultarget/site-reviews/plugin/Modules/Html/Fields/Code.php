<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Code extends Field
{
    /**
     * {@inheritdoc}
     */
    public static function defaults($fieldLocation = null)
    {
        $classes = [
            'metabox' => '',
            'setting' => 'large-text code',
            'widget' => '',
        ];
        return [
            'class' => Arr::get($classes, $fieldLocation),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function required($fieldLocation = null)
    {
        return [
            'type' => 'textarea',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function tag()
    {
        return 'textarea';
    }
}
