<?php

namespace Dhii\Validation;

use Dhii\Validation\Exception\ValidationException;
use Dhii\Validation\Exception\ValidationFailedException;
use Dhii\Validation\Exception\ValidationFailedExceptionInterface;
use Exception as RootException;

/**
 * Common functionality for validators.
 *
 * @since 0.1
 */
abstract class AbstractValidatorBase implements ValidatorInterface
{
    /* Common validator dependencies.
     *
     * @since [*next-version*]
     */
    use ValidatorTrait;

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
     * @since 0.1
     */
    public function validate($value)
    {
        try {
            $this->_validate($value);
        } catch (RootException $e) {
            if ($e instanceof ValidationFailedExceptionInterface) {
                throw $e;
            }

            $this->_throwValidationException($this->__('Problem validating'), null, $e, true);
        }
    }

    /**
     * {inheritdoc}.
     *
     * @since [*next-version*]
     *
     * @throws ValidationException
     */
    protected function _throwValidationException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $validator = null
    ) {
        if ($validator === true) {
            $validator = $this;
        }

        throw $this->_createValidationException($message, $code, $previous, $validator);
    }

    /**
     * {inheritdoc}.
     *
     * @since [*next-version*]
     *
     * @throws ValidationFailedException
     */
    protected function _throwValidationFailedException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $validator = null,
        $subject = null,
        $validationErrors = null
    ) {
        if ($validator === true) {
            $validator = $this;
        }

        throw $this->_createValidationFailedException($message, $code, $previous, $validator, $subject, $validationErrors);
    }
}
