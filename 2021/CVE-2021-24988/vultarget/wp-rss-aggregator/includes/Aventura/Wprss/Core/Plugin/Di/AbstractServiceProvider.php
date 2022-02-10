<?php

namespace Aventura\Wprss\Core\Plugin\Di;

use Aventura\Wprss\Core\Model\AbstractTranslatingModel;

/**
 * Common functionality for WPRA service providers.
 *
 * @since 4.11
 */
abstract class AbstractServiceProvider extends AbstractTranslatingModel
{
    /**
     * @since 4.11
     * @var array
     */
    protected $services;

    /**
     * Retrieves the definitions that this service provides.
     *
     * Definitions are get exposed to the `services` event as 'definitions'.
     * The definition list is normalized to an array.
     * The eventual definitions list is cached.
     *
     * @since 4.11
     *
     * @return array The definitions provided by this instance.
     */
    protected function _getServices()
    {
        if (is_null($this->services)) {
            $definitions = $this->_getServiceDefinitions();
            $this->_trigger('services', array('definitions' => &$definitions));

            if (empty($definitions)) {
                $definitions = array();
            }

            if (!is_array($definitions)) {
                $definitions = (array) $definitions;
            }

            $this->services = $definitions;
        }

        return $this->services;
    }

    /**
     * Retrieves the internal services list.
     *
     * @since 4.11
     *
     * @param array $services An array of service definitions.
     * @return AbstractServiceProvider This instance.
     */
    protected function _setServices(array $services)
    {
        $this->services = $services;

        return $this;
    }

    /**
     * The definitions provided by this instance.
     *
     * Not cached, not normalized. Override this in descendants class.
     *
     * @since 4.11
     *
     * @return array The definition list.
     */
    protected function _getServiceDefinitions()
    {
        return array();
    }

    /**
     * Retrieves the prefix used by IDs of services that this instance provides.
     *
     * @since 4.11
     *
     * @return string The prefix used for IDs of services.
     */
    protected function _getServiceIdPrefix()
    {
        return $this->_getDataOrConst('service_id_prefix', $this->_getEventPrefix());
    }

    /**
     * Alias of `getServiceIdPrefix()`.
     *
     * @see getServiceIdPrefix().
     *
     * @since 4.11
     */
    protected function _p($name = null)
    {
        $prefix = $this->_getServiceIdPrefix();
        return static::stringHadPrefix($name)
            ? $name
            : "{$prefix}{$name}";
    }

    /**
     * Triggers and returns an event.
     *
     * Due to nature of the WP native function used by this method,
     * the single argument accepted by handlers is an array, and
     * must be accepted by reference.
     *
     * @since 4.11
     *
     * @param string $name Name of the event.
     *  Will be automatically prefixed, unless prefix overridden.
     * @param array $args Data for the event.
     * @return array The args list, after all handlers have been applied to it.
     */
    protected function _trigger($name, $args = array())
    {
        if (!isset($args['caller'])) {
            $args['caller'] = $this;
        }

        $realName = $this->getEventPrefix($name);
        do_action_ref_array($realName, array(&$args));

        return $args;
    }
}
