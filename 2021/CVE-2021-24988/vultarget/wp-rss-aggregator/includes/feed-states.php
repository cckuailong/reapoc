<?php

namespace
{
    /**
     * Activates the feed source. Runs on a schedule.
     *
     * @since 3.7
     *
     * @param int|string $feedId The of of the wprss_feed
     */
    function wprss_activate_feed_source($feedId)
    {
        update_post_meta($feedId, 'wprss_state', 'active');
        update_post_meta($feedId, 'wprss_activate_feed', '');

        // Add an action hook, so functions can be run when a feed source is activated
        do_action('wprss_on_feed_source_activated', $feedId);
    }

    /**
     * Pauses the feed source. Runs on a schedule.
     *
     * @since 3.7
     *
     * @param int|string $feedId The ID of the feed source.
     */
    function wprss_pause_feed_source($feedId)
    {
        update_post_meta($feedId, 'wprss_state', 'paused');
        update_post_meta($feedId, 'wprss_pause_feed', '');

        // Add an action hook, so functions can be run when a feed source is paused
        do_action('wprss_on_feed_source_paused', $feedId);
    }

    /**
     * Returns whether or not a feed source is active.
     *
     * @since 3.7
     *
     * @param int|string $feedId The ID of the feed source.
     *
     * @return boolean
     */
    function wprss_is_feed_source_active($feedId)
    {
        $state = get_post_meta($feedId, 'wprss_state', true);

        return empty($state) || $state === 'active';
    }
}

namespace RebelCode\Wpra\Feeds\States
{
    const NOTICE_TRANSIENT_ACTIVATED = 'activated';
    const NOTICE_TRANSIENT_PAUSED = 'paused';

    /**
     * Changes the state of feed sources selected from the table bulk actions.
     */
    add_action('admin_init', function () {
        $postType = filter_input(INPUT_GET, 'post_type', FILTER_SANITIZE_STRING);
        $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
        $action2 = filter_input(INPUT_GET, 'action2', FILTER_SANITIZE_STRING);
        $postIds = filter_input(INPUT_GET, 'post', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);

        $action = sanitize_text_field($action);
        $action2 = sanitize_text_field($action2);

        if ($postType && ($action || $action2) && $postIds) {
            $action = (!empty($action) && $action !== '-1') ? $action : $action2;
            $action = strtolower($action);
            $stateChange = null;

            switch ($action) {
                // Activate all feed sources in $postIds
                case 'activate':
                    foreach ($postIds as $postId) {
                        wprss_activate_feed_source($postId);
                    }
                    $stateChange = NOTICE_TRANSIENT_ACTIVATED;
                    break;

                // Pause all feed sources in $postIds
                case 'pause':
                    foreach ($postIds as $postId) {
                        wprss_pause_feed_source($postId);
                    }
                    $stateChange = NOTICE_TRANSIENT_PAUSED;
                    break;
            }

            if ($stateChange !== null) {
                // Set a transient to show the admin notice, after redirection
                set_transient('wprss_notify_bulk_change_state', $stateChange);
                /* Note:
                 * Transients are used since bulk actions will, after processing, case a redirect to the same page.
                 * Thus, using add_action( 'all_admin_notices', ... ) will result in the notice appearing on the
                 * first request, and not be shown after redirection.
                 * The transient is set to show the notification AFTER redirection.
                 */
            }
        }
    }, 2);

    /**
     * Checks if the 'wprss_notify_bulk_change_state' transient is set.
     * If it is, it will show the appropriate admin notice
     */
    add_action('admin_init', function () {
        $transient = get_transient('wprss_notify_bulk_change_state');

        if (empty($transient)) {
            return;
        }

        $transient = strtolower($transient);
        $notice = null;

        switch ($transient) {
            case NOTICE_TRANSIENT_ACTIVATED:
                $notice = 'bulk_feed_activated';
                break;
            case NOTICE_TRANSIENT_PAUSED:
                $notice = 'bulk_feed_paused';
                break;
        }

        if ($notice !== null) {
            wprss()->getAdminAjaxNotices()->addNotice($notice);
        }

        delete_transient('wprss_notify_bulk_change_state');
    }, 1);
}
