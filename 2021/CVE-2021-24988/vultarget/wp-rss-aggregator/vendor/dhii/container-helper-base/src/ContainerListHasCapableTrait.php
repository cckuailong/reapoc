<?php

namespace Dhii\Data\Container;

use ArrayAccess;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use OutOfRangeException;
use stdClass;
use Traversable;
use Psr\Container\ContainerExceptionInterface as BaseContainerExceptionInterface;
use Psr\Container\ContainerInterface as BaseContainerInterface;

/**
 * Functionality for checking for a key on a list of containers.
 *
 * @since [*next-version*]
 */
trait ContainerListHasCapableTrait
{
    /**
     * Checks if a list of containers has the specified key.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable          $key  The key to check for.
     * @param array|stdClass|Traversable $list A list of containers.
     *
     * @throws InvalidArgumentException        If the key or the list are of the wrong type.
     * @throws BaseContainerExceptionInterface If problem checking for key.
     *
     * @return bool True if a container in the list has the specified key; false otherwise.
     */
    protected function _containerListHas($key, $list)
    {
        $list = $this->_normalizeIterable($list);

        foreach ($list as $_container) {
            if ($this->_containerHas($_container, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks for a key on a container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $container The container to read from.
     * @param string|int|float|bool|Stringable                  $key       The key of the value to check.
     *
     * @throws BaseContainerExceptionInterface If an error occurred while reading from the container.
     * @throws OutOfRangeException             If the container or the key is invalid.
     *
     * @return bool True if the container has an entry for the given key, false if not.
     */
    abstract protected function _containerHas($container, $key);

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
}
