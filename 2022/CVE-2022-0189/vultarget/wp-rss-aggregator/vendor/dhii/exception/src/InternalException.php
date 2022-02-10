<?php

namespace Dhii\Exception;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Represents an internal problem, i.e. that which is not caused by the consumer.
 *
 * @since [*next-version*]
 */
class InternalException extends RootException implements InternalExceptionInterface
{
    /*
     * Functionality common to exceptions
     *
     * @since [*next-version*]
     */
    use ExceptionTrait;

    /**
     * @since [*next-version*]
     *
     * @param string|Stringable|int|float|bool|null $message  The message, if any.
     * @param int|float|string|Stringable|null      $code     The numeric error code, if any.
     * @param RootException                         $previous The inner exception.
     */
    public function __construct($message = null, $code = null, RootException $previous = null)
    {
        if (is_null($previous)) {
            throw $this->_createInvalidArgumentException($this->__('Internal exceptions require an inner exception'));
        }

        $this->_initBaseException($message, $code, $previous);
        $this->_construct();
    }

    /**
     * Parameter-less constructor.
     *
     * Invoke this in actual constructor.
     *
     * @since [*next-version*]
     */
    protected function _construct()
    {
    }

    /**
     * Calls the parent constructor.
     *
     * @param string        $message  The error message.
     * @param int           $code     The error code.
     * @param RootException $previous The inner exception, if any.
     *
     * @since [*next-version*]
     */
    protected function _initParent($message = '', $code = 0, RootException $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
