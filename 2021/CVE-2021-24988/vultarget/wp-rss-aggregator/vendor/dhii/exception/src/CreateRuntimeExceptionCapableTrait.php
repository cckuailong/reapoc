<?php

namespace Dhii\Exception;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for creating Runtime exceptions.
 *
 * @since [*next-version*]
 */
trait CreateRuntimeExceptionCapableTrait
{
    /**
     * Creates a new Runtime exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     *
     * @return RuntimeException The new exception.
     */
    protected function _createRuntimeException($message = null, $code = null, $previous = null)
    {
        return new RuntimeException($message, $code, $previous);
    }
}
