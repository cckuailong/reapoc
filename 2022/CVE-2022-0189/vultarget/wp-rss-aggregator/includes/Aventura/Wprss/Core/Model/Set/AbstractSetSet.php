<?php

namespace Aventura\Wprss\Core\Model\Set;

use Dhii\Collection;

/**
 * Common functionality for sets of sets.
 *
 * @since 4.10
 */
abstract class AbstractSetSet extends AbstractCompositeSet
{
    /**
     * @inheritdoc
     *
     * @since 4.10
     */
    protected function _getAllItems()
    {
        $items = array();

        foreach ($this->_getItems() as $_set) {
            /* @var $_set Collection\SetInterface */
            $items = array_merge($items, $_set->items());
        }

        return $this->_createAllItemsSet($items);
    }

    /**
     * Creates a set that will hold items from all internal sets.
     *
     * @since 4.10
     *
     * @return Collection\SetInterface A set to be populated by items from all internal sets.
     */
    abstract protected function _createAllItemsSet($items);

    /**
     * Search evaluator to find sets that contain a certain item.
     *
     * @since 4.10
     *
     * @param Collection\SetInterface $set The set to check in.
     * @param mixed $item The item to check for.
     *
     * @return Collection\SetInterface|null The set, if it contains item; null otherwise.
     */
    public function _evalSetByHavingItem($idx, Collection\SetInterface $set, &$isContinue, $item)
    {
        return $set->has($item)
            ? $set
            : null;
    }

    /**
     * Gets a list of internal sets that contain the given item.
     *
     * @since 4.10
     *
     * @return Collection\SetInterface[]|\Traversable The list of internal sets with the matching item.
     */
    protected function _getContaining($item)
    {
        $me = $this;
        return $this->_search(function($idx, $set, &$isContinue) use ($item, $me) {
            return $me->_evalSetByHavingItem($idx, $set, $isContinue, $item);
        });
    }
}
