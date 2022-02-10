<?php

namespace Dhii\Data\Container;

use ArrayAccess;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;
use Traversable;

/**
 * Functionality for getting data from nested container.
 *
 * @since [*next-version*]
 */
trait ContainerGetPathCapableTrait
{
    /**
     * Retrieves a value from a chain of nested containers by path.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $container The top container in the chain to read from.
     * @param array|Traversable|stdClass                        $path      The list of path segments.
     *
     * @throws InvalidArgumentException    If one of the containers in the chain is invalid.
     * @throws ContainerExceptionInterface If an error occurred while reading from one of the containers in the chain.
     * @throws NotFoundExceptionInterface  If one of the containers in the chain does not have the corresponding key.
     *
     * @return mixed The value at the specified path.
     */
    protected function _containerGetPath($container, $path)
    {
        $path = $this->_normalizeIterable($path);

        $service = $container;

        foreach ($path as $segment) {
            $service = $this->_containerGet($service, $segment);
        }

        return $service;
    }

    /**
     * Retrieves a value from a container or data set.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $container The container to read from.
     * @param string|int|float|bool|Stringable                  $key       The key of the value to retrieve.
     *
     * @throws InvalidArgumentException    If container is invalid.
     * @throws ContainerExceptionInterface If an error occurred while reading from the container.
     * @throws NotFoundExceptionInterface  If the key was not found in the container.
     *
     * @return mixed The value mapped to the given key.
     */
    abstract protected function _containerGet($container, $key);

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
