<?php

defined('ABSPATH') || die;

/**
 * Provide a partial, native PHP implementation for the Ctype extension.
 * @see https://github.com/symfony/polyfill-ctype
 */
if (!extension_loaded('ctype')) {
    require_once __DIR__.'/vendors/symfony/polyfill-ctype/bootstrap.php';
}

/**
 * Provide a partial, native PHP implementation for the Mbstring extension.
 * @see https://github.com/symfony/polyfill-mbstring
 */
if (!extension_loaded('mbstring')) {
    require_once __DIR__.'/vendors/symfony/polyfill-mbstring/bootstrap.php';
}

/**
 * Load the Action Scheduler library
 * @see https://actionscheduler.org
 */
require_once __DIR__.'/vendors/woocommerce/action-scheduler/action-scheduler.php';

spl_autoload_register(function ($className) {
    $classMap = [
        'WP_Debug_Data' => ABSPATH.'wp-admin/includes/class-wp-debug-data.php',
        'WP_List_Table' => ABSPATH.'wp-admin/includes/class-wp-list-table.php',
        'WP_Posts_List_Table' => ABSPATH.'wp-admin/includes/class-wp-posts-list-table.php',
    ];
    if (array_key_exists($className, $classMap) && file_exists($classMap[$className])) {
        require_once $classMap[$className];
    }
    $namespaces = [
        'GeminiLabs\\SiteReviews\\' => __DIR__.'/plugin/',
        'GeminiLabs\\SiteReviews\\Tests\\' => __DIR__.'/tests/',
        'GeminiLabs\\League\\Csv\\' => __DIR__.'/vendors/thephpleague/csv/',
        'GeminiLabs\\Sepia\\PoParser\\' => __DIR__.'/vendors/sepia/po-parser/',
        'GeminiLabs\\Sinergi\\BrowserDetector\\' => __DIR__.'/vendors/sinergi/browser-detector/',
        'GeminiLabs\\Spatie\\Color\\' => __DIR__.'/vendors/spatie/color/',
        'GeminiLabs\\Symfony\\Polyfill\\Ctype\\' => __DIR__.'/vendors/symfony/polyfill-ctype/',
        'GeminiLabs\\Symfony\\Polyfill\\Mbstring\\' => __DIR__.'/vendors/symfony/polyfill-mbstring/',
        'GeminiLabs\\Vectorface\\Whip\\' => __DIR__.'/vendors/vectorface/whip/',
    ];
    foreach ($namespaces as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (0 !== strncmp($prefix, $className, $len)) {
            continue;
        }
        $file = $baseDir.str_replace('\\', '/', substr($className, $len)).'.php';
        if (!file_exists($file)) {
            continue;
        }
        require $file;
        break;
    }
});
