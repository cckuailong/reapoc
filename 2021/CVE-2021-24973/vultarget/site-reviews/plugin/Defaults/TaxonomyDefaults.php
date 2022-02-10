<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestCategoryController;
use GeminiLabs\SiteReviews\Controllers\MetaboxController;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class TaxonomyDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'hierarchical' => true,
            'meta_box_cb' => [glsr(MetaboxController::class), 'renderTaxonomyMetabox'],
            'public' => false,
            'rest_controller_class' => RestCategoryController::class,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'show_ui' => true,
        ];
    }
}
