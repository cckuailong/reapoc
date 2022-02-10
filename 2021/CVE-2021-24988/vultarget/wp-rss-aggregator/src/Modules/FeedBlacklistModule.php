<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Entities\Properties\Property;
use RebelCode\Entities\Schemas\Schema;
use RebelCode\Wpra\Core\Entities\Collections\FeedBlacklistCollection;
use RebelCode\Wpra\Core\Handlers\AddCptMetaCapsHandler;
use RebelCode\Wpra\Core\Handlers\NullHandler;
use RebelCode\Wpra\Core\Handlers\RegisterCptHandler;
use RebelCode\Wpra\Core\Ui\BlacklistTable;

/**
 * The feed blacklist module for WP RSS Aggregator.
 *
 * @since 4.13
 */
class FeedBlacklistModule implements ModuleInterface
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
             * The name of the feed item blacklist CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/blacklist/cpt/name' => function () {
                return 'wprss_blacklist';
            },
            /*
             * The labels for the feed item blacklist CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/blacklist/cpt/labels' => function () {
                return [
                    'name' => __('Blacklisted Items', 'wprss'),
                    'singular_name' => __('Blacklisted Item', 'wprss'),
                    'add_new' => __('Blacklist An Item', 'wprss'),
                    'all_items' => __('Blacklisted Items', 'wprss'),
                    'add_new_item' => __('Blacklist An Item', 'wprss'),
                    'edit_item' => __('Edit Blacklisted Item', 'wprss'),
                    'new_item' => __('Blacklist An Item', 'wprss'),
                    'view_item' => __('View Blacklisted Items', 'wprss'),
                    'search_items' => __('Search Blacklisted Items', 'wprss'),
                    'not_found' => __('No Blacklisted Items', 'wprss'),
                    'not_found_in_trash' => __('No blacklisted items found in the trash', 'wprss'),
                ];
            },
            /*
             * The capability for the feed item blacklist CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/blacklist/cpt/capability' => function () {
                return 'feed_blacklist';
            },
            /*
             * The user roles that have the feed item blacklist CPT capabilities.
             *
             * Resolves to the feed items CPT capability roles, if available.
             *
             * @since 4.13
             */
            'wpra/feeds/blacklist/cpt/capability_roles' => function (ContainerInterface $c) {
                if (!$c->has('wpra/feeds/items/cpt/capability_roles')) {
                    return ['administrator'];
                }

                return $c->get('wpra/feeds/items/cpt/capability_roles');
            },
            /*
             * The full arguments for the feed item blacklist CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/blacklist/cpt/args' => function (ContainerInterface $c) {
                return [
                    'public' => false,
                    'exclude_from_search' => true,
                    'show_ui' => true,
                    'show_in_menu' => false,
                    'capability_type' => $c->get('wpra/feeds/blacklist/cpt/capability'),
                    'map_meta_cap' => true,
                    'supports' => ['title'],
                    'labels' => $c->get('wpra/feeds/blacklist/cpt/labels'),
                ];
            },
            /*
             * The handler that registers the feed item blacklist CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/blacklist/handlers/register_cpt' => function (ContainerInterface $c) {
                return new RegisterCptHandler(
                    $c->get('wpra/feeds/blacklist/cpt/name'),
                    $c->get('wpra/feeds/blacklist/cpt/args')
                );
            },
            /*
             * The handler that adds the feed item blacklist CPT capabilities to the appropriate user roles.
             *
             * Resolves to a null handler if the WordPress role manager is not available.
             *
             * @since 4.13
             */
            'wpra/feeds/blacklist/handlers/add_cpt_capabilities' => function (ContainerInterface $c) {
                if (!$c->has('wp/roles')) {
                    return new NullHandler();
                }

                return new AddCptMetaCapsHandler(
                    $c->get('wp/roles'),
                    $c->get('wpra/feeds/blacklist/cpt/capability_roles'),
                    $c->get('wpra/feeds/blacklist/cpt/capability')
                );
            },
            /*
             * The properties for blacklist entities.
             *
             * @since 4.17
             */
            'wpra/feeds/blacklist/properties' => function () {
                return [
                    'id' => new Property('ID'),
                    'title' => new Property('post_title'),
                    'url' => new Property('wprss_permalink'),
                ];
            },
            /*
             * The default values for blacklist entity properties.
             *
             * @since 4.17
             */
            'wpra/feeds/blacklist/defaults' => function () {
                return [
                    'id' => null,
                    'title' => '',
                    'url' => '',
                ];
            },
            /*
             * The schema for blacklist entities.
             *
             * @since 4.17
             */
            'wpra/feeds/blacklist/schema' => function (ContainerInterface $c) {
                return new Schema(
                    $c->get('wpra/feeds/blacklist/properties'),
                    $c->get('wpra/feeds/blacklist/defaults')
                );
            },
            /*
             * The collection for blacklist entities.
             *
             * @since 4.17
             */
            'wpra/feeds/blacklist/collection' => function (ContainerInterface $c) {
                return new FeedBlacklistCollection(
                    $c->get('wpra/feeds/blacklist/cpt/name'),
                    $c->get('wpra/feeds/blacklist/schema')
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
        add_action('init', $c->get('wpra/feeds/blacklist/handlers/register_cpt'), 11);
        add_action('admin_init', $c->get('wpra/feeds/blacklist/handlers/add_cpt_capabilities'));
    }
}
