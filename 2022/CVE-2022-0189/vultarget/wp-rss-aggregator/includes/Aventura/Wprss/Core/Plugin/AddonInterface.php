<?php

namespace Aventura\Wprss\Core\Plugin;

/**
 * @since 4.8.1
 */
interface AddonInterface extends PluginInterface
{

    /**
     * Get the plugin, for which this is an add-on.
     *
     * @since 4.8.1
     * @return PluginInterface The parent plugin instance.
     */
    public function getParent();
}