<?php

namespace Dhii\Collection;

/**
 * Something that can act like a collection.
 *
 * @since 0.1.0
 */
interface CollectionInterface
{
    /**
     * Retrieve the items of the collection.
     *
     * This method may return an array. However, this is not a reliable way to convert a collection
     * to an array. For that purpose, use {@see iterator_to_array()}.
     *
     * @since 0.1.0
     *
     * @return mixed[]|\Traversable The list of items, by original key.
     */
    public function getItems();
}
