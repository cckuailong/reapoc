<?php

namespace RebelCode\Wpra\Core\Entities\Collections;

use RebelCode\Entities\Api\SchemaInterface;

/**
 * A collection implementation that is specific to WP RSS Aggregator feed sources.
 *
 * @since 4.14
 */
class FeedSourceCollection extends WpEntityCollection
{
    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param string          $postType The name of the post type.
     * @param SchemaInterface $schema   The entity schema to use for created entities.
     */
    public function __construct($postType, SchemaInterface $schema)
    {
        parent::__construct($postType, $schema);
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to ensure that only "published" feed sources are queried.
     *
     * @since 4.14
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
     * Overridden to ensure that new feed sources are "published".
     *
     * @since 4.14
     */
    protected function getNewPostData($data)
    {
        $postData = parent::getNewPostData($data);
        $postData['post_status'] = 'publish';

        return $postData;
    }
}
