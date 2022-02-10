<?php

namespace Dhii\Exception;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * The most basic exception.
 *
 * @since [*next-version*]
 */
class Exception extends AbstractBaseException
{
    /**
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException|null                    $previous The inner exception, if any.
     */
    public function __construct($message = null, $code = null, RootException $previous = null)
    {
        $this->_initBaseException($message, $code, $previous);
        $this->_construct();
    }
}
