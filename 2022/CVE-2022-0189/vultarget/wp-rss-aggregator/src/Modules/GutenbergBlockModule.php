<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Handlers\GutenbergBlock\FetchFeedSourcesHandler;
use RebelCode\Wpra\Core\Handlers\RegisterGutenbergBlockHandler;
use RebelCode\Wpra\Core\Wp\Asset\AssetInterface;
use RebelCode\Wpra\Core\Wp\Asset\ScriptAsset;
use RebelCode\Wpra\Core\Wp\Asset\StyleAsset;

/**
 * The Gutenberg block for WP RSS Aggregator.
 *
 * @since 4.13
 */
class GutenbergBlockModule implements ModuleInterface
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
             * The Gutenberg block name.
             *
             * @since 4.13
             */
            'wpra/gutenberg_block/name' => function (ContainerInterface $c) {
                return 'wpra-shortcode/wpra-shortcode';
            },

            /*
             * Available Gutenberg block attributes.
             *
             * @since 4.13
             */
            'wpra/gutenberg_block/attributes' => function (ContainerInterface $c) {
                return [
                    'isAll' => [
                        'type' => 'boolean',
                        'default' => true,
                    ],
                    'template' => [
                        'type' => 'string',
                        'default' => 'default',
                    ],
                    'pagination' => [
                        'type' => 'boolean',
                        'default' => true,
                    ],
                    'page' => [
                        'type' => 'number',
                    ],
                    'limit' => [
                        'type' => 'number',
                    ],
                    'exclude' => [
                        'type' => 'string'
                    ],
                    'source' => [
                        'type' => 'string'
                    ],
                    'className' => [
                        'type' => 'string'
                    ],
                ];
            },

            /*
             * The Gutenberg block configuration.
             *
             * @since 4.13
             */
            'wpra/gutenberg_block/config' => function (ContainerInterface $c) {
                return [
                    'attributes' => $c->get('wpra/gutenberg_block/attributes'),
                    'render_callback' => $c->get('wpra/shortcode/feeds/handler')
                ];
            },

            /*
             * The Gutenberg block configuration.
             *
             * @since 4.13
             */
            'wpra/gutenberg_block/handlers/register' => function (ContainerInterface $c) {
                return new RegisterGutenbergBlockHandler(
                    $c->get('wpra/gutenberg_block/name'),
                    $c->get('wpra/gutenberg_block/config')
                );
            },

            /*
             * The list of the block's assets.
             *
             * @since 4.14
             */
            'wpra/gutenberg_block/assets' => function (ContainerInterface $c) {
                return [
                    'gutenberg_script' => $c->get('wpra/gutenberg_block/scripts/main'),
                    'gutenberg_style' => $c->get('wpra/gutenberg_block/styles/main'),
                ];
            },

            /*
             * The block's style.
             *
             * @since 4.14
             */
            'wpra/gutenberg_block/styles/main' => function (ContainerInterface $c) {
                return new StyleAsset('wpra-gutenberg-block', WPRSS_APP_CSS . 'gutenberg-block.min.css');
            },
            /*
             * The block's script.
             *
             * @since 4.14
             */
            'wpra/gutenberg_block/scripts/main' => function (ContainerInterface $c) {
                $script = new ScriptAsset('wpra-gutenberg-block', WPRSS_APP_JS . 'gutenberg-block.min.js', [
                    'wp-hooks',
                    'wp-blocks',
                    'wp-i18n',
                    'wp-element',
                    'wp-editor',
                ]);

                return $script->localize('WPRA_BLOCK', function () use ($c) {
                    return $c->get('wpra/gutenberg_block/states/main');
                });
            },
            /*
             * Gutenberg block script state.
             *
             * @since 4.14
             */
            'wpra/gutenberg_block/states/main' => function (ContainerInterface $c) {
                $templatesCollection = $c->get('wpra/feeds/templates/collection');
                $templates = [];

                foreach ($templatesCollection as $template) {
                    $tOptions = isset($template['options']) ? $template['options'] : [];
                    $templates[] = [
                        'label' => $template['name'],
                        'value' => $template['slug'],
                        'limit' => isset($tOptions['limit']) ? $tOptions['limit'] : 15,
                        'pagination' => isset($tOptions['pagination']) ? $tOptions['pagination'] : true,
                    ];
                }

                return [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'templates' => $templates,
                    'is_et_active' => wprss_is_et_active(),
                ];
            },

            /*
             * The handler for retrieving feed sources in Gutenberg block.
             *
             * @since 4.13
             */
            'wpra/gutenberg_block/handlers/fetch_feed_sources' => function (ContainerInterface $c) {
                return new FetchFeedSourcesHandler();
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
        // Registers the block
        call_user_func($c->get('wpra/gutenberg_block/handlers/register'));

        // Enqueues the assets for the block
        add_action('enqueue_block_editor_assets', function () use ($c) {
            $assets = $c->get('wpra/gutenberg_block/assets');

            /* @var $assets AssetInterface[] */
            foreach ($assets as $asset) {
                $asset->register();
                $asset->enqueue();
            }
        });

        // Adds the AJAX listener for fetching feed sources from the block
        add_action('wp_ajax_wprss_fetch_items', $c->get('wpra/gutenberg_block/handlers/fetch_feed_sources'));
    }
}
