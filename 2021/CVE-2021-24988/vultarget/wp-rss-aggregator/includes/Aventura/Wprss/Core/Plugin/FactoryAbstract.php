<?php

namespace Aventura\Wprss\Core\Plugin;

use Aventura\Wprss\Core;

/**
 * @since 4.8.1
 */
abstract class FactoryAbstract extends Core\Model\ModelAbstract implements FactoryInterface
{
    /**
     * Creates an instance of this class.
     *
     * @since 4.8.1
     * @param array $data Data for the new instance.
     * @return FactoryAbstract
     */
    static protected function _getInstance($data = array())
    {
        return new static($data);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     * @param type $parent
     * @param array|string $data Data for the new plugin. If string given, it will be assumed the value of the
     *  'basename' index.
     * @return PluginInterface
     */
    static public function create($data = array())
    {
        $me = static::_getInstance();
        do_action('wprss_plugin_factory_create_plugin_before', $me);
        $addon = $me->_create($data);
        do_action('wprss_plugin_factory_create_plugin_after', $addon, $me);

        return $addon;
    }

    /**
     * Does the actual creation.
     *
     * @since 4.8.1
     * @return PluginInterface
     */
    abstract protected function _create($data);
}