<?php

namespace Aventura\Wprss\Core\Model\Collection;

use Dhii\Collection\AbstractSearchableCollection;

/**
 * Common functionality for all collections.
 *
 * @since 4.10
 */
abstract class AbstractCollection extends AbstractSearchableCollection
{
    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    protected function _construct()
    {
        $this->_clearItems();
        $this->_clearItemCache();
    }

    /**
     * Returns the item set of this instance to its initial state.
     *
     * @since 4.10
     */
    protected function _clearItems()
    {
        $this->items = array();
    }
}
