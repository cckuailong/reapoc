<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;

/**
 * Interface for WP RSS Aggregator modules.
 *
 * @since 4.13
 */
interface ModuleInterface
{
    /**
     * Retrieves the module's service factories.
     *
     * @since 4.13
     *
     * @return callable[]
     */
    public function getFactories();

    /**
     * Retrieves the module's extensions.
     *
     * @since 4.13
     *
     * @return callable[]
     */
    public function getExtensions();

    /**
     * Runs the module.
     *
     * @since 4.13
     *
     * @param ContainerInterface $c The services container.
     */
    public function run(ContainerInterface $c);
}
