<?php

namespace RebelCode\Wpra\Core\Handlers\GutenbergBlock;

/**
 * Class for retrieving feed sources in Gutenberg block.
 *
 * @since 4.13
 */
class FetchFeedSourcesHandler
{
    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke()
    {
        $feed_sources = get_posts(array(
            'post_type' => 'wprss_feed',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'no_found_rows' => true
        ));

        $data = [
            'items' => array_map(function ($feedItem) {
                return (object)[
                    'value' => $feedItem->ID,
                    'title' => $feedItem->post_title,
                ];
            }, $feed_sources)
        ];

        echo json_encode($data);
        wp_die();
    }
}
