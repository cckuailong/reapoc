<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Rating;

class RatingManager
{
    /**
     * @return array
     */
    public function ratings(array $args = [])
    {
        $results = glsr(Query::class)->ratings($args);
        return $this->reduce($results, $args);
    }

    /**
     * @return array
     */
    public function ratingsGroupedBy($metaType, array $args = [])
    {
        $metaTables = [
            'post' => 'postmeta',
            'term' => 'termmeta',
            'user' => 'usermeta',
        ];
        $metaTable = Arr::get($metaTables, Cast::toString($metaType), 'postmeta');
        $ratings = glsr(Query::class)->ratingsFor($metaTable, $args);
        foreach ($ratings as $id => &$results) {
            $results = $this->reduce($results, $args);
        }
        return $ratings;
    }

    /**
     * @return int
     */
    protected function maxRating(array $args)
    {
        return Cast::toInt(Arr::get($args, 'max', glsr()->constant('MAX_RATING', Rating::class)));
    }

    /**
     * @return int
     */
    protected function minRating(array $args)
    {
        return Cast::toInt(Arr::get($args, 'min', glsr()->constant('MIN_RATING', Rating::class)));
    }

    /**
     * Combine ratings grouped by type into a single rating array.
     * @return array
     */
    protected function reduce(array $ratings, array $args = [])
    {
        $max = $this->maxRating($args);
        $min = $this->minRating($args);
        $normalized = [];
        array_walk_recursive($ratings, function ($rating, $index) use (&$normalized) {
            $normalized[$index] = $rating + intval(Arr::get($normalized, $index, 0));
        });
        foreach ($normalized as $index => &$rating) {
            if (!Helper::inRange($index, $min, $max)) {
                $rating = 0;
            }
        }
        return $normalized;
    }
}
