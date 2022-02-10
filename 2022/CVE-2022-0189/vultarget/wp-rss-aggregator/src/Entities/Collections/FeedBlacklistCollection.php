<?php

namespace RebelCode\Wpra\Core\Entities\Collections;

use RebelCode\Entities\Api\SchemaInterface;

/**
 * A collection implementation that is specific to WP RSS Aggregator blacklisted items.
 *
 * @since 4.17
 */
class FeedBlacklistCollection extends WpEntityCollection
{
    /**
     * Constructor.
     *
     * @since 4.17
     *
     * @param string          $postType The name of the post type.
     * @param SchemaInterface $schema   The schema for feed item entities.
     */
    public function __construct($postType, SchemaInterface $schema)
    {
        parent::__construct($postType, $schema);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.17
     */
    protected function getBasePostQueryArgs()
    {
        $args = parent::getBasePostQueryArgs();
        $args['post_status'] = 'publish';

        return $args;
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to ensure that the status is "publish".
     *
     * @since 4.17
     */
    protected function getNewPostData($data)
    {
        $post = parent::getNewPostData($data);
        $post['post_status'] = 'publish';

        return $post;
    }
}
