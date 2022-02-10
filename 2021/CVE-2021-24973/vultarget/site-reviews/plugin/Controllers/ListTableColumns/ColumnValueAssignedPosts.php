<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Modules\Html\Tags\ReviewAssignedLinksTag;
use GeminiLabs\SiteReviews\Review;

class ColumnValueAssignedPosts implements ColumnValueContract
{
    /**
     * {@inheritdoc}
     */
    public function handle(Review $review)
    {
        $links = ReviewAssignedLinksTag::assignedLinks($review->assigned_posts);
        return implode(', ', $links);
    }
}
