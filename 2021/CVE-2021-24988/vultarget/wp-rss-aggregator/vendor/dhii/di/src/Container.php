<?php

namespace Dhii\Di;

use Interop\Container\ServiceProvider as BaseServiceProviderInterface;

/**
 * A simple, parent-agnostic container implementation.
 *
 * @since 0.1
 */
class Container extends AbstractContainerBase implements
    ContainerInterface,
    WritableContainerInterface,
    FactoryInterface
{
    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param BaseServiceProviderInterface $definitions Service definitions to add to this container.
     */
    public function __construct(BaseServiceProviderInterface $definitions = null)
    {
        if (!is_null($definitions)) {
            $this->_set($definitions);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function set($id, $definition = null)
    {
        $this->_set($id, $definition);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function register(BaseServiceProviderInterface $serviceProvider)
    {
        $this->_register($serviceProvider);

        return $this;
    }
}
