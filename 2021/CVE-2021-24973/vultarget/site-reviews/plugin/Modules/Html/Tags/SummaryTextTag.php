<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryTextTag extends SummaryTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            return $this->wrap($this->text(), 'span');
        }
    }

    /**
     * @return string
     */
    protected function text()
    {
        $max = glsr()->constant('MAX_RATING', Rating::class);
        $num = (int) array_sum($this->ratings);
        $rating = glsr(Rating::class)->average($this->ratings);
        $text = $this->args->text;
        if (empty($text)) {
            $text = _nx(
                '{rating} out of {max} stars (based on {num} review)',
                '{rating} out of {max} stars (based on {num} reviews)',
                $num,
                'Do not translate {rating}, {max}, and {num}, they are template tags.',
                'site-reviews'
            );
        }
        return str_replace(['{rating}','{max}','{num}'], [$rating, $max, $num], $text);
    }
}
