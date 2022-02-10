<?php

namespace Dhii\Exception;

use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use OutOfRangeException as BaseOutOfRangeException;

/**
 * A standards-compliant Out of Range exception implementation.
 *
 * @since [*next-version*]
 */
class OutOfRangeException extends BaseOutOfRangeException implements OutOfRangeExceptionInterface
{
    /*
     * Adds argument awareness.
     *
     * @since [*next-version*]
     */
    use SubjectAwareTrait;

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
     * @param RootException|null                    $previous The inner exception, if any.
     * @param mixed|null                            $argument The argument value, if any.
     */
    public function __construct($message = null, $code = null, RootException $previous = null, $argument = null)
    {
        $this->_initBaseException($message, $code, $previous);
        $this->_setSubject($argument);

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
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getSubject()
    {
        return $this->_getSubject();
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
