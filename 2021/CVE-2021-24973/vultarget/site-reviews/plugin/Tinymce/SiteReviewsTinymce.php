<?php

namespace GeminiLabs\SiteReviews\Tinymce;

class SiteReviewsTinymce extends TinymceGenerator
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
            'tooltip' => _x('Enter a custom shortcode heading.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'label' => _x('Display', 'admin-text', 'site-reviews'),
            'maxLength' => 5,
            'name' => 'display',
            'size' => 3,
            'text' => '10',
            'tooltip' => _x('How many reviews would you like to display (default: 10)?', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'label' => _x('Rating', 'admin-text', 'site-reviews'),
            'name' => 'rating',
            'options' => [
                '5' => sprintf(_nx('%s star', '%s stars', 5, 'admin-text', 'site-reviews'), 5),
                '4' => sprintf(_nx('%s star', '%s stars', 4, 'admin-text', 'site-reviews'), 4),
                '3' => sprintf(_nx('%s star', '%s stars', 3, 'admin-text', 'site-reviews'), 3),
                '2' => sprintf(_nx('%s star', '%s stars', 2, 'admin-text', 'site-reviews'), 2),
                '1' => sprintf(_nx('%s star', '%s stars', 1, 'admin-text', 'site-reviews'), 1),
                '0' => _x('Unrated', 'admin-text', 'site-reviews'),
            ],
            'tooltip' => _x('What is the minimum rating to display (default: 1 star)?', 'admin-text', 'site-reviews'),
            'type' => 'listbox',
        ], [
            'label' => _x('Pagination', 'admin-text', 'site-reviews'),
            'name' => 'pagination',
            'options' => [
                'true' => _x('Enable', 'admin-text', 'site-reviews'),
                'ajax' => _x('Enable (using ajax)', 'admin-text', 'site-reviews'),
                'false' => _x('Disable', 'admin-text', 'site-reviews'),
            ],
            'tooltip' => _x('When using pagination this shortcode can only be used once on a page. (default: disable)', 'admin-text', 'site-reviews'),
            'type' => 'listbox',
        ],
        $this->getTypes(_x('Which type of review would you like to display?', 'admin-text', 'site-reviews')),
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
            'tooltip' => _x('Rich snippets are disabled by default.', 'admin-text', 'site-reviews'),
            'type' => 'listbox',
        ], [
            'label' => _x('Classes', 'admin-text', 'site-reviews'),
            'name' => 'class',
            'tooltip' => _x('Add custom CSS classes to the shortcode.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'columns' => 2,
            'items' => $this->getHideOptions(),
            'label' => _x('Hide', 'admin-text', 'site-reviews'),
            'layout' => 'grid',
            'spacing' => 5,
            'type' => 'container',
        ], [
            'hidden' => true,
            'name' => 'id',
            'type' => 'textbox',
        ], ];
    }
}
