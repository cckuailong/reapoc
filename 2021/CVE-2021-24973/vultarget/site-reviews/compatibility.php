<?php

use GeminiLabs\SiteReviews\Controllers\BlocksController;

defined('ABSPATH') || die;

/**
 * @param array $editors
 * @param string $postType
 * @return array
 * @see https://wordpress.org/plugins/classic-editor/
 */
add_filter('classic_editor_enabled_editors_for_post_type', function ($editors, $postType) {
    return glsr()->post_type == $postType
        ? ['block_editor' => false, 'classic_editor' => false]
        : $editors;
}, 10, 2);

/**
 * Add human-readable capability names
 * @return void
 * @see https://wordpress.org/plugins/members/
 */
add_action('members_register_caps', function () {
    members_register_cap('create_site-reviews', [
        'label' => _x('Create Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('delete_others_site-reviews', [
        'label' => _x("Delete Others' Reviews", 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('delete_site-reviews', [
        'label' => _x('Delete Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('delete_private_site-reviews', [
        'label' => _x('Delete Private Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('delete_published_site-reviews', [
        'label' => _x('Delete Approved Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('edit_others_site-reviews', [
        'label' => _x("Edit Others' Reviews", 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('edit_site-reviews', [
        'label' => _x('Edit Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('edit_private_site-reviews', [
        'label' => _x('Edit Private Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('edit_published_site-reviews', [
        'label' => _x('Edit Approved Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('publish_site-reviews', [
        'label' => _x('Approve Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('read_private_site-reviews', [
        'label' => _x('Read Private Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('respond_to_site-reviews', [
        'label' => _x('Respond To Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('respond_to_others_site-reviews', [
        'label' => _x("Respond To Others' Reviews", 'admin-text', 'site-reviews'),
    ]);
});

/**
 * Exclude the reCAPTCHA script from being defered
 * @param array $scriptHandles
 * @return array
 * @see https://wordpress.org/plugins/speed-booster-pack/
 */
add_filter('sbp_exclude_defer_scripts', function ($scriptHandles) {
    $scriptHandles[] = 'site-reviews/google-recaptcha';
    return array_keys(array_flip($scriptHandles));
});

/**
 * Fix to display all reviews when sorting by rank
 * @param array $query
 * @return array
 * @see https://searchandfilter.com/
 */
add_filter('sf_edit_query_args', function ($query) {
    if (!empty($query['meta_key']) && '_glsr_ranking' == $query['meta_key']) {
        unset($query['meta_key']);
        $query['meta_query'] = [
            'relation' => 'OR',
            ['key' => '_glsr_ranking', 'compare' => 'NOT EXISTS'], // this comes first!
            ['key' => '_glsr_ranking', 'compare' => 'EXISTS'],
        ];
    }
    return $query;
}, 20);

/**
 * Fix checkboxes for the Divi plugin style
 * @param \GeminiLabs\SiteReviews\Modules\Html\Builder $instance
 * @return void
 * @see https://www.elegantthemes.com/gallery/divi/
 */
add_action('site-reviews/customize/divi', function ($instance) {
    if ('label' == $instance->tag && 'checkbox' == $instance->args['type']) {
        $instance->args['text'] = '<i></i>'.$instance->args['text'];
        return;
    }
});

/**
 * Load the Ninja Forms (v3) CSS if the plugin style is selected.
 * @see https://ninjaforms.com/
 */
function glsr_is_ninja_forms_compatible() {
    return class_exists('Ninja_Forms')
        && class_exists('NF_Display_Render')
        && method_exists('Ninja_Forms', 'get_setting')
        && method_exists('NF_Display_Render', 'enqueue_styles_display');
}
add_action('enqueue_block_editor_assets', function () {
    if ('ninja_forms' === glsr_get_option('general.style') && glsr_is_ninja_forms_compatible()) {
        NF_Display_Render::enqueue_styles_display(Ninja_Forms::$url.'assets/css/');
    }
});
add_filter('site-reviews/config/styles/ninja_forms', function ($config) {
    if (glsr_is_ninja_forms_compatible()) {
        $formClass = 'nf-style-'.Ninja_Forms()->get_setting('opinionated_styles');
        $config = glsr_set($config, 'classes.form', $formClass);
    }
    return $config;
});
add_action('site-reviews/customize/ninja_forms', function () {
    if (glsr_is_ninja_forms_compatible()) {
        NF_Display_Render::enqueue_styles_display(Ninja_Forms::$url.'assets/css/');
    }
});

/**
 * Purge the W3 Total Cache database and object caches after plugin migrations.
 * @return void
 * @see https://wordpress.org/plugins/w3-total-cache/
 */
add_action('site-reviews/migration/end', function () {
    if (function_exists('w3tc_dbcache_flush')) {
        w3tc_dbcache_flush();
    }
    if (function_exists('w3tc_objectcache_flush')) {
        w3tc_objectcache_flush();
    }
});

/**
 * Purge the WP Rocket plugin cache of assigned posts after a review has been created.
 * @param \GeminiLabs\SiteReviews\Review $review
 * @param \GeminiLabs\SiteReviews\Commands\CreateReview $command
 * @return void
 * @see https://docs.wp-rocket.me/article/93-rocketcleanpost
 */
add_action('site-reviews/review/created', function ($review, $command) {
    if (!function_exists('rocket_clean_post')) {
        return;
    }
    rocket_clean_post($command->post_id); // The page the review was submitted on
    foreach ($command->assigned_posts as $postId) {
        if ($postId != $command->post_id) {
            rocket_clean_post($postId);
        }
    }
}, 10, 2);

/**
 * Purge the WP-Super-Cache plugin cache after a review has been created.
 * @param \GeminiLabs\SiteReviews\Review $review
 * @param \GeminiLabs\SiteReviews\Commands\CreateReview $command
 * @return void
 * @see https://wordpress.org/plugins/wp-super-cache/
 */
add_action('site-reviews/review/created', function ($review, $command) {
    if (!function_exists('wp_cache_post_change')) {
        return;
    }
    wp_cache_post_change($command->post_id);
    foreach ($review->assigned_posts as $postId) {
        if ($postId != $command->post_id) {
            wp_cache_post_change($postId);
        }
    }
}, 10, 2);

/**
 * Purge the Hummingbird page cache after a review has been created.
 * @param \GeminiLabs\SiteReviews\Review $review
 * @param \GeminiLabs\SiteReviews\Commands\CreateReview $command
 * @return void
 * @see https://premium.wpmudev.org/docs/api-plugin-development/hummingbird-api-docs/#action-wphb_clear_page_cache
 */
add_action('site-reviews/review/created', function ($review, $command) {
    do_action('wphb_clear_page_cache', $command->post_id);
}, 10, 2);

/**
 * Purge the SiteGround page cache after a review has been created.
 * @param \GeminiLabs\SiteReviews\Review $review
 * @param \GeminiLabs\SiteReviews\Commands\CreateReview $command
 * @return void
 * @see https://wordpress.org/plugins/sg-cachepress/
 */
add_action('site-reviews/review/created', function ($review, $command) {
    if (function_exists('sg_cachepress_purge_cache')) {
        sg_cachepress_purge_cache(get_permalink($command->post_id));
    }
}, 10, 2);

/**
 * Purge the WP-Optimize page cache after a review has been created.
 * @param \GeminiLabs\SiteReviews\Review $review
 * @param \GeminiLabs\SiteReviews\Commands\CreateReview $command
 * @return void
 * @see https://getwpo.com/documentation/#Purging-the-cache-from-an-other-plugin-or-theme
 */
add_action('site-reviews/review/created', function ($review, $command) {
    if (class_exists('WPO_Page_Cache')) {
        WPO_Page_Cache::delete_single_post_cache($command->post_id);
    }
}, 10, 2);

/**
 * Load the WPForms stylesheet when using the WPForms plugin style
 * @param string $template
 * @return string
 * @see https://wordpress.org/plugins/wpforms-lite/
 */
add_filter('site-reviews/build/template/reviews-form', function ($template) {
    if ('wpforms' === glsr_get_option('general.style')) {
        add_filter('wpforms_global_assets', '__return_true');
    }
    return $template;
});

/**
 * Remove the "Launch Thrive Architect" button from reviews
 * @return array
 * @see https://thrivethemes.com/architect/
 */
add_filter('tcb_post_types', function ($blacklist) {
    $blacklist[] = glsr()->post_type;
    return $blacklist;
});

/**
 * WordPress <5.8 compatibility
 */
if (!is_wp_version_compatible('5.8')) {
    add_action('init', function () {
        add_filter('allowed_block_types', [glsr(BlocksController::class), 'filterAllowedBlockTypes'], 10, 2);
        add_filter('block_categories', [glsr(BlocksController::class), 'filterBlockCategories']);
    });
}
