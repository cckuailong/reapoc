<?php

namespace ProfilePress\Core\NavigationMenuLinks;


if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Frontend
{
    private static $instance = null;

    public function __construct()
    {
        /* The main code, this replace the #keyword# by the correct links with nonce ect */
        add_filter('wp_setup_nav_menu_item', [$this, 'setup_nav_menu_item']);

        add_filter('wp_nav_menu_objects', [$this, 'wp_nav_menu_objects']);
    }

    /**
     * Used to return the correct title for the double login/logout menu item
     */
    public function loginout_title($title)
    {
        $titles = explode('|', $title);
        if ( ! is_user_logged_in()) {
            return esc_html(isset($titles[0]) ? $titles[0] : $title);
        } else {
            return esc_html(isset($titles[1]) ? $titles[1] : $title);
        }
    }

    public function setup_nav_menu_item($item)
    {
        global $pagenow;

        if (is_customize_preview() && ! (is_home() || is_front_page() || is_singular() || is_archive() || is_tax())) {
            return $item;
        }

        if ($pagenow != 'nav-menus.php' && ! defined('DOING_AJAX') && isset($item->url) && strstr($item->url, '#pp-') != ''
        ) {
            $item_url = substr($item->url, 0, strpos($item->url, '#', 1)) . '#';

            switch ($item_url) {
                case '#pp-loginout#' :
                    $item->url   = is_user_logged_in() ? wp_logout_url() : wp_login_url();
                    $item->title = $this->loginout_title($item->title);
                    $item        = apply_filters('pp_nav_loginout_item', $item);
                    break;
                case '#pp-login#' :
                    if (is_user_logged_in()) {
                        $item->title = '#pp-login#';
                    } else {
                        $item->url = wp_login_url();
                    }
                    $item = apply_filters('pp_nav_login_item', $item);
                    break;
                case '#pp-logout#' :
                    if (is_user_logged_in()) {
                        $item->url = wp_logout_url();
                    } else {
                        $item->title = '#pp-logout#';
                    }
                    $item = apply_filters('pp_nav_logout_item', $item);
                    break;
                case '#pp-signup#' :
                    if (is_user_logged_in()) {
                        $item->title = '#pp-signup#';
                    } else {
                        $item->url = wp_registration_url();
                    }
                    $item = apply_filters('pp_nav_signup_item', $item);
                    break;
                case '#pp-myprofile#' :
                    if (is_user_logged_in()) {

                        if (function_exists('ppress_profile_url')) {
                            $item->url = ppress_profile_url();
                        }

                        if (function_exists('pp_profile_url')) {
                            $item->url = pp_profile_url();
                        }

                    } else {
                        $item->title = '#pp-myprofile#';
                    }
                    $item = apply_filters('pp_nav_myprofile_item', $item);
                    break;
                case '#pp-editprofile#' :
                    if (is_user_logged_in() && function_exists('ppress_edit_profile_url')) {
                        $item->url = ppress_edit_profile_url();
                    } elseif (is_user_logged_in() && function_exists('pp_edit_profile_url')) {
                        $item->url = pp_edit_profile_url();
                    } else {
                        $item->title = '#pp-editprofile#';
                    }
                    $item = apply_filters('pp_nav_editprofile_item', $item);
                    break;
            }
            $item->url = esc_url($item->url);
        }

        return apply_filters('pp_nav_item', $item);
    }


    /**
     * Remove navigation item with URL and title that are the same.
     *
     * @param array $sorted_menu_items
     *
     * @return mixed
     */
    public function wp_nav_menu_objects($sorted_menu_items)
    {
        // in self::setup_nav_menu_item we made the title and url of some nav items the same.
        // an item that fall in this category get removed here.
        foreach ($sorted_menu_items as $k => $item) {
            if (strlen($item->title) && ($item->title == $item->url)) {
                unset($sorted_menu_items[$k]);
            }
        }

        return $sorted_menu_items;
    }

    /**
     * @return null|Frontend
     */
    public static function get_instance()
    {
        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

}