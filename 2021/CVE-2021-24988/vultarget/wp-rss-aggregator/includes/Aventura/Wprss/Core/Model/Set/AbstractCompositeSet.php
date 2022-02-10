<?php

namespace Aventura\Wprss\Core\Model\Set;

/**
 * Common functionality for sets that contain other collection descendants.
 *
 * @since 4.10
 */
abstract class AbstractCompositeSet extends AbstractGenericSet
{
    /**
     * Get items from all internal sets.
     *
     * @since 4.10
     *
     * @return mixed[]|\Traversable A list of unique items from all sets in this set.
     */
    abstract protected function _getAllItems();
}