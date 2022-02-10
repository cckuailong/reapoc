<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Arr;

class ColumnFilterAssignedPost extends ColumnFilter
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $enabledFilters = [])
    {
        if (in_array('assigned_post', $enabledFilters)) {
            $this->enabled = true;
        }
        if ($options = $this->options()) {
            $label = $this->label('assigned_post',
                _x('Filter by assigned post', 'admin-text', 'site-reviews')
            );
            $filter = $this->filter('assigned_post', $options,
                _x('All assigned posts', 'admin-text', 'site-reviews')
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
        $table = glsr(Query::class)->table('assigned_posts');
        $postIds = $wpdb->get_col("SELECT DISTINCT post_id FROM {$table}");
        if (empty($postIds)) {
            return [];
        }
        $posts = get_posts([
            'no_found_rows' => true, // skip counting the total rows found
            'post_status' => 'any',
            'post_type' => 'any',
            'post__in' => $postIds,
            'posts_per_page' => -1,
        ]);
        $options = wp_list_pluck($posts, 'post_title', 'ID');
        foreach ($options as $id => &$title) {
            if (empty($title)) {
                $title = sprintf('%s', _x('No title', 'admin-text', 'site-reviews'));
            }
            $title = sprintf('%s (ID: %s)', $title, $id);
        }
        natcasesort($options);
        $options = Arr::prepend($options, _x('No assigned post', 'admin-text', 'site-reviews'), '-1');
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
