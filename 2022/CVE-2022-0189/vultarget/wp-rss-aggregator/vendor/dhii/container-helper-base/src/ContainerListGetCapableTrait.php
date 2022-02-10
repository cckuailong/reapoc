<?php

namespace Dhii\Data\Container;

use ArrayAccess;
use stdClass;
use Traversable;
use InvalidArgumentException;
use OutOfRangeException;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Psr\Container\NotFoundExceptionInterface as BaseNotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface as BaseContainerExceptionInterface;

/**
 * Functionality for retrieving something from a list of containers.
 *
 * @since [*next-version*]
 */
trait ContainerListGetCapableTrait
{
    /**
     * Retrieves a service from the list of containers.
     *
     * Retrieves from the first container that has the service.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable          $key  The key of the service to get from the list.
     * @param array|stdClass|Traversable $list A list of containers.
     *
     * @throws InvalidArgumentException        If the key or the list are of the wrong type.
     * @throws BaseNotFoundExceptionInterface  If the service is not found in the list.
     * @throws BaseContainerExceptionInterface If problem retrieving the service.
     *
     * @return mixed The service.
     */
    protected function _containerListGet($key, $list)
    {
        $list = $this->_normalizeIterable($list);

        foreach ($list as $_container) {
            if ($this->_containerHas($_container, $key)) {
                return $this->_containerGet($_container, $key);
            }
        }

        throw $this->_createNotFoundException($this->__('Container with key "%1$s" not found', [$this->_normalizeString($key)]), null, null, null, $key);
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
     * Retrieves a value from a container or data set.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $container The container to read from.
     * @param string|int|float|bool|Stringable                  $key       The key of the value to retrieve.
     *
     * @throws InvalidArgumentException        If container is invalid.
     * @throws BaseContainerExceptionInterface If an error occurred while reading from the container.
     * @throws BaseNotFoundExceptionInterface  If the key was not found in the container.
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

    /**
     * Normalizes a value to its string representation.
     *
     * The values that can be normalized are any scalar values, as well as
     * {@see StringableInterface).
     *
     * @since [*next-version*]
     *
     * @param Stringable|string|int|float|bool $subject The value to normalize to string.
     *
     * @throws InvalidArgumentException If the value cannot be normalized.
     *
     * @return string The string that resulted from normalization.
     */
    abstract protected function _normalizeString($subject);

    /**
     * Creates a new not found exception.
     *
     * @param string|Stringable|null      $message   The exception message, if any.
     * @param int|string|Stringable|null  $code      The numeric exception code, if any.
     * @param RootException|null          $previous  The inner exception, if any.
     * @param BaseContainerInterface|null $container The associated container, if any.
     * @param string|Stringable|null      $dataKey   The missing data key, if any.
     *
     * @since [*next-version*]
     *
     * @return BaseNotFoundExceptionInterface The new exception.
     */
    abstract protected function _createNotFoundException(
        $message = null,
        $code = null,
        RootException $previous = null,
        BaseContainerInterface $container = null,
        $dataKey = null
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
