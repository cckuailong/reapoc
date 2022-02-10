<?php

namespace RebelCode\Wpra\Core\Wp\Asset;

/**
 * Interface for UI assets.
 *
 * Assets provide ability to to include styles or scripts.
 *
 * @since 4.14
 */
interface AssetInterface
{
    /**
     * Registers the asset.
     *
     * @since 4.14
     */
    public function register();

    /**
     * Enqueues the asset.
     *
     * @since 4.14
     */
    public function enqueue();
}
