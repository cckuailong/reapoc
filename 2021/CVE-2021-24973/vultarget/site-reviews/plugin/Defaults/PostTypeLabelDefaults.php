<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PostTypeLabelDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        $plural = _x('Reviews', 'admin-text', 'site-reviews');
        $singular = _x('Review', 'admin-text', 'site-reviews');
        return [
            'add_new_item' => sprintf(_x('Add New %s', 'Add New Post (admin-text)', 'site-reviews'), $plural),
            'all_items' => sprintf(_x('All %s', 'All Posts (admin-text)', 'site-reviews'), $plural),
            'archives' => sprintf(_x('%s Archives', 'Post Archives (admin-text)', 'site-reviews'), $singular),
            'edit_item' => sprintf(_x('Edit %s', 'Edit Post (admin-text)', 'site-reviews'), $singular),
            'insert_into_item' => sprintf(_x('Insert into %s', 'Insert into Post (admin-text)', 'site-reviews'), $singular),
            'menu_name' => glsr()->name,
            'name' => $plural,
            'new_item' => sprintf(_x('New %s', 'New Post (admin-text)', 'site-reviews'), $singular),
            'not_found' => sprintf(_x('No %s found', 'No Posts found (admin-text)', 'site-reviews'), $plural),
            'not_found_in_trash' => sprintf(_x('No %s found in Trash', 'No Posts found in Trash (admin-text)', 'site-reviews'), $plural),
            'search_items' => sprintf(_x('Search %s', 'Search Posts (admin-text)', 'site-reviews'), $plural),
            'singular_name' => $singular,
            'uploaded_to_this_item' => sprintf(_x('Uploaded to this %s', 'Uploaded to this Post (admin-text)', 'site-reviews'), $singular),
            'view_item' => sprintf(_x('View %s', 'View Post (admin-text)', 'site-reviews'), $singular),
        ];
    }
}
