<?php

namespace Aventura\Wprss\Core\Model\Set;

use Dhii\Collection;

/**
 * Something that can act as a set of sets.
 *
 * @since 4.10
 */
interface SetSetInterface extends SetInterface
{
    /**
     * Returns a list of items from all internal sets.
     *
     * @since 4.10
     *
     * @return mixed[]|\Traversable A list of items from all sets in this instance.
     */
    public function getAllItems();

    /**
     * Gets internal sets that contain a specific item.
     *
     * @since 4.10
     *
     * @param Collection\SetInterface[]|\Traversable $item A list of internal sets that contain the given item.
     */
    public function getContaining($item);
}
