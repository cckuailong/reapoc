<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Handlers\RegisterSubMenuPageHandler;
use RebelCode\Wpra\Core\Util\NullFunction;

/**
 * The module that adds upselling of the addons and other services within WP RSS Aggregator's UI.
 *
 * Specifically, it adds UI elements such as the the "More Features" page, and the upselling of the addons in the
 * "Share The Love" metabox in the Feed Source new/edit page.
 *
 * @since 4.15.1
 */
class UpsellModule implements ModuleInterface
{
    /**
     * @inheritdoc
     *
     * @since 4.15.1
     */
    public function run(ContainerInterface $c)
    {
        // Registers the "More Features" menu and page
        add_action(
            'admin_menu',
            $c->get('wpra/upsell/more_features_page/register'),
            $c->get('wpra/upsell/more_features_page/menu_pos')
        );

        // Add the add-ons list to the "Share the Love" metabox
        add_action('wpra_share_the_love_metabox', $c->get('wpra/upsell/share_the_love/render_addon_list_fn'));
    }

    /**
     * @inheritdoc
     *
     * @since 4.15.1
     */
    public function getFactories()
    {
        return array(
            /**
             * Retrieves the base names of known addons.
             *
             * @since 4.17.8
             */
            'wpra/upsell/known_addons' => function () {
                return [
                    'wp-rss-feed-to-post/wp-rss-feed-to-post.php',
                    'wp-rss-full-text-feeds/wp-rss-full-text.php',
                    'wp-rss-templates/wp-rss-templates.php',
                    'wp-rss-keyword-filtering/wp-rss-keyword-filtering.php',
                    'wp-rss-categories/wp-rss-categories.php',
                    'wp-rss-wordai/wp-rss-wordai.php',
                    'wp-rss-spinnerchief/wp-rss-spinnerchief.php',
                ];
            },
            /**
             * Retrieves the base names of installed addons, irrespective of whether they are active or not.
             *
             * @since 4.17.8
             */
            'wpra/upsell/installed_addons' => function (ContainerInterface $c) {
                $addons = $c->get('wpra/upsell/known_addons');

                return array_filter($addons, function ($basename) {
                    return wpra_get_plugin_state($basename) > 0;
                });
            },

            /*
             * The items to upsell.
             *
             * @since 4.15.1
             */
            'wpra/upsell/items' => function (ContainerInterface $c) {
                $f2pBaseName = 'wp-rss-feed-to-post/wp-rss-feed-to-post.php';
                $ftrBaseName = 'wp-rss-full-text-feeds/wp-rss-full-text.php';
                $tmpBaseName = 'wp-rss-templates/wp-rss-templates.php';
                $kwfBaseName = 'wp-rss-keyword-filtering/wp-rss-keyword-filtering.php';
                $catBaseName = 'wp-rss-categories/wp-rss-categories.php';
                $waiBaseName = 'wp-rss-wordai/wp-rss-wordai.php';
                $spcBaseName = 'wp-rss-spinnerchief/wp-rss-spinnerchief.php';

                return apply_filters('wprss_extra_addons', [
                        [
                            'code' => 'ftp',
                            'type' => 'add-on',
                            'title' => 'Feed to Post',
                            'desc' => __(
                                'An advanced importer that lets you import RSS feed items as WordPress posts or any other custom post type. You can use it to populate a website in minutes (auto-blog). This is the most popular and feature-filled extension.',
                                'wprss'
                            ),
                            'url' => 'https://www.wprssaggregator.com/extension/feed-to-post/',
                            'state' => wpra_get_plugin_state($f2pBaseName),
                            'activateUrl' => wpra_get_activate_plugin_url($f2pBaseName),
                        ],
                        [
                            'code' => 'ftr',
                            'type' => 'add-on',
                            'title' => 'Full Text RSS Feeds',
                            'desc' => __(
                                'An extension for Feed to Post that adds connectivity to our premium full text service, which allows you to import the full post content for an unlimited number of feed items per feed source, even when the feed itself doesn\'t provide it',
                                'wprss'
                            ),
                            'url' => 'https://www.wprssaggregator.com/extension/full-text-rss-feeds/',
                            'state' => wpra_get_plugin_state($ftrBaseName),
                            'activateUrl' => wpra_get_activate_plugin_url($ftrBaseName),
                        ],
                        [
                            'code' => 'tmp',
                            'type' => 'add-on',
                            'title' => 'Templates',
                            'desc' => __(
                                'Premium templates to display images and excerpts in various ways. It includes a fully customisable grid template and a list template that includes excerpts & thumbnails, both of which will spruce up your site!',
                                'wprss'
                            ),
                            'url' => 'https://www.wprssaggregator.com/extension/templates/',
                            'state' => wpra_get_plugin_state($tmpBaseName),
                            'activateUrl' => wpra_get_activate_plugin_url($tmpBaseName),
                        ],
                        [
                            'code' => 'kf',
                            'type' => 'add-on',
                            'title' => 'Keyword Filtering',
                            'desc' => __(
                                'Filters the feed items to be imported based on your own keywords, key phrases, or tags; you only get the items you\'re interested in. It is compatible with all other add-ons.',
                                'wprss'
                            ),
                            'url' => 'https://www.wprssaggregator.com/extension/keyword-filtering/',
                            'state' => wpra_get_plugin_state($kwfBaseName),
                            'activateUrl' => wpra_get_activate_plugin_url($kwfBaseName),
                        ],
                        [
                            'code' => 'cat',
                            'type' => 'add-on',
                            'title' => 'Source Categories',
                            'desc' => __(
                                'Categorises your feed sources and allows you to display feed items from a particular category within your site using the shortcode parameters.',
                                'wprss'
                            ),
                            'url' => 'https://www.wprssaggregator.com/extension/categories/',
                            'state' => wpra_get_plugin_state($catBaseName),
                            'activateUrl' => wpra_get_activate_plugin_url($catBaseName),
                        ],
                        [
                            'code' => 'wai',
                            'type' => 'add-on',
                            'title' => 'WordAi',
                            'desc' => __(
                                'An extension for Feed to Post that allows you to integrate the WordAi article spinner so that the imported content is both completely unique and completely readable.',
                                'wprss'
                            ),
                            'url' => 'https://www.wprssaggregator.com/extension/wordai/',
                            'state' => wpra_get_plugin_state($waiBaseName),
                            'activateUrl' => wpra_get_activate_plugin_url($waiBaseName),
                        ],
                        [
                            'code' => 'spc',
                            'type' => 'add-on',
                            'title' => 'SpinnerChief',
                            'desc' => __(
                                'An extension for Feed to Post that allows you to integrate the SpinnerChief article spinner so that the imported content is both completely unique and completely readable.',
                                'wprss'
                            ),
                            'url' => 'https://www.wprssaggregator.com/extension/spinnerchief/',
                            'state' => wpra_get_plugin_state($spcBaseName),
                            'activateUrl' => wpra_get_activate_plugin_url($spcBaseName),
                        ],
                    ]
                );
            },
            /*
             * The function that registers the "More Features" page and menu.
             *
             * @since 4.15.1
             */
            'wpra/upsell/more_features_page/register' => function (ContainerInterface $c) {
                return new RegisterSubMenuPageHandler([
                    'parent' => $c->get('wpra/upsell/more_features_page/parent'),
                    'slug' => $c->get('wpra/upsell/more_features_page/slug'),
                    'page_title' => $c->get('wpra/upsell/more_features_page/title'),
                    'menu_label' => $c->get('wpra/upsell/more_features_page/menu_label'),
                    'capability' => $c->get('wpra/upsell/more_features_page/capability'),
                    'callback' => $c->get('wpra/upsell/more_features_page/render_fn'),
                ]);
            },
            /*
             * The slug of the "More Features"'s parent page.
             *
             * @since 4.15.1
             */
            'wpra/upsell/more_features_page/parent' => function () {
                return 'edit.php?post_type=wprss_feed';
            },
            /*
             * The slug of the "More Features" page.
             *
             * @since 4.15.1
             */
            'wpra/upsell/more_features_page/slug' => function () {
                return 'wprss_addons';
            },
            /*
             * The title for the "More Features" page.
             *
             * @since 4.15.1
             */
            'wpra/upsell/more_features_page/title' => function () {
                return __('More Features', 'wprss');
            },
            /*
             * The required admin capability for viewing the "More Features" page.
             *
             * @since 4.15.1
             */
            'wpra/upsell/more_features_page/capability' => function () {
                return apply_filters('wprss_capability', 'manage_feed_settings');
            },
            /*
             * The label for the "More Features" menu.
             *
             * @since 4.15.1
             */
            'wpra/upsell/more_features_page/menu_label' => function (ContainerInterface $c) {
                $installed = $c->get('wpra/upsell/installed_addons');

                $label = count($installed) > 0
                    ? $c->get('wpra/upsell/more_features_page/title')
                    : __('Upgrade', 'wprss');

                $icon = $c->get('wpra/upsell/more_features_page/menu_icon');

                return $label . $icon;
            },
            /*
             * The icon for the "More Features" menu.
             *
             * @since 4.15.1
             */
            'wpra/upsell/more_features_page/menu_icon' => function () {
                return '<span class="dashicons dashicons-star-filled wprss-more-features-glyph"></span>';
            },
            /*
             * The position of the "More Features" menu.
             *
             * @since 4.15.1
             */
            'wpra/upsell/more_features_page/menu_pos' => function () {
                return 50;
            },
            /*
             * The function to use for rendering the "More Features" page.
             *
             * @since 4.15.1
             */
            'wpra/upsell/more_features_page/render_fn' => function (ContainerInterface $c) {
                if (!$c->has('wpra/twig/collection')) {
                    return new NullFunction();
                }

                return function () use ($c) {
                    $collection = $c->get('wpra/twig/collection');
                    $template = $collection[$c->get('wpra/upsell/more_features_page/template')];
                    $items = $c->get('wpra/upsell/items');

                    echo $template->render(['items' => $items]);
                };
            },
            /*
             * The path to the template to use when rendering the "More Features" page.
             *
             * @since 4.15.1
             */
            'wpra/upsell/more_features_page/template' => function () {
                return 'admin/upsell/more-features-page/main.twig';
            },
            /*
             * The path to the template to use when rendering the add-on list in the "Share the Love" metabox.
             *
             * @since 4.15.1
             */
            'wpra/upsell/share_the_love/addon_list_template' => function () {
                return 'admin/upsell/add-on-list.twig';
            },
            /*
             * The function for rendering the add-on list in the "Share the Love" metabox.
             *
             * @since 4.15.1
             */
            'wpra/upsell/share_the_love/render_addon_list_fn' => function (ContainerInterface $c) {
                if (!$c->has('wpra/twig/collection')) {
                    return new NullFunction();
                }

                return function () use ($c) {
                    $collection = $c->get('wpra/twig/collection');
                    $template = $collection[$c->get('wpra/upsell/share_the_love/addon_list_template')];
                    $addons = array_filter($c->get('wpra/upsell/items'), function ($item) {
                        return $item['type'] === 'add-on';
                    });

                    echo $template->render(['addons' => $addons]);
                };
            },
        );
    }

    /**
     * @inheritdoc
     *
     * @since 4.15.1
     */
    public function getExtensions()
    {
        return [];
    }
}
