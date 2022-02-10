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
            include Template::locate("shortcode-iframe.php", __DIR__.'/views');
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
