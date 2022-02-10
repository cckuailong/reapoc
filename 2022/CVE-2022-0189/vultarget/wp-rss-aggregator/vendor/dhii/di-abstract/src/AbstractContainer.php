<?php

namespace Dhii\Di;

use Interop\Container\Exception\ContainerException as ContainerExceptionInterface;
use Interop\Container\Exception\NotFoundException as NotFoundExceptionInterface;
use Exception;
use Interop\Container\ServiceProvider as BaseServiceProvider;

/**
 * Basic functionality of a DI container.
 *
 * @since 0.1
 */
abstract class AbstractContainer
{
    /**
     * Cache for created service instances.
     *
     * @since 0.1
     *
     * @var array
     */
    protected $serviceCache = array();

    /**
     * The service definitions.
     *
     * @since 0.1
     *
     * @var callable[]
     */
    protected $serviceDefinitions = array();

    /**
     * Retrieves a service by its ID.
     *
     * @since 0.1
     *
     * @param string ID The ID of the service to retrieve.
     *
     * @throws NotFoundExceptionInterface If no service is registered with the given ID.
     *
     * @return mixed The service identified by the given ID.
     */
    protected function _get($id)
    {
        if ($this->_isCached($id)) {
            return $this->_getCached($id);
        }

        $this->_cacheService($id, $this->_make($id));

        return $this->_getCached($id);
    }

    /**
     * Creates a new instance of a service.
     *
     * This can be exposed by a public method to implement FactoryInterface.
     *
     * @param string $id     The ID of the service to create.
     * @param mixed  $config Some kind of configuration.
     *
     * @throws NotFoundExceptionInterface If no service is registered with the given ID.
     *
     * @return mixed The created service.
     */
    protected function _make($id, $config = array())
    {
        if (!($definition = $this->_getDefinition($id))) {
            throw $this->_createNotFoundException(sprintf('Could not create service for ID "%1$s": no service defined', $id));
        }

        return $this->_resolveDefinition($definition, $config);
    }

    /**
     * Checks if a service ID exists in this container.
     *
     * @since 0.1
     *
     * @return bool True if a definition with the specified ID exists in this container;
     *              false otherwise.
     */
    protected function _has($id)
    {
        return $this->_hasDefinition($id);
    }

    /**
     * Registers a service or multiple services to this container.
     *
     * @since 0.1
     *
     * @param string|BaseServiceProvider $id         The service ID, or a service provider
     * @param callable|null              $definition The service definition.
     *
     * @return $this This instance.
     */
    protected function _set($id, $definition = null)
    {
        if ($id instanceof BaseServiceProvider) {
            $this->_register($id);

            return $this;
        }

        $this->_setDefinition($id, $definition);

        return $this;
    }

    /**
     * Registers a service provider in this container.
     *
     * @since 0.1
     *
     * @param BaseServiceProvider $provider The service provider to register.
     *
     * @return $this This instance.
     */
    protected function _register(BaseServiceProvider $provider)
    {
        foreach ($provider->getServices() as $_id => $_definition) {
            $this->_setDefinition($_id, $_definition);
        }

        return $this;
    }

    /**
     * Retrieves the service definitions.
     *
     * @since 0.1
     *
     * @return callable[] An associative array of all the registered definitions, mapped by their ID.
     */
    protected function _getDefinitions()
    {
        return $this->serviceDefinitions;
    }

    /**
     * Retrieves a service definition by ID.
     *
     * @since 0.1
     *
     * @param string $id The ID of the service to get the definition for.
     *
     * @return callable|null The service definition mapped to the given ID, if the ID is registered;
     *                       otherwise null.
     */
    protected function _getDefinition($id)
    {
        return isset($this->serviceDefinitions[$id])
            ? $this->serviceDefinitions[$id]
            : null;
    }

    /**
     * Checks if a service definition is registered to a given ID.
     *
     * @since 0.1
     *
     * @param string $id The ID of the service definition to check for.
     *
     * @return bool True if a definition with the specified ID is registered;
     *              otherwise false.
     */
    protected function _hasDefinition($id)
    {
        return isset($this->serviceDefinitions[$id]);
    }

    /**
     * Registers a service definition.
     *
     * @since 0.1
     *
     * @param string   $id         The service ID.
     * @param callable $definition The service definition.
     */
    protected function _setDefinition($id, $definition)
    {
        $this->serviceDefinitions[$id] = $definition;

        return $this;
    }

    /**
     * Retrieves the cached instance of a service.
     *
     * @since 0.1
     *
     * @param string $id The ID of the service to retrieve.
     *
     * @return mixed|null The cached service instance if found; otherwise null.
     */
    protected function _getCached($id)
    {
        return isset($this->serviceCache[$id])
            ? $this->serviceCache[$id]
            : null;
    }

    /**
     * Checks if a service instance is cached.
     *
     * @since 0.1
     *
     * @param string $id The service ID to check.
     *
     * @return bool True if a service with this ID exists in cache; false otherwise.
     */
    protected function _isCached($id)
    {
        return isset($this->serviceCache[$id]);
    }

    /**
     * Caches a service instance.
     *
     * @since 0.1
     *
     * @param string $id      The ID of the service to cache.
     * @param mixed  $service The service.
     *
     * @return $this This instance.
     */
    protected function _cacheService($id, $service)
    {
        $this->serviceCache[$id] = $service;

        return $this;
    }

    /**
     * Resolves a service definition into a service instance.
     *
     * @since 0.1
     *
     * @param callable $definition The service definition.
     * @param array    $config     An array of configuration arguments to pass to the definition.
     *
     * @throws ContainerExceptionInterface If the service definition is not a valid callable.
     *
     * @return mixed The service, to which the definition resolves.
     */
    protected function _resolveDefinition($definition, $config)
    {
        if (!is_callable($definition)) {
            throw $this->_createContainerException(sprintf('Could not resolve service definition": definition must be callable'));
        }

        return call_user_func_array($definition, array($this, null, $config));
    }

    /**
     * Creates a new exception that represents a case where a container entry is not found.
     *
     * @since 0.1
     * 
     * @return NotFoundExceptionInterface The new exception instance.
     */
    abstract protected function _createNotFoundException($message, $code = 0, Exception $innerException = null);

    /**
     * Creates a new exception that represents a generic DI container error.
     *
     * @since 0.1
     * 
     * @return ContainerExceptionInterface The new exception instance.
     */
    abstract protected function _createContainerException($message, $code = 0, Exception $innerException = null);
}
