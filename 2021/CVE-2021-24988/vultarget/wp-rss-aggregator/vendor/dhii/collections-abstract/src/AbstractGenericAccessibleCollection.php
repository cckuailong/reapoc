<?php

namespace Dhii\Collection;

/**
 * Common functionality for generic collections that can have its items retrieved and checked.
 *
 * @since 0.1.0
 */
abstract class AbstractGenericAccessibleCollection extends AbstractGenericCollection implements AccessibleCollectionInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 0.1.0
     */
    public function getItem($key)
    {
        return $this->_getItem($key, null);
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1.0
     */
    public function hasItem($item)
    {
        return $this->_hasItem($item);
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1.0
     */
    public function hasItemKey($key)
    {
        return $this->_hasItemKey($key);
    }
}
