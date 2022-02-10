<?php

namespace RebelCode\Wpra\Core\Entities\Collections;

use RebelCode\Entities\Api\SchemaInterface;
use RuntimeException;

/**
 * A collection implementation for all items imported by WP RSS Aggregator.
 *
 * @since 4.13
 */
class ImportedItemsCollection extends FeedItemCollection
{
    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param array           $metaQuery The meta query.
     * @param SchemaInterface $schema    The schema for imported item entities.
     */
    public function __construct($metaQuery, SchemaInterface $schema)
    {
        parent::__construct(null, $schema);

        $this->metaQuery = $metaQuery;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function set($key, $data)
    {
        throw new RuntimeException('Cannot write to imported items collection');
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function getBasePostQueryArgs()
    {
        $args = parent::getBasePostQueryArgs();

        $args['post_type'] = get_post_types();
        $args['post_status'] = 'publish';

        return $args;
    }
}
