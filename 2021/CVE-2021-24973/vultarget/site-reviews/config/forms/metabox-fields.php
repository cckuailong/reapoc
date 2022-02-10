<?php

return [
    'rating' => [
        'label' => esc_html_x('Rating', 'admin-text', 'site-reviews'),
        'type' => 'rating',
    ],
    'type' => [
        'label' => esc_html_x('Type', 'admin-text', 'site-reviews'),
        'options' => glsr()->retrieve('review_types'),
        'type' => 'select',
    ],
    'name' => [
        'label' => esc_html_x('Name', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'email' => [
        'label' => esc_html_x('Email', 'admin-text', 'site-reviews'),
        'type' => 'email',
    ],
    'ip_address' => [
        'label' => esc_html_x('IP Address', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'avatar' => [
        'label' => esc_html_x('Avatar', 'admin-text', 'site-reviews'),
        'type' => 'url',
    ],
    'terms' => [
        'label' => esc_html_x('Terms', 'admin-text', 'site-reviews'),
        'options' => [1 => _x('Terms Accepted', 'admin-text', 'site-reviews')],
        'type' => 'checkbox',
    ],
];
