<?php

namespace RebelCode\Wpra\Core\Modules;

use Aventura\Wprss\Core\Caching\ImageCache;
use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use RebelCode\Wpra\Core\Entities\Feeds\Items\WpPostFeedItem;
use RebelCode\Wpra\Core\Handlers\Images\AddItemsImageColumnHandler;
use RebelCode\Wpra\Core\Handlers\Images\DeleteImagesHandler;
use RebelCode\Wpra\Core\Handlers\Images\RemoveFtImageMetaBoxHandler;
use RebelCode\Wpra\Core\Handlers\Images\RenderItemsImageColumnHandler;
use RebelCode\Wpra\Core\Handlers\RegisterMetaBoxHandler;
use RebelCode\Wpra\Core\Handlers\RenderTemplateHandler;
use RebelCode\Wpra\Core\Importer\Images\FbImageContainer;
use RebelCode\Wpra\Core\Importer\Images\ImageContainer;
use WPRSS_Help;

/**
 * The module that adds image importing functionality to WP RSS Aggregator.
 *
 * @since 4.14
 */
class ImagesModule implements ModuleInterface
{
    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function getFactories()
    {
        return [
            /*
             * The flag that controls whether UI for controlling image importing is enabled or not.
             *
             * @since 4.14
             */
            'wpra/images/ui_enabled' => function (ContainerInterface $c) {
                return false;
            },
            /*
             * The flag that controls whether logging for image importing is enabled or not.
             *
             * @since 4.14
             */
            'wpra/images/logging/enabled' => function (ContainerInterface $c) {
                return false;
            },
            /*
             * The logger for image importing, that only logs if image logging is enabled.
             *
             * @since 4.14
             */
            'wpra/images/logging/logger' => function (ContainerInterface $c) {
                // If image logging is disabled, return a null logger
                if (!$c->get('wpra/images/logging/enabled')) {
                    return new NullLogger();
                }

                // Get the original logger from WPRA's logger module, if available
                return $c->has('wpra/logging/logger')
                    ? $c->get('wpra/logging/logger')
                    : new NullLogger();
            },
            /*
             * Whether the feature to import featured images is enabled or not.
             *
             * @since 4.14
             */
            'wpra/images/features/import_ft_images' => function () {
                return false;
            },
            /*
             * Whether the feature to restrict images by size is enabled or not.
             *
             * @since 4.14
             */
            'wpra/images/features/image_min_size' => function () {
                return false;
            },
            /*
             * Whether the feature to download non-featured images is enabled or not.
             *
             * @since 4.14
             */
            'wpra/images/features/download_images' => function () {
                return false;
            },
            /*
             * Whether the feature to remove featured images from the content is enabled or not.
             *
             * @since 4.14
             */
            'wpra/images/features/siphon_ft_image' => function () {
                return false;
            },
            /*
             * Whether the feature to reject items without featured images is enabled or not.
             *
             * @since 4.14
             */
            'wpra/images/features/must_have_ft_image' => function () {
                return false;
            },
            /*
             * The image cache manager instance.
             *
             * @since 4.14
             */
            'wpra/images/cache' => function (ContainerInterface $c) {
                $cache = new ImageCache();
                $cache->set_ttl($c->get('wpra/images/cache/ttl'));

                return $cache;
            },
            /*
             * The time-to-live, in seconds, for cached images.
             *
             * @since 4.14
             */
            'wpra/images/cache/ttl' => function () {
                return WEEK_IN_SECONDS;
            },
            /*
             * The container for remote images.
             *
             * @since 4.14
             */
            'wpra/images/container' => function (ContainerInterface $c) {
                return new ImageContainer(
                    $c->get('wpra/images/cache'),
                    $c->get('wpra/logging/logger')
                );
            },
            /*
             * The feed items image column key.
             *
             * @since 4.14
             */
            'wpra/images/items/column/key' => function () {
                return 'image';
            },
            /*
             * The feed items image column name.
             *
             * @since 4.14
             */
            'wpra/images/items/column/name' => function () {
                return __('Image', 'wprss');
            },
            /*
             * The feed items image column position.
             *
             * @since 4.14
             */
            'wpra/images/items/column/position' => function () {
                return 1;
            },
            /*
             * The handler that adds the image column to the items list page.
             *
             * @since 4.14
             */
            'wpra/images/items/column/handler/add' => function (ContainerInterface $c) {
                return new AddItemsImageColumnHandler(
                    $c->get('wpra/images/items/column/key'),
                    $c->get('wpra/images/items/column/name'),
                    $c->get('wpra/images/items/column/position')
                );
            },
            /*
             * The handler that renders the contents of the image column in the items list page.
             *
             * @since 4.14
             */
            'wpra/images/items/column/handler/render' => function (ContainerInterface $c) {
                return new RenderItemsImageColumnHandler(
                    $c->get('wpra/feeds/items/collection'),
                    $c->get('wpra/images/items/column/key')
                );
            },
            /*
             * The handler that registers the feed sources images meta box.
             *
             * @since 4.14
             */
            'wpra/images/feeds/meta_box/handler/register' => function (ContainerInterface $c) {
                return new RegisterMetaBoxHandler(
                    'wpra-images',
                    __('Images', 'wprss'),
                    $c->get('wpra/images/feeds/meta_box/handler/render'),
                    'wprss_feed'
                );
            },
            /*
             * The handler that renders the feed sources image options meta box.
             *
             * @since 4.14
             */
            'wpra/images/feeds/meta_box/handler/render' => function (ContainerInterface $c) {
                return new RenderTemplateHandler(
                    $c->get('wpra/images/feeds/meta_box/template'),
                    $c->get('wpra/images/feeds/meta_box/template/context'),
                    true
                );
            },
            /*
             * The template for the feed sources image options meta box.
             *
             * @since 4.14
             */
            'wpra/images/feeds/meta_box/template' => function (ContainerInterface $c) {
                return $c->get('wpra/twig/collection')['admin/feeds/images-meta-box.twig'];
            },
            /*
             * The context for the feed sources image options meta box template.
             *
             * @since 4.14
             */
            'wpra/images/feeds/meta_box/template/context' => function (ContainerInterface $c) {
                return function () use ($c) {
                    global $post;

                    $collection = $c->get('wpra/feeds/sources/collection');
                    $info = isset($collection[$post->ID])
                        ? $collection[$post->ID]->export()
                        : [];

                    // Get the URL for the default ft. image and add it to the info
                    if (array_key_exists('def_ft_image', $info)) {
                        $info['def_ft_image_url'] = wp_get_attachment_url($info['def_ft_image']);
                    }

                    return [
                        'feed' => $info,
                        'options' => $c->get('wpra/images/feeds/meta_box/template/enabled_options'),
                    ];
                };
            },
            /*
             * The image options that should be shown in the feed sources image meta box.
             *
             * @since 4.14
             */
            'wpra/images/feeds/meta_box/template/enabled_options' => function (ContainerInterface $c) {
                return [
                    'import_ft_images' => $c->get('wpra/images/features/import_ft_images'),
                    'image_min_size' => $c->get('wpra/images/features/image_min_size'),
                    'download_images' => $c->get('wpra/images/features/download_images'),
                    'siphon_ft_image' => $c->get('wpra/images/features/siphon_ft_image'),
                    'must_have_ft_image' => $c->get('wpra/images/features/must_have_ft_image'),
                ];
            },
            /*
             * The handler that removes the WordPress featured image meta box.
             *
             * @since 4.14
             */
            'wpra/images/ft_image/meta_box/handler' => function (ContainerInterface $c) {
                return new RemoveFtImageMetaBoxHandler();
            },
            /*
             * The help tooltips for the feed sources images meta box.
             *
             * @since 4.14
             */
            'wpra/images/feeds/meta_box/tooltips' => function () {
                return [
                    'ft_image' => __(
                        "This option allows you to select which feed item image to use as the featured image. Automatic best image detection will attempt to find the largest image with the best aspect ratio.

                        WordPress requires that featured images exist in the media library, so WP RSS Aggregator will always download and save featured images.",
                        'wprss'
                    ),
                ];
            },
            /*
             * The handler for the developer images meta box for feed items.
             *
             * @since 4.14
             */
            'wpra/images/items/dev_meta_box/handler' => function (ContainerInterface $c) {
                $items = $c->get('wpra/feeds/items/collection');
                $template = $c->get('wpra/twig/collection')['admin/items/images-meta-box.twig'];

                return new RegisterMetaBoxHandler(
                    'wpra-dev-items-images',
                    __('Images', 'wprss'),
                    new RenderTemplateHandler($template, function () use ($items) {
                        global $post;
                        return [
                            'item' => $items[$post->ID],
                        ];
                    }, true),
                    'wprss_feed_item'
                );
            },
            /*
             * The handler that deletes an imported item's featured image when the item is deleted.
             *
             * @since 4.14
             */
            'wpra/images/items/handlers/delete_images' => function (ContainerInterface $c) {
                return new DeleteImagesHandler(
                    $c->get('wpra/importer/items/collection')
                );
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function getExtensions()
    {
        return [
            /*
             * Adds featured image support to feed sources.
             *
             * @since 4.14
             */
            'wpra/feeds/sources/cpt/args' => function (ContainerInterface $c, $args) {
                $args['supports'][] = 'thumbnail';

                return $args;
            },
            /*
             * Adds featured image support to feed items.
             *
             * @since 4.14
             */
            'wpra/feeds/items/cpt/args' => function (ContainerInterface $c, $args) {
                $args['supports'][] = 'thumbnail';

                return $args;
            },
            /*
             * Decorates the images container to be able to fetch large versions of Facebook images.
             *
             * @since 4.14
             */
            'wpra/images/container' => function (ContainerInterface $c, $container) {
                return new FbImageContainer($container);
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function run(ContainerInterface $c)
    {
        // The handler that removes the WordPress featured image meta box
        add_action('add_meta_boxes', $c->get('wpra/images/ft_image/meta_box/handler'));

        // Check if the images UI is enabled
        if ($c->get('wpra/images/ui_enabled')) {
            // The handler that registers the images meta box
            add_action('add_meta_boxes', $c->get('wpra/images/feeds/meta_box/handler/register'));

            // Show the developer images meta box for feed items, if the developer filter is enabled
            if (wpra_is_dev_mode()) {
                add_action('add_meta_boxes', $c->get('wpra/images/items/dev_meta_box/handler'));
            }

            // Register the meta box tooltips
            $tooltips = $c->get('wpra/images/feeds/meta_box/tooltips');
            add_action('admin_init', function () use ($tooltips) {
                WPRSS_Help::get_instance()->add_tooltips($tooltips);
            });

            // The handler that deletes images when the respective imported item is deleted
            add_action('before_delete_post', $c->get('wpra/images/items/handlers/delete_images'));

            // Adds the image column in the feed items page
            add_filter('wprss_set_feed_item_custom_columns', $c->get('wpra/images/items/column/handler/add'));
            // Renders the image column in the feed items page
            add_action('manage_wprss_feed_item_posts_custom_column', $c->get('wpra/images/items/column/handler/render'), 10, 2 );
        }
    }
}
