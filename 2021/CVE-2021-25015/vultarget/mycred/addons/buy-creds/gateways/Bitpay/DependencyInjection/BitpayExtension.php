<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\DependencyInjection;

use Bitpay\Config\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @package Bitpay
 */
class BitpayExtension implements ExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), $configs);

        foreach (array_keys($config) as $key) {
            $container->setParameter('bitpay.'.$key, $config[$key]);
        }

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__));
        $loader->load('services.xml');

        $container->setParameter('network.class', 'Bitpay\Network\\'.ContainerBuilder::camelize($config['network']));
        $container->setParameter(
            'adapter.class',
            'Bitpay\Client\Adapter\\'.ContainerBuilder::camelize($config['adapter']).'Adapter'
        );
        $container->setParameter('key_storage.class', $config['key_storage']);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getAlias()
    {
        return 'bitpay';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getNamespace()
    {
        return 'http://example.org/schema/dic/bitpay';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getXsdValidationBasePath()
    {
        return false;
    }
}
