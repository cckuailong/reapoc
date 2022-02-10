<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;

class ColumnValueIsPinned implements ColumnValueContract
{
    /**
     * {@inheritdoc}
     */
    public function handle(Review $review)
    {
        $pinned = $review->is_pinned ? 'pinned ' : '';
        if (glsr()->can('edit_others_posts')) {
            $pinned .= 'pin-review ';
        }
        return glsr(Builder::class)->i([
            'class' => $pinned.'dashicons dashicons-sticky',
            'data-id' => $review->ID,
        ]);
    }
}
