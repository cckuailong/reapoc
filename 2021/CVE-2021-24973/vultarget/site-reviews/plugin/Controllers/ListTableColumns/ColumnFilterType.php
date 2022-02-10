<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ColumnFilterType extends ColumnFilter
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $enabledFilters = [])
    {
        if (in_array('type', $enabledFilters)) {
            $this->enabled = true;
        }
        $options = $this->options();
        if (count($options) > 1) {
            $label = $this->label('type',
                _x('Filter by review type', 'admin-text', 'site-reviews')
            );
            $filter = $this->filter('type', $options,
                _x('All review types', 'admin-text', 'site-reviews')
            );
            return $label.$filter;
        }
    }

    /**
     * @return array
     */
    protected function options()
    {
        return glsr()->retrieveAs('array', 'review_types');
    }
}
