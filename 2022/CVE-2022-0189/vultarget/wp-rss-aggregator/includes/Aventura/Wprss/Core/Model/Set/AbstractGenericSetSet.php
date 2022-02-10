<?php

namespace Aventura\Wprss\Core\Model\Set;

/**
 * A base set for sets of sets that expose relevant interfaces.
 *
 * @since 4.10
 */
class AbstractGenericSetSet extends AbstractSetSet implements SetSetInterface
{
    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    protected function _createAllItemsSet($items)
    {
        $set = new Set();
        $set->addMany($items);

        return $set;
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function getAllItems()
    {
        return $this->_getAllItems();
    }

    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    public function getContaining($item)
    {
        return $this->_getContaining($item);
    }
}
