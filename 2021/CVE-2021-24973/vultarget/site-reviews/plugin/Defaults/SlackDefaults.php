<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class SlackDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'button_text' => __('View Review', 'site-reviews'),
            'button_url' => '',
            'color' => '#665068',
            'fallback' => '',
            'icon_url' => glsr()->url('assets/images/icon.png'),
            'pretext' => '',
            'username' => glsr()->name,
        ];
    }
}
