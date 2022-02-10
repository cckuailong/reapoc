<?php

namespace Dhii\Collection;

use Exception;
use RuntimeException;
use UnexpectedValueException;

/**
 * Common functionality for collections.
 *
 * This implementation permits the same value to appear multiple times.
 *
 * @since 0.1.0
 */
abstract class AbstractCollection extends AbstractHasher implements CollectionInterface
{
    protected $items;

    /**
     * Parameter-less constructor.
     *
     * The actual constructor MUST call this method.
     *
     * @since 0.1.0
     */
    protected function _construct()
    {
        $this->items = array();
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1.0
     */
    public function getItems()
    {
        return $this->_getItems();
    }

    /**
     * Low-level retrieval of all items.
     *
     * @since 0.1.0
     *
     * @return mixed[]|\Traversable
     */
    protected function _getItems()
    {
        return $this->items;
    }

    /**
     * Adds items to the collection.
     *
     * @since 0.1.0
     *
     * @param array|\Traversable $items Items to add.
     *
     * @return AbstractCollection This instance.
     */
    protected function _addItems($items)
    {
        foreach ($items as $_key => $_item) {
            $this->_validateItem($_item);
            $this->_addItem($_item);
        }

        return $this;
    }

    /**
     * Add an item to the collection.
     *
     * @since 0.1.0
     *
     * @param mixed $item The item to add.
     *
     * @return bool True if item successfully added; false if adding failed.
     */
    protected function _addItem($item)
    {
        $key = $this->_getItemUniqueKey($item);

        return $this->_arraySet($this->items, $item, $key);
    }

    /**
     * Sets an item at the specified key in this collection.
     *
     * @since 0.1.0
     *
     * @param string $key  The key, at which to set the item
     * @param mixed  $item The item to set.
     *
     * @return bool True if item successfully set; false if setting failed.
     */
    protected function _setItem($key, $item)
    {
        return $this->_arraySet($this->items, $item, $key);
    }

    /**
     * Set the internal items list.
     *
     * The internal list will be replaced with the one given.
     *
     * @since 0.1.0
     *
     * @param array|\Traversable $items The item list to set.
     *
     * @return AbstractCollection This instance.
     */
    protected function _setItems($items)
    {
        $this->_validateItemList($items);
        $this->items = $items;

        return $this;
    }

    /**
     * Removes the given item from this collection.
     *
     * @since 0.1.0
     *
     * @param mixed $item The item to remove.
     *
     * @return bool True if removal successful; false if failed.
     */
    protected function _removeItem($item)
    {
        if (($key = $this->_findItem($item, true)) !== false) {
            return $this->_arrayUnset($this->items, $key);
        }

        return false;
    }

    /**
     * Checks whether the given item exists in this collection.
     *
     * @since 0.1.0
     *
     * @param mixed $item The item to check for.
     *
     * @return bool True if the given item exists in this collection; false otherwise.
     */
    protected function _hasItem($item)
    {
        return $this->_findItem($item, true) !== false;
    }

    /**
     * Checks whether an item with the specified key exists in this collection.
     *
     * @since 0.1.0
     *
     * @param int|string $key The key to check for.
     *
     * @return bool True if the key exists in this collection; false otherwise.
     */
    protected function _hasItemKey($key)
    {
        return $this->_arrayKeyExists($this->items, $key);
    }

    /**
     * Retrieve an item with the specified key from this collection.
     *
     * @since 0.1.0
     *
     * @param string|int $key     The key to get an item for.
     * @param mixed      $default The value to return if the specified key does not exists.
     *
     * @return mixed|null The item at the specified key, if it exists; otherwise, the default value.
     */
    protected function _getItem($key, $default = null)
    {
        return $this->_arrayGet($this->items, $key, $default);
    }

    /**
     * Get the key of an item to use for consistency checks.
     *
     * @since 0.1.0
     *
     * @param mixed $item Get the key of an item.
     *
     * @return string|int The key of an item.
     */
    protected function _getItemKey($item)
    {
        return $this->_hash($item);
    }

    /**
     * Get the index, at which an item exists in this collection.
     *
     * @since 0.1.0
     *
     * @param mixed $item   The item to find.
     * @param bool  $strict Whether or not the type must also match.
     *
     * @return int|string|false The key, at which the item exists in this collection, if found;
     *                          false otherwise.
     */
    public function _findItem($item, $strict = false)
    {
        return $this->_arraySearch($this->items, $item, $strict);
    }

    /**
     * Get a collection-wide unique key for an item.
     *
     * It is not guaranteed to be consistent, e.g. running this several
     * times on the same argument will likely produce different results.
     *
     * @param mixed $item The item, for which to get the key.
     *
     * @return string|int A key that is guaranteed to be different from all other keys in this collection.
     */
    protected function _getItemUniqueKey($item)
    {
        return count($this->items);
    }

    /**
     * Search a list for a value.
     *
     * @since 0.1.0
     *
     * @param AbstractCollection|array|\Traversable $array  The list to search.
     * @param mixed                                 $value  The value to search for.
     * @param bool                                  $strict Whether the type must also match.
     *
     * @return int|string|bool The key, at which the value exists in the list, if found;
     *                         false otherwise.
     */
    protected function _arraySearch(&$array, $value, $strict = false)
    {
        // Regular array matching
        if (is_array($array)) {
            return array_search($value, $array, $strict);
        }
        // Using familiar interface
        if ($array instanceof self) {
            return $array->_findItem($value, $strict);
        }
        // Last resort - iterate and compare
        if ($array instanceof \Traversable) {
            foreach ($array as $_idx => $_value) {
                if ($strict && $value === $_value) {
                    return $_idx;
                }

                if (!$strict && $value == $_value) {
                    return $_idx;
                }
            }

            return false;
        }

        throw new RuntimeException('Could not search list: the list is not something that can be searched');
    }

    /**
     * Checks if an item with the specified key exists in a list.
     *
     * @since 0.1.0
     *
     * @param array|\ArrayAccess|AccessibleCollectionInterface $list The list to check.
     * @param string|int                                       $key  The key to check for.
     *
     * @throws RuntimeException If list is not something that can have a key checked.
     *
     * @return bool True if an item with the specified key exists the given list; otherwise false.
     */
    protected function _arrayKeyExists(&$list, $key)
    {
        if (is_array($list)) {
            return array_key_exists($key, $list);
        }

        if ($list instanceof \ArrayAccess) {
            return $list->offsetExists($key);
        }

        if ($list instanceof AccessibleCollectionInterface) {
            return $list->hasItemKey($key);
        }

        throw new RuntimeException(sprintf(
            'Could not check list for key "%1$s": the list is not something that can have an item checked', $key));
    }

    /**
     * Retrieves an item with the specified key from the given list.
     *
     * @since 0.1.0
     *
     * @param array|\ArrayAccess|AccessibleCollectionInterface $list The list to retrieve from.
     * @param string|int                                       $key  The key to retrieve the item for.
     *
     * @throws RuntimeException If list is not something that can have a value retrieved by key.
     *
     * @return mixed|null The item at the specified key.
     */
    protected function _arrayGet(&$list, $key, $default = null)
    {
        if (is_array($list)) {
            return isset($list[$key])
                ? $list[$key]
                : $default;
        }

        if ($list instanceof \ArrayAccess) {
            return $list->offsetExists($key)
                ? $list->offsetGet($key)
                : $default;
        }

        if ($list instanceof AccessibleCollectionInterface) {
            return $list->hasItemKey($key)
                ? $list->getItem($key)
                : $default;
        }

        throw new RuntimeException(sprintf(
            'Could not get list item for key "%1$s": the list is not something that can have an item retrieved', $key));
    }

    /**
     * Set an item at the specified key in the given list.
     *
     * @since 0.1.0
     *
     * @param mixed[]|\ArrayAccess|MutableCollectionInterface $list The list, for which to set the value.
     * @param mixed                                           $item The item to set for the specified key.
     * @param string                                          $key  The key, for which to set the item.
     *
     * @throws RuntimeException If list is not something that can have a value set.
     *
     * @return bool True if the value has been successfully set; false if setting failed.
     */
    protected function _arraySet(&$list, $item, $key)
    {
        if (is_array($list)) {
            $list[$key] = $item;

            return true;
        }

        if ($list instanceof \ArrayAccess) {
            $list->offsetSet($key, $item);

            return true;
        }

        if ($list instanceof MutableCollectionInterface) {
            return $list->setItem($item, $key);
        }

        throw new RuntimeException(sprintf(
            'Could not set list item  for key "%1$s": the list is not something that can have an item set', $key));
    }

    /**
     * Unset the specified key in the given list.
     *
     * @since 0.1.0
     *
     * @param mixed[]|\ArrayAccess|MutableCollectionInterface $list The list, for which to set the value.
     * @param string                                          $key  The key, for which to unset the item.
     *
     * @throws RuntimeException If list is not something that can have a value unset.
     *
     * @return bool True if the value has been successfully unset; false if unsetting failed.
     */
    protected function _arrayUnset(&$array, $key)
    {
        if (is_array($array)) {
            if (isset($array[$key])) {
                unset($array[$key]);

                return true;
            }

            return false;
        }

        if ($array instanceof \ArrayAccess) {
            if ($array->offsetExists($key)) {
                $array->offsetUnset($key);

                return true;
            }

            return false;
        }

        if ($array instanceof MutableCollectionInterface) {
            return $array->removeItemByKey($key);
        }

        throw new RuntimeException(sprintf(
            'Could not unset list item for key "%1$s": the list is not something that can have an item unset', $key));
    }

    /**
     * Normalize an array-ish value to array.
     *
     * @since 0.1.0
     *
     * @param array|AbstractCollection|\Traversable $list The list, which to convert.
     *
     * @throws RuntimeException If list is not something that can have a value unset.
     *
     * @return array The array that resulted from the conversion.
     *  If the argument is an array, returns unmodified.
     *  If it is an {@see AbstractCollection} and not a {@see \Traversable}, gets its internal items and tries to convert those to array.
     *  If it is a {@see \Traversable}, returns the result of {@see iterator_to_array()} on that.
     */
    protected function _arrayConvert(&$list)
    {
        if (is_array($list)) {
            return $list;
        }

        if ($list instanceof self && !($list instanceof \Traversable)) {
            $items = $list->_getItems();
            return $this->_arrayConvert($items);
        }

        if ($list instanceof \Traversable) {
            return iterator_to_array($list, true);
        }

        throw new RuntimeException(sprintf(
            'Could not convert to array: not something that can be converted'));
    }

    /**
     * Determines if item is a valid member of the collection.
     *
     * @since 0.1.0
     *
     * @throws \Exception If the item is invalid;
     */
    abstract protected function _validateItem($item);

    /**
     * Determines if item is a valid member of the collection.
     *
     * @since 0.1.0
     *
     * @param mixed $item The item to evaluate.
     *
     * @return bool True if the item is valid; false otherwise.
     */
    protected function _isValidItem($item)
    {
        try {
            $this->_validateItem($item);
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * Throws an exception if the given value is not a valid item list.
     *
     * @since 0.1.0
     *
     * @param mixed $items An item list.
     *
     * @throws UnexpectedValueException If the list is not a valid item list.
     */
    protected function _validateItemList($items)
    {
        if (!is_array($items) && !($items instanceof \Traversable)) {
            throw new UnexpectedValueException(sprintf('Must be a valid item list'));
        }
    }
}
