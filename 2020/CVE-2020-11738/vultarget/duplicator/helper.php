<?php
defined('ABSPATH') || exit;

if (!function_exists('duplicator_cloned_get_home_path')) {
    /**
     * Cloned function of the get_home_path(). It is same code except two lines of code
     * Get the absolute filesystem path to the root of the WordPress installation
     *
     * @return string Full filesystem path to the root of the WordPress installation
     */
    function duplicator_cloned_get_home_path()
    {
        $home    = set_url_scheme(get_option('home'), 'http');
        $siteurl = set_url_scheme(get_option('siteurl'), 'http');

        // below two lines
        // extra added by snapcreek
        // when home is www. path and siteurl is non-www , the duplicator_get_home_psth() was  returning empty value
        $home = str_ireplace('://www.', '://', $home);
        $siteurl = str_ireplace('://www.', '://', $siteurl);

        if (!empty($home) && 0 !== strcasecmp($home, $siteurl)  && $home !== $siteurl) {
            $wp_path_rel_to_home = str_ireplace($home, '', $siteurl); /* $siteurl - $home */
            $pos                 = strripos(str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']), trailingslashit($wp_path_rel_to_home));
            $home_path           = substr($_SERVER['SCRIPT_FILENAME'], 0, $pos);
            $home_path           = trailingslashit($home_path);
        } else {
            $home_path = ABSPATH;
        }
        return str_replace('\\', '/', $home_path);
    }
}

if (!function_exists('duplicator_get_home_path')) {
    function duplicator_get_home_path() {
        static $homePath = null;
        if (is_null($homePath)) {
            if (!function_exists('get_home_path')) {
                require_once(ABSPATH.'wp-admin/includes/file.php');
            }
            $homePath = wp_normalize_path(duplicator_cloned_get_home_path());
            if ($homePath == '//' || $homePath == '') {
                $homePath = '/';
            } else {
                $homePath = rtrim($homePath, '/');
            }
        }
        return $homePath;
    }
}

if (!function_exists('duplicator_get_abs_path')) {
    function duplicator_get_abs_path() {
        static $absPath = null;
        if (is_null($absPath)) {
            $absPath = wp_normalize_path(ABSPATH);
            if ($absPath == '//' || $absPath == '') {
                $absPath = '/';
            } else {
                $absPath = rtrim($absPath, '/');
            }
        }
        return $absPath;
    }
}