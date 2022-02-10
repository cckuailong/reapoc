<?php

namespace Dhii\Data\Container;

use ArrayAccess;
use InvalidArgumentException;
use OutOfRangeException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use stdClass;
use Exception as RootException;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Functionality for setting data on a container.
 *
 * @since [*next-version*]
 */
trait ContainerSetCapableTrait
{
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
    protected function _containerSet(&$container, $key, $value)
    {
        $key = $this->_normalizeKey($key);

        try {
            if (is_array($container)) {
                $container[$key] = $value;

                return;
            }

            if ($container instanceof ArrayAccess) {
                $container->offsetSet($key, $value);

                return;
            }

            if ($container instanceof stdClass) {
                $container->{$key} = $value;

                return;
            }
        } catch (RootException $e) {
            throw $this->_createContainerException($this->__('Could not write to container key "%1$s"', [$key]), null, $e);
        }

        throw $this->_createInvalidArgumentException($this->__('Not a valid container'), null, null, $container);
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
