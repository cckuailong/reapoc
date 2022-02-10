<?php

namespace Dhii\Exception;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for creating Dhii invalid argument exceptions.
 *
 * @since [*next-version*]
 */
trait CreateInvalidArgumentExceptionCapableTrait
{
    /**
     * Creates a new Dhii invalid argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     * @param mixed|null                            $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    protected function _createInvalidArgumentException(
            $message = null,
            $code = null,
            RootException $previous = null,
            $argument = null
    ) {
        return new InvalidArgumentException($message, $code, $previous, $argument);
    }
}
