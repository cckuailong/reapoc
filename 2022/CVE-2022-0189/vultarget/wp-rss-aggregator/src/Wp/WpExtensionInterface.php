<?php

namespace RebelCode\Wpra\Core\Wp;

/**
 * Represents a WordPress extension.
 *
 * @since 4.14
 */
interface WpExtensionInterface
{
    /**
     * Registers the WordPress extension.
     *
     * @since 4.14
     */
    public function register();
}
