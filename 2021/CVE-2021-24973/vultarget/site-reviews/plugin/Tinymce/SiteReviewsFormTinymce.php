<?php

namespace GeminiLabs\SiteReviews\Tinymce;

class SiteReviewsFormTinymce extends TinymceGenerator
{
    /**
     * @return array
     */
    public function fields()
    {
        return [[
            'type' => 'container',
            'html' => '<p class="strong">'._x('All settings are optional.', 'admin-text', 'site-reviews').'</p>',
        ], [
            'label' => _x('Title', 'admin-text', 'site-reviews'),
            'name' => 'title',
            'tooltip' => esc_attr_x('Enter a custom shortcode heading.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ], [
            'label' => _x('Description', 'admin-text', 'site-reviews'),
            'minHeight' => 60,
            'minWidth' => 240,
            'multiline' => true,
            'name' => 'description',
            'tooltip' => esc_attr_x('Enter a custom shortcode description.', 'admin-text', 'site-reviews'),
            'type' => 'textbox',
        ],
        $this->getCategories(_x('Automatically assign a category to reviews submitted with this shortcode.', 'admin-text', 'site-reviews')),
        [
            'label' => _x('Assign to Post ID', 'admin-text', 'site-reviews'),
            'name' => 'assigned_posts',
            'tooltip' => sprintf(esc_attr_x('Automatically assign reviews to a Post ID. You may also enter "%s" to use the Post ID of the current page.', 'admin-text', 'site-reviews'), 'post_id'),
            'type' => 'textbox',
        ], [
            'label' => _x('Assign to User ID', 'admin-text', 'site-reviews'),
            'name' => 'assigned_users',
            'tooltip' => sprintf(esc_attr_x('Automatically assign reviews to a User ID. You may also enter "%s" to use the ID of the logged-in user.', 'admin-text', 'site-reviews'), 'user_id'),
            'type' => 'textbox',
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
        ], [
            'hidden' => true,
            'name' => 'id',
            'type' => 'textbox',
        ], ];
    }
}
