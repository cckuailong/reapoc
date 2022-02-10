<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Templates\Feeds\LegacyDisplayTemplate;

/**
 * The feeds display module for WP RSS Aggregator.
 *
 * @since 4.13
 */
class FeedDisplayModule implements ModuleInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getFactories()
    {
        return [
            /*
             * The feed display template.
             *
             * @since 4.13
             */
            'wpra/display/feeds/template' => function (ContainerInterface $c) {
                return $c->get('wpra/display/feeds/legacy_template');
            },
            /*
             * The legacy feed display template used by older versions of WP RSS Aggregator.
             *
             * @since 4.13
             */
            'wpra/display/feeds/legacy_template' => function (ContainerInterface $c) {
                return new LegacyDisplayTemplate();
            },
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function getExtensions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function run(ContainerInterface $c)
    {
    }
}
