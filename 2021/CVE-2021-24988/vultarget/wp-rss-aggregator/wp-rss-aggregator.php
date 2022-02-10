<?php /** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */

/**
 * Plugin Name: WP RSS Aggregator
 * Plugin URI: https://www.wprssaggregator.com/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wpraplugin
 * Description: Imports and aggregates multiple RSS Feeds.
 * Version: 4.19.2
 * Author: RebelCode
 * Author URI: https://www.wprssaggregator.com
 * Text Domain: wprss
 * Domain Path: /languages/
 * License: GPLv3
 */

/**
 * @copyright   Copyright (c) 2012-2021, RebelCode Ltd.
 * @link        https://www.wprssaggregator.com/
 * @license     http://www.gnu.org/licenses/gpl.html
 *
 * Copyright (C) 2012-2019 RebelCode Ltd.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Psr\Container\ContainerInterface;
use RebelCode\Wpra\Core\Container\ModuleContainer;
use RebelCode\Wpra\Core\Container\WpFilterContainer;
use RebelCode\Wpra\Core\ErrorHandler;
use RebelCode\Wpra\Core\Modules\AddonsModule;
use RebelCode\Wpra\Core\Modules\AssetsModule;
use RebelCode\Wpra\Core\Modules\BlacklistToolModule;
use RebelCode\Wpra\Core\Modules\BulkAddToolModule;
use RebelCode\Wpra\Core\Modules\CoreModule;
use RebelCode\Wpra\Core\Modules\CustomFeedModule;
use RebelCode\Wpra\Core\Modules\FeedBlacklistModule;
use RebelCode\Wpra\Core\Modules\FeedDisplayModule;
use RebelCode\Wpra\Core\Modules\FeedItemsModule;
use RebelCode\Wpra\Core\Modules\FeedShortcodeModule;
use RebelCode\Wpra\Core\Modules\FeedSourcesModule;
use RebelCode\Wpra\Core\Modules\FeedTemplatesModule;
use RebelCode\Wpra\Core\Modules\GutenbergBlockModule;
use RebelCode\Wpra\Core\Modules\I18nModule;
use RebelCode\Wpra\Core\Modules\ImagesModule;
use RebelCode\Wpra\Core\Modules\ImporterModule;
use RebelCode\Wpra\Core\Modules\ImportExportToolsModule;
use RebelCode\Wpra\Core\Modules\LicensingModule;
use RebelCode\Wpra\Core\Modules\LoggerModule;
use RebelCode\Wpra\Core\Modules\LogsToolModule;
use RebelCode\Wpra\Core\Modules\ModuleInterface;
use RebelCode\Wpra\Core\Modules\ParsedownModule;
use RebelCode\Wpra\Core\Modules\PolyLangCompatModule;
use RebelCode\Wpra\Core\Modules\ResetToolModule;
use RebelCode\Wpra\Core\Modules\RestApiModule;
use RebelCode\Wpra\Core\Modules\SettingsModule;
use RebelCode\Wpra\Core\Modules\SysInfoToolModule;
use RebelCode\Wpra\Core\Modules\ToolsModule;
use RebelCode\Wpra\Core\Modules\TwigModule;
use RebelCode\Wpra\Core\Modules\UpsellModule;
use RebelCode\Wpra\Core\Modules\WpModule;
use RebelCode\Wpra\Core\Plugin;

/**
 * Define constants used by the plugin.
 */

// Set the version number of the plugin.
if( !defined( 'WPRSS_VERSION' ) )
    define( 'WPRSS_VERSION', '4.19.2' );

if( !defined( 'WPRSS_WP_MIN_VERSION' ) )
    define( 'WPRSS_WP_MIN_VERSION', '4.8' );

if( !defined( 'WPRSS_MIN_PHP_VERSION' ) )
    define( 'WPRSS_MIN_PHP_VERSION', '5.4' );

// Set the database version number of the plugin.
if( !defined( 'WPRSS_DB_VERSION' ) )
    define( 'WPRSS_DB_VERSION', 16 );

// Set the plugin prefix
if( !defined( 'WPRSS_PREFIX' ) )
    define( 'WPRSS_PREFIX', 'wprss' );

// Set the plugin prefix
if( !defined( 'WPRSS_FILE_CONSTANT' ) )
    define( 'WPRSS_FILE_CONSTANT', __FILE__ );

// Set constant path to the plugin directory.
if( !defined( 'WPRSS_DIR' ) )
    define( 'WPRSS_DIR', __DIR__ . '/' );

// Set constant URI to the plugin URL.
if( !defined( 'WPRSS_URI' ) )
    define( 'WPRSS_URI', plugin_dir_url( __FILE__ ) );

// Set the constant path to the plugin's javascript directory.
if( !defined( 'WPRSS_JS' ) )
    define( 'WPRSS_JS', WPRSS_URI . 'js/' );

// Set the constant path to the plugin's CSS directory.
if( !defined( 'WPRSS_CSS' ) )
    define( 'WPRSS_CSS', WPRSS_URI . 'css/' );

// Set the constant path to the plugin's javascript build directory.
if( !defined( 'WPRSS_APP_JS' ) )
    define( 'WPRSS_APP_JS', WPRSS_URI . 'js/build/' );

// Set the constant path to the plugin's CSS build directory.
if( !defined( 'WPRSS_APP_CSS' ) )
    define( 'WPRSS_APP_CSS', WPRSS_URI . 'css/build/' );

// Set the constant path to the plugin's images directory.
if( !defined( 'WPRSS_IMG' ) )
    define( 'WPRSS_IMG', WPRSS_URI . 'images/' );

// Set the constant path to the plugin's includes directory.
if( !defined( 'WPRSS_INC' ) )
    define( 'WPRSS_INC', __DIR__ . '/includes/' );

if( !defined( 'WPRSS_LANG' ) )
    define( 'WPRSS_LANG', __DIR__ . '/languages/' );

// Set the constant path to the plugin's log file.
if( !defined( 'WPRSS_LOG_FILE' ) )
    define( 'WPRSS_LOG_FILE', WP_CONTENT_DIR . '/log/wprss/log' );

if( !defined( 'WPRSS_LOG_FILE_EXT' ) )
    define( 'WPRSS_LOG_FILE_EXT', '.txt' );

if ( !defined('WPRSS_SL_STORE_URL') ) {
    define( 'WPRSS_SL_STORE_URL', 'https://www.wprssaggregator.com/edd-sl-api/' );
}

if ( !defined( 'WPRSS_TEXT_DOMAIN' ) ) {
    define( 'WPRSS_TEXT_DOMAIN', 'wprss' );
}

// Maximum time for the feed source to be fetched
if ( !defined( 'WPRSS_FEED_FETCH_TIME_LIMIT' ) ) {
    define( 'WPRSS_FEED_FETCH_TIME_LIMIT', 120 );
}
// Maximum time for a single feed item to import
if ( !defined( 'WPRSS_ITEM_IMPORT_TIME_LIMIT' ) ) {
    define( 'WPRSS_ITEM_IMPORT_TIME_LIMIT', 15 );
}
// Where to take the diagnostic tests from
if ( !defined( 'WPRACORE_DIAG_TESTS_DIR' ) ) {
    define( 'WPRACORE_DIAG_TESTS_DIR', WPRSS_DIR . 'test/diag' );
}

const WPRSS_CORE_PLUGIN_NAME = 'WP RSS Aggregator';

/**
 * Code of the Core plugin.
 *
 * @since 4.11
 */
const WPRSS_PLUGIN_CODE = 'wprss';

/**
 * Prefix for events used by this plugin.
 *
 * @since 4.11
 */
define('WPRSS_EVENT_PREFIX', WPRSS_PLUGIN_CODE . '_');

/**
 * Whether this plugin is in debug mode.
 *
 * @since 4.11
 */
const WPRSS_DEBUG = WP_DEBUG;

/**
 * Load required files.
 */

/* Autoloader for this plugin */
require_once ( WPRSS_INC . 'autoload.php' );
// Adding autoload paths
wprss_autoloader()->add('Aventura\\Wprss\\Core', WPRSS_INC);
wprss_autoloader()->add('Aventura\\Wprss\\Core\\DiagTest', WPRACORE_DIAG_TESTS_DIR);

/* Only function definitions, no effect! */
require_once(WPRSS_INC . 'functions.php');

/* Deprecated functions */
require_once(WPRSS_INC . 'deprecated.php');

/* SimplePie */
require_once ( ABSPATH . WPINC . '/class-simplepie.php' );

/* Twig */
require_once ( WPRSS_INC . '/twig.php' );

/* Dependency injection */
require_once ( WPRSS_INC . 'di.php' );

/* Load install, upgrade and migration code. */
require_once ( WPRSS_INC . 'update.php' );

/* Load the file for setting capabilities of our post types */
require_once ( WPRSS_INC . 'roles-capabilities.php' );

/* Load the feed processing functions file */
require_once ( WPRSS_INC . 'feed-processing.php' );

/* Load the blacklist functions file */
require_once ( WPRSS_INC . 'feed-blacklist.php' );

/* Load the feed importing functions file */
require_once ( WPRSS_INC . 'feed-importing.php' );

/* Load the feed image importing functions file */
require_once ( WPRSS_INC . 'feed-importing-images.php' );

/* Load the site-specific importing functions file */
require_once ( WPRSS_INC . 'feed-importing-sites.php' );

/* Load the feed states functions file */
require_once ( WPRSS_INC . 'feed-states.php' );

/* Load the feed display functions file */
require_once ( WPRSS_INC . 'legacy-feed-display.php' );

/* Load the custom post type feeds file */
require_once ( WPRSS_INC . 'cpt-feeds.php' );

/* Load the cron job scheduling functions. */
require_once ( WPRSS_INC . 'cron-jobs.php' );

/* Load the admin functions file. */
require_once ( WPRSS_INC . 'admin.php' );

/* Load the admin options functions file. */
require_once ( WPRSS_INC . 'admin-options.php' );

/* Load the legacy admin options functions file. */
require_once ( WPRSS_INC . 'admin-options-legacy.php' );

/* Load the system info file */
require_once ( WPRSS_INC . 'system-info.php' );

/* Load the miscellaneous functions file */
require_once ( WPRSS_INC . 'misc-functions.php' );

/* Load the OPML Class file */
require_once ( WPRSS_INC . 'OPML.php' );

/* Load the OPML Importer file */
require_once ( WPRSS_INC . 'opml-importer.php' );

/* Load the admin display-related functions */
require_once ( WPRSS_INC . 'admin-display.php' );

/* Load the admin metaboxes functions */
require_once ( WPRSS_INC . 'admin-metaboxes.php' );

/* Load the scripts loading functions file */
require_once ( WPRSS_INC . 'scripts.php' );

/* Load the Ajax notification file */
require_once ( WPRSS_INC . 'admin-ajax-notice.php' );

/* Load the logging class */
require_once ( WPRSS_INC . 'roles-capabilities.php' );

/* Load the licensing file */
require_once ( WPRSS_INC . 'licensing.php' );

/* Load the admin editor file */
require_once ( WPRSS_INC . 'admin-editor.php' );

/* Load the admin heartbeat functions */
require_once ( WPRSS_INC . 'admin-heartbeat.php' );

// Load the logging functions file
require_once ( WPRSS_INC . 'admin-log.php' );

if ( !defined( 'WPRSS_LOG_LEVEL' ) )
    define( 'WPRSS_LOG_LEVEL', WPRSS_LOG_LEVEL_ERROR );

/* Load the admin help file */
require_once ( WPRSS_INC . 'admin-help.php' );

/* Load the admin metaboxes help file */
require_once ( WPRSS_INC . 'admin-help-metaboxes.php' );

/* Load the admin settings help file */
require_once ( WPRSS_INC . 'admin-help-settings.php' );

/* Admin plugin activation events */
require_once ( WPRSS_INC . 'admin-activate.php' );

/* Add components to the plugins page  */
require_once(WPRSS_INC . 'admin-plugins.php');

/* Access to feed */
require_once ( WPRSS_INC . 'feed-access.php' );

/* Load the fallbacks for mbstring */
require_once ( WPRSS_INC . 'fallback-mbstring.php' );

/* Load the polyfill functions file */
require_once ( WPRSS_INC . 'polyfills.php' );

/* Load the youtube functionality */
require_once ( WPRSS_INC . 'youtube.php' );

/* Load the multi-media file */
require_once ( WPRSS_INC . 'multimedia.php' );

/* Load the Templates v0.2.1 update path */
require_once ( WPRSS_INC . 'templates-update.php' );

register_activation_hook(__FILE__, 'wprss_activate');
register_deactivation_hook(__FILE__, 'wprss_deactivate');

// Safe deactivation hook (for the error handler)
add_action('plugins_loaded', 'wpra_safe_deactivate', 50);
// Run WPRA
add_action('plugins_loaded', 'wpra_run', 100);
// Initializes licensing
add_action('plugins_loaded', 'wprss_licensing', 150);

/**
 * Runs WP RSS Aggregator.
 *
 * @since 4.13
 */
function wpra_run()
{
    try {
        $errorHandler = new ErrorHandler(__DIR__, 'wpra_critical_error_handler');
        $errorHandler->register();

        $plugin = wpra();
        $container = wpra_container();
        do_action('wpra_loaded', $container, $plugin);

        $plugin->run($container);
        do_action('wpra_after_run', $container, $plugin);
    } catch (Throwable $throwable) {
        wpra_error_handler($throwable);
    } catch (Exception $exception) {
        wpra_error_handler($exception);
    }
}

/**
 * Retrieves the WP RSS Aggregator instance.
 *
 * @since 4.13
 *
 * @return Plugin
 */
function wpra()
{
    static $instance = null;

    if ($instance === null) {
        $modules = wpra_modules();
        $plugin = new Plugin($modules);
        $instance = apply_filters('wpra_plugin_instance', $plugin);
    }

    return $instance;
}

/**
 * Retrieves the WP RSS Aggregator plugin modules.
 *
 * @since 4.13
 *
 * @return ModuleInterface[] The modules.
 */
function wpra_modules()
{
    return apply_filters('wpra_plugin_modules', [
        'core' => new CoreModule(__FILE__),
        'addons' => new AddonsModule(),
        'wordpress' => new WpModule(),
        'assets' => new AssetsModule(),
        'importer' => new ImporterModule(),
        'feed_sources' => new FeedSourcesModule(),
        'feed_items' => new FeedItemsModule(),
        'feed_blacklist' => new FeedBlacklistModule(),
        'feed_display' => new FeedDisplayModule(),
        'feed_shortcode' => new FeedShortcodeModule(),
        'feed_templates' => new FeedTemplatesModule(),
        'gutenberg_block' => new GutenbergBlockModule(),
        'images' => new ImagesModule(),
        'custom_feed' => new CustomFeedModule(),
        'rest_api' => new RestApiModule(),
        'tools' => new ToolsModule(),
        'tools/bulk_add' => new BulkAddToolModule(),
        'tools/blacklist' => new BlackListToolModule(),
        'tools/import_export' => new ImportExportToolsModule(),
        // 'tools/crons' => new CronsToolModule(),
        'tools/logs' => new LogsToolModule(),
        'tools/sys_info' => new SysInfoToolModule(),
        'tools/reset' => new ResetToolModule(),
        'settings' => new SettingsModule(),
        'licensing' => new LicensingModule(),
        'upsell' => new UpsellModule(),
        'logging' => new LoggerModule(),
        'i18n' => new I18nModule(),
        'twig' => new TwigModule(),
        'parsedown' => new ParsedownModule(),
        'polylang_compat' => new PolyLangCompatModule(),
    ]);
}

/**
 * Retrieves the WP RSS Aggregator DI container.
 *
 * @since 4.13
 *
 * @return ContainerInterface The container instance.
 */
function wpra_container()
{
    static $instance = null;

    if ($instance === null) {
        $plugin = wpra();
        // Create the container for the plugin module
        $moduleCntr = new ModuleContainer($plugin);
        // Create a WP filter container that wraps around it
        $filterCntr = new WpFilterContainer($moduleCntr);
        // Set the module container to pass the WP filter container to service definitions
        $moduleCntr->useProxy($filterCntr);
        // Use the WP filter container as the main container
        $instance = apply_filters('wpra_plugin_container', $filterCntr);
    }

    return $instance;
}

/**
 * Retrieves a WP RSS Aggregator service from the container.
 *
 * @since 4.13
 *
 * @param string $key The service key, without the 'wpra/' prefix.
 *
 * @return mixed The service instance or value.
 */
function wpra_get($key)
{
    return wpra_container()->get('wpra/' . $key);
}

/**
 * Loads a WP RSS Aggregator module.
 *
 * @since 4.13
 *
 * @param string          $key    The module key.
 * @param ModuleInterface $module The module instance.
 */
function wpra_load_module($key, $module)
{
    if (!($module instanceof ModuleInterface)) {
        throw new RuntimeException(__('Attempted to load an invalid WP RSS Aggregator module', 'wprss'));
    }

    add_filter('wpra_plugin_modules', function ($modules) use ($key, $module) {
        $modules[$key] = $module;

        return $modules;
    });
}

/**
 * Handles catchable errors, caught from {@link wpra_run()}.
 *
 * @since 4.15
 *
 * @param Exception|Throwable $error The caught exception or throwable instance.
 */
function wpra_error_handler($error)
{
    add_action('all_admin_notices', function () use ($error) {
        $message = __(
            '<b>WP RSS Aggregator</b> has encountered an error. If this problem persists, kindly contact customer support and provide the following details:',
            'wprss'
        );

        printf(
            '<div class="notice notice-error">%s</div>',
            wpra_display_error($message, $error)
        );
    });

    do_action('wpra_error', $error);
}

/**
 * Handles critical errors.
 *
 * This function is used as a callback in the {@link ErrorHandler}.
 *
 * @since 4.15
 *
 * @param Exception|Throwable $error The encountered error or throwable instance.
 */
function wpra_critical_error_handler($error)
{
    $hasAddons = count(wpra_get_addon_paths()) > 0;
    $buttonText = $hasAddons
        ? __('Deactivate WP RSS Aggregator and its addons', 'wprss')
        : __('Deactivate WP RSS Aggregator', 'wprss');

    $buttonHtml = sprintf(
        '<button type="submit" class="button button-secondary">%s</button>',
        esc_html($buttonText)
    );

    $formNonceHtml = wp_nonce_field('wprss_safe_deactivate', 'wprss_safe_deactivate_nonce', true, false);

    $formHtml = sprintf(
        '<br/><form method="POST" action="%s">%s %s</form>',
        esc_attr(admin_url()),
        $formNonceHtml,
        $buttonHtml
    );

    $message = __(
        '<b>WP RSS Aggregator</b> has encountered a critical error. The safest course of action is to deactivate the plugin and any of its add-ons on this site using the button below. Once you’ve done that, you may reactivate them and start using the plugins again. If the problem persists, please copy the below error and send it to our support team with an explanation of when and how it happened.',
        'wprss'
    );
    $errorDetailsHtml = wpra_display_error($message, $error);

    do_action('wpra_critical_error', $error);

    wp_die(
        $errorDetailsHtml . $formHtml,
        __('WP RSS Aggregator Error', 'wprss')
    );
}

/**
 * Generates common display for WP RSS Aggregator errors.
 *
 * @since 4.15
 *
 * @param string $message The message to show.
 * @param Exception|Throwable $error The error.
 *
 * @return string
 */
function wpra_display_error($message, $error)
{
    $exceptionMsg = sprintf(
        '<pre>%s (%s)</pre>',
        esc_html($error->getMessage()),
        esc_html(wprss_error_path($error))
    );

    $exceptionChain = '';
    $prev = $error;
    while ($prev = $prev->getPrevious()) {
        $exceptionChain .= sprintf(
            '<strong>%s</strong>
             <br/>
             <pre>%s (%s)</pre>',
            __('Caused by:', 'wprss'),
            esc_html($prev->getMessage()),
            esc_html(wprss_error_path($prev))
        );
    }

    $stackTrace = esc_html($error->getTraceAsString());

    ob_start(); ?>

    <p>
        <?= esc_html($message) ?>
    </p>
    <div style="background-color: rgba(0,0,0,.07); padding: 5px;">
        <details>
            <summary style="cursor: pointer;">
                <?= __('Click to show error details', 'wprss') ?>
            </summary>
            <div style="padding-top: 10px; overflow-x: scroll;">
                <strong>
                    <?= __('Error Message:', 'wprss'); ?>
                </strong>

                <br />

                <?= $exceptionMsg ?>
                <?= $exceptionChain ?>

                <strong><?= __('Stack trace:', 'wprss'); ?></strong>
                <br />
                <pre><?= $stackTrace ?></pre>
            </div>
        </details>
    </div>
    <br />
    <?php

    return ob_get_clean();
}

/**
 * @since 4.17.9
 *
 * @param Exception|Error $exception
 *
 * @return string
 */
function wprss_error_path($exception)
{
    $file = $exception->getFile();

    $pos = stripos($file, 'wp-content');

    if ($pos === false) {
        $pos = stripos($file, 'wp-includes');
    }

    if ($pos === false) {
        $pos = stripos($file, 'wp-admin');
    }

    if ($pos !== false) {
        $file = substr($file, $pos);
    }

    return $file . ':' . $exception->getLine();
}

/**
 * Safely deactivates WP RSS Aggregator.
 *
 * This function is intended to be called from error handlers that give users the option to deactivate the plugin.
 *
 * @since 4.15
 */
function wpra_safe_deactivate()
{
    $nonce = filter_input(INPUT_POST, 'wprss_safe_deactivate_nonce', FILTER_DEFAULT);

    if (empty($nonce) || !wp_verify_nonce($nonce, 'wprss_safe_deactivate')) {
        return;
    }

    $plugins = wpra_get_addon_paths();
    $plugins[] = plugin_basename(__FILE__);

    if (!function_exists('deactivate_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    deactivate_plugins($plugins, true);
    header('Location: ' . admin_url('plugins.php'));
    exit;
}

/**
 * Retrieves the list of full paths to the main files of activated addons.
 *
 * @since 4.15
 *
 * @return string[]
 */
function wpra_get_addon_paths()
{
    $check = [
        'WPRSS_TEMPLATES',
        'WPRSS_C_PATH',
        'WPRSS_ET_PATH',
        'WPRSS_KF_PATH',
        'WPRSS_FTP_PATH',
        'WPRSS_FTR_PATH',
        'WPRSS_SPC_ADDON'
    ];

    $addons = [];
    foreach ($check as $pathConstant) {
        if (defined($pathConstant)) {
            $addons[] = plugin_basename(constant($pathConstant));
        }
    }

    return $addons;
}

/**
 * Returns the Core plugin singleton instance.
 *
 * Using DI container since 4.11.
 *
 * @since 4.8.1
 * @return Aventura\Wprss\Core\Plugin
 */
function wprss() {
    return wprss_wp_container()->get(sprintf('%1$splugin', \WPRSS_SERVICE_ID_PREFIX));
}

try {
    do_action('wprss_pre_init');
    $instance = wprss();
} catch (Throwable $t) {
    wpra_error_handler($t);
} catch (Exception $e) {
    wpra_error_handler($e);
}

add_action( 'init', 'wprss_init' );
/**
 * Initialise the plugin
 *
 * @since  1.0
 * @return void
 */
function wprss_init() {
    do_action( 'wprss_init' );
}

/**
 * Informs users that have not updated to 4.13 that 4.13 will stop supporting PHP 5.3, if their PHP version is
 * less than 5.4.
 *
 * @since 4.13
 */
add_action('after_plugin_row', function($plugin_file) {
    if ($plugin_file !== plugin_basename(__FILE__)
        || version_compare(WPRSS_VERSION, '4.13', '>=')
        || version_compare(PHP_VERSION, '5.4', '>=')
    ) {
        return;
    }

    $message = __(
        'As of version 4.13, WP RSS Aggregator will stop supporting PHP 5.3 and will require PHP 5.4 or later. Kindly contact your site\'s hosting provider for PHP version update options.',
        'wprss'
    );
    $notice = sprintf(
        '<div class="update-notice notice inline notice-error notice-alt"><p>%s</p></div>',
        esc_html($message)
    );
    $td = sprintf('<td colspan="3" class="plugin-update colspanchange">%s</td>', $notice);
    printf('<tr class="plugin-update-tr active">%s</tr>', $td);
}, 5, 2);


function wprss_wp_min_version_satisfied() {
    return version_compare( get_bloginfo( 'version' ), WPRSS_WP_MIN_VERSION, '>=' );
}


add_action( 'init', 'wprss_add_wp_version_warning' );
function wprss_add_wp_version_warning() {
    if (wprss_wp_min_version_satisfied()) {
        return;
    }

    wprss_admin_notice_add([
        'id' => 'wp_version_warning',
        'content' => sprintf(
            __(
                '<p><strong>%2$s requires WordPress to be of version %1$s or higher.</strong></br>' .
                'Older versions of WordPress are no longer supported by %2$s. Please upgrade your WordPress core to continue benefiting from %2$s support services.</p>',
                'wprss'
            ),
            esc_html(WPRSS_WP_MIN_VERSION),
            esc_html(WPRSS_CORE_PLUGIN_NAME)
        ),
        'notice_type' => 'error',
    ]);
}


add_action( 'init', 'wprss_add_php_version_warning' );
function wprss_add_php_version_warning() {
    if (version_compare(PHP_VERSION, WPRSS_MIN_PHP_VERSION, '>=')) {
        return;
    }

    if (!function_exists('deactivate_plugins')) {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }

    deactivate_plugins(plugin_basename(__FILE__));

    $firstLine = get_transient('_wprss_activation_redirect')
        ? __('WP RSS Aggregator cannot be activated.', 'wprss')
        : __('WP RSS Aggregator has been deactivated.', 'wprss');

    $supportLink = sprintf(
    '<a href="%2$s" target="_blank">%1$s</a>',
        _x(
            'contact support',
            'Used like "Kindly contact your hosting provider or contact support for more information."',
            'wprss'
        ),
        'https://wordpress.org/support/plugin/wp-rss-aggregator'
    );
    $secondLine = sprintf(
        __("The plugin requires version %s or later and your site's PHP version is %s.", 'wprss'),
        '<strong>' . WPRSS_MIN_PHP_VERSION . '</strong>',
        '<strong>' . PHP_VERSION . '</strong>'
    );
    $thirdLine = sprintf(
        _x(
            'Kindly contact your hosting provider to upgrade your PHP version or %s for more information.',
            'the "%s" part is a link with text = "contact support"',
            'wprss'
        ),
        $supportLink
    );

    wp_die(
        implode('<br/>', array($firstLine, $secondLine, $thirdLine)),
        __('WP RSS Aggregator - PHP version error'),
        array(
            'back_link' => true
        )
    );
}

/**
 * Informs users that have not updated to 4.13 that 4.13 will stop supporting PHP 5.3, if their PHP version is
 * less than 5.4.
 *
 * @since 4.12.1
 */
add_action('after_plugin_row', function($plugin_file) {
    if ($plugin_file !== plugin_basename(__FILE__)
        || version_compare(WPRSS_VERSION, '4.13', '>=')
        || version_compare(PHP_VERSION, '5.4', '>=')
    ) {
        return;
    }

    $message = __(
        'As of version 4.13, WP RSS Aggregator will stop supporting PHP 5.3 and will require PHP 5.4 or later. Kindly contact your site\'s hosting provider for PHP version update options.',
        'wprss'
    );
    $notice = sprintf('<div class="update-notice notice inline notice-error notice-alt"><p>%s</p></div>', $message);
    $td = sprintf('<td colspan="3" class="plugin-update colspanchange">%s</td>', $notice);
    printf('<tr class="plugin-update-tr active">%s</tr>', $td);
}, 5, 2);

/**
 * Plugin activation procedure
 *
 * @since  1.0
 * @return void
 */
function wprss_activate() {
    /* Prevents activation of plugin if compatible version of WordPress not found */
    if ( !wprss_wp_min_version_satisfied() ) {
        deactivate_plugins ( basename( __FILE__ ));     // Deactivate plugin
        wp_die( sprintf ( __( '%2$s requires WordPress version %1$s or higher.' ), WPRSS_WP_MIN_VERSION, WPRSS_CORE_PLUGIN_NAME ), WPRSS_CORE_PLUGIN_NAME, array( 'back_link' => true ) );
    }
    wprss_settings_initialize();
    flush_rewrite_rules();
    wprss_schedule_fetch_all_feeds_cron();

    // Sets a transient to trigger a redirect upon completion of activation procedure
    set_transient( '_wprss_activation_redirect', true, 30 );

    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    // Check if WordPress SEO is activate, if yes set its options for hiding the metaboxes on the wprss_feed and wprss_feed_item screens
    if (is_plugin_active('wordpress-seo/wp-seo.php')) {
        $wpseo_titles = get_option('wpseo_titles', []);
        if (isset($wpseo_titles['hideeditbox-wprss_feed'])) {
            $wpseo_titles['hideeditbox-wprss_feed'] = true;
            $wpseo_titles['hideeditbox-wprss_feed_item'] = true;
        }
        update_option('wpseo_titles', $wpseo_titles);
    }

    {
        // Get existing active feeds that use their own interval
        $activeFeeds = get_posts([
            'post_type' => 'wprss_feed',
            'post_status' => 'publish',
            'cache_results' => false,
            'posts_per_page' => -1,
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'wprss_state',
                    'value' => 'active',
                ],
                [
                    'key' => 'wprss_update_interval',
                    'compare' => '!=',
                    'value' => 'global',
                ],
                [
                    'key' => 'wprss_update_interval',
                    'compare' => '!=',
                    'value' => '',
                ],
            ],
        ]);

        // Schedule their cron jobs
        foreach ($activeFeeds as $feed) {
            wprss_feed_source_update_start_schedule($feed->ID);
        }
    }
}

/**
 * Plugin deactivation procedure
 *
 * @since 1.0
 */
function wprss_deactivate() {
    // On deactivation remove the cron jobs
    wp_clear_scheduled_hook(wpra_container()->get('wpra/logging/trunc_logs_cron/event'));
    wp_clear_scheduled_hook(WPRA_FETCH_ALL_FEEDS_HOOK);
    wp_clear_scheduled_hook(WPRA_TRUNCATE_ITEMS_HOOK);
    wpra_clear_all_scheduled_hooks(WPRA_FETCH_FEED_HOOK);

    // Flush the rewrite rules
    flush_rewrite_rules();
}

/**
 * Utility filter function that returns TRUE;
 *
 * @since 3.8
 */
function wprss_enable() {
    return TRUE;
}


/**
 * Utility filter function that returns FALSE;
 *
 * @since 3.8
 */
function wprss_disable() {
    return FALSE;
}

/**
 * Gets the timezone string that corresponds to the timezone set for
 * this site. If the timezone is a UTC offset, or if it is not set, still
 * returns a valid timezone string.
 * However, if no actual zone exists in the configured offset, the result
 * may be rounded up, or failure.
 *
 * @see http://pl1.php.net/manual/en/function.timezone-name-from-abbr.php
 * @return string A valid timezone string, or false on failure.
 */
function wprss_get_timezone_string() {
    $tzstring = get_option( 'timezone_string' );

    if ( empty($tzstring) ) {
        $offset = ( int )get_option( 'gmt_offset' );
        $tzstring = timezone_name_from_abbr( '', $offset * 60 * 60, 1 );
    }

    return $tzstring;
}


/**
 * @see http://wordpress.stackexchange.com/questions/94755/converting-timestamps-to-local-time-with-date-l18n#135049
 * @param string|null $format Format to use. Default: Wordpress date and time format.
 * @param int|null $timestamp The timestamp to localize. Default: time().
 * @return string The formatted datetime, localized and offset for local timezone.
 */
function wprss_local_date_i18n( $timestamp = null, $format = null ) {
    $format = is_null( $format ) ? get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) : $format;
    $timestamp = $timestamp ? $timestamp : time();

    $timezone_str = wprss_get_timezone_string() ? wprss_get_timezone_string() : 'UTC';
    $timezone = new DateTimeZone( $timezone_str );

    // The date in the local timezone.
    $date = new DateTime( null, $timezone );
    if ( version_compare(PHP_VERSION, '5.3', '>=') ) {
        $date->setTimestamp( $timestamp );
    } else {
        $datetime = getdate( intval($timestamp) );
        $date->setDate( $datetime['year'] , $datetime['mon'] , $datetime['mday'] );
        $date->setTime( $datetime['hours'] , $datetime['minutes'] , $datetime['seconds'] );
    }
    $date_str = $date->format( 'Y-m-d H:i:s' );

    // Pretend the local date is UTC to get the timestamp
    // to pass to date_i18n().
    $utc_timezone = new DateTimeZone( 'UTC' );
    $utc_date = new DateTime( $date_str, $utc_timezone );
    $timestamp = intval( $utc_date->format('U') );

    return date_i18n( $format, $timestamp, true );
}


/**
 * Gets an internationalized and localized datetime string, defaulting
 * to WP RSS format.
 *
 * @see wprss_local_date_i18n;
 * @param string|null $format Format to use. Default: Wordpress date and time format.
 * @param int|null $timestamp The timestamp to localize. Default: time().
 * @return string The formatted datetime, localized and offset for local timezone.
 */
function wprss_date_i18n( $timestamp = null, $format = null ) {
    $format = is_null( $format ) ? wprss_get_general_setting( 'date_format' ) : $format;

    return wprss_local_date_i18n( $timestamp, $format );
}


/**
 * Checks whether or not the Script Debug mode is on.
 *
 * By default, this is the value of the SCRIPT_DEBUG WordPress constant.
 * However, this can be changed via the filter.
 * Also, in earlier versions of WordPress, this constant does not seem
 * to be initially declared. In this case it is assumed to be false,
 * as per {@link https://codex.wordpress.org/Debugging_in_WordPress#SCRIPT_DEBUG WordPress Codex} documentation.
 *
 * @since 4.7.4
 * @uses-filter wprss_is_script_debug To modify return value.
 * @return boolean True if script debugging is on; false otherwise.
 */
function wprss_is_script_debug() {
    return apply_filters( 'wprss_is_script_debug', defined( 'SCRIPT_DEBUG' ) ? SCRIPT_DEBUG : false );
}


/**
 * Get the prefix for minified resources' extensions.
 *
 * @since 4.7.4
 * @see wprss_is_script_debug()
 * @uses-filter wprss_minified_extension_prefix To modify return value.
 * @return string The prefix that is to be applied to minified resources' file names, before the extension.
 */
function wprss_get_minified_extension_prefix() {
    return apply_filters( 'wprss_minified_extension_prefix', '.min' );
}


/**
 * Get the absolute URL to a WP RSS Aggregator script.
 *
 * If Script Debugging is on, the extension will be prefixed appropriately.
 *
 * @since 4.7.4
 * @see wprss_get_minified_extension_prefix()
 * @param string $url The relative URL to the script resource, without the extension.
 * @param string $extension The extension of the script file name, including the period (.). Default: '.js'.
 * @return string The URL to the script local to WP RSS Aggregator, possibly minified.
 */
function wprss_get_script_url( $url, $extension = null ) {
    if ( is_null( $extension ) )
        $extension = '.js';

    $script_url = WPRSS_JS . $url . (!wprss_is_script_debug() ? wprss_get_minified_extension_prefix() : '') . $extension;
    return apply_filters( 'wprss_script_url',  $script_url, $url, $extension );
}

// Checks if E&T add on is active
function wprss_is_et_active()
{
    return defined('WPRSS_ET_VERSION');
}

// If ET is active, fallback to the legacy rendering system
add_filter('wpra/templates/fallback_to_legacy_system', function ($fallback) {
    return $fallback || wprss_is_et_active();
});

// Notice for ET discontinuation
add_action('after_plugin_row', function ($plugin_file, $plugin_data, $status) {
    if ($plugin_data['Name'] !== 'WP RSS Aggregator - Excerpts and Thumbnails') {
        return;
    }

    echo '<tr class="plugin-update-tr wprss-et-plugin-row-msg">';
    echo '    <td colspan="3" class="plugin-update colspanchange">';
    echo '        <div class="update-message notice inline notice-success notice-alt">';

    printf('<p>%1$s <a href="%3$s" target="_blank">%2$s</a></p>',
        __('The Excerpts & Thumbnails addon has been discontinued in favor of the Templates addon.', 'wprss'),
        __('Click here to learn more.', 'wprss'),
        'https://www.wprssaggregator.com/excerpts-thumbnails-add-on-discontinued-templates-coming-soon/'
    );

    echo '        </div>';
    echo '    </td>';
    echo '</tr>';
}, 10, 3);
