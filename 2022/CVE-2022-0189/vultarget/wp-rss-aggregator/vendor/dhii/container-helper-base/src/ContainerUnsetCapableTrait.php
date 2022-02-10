<?php

namespace Dhii\Data\Container;

use ArrayAccess;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;
use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use Psr\Container\ContainerInterface as BaseContainerInterface;

/**
 * Functionality for unsetting data on a container.
 *
 * @since [*next-version*]
 */
trait ContainerUnsetCapableTrait
{
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
    protected function _containerUnset(&$container, $key)
    {
        $origKey = $key;
        $key     = $this->_normalizeKey($key);

        if (is_array($container)) {
            if (!isset($container[$key])) {
                throw $this->_createNotFoundException($this->__('Key "%1$s" not found', [$key]), null, null, null, $key);
            }

            unset($container[$key]);

            return;
        }

        if ($container instanceof stdClass) {
            if (!isset($container->{$key})) {
                throw $this->_createNotFoundException($this->__('Key "%1$s" not found', [$key]), null, null, null, $key);
            }

            unset($container->{$key});

            return;
        }

        if ($container instanceof ArrayAccess) {
            try {
                $hasKey = $container->offsetExists($key);
            } catch (RootException $e) {
                throw $this->_createContainerException($this->__('Could not check key "%1$s" on container', [$key]), null, $e, null);
            }

            if (!$hasKey) {
                throw $this->_createNotFoundException($this->__('Key "%1$s" not found', [$key]), null, null, null, $key);
            }

            try {
                $container->offsetUnset($key);

                return;
            } catch (RootException $e) {
                throw $this->_createContainerException($this->__('Could not unset key "%1$s" on container', [$key]), null, $e, null);
            }
        }

        throw $this->_createInvalidArgumentException($this->__('Invalid container'), null, null, $container);
    }

    /**
     * Normalizes a data key.
     *
     * @param string|int|float|bool|Stringable $key The key to normalize.
     *
     * @throws OutOfRangeException If key cannot be normalized.
     *
     * @return string The normalized key.
     */
    abstract protected function _normalizeKey($key);

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
