<?php

namespace RebelCode\Wpra\Core\Modules;

use Aventura\Wprss\Core\Component\BulkSourceImport;
use Aventura\Wprss\Core\Model\BulkSourceImport\ServiceProvider;
use Dhii\Di\WritableContainerInterface;
use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Templates\NullTemplate;

/**
 * The module that adds the "Bulk Add" tool to WP RSS Aggregator.
 *
 * @since 4.17
 */
class BulkAddToolModule implements ModuleInterface
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
             * Information about the "Bulk Add" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools/bulk_add/info' => function (ContainerInterface $c) {
                return [
                    'name' => __('Bulk Add Sources', 'wprss'),
                    'template' => $c->has('wpra/twig/collection')
                        ? $c->get('wpra/twig/collection')['admin/tools/bulk_add.twig']
                        : new NullTemplate(),
                ];
            },
            /*
             * The handler that listens to the bulk add request and creates the feed sources.
             *
             * @since 4.17
             */
            'wpra/admin/tools/bulk_add/handler' => function (ContainerInterface $c) {
                return function () {
                    $feeds = filter_input(INPUT_POST, 'wpra_bulk_feeds', FILTER_DEFAULT);
                    if (empty($feeds)) {
                        return;
                    }

                    // Check nonce
                    check_admin_referer('wpra_bulk_add', 'wpra_bulk_nonce');

                    /* @var $importer BulkSourceImport */
                    $importer = wprss_wp_container()->get(WPRSS_SERVICE_ID_PREFIX . 'bulk_source_import');

                    $results = $importer->import($feeds);
                    wprss()->getAdminAjaxNotices()->addNotice('bulk_feed_import');
                };
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
        return [
            /*
             * Registers the "Bulk Add" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools' => function (ContainerInterface $c, $tools) {
                return $tools + ['bulk_add' => $c->get('wpra/admin/tools/bulk_add/info')];
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
        // Register the Bulk Add handler
        add_action('admin_init', $c->get('wpra/admin/tools/bulk_add/handler'));
    }
}
