<?php

namespace Dhii\Validation;

use Exception as RootException;
use InvalidArgumentException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for retrieving the validator.
 *
 * @since [*next-version*]
 */
trait ValidatorAwareTrait
{
    /**
     * The validator.
     *
     * @since [*next-version*]
     *
     * @var ValidatorInterface|null
     */
    protected $validator;

    /**
     * Retrieves the validator associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return ValidatorInterface The validator.
     */
    protected function _getValidator()
    {
        return $this->validator;
    }

    /**
     * Assigns a validator to this instance.
     *
     * @since [*next-version*]
     *
     * @param ValidatorInterface|null $validator The validator.
     */
    protected function _setValidator($validator)
    {
        if (!is_null($validator) && !($validator instanceof ValidatorInterface)) {
            throw $this->_createInvalidArgumentException($this->__('Invalid validator'), null, null, $validator);
        }

        $this->validator = $validator;
    }

    /**
     * Creates a new Dhii invalid argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
            $message = null,
            $code = null,
            RootException $previous = null,
            $argument = null
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
