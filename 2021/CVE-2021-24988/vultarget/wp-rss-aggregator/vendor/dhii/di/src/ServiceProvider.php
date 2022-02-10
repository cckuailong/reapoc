<?php

namespace Dhii\Di;

use Exception;
use Dhii\Di\Exception\ContainerException;
use Traversable;

/**
 * Generic standards-compliant immutable DI service provider.
 *
 * @since 0.1
 */
class ServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param callable[]|Traversable $definitions A list of definitions for this provider.
     */
    public function __construct($definitions = array())
    {
        $this->_addMany($definitions);
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     */
    public function getServices()
    {
        return $this->_getServices();
    }

    /**
     * {@inheritdoc}
     *
     * @since 0.1
     *
     * @return ContainerException The new exception instance.
     */
    protected function _createContainerException($message, $code = 0, Exception $innerException = null)
    {
        return new ContainerException($message, $code, $innerException);
    }
}
