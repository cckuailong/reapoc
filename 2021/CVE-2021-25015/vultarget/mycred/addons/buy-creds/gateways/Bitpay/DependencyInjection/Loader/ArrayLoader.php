<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\DependencyInjection\Loader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Used to load a configuration that is passed in as an array
 *
 * @package Bitpay
 */
class ArrayLoader extends Loader
{
    protected $container;

    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    public function load($resource, $type = null)
    {
        // validation
        foreach (array_keys($resource) as $namespace) {
            if (in_array($namespace, array('imports', 'paramters', 'services'))) {
                continue;
            }
            if (!$this->container->hasExtension($namespace)) {
                $extensionNamespaces = array_filter(
                    array_map(
                        function ($ext) {
                            return $ext->getAlias();
                        },
                        $this->container->getExtensions()
                    )
                );
                throw new InvalidArgumentException(sprintf(
                    'There is no extension able to load the configuration for "%s". Looked for namespace "%s", found %s',
                    $namespace,
                    $namespace,
                    $extensionNamespaces ? sprintf('"%s"', implode('", "', $extensionNamespaces)) : 'none'
                ));
            }
        }

        // Set Paramters
        if (isset($resource['parameters'])) {
            foreach ($resource['parameters'] as $key => $value) {
                $this->container->setParameter($key, $value);
            }
        }

        // extensions
        foreach ($resource as $namespace => $values) {
            if (in_array($namespace, array('imports', 'parameters', 'services'))) {
                continue;
            }

            if (!is_array($values)) {
                $values = array();
            }

            $this->container->loadFromExtension($namespace, $values);
        }
    }

    public function supports($resource, $type = null)
    {
        return is_array($resource);
    }
}
