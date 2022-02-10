<?php

namespace RebelCode\Wpra\Core\Modules;

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Handlers\Logger\ClearLogHandler;
use RebelCode\Wpra\Core\Handlers\Logger\DownloadLogHandler;
use RebelCode\Wpra\Core\Handlers\Logger\SaveLogOptionsHandler;
use RebelCode\Wpra\Core\Templates\NullTemplate;

/**
 * The module that adds the "Logs" tool to WP RSS Aggregator.
 *
 * @since 4.17
 */
class LogsToolModule implements ModuleInterface
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
             * Information about the "Logs" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools/logs/info' => function (ContainerInterface $c) {
                return [
                    'name' => __('Logs', 'wprss'),
                    'template' => $c->has('wpra/twig/collection')
                        ? $c->get('wpra/twig/collection')['admin/tools/logs.twig']
                        : new NullTemplate(),
                ];
            },
            /*
             * The number of logs to show in the "logs" tool page.
             *
             * @since 4.17
             */
            'wpra/admin/tools/logs/num_logs' => function () {
                return 500;
            },
            /*
             * Additional context to add to the "Tools" page.
             *
             * @since 4.17
             */
            'wpra/admin/tools/logs/context' => function (ContainerInterface $c) {
                $numLogs = $c->get('wpra/admin/tools/logs/num_logs');
                $nonce = $c->get('wpra/admin/tools/logs/page/nonce');

                $logs = $c->has('wpra/logging/reader')
                    ? $c->get('wpra/logging/reader')->getLogs($numLogs)
                    : [];
                $config = $c->has('wpra/core/config')
                    ? $c->get('wpra/core/config')
                    : [];

                return [
                    'logs' => [
                        'entries' => $logs,
                        'config' => $config,
                        'nonce' => $nonce,
                        'show_options' => true,
                    ],
                ];
            },
            /*
             * The name of the nonce used in the "Logs" tool page to verify the referer of log-related requests.
             *
             * @since 4.14
             */
            'wpra/admin/tools/logs/page/nonce' => function () {
                return 'wpra_debug_log';
            },
            /*
             * The handler that listens to log clear requests and clears the log.
             *
             * @since 4.17
             */
            'wpra/admin/tools/logs/clear_handler' => function (ContainerInterface $c) {
                return new ClearLogHandler(
                    $c->get('wpra/logging/clearer'),
                    $c->get('wpra/admin/tools/logs/page/nonce')
                );
            },
            /*
             * The handler that listens to log download requests and sends the log file.
             *
             * @since 4.17
             */
            'wpra/admin/tools/logs/download_handler' => function (ContainerInterface $c) {
                return new DownloadLogHandler(
                    $c->get('wpra/logging/reader'),
                    $c->get('wpra/admin/tools/logs/page/nonce')
                );
            },
            /*
             * The handler that listens to log config save requests and saves the logging config.
             *
             * @since 4.17
             */
            'wpra/admin/tools/logs/save_config_handler' => function (ContainerInterface $c) {
                return new SaveLogOptionsHandler(
                    $c->get('wpra/core/config'),
                    $c->get('wpra/admin/tools/logs/page/nonce')
                );
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
             * Registers the "Logs" tool.
             *
             * @since 4.17
             */
            'wpra/admin/tools' => function (ContainerInterface $c, $tools) {
                return $tools + ['logs' => $c->get('wpra/admin/tools/logs/info')];
            },
            /*
             * Adds the logs to the render context on the "Tools" page.
             *
             * @since 4.17
             */
            'wpra/admin/tools/page/context' => function (ContainerInterface $c, $ctx) {
                return $ctx + $c->get('wpra/admin/tools/logs/context');
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
        // Register the clear log handler
        add_action('admin_init', $c->get('wpra/admin/tools/logs/clear_handler'));
        // Register the download log handler
        add_action('admin_init', $c->get('wpra/admin/tools/logs/download_handler'));
        // Register the save log options handler
        add_action('admin_init', $c->get('wpra/admin/tools/logs/save_config_handler'));
    }
}
