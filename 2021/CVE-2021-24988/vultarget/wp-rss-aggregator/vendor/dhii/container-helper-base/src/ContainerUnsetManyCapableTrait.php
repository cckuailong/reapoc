<?php

namespace Dhii\Data\Container;

use ArrayAccess;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;
use Traversable;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for unsetting multiple data pieces on a container.
 *
 * @since [*next-version*]
 */
trait ContainerUnsetManyCapableTrait
{
    /**
     * Unsets values with the specified keys on the given container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass                                      $container The writable container to unset the values on.
     * @param string[]|Stringable[]|bool[]|int[]|float[]|Traversable|stdClass $keys      The list keys to unset the values for.
     *
     * @throws InvalidArgumentException    If the container or the list of keys is invalid.
     * @throws OutOfRangeException         If one of the keys is invalid.
     * @throws NotFoundExceptionInterface  If one of the keys is not found.
     * @throws ContainerExceptionInterface If problem accessing the container.
     */
    protected function _containerUnsetMany(&$container, $keys)
    {
        $keys = $this->_normalizeIterable($keys);

        foreach ($keys as $_k) {
            $this->_containerUnset($container, $_k);
        }
    }

    /**
     * Unsets a value with the specified key on the given container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass       $container The writable container to unset the value on.
     * @param string|int|float|bool|Stringable $key       The key to unset the value for.
     *
     * @throws InvalidArgumentException    If the container is invalid.
     * @throws OutOfRangeException         If the key is invalid.
     * @throws NotFoundExceptionInterface  If the key is not found.
     * @throws ContainerExceptionInterface If problem accessing the container.
     */
    abstract protected function _containerUnset(&$container, $key);

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
