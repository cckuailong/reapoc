<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewRatingTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            $stars = glsr_star_rating($value, 0, $this->args->toArray());
            return $this->wrap($stars);
        }
    }
}
