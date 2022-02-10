<?php

namespace Dhii\Iterator;

use Traversable;
use Iterator;
use IteratorAggregate;
use Exception as RootException;
use OutOfRangeException;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;

/**
 * Capable of resolving an iterable to its innermost iterable.
 *
 * @since [*next-version*]
 */
trait ResolveIteratorCapableTrait
{
    /**
     * Finds the deepest iterator that matches.
     *
     * Because the given traversable can be an {@see IteratorAggregate}, it will try to get its inner iterator.
     * On each iterator, it will run the test function.
     * It will keep going inwards until one of these conditions occurs:
     *      - The iterator matches (test function returns `true`).
     *          This can also be the supplied iterator. In this case the iterator will be returned.
     *      - The limit or the maximal depth are reached.
     *          In this case, if the iterator is not a match, an exception will be thrown.
     *
     * @since [*next-version*]
     *
     * @param Traversable $iterator The iterator to resolve.
     * @param callable    $test     The test function which determines when the iterator is considered to be resolved.
     *                              Default: Returns `true` on first found instance of {@see Iterator}.
     * @param $limit int|float|string|Stringable The depth limit for resolution.
     *
     * @throws InvalidArgumentException If limit is not a valid integer representation.
     * @throws OutOfRangeException      If infinite recursion is detected, or the iterator could not be resolved within the depth limit.
     *
     * @return Iterator The inner-most iterator, or whatever the test function allows.
     */
    protected function _resolveIterator(Traversable $iterator, $test = null, $limit = null)
    {
        $origIterator = $iterator;

        // Default test function
        if (!is_callable($test)) {
            $test = function (Traversable $subject) {
                return $subject instanceof Iterator;
            };
        }

        if (is_null($limit)) {
            $limit = 100;
        } else {
            $limit = $this->_normalizeInt($limit);
        }

        $i = 0;
        while (!$test($iterator) && $i < $limit) {
            if (!($iterator instanceof IteratorAggregate)) {
                break;
            }

            $_it = $iterator->getIterator();
            if ($iterator === $_it) {
                throw $this->_createOutOfRangeException(
                    $this->__('Infinite recursion: looks like the traversable wraps itself on level %1$d', [$i]),
                    null,
                    null,
                    $origIterator
                );
            }

            $iterator = $_it;
            ++$i;
        }

        if (!$test($iterator)) {
            throw $this->_createOutOfRangeException(
                $this->__('The deepest iterator is not a match (limit is %1$d)', [$limit]),
                null,
                null,
                $origIterator
            );
        }

        return $iterator;
    }

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
     * @see sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);

    /**
     * Normalizes a value into an integer.
     *
     * The value must be a whole number, or a string representing such a number,
     * or an object representing such a string.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|float|int $value The value to normalize.
     *
     * @throws InvalidArgumentException If value cannot be normalized.
     *
     * @return int The normalized value.
     */
    abstract protected function _normalizeInt($value);
}
