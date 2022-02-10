<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

use Bitpay\DependencyInjection\BitpayExtension;
use Bitpay\DependencyInjection\Loader\ArrayLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Setups container and is ready for some dependency injection action
 *
 * @package Bitpay
 */
class Bitpay
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * First argument can either be a string or fullpath to a yaml file that
     * contains configuration parameters. For a list of configuration values
     * see \Bitpay\Config\Configuration class
     *
     * The second argument is the container if you want to build one by hand.
     *
     * @param array|string       $config
     * @param ContainerInterface $container
     */
    public function __construct($config = array(), ContainerInterface $container = null)
    {
        $this->container = $container;

        if (is_null($container)) {
            $this->initializeContainer($config);
        }
    }

    /**
     * Initialize the container
     */
    protected function initializeContainer($config)
    {
        $this->container = $this->buildContainer($config);
        $this->container->compile();
    }

    /**
     * Build the container of services and parameters
     */
    protected function buildContainer($config)
    {
        $container = new ContainerBuilder(new ParameterBag($this->getParameters()));

        $this->prepareContainer($container);
        $this->getContainerLoader($container)->load($config);

        return $container;
    }

    protected function getParameters()
    {
        return array(
            'bitpay.root_dir' => realpath(__DIR__.'/..'),
        );
    }

    /**
     */
    private function prepareContainer(ContainerInterface $container)
    {
        foreach ($this->getDefaultExtensions() as $ext) {
            $container->registerExtension($ext);
            $container->loadFromExtension($ext->getAlias());
        }
    }

    /**
     * @param  ContainerInterface $container
     * @return LoaderInterface
     */
    private function getContainerLoader(ContainerInterface $container)
    {
        $locator  = new FileLocator();
        $resolver = new LoaderResolver(
            array(
                new ArrayLoader($container),
                new YamlFileLoader($container, $locator),
            )
        );

        return new DelegatingLoader($resolver);
    }

    /**
     * Returns an array of the default extensions
     *
     * @return array
     */
    private function getDefaultExtensions()
    {
        return array(
            new BitpayExtension(),
        );
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return mixed
     */
    public function get($service)
    {
        return $this->container->get($service);
    }
}
