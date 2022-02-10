<?php

namespace Dhii\Di;

use Interop\Container\ContainerInterface as BaseContainerInterface;
use Interop\Container\Exception\NotFoundException;
use Traversable;

/**
 * A container that can have many containers.
 *
 * @since 0.1
 */
abstract class AbstractCompositeContainer extends AbstractParentAwareContainer
{
    /**
     * The prefix for container IDs.
     *
     * @since 0.1
     */
    const CONTAINER_ID_PREFIX = 'container-';

    /**
     * Adds a container.
     *
     * @since 0.1
     *
     * @param BaseContainerInterface $container The container to add.
     *
     * @return $this This instance.
     */
    protected function _add(BaseContainerInterface $container)
    {
        $this->_set($this->_createContainerId($container), function (BaseContainerInterface $c, $previous = null) use ($container) {
            return $container;
        });

        return $this;
    }

    /**
     * Generates a container ID.
     *
     * @since 0.1
     *
     * @param ContainerInterface $container The container for which an ID will be generated.
     *
     * @return string A new container ID, guaranteed to be unique in the scope of this container.
     */
    protected function _createContainerId(BaseContainerInterface $container)
    {
        do {
            $id = uniqid(static::CONTAINER_ID_PREFIX);
        } while ($this->_hasDefinition($id));

        return $id;
    }

    /**
     * Retrieves a service from the first child container that has its definition.
     *
     * @since 0.1
     *
     * @param string $id The ID of the service to retrieve.
     *
     * @return mixed The service.
     *
     * @throws NotFoundException If none of the inner containers have a matching service.
     */
    protected function _getDelegated($id)
    {
        if (!($having = $this->_hasDelegated($id))) {
            throw $this->_createNotFoundException(sprintf('Could not create service for ID "%1$s": no service defined', $id));
        }

        return $having->get($id);
    }

    /**
     * Determines which of the child containers has a service with the specified ID.
     *
     * @since 0.1
     *
     * @param string $id The ID of the service to check for.
     *
     * @return ContainerInterface|bool The container, which has the definition with the specified ID, if found;
     *                                 otherwise, false.
     */
    protected function _hasDelegated($id)
    {
        $containers = $this->_getContainersReversed();

        foreach ($containers as $_container) {
            if ($_container->has($id)) {
                return $_container;
            }
        }

        return false;
    }

    /**
     * Gets the child containers.
     *
     * @since 0.1
     * @see CompositeContainerInterface::getContainers()
     *
     * @return ContainerInterface[]|Traversable A list of containers.
     */
    protected function _getContainers()
    {
        $containers = array();
        foreach ($this->serviceDefinitions as $_key => $_value) {
            $service = $this->_get($_key);
            if ($service instanceof BaseContainerInterface) {
                $containers[$_key] = $service;
            }
        }

        return $containers;
    }

    /**
     * Gets the child containers in reverse order.
     *
     * @since 0.1
     * @see CompositeContainerInterface::getContainers()
     *
     * @return ContainerInterface[]|Traversable A list of containers.
     */
    protected function _getContainersReversed()
    {
        return array_reverse($this->_getContainers());
    }
}
