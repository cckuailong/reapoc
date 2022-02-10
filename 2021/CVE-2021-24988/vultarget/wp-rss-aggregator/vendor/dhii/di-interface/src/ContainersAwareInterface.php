<?php

namespace Dhii\Di;

use Traversable;

/**
 * Something that can have a container list retrieved from it.
 *
 * @since 0.1
 */
interface ContainersAwareInterface
{
    /**
     * Return a list of containers that belong to this instance.
     *
     * @since 0.1
     *
     * @return array|Traversable The list of containers belonging to this instance.
     */
    public function getContainers();
}
