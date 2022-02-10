<?php

namespace Dhii\Collection;

use UnexpectedValueException;

/**
 * Something that can iterate over its items using a callback.
 *
 * @since 0.1.0
 */
interface CallbackIterableInterface
{
    /**
     * Do something with each element of the collection.
     *
     * @since 0.1.0
     *
     * @param callable $callback The callback to apply to each element of the collection.
     *                           See {@see CallbackIteratorInterface::getCallback()} for callback specification.
     * @param mixed[]|\Traversable A list of items. Default: this collection's items.
     *
     * @throws UnexpectedValueException If the callback is not callable.
     *
     * @return CallbackIterator The iterator that will apply the callback to each element.
     */
    public function each($callback);
}
