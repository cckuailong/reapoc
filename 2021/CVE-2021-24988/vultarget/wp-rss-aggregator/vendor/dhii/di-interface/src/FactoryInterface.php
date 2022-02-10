<?php

namespace Dhii\Di;

/**
 * Something that can create service instances.
 *
 * @since 0.1
 */
interface FactoryInterface
{
    /**
     * Creates a new instance of a service.
     *
     * @since 0.1
     *
     * @param string $id     The ID of the service to create.
     * @param mixed  $config Some kind of configuration.
     *
     * @throws NotFoundException  If the given ID does not represent a known service.
     * @throws ContainerException If an error occurred while resolving the service definition.
     *
     * @return object|null The created service instance or null if the given ID does not represent
     */
    public function make($id, array $config = array());
}
