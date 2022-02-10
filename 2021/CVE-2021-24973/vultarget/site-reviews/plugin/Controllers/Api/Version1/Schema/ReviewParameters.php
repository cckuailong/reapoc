<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1\Schema;

class ReviewParameters
{
    public function parameters()
    {
        $parameters = glsr(SummaryParameters::class)->parameters();
        $parameters['offset'] = [
            'description' => _x('Offset the result set by a specific number of items.', 'admin-text', 'site-reviews'),
            'type' => 'integer',
        ];
        $parameters['order'] = [
            'default' => 'desc',
            'description' => _x('Order sort attribute ascending or descending.', 'admin-text', 'site-reviews'),
            'enum' => ['asc', 'desc'],
            'type' => 'string',
        ];
        $parameters['orderby'] = [
            'default' => 'date',
            'description' => _x('Sort collection by object attribute.', 'admin-text', 'site-reviews'),
            'enum' => ['author', 'comment_count', 'date', 'date_gmt', 'id', 'menu_order', 'none', 'random', 'rating'],
            'type' => 'string',
        ];
        $parameters['page'] = [
            'default' => 1,
            'description' => _x('Current page of the collection.', 'admin-text', 'site-reviews'),
            'minimum' => 1,
            'sanitize_callback' => 'absint',
            'type' => 'integer',
        ];
        $parameters['per_page'] = [
            'default' => 10,
            'description' => _x('Maximum number of items to be returned in result set.', 'admin-text', 'site-reviews'),
            'maximum' => 100,
            'minimum' => 1,
            'sanitize_callback' => 'absint',
            'type' => 'integer',
        ];
        return $parameters;
    }
}
