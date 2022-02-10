<?php

namespace RebelCode\Wpra\Core\Modules;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;
use RebelCode\Wpra\Core\Database\NullTable;
use RebelCode\Wpra\Core\Database\WpdbTable;
use RebelCode\Wpra\Core\Handlers\Logger\ClearLogHandler;
use RebelCode\Wpra\Core\Handlers\Logger\DownloadLogHandler;
use RebelCode\Wpra\Core\Handlers\Logger\RenderLogHandler;
use RebelCode\Wpra\Core\Handlers\Logger\SaveLogOptionsHandler;
use RebelCode\Wpra\Core\Handlers\Logger\TruncateLogsCronHandler;
use RebelCode\Wpra\Core\Handlers\ScheduleCronJobHandler;
use RebelCode\Wpra\Core\Logger\ProblemLogger;
use RebelCode\Wpra\Core\Logger\WpdbLogger;
use RebelCode\Wpra\Core\Logger\WpraLogger;

/**
 * A module that adds a logger to WP RSS Aggregator.
 *
 * @since 4.13
 */
class LoggerModule implements ModuleInterface
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
             * Whether or not logging is enabled.
             *
             * @since 4.14
             */
            'wpra/logging/enabled' => function (ContainerInterface $c) {
                return $c->get('wpra/core/config')['logging/enabled'];
            },
            /*
             * The main logger instance.
             *
             * @since 4.13
             */
            'wpra/logging/logger' => function (ContainerInterface $c) {
                return $c->get('wpra/logging/enabled')
                    ? $c->get('wpra/logging/main_logger')
                    : new NullLogger();
            },
            /*
             * The WPDB logger instance.
             *
             * @since 4.15.1
             */
            'wpra/logging/main_logger' => function (ContainerInterface $c) {
                try {
                    return new WpraLogger(
                        $c->get('wpra/logging/log_table'),
                        $c->get('wpra/logging/log_table_columns'),
                        $c->get('wpra/logging/log_table_extra')
                    );
                }  catch (Exception $exception) {
                    return new ProblemLogger($exception->getMessage());
                }
            },
            /*
             * The log reader instance.
             *
             * @since 4.14
             */
            'wpra/logging/reader' => function (ContainerInterface $c) {
                return $c->get('wpra/logging/main_logger');
            },
            /*
             * The log clearer instance.
             *
             * @since 4.14
             */
            'wpra/logging/clearer' => function (ContainerInterface $c) {
                return $c->get('wpra/logging/main_logger');
            },
            /*
             * The table where logs are stored.
             *
             * Resolves to a null table if WordPress' database adapter is not available.
             *
             * @since 4.13
             */
            'wpra/logging/log_table' => function (ContainerInterface $c) {
                if (!$c->has('wp/db')) {
                    return new NullTable();
                }

                return new WpdbTable(
                    $c->get('wp/db'),
                    $c->get('wpra/logging/log_table_name'),
                    $c->get('wpra/logging/log_table_schema'),
                    $c->get('wpra/logging/log_table_primary_key')
                );
            },
            /*
             * The name of the table where logs are stored.
             *
             * @since 4.13
             */
            'wpra/logging/log_table_name' => function (ContainerInterface $c) {
                return 'wprss_logs';
            },
            /*
             * The table columns to use for the log table.
             *
             * @since 4.13
             */
            'wpra/logging/log_table_schema' => function () {
                return [
                    'id' => 'BIGINT NOT NULL AUTO_INCREMENT',
                    'date' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                    'level' => 'varchar(30) NOT NULL',
                    'message' => 'text NOT NULL',
                    'feed_id' => 'varchar(100)',
                ];
            },
            /*
             * The mapping of log properties to columns.
             *
             * @since 4.13
             */
            'wpra/logging/log_table_columns' => function () {
                return [
                    WpdbLogger::LOG_ID => 'id',
                    WpdbLogger::LOG_DATE => 'date',
                    WpdbLogger::LOG_LEVEL => 'level',
                    WpdbLogger::LOG_MESSAGE => 'message',
                    'feed_id' => 'feed_id',
                ];
            },
            /*
             * The log table's primary key.
             *
             * @since 4.13
             */
            'wpra/logging/log_table_primary_key' => function () {
                return 'id';
            },
            /*
             * Additional data to include per-log in the log table.
             *
             * @since 4.13
             */
            'wpra/logging/log_table_extra' => function () {
                return [];
            },
            /*
             * The scheduler for the log truncation cron job.
             *
             * @since 4.13
             */
            'wpra/logging/trunc_logs_cron/scheduler' => function (ContainerInterface $c) {
                return new ScheduleCronJobHandler(
                    $c->get('wpra/logging/trunc_logs_cron/event'),
                    $c->get('wpra/logging/trunc_logs_cron/handler'),
                    $c->get('wpra/logging/trunc_logs_cron/first_run'),
                    $c->get('wpra/logging/trunc_logs_cron/frequency'),
                    $c->get('wpra/logging/trunc_logs_cron/args')
                );
            },
            /*
             * The event for the log truncation cron job.
             *
             * @since 4.13'
             */
            'wpra/logging/trunc_logs_cron/event' => function (ContainerInterface $c) {
                return 'wprss_truncate_logs';
            },
            /*
             * How frequently the log truncation cron job runs.
             *
             * @since 4.13'
             */
            'wpra/logging/trunc_logs_cron/frequency' => function (ContainerInterface $c) {
                return 'daily';
            },
            /*
             * When to first run the log truncation cron job after it's been scheduled.
             *
             * @since 4.13'
             */
            'wpra/logging/trunc_logs_cron/first_run' => function (ContainerInterface $c) {
                return time() + DAY_IN_SECONDS;
            },
            /*
             * The handler for the log truncation cron job.
             *
             * @since 4.13'
             */
            'wpra/logging/trunc_logs_cron/handler' => function (ContainerInterface $c) {
                return new TruncateLogsCronHandler(
                    $c->get('wpra/logging/log_table'),
                    $c->get('wpra/core/config')
                );
            },
            /*
             * The arguments to pass to the log truncation cron job handler.
             *
             * @since 4.13'
             */
            'wpra/logging/trunc_logs_cron/args' => function (ContainerInterface $c) {
                return [];
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
             * Adds the logging options to the plugin config.
             *
             * @since 4.14
             */
            'wpra/core/config/options' => function (ContainerInterface $c, $options) {
                $options['logging/enabled'] = true;
                $options['logging/limit_days'] = 3;

                return $options;
            },
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function run(ContainerInterface $c)
    {
        // Hook in the scheduler for the truncate logs cron job
        add_action('init', $c->get('wpra/logging/trunc_logs_cron/scheduler'));
    }
}
