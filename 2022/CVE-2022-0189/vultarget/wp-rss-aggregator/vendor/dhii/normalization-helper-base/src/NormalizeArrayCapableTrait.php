<?php

namespace Dhii\Util\Normalization;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;
use stdClass;
use Traversable;

/**
 * Functionality for map normalization.
 *
 * @since [*next-version*]
 */
trait NormalizeArrayCapableTrait
{
    /**
     * Normalizes a value into an array.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable $value The value to normalize.
     *
     * @throws InvalidArgumentException If value cannot be normalized.
     *
     * @return array The normalized value.
     */
    protected function _normalizeArray($value)
    {
        if ($value instanceof stdClass) {
            $value = (array) $value;
        }

        if (is_array($value)) {
            return $value;
        }

        if (!($value instanceof Traversable)) {
            throw $this->_createInvalidArgumentException($this->__('Not an iterable'), null, null, $value);
        }

        $value = iterator_to_array($value, true);

        return $value;
    }

    /**
     * Creates a new invalid argument exception.
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
