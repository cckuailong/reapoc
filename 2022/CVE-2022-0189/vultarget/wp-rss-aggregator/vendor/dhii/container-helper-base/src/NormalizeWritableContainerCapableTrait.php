<?php

namespace Dhii\Data\Container;

use Dhii\Util\String\StringableInterface as Stringable;
use ArrayAccess;
use InvalidArgumentException;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use stdClass;
use Exception as RootException;

/**
 * Functionality for writable container normalization.
 *
 * @since [*next-version*]
 */
trait NormalizeWritableContainerCapableTrait
{
    /**
     * Normalizes a writable container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass $container The writable container to normalize.
     *
     * @throws InvalidArgumentException If not a valid writable container.
     *
     * @return array|ArrayAccess|stdClass A container that can be written to.
     */
    protected function _normalizeWritableContainer($container)
    {
        $container = $this->_normalizeContainer($container);

        if ($container instanceof BaseContainerInterface) {
            throw $this->_createInvalidArgumentException(
                $this->__('Invalid container'),
                null,
                null,
                $container
            );
        }

        return $container;
    }

    /**
     * Normalizes a container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|BaseContainerInterface $container The container to normalize.
     *
     * @throws InvalidArgumentException If the container is invalid.
     *
     * @return array|ArrayAccess|stdClass|BaseContainerInterface Something that can be used with
     *                                                           {@see ContainerGetCapableTrait#_containerGet()} or
     *                                                           {@see ContainerHasCapableTrait#_containerHas()}.
     */
    abstract protected function _normalizeContainer($container);

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
