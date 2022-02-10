<?php

namespace Dhii\Validation;

use Dhii\Validation\Exception\ValidationFailedException;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use stdClass;
use Traversable;

/**
 * Functionality for creating Validation exceptions.
 *
 * @since [*next-version*]
 */
trait CreateValidationFailedExceptionCapableTrait
{
    /**
     * Creates a new validation exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null                 $message          The message, if any. Defaults to the first validation error, if available.
     * @param int|null                               $code             The error code, if any.
     * @param RootException|null                     $previous         The inner exception, if any.
     * @param ValidatorInterface|null                $validator        The validator which triggered the exception, if any.
     * @param mixed|null                             $subject          The subject that has failed validation, if any.
     * @param string[]|Stringable[]|Traversable|null $validationErrors The errors that are to be associated with the new exception, if any.
     *
     * @return ValidationFailedException The new exception.
     */
    protected function _createValidationFailedException(
        $message = null,
        $code = null,
        RootException $previous = null,
        ValidatorInterface $validator = null,
        $subject = null,
        $validationErrors = null
    ) {
        if (is_null($message)) {
            try {
                $validationErrors = $this->_normalizeIterable($validationErrors);
                $error            = null;
                foreach ($validationErrors as $_idx => $error) {
                    break;
                }
                $message = $this->_normalizeString($error);
            } catch (RootException $e) {
                // Do nothing
            }
        }

        return new ValidationFailedException($message, $code, $previous, $validator, $subject, $validationErrors);
    }

    /**
     * Normalizes an iterable.
     *
     * Makes sure that the return value can be iterated over.
     *
     * @since [*next-version*]
     *
     * @param mixed $iterable The iterable to normalize.
     *
     * @throws InvalidArgumentException If the iterable could not be normalized.
     *
     * @return array|Traversable|stdClass The normalized iterable.
     */
    abstract protected function _normalizeIterable($iterable);

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see StringableInterface).
     *
     * @since [*next-version*]
     *
     * @param Stringable|string|int|float|bool $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);
}
