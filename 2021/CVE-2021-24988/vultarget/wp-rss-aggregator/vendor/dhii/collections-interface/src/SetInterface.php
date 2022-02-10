<?php

namespace Dhii\Collection;

/**
 * Something that can behave as a set with complete functionality.
 *
 * @since 0.1.2
 */
interface SetInterface extends \Countable
{
    /**
     * Adds an item.
     *
     * @param mixed $item An item to add.
     *
     * @since 0.1.2
     *
     * @return bool True if item has been added; false otherwise.
     */
    public function add($item);

    /**
     * Adds multiple items.
     *
     * @param mixed[]|\Traversable $values A list of items to add.
     *
     * @since 0.1.2
     *
     * @return bool True if items have been added; false otherwise.
     */
    public function addMany($values);

    /**
     * Removes an item.
     *
     * @param mixed $item The item to remove.
     *
     * @since 0.1.2
     *
     * @return bool True if item has been removed; false otherwise.
     */
    public function remove($item);

    /**
     * Removes multiple items.
     *
     * @since 0.1.2
     *
     * @param mixed[]|\Traversable $items The items to remove
     */
    public function removeMany($items);

    /**
     * Checks whether this instance contains an item with the given key.
     *
     * @since 0.1.2
     *
     * @param string|int $key The key to check for.
     *
     * @return bool True if an item witht he specified key exists in this instance; false otherwise.
     */
    public function has($key);

    /**
     * Clears all items of this collection.
     *
     * @since 0.1.2
     */
    public function clear();

    /**
     * Retrieve the items of the collection.
     *
     * This method may return an array. However, this is not a reliable way to convert a collection
     * to an array. For that purpose, use {@see iterator_to_array()}.
     *
     * @since 0.1.2
     *
     * @return mixed[]|\Traversable The list of items, by original key.
     */
    public function items();
}
