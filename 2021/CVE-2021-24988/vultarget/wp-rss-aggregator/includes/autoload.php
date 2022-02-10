<?php

!defined('WPRSS_CORE_DISABLE_AUTOLOAD') &&
    define('WPRSS_CORE_DISABLE_AUTOLOAD', false);

if (!function_exists('wprss_autoloader')) {
    /**
     *
     * @return Aventura\Wprss\Core\Loader The loader singleton instance
     */
    function wprss_autoloader() {
        static $loader = null;
        $className = 'Aventura\\Wprss\\Core\\Loader';

        $composerAutoloader = '/vendor/autoload.php';
        if (!WPRSS_CORE_DISABLE_AUTOLOAD) {
            foreach(array(
                untrailingslashit(WPRSS_DIR) . $composerAutoloader, // Standalone
                realpath(WPRSS_DIR . '/../../..') . $composerAutoloader, // Vanilla WP, or another root package
            ) as $wprssAutoloadPath) {
                if (file_exists($wprssAutoloadPath)) {
                    require_once($wprssAutoloadPath);
                    break;
                }
            }
        }

        if (!class_exists($className)) {
            $dir = dirname(__FILE__);
            $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
            $classPath = "{$dir}/{$classPath}.php";
            require_once($classPath);
        }

        if ($loader === null) {
            $loader = new $className();
            /* @var $loader Aventura\Wprss\Core\Loader */
            !WPRSS_CORE_DISABLE_AUTOLOAD &&
                $loader->register();
        }

        return $loader;
    }
}
