<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Entities\Entity;
use RebelCode\Entities\Properties\DefaultingProperty;
use RebelCode\Entities\Properties\Property;
use RebelCode\Entities\Schemas\Schema;
use RebelCode\Wpra\Core\Data\EntityDataSet;
use RebelCode\Wpra\Core\Entities\Collections\FeedItemCollection;
use RebelCode\Wpra\Core\Entities\Properties\TimestampProperty;
use RebelCode\Wpra\Core\Entities\Properties\WpFtImageUrlProperty;
use RebelCode\Wpra\Core\Entities\Properties\WpPostEntityProperty;
use RebelCode\Wpra\Core\Entities\Properties\WpPostPermalinkProperty;
use RebelCode\Wpra\Core\Entities\Properties\WpraItemSourceProperty;
use RebelCode\Wpra\Core\Entities\Properties\WpraPostTypeDependentProperty;
use RebelCode\Wpra\Core\Handlers\AddCptMetaCapsHandler;
use RebelCode\Wpra\Core\Handlers\NullHandler;
use RebelCode\Wpra\Core\Handlers\RegisterCptHandler;

/**
 * The feed items module for WP RSS Aggregator.
 *
 * @since 4.13
 */
class FeedItemsModule implements ModuleInterface
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
             * The properties for feed item entities.
             *
             * @since 4.16
             */
            'wpra/feeds/items/properties' => function (ContainerInterface $c) {
                $sourceSchema = $c->get('wpra/feeds/sources/schema');

                $idProp = new Property('ID');

                $urlProp = new WpraPostTypeDependentProperty(
                    $idProp,
                    new Property('wprss_item_permalink'),
                    new WpPostPermalinkProperty($idProp)
                );

                $enclosureProp = new WpraPostTypeDependentProperty(
                    $idProp,
                    new Property('wprss_item_enclosure'),
                    new Property('wprss_ftp_enclosure_link')
                );

                return [
                    'id' => $idProp,
                    'title' => new Property('post_title'),
                    'content' => new DefaultingProperty(['post_content', 'post_excerpt']),
                    'excerpt' => new DefaultingProperty(['post_excerpt', 'post_content']),
                    'url' => $urlProp,
                    'permalink' => $urlProp,
                    'enclosure' => $enclosureProp,
                    'enclosure_type' => new Property('wprss_item_enclosure_type'),
                    'author' => new Property('wprss_item_author'),
                    'date' => new Property('wprss_item_date'),
                    'timestamp' => new TimestampProperty(new Property('post_date_gmt'), 'Y-m-d H:i:s'),
                    'ft_image' => new Property('_thumbnail_id'),
                    'ft_image_url' => new WpFtImageUrlProperty('_thumbnail_id', 'wprss_item_thumbnail'),
                    'is_using_def_image' => new Property('wprss_item_is_using_def_image'),
                    'images' => new Property('wprss_images'),
                    'best_image' => new Property('wprss_best_image'),
                    'embed_url' => new Property('wprss_item_embed_url'),
                    'is_yt' => new Property('wprss_item_is_yt'),
                    'yt_embed_url' => new Property('wprss_item_yt_embed_url'),
                    'audio_url' => new Property('wprss_item_audio'),
                    'source_id' => new Property('wprss_feed_id'),
                    'source_name' => new WpraItemSourceProperty(
                        new Property('wprss_item_source_name'),
                        new Property('post_title'),
                        'use_source_info'
                    ),
                    'source_url' => new WpraItemSourceProperty(
                        new Property('wprss_item_source_url'),
                        new Property('wprss_url'),
                        'use_source_info'
                    ),
                    // @todo remove after templates 0.2
                    'source' => new WpPostEntityProperty('wprss_feed_id', $sourceSchema, function ($schema, $store) {
                        return new EntityDataSet(new Entity($schema, $store));
                    }),
                ];
            },
            /*
             * The default values for feed item entities.
             *
             * @since 4.16
             */
            'wpra/feeds/items/defaults' => function (ContainerInterface $c) {
                return [
                    'id' => null,
                    'title' => '',
                    'content' => '',
                    'excerpt' => '',
                    'url' => '',
                    'permalink' => '',
                    'enclosure' => '',
                    'author' => '',
                    'date' => '',
                    'timestamp' => 0,
                    'source_id' => null,
                    'source_name' => '',
                    'source_url' => '',
                    'ft_image' => null,
                    'ft_image_url' => '',
                    'images' => [],
                    'best_image' => null,
                    'embed_url' => '',
                    'is_yt' => false,
                    'yt_embed_url' => '',
                ];
            },
            /*
             * The schema for feed items.
             *
             * @since 4.16
             */
            'wpra/feeds/items/schema' => function (ContainerInterface $c) {
                return new Schema(
                    $c->get('wpra/feeds/items/properties'),
                    $c->get('wpra/feeds/items/defaults')
                );
            },
            /*
             * The name of the feed items CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/items/cpt/name' => function () {
                return 'wprss_feed_item';
            },
            /*
             * The labels for the feed items CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/items/cpt/labels' => function () {
                return [
                    'name' => __('Feed Items', 'wprss'),
                    'singular_name' => __('Feed Item', 'wprss'),
                    'all_items' => __('Feed Items', 'wprss'),
                    'view_item' => __('View Feed Items', 'wprss'),
                    'search_items' => __('Search Feed Items', 'wprss'),
                    'not_found' => __('No Feed Items Found', 'wprss'),
                    'not_found_in_trash' => __('No Feed Items Found In Trash', 'wprss'),
                ];
            },
            /*
             * The capability for the feed items CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/items/cpt/capability' => function () {
                return 'feed_item';
            },
            /*
             * The user roles that have the feed items CPT capabilities.
             *
             * Resolves to the feed sources CPT capability roles, if available.
             *
             * @since 4.13
             */
            'wpra/feeds/items/cpt/capability_roles' => function (ContainerInterface $c) {
                if (!$c->has('wpra/feeds/sources/cpt/capability_roles')) {
                    return ['administrator'];
                }

                return $c->get('wpra/feeds/sources/cpt/capability_roles');
            },
            /*
             * The full arguments for the feed items CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/items/cpt/args' => function (ContainerInterface $c) {
                return [
                    'exclude_from_search' => true,
                    'publicly_queryable' => false,
                    'show_in_nav_menus' => false,
                    'show_in_admin_bar' => false,
                    'public' => false,
                    'show_ui' => true,
                    'query_var' => false,
                    'show_in_menu' => 'edit.php?post_type=wprss_feed',
                    'rewrite' => false,
                    'capability_type' => $c->get('wpra/feeds/items/cpt/capability'),
                    'map_meta_cap' => true,
                    'labels' => $c->get('wpra/feeds/items/cpt/labels'),
                    'supports' => ['title', 'editor', 'excerpt'],
                ];
            },
            /*
             * The collection for feed items.
             *
             * @since 4.13
             */
            'wpra/feeds/items/collection' => function (ContainerInterface $c) {
                return new FeedItemCollection(
                    $c->get('wpra/feeds/items/cpt/name'),
                    $c->get('wpra/feeds/items/schema')
                );
            },
            /*
             * The handler that registers the feed items CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/items/handlers/register_cpt' => function (ContainerInterface $c) {
                return new RegisterCptHandler(
                    $c->get('wpra/feeds/items/cpt/name'),
                    $c->get('wpra/feeds/items/cpt/args')
                );
            },
            /*
             * The handler that adds the feed items CPT capabilities to the appropriate user roles.
             *
             * Resolves to a null handler if the WordPress role manager is not available.
             *
             * @since 4.13
             */
            'wpra/feeds/items/handlers/add_cpt_capabilities' => function (ContainerInterface $c) {
                if (!$c->has('wp/roles')) {
                    return new NullHandler();
                }

                return new AddCptMetaCapsHandler(
                    $c->get('wp/roles'),
                    $c->get('wpra/feeds/items/cpt/capability_roles'),
                    $c->get('wpra/feeds/items/cpt/capability')
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
        add_action('init', $c->get('wpra/feeds/items/handlers/register_cpt'));
        add_action('admin_init', $c->get('wpra/feeds/items/handlers/add_cpt_capabilities'));

        // Set the public permalink of feed items to be equal to the URL to the original article
        add_filter('post_type_link', function($url, $post) {
            return (get_post_type($post->ID) === 'wprss_feed_item')
                ? get_post_meta($post->ID, 'wprss_item_permalink', true)
                : $url;
        }, 10, 2);
    }
}
