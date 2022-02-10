<?php

// This whole namespace is a temporary one, until there's a real Core add-on
namespace Aventura\Wprss\Core;

use Dhii\Di\FactoryInterface;
use Interop\Container\ContainerInterface;
use Aventura\Wprss\Core\Model\LoggerInterface;
use Aventura\Wprss\Core\Model\Event\EventManagerInterface;

/**
 * A dummy factory of Core components.
 *
 * This is to be used with the Core plugin.
 *
 * @since 4.8.1
 * @deprecated 4.11 Here only for BC.
 */
class ComponentFactory extends Plugin\ComponentFactoryAbstract
{
    /**
     * @since 4.8.1
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setBaseNamespace(__NAMESPACE__ . '\\Component');
    }

    /**
     * Retrieve the DI container.
     *
     * @since 4.11
     * @deprecated 4.11 This is just a temporary measure, until this class is removed.
     *
     * @return ContainerInterface The container instance.
     */
    protected function _getContainer()
    {
        return wprss_wp_container();
    }

    /**
     * Prefixes a service name with the WPRA service ID prefix.
     *
     * @since 4.11
     *
     * @param string $name A service name.
     * @return string The prefixed name.
     */
    protected function _p($name)
    {
        return \WPRSS_SERVICE_ID_PREFIX . $name;
    }

    /**
     * Retrieve the factory used for component creation.
     *
     * @since 4.11
     * @deprecated 4.11 This is just a temporary measure, until this class is removed.
     *
     * @return FactoryInterface The factory instance.
     */
    protected function _getFactory()
    {
        return $this->_getContainer()->get($this->_p('factory'));
    }

    /**
     * Creates a logger.
     *
     * @since 4.8.1
     *
     * @param array $data Data for the logger.
     * @return LoggerInterface The new logger instance.
     */
    public function createLogger($data = array())
    {
        $component = $this->_getFactory()->make($this->_p('logger'), $data);

        return $component;
    }

    /**
     * Creates an event manager.
     *
     * @since 4.8.1
     *
     * @param array $data
     * @return EventManagerInterface The new event manager instance.
     */
    public function createEventManager($data = array())
    {
        $component = $this->_getFactory()->make($this->_p('event_manager'), $data);
        return $component;
    }

    /**
     * Creates a component that is responsible for the "Leave a Review" notification.
     *
     * @since 4.10
     *
     * @param array $data Additional data to use for component configuration.
     * @return Component\LeaveReviewNotification The new component instance.
     */
    public function createLeaveReviewNotification($data = array())
    {
        $component = $this->_getFactory()->make($this->_p('leave_review'), $data);

        return $component;
    }

    /**
     * Creates a component that is responsible for the admin notices.
     *
     * @deprecated 4.11
     * @since 4.10
     *
     * @return Component\AdminAjaxNotices
     */
    public function createAdminAjaxNotices($data = array())
    {
        $component = $this->_getFactory()->make($this->_p('admin_ajax_notices'), $data);

        return $component;
    }

    /**
     * Creates a helper component related to the backend.
     *
     * @deprecated 4.11
     * @since 4.10
     *
     * @return Component\AdminHelper
     */
    public function createAdminHelper($data = array())
    {
        $component = $this->_getFactory()->make($this->_p('admin_helper'), $data);

        return $component;
    }
}