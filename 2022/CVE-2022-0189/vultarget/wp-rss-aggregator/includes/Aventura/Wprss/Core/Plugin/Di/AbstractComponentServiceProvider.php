<?php

namespace Aventura\Wprss\Core\Plugin\Di;

/**
 * A service provider that can create components.
 *
 * @since 4.11
 */
class AbstractComponentServiceProvider extends AbstractServiceProvider
{
    const PREFIX_OVERRIDE = '!';
    const COMPONENT_INTERFACE = 'Aventura\\Wprss\\Core\\Plugin\\ComponentInterface';

    /**
     * Throws an exception if given instance or class name is not a valid component or component class name.
     *
     * @since 4.11
     *
     * @param string|ComponentInterface|mixed $component
     * @throws Exception If the argument is not a valid component instance or class name.
     */
    protected function _assertComponent($component)
    {
        if (!is_a($component, static::COMPONENT_INTERFACE)) {
            $componentType = is_string($component)
                    ? $component
                    : (is_object($component)
                            ? get_class($component)
                            : get_type($component));
            throw $this->exception(array('"%1$s" is not a component', $componentType));
        }
    }

    /**
     * Prepares a component instance.
     *
     * @since 4.11
     *
     * @param ComponentInterface $component The component to prepare.
     * @return ComponentInterface The prepared component.
     */
    protected function _prepareComponent($component)
    {
        $this->_assertComponent($component);
        $component->hook();

        return $component;
    }

    /**
     * Normalizes a factory config, optionally by using defaults.
     *
     * @since 4.11
     *
     * @param array|null $config The config to normalize.
     * @param array $defaults Defaults, if any, which will be extended by the normalized config.
     * @return array The normalized config, optionally applied on top of defaults.
     */
    protected function _normalizeConfig($config, $defaults = array())
    {
        if (is_null($config)) {
            $config = array();
        }

        return $this->_arrayMergeRecursive($defaults, $config);
    }

    /**
     * Merges two arrays recursively, preserving element types.
     *
     * @since 4.11
     *
     * @see \array_merge_recursive_distinct()
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function _arrayMergeRecursive(&$array1, &$array2)
    {
        return \array_merge_recursive_distinct($array1, $array2);
    }
}