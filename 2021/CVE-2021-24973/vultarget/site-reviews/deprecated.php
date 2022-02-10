<?php

use GeminiLabs\SiteReviews\Helpers\Str;

defined('ABSPATH') || exit;

/**
 * Provide support for the deprecated {{ assigned_to }} tag
 * @param string $template
 * @return string
 * @since 5.0
 */
add_filter('site-reviews/build/template/review', function ($template) {
    return str_replace('{{ assigned_to }}', '{{ assigned_links }}', $template);
});

/**
 * Fix the {{ review_id }} tag in the review template which now only returns the ID
 * @param string $template
 * @return string
 * @since 5.3
 */
add_filter('site-reviews/build/template/review', function ($template) {
    return str_replace('id="{{ review_id }}"', 'id="review-{{ review_id }}"', $template);
});

add_action('plugins_loaded', function () {
    if (!glsr()->filterBool('support/deprecated/v5', true)) {
        return;
    }
    /**
     * Review meta data has been deprecated
     * @since 5.0.0
     */
    add_filter('get_post_metadata', function ($data, $postId, $metaKey) {
        if (glsr()->post_type !== get_post_type($postId)) {
            return $data;
        }
        $metaKey = Str::removePrefix($metaKey, '_');
        $metaKeys = [
            'assigned_to', 'author', 'avatar', 'content', 'date', 'email',
            'ip_address', 'pinned', 'rating', 'review_id', 'review_type',
            'title', 'url',
        ];
        if (!in_array($metaKey, $metaKeys)) {
            return $data;
        }
        $message = 'Site Reviews no longer stores the "'.$metaKey.'" review value as meta data. Please use the glsr_get_review() helper function instead.';
        glsr()->append('deprecated', $message);
        return glsr_get_review($postId)->{$metaKey};
    }, 10, 3);

    /*
     * Application
     * @since 5.0.0
     */
    add_filter('site-reviews/config/forms/review-form', function ($config) {
        if (has_filter('site-reviews/config/forms/submission-form')) {
            $message = 'The "site-reviews/config/forms/submission-form" hook has been deprecated. Please use the "site-reviews/config/forms/review-form" hook instead.';
            glsr()->append('deprecated', $message);
            return apply_filters('site-reviews/config/forms/submission-form', $config);
        }
        return $config;
    }, 9);

    /*
     * Modules\Html\ReviewsHtml
     * @since 5.0.0
     */
    add_filter('site-reviews/rendered/template/reviews', function ($html) {
        if (has_filter('site-reviews/reviews/reviews-wrapper')) {
            $message = 'The "site-reviews/reviews/reviews-wrapper" hook has been removed. Please use a custom "reviews.php" template instead.';
            glsr()->append('deprecated', $message);
        }
        return $html;
    });

    /**
     * Controllers\PublicController
     * @since 5.0.0
     */
    add_filter('site-reviews/review-form/order', function ($order) {
        if (has_filter('site-reviews/submission-form/order')) {
            $message = 'The "site-reviews/submission-form/order" hook has been deprecated. Please use the "site-reviews/review-form/order" hook instead.';
            glsr()->append('deprecated', $message);
            return apply_filters('site-reviews/submission-form/order', $order);
        }
        return $order;
    }, 9);

    /*
     * Controllers\ListTableController
     * @since 5.11.0
     */
    add_filter('site-reviews/defaults/review-table-filters', function ($defaults) {
        if (has_filter('site-reviews/review-table/filter')) {
            $message = 'The "site-reviews/review-table/filter" hook has been deprecated. Please use the "site-reviews/defaults/review-table-filters" hook instead.';
            glsr()->append('deprecated', $message);
        }
        return $defaults;
    });

    /*
     * Database\ReviewManager
     * @since 5.11.0
     */
    add_action('site-reviews/review/responded', function ($review, $response) {
        if (has_action('site-reviews/review/response')) {
            $message = 'The "site-reviews/review/response" hook has been deprecated. Please use the "site-reviews/review/responded" hook instead which is documented on the FAQ page.';
            glsr()->append('deprecated', $message);
            do_action('site-reviews/review/response', $response, $review);
        }
    }, 9, 2);
});

/**
 * @return void
 * @since 5.0.0
 */
function glsr_calculate_ratings() {
    _deprecated_function('glsr_calculate_ratings', '5.0 (of Site Reviews)');
    glsr_log()->error(sprintf(
        __('%s is <strong>deprecated</strong> since version %s with no alternative available.', 'site-reviews'),
        'glsr_calculate_ratings',
        '5.0'
    ));
}

/**
 * @return object
 * @since 5.0.0
 */
function glsr_get_rating($args = []) {
    _deprecated_function('glsr_get_rating', '5.0 (of Site Reviews)', 'glsr_get_ratings');
    glsr_log()->warning(sprintf(
        __('%s is <strong>deprecated</strong> since version %s! Use %s instead.', 'site-reviews'),
        'glsr_get_rating',
        '5.0',
        'glsr_get_ratings'
    ));
    return glsr_get_ratings($args);
}

function glsr_log_deprecated_notices() {
    $notices = (array) glsr()->retrieve('deprecated', []);
    $notices = array_keys(array_flip(array_filter($notices)));
    natsort($notices);
    foreach ($notices as $notice) {
        trigger_error($notice, E_USER_DEPRECATED);
        glsr_log()->warning($notice);
    }
}
add_action('admin_footer', 'glsr_log_deprecated_notices');
add_action('wp_footer', 'glsr_log_deprecated_notices');
