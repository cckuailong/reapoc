<?php

namespace Dhii\Exception;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for creating Internal exceptions.
 *
 * @since [*next-version*]
 */
trait CreateInternalExceptionCapableTrait
{
    /**
     * Creates a new Internal exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     *
     * @return InternalException The new exception.
     */
    protected function _createInternalException($message = null, $code = null, RootException $previous = null)
    {
        return new InternalException($message, $code, $previous);
    }
}
