<?php

namespace Dhii\Collection;

/**
 * Common functionality for collections that can have its item set changed.
 *
 * @since 0.1.0
 */
abstract class AbstractGenericMutableCollection extends AbstractGenericCollection implements MutableCollectionInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 0.1.0
     */
    public function addItem($item)
    {
        $result = $this->_addItem($item);
        $this->_clearItemCache();
        $this->_resetStats();

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1.0
     */
    public function addItems($items)
    {
        $result = $this->_addItems($items);
        $this->_clearItemCache();
        $this->_resetStats();

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1.0
     */
    public function removeItem($item)
    {
        $result = $this->_removeItem($item);
        $this->_clearItemCache();
        $this->_resetStats();

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1.0
     */
    public function removeItemByKey($key)
    {
        $result = $this->_removeItem($item);

        return $result;
    }
}
