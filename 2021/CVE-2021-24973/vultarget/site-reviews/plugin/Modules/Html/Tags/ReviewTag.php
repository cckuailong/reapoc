<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Review;

class ReviewTag extends Tag
{
    /**
     * @var \GeminiLabs\SiteReviews\Review
     */
    public $review;

    /**
     * @param mixed $with
     * @return bool
     */
    protected function validate($with)
    {
        if ($with instanceof Review) {
            $this->review = $with;
            return true;
        }
        return false;
    }
}
