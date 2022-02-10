<?php

namespace Dhii\Validation;

use Exception as RootException;
use Dhii\Validation\Exception\ValidationException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for creating Validation exceptions.
 *
 * @since [*next-version*]
 */
trait CreateValidationExceptionCapableTrait
{
    /**
     * Creates a new validation exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null  $message   The message, if any
     * @param int|null                $code      The error code, if any.
     * @param RootException|null      $previous  The inner exception, if any.
     * @param ValidatorInterface|null $validator The validator which triggered the exception, if any.
     *
     * @return ValidationException The new exception.
     */
    protected function _createValidationException($message = null, $code = null, RootException $previous = null, ValidatorInterface $validator = null)
    {
        return new ValidationException($message, $code, $previous, $validator);
    }
}
