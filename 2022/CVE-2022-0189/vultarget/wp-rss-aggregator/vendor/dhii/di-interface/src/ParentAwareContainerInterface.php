<?php

namespace Dhii\Di;

/**
 * A container that can have a parent container.
 *
 * This interface is often used to delegate lookup.
 *
 * @since 0.1
 */
interface ParentAwareContainerInterface extends ContainerInterface
{
    /**
     * Retrieve the container that is the parent of this container.
     *
     * @since 0.1
     *
     * @return ContainerInterface
     */
    public function getParentContainer();
}
