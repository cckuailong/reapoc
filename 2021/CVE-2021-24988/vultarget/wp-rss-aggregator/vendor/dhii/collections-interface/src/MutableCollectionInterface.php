<?php

namespace Dhii\Collection;

/**
 * Something that can act as a collection which can have its item set changed.
 *
 * @since 0.1.0
 */
interface MutableCollectionInterface
{
    /**
     * Adds an item to this collection.
     *
     * @param mixed $item An item to add to the collection.
     *
     * @since 0.1.0
     *
     * @return bool True if item has been added; false otherwise.
     */
    public function addItem($item);

    /**
     * Adds multiple items to this collection.
     *
     * @param mixed[]|\Traversable $items A list of items to add.
     *
     * @since 0.1.0
     *
     * @return bool True if items have been added; false otherwise.
     */
    public function addItems($items);

    /**
     * Removes an item from the collection.
     *
     * @param mixed $item The item to remove from the collection.
     *
     * @since 0.1.0
     *
     * @return bool True if item has been removed; false otherwise.
     */
    public function removeitem($item);

    /**
     * Removes an item with the specified key from the collection.
     *
     * @param string|int $key The key of an item to remove.
     *
     * @since 0.1.0
     *
     * @return bool True if item has been removed; false otherwise.
     */
    public function removeItemByKey($key);
}
