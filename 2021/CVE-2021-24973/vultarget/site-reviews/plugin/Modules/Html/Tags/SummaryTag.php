<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryTag extends Tag
{
    /**
     * @var array
     */
    protected $ratings;

    /**
     * @return string
     */
    protected function hideOption()
    {
        $mappedTags = [
            'percentages' => 'bars',
            'text' => 'summary',
        ];
        return Arr::get($mappedTags, $this->tag, $this->tag);
    }

    /**
     * @param mixed $with
     * @return bool
     */
    protected function validate($with)
    {
        if (Arr::isIndexedAndFlat($with) && $with === array_filter($with, 'is_numeric')) {
            if (empty($with)) {
                $with = glsr(Rating::class)->emptyArray();
            }
            $this->ratings = $with;
            return true;
        }
        return false;
    }
}
