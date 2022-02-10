<?php

namespace Aventura\Wprss\Core\Plugin;

/**
 * Something that allows creation and initialization of SpinnerChief classes.
 */
interface ComponentFactoryInterface
{
    public function createComponent($class, PluginInterface $parent);

    /**
     * @return string The base namespace, to which components created by this factory will belong.
     */
    public function getBaseNamespace();

}