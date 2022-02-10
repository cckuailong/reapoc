<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewDateTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            return $this->wrap($this->review->date(), 'span');
        }
    }
}
