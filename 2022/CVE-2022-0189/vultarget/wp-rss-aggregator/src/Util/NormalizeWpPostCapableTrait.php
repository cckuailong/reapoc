<?php

namespace RebelCode\Wpra\Core\Util;

use OutOfRangeException;
use WP_Post;

/**
 * Functionality for normalizing a variable into a {@link WP_Post} instance.
 *
 * @since 4.13
 */
trait NormalizeWpPostCapableTrait
{
    /**
     * Normalizes the argument into a {@link WP_Post} instance.
     *
     * @since 4.13
     *
     * @param int|string|WP_Post $postOrId The WordPress post instance or post ID.
     *
     * @return array|WP_Post|null
     *
     * @throws OutOfRangeException If the argument is an ID and no post with the given ID was found.
     */
    protected function normalizeWpPost($postOrId)
    {
        $post = ($postOrId instanceof WP_Post) ? $postOrId : get_post($postOrId);

        if (!($post instanceof WP_Post)) {
            throw new OutOfRangeException(
                sprintf(__('Post with ID %s does not exist', 'wprss'), $post)
            );
        }

        return $post;
    }
}
