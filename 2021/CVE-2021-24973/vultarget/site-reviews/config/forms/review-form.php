<?php

return [
    'rating' => [
        'label' => __('Your overall rating', 'site-reviews'),
        'type' => 'rating',
    ],
    'title' => [
        'label' => __('Title of your review', 'site-reviews'),
        'placeholder' => esc_attr__('Summarize your review or highlight an interesting detail', 'site-reviews'),
        'type' => 'text',
    ],
    'content' => [
        'label' => __('Your review', 'site-reviews'),
        'placeholder' => esc_attr__('Tell people your review', 'site-reviews'),
        'rows' => 5,
        'type' => 'textarea',
    ],
    'name' => [
        'label' => __('Your name', 'site-reviews'),
        'placeholder' => esc_attr__('Tell us your name', 'site-reviews'),
        'type' => 'text',
    ],
    'email' => [
        'label' => __('Your email', 'site-reviews'),
        'placeholder' => esc_attr__('Tell us your email', 'site-reviews'),
        'type' => 'email',
    ],
    'terms' => [
        'label' => __('This review is based on my own experience and is my genuine opinion.', 'site-reviews'),
        'type' => 'toggle',
    ],
];
