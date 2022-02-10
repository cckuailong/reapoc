<?php

namespace Dhii\Collection;

/**
 * Something that can act as a searchable collection.
 *
 * @since 0.1.0
 */
interface SearchableCollectionInterface
{
    /**
     * Search this collection for items using custom criteria.
     *
     * @param callable           $eval  A callable that evaluates an item to determine whether it matches a criteria.
     *                                  This callable must return the item passed as the second argument, the validity of which will be evaluated by {@see _isValidItem()}, if it is a match.
     *                                  The third argument is a boolean value passed by reference which, if set to false, will prevent any further evaluation, and cause the search to stop.
     *                                  The first argument is the key of the item being evaluated.
     * @param array|\Traversable $items
     *
     * @return mixed[]|\Traversable A list of items which represents the search result.
     */
    public function search($eval);
}
