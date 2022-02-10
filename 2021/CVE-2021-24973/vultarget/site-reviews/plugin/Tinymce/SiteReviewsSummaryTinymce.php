<?php

namespace GeminiLabs\SiteReviews\Tinymce;

class SiteReviewsSummaryTinymce extends SiteReviewsTinymce
{
    /**
     * @return array
     */
    public function fields()
    {
        return [[
            'html' => sprintf('<p class="strong">%s</p>', _x('All settings are optional.', 'admin-text', 'site-reviews')),
            'minWidth' => 320,
            'type' => 'container',
        ], [
            'label' => _x('Title', 'admin-text', 'site-reviews'),
            'name' => 'title',
            'tooltip' => esc_attr_x('Enter a custom shortcode heading.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ],
        $this->getTypes(_x('Which type of review would you like to use?', 'admin-text', 'site-reviews')),
        $this->getCategories(_x('Limit reviews to this category.', 'admin-text', 'site-reviews')),
        [
            'label' => _x('Assigned Posts', 'admin-text', 'site-reviews'),
            'name' => 'assigned_posts',
            'tooltip' => sprintf(esc_attr_x('Limit reviews to those assigned to a Post ID. You may also enter "%s" to use the Post ID of the current page.', 'admin-text', 'site-reviews'), 'post_id'),
            'type' => 'textbox',
        ], [
            'label' => _x('Assign to User ID', 'admin-text', 'site-reviews'),
            'name' => 'assigned_users',
            'tooltip' => sprintf(esc_attr_x('Limit reviews to those assigned to a User ID. You may also enter "%s" to use the ID of the logged-in user.', 'admin-text', 'site-reviews'), 'user_id'),
            'type' => 'textbox',
        ], [
            'label' => _x('Schema', 'admin-text', 'site-reviews'),
            'name' => 'schema',
            'options' => [
                'true' => _x('Enable rich snippets', 'admin-text', 'site-reviews'),
                'false' => _x('Disable rich snippets', 'admin-text', 'site-reviews'),
            ],
            'tooltip' => esc_attr_x('Rich snippets are disabled by default.', 'admin-text', 'site-reviews'),
            'type' => 'listbox',
        ], [
            'label' => _x('Classes', 'admin-text', 'site-reviews'),
            'name' => 'class',
            'tooltip' => esc_attr_x('Add custom CSS classes to the shortcode.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'columns' => 2,
            'items' => $this->getHideOptions(),
            'label' => _x('Hide', 'admin-text', 'site-reviews'),
            'layout' => 'grid',
            'spacing' => 5,
            'type' => 'container',
        ], ];
    }
}
