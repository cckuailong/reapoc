<?php

namespace Dhii\Data\Container;

use ArrayAccess;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

/**
 * Common functionality for reading data from data sets.
 *
 * @since [*next-version*]
 */
trait ContainerGetCapableTrait
{
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
    protected function _containerGet($container, $key)
    {
        $origKey = $key;
        $key     = $this->_normalizeKey($key);
        // NotFoundExceptionInterface#getDataKey() returns `string` or `Stringable`,
        // so normalize only other types, and preserve original
        $origKey = is_string($origKey) || $origKey instanceof Stringable
            ? $origKey
            : $key;

        if ($container instanceof BaseContainerInterface) {
            try {
                return $container->get($key);
            } catch (NotFoundExceptionInterface $e) {
                throw $this->_createNotFoundException($this->__('Key "%1$s" not found', [$key]), null, $e, null, $origKey);
            }
        }

        if ($container instanceof ArrayAccess) {
            // Catching exceptions thrown by `offsetExists()`
            try {
                $hasKey = $container->offsetExists($key);
            } catch (RootException $e) {
                throw $this->_createContainerException($this->__('Could not check for key "%1$s"', [$key]), null, $e, null);
            }

            if (!$hasKey) {
                throw $this->_createNotFoundException($this->__('Key "%1$s" not found', [$key]), null, null, null, $origKey);
            }

            // Catching exceptions thrown by `offsetGet()`
            try {
                return $container->offsetGet($key);
            } catch (RootException $e) {
                throw $this->_createContainerException($this->__('Could not retrieve value for key "%1$s"', [$key]), null, $e, null);
            }
        }

        if (is_array($container)) {
            if (!array_key_exists($key, $container)) {
                throw $this->_createNotFoundException($this->__('Key "%1$s" not found', [$key]), null, null, null, $origKey);
            }

            return $container[$key];
        }

        if ($container instanceof stdClass) {
            // Container is an `stdClass`
            if (!property_exists($container, $key)) {
                throw $this->_createNotFoundException($this->__('Key "%1$s" not found', [$key]), null, null, null, $origKey);
            }

            return $container->{$key};
        }

        throw $this->_createInvalidArgumentException($this->__('Not a valid container'), null, null, $container);
    }

    /**
     * Normalizes a key.
     *
     * Treats it as one of many keys, throwing a more appropriate exception.
     *
     * @param string|int|float|bool|Stringable $key The key to normalize.
     *
     * @throws OutOfRangeException If key cannot be normalized.
     *
     * @return string The normalized key.
     */
    abstract protected function _normalizeKey($key);

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
     * @return NotFoundExceptionInterface The new exception.
     */
    abstract protected function _createNotFoundException(
        $message = null,
        $code = null,
        RootException $previous = null,
        BaseContainerInterface $container = null,
        $dataKey = null
    );

    /**
     * Creates a new container exception.
     *
     * @param string|Stringable|null      $message   The exception message, if any.
     * @param int|string|Stringable|null  $code      The numeric exception code, if any.
     * @param RootException|null          $previous  The inner exception, if any.
     * @param BaseContainerInterface|null $container The associated container, if any.
     *
     * @since [*next-version*]
     *
     * @return ContainerExceptionInterface The new exception.
     */
    abstract protected function _createContainerException(
        $message = null,
        $code = null,
        RootException $previous = null,
        BaseContainerInterface $container = null
    );

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
