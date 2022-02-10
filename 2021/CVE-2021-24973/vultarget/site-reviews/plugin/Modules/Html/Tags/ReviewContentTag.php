<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Text;

class ReviewContentTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            return $this->wrap($this->textExcerpt($value), 'p');
        }
    }

    protected function textExcerpt($value)
    {
        $useExcerpts = glsr_get_option('reviews.excerpts', false, 'bool');
        if ($this->isRaw() || !$useExcerpts) {
            return Text::text($value);
        }
        $limit = Cast::toInt(glsr_get_option('reviews.excerpts_length', 55));
        return Text::excerpt($value, $limit);
    }
}
