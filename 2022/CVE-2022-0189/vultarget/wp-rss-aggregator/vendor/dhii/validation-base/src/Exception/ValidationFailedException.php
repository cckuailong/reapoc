<?php

namespace Dhii\Validation\Exception;

use Dhii\Exception\ExceptionTrait;
use Dhii\Util\Normalization\NormalizeIterableCapableTrait;
use Traversable;
use Exception as RootException;
use RangeException;
use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Validation\ValidationSubjectAwareTrait;
use Dhii\Validation\ValidatorAwareTrait;
use Dhii\Validation\ValidationErrorsAwareTrait;
use Dhii\Validation\ValidatorInterface;

/**
 * Represents an exception that occurs when a subject is determined to be invalid.
 *
 * @since 0.1
 */
class ValidationFailedException extends RangeException implements ValidationFailedExceptionInterface
{
    /*
     * Adds validation subject awareness.
     *
     * @since [*next-version*]
     */
    use ValidationSubjectAwareTrait;

    /*
     * Adds validator awareness.
     *
     * @since [*next-version*]
     */
    use ValidatorAwareTrait;

    /*
     * Adds validation errors awareness.
     *
     * @since [*next-version*]
     */
    use ValidationErrorsAwareTrait;

    /*
     * Common exception functionality.
     *
     * @since [*next-version*]
     */
    use ExceptionTrait;

    /* Normalization of iterables.
     *
     * @since [*next-version*]
     */
    use NormalizeIterableCapableTrait;

    /**
     * @since 0.1
     *
     * @param string|Stringable|null                 $message          The error message, if any.
     * @param int|null                               $code             The error code, if any.
     * @param RootException|null                     $previous         The inner exception, if any.
     * @param ValidatorInterface|null                $validator        The validator, if any.
     * @param mixed|null                             $subject          The validation subject, if any.
     * @param string[]|Stringable[]|Traversable|null $validationErrors The validation errors to associate with this instance.
     */
    public function __construct($message = null, $code = null, RootException $previous = null, $validator = null, $subject = null, $validationErrors = null)
    {
        $this->_initBaseException($message, $code, $previous);

        $this->_setValidator($validator);
        $this->_setValidationSubject($subject);
        $this->_setValidationErrors($validationErrors);
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

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getSubject()
    {
        return $this->_getValidationSubject();
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getValidationErrors()
    {
        return $this->_getValidationErrors();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getValidator()
    {
        return $this->_getValidator();
    }
}
