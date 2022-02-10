<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Handlers\FeedShortcode\FeedsShortcodeHandler;
use RebelCode\Wpra\Core\Handlers\RegisterShortcodeHandler;
use RebelCode\Wpra\Core\Templates\NullTemplate;

/**
 * The feeds shortcode for WP RSS Aggregator.
 *
 * @since 4.13
 */
class FeedShortcodeModule implements ModuleInterface
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
             * The shortcode names.
             *
             * @since 4.13
             */
            'wpra/shortcode/feeds/names' => function (ContainerInterface $c) {
                return [
                    'wp_rss_aggregator',
                    'wp-rss-aggregator',
                ];
            },
            /*
             * The template used by the shortcode.
             *
             * @since 4.13
             */
            'wpra/shortcode/feeds/template' => function (ContainerInterface $c) {
                if (!$c->has('wpra/display/feeds/template')) {
                    return new NullTemplate();
                }

                return $c->get('wpra/display/feeds/template');
            },
            /*
             * The shortcode handler.
             *
             * @since 4.13
             */
            'wpra/shortcode/feeds/handler' => function (ContainerInterface $c) {
                return new FeedsShortcodeHandler($c->get('wpra/shortcode/feeds/template'));
            },
            /*
             * The handler that registers the shortcode.
             *
             * @since 4.13
             */
            'wpra/shortcode/feeds/handlers/register' => function (ContainerInterface $c) {
                return new RegisterShortcodeHandler(
                    $c->get('wpra/shortcode/feeds/names'),
                    $c->get('wpra/shortcode/feeds/handler')
                );
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
        call_user_func($c->get('wpra/shortcode/feeds/handlers/register'));
    }
}
