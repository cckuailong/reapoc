<?php

namespace GeminiLabs\SiteReviews\Controllers;

class TaxonomyController extends Controller
{
    /**
     * @param array $actions
     * @param \WP_Term $term
     * @return array
     * @filter {glsr()->taxonomy}_row_actions
     */
    public function filterRowActions($actions, $term)
    {
        $action = ['id' => sprintf(_x('<span>ID: %d</span>', 'The Term ID (admin-text)', 'site-reviews'), $term->term_id)];
        return array_merge($action, $actions);
    }
}
