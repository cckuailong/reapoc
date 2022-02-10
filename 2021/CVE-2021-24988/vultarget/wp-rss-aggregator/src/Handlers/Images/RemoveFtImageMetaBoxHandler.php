<?php

namespace RebelCode\Wpra\Core\Handlers\Images;

/**
 * The handler that removes the WordPress featured image meta box.
 *
 * @since 4.14
 */
class RemoveFtImageMetaBoxHandler
{
    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function __invoke()
    {
        // Removes the 'Featured Image' meta box
        remove_meta_box('postimagediv', 'wprss_feed', 'side');
        // Removes the hook that E&T uses to add the same meta box
        remove_action('do_meta_boxes', 'wprss_et_feed_default_thumbnail_metabox');
    }
}
