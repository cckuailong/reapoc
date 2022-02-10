<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryStarsTag extends SummaryTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            $value = glsr(Rating::class)->average($this->ratings);
            $rating = glsr_star_rating($value, 0, $this->args->toArray());
            return $this->wrap($rating);
        }
    }
}
