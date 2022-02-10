<?php

namespace Aventura\Wprss\Core\Plugin;

/**
 * Common functionality of component factories.
 * A component factory is also a component ;P
 *
 * @since 4.8.1
 */
abstract class ComponentFactoryAbstract extends ComponentAbstract implements ComponentFactoryInterface
{
    const COMPONENT_INTERFACE = 'Aventura\\Wprss\\Core\\Plugin\\ComponentInterface';

    /**
     * Creates a new component instance.
     *
     * @since 4.8.1
     * @param string $class The classname of the component to create.
     *  Can be relative to the base namespace of this factory.
     * @param PluginInterface $parent The parent plugin for the new component.
     * @return ComponentInterface A new component.
     * @throws Exception If class does not exist, or is not a component class.
     */
    public function createComponent($class, PluginInterface $parent, array $data = array())
    {
        $className = $this->getComponentClassName($class);
        if (!$this->validComponentClass($className)) {
            throw $this->exception(array('Could not create component: "%1$s" is not a component class as it does not implement "%2$s"', $className, $componentBase), array(__NAMESPACE__, 'Exception'));
        }

        if (!class_exists($className)) {
            throw $this->exception(array('Could not create component: component class"%1$s" does not exist', $className), array(__NAMESPACE__, 'Exception'));
        }

        $data['plugin'] = $parent;
        $component = new $className($data);
        $component->hook();

        return $component;
    }

    /**
     * Get the name of a component class, based on it's relative or absolute name, or mapped ID.
     *
     * @since 4.8.1
     *
     * @param string $name A relative or absolute class name, or some other class identifier that is mapped
     *  to a class name. If relative, then relative to the {@see getBaseNamespace()}.
     *
     * @return string Name of the component class.
     */
    public function getComponentClassName($name)
    {
        $className = null;
        // Namespace specified as array of parts; assume root namespace
        if (is_array($name)) {
            $name = '\\' . trim(implode('\\', $name), '\\');
        }

        if (static::isRootNamespace($name) && $this->validComponentClass($name)) {
            $className = $name;
        } else {
            $rootNamespace = $this->getBaseNamespace();
            $className = sprintf('%1$s\\%2$s', $rootNamespace, $name);
        }

        $this->event('component_class_name', array(
            'name'          => $name,
            'class_name'    => &$className
        ));

        return $className;
    }

    /**
     * Determines if a class name is of a valid component.
     *
     * @since 4.10
     *
     * @param string|object $className A class name or object to check.
     *
     * @return bool
     *
     * @throws \InvalidArgumentException If supplied argument is not a string or object.
     */
    public function validComponentClass($className)
    {
        if (is_object($className)) {
            $className = get_class($className);
        }

        if (!is_string($className)) {
            throw new \InvalidArgumentException('Could not validate component class: class name must be a string or object');
        }

        return static::classImplements($className, static::COMPONENT_INTERFACE);
    }
}
