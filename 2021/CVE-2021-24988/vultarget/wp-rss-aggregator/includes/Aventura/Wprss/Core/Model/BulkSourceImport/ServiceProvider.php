<?php

namespace Aventura\Wprss\Core\Model\BulkSourceImport;

use Aventura\Wprss\Core\Component\BulkSourceImport;
use Aventura\Wprss\Core\Plugin\Di\AbstractComponentServiceProvider;
use Aventura\Wprss\Core\Plugin\Di\ServiceProviderInterface;
use Interop\Container\ContainerInterface;

/**
 * Provides services that represent admin notices.
 *
 * @since 4.11
 */
class ServiceProvider extends AbstractComponentServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 4.11
     */
    protected function _getServiceDefinitions()
    {
        return array(
            $this->_p('bulk_source_import')         => array($this, '_createBulkSourceImport'),
            $this->_p('source_importer')            => array($this, '_createPlainTextImporter'),
            $this->_p('array_source_importer')      => array($this, '_createArrayImporter'),
        );
    }

    /**
     * Creates a bulk source import component.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return \Aventura\Wprss\Core\Component\BulkSourceImport
     */
    public function _createBulkSourceImport(ContainerInterface $c, $p = null, $config = null)
    {
        $config = $this->_normalizeConfig($config, array(
            'plugin'            => $c->get($this->_p('plugin')),
            'event_prefix'              => \WPRSS_EVENT_PREFIX,
        ));
        $importer = $c->get($this->_p('source_importer'));
        $service = new BulkSourceImport($config, $importer);
        $this->_prepareComponent($service);

        return $service;
    }

    /**
     * Creates a plain text importer component.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return PlainTextImporter
     */
    public function _createPlainTextImporter(ContainerInterface $c, $p = null, $config = null)
    {
        $config = $this->_normalizeConfig($config, array(
            'event_prefix'         => \WPRSS_EVENT_PREFIX,
            'default_status'       => 'publish',
            'default_type'         => \WPRSS_POST_TYPE_FEED_SOURCE,
            'default_site'         => \is_multisite() ? \get_current_blog_id() : '',
        ));
        $service = new PlainTextImporter($config, $c->get($this->_p('translator')));

        return $service;
    }

    /**
     * Creates an array importer component.
     *
     * @since 4.12
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     *
     * @return ArrayImporter
     */
    public function _createArrayImporter(ContainerInterface $c, $p = null, $config = null)
    {
        $config = $this->_normalizeConfig($config, array(
            'event_prefix'         => \WPRSS_EVENT_PREFIX,
            'default_status'       => 'publish',
            'default_type'         => \WPRSS_POST_TYPE_FEED_SOURCE,
            'default_site'         => \is_multisite() ? \get_current_blog_id() : '',
        ));
        $service = new ArrayImporter($config, $c->get($this->_p('translator')));

        return $service;
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
