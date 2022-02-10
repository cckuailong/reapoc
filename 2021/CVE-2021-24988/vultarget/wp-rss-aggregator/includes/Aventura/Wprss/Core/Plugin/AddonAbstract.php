<?php

namespace Aventura\Wprss\Core\Plugin;

/**
 * A base class for SpinnerChief add-ons.
 *
 * @since 4.8.1
 */
abstract class AddonAbstract extends PluginAbstract implements AddonInterface, ComponentInterface
{
    /** @since 4.8.1 */
    protected $_parent;

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function __construct($data, PluginInterface $parent, ComponentFactoryInterface $factory = null)
    {
        parent::__construct($data, $factory);
        $this->_setParent($parent);

        /**
         * This is necessary because extensions still don't know about the new
         * DI container mechanics, and no container is being passed in those
         * extensions to the constructor of this class.
         *
         * @todo Remove when add-ons re-factored.
         */
        $this->tmpParent = wprss_wp_container()->get($this->_getServiceIdPrefix('plugin'));
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function getPlugin()
    {
        return $this->getParent();
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function getParent() {
        return $this->_parent;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    protected function _setParent(PluginInterface $parent) {
        $this->_parent = $parent;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function getLogger()
    {
        if ($logger = parent::getLogger()) {
            return $logger;
        }

        return $this->getParent()->getLogger();
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function getEventManager()
    {
        if ($events = parent::getEventManager()) {
            return $events;
        }

        return $this->getParent()->getEventManager();
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function getEventPrefix($name = null)
    {
        $prefix = '';
        $prefix .= $this->getParent()->getEventPrefix();
        $prefix .= parent::getEventPrefix();
        if (is_null($name)) {
            return $prefix;
        }
        $prefix = static::stringHadPrefix($name)
            ? $name
            : "{$prefix}{$name}";

        return $prefix;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function on($name, $listener, $data = null, $priority = null, $acceptedArgs = null)
    {
        if (is_string($listener) && !is_object($listener)) {
            $listener = array($this, $listener);
        }

        return parent::on($name, $listener, $data, $priority, $acceptedArgs);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function event($name, $data = array())
    {
        if (!isset($data['caller'])) {
            $data['caller'] = $this;
        }

        return parent::event($name, $data);
    }
}