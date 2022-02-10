<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class SiteReviewsSummaryWidget extends Widget
{
    /**
     * @param array $instance
     * @return string
     */
    public function form($instance)
    {
        $this->widgetArgs = $this->shortcode()->normalizeAtts($instance)->toArray();
        $terms = glsr(Database::class)->terms();
        $this->renderField('text', [
            'label' => _x('Title', 'admin-text', 'site-reviews'),
            'name' => 'title',
        ]);
        if (count($reviewTypes = glsr()->retrieveAs('array', 'review_types')) > 1) {
            $this->renderField('select', [
                'label' => _x('Which type of review would you like to use?', 'admin-text', 'site-reviews'),
                'name' => 'type',
                'options' => Arr::prepend($reviewTypes, _x('All review types', 'admin-text', 'site-reviews'), ''),
            ]);
        }
        if (!empty($terms)) {
            $this->renderField('select', [
                'label' => _x('Limit summary to this category', 'admin-text', 'site-reviews'),
                'name' => 'assigned_terms',
                'options' => Arr::prepend($terms, _x('Do not limit by category', 'admin-text', 'site-reviews'), ''),
            ]);
        }
        $this->renderField('text', [
            'default' => '',
            'description' => sprintf(_x("You may also enter %s to use the Post ID of the current page.", 'admin-text', 'site-reviews'), '<code>post_id</code>'),
            'label' => _x('Limit summary to reviews assigned to a Post ID', 'admin-text', 'site-reviews'),
            'name' => 'assigned_posts',
        ]);
        $this->renderField('text', [
            'default' => '',
            'description' => sprintf(esc_html_x("You may also enter %s to use the ID of the logged-in user.", 'admin-text', 'site-reviews'), '<code>user_id</code>'),
            'label' => _x('Limit summary to reviews assigned to a User ID', 'admin-text', 'site-reviews'),
            'name' => 'assigned_users',
        ]);
        $this->renderField('text', [
            'label' => _x('Enter any custom CSS classes here', 'admin-text', 'site-reviews'),
            'name' => 'class',
        ]);
        $this->renderField('checkbox', [
            'name' => 'hide',
            'options' => $this->shortcode()->getHideOptions(),
        ]);
        return ''; // WP_Widget::form should return a string
    }

    /**
     * {@inheritdoc}
     */
    protected function shortcode()
    {
        return glsr(SiteReviewsSummaryShortcode::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function widgetDescription()
    {
        return _x('Site Reviews: Display a summary of your reviews.', 'admin-text', 'site-reviews');
    }

    /**
     * {@inheritdoc}
     */
    protected function widgetName()
    {
        return _x('Summary of Reviews', 'admin-text', 'site-reviews');
    }
}
