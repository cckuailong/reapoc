<?php

namespace Dhii\Data\Container;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;
use OutOfRangeException;

/**
 * Functionality for container data key normalization.
 *
 * @since [*next-version*]
 */
trait NormalizeKeyCapableTrait
{
    /**
     * Normalizes a key.
     *
     * Treats it as one of many keys, throwing a more appropriate exception.
     *
     * @param string|int|float|bool|Stringable $key The key to normalize.
     *
     * @throws OutOfRangeException If key cannot be normalized.
     *
     * @return string The normalized key.
     */
    protected function _normalizeKey($key)
    {
        try {
            return $this->_normalizeString($key);
        } catch (InvalidArgumentException $e) {
            throw $this->_createOutOfRangeException($this->__('Invalid key'), null, $e, $key);
        }
    }

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see Stringable).
     *
     * @since [*next-version*]
     *
     * @param string|int|float|bool|Stringable $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);

    /**
     * Creates a new Out Of Range exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return OutOfRangeException The new exception.
     */
    abstract protected function _createOutOfRangeException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
