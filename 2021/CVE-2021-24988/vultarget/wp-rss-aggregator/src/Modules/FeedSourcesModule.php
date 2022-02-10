<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Entities\Properties\AliasProperty;
use RebelCode\Entities\Properties\Property;
use RebelCode\Entities\Schemas\Schema;
use RebelCode\Wpra\Core\Entities\Collections\FeedSourceCollection;
use RebelCode\Wpra\Core\Entities\Properties\SanitizedProperty;
use RebelCode\Wpra\Core\Handlers\AddCapabilitiesHandler;
use RebelCode\Wpra\Core\Handlers\AddCptMetaCapsHandler;
use RebelCode\Wpra\Core\Handlers\FeedSources\FeedSourceSaveMetaHandler;
use RebelCode\Wpra\Core\Handlers\FeedSources\RenderFeedSourceContentHandler;
use RebelCode\Wpra\Core\Handlers\MultiHandler;
use RebelCode\Wpra\Core\Handlers\NullHandler;
use RebelCode\Wpra\Core\Handlers\RegisterCptHandler;
use RebelCode\Wpra\Core\Handlers\RenderMetaBoxTemplateHandler;
use RebelCode\Wpra\Core\RestApi\EndPoints\EndPoint;
use RebelCode\Wpra\Core\RestApi\EndPoints\Handlers\GetEntityHandler;
use RebelCode\Wpra\Core\Templates\NullTemplate;
use RebelCode\Wpra\Core\Util\Sanitizers\BoolSanitizer;
use RebelCode\Wpra\Core\Util\Sanitizers\CallbackSanitizer;
use RebelCode\Wpra\Core\Util\Sanitizers\IntSanitizer;

/**
 * The feed sources module for WP RSS Aggregator.
 *
 * @since 4.13
 */
class FeedSourcesModule implements ModuleInterface
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
             * The properties for feed source entities.
             *
             * @since 4.16
             */
            'wpra/feeds/sources/properties' => function (ContainerInterface $c) {
                return [
                    // == Basic info ==
                    'id' => new Property('ID'),
                    'name' => new Property('post_title'),
                    'slug' => new Property('post_name'),
                    'active' => new SanitizedProperty(
                        new Property('wprss_state'),
                        new CallbackSanitizer(function ($state) {
                            return empty($state) || strtolower($state) === 'active';
                        })
                    ),
                    // == Import options ==
                    'url' => new Property('wprss_url'),
                    'import_source' => new SanitizedProperty(
                        new Property('wprss_import_source'),
                        new BoolSanitizer()
                    ),
                    'use_source_info' => new AliasProperty('import_source'),
                    'import_limit' => new SanitizedProperty(
                        new Property('wprss_limit'),
                        new IntSanitizer(0, 0)
                    ),
                    'unique_titles_only' => new SanitizedProperty(
                        new Property('wprss_unique_titles'),
                        new BoolSanitizer()
                    ),
                    // == Cron options ==
                    'update_interval' => new Property('wprss_update_interval'),
                    'update_time' => new Property('wprss_update_time'),
                    // == Image options ==
                    'def_ft_image' => new Property('_thumbnail_id'),
                    'import_ft_images' => new Property('wprss_import_ft_images'),
                    'download_images' => new SanitizedProperty(
                        new Property('wprss_download_images'),
                        new BoolSanitizer()
                    ),
                    'download_srcset' => new SanitizedProperty(
                        new Property('wprss_download_srcset'),
                        new BoolSanitizer()
                    ),
                    'siphon_ft_image' => new SanitizedProperty(
                        new Property('wprss_siphon_ft_image'),
                        new BoolSanitizer()
                    ),
                    'must_have_ft_image' => new SanitizedProperty(
                        new Property('wprss_must_have_ft_image'),
                        new BoolSanitizer()
                    ),
                    'image_min_width' => new SanitizedProperty(
                        new Property('wprss_image_min_width'),
                        new IntSanitizer(0, 0)
                    ),
                    'image_min_height' => new SanitizedProperty(
                        new Property('wprss_image_min_height'),
                        new IntSanitizer(0, 0)
                    ),
                    // @todo remove after templates 0.2
                    'title' => new Property('post_title'),
                ];
            },
            /*
             * The default values for feed source entities.
             *
             * @since 4.16
             */
            'wpra/feeds/sources/defaults' => function (ContainerInterface $c) {
                return [
                    'id' => null,
                    'name' => '',
                    'active' => true,
                    'url' => '',
                    'import_source' => false,
                    'import_limit' => 0,
                    'unique_titles_only' => wprss_get_general_setting('unique_titles'),
                    'def_ft_image' => null,
                    'import_ft_images' => '',
                    'download_images' => false,
                    'download_srcset' => false,
                    'siphon_ft_image' => false,
                    'must_have_ft_image' => false,
                    'image_min_width' => 150,
                    'image_min_height' => 150,
                ];
            },
            /*
             * The schema for feed source entities.
             *
             * @since 4.16
             */
            'wpra/feeds/sources/schema' => function (ContainerInterface $c) {
                return new Schema(
                    $c->get('wpra/feeds/sources/properties'),
                    $c->get('wpra/feeds/sources/defaults')
                );
            },
            /*
             * The collection for feed sources.
             *
             * @since 4.14
             */
            'wpra/feeds/sources/collection' => function (ContainerInterface $c) {
                return new FeedSourceCollection(
                    $c->get('wpra/feeds/sources/cpt/name'),
                    $c->get('wpra/feeds/sources/schema')
                );
            },
            /*
             * The name of the feed sources CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/cpt/name' => function () {
                return 'wprss_feed';
            },
            /*
             * The labels for the feed sources CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/cpt/labels' => function () {
                return [
                    'name' => __('Feed Sources', 'wprss'),
                    'singular_name' => __('Feed Source', 'wprss'),
                    'add_new' => __('Add New', 'wprss'),
                    'all_items' => __('Feed Sources', 'wprss'),
                    'add_new_item' => __('Add New Feed Source', 'wprss'),
                    'edit_item' => __('Edit Feed Source', 'wprss'),
                    'new_item' => __('New Feed Source', 'wprss'),
                    'view_item' => __('View Feed Source', 'wprss'),
                    'search_items' => __('Search Feeds', 'wprss'),
                    'not_found' => __('No Feed Sources Found', 'wprss'),
                    'not_found_in_trash' => __('No Feed Sources Found In Trash', 'wprss'),
                    'menu_name' => __('RSS Aggregator', 'wprss'),
                ];
            },
            /*
             * The capability for the feed sources CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/cpt/capability' => function () {
                return 'feed_source';
            },
            /*
             * The full arguments for the feed sources CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/cpt/args' => function (ContainerInterface $c) {
                return [
                    'exclude_from_search' => true,
                    'publicly_queryable' => false,
                    'show_in_nav_menus' => false,
                    'show_in_admin_bar' => true,
                    'public' => false,
                    'show_ui' => true,
                    'query_var' => 'feed_source',
                    'menu_position' => 100,
                    'show_in_menu' => true,
                    'rewrite' => [
                        'slug' => 'feeds',
                        'with_front' => false,
                    ],
                    'capability_type' => $c->get('wpra/feeds/sources/cpt/capability'),
                    'map_meta_cap' => true,
                    'supports' => ['title'],
                    'labels' => $c->get('wpra/feeds/sources/cpt/labels'),
                    'menu_icon' => 'dashicons-rss',
                ];
            },
            /*
             * The user roles that have the feed sources CPT capabilities.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/cpt/capability_roles' => function () {
                return ['administrator', 'editor'];
            },
            /*
             * The capability for the feed sources CPT admin menu.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/menu/capability' => function () {
                return 'manage_feed_settings';
            },
            /*
             * The user roles that have the feed sources CPT admin menu capabilities.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/menu/capability_roles' => function (ContainerInterface $c) {
                // Identical to CPT roles
                return $c->get('wpra/feeds/sources/cpt/capability_roles');
            },
            /*
             * The handler that registers the feed sources CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/handlers/register_cpt' => function (ContainerInterface $c) {
                return new RegisterCptHandler(
                    $c->get('wpra/feeds/sources/cpt/name'),
                    $c->get('wpra/feeds/sources/cpt/args')
                );
            },
            /*
             * The template used to render feed source content.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/content_template' => function (ContainerInterface $c) {
                if ($c->has('wpra/display/feeds/template')) {
                    return $c->get('wpra/display/feeds/template');
                }

                return new NullTemplate();
            },
            /*
             * The handler that renders a feed source's content on the front-end.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/handlers/render_content' => function (ContainerInterface $c) {
                return new RenderFeedSourceContentHandler($c->get('wpra/feeds/sources/content_template'));
            },
            /*
             * The handler that adds the capability that allows users to see and access the admin menu.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/handlers/add_menu_capabilities' => function (ContainerInterface $c) {
                if (!$c->has('wp/roles')) {
                    return new NullHandler();
                }

                return new AddCapabilitiesHandler(
                    $c->get('wp/roles'),
                    $c->get('wpra/feeds/sources/menu/capability_roles'),
                    [$c->get('wpra/feeds/sources/menu/capability')]
                );
            },
            /*
             * The handler that adds the CPT's capabilities to the appropriate user roles.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/handlers/add_cpt_capabilities' => function (ContainerInterface $c) {
                return new AddCptMetaCapsHandler(
                    $c->get('wp/roles'),
                    $c->get('wpra/feeds/sources/cpt/capability_roles'),
                    $c->get('wpra/feeds/sources/cpt/capability')
                );
            },
            /*
             * The full handler for adding all capabilities related to the feed sources CPT.
             *
             * @since 4.13
             */
            'wpra/feeds/sources/add_capabilities_handler' => function (ContainerInterface $c) {
                return new MultiHandler([
                    $c->get('wpra/feeds/sources/handlers/add_menu_capabilities'),
                    $c->get('wpra/feeds/sources/handlers/add_cpt_capabilities'),
                ]);
            },
            /*
             * The handler that renders the source info meta box on the edit page.
             *
             * @since 4.17
             */
            'wpra/feeds/sources/meta_boxes/save/renderer' => function (ContainerInterface $c) {
                return new RenderMetaBoxTemplateHandler(
                    $c->get('wpra/twig/collection')['admin/feeds/save-meta-box.twig'],
                    $c->get('wpra/feeds/sources/collection'),
                    ['wprss_feed'],
                    'feed'
                );
            },
            /*
             * The handler that renders the shortcode on the edit page.
             *
             * @since 4.17
             */
            'wpra/feeds/sources/meta_boxes/shortcode/renderer' => function (ContainerInterface $c) {
                return new RenderMetaBoxTemplateHandler(
                    $c->get('wpra/twig/collection')['admin/feeds/shortcode.twig'],
                    $c->get('wpra/feeds/sources/collection'),
                    ['wprss_feed'],
                    'feed'
                );
            },
            /*
             * The handler that saves meta data for feed sources when saved through the edit page.
             *
             * @since 4.14
             */
            'wpra/feeds/sources/meta_box/save_handler' => function (ContainerInterface $c) {
                return new FeedSourceSaveMetaHandler(
                    $c->get('wpra/feeds/sources/cpt/name'),
                    $c->get('wpra/feeds/sources/collection')
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
        return [
            /*
             * Extends the list of REST API endpoints to register the feed sources endpoints.
             *
             * @since 4.18
             */
            'wpra/rest_api/v1/endpoints' => function (ContainerInterface $c, $endpoints) {
                $endpoints['get_sources'] = new EndPoint(
                    '/sources(?:/(?P<id>[^/]+))?',
                    ['GET'],
                    new GetEntityHandler($c->get('wpra/feeds/sources/collection'), 'id', []),
                    $c->get('wpra/rest_api/v1/auth/user_is_admin')
                );

                return $endpoints;
            }
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function run(ContainerInterface $c)
    {
        add_action('init', $c->get('wpra/feeds/sources/handlers/register_cpt'));
        add_filter('the_content', $c->get('wpra/feeds/sources/handlers/render_content'));
        add_action('admin_init', $c->get('wpra/feeds/sources/add_capabilities_handler'));
        add_action('save_post', $c->get('wpra/feeds/sources/meta_box/save_handler'), 20, 2);

        // Show shortcode under feed title input field on the edit page
        add_action('edit_form_after_title', $c->get('wpra/feeds/sources/meta_boxes/shortcode/renderer'));
        // Show extra options in the save metabox on the edit page
        add_action('post_submitbox_start', $c->get('wpra/feeds/sources/meta_boxes/save/renderer'));
    }
}
