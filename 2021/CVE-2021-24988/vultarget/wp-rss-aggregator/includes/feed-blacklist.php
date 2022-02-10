<?php

// Check if the 'blacklist' GET param is set
add_action('admin_init', 'wprss_check_if_blacklist_item');
// Checks if the transient is set to show the notice
add_action('admin_init', 'wprss_check_notice_transient');
// Register custom post type
// Add the row actions to the targetted post type
add_filter('post_row_actions', 'wprss_blacklist_row_actions', 10, 1);

/**
 * Creates a blacklist entry for the given feed item.
 *
 * @since 4.4
 *
 * @param int|string The ID of the feed item to add to the blacklist
 */
function wprss_blacklist_item($ID)
{
    // Return if feed item is null
    if (is_null($ID)) {
        return;
    }

    // Get the feed item data
    $item_title = get_the_title($ID);
    $item_permalink = get_post_meta($ID, 'wprss_item_permalink', true);
    // If not an imported item, stop
    if ($item_permalink === '') {
        wpra_get_logger()->warning('Feed item with ID {0} was not blacklisted because its URL was empty', [$ID]);

        return;
    }
    // Prepare the data for blacklisting
    $title = apply_filters('wprss_blacklist_title', trim($item_title));
    $permalink = apply_filters('wprss_blacklist_permalink', trim($item_permalink));

    // Delete the item
    wp_delete_post($ID, true);

    // Add the blacklisted item
    $id = wp_insert_post([
        'post_title' => $title,
        'post_type' => 'wprss_blacklist',
        'post_status' => 'publish',
        'meta_input' => [
            'wprss_permalink' => $permalink,
        ],
    ]);
}

/**
 * Determines whether the given item is blacklist.
 *
 * @since 4.4
 *
 * @param string $permalink The permalink to look for in the saved option
 *
 * @return bool TRUE if the permalink is found, FALSE otherwise.
 */
function wprss_is_blacklisted($permalink)
{
    if (empty($permalink)) {
        return false;
    }

    // Query the blacklist entries, for an item with the given permalink
    $query = new WP_Query([
        'post_type' => 'wprss_blacklist',
        'meta_key' => 'wprss_permalink',
        'meta_value' => $permalink,
    ]);

    // Return TRUE if the query returned a result, FALSE otherwise
    return $query->have_posts();
}

/**
 * Check if the 'blacklist' GET param is set, and prepare to blacklist the item.
 *
 * @since 4.4
 */
function wprss_check_if_blacklist_item()
{
    // Get the ID from the GET param
    $id = filter_input(INPUT_GET, 'wprss_blacklist', FILTER_VALIDATE_INT);
    if (empty($id)) {
        return;
    }

    // If the post does not exist, stop. Show a message
    $post = (is_int($id) && $id > 0)
        ? get_post($id)
        : null;
    if ($post === null) {
        wp_die(__('The item you are trying to blacklist does not exist', 'wprss'));
    }

    // If the post type is not correct,
    if (get_post_meta($id, 'wprss_item_permalink', true) === '' || $post->post_status !== 'trash') {
        wp_die(__('The item you are trying to blacklist is not valid!', 'wprss'));
    }

    check_admin_referer('blacklist-item-' . $id, 'wprss_blacklist_item');
    wprss_blacklist_item($id);

    // Get the current post type for the current page
    $postType = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
    $postType = $postType ? $postType : 'post';

    // Check the current page, and generate the URL query string for the page
    $paged = filter_input(INPUT_GET, 'paged', FILTER_VALIDATE_INT);
    $pagedArg = $paged
        ? '&paged=' . urlencode($paged)
        : '';

    // Set the notice transient
    set_transient('wprss_item_blacklist_notice', 'true');
    // Refresh the page without the GET parameter
    wp_redirect(admin_url("edit.php?post_type=$postType&post_status=trash" . $pagedArg));

    exit;
}

/**
 * Checks if the transient for the blacklist notice is set, and shows the notice
 * if it is set.
 */
function wprss_check_notice_transient()
{
    // Check if the transient exists
    $transient = get_transient('wprss_item_blacklist_notice');
    if ($transient !== false) {
        // Remove the transient
        delete_transient('wprss_item_blacklist_notice');
        // Show the notice
        // add_action( 'admin_notices', 'wprss_blacklist_item_notice' );
        wprss()->getAdminAjaxNotices()->addNotice('blacklist_item_success');
    }
}

/**
 * Adds the row actions to the targetted post type.
 * Default post type = wprss_feed_item
 *
 * @since 4.4
 *
 * @param array $actions The row actions to be filtered
 *
 * @return array The new filtered row actions
 */
function wprss_blacklist_row_actions($actions)
{
    // Check the current page, and generate the URL query string for the page
    $paged = filter_input(INPUT_GET, 'paged', FILTER_VALIDATE_INT);
    $pagedArg = is_int($paged) && $paged > 0
        ? '&paged=' . urlencode($paged)
        : '';

    // Check the post type
    if (get_post_status() !== 'trash') {
        return $actions;
    }

    // Get the Post ID
    $ID = get_the_ID();

    // Get the permalink. If does not exist, then it is not an imported item.
    $permalink = get_post_meta($ID, 'wprss_item_permalink', true);
    if ($permalink === '') {
        return $actions;
    }

    // The post type on the current screen
    $post_type = get_post_type();
    // Prepare the blacklist URL
    $plain_url = apply_filters(
        'wprss_blacklist_row_action_url',
        admin_url("edit.php?post_type=$post_type&wprss_blacklist=$ID"),
        $ID
    );
    $plain_url = $plain_url . $pagedArg;
    // Add a nonce to the URL
    $nonced_url = wp_nonce_url($plain_url, 'blacklist-item-' . $ID, 'wprss_blacklist_item');

    // Prepare the text
    $text = htmlentities(__('Delete Permanently & Blacklist', 'wprss'));
    $text = apply_filters('wprss_blacklist_row_action_text', $text);

    // Prepare the hint
    $hint = apply_filters(
        'wprss_blacklist_row_action_hint',
        __('The item will be deleted permanently, and its permalink will be recorded in the blacklist', 'wprss')
    );
    $hint = esc_attr(__($hint, 'wprss'));

    // Add the blacklist action
    $actions['blacklist-item'] = "<span class='delete'><a title='$hint' href='$nonced_url'>$text</a></span>";

    return $actions;
}
