<?php

namespace Aventura\Wprss\Core\Plugin\Di;

use Interop\Container\ServiceProvider as BaseServiceProviderInterface;

/**
 * Represents a WPRA-specific service provider.
 *
 * @since 4.11
 */
interface ServiceProviderInterface extends BaseServiceProviderInterface
{
    /**
     * Retrieve the prefix that is used by services provided by this instance.
     *
     * @param string|null $id The ID to prefix, if not null.
     *
     * @since 4.11
     */
    public function getServiceIdPrefix($id = null);
}
