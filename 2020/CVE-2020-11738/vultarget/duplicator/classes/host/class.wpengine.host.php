<?php
defined("ABSPATH") or die("");
// New encryption class

class DUP_WPEngine_Host {
    public static function init() {
        add_filter('duplicator_installer_file_path', array('DUP_WPEngine_Host', 'installerFilePath'), 10, 1);
        add_filter('duplicator_global_file_filters_on', '__return_true');
        add_filter('duplicator_global_file_filters', array('DUP_WPEngine_Host', 'globalFileFilters'), 10, 1);
        add_filter('duplicator_defaults_settings', array('DUP_WPEngine_Host', 'defaultsSettings'));
    }

    public static function installerFilePath($path) {
        $path_info = pathinfo($path);
        $newPath = $path;
        if ('php' == $path_info['extension']) {
            $newPath = substr_replace($path, '.txt', -4);            
        }
        return $newPath;
    }

    public static function globalFileFilters($files) {
        $files[] = wp_normalize_path(WP_CONTENT_DIR).'/mysql.sql';
        return $files;
    }

    public static function defaultsSettings($defaults) {
        $defaults['package_zip_flush'] = '1';
        return $defaults;
    }

}

DUP_WPEngine_Host::init();