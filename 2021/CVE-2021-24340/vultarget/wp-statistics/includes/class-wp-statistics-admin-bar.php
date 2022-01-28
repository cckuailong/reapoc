<?php

namespace WP_STATISTICS;

class AdminBar
{
    /**
     * AdminBar constructor.
     */
    public function __construct()
    {

        # Show Wordpress Admin Bar
        add_action('admin_bar_menu', array($this, 'admin_bar'), 20);
    }

    /**
     * Check Show WP-Statistics Admin Bar
     */
    public static function show_admin_bar()
    {
        /**
         * Show/Hide Wp-Statistics Admin Bar
         *
         * @example add_filter('wp_statistics_show_admin_bar', function(){ return false; });
         */
        return (has_filter('wp_statistics_show_admin_bar')) ? apply_filters('wp_statistics_show_admin_bar', true) : Option::get('menu_bar');
    }

    /**
     * Show WordPress Admin Bar
     */
    public function admin_bar()
    {
        global $wp_admin_bar;

        // Check Show WordPress Admin Bar
        if (self::show_admin_bar() and is_admin_bar_showing() and User::Access()) {

            /**
             * List Of Admin Bar Wordpress
             *
             * --- Array Arg ---
             * Key : ID of Admin bar
             */
            $admin_bar_list = array(
                'wp-statistic-menu'                   => array(
                    'title' => '<span class="ab-icon"></span>',
                    'href'  => Menus::admin_url('overview')
                ),
                'wp-statistics-menu-useronline'       => array(
                    'parent' => 'wp-statistic-menu',
                    'title'  => __('Online User', 'wp-statistics') . ": " . wp_statistics_useronline(),
                    'href'   => Menus::admin_url('online')
                ),
                'wp-statistics-menu-todayvisitor'     => array(
                    'parent' => 'wp-statistic-menu',
                    'title'  => __('Today\'s Visitors', 'wp-statistics') . ": " . wp_statistics_visitor('today'),
                ),
                'wp-statistics-menu-todayvisit'       => array(
                    'parent' => 'wp-statistic-menu',
                    'title'  => __('Today\'s Visits', 'wp-statistics') . ": " . wp_statistics_visit('today')
                ),
                'wp-statistics-menu-yesterdayvisitor' => array(
                    'parent' => 'wp-statistic-menu',
                    'title'  => __('Yesterday\'s Visitors', 'wp-statistics') . ": " . wp_statistics_visitor('yesterday'),
                ),
                'wp-statistics-menu-yesterdayvisit'   => array(
                    'parent' => 'wp-statistic-menu',
                    'title'  => __('Yesterday\'s Visits', 'wp-statistics') . ": " . wp_statistics_visit('yesterday')
                ),
                'wp-statistics-menu-viewstats'        => array(
                    'parent' => 'wp-statistic-menu',
                    'title'  => __('View Stats', 'wp-statistics'),
                    'href'   => Menus::admin_url('overview')
                )
            );

            /**
             * WP-Statistics Admin Bar List
             *
             * @example add_filter('wp_statistics_admin_bar', function( $admin_bar_list ){ unset( $admin_bar_list['wp-statistics-menu-useronline'] ); return $admin_bar_list; });
             */
            $admin_bar_list = apply_filters('wp_statistics_admin_bar', $admin_bar_list);

            # Show Admin Bar
            foreach ($admin_bar_list as $id => $v_admin_bar) {
                $wp_admin_bar->add_menu(array_merge(array('id' => $id), $v_admin_bar));
            }
        }
    }
}

new AdminBar;