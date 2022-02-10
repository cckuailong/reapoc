<?php

namespace Dhii\Iterator;

use Dhii\Invocation\Exception\InvocationExceptionInterface;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use stdClass;
use Traversable;

/**
 * Functionality for looping over an iterable.
 *
 * @since [*next-version*]
 */
trait MapIterableCapableTrait
{
    /**
     * Invokes a callback for each element of the iterable.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable            $iterable The iterable to map.
     * @param callable                              $callback The callback to apply to the elements of the iterable.
     *                                                        The callback return value will be stored in `$results`.
     *                                                        Signature:
     *                                                        `function ($value, $key, $iterable)`
     * @param Stringable|string|int|float|bool|null $start    The offset of the iteration, at which to start applying the callback.
     *                                                        Iterations will still happen on all previous elements, but the callback will not be applied.
     *                                                        Default: 0.
     * @param Stringable|string|int|float|bool|null $count    The number  of invocations to make. Iteration will stop when this number is reached.
     *                                                        Pass 0 (zero) to iterate until end.
     *                                                        Default: 0.
     * @param array|null                            $results  If array, this will be filled with the results of the callback, in the same order, preserving keys.
     *
     * @throws InvalidArgumentException     If the iterable, the callback, start, or end are invalid.
     * @throws InvocationExceptionInterface If problem during invocation of the callback.
     */
    protected function _mapIterable($iterable, $callback, $start = null, $count = null, array &$results = null)
    {
        $iterable = $this->_normalizeIterable($iterable);

        $start = is_null($start)
            ? 0
            : $this->_normalizeInt($start);
        $count = is_null($count)
            ? 0
            : $this->_normalizeInt($count);

        $cStop = $count - 1;

        $i = 0; // Current iteration
        $c = 0; // Current count
        foreach ($iterable as $_k => $_v) {
            if ($i < $start) {
                ++$i;
                continue;
            }

            $result = $this->_invokeCallable($callback, [$_v, $_k, $iterable]);

            if (is_array($results)) {
                $results[$_k] = $result;
            }

            if ($c === $cStop) {
                break;
            }
            ++$c;
            ++$i;
        }
    }

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
     * Invokes a callable.
     *
     * @since [*next-version*]
     *
     * @param callable                   $callable The callable to invoke.
     * @param array|Traversable|stdClass $args     The arguments to invoke the callable with.
     *
     * @throws InvalidArgumentException     If the callable is not callable.
     * @throws InvalidArgumentException     if the args are not a valid list.
     * @throws InvocationExceptionInterface For errors that happen during invocation.
     *
     * @return mixed The result of the invocation.
     */
    abstract protected function _invokeCallable($callable, $args);
}
