<?php

namespace Aventura\Wprss\Core\Plugin;

/**
 * An interface for something that creates add-ons.
 *
 * @since 4.8.1
 */
interface AddonFactoryInterface extends FactoryInterface
{

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     * @return AddonInterface
     */
    static public function create();

    /**
     * Get the parent of the add-ons created by this interface.
     *
     * @since 4.8.1
     * @return PluginInterface
     */
    public function getParent();
}