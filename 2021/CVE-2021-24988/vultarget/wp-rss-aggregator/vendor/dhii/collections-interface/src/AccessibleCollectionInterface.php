<?php

namespace Dhii\Collection;

/**
 * Something that can act as a collection, the individual items of which can be accessed.
 *
 * @since 0.1.0
 */
interface AccessibleCollectionInterface
{
    /**
     * Checks whether this collection contains the given item.
     *
     * @since 0.1.0
     *
     * @param mixed $item The item to check for.
     *
     * @return bool True if the given item exists in this collection; false otherwise.
     */
    public function hasItem($item);

    /**
     * Checks whether this collection contains an item with the given key.
     *
     * @since 0.1.0
     *
     * @param string|int $key The key to check for.
     *
     * @return bool True if an item witht he specified key exists in this collcetion; false otherwise.
     */
    public function hasItemKey($key);

    /**
     * Retrieves an item with the specified key.
     *
     * @since 0.1.0
     *
     * @param string|int $key The key of the item to retrieve.
     *
     * @return mixed The item, if key is found; null otherwise.
     */
    public function getItem($key);
}
