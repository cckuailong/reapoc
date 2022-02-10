<?php

namespace Dhii\Di;

use Interop\Container\ContainerInterface as BaseContainerInterface;

/**
 * A composite container that can have child containers added.
 *
 * @since [*next-version*]
 */
interface WritableCompositeContainerInterface extends CompositeContainerInterface
{
    /**
     * Adds a child container.
     *
     * @since [*next-version*]
     *
     * @param BaseContainerInterface $container The container to add.
     */
    public function add(BaseContainerInterface $container);
}
