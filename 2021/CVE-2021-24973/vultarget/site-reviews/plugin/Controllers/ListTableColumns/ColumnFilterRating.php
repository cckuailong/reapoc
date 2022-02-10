<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Modules\Rating;

class ColumnFilterRating extends ColumnFilter
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $enabledFilters = [])
    {
        if (in_array('rating', $enabledFilters)) {
            $this->enabled = true;
        }
        $options = $this->options();
        if (count($options) > 1) {
            $label = $this->label('rating',
                _x('Filter by rating', 'admin-text', 'site-reviews')
            );
            $filter = $this->filter('rating', $options,
                _x('All ratings', 'admin-text', 'site-reviews')
            );
            return $label.$filter;
        }
    }

    /**
     * @return array
     */
    protected function options()
    {
        $options = [];
        $ratings = range(glsr()->constant('MAX_RATING', Rating::class), 0);
        foreach ($ratings as $rating) {
            $label = _nx('%s star', '%s stars', $rating, 'admin-text', 'site-reviews');
            $options[$rating] = sprintf($label, $rating);
        }
        return $options;
    }

    /**
     * @param string $id
     * @return int|string
     */
    protected function value($id)
    {
        return filter_input(INPUT_GET, $id, FILTER_SANITIZE_NUMBER_INT);
    }
}
