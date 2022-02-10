<?php

namespace Aventura\Wprss\Core;

use Aventura\Wprss\Core\Plugin\Di\AbstractContainer;
use Interop\Container\ServiceProvider as BaseServiceProvider;
use Interop\Container\ContainerInterface as BaseContainerInterface;
use Dhii\Di\FactoryInterface;
use Dhii\Di\WritableContainerInterface;

/**
 * The container that stores local, specific services.
 *
 * @since 4.11
 */
class Container extends AbstractContainer implements FactoryInterface, WritableContainerInterface
{
    /**
     * @since 4.11
     */
    public function __construct(BaseServiceProvider $serviceProvider, BaseContainerInterface $parent = null)
    {
        $this->_register($serviceProvider);
        if (!is_null($parent)) {
            $this->_setParentContainer($parent);
        }

        $this->_construct();
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function make($id, array $config = array())
    {
        return $this->_make($id, $config);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function register(\Interop\Container\ServiceProvider $serviceProvieder)
    {
        $this->_register($serviceProvieder);

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function set($id, $definition)
    {
        $this->_set($id, $definition);

        return $this;
    }
}
