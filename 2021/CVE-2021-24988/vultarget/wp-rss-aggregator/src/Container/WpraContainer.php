<?php

namespace RebelCode\Wpra\Core\Container;

/**
 * A container implementation specific to WP RSS Aggregator.
 *
 * @since 4.13
 */
class WpraContainer extends ModuleContainer
{
    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function createInnerContainer(array $definitions)
    {
        return new WpFilterContainer(parent::createInnerContainer($definitions));
    }
}
