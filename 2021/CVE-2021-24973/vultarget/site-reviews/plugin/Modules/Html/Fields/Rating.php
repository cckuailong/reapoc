<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Rating as RatingModule;

class Rating extends Field
{
    /**
     * {@inheritdoc}
     */
    public static function required($fieldLocation = null)
    {
        $options = glsr(RatingModule::class)->optionsArray(
            _n_noop('%s Star', '%s Stars', 'site-reviews')
        );
        return [
            'class' => 'glsr-star-rating',
            'options' => $options,
            'placeholder' => __('Select a Rating', 'site-reviews'),
            'type' => 'select',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function tag()
    {
        return 'select';
    }
}
