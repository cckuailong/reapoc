<?php

namespace Aventura\Wprss\Core\Model;
use Aventura\Wprss\Core\Plugin\Di\AbstractServiceProvider;
use Aventura\Wprss\Core\Plugin\Di\ServiceProviderInterface;

/**
 * A generic service provider.
 *
 * Many instances can be created, as needed, to group service definitions,
 * which can be injected in the constructor.
 *
 * @since 4.11
 */
class GenericServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * @since 4.11
     *
     * @param array $services The services to set for this instance, if any.
     */
    public function __construct($data = null, array $services = null)
    {
        parent::__construct($data);

        $this->_setServices($services);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getServices()
    {
        return $this->_getServices();
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    public function getServiceIdPrefix($id = null)
    {
        return $this->_p($id);
    }
}