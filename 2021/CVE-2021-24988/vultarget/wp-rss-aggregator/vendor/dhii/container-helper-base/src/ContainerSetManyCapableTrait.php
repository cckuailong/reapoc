<?php

namespace Dhii\Data\Container;

use ArrayAccess;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerExceptionInterface;
use stdClass;
use Traversable;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for setting multiple values on a container.
 *
 * @since [*next-version*]
 */
trait ContainerSetManyCapableTrait
{
    /**
     * Sets multiple values on the container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass $container The container to set data on.
     * @param array|Traversable|stdClass $data      The map of data to set on the container.
     *
     * @throws InvalidArgumentException    If the container or the data map is invalid.
     * @throws OutOfRangeException         If one of the data keys is invalid.
     * @throws ContainerExceptionInterface If a problem with setting data occurs.
     */
    protected function _containerSetMany(&$container, $data)
    {
        $data = $this->_normalizeIterable($data);

        foreach ($data as $_k => $_v) {
            $this->_containerSet($container, $_k, $_v);
        }
    }

    /**
     * Sets data on the container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass       $container The container to set data on.
     * @param string|int|float|bool|Stringable $key       The key to set the value for.
     * @param mixed                            $value     The value to set.
     *
     * @throws InvalidArgumentException    If the container or the key is invalid.
     * @throws ContainerExceptionInterface If error occurs while writing to container.
     */
    abstract protected function _containerSet(&$container, $key, $value);

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
