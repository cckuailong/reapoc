<?php

namespace Dhii\Iterator;

use InvalidArgumentException;
use Iterator;
use OutOfRangeException;
use stdClass;
use Traversable;
use Exception as RootException;
use Countable;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for counting elements in an iterable.
 *
 * @since [*next-version*]
 */
trait CountIterableCapableTrait
{
    /**
     * Counts the elements in an iterable.
     *
     * Is optimized to retrieve count from values that support it.
     * - If {@see stdClass} instance, will enumerate the properties into an array.
     * - If array, will count in regular way using count();
     * - If {@see Countable}, will do the same;
     * - If {@see IteratorAggregate}, will drill down into internal iterators
     * until the first {@see Countable} is encountered, in which case the same
     * as above will be done.
     * - In any other case, will apply {@see iterator_count()}, which means
     * that it will iterate over the whole traversable to determine the count.
     *
     * @since [*next-version*]
     *
     * @param array|stdClass|Traversable $iterable The iterable to count. Must be finite.
     *
     * @return int The amount of elements.
     */
    protected function _countIterable($iterable)
    {
        $resolve = function ($it) {
            try {
                return $this->_resolveIterator($it, function ($it) {
                    return $it instanceof Countable;
                });
            } catch (RootException $e) {
                return;
            }
        };

        if ($iterable instanceof stdClass) {
            $iterable = (array) $iterable;
        }

        if (is_array($iterable) || $iterable instanceof Countable) {
            return count($iterable);
        }

        if ($countable = $resolve($iterable)) {
            return count($countable);
        }

        return iterator_count($iterable);
    }

    /**
     * Finds the deepest iterator that matches.
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
    abstract protected function _resolveIterator(Traversable $iterator, $test = null, $limit = null);
}
