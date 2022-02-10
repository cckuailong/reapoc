<?php

namespace Dhii\Di;

use Interop\Container\Exception\ContainerException as ContainerExceptionInterface;
use Interop\Container\ServiceProvider;

/**
 * Abstract implementation of an object that can provide services.
 *
 * @since 0.1
 */
abstract class AbstractServiceProvider
{
    /**
     * The service definitions.
     *
     * @since 0.1
     *
     * @var callable[]
     */
    protected $serviceDefinitions = array();

    /**
     * Gets the service definitions.
     *
     * @since 0.1
     * @see ServiceProvider::getServices()
     *
     * @return callable[]|\Traversable A list of service definitions.
     */
    protected function _getServices()
    {
        return $this->serviceDefinitions;
    }

    /**
     * Adds a service definition to this provider.
     *
     * @since 0.1
     *
     * @param string   $id         The ID of the service definition.
     * @param callable $definition The service definition.
     *
     * @throws ContainerException
     */
    protected function _add($id, $definition)
    {
        // Checking only format, because the definition may become available later
        if (!is_callable($definition, true)) {
            throw $this->_createContainerException(
                sprintf('Could not add service definition with ID "%1$s": The definition must be a callable', $id)
            );
        }

        $this->serviceDefinitions[$id] = $definition;

        return $this;
    }

    /**
     * Adds multiple service definitions to this provider.
     *
     * @since 0.1
     *
     * @param array|\Traversable $definitions An associative array of service definitions mapped by string keys.
     *
     * @return $this This instance.
     */
    protected function _addMany($definitions)
    {
        foreach ($definitions as $_id => $_definition) {
            $this->_add($_id, $_definition);
        }

        return $this;
    }

    /**
     * Creates a new exception that represents a generic DI container error.
     *
     * @since 0.1
     * 
     * @return ContainerExceptionInterface The new exception instance.
     */
    abstract protected function _createContainerException($message, $code = 0, \Exception $innerException = null);
}
