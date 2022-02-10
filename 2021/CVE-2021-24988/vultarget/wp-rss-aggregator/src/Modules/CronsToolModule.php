<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Templates\NullTemplate;

/**
 * The module that adds the "Crons" tool to WP RSS Aggregator.
 *
 * @since [*next-version*]
 */
class CronsToolModule implements ModuleInterface
{
    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function getFactories()
    {
        return [
            /*
             * Information about the "Crons" tool.
             *
             * @since [*next-version*]
             */
            'wpra/admin/tools/crons/info' => function (ContainerInterface $c) {
                return [
                    'name' => __('Crons', 'wprss'),
                    'template' => $c->has('wpra/twig/collection')
                        ? $c->get('wpra/twig/collection')['admin/tools/crons.twig']
                        : new NullTemplate(),
                ];
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function getExtensions()
    {
        return [
            /*
             * Registers the "Crons" tool.
             *
             * @since [*next-version*]
             */
            'wpra/admin/tools' => function (ContainerInterface $c, $tools) {
                return $tools + ['crons' => $c->get('wpra/admin/tools/crons/info')];
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function run(ContainerInterface $c)
    {
    }
}
