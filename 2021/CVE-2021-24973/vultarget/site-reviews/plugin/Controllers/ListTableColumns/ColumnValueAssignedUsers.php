<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnValueContract;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;

class ColumnValueAssignedUsers implements ColumnValueContract
{
    /**
     * {@inheritdoc}
     */
    public function handle(Review $review)
    {
        $links = [];
        foreach ($review->assigned_users as $userId) {
            $links[] = glsr(Builder::class)->a([
                'href' => esc_url(get_author_posts_url($userId)),
                'text' => esc_attr(get_the_author_meta('display_name', $userId)),
            ]);
        }
        return implode(', ', $links);
    }
}
