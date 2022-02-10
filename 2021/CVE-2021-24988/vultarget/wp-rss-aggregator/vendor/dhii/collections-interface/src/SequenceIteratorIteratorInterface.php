<?php

namespace Dhii\Collection;

use OuterIterator;

/**
 * Something that can act as an iterator which iterates sequentially over elemnts in other aggregators.
 *
 * Based on {@see \AppendIterator}. However, does not require a public {@link \AppendIterator::append() append()}
 * method, thus allowing iteration over a read-only list of iterators.
 *
 * @since 0.1.0
 */
interface SequenceIteratorIteratorInterface extends OuterIterator
{
    /**
     * @see \AppendIterator::getArrayIterator()
     * @since 0.1.0
     */
    public function getArrayIterator();

    /**
     * @see \AppendIterator::getIteratorIndex()
     * @since 0.1.0
     */
    public function getIteratorIndex();
}
