<?php

namespace Dhii\Output;

use ArrayAccess;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use stdClass;

/**
 * Common functionality for objects that are aware of a context.
 *
 * @since 0.1
 */
trait ContextAwareTrait
{
    /**
     * The context.
     *
     * @since 0.1
     *
     * @var array|ArrayAccess|stdClass|ContainerInterface|null
     */
    protected $context;

    /**
     * Retrieves the context associated with this instance.
     *
     * @since 0.1
     *
     * @return array|ArrayAccess|stdClass|ContainerInterface|null The context.
     */
    protected function _getContext()
    {
        return $this->context;
    }

    /**
     * Sets the context for this instance.
     *
     * @since 0.1
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface|null $context The context instance, or null.
     */
    protected function _setContext($context)
    {
        $this->context = ($context !== null)
            ? $this->_normalizeContainer($context)
            : null;
    }

    /**
     * Normalizes a container.
     *
     * @since [*next-version*]
     *
     * @param array|ArrayAccess|stdClass|ContainerInterface $container The container to normalize.
     *
     * @throws InvalidArgumentException If the container is invalid.
     *
     * @return array|ArrayAccess|stdClass|ContainerInterface Something that can be used with
     *                                                       {@see ContainerGetCapableTrait#_containerGet()} or
     *                                                       {@see ContainerHasCapableTrait#_containerHas()}.
     */
    abstract protected function _normalizeContainer($container);
}
