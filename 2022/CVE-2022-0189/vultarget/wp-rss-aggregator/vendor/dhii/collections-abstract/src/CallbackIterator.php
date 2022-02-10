<?php

namespace Dhii\Collection;

/**
 * A default implementation of a callback iterator.
 *
 * Can have values of any type, and of different types.
 *
 * @since 0.1.0
 */
class CallbackIterator extends AbstractCallbackIterator
{
    /**
     * @since 0.1.0
     *
     * @param mixed[]|\Traversable $items    A list of items to iterate over.
     * @param callable             $callback
     */
    public function __construct($items, $callback)
    {
        $this->_setItems($items);
        $this->_setCallback($callback);
    }

    /**
     * Allows any items through.
     *
     * @since 0.1.0
     *
     * @return bool Always true.
     */
    protected function _validateItem($item)
    {
        // Nothing needs to happen in order to indicate that an item is valid.
    }
}
