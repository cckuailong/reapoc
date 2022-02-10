<?php

namespace Dhii\Exception;

use Exception as RootException;
use InvalidArgumentException as RootInvalidArgumentException;

trait CreateNativeInvalidArgumentExceptionCapableTrait
{
    /**
     * Creates a new invalid argument exception.
     *
     * @since [*next-version*]
     *
     * @param string             $message  The error message, if any.
     * @param int                $code     The error code, if any.
     * @param RootException|null $previous The inner exception for chaining, if any.
     * @param mixed|null         $argument The invalid argument, if any.
     *
     * @return RootInvalidArgumentException The new exception.
     */
    protected function _createInvalidArgumentException(
        $message = '',
        $code = 0,
        RootException $previous = null,
        $argument = null
    ) {
        return new RootInvalidArgumentException($message, $code, $previous);
    }
}
