<?php

namespace Dhii\Data\Container;

use ArrayAccess;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Exception as RootException;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;
use Traversable;

/**
 * Functionality for getting data from nested container.
 *
 * @since [*next-version*]
 */
trait ContainerSetPathCapableTrait
{
    /**
     * Sets data on the nested container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $container The top container in the chain.
     * @param array|Traversable|stdClass                        $path      The list of path segments.
     * @param mixed                                             $value     The value to set by path.
     *
     * @throws ContainerExceptionInterface If an error occurred while reading or writing from one of the containers in the chain.
     * @throws InvalidArgumentException    If one of the containers in the chain is invalid.
     * @throws NotFoundExceptionInterface  If one of the containers in the chain does not have the corresponding key.
     * @throws OutOfRangeException         If key in one of the containers in the chain is invalid.
     */
    protected function _containerSetPath(&$container, $path, $value)
    {
        $path       = $this->_normalizeArray($path);
        $pathLength = count($path);

        if (!$pathLength) {
            throw $this->_createInvalidArgumentException($this->__('Path is empty'), null, null, $container);
        }

        if ($pathLength === 1) {
            $this->_containerSet($container, $path[0], $value);

            return;
        }

        $currentSegment = array_shift($path);
        if (is_array($container)) {
            $this->_containerSetPath($container[$currentSegment], $path, $value);
        } else {
            $childContainer = $this->_containerGet($container, $currentSegment);
            $this->_containerSetPath($childContainer, $path, $value);
        }
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
     * Sets data on the container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass       $container The container to set data on.
     * @param string|int|float|bool|Stringable $key       The key to set the value for.
     * @param mixed                            $value     The value to set.
     *
     * @throws InvalidArgumentException    If the container is invalid.
     * @throws OutOfRangeException         If key is invalid.
     * @throws ContainerExceptionInterface If error occurs while writing to container.
     */
    abstract protected function _containerSet(&$container, $key, $value);

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
    abstract protected function _normalizeArray($value);

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
