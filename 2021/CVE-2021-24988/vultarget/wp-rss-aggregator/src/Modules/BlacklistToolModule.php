<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Data\Collections\NullCollection;
use RebelCode\Wpra\Core\Handlers\EchoHandler;
use RebelCode\Wpra\Core\Templates\NullTemplate;
use RebelCode\Wpra\Core\Ui\BlacklistTable;

/**
 * The module that adds the "Blacklist" tool to WP RSS Aggregator.
 *
 * @since 4.17
 */
class BlacklistToolModule implements ModuleInterface
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
             * Information about the "Blacklist" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools/blacklist/info' => function (ContainerInterface $c) {
                return [
                    'name' => __('Blacklist', 'wprss'),
                    'template' => $c->has('wpra/twig/collection')
                        ? $c->get('wpra/twig/collection')['admin/tools/blacklist.twig']
                        : new NullTemplate(),
                ];
            },
            /*
             * The context to add to the "Tools" page.
             *
             * @since 4.17
             */
            'wpra/admin/tools/blacklist/page/context' => function (ContainerInterface $c) {
                ob_start();
                $listTable = $c->get('wpra/admin/tools/blacklist/page/list_table');
                $listTable->prepare_items();
                $listTable->display();
                $content = ob_get_clean();

                return [
                    'list_table' => $content,
                ];
            },
            /*
             * The list table to show on the "Blacklist" tool page.
             *
             * @since 4.17
             */
            'wpra/admin/tools/blacklist/page/list_table' => function (ContainerInterface $c) {
                return new BlacklistTable($c->get('wpra/admin/tools/blacklist/collection'));
            },
            /*
             * The notice to show when a URL is being added to the blacklist with an empty URL.
             *
             * @since 4.17
             */
            'wpra/admin/tools/blacklist/empty_url_notice' => function () {
                return sprintf(
                    '<div class="notice notice-error is-dismissable"><p>%s</p></div>',
                    __('The blacklist item URL is empty. Please enter the URL to blacklist.', 'wprss')
                );
            },
            /* The notice to show when a URL is being added to the blacklist and it is invalid. */
            'wpra/admin/tools/blacklist/invalid_url_notice' => function () {
                return sprintf(
                    '<div class="notice notice-error is-dismissable"><p>%s</p></div>',
                    __('The blacklisted item does not have a valid URL.', 'wprss')
                );
            },
            /*
             * The notice to show when a URL has been added to the blacklist.
             *
             * @since 4.17
             */
            'wpra/admin/tools/blacklist/added_notice' => function () {
                return sprintf(
                    '<div class="notice notice-success is-dismissable"><p>%s</p></div>',
                    __('Added to blacklist.', 'wprss')
                );
            },
            /*
             * The handler that listens to requests for adding URLs to the blacklist.
             *
             * @since 4.17
             */
            'wpra/admin/tools/blacklist/add_handler' => function (ContainerInterface $c) {
                return function () use ($c) {
                    $action = filter_input(INPUT_POST, 'wpra_add_blacklist', FILTER_DEFAULT);
                    if (empty($action)) {
                        return;
                    }

                    check_admin_referer('wpra_add_blacklist', 'wpra_add_blacklist_nonce');

                    $title = filter_input(INPUT_POST, 'wpra_blacklist_title', FILTER_DEFAULT);
                    $url = filter_input(INPUT_POST, 'wpra_blacklist_url', FILTER_DEFAULT);

                    // URL cannot be empty
                    if (empty($url)) {
                        $notice = $c->get('wpra/admin/tools/blacklist/empty_url_notice');
                        add_action('admin_notices', new EchoHandler($notice));

                        return;
                    }

                    $url = sanitize_text_field($url);

                    // URL cannot be empty
                    if (empty($url)) {
                        $notice = $c->get('wpra/admin/tools/blacklist/invalid_url_notice');
                        add_action('admin_notices', new EchoHandler($notice));

                        return;
                    }

                    // Empty titles default to the URL
                    if (empty($title)) {
                        $title = $url;
                    }

                    $collection = $c->get('wpra/admin/tools/blacklist/collection');
                    $collection[] = [
                        'title' => $title,
                        'url' => $url,
                    ];

                    $notice = $c->get('wpra/admin/tools/blacklist/added_notice');
                    add_action('admin_notices', new EchoHandler($notice));
                };
            },
            /*
             * The handler that listens to blacklist deletion requests.
             *
             * @since 4.17
             */
            'wpra/admin/tools/blacklist/delete_handler' => function (ContainerInterface $c) {
                return function () use ($c) {
                    $id = filter_input(INPUT_POST, 'wpra_delete_blacklist', FILTER_DEFAULT);
                    if (empty($id)) {
                        return;
                    }

                    check_admin_referer('wpra_delete_blacklist', 'wpra_delete_blacklist_nonce');

                    $collection = $c->get('wpra/admin/tools/blacklist/collection');
                    if (isset($collection[$id])) {
                        unset($collection[$id]);
                    }
                };
            },
            /*
             * The handlerthat listens to bulk blacklist deletion requests.
             *
             * @since 4.17
             */
            'wpra/admin/tools/blacklist/bulk_delete_handler' => function (ContainerInterface $c) {
                return function () use ($c) {
                    $bulkAction = filter_input(INPUT_POST, 'action', FILTER_DEFAULT);
                    $bulkAction2 = filter_input(INPUT_POST, 'action2', FILTER_DEFAULT);

                    if ($bulkAction !== 'wpra_bulk_delete_blacklist' && $bulkAction2 !== 'wpra_bulk_delete_blacklist') {
                        return;
                    }

                    $ids = filter_input(INPUT_POST, 'bulk-delete', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
                    if (!is_array($ids) || empty($ids)) {
                        return;
                    }

                    check_admin_referer('bulk-blacklist');

                    $collection = $c->get('wpra/admin/tools/blacklist/collection');
                    $collection->filter(['id' => $ids])->clear();
                };
            },
            /*
             * Alias for the blacklist entity collection, if it exists.
             *
             * @since 4.17
             */
            'wpra/admin/tools/blacklist/collection' => function (ContainerInterface $c) {
                return $c->has('wpra/feeds/blacklist/collection')
                    ? $c->get('wpra/feeds/blacklist/collection')
                    : new NullCollection();
            }
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function getExtensions()
    {
        return [
            /*
             * Registers the "Blacklist" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools' => function (ContainerInterface $c, $tools) {
                return $tools + ['blacklist' => $c->get('wpra/admin/tools/blacklist/info')];
            },
            /*
             * Adds the context for the "Blacklist" tool on the "Tools" page.
             *
             * @since 4.17
             */
            'wpra/admin/tools/page/context' => function (ContainerInterface $c, $ctx) {
                return $ctx + ['blacklist' => $c->get('wpra/admin/tools/blacklist/page/context')];
            },
        ];
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function run(ContainerInterface $c)
    {
        add_action('admin_init', $c->get('wpra/admin/tools/blacklist/add_handler'));
        add_action('admin_init', $c->get('wpra/admin/tools/blacklist/delete_handler'));
        add_action('admin_init', $c->get('wpra/admin/tools/blacklist/bulk_delete_handler'));
    }
}
