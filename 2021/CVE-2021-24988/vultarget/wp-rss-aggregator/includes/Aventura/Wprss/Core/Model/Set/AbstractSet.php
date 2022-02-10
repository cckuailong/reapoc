<?php

namespace Aventura\Wprss\Core\Model\Set;

use Aventura\Wprss\Core\Model\Collection;

/**
 * Base functionality for sets.
 *
 * @since 4.10
 */
abstract class AbstractSet extends Collection\AbstractCollection
{
    /**
     * Determine if this instance contains at least one items in the given list.
     *
     * @since 4.10
     *
     * @param mixed[]|\Traversable $items The list of items to for.
     * @return boolean True if this instance contains at least one of the items in the given list; false otherwise.
     */
    protected function _hasOneOf($items)
    {
        foreach ($items as $_item) {
            if ($this->_hasItem($_item)) {
                return true;
            }
        }

        return false;
    }
}
