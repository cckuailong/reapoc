<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;

class ReviewAssignedTermsTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $terms = wp_list_pluck($this->review->assignedTerms(), 'name');
        $tagValue = Str::naturalJoin($terms);
        return $this->wrap($tagValue, 'span');
    }
}
