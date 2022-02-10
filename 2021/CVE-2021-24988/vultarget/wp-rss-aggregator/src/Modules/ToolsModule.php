<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Handlers\RegisterSubMenuPageHandler;
use RebelCode\Wpra\Core\Templates\NullTemplate;

/**
 * The module that adds the "Tools" page to WP RSS Aggregator.
 *
 * @since 4.17
 */
class ToolsModule implements ModuleInterface
{
    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function getFactories()
    {
        return [
            /*
             * The list of available tools.
             *
             * @since 4.17
             */
            'wpra/admin/tools' => function () {
                return [];
            },
            /*
             * Information about the "Tools" menu.
             *
             * @since 4.17
             */
            'wpra/admin/tools/menu' => function () {
                return [
                    'slug' => 'wpra_tools',
                    'parent' => 'edit.php?post_type=wprss_feed',
                    'label' => __('Tools', 'wprss'),
                    'position' => 4,
                ];
            },
            /*
             * Information about the "Tools" page.
             *
             * @since 4.17
             */
            'wpra/admin/tools/page' => function () {
                return [
                    'title' => __('Tools', 'wprss'),
                    'capability' => 'manage_options',
                ];
            },
            /*
             * The template for the "Tools" page.
             *
             * @since 4.17
             */
            'wpra/admin/tools/page/template' => function (ContainerInterface $c) {
                if (!$c->has('wpra/twig/collection')) {
                    return new NullTemplate();
                }

                return $c->get('wpra/twig/collection')['admin/tools/main.twig'];
            },
            /*
             * The template context for the "Tools" page.
             *
             * @since 4.17
             */
            'wpra/admin/tools/page/context' => function (ContainerInterface $c) {
                return [];
            },
            /*
             * The render function for the "Tools" page.
             *
             * @since 4.17
             */
            'wpra/admin/tools/page/render_fn' => function (ContainerInterface $c) {
                return function () use ($c) {
                    $tools = $c->get('wpra/admin/tools');
                    $context = $c->get('wpra/admin/tools/page/context');
                    $template = $c->get('wpra/admin/tools/page/template');

                    // Add the tools the context, rendering their templates
                    foreach ($tools as $key => $tool) {
                        $context['tools'][$key] = [
                            'name' => $tool['name'],
                            'html' => $tool['template']->render($context),
                        ];
                    }

                    echo $template->render($context);
                };
            },
            /*
             * The handler that registers the "Tools" menu.
             *
             * @since 4.17
             */
            'wpra/admin/tools/menu/register_handler' => function (ContainerInterface $c) {
                $menu = $c->get('wpra/admin/tools/menu');
                $page = $c->get('wpra/admin/tools/page');
                $renderFn = $c->get('wpra/admin/tools/page/render_fn');

                return new RegisterSubMenuPageHandler([
                    'parent' => $menu['parent'],
                    'slug' => $menu['slug'],
                    'page_title' => $page['title'],
                    'menu_label' => $menu['label'],
                    'capability' => $page['capability'],
                    'callback' => $renderFn,
                    'position' => $menu['position'],
                ]);
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function getExtensions()
    {
        return [];
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function run(ContainerInterface $c)
    {
        add_action('admin_menu', $c->get('wpra/admin/tools/menu/register_handler'));
    }
}
