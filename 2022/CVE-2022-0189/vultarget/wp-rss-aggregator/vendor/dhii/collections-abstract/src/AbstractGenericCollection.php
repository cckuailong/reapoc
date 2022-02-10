<?php

namespace Dhii\Collection;

/**
 * A default implementation of a general purpose collection.
 *
 * @since 0.1.0
 */
abstract class AbstractGenericCollection extends AbstractSearchableCollection
{
    /**
     * @since 0.1.0 
     *
     * @param mixed[]|\Traversable $items The items to populate this collection with.
     */
    public function __construct($items = null)
    {
        if (!is_null($items)) {
            $this->_addItems($items);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1.0 
     */
    public function _validateItem($item)
    {
        return true;
    }
}
