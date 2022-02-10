<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsFormWidget extends Widget
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
        $this->renderField('textarea', [
            'label' => _x('Description', 'admin-text', 'site-reviews'),
            'name' => 'description',
        ]);
        if (!empty($terms)) {
            $this->renderField('select', [
                'label' => _x('Automatically assign a category', 'admin-text', 'site-reviews'),
                'name' => 'assigned_terms',
                'options' => Arr::prepend($terms, _x('Do not assign a category', 'admin-text', 'site-reviews'), ''),
            ]);
        }
        $this->renderField('text', [
            'default' => '',
            'description' => sprintf(_x("You may also enter %s to use the Post ID of the current page.", 'admin-text', 'site-reviews'), '<code>post_id</code>'),
            'label' => _x('Automatically assign reviews to a Post ID', 'admin-text', 'site-reviews'),
            'name' => 'assigned_posts',
        ]);
        $this->renderField('text', [
            'default' => '',
            'description' => sprintf(esc_html_x("You may also enter %s to use the ID of the logged-in user.", 'admin-text', 'site-reviews'), '<code>user_id</code>'),
            'label' => _x('Automatically assign reviews to a User ID', 'admin-text', 'site-reviews'),
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
        return glsr(SiteReviewsFormShortcode::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function widgetDescription()
    {
        return _x('Site Reviews: Display a form to submit reviews.', 'admin-text', 'site-reviews');
    }

    /**
     * {@inheritdoc}
     */
    protected function widgetName()
    {
        return _x('Submit a Review', 'admin-text', 'site-reviews');
    }
}
