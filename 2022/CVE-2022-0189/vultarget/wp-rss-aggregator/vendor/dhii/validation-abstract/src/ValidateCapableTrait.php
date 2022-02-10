<?php

namespace Dhii\Validation;

use Dhii\Validation\Exception\ValidationExceptionInterface;
use stdClass;
use Traversable;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use Dhii\Validation\Exception\ValidationFailedExceptionInterface;

/**
 * Functionality for validation.
 *
 * @since [*next-version*]
 */
trait ValidateCapableTrait
{
    /**
     * Validates a subject.
     *
     * @since [*next-version*]
     *
     * @param mixed $subject The value to validate.
     *
     * @throws ValidationFailedExceptionInterface If subject is invalid.
     * @throws RootException                      If problem validating.
     */
    protected function _validate($subject)
    {
        $errors = $this->_getValidationErrors($subject);

        if (!$this->_countIterable($errors)) {
            return;
        }

        throw $this->_throwValidationFailedException($this->__('Validation failed'), null, null, true, $subject, $errors);
    }

    /**
     * Retrieve a list of reasons that make the subject invalid.
     *
     * An empty list means that the subject is valid.
     *
     * @since [*next-version*]
     *
     * @param mixed $subject The value to validate.
     *
     * @throws RootException If a problem occurs.
     *
     * @return string[]|Stringable[]|Traversable|stdClass The list of validation errors. Must be finite.
     */
    abstract protected function _getValidationErrors($subject);

    /**
     * Retrieves the number of elements in the iterable.
     *
     * Does not guarantee that the internal pointer of the iterable is preserved.
     *
     * @param array|Traversable $iterable The iterable to count.
     *
     * @since [*next-version*]
     *
     * @return int The count.
     */
    abstract protected function _countIterable($iterable);

    /**
     * Throws a Validation Failed exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null                 $message          The error message, if any.
     * @param int|null                               $code             The error code, if any.
     * @param RootException|null                     $previous         The inner exception, if any.
     * @param ValidatorInterface|null|bool           $validator        The validator which validated the subject, if any.
     *                                                                 Pass `true` to attempt a late validator substitution.
     * @param mixed|null                             $subject          The subject that has failed validation, if any.
     * @param string[]|Stringable[]|Traversable|null $validationErrors The errors that are to be associated with the new exception, if any.
     *
     * @throws ValidationFailedExceptionInterface
     */
    abstract protected function _throwValidationFailedException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $validator = null,
        $subject = null,
        $validationErrors = null
    );

    /**
     * Throws a Validation exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null       $message   The error message, if any.
     * @param int|null                     $code      The error code, if any.
     * @param RootException|null           $previous  The inner exception, if any.
     * @param ValidatorInterface|null|bool $validator The validator which validated the subject, if any.
     *                                                Pass `true` to attempt a late validator substitution.
     *
     * @throws ValidationExceptionInterface
     */
    abstract protected function _throwValidationException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $validator = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = array(), $context = null);
}
