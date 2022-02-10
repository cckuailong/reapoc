<?php

namespace Dhii\Iterator;

use InvalidArgumentException;
use stdClass;
use Traversable;
use Iterator;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for normalizing iterables into iterators.
 *
 * @since [*next-version*]
 */
trait NormalizeIteratorCapableTrait
{
    /**
     * Normalizes an iterable value into an iterator.
     *
     * If the value is iterable, the resulting iterator would iterate over the
     * elements in the iterable.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable|mixed $iterable The value to normalize.
     *
     * @return Iterator The normalized iterator.
     */
    protected function _normalizeIterator($iterable)
    {
        if ($iterable instanceof stdClass) {
            $iterable = (array) $iterable;
        }

        if (!is_array($iterable) && !($iterable instanceof Traversable)) {
            throw $this->_createInvalidArgumentException(
                $this->__('Invalid iterable'),
                null,
                null,
                $iterable
            );
        }

        if ($iterable instanceof Iterator) {
            return $iterable;
        }

        if (is_array($iterable)) {
            return $this->_createArrayIterator($iterable);
        }

        // If not array them gotta be traversable
        return $this->_createTraversableIterator($iterable);
    }

    /**
     * Creates an iterator that will iterate over the given array.
     *
     * @param array $array The array to create an iterator for.
     *
     * @since [*next-version*]
     *
     * @return Iterator The iterator that will iterate over the array.
     */
    abstract protected function _createArrayIterator(array $array);

    /**
     * Creates an iterator that will iterate over the given traversable.
     *
     * @param Traversable $traversable The traversable to create an iterator for.
     *
     * @since [*next-version*]
     *
     * @return Iterator The iterator that will iterate over the traversable.
     */
    abstract protected function _createTraversableIterator(Traversable $traversable);

    /**
     * Creates a new Invalid Argument exception.
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
    abstract protected function __($string, $args = [], $context = null);
}
