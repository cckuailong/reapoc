<?php

namespace Dhii\Collection;

/**
 * Common functionality for callback collections.
 *
 * Ready to be extended and instantiated, with minimal or no modifications.
 *
 * @since 0.1.0
 */
abstract class AbstractCallbackCollection extends AbstractCallbackCollectionBase implements CallbackIterableInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 0.1.0
     */
    public function each($callback)
    {
        return $this->_each($callback, $this);
    }
}
