<?php
/**
 * Actions and filters for package component
 * @since 4.6.0
 * @version 1.0.0
 */

namespace WPDM\Package;


use PrivateMessage\__\__Template;
use WPDM\__\Crypt;
use WPDM\__\Messages;
use WPDM\__\Template;
use WPDM\__\TempStorage;
use WPDM\__\UI;

class Hooks
{
    function __construct()
    {
        add_action("wp", [$this, 'wp']);
        add_filter("the_content", [$this, 'embedPackage']);
    }

    function wp()
    {
        $this->wpdmIframe();
        $this->shortcodeIframe();
        $this->customDownloadLinkPage();
        WPDM()->package->addViewCount();
    }

    function wpdmIframe()
    {
        if (isset($_REQUEST['__wpdmlo'])) {
            include Template::locate('lock-options-iframe.php', __DIR__.'/views');
            die();
        }
    }

    function shortcodeIframe()
    {
        if (isset($_REQUEST['__wpdmxp'])) {
            include __Template::locate("shortcode-iframe.php", __DIR__.'/views');
            die();
        }
    }

    function customDownloadLinkPage()
    {
        global $wp_query;
        $url = parse_url($_SERVER['REQUEST_URI']);
        if (preg_match('/wpdm\-download\/([^\/]+)/', wpdm_valueof($url, 'path'), $matches)) {
            $pack = Crypt::decrypt($matches[1], true);
            $package = get_post($pack['pid']);
            if(!$package) Messages::error(__( 'Package not found!', 'download-manager' ), 1);
            $validity = TempStorage::get("__wpdmkey_{$pack['key']}");
            if (!$validity)
                $validity = get_post_meta($pack['pid'], "__wpdmkey_{$pack['key']}", true);

            if (!is_array($validity)) $validity = array('expire' => 0, 'use' => 0);

            $validity['expire'] = $validity['expire'] - time();

            $mtime = '';
            if ($validity['expire'] > 0 && $validity['use'] > 0) {
                $init = $validity['expire'];
                $days = round($init / 86400);
                $hours = round($init / 3600);
                $minutes = round(($init / 60) % 60);
                $seconds = $init % 60;
                if ($days > 0)
                    $mtime .= "<b>{$days}</b> days ";
                else if ($hours > 0)
                    $mtime .= "<b>{$hours}</b> hours ";
                else if ($minutes > 0)
                    $mtime .= "<b>{$minutes}</b> mins ";
                else if ($seconds > 0)
                    $mtime .= "<b>{$seconds}</b> secs ";
                $keyvalid = true;
            } else {
                $keyvalid = false;
                $init = abs($validity['expire']);
                $days = round($init / 86400);
                $hours = round($init / 3600);
                $minutes = round(($init / 60) % 60);
                $_mtime = '';
                $seconds = $init % 60;
                if ($days > 0)
                    $_mtime .= "<b>{$days}</b> days ";
                else if ($hours > 0)
                    $_mtime .= "<b>{$hours}</b> hours ";
                else if ($minutes > 0)
                    $_mtime .= "<b>{$minutes}</b> mins ";
                else if ($seconds > 0)
                    $_mtime .= "{$seconds} secs";
                $validity['expired'] = $_mtime;
            }

            $files = WPDM()->package->getFiles($package->ID, true);
            $picon = get_post_meta($package->ID, '__wpdm_icon', true);
            $validity['expire'] = $mtime;
            $download_url = add_query_arg(array('wpdmdl' => $pack['pid'], '_wpdmkey' => $pack['key']), home_url());
            include Template::locate("download-page-clean.php", __DIR__.'/views');
            die();
        }
    }

    /**
     * Package detail page
     * @param $content
     * @return mixed|string
     */
    function embedPackage($content)
    {

        $wpdm_single_main_query = apply_filters('wpdmpro_single_main_query', is_main_query());
        $wpdm_single_main_loop = apply_filters('wpdmpro_single_main_loop', in_the_loop());

        if ( ( defined('WPDM_THEME_SUPPORT') && WPDM_THEME_SUPPORT == true )
            || current_theme_supports('wpdm')
            || get_post_type(get_the_ID()) != 'wpdmpro'
            || ! is_singular('wpdmpro')
            || ! $wpdm_single_main_query
            || ! $wpdm_single_main_loop
        ) {
            return $content;
        }

        $template = get_post_meta(get_the_ID(), '__wpdm_page_template', true);
        $data = WPDM()->package->fetchTemplate($template, get_the_ID(), 'page');

        return UI::div($data, 'w3eden');
    }
}
