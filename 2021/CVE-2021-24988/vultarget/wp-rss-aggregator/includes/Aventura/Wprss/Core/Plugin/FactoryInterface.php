<?php

namespace Aventura\Wprss\Core\Plugin;

/**
 * An interface for something that creates plugins.
 *
 * @since 4.8.1
 */
interface FactoryInterface
{
    /**
     * Create a plugin.
     *
     * @since 4.8.1
     * @return PluginInterface
     */
    static public function create();
}