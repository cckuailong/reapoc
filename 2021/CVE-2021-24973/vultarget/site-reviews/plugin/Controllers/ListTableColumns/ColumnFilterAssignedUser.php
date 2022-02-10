<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Arr;

class ColumnFilterAssignedUser extends ColumnFilter
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $enabledFilters = [])
    {
        if (in_array('assigned_user', $enabledFilters)) {
            $this->enabled = true;
        }
        if ($options = $this->options()) {
            $label = $this->label('assigned_user',
                _x('Filter by assigned user', 'admin-text', 'site-reviews')
            );
            $filter = $this->filter('assigned_user', $options,
                _x('All assigned users', 'admin-text', 'site-reviews')
            );
            return $label.$filter;
        }
    }

    /**
     * @return array
     */
    protected function options()
    {
        global $wpdb;
        $table = glsr(Query::class)->table('assigned_users');
        $userIds = $wpdb->get_col("SELECT DISTINCT user_id FROM {$table}");
        if (empty($userIds)) {
            return [];
        }
        $options = glsr(Database::class)->users(['include' => $userIds]);
        $options = Arr::prepend($options, _x('No assigned user', 'admin-text', 'site-reviews'), '-1');
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
