<?php

namespace RebelCode\Wpra\Core\Modules;

use Parsedown;
use Psr\Container\ContainerInterface;

/**
 * A module that provides the Parsedown service for WP RSS Aggregator.s
 *
 * @since 4.13
 */
class ParsedownModule implements ModuleInterface
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
             * The Parsedown service.
             *
             * @since 4.13
             */
            'wpra/parsedown' => function () {
                $instance = new Parsedown();
                $instance->setBreaksEnabled(true);
                $instance->setMarkupEscaped(true);

                return $instance;
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
        return [
            /*
             * Extends the changelog by parsing it as Markdown using Parsedown.
             *
             * @since 4.13
             */
            'wpra/core/changelog' => function (ContainerInterface $c, $changelog) {
                $parsed = $c->get('wpra/parsedown')->text($changelog);
                $wrapped = sprintf('<div class="wpra-changelog-container">%s</div>', $parsed);

                return $wrapped;
            },
        ];
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
