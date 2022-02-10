<?php

namespace Aventura\Wprss\Core;

use Aventura\Wprss\Core\Plugin\Di\AbstractComponentServiceProvider;
use Aventura\Wprss\Core\Plugin\Di\ServiceProviderInterface;
use Interop\Container\ContainerInterface;
use Dhii\Di\FactoryInterface;
use Aventura\Wprss\Core\Plugin\ComponentInterface;
use Aventura\Wprss\Core\Model\Event\EventManagerInterface;
use Aventura\Wprss\Core\Component\AdminHelper;

/**
 * Providers service definitions.
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
            $this->_p('plugin')                  => array($this, '_createPlugin'),
            $this->_p('factory')                 => array($this, '_createFactory'),
            $this->_p('event_manager')           => array($this, '_createEventManager'),
            $this->_p('logger')                  => array($this, '_createLogger'),
            $this->_p('admin_helper')            => array($this, '_createAdminHelper'),
            $this->_p('leave_review')            => array($this, '_createLeaveReview'),
            $this->_p('translator')              => array($this, '_createTranslator'),
        );
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
    public function getServiceIdPrefix($name = null)
    {
        return $this->_p($name);
    }

    /**
     * Creates the main plugin instance.
     *
     * @since 4.11
     *
     * @return Plugin
     */
    public function _createPlugin(ContainerInterface $c, $p = null, $config = null)
    {
        $factory = $c->get($this->_p('factory'));
        $config = $this->_normalizeConfig($config, array(
            'basename'          => \WPRSS_FILE_CONSTANT,
            'name'              => \WPRSS_CORE_PLUGIN_NAME,
            'service_id_prefix' => \WPRSS_SERVICE_ID_PREFIX,
            'event_prefix'      => \WPRSS_EVENT_PREFIX,
        ), $config);
        $plugin = new Plugin($config, null, $c, $factory);

        $plugin->hook();

        return $plugin;
    }

    /**
     * Gets the reference to the factory.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p Previous definition.
     * @param array|null $config
     * @return FactoryInterface
     */
    public function _createFactory(ContainerInterface $c, $p = null, $config = null)
    {
        return wprss_core_container();
    }

    /**
     * Creates an event manager instance.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return EventManagerInterface
     */
    public function _createEventManager(ContainerInterface $c, $p = null, $config = null)
    {
        $config = $this->_normalizeConfig($config, array(
            'is_keep_records'           => \WPRSS_DEBUG
        ));
        $service = new EventManager($config);

        return $service;
    }

    /**
     * Creates an instance of the admin helper component.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return Component\AdminHelper
     */
    public function _createAdminHelper(ContainerInterface $c, $p = null, $config = null)
    {
        $config = $this->_normalizeConfig($config, array(
            'plugin'                    => $c->get($this->_p('plugin')),
            'service_id_prefix'         => \WPRSS_SERVICE_ID_PREFIX,
            'notice_service_id_prefix'  => \WPRSS_NOTICE_SERVICE_ID_PREFIX,
        ));
        $service = new Component\AdminHelper($config, $c->get($this->_p('factory')));
        $this->_prepareComponent($service);

        return $service;
    }

    /**
     * Creates an instance of the leave-a-review component.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return Component\LeaveReviewNotification
     */
    public function _createLeaveReview(ContainerInterface $c, $p = null, $config = null)
    {
        $config = $this->_normalizeConfig($config, array(
            'plugin'            => $c->get($this->_p('plugin'))
        ));
        $service = new Component\LeaveReviewNotification($config);
        $this->_prepareComponent($service);

        return $service;
    }

    /**
     * Creates an instance of the leave-a-review component.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return Component\LeaveReviewNotification
     */
    public function _createLogger(ContainerInterface $c, $p = null, $config = null)
    {
        $config = $this->_normalizeConfig($config, array(
            'plugin'            => $c->get($this->_p('plugin')),
            'log_file_path'     => WPRSS_LOG_FILE . '-' . get_current_blog_id() . WPRSS_LOG_FILE_EXT,
            'level_threshold'   => WPRSS_LOG_LEVEL_ERROR,
        ));
        $service = new Component\Logger($config);
        $this->_prepareComponent($service);

        return $service;
    }

    /**
     * Creates a translator.
     *
     * @since 4.11
     *
     * @param ContainerInterface $c
     * @param null $p
     * @param array $config
     * @return callable
     */
    public function _createTranslator(ContainerInterface $c, $p = null, $config = null)
    {
        $textDomain = 'wprss';
        $helper = $c->get($this->_p('admin_helper'));
        /* @var $helper \Aventura\Wprss\Core\Component\AdminHelper */
        $command = $helper->createCommand(array(
            'function'      => function($text, $context = null) use ($textDomain) {
                return is_null($context)
                        ? __($text, $textDomain)
                        : _x($text, $context, $textDomain);
            }
        ));

        return $command;
    }
}
