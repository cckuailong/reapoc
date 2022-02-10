<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterAssignedPost;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterAssignedUser;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterCategory;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterRating;
use GeminiLabs\SiteReviews\Controllers\ListTableColumns\ColumnFilterType;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ReviewTableFiltersDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [ // order is intentional
            'rating' => ColumnFilterRating::class,
            'type' => ColumnFilterType::class,
            'category' => ColumnFilterCategory::class,
            'assigned_post' => ColumnFilterAssignedPost::class,
            'assigned_user' => ColumnFilterAssignedUser::class,
        ];
    }
}
