<?php

namespace Dhii\Exception;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for creating Dhii Out of Bounds exceptions.
 *
 * @since [*next-version*]
 */
trait CreateOutOfBoundsExceptionCapableTrait
{
    /**
     * Creates a new Dhii Out Of Bounds exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     * @param mixed|null                            $argument The value that is out of bounds, if any.
     *
     * @return OutOfBoundsException The new exception.
     */
    protected function _createOutOfBoundsException(
            $message = null,
            $code = null,
            RootException $previous = null,
            $argument = null
    ) {
        return new OutOfBoundsException($message, $code, $previous, $argument);
    }
}
