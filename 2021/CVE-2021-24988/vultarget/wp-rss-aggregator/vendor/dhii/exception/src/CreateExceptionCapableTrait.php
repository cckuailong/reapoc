<?php

namespace Dhii\Exception;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for creating the most basic Dhii exceptions.
 *
 * @since [*next-version*]
 */
trait CreateExceptionCapableTrait
{
    /**
     * Creates a new basic Dhii exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     *
     * @return ThrowableInterface The new exception.
     */
    protected function _createException($message = null, $code = null, $previous = null)
    {
        return new Exception($message, $code, $previous);
    }
}
