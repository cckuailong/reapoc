<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Review;

interface ColumnValueContract
{
    /**
     * @return string|void
     */
    public function handle(Review $review);
}
