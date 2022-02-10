<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;

/**
 * A module that adds compatibility with the PolyLang plugin.
 *
 * @since 4.16
 */
class PolyLangCompatModule implements ModuleInterface
{
    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function getFactories()
    {
        return [
            /*
             * The filter handler that registers the feed item CPT.
             *
             * @since 4.16
             */
            'wpra/polylang/handlers/register_feed_item_cpt' => function (ContainerInterface $c) {
                return function ($postTypes) use ($c) {
                    // Stop if the feed items CPT name is not available
                    if (!$c->has('wpra/feeds/items/cpt/name')) {
                        return $postTypes;
                    }

                    $cpt = $c->get('wpra/feeds/items/cpt/name');

                    $postTypes[$cpt] = $cpt;

                    return $postTypes;
                };
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function getExtensions()
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function run(ContainerInterface $c)
    {
        add_filter('pll_get_post_types', $c->get('wpra/polylang/handlers/register_feed_item_cpt'));
    }
}
