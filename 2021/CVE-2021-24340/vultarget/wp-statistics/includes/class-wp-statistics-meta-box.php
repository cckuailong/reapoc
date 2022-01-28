<?php

namespace WP_STATISTICS;

class Meta_Box
{
    /**
     * Meta Box Class namespace
     *
     * @var string
     */
    public static $namespace = "\\WP_Statistics\\MetaBox\\";

    /**
     * Meta Box Setup Key
     *
     * @param $key
     * @return string
     */
    public static function getMetaBoxKey($key)
    {
        return 'wp-statistics-' . $key . '-widget';
    }

    /**
     * Load WordPress Meta Box
     */
    public static function includes()
    {
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-quickstats.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-summary.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-browsers.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-platforms.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-countries.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-hits.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-pages.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-referring.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-search.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-words.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-top-visitors.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-recent.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-hitsmap.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-useronline.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-about.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-post.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-top-pages-chart.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-pages-chart.php';
        require_once WP_STATISTICS_DIR . 'includes/admin/meta-box/wp-statistics-meta-box-exclusions.php';
    }

    /**
     * Get Admin Meta Box List
     *
     * @param bool $meta_box
     * @return array|mixed
     */
    public static function getList($meta_box = false)
    {
        /**
         * List of WP-Statistics Admin Meta Box
         *
         * --- Array Arg -----
         * page_url          : link of Widget Page @see WP_Statistics::$page
         * name              : Name Of Widget Box
         * require           : the Condition From Wp-statistics Option if == true
         * show_on_dashboard : Show Meta Box in WordPress Dashboard
         * hidden            : if set true , Default Hidden Dashboard in Wordpress Admin
         * js                : if set false, Load without RestAPI Request.
         * place             : Meta Box Place in Overview Page [ normal | side ]
         * disable_overview  : Disable MetaBox From Overview Page [ default : false ]
         * hidden_overview   : if set true , Default Hidden Meta Box in OverView Page
         *
         */
        $list = array(
            'quickstats'      => array(
                'page_url'          => 'overview',
                'name'              => __('Quick Stats', 'wp-statistics'),
                'show_on_dashboard' => true,
                'hidden'            => false,
                'place'             => 'side',
                'disable_overview'  => true
            ),
            'summary'         => array(
                'name'              => __('Summary', 'wp-statistics'),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'side'
            ),
            'browsers'        => array(
                'page_url'          => 'browser',
                'name'              => __('Top 10 Browsers', 'wp-statistics'),
                'require'           => array('visitors' => true),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'side'
            ),
            'platforms'       => array(
                'page_url'          => 'platform',
                'name'              => __('Top Platforms', 'wp-statistics'),
                'require'           => array('visitors' => true),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'side'
            ),
            'countries'       => array(
                'page_url'          => 'countries',
                'name'              => __('Top 10 Countries', 'wp-statistics'),
                'require'           => array('geoip' => true, 'visitors' => true),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'side'
            ),
            'hits'            => array(
                'page_url'          => 'hits',
                'name'              => __('Hit Statistics', 'wp-statistics'),
                'require'           => array('visits' => true),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'normal'
            ),
            'pages'           => array(
                'page_url'          => 'pages',
                'name'              => __('Top 10 Pages', 'wp-statistics'),
                'require'           => array('pages' => true),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'normal'
            ),
            'referring'       => array(
                'page_url'          => 'referrers',
                'name'              => __('Top Referring Sites', 'wp-statistics'),
                'require'           => array('visitors' => true),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'side'
            ),
            'search'          => array(
                'page_url'          => 'searches',
                'name'              => __('Search Engine Referrals', 'wp-statistics'),
                'require'           => array('visitors' => true),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'normal'
            ),
            'words'           => array(
                'page_url'          => 'words',
                'name'              => __('Latest Search Words', 'wp-statistics'),
                'require'           => array('visitors' => true),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'normal',
                'hidden_overview'   => true
            ),
            'top-visitors'    => array(
                'page_url'          => 'top-visitors',
                'name'              => __('Top 10 Visitors Today', 'wp-statistics'),
                'require'           => array('visitors' => true),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'normal'
            ),
            'recent'          => array(
                'page_url'          => 'visitors',
                'name'              => __('Recent Visitors', 'wp-statistics'),
                'require'           => array('visitors' => true),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'normal'
            ),
            'hitsmap'         => array(
                'name'              => __('Today\'s Visitors Map', 'wp-statistics'),
                'require'           => array('geoip' => true, 'visitors' => true, 'disable_map' => false),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'normal'
            ),
            'useronline'      => array(
                'name'              => __('Online Users', 'wp-statistics'),
                'page_url'          => 'online',
                'require'           => array('useronline' => true),
                'hidden'            => true,
                'show_on_dashboard' => true,
                'place'             => 'side'
            ),
            'about'           => array(
                'name'              => sprintf(__('WP Statistics - Version %s', 'wp-statistics'), WP_STATISTICS_VERSION),
                'show_on_dashboard' => false,
                'js'                => false,
                'place'             => 'side'
            ),
            'post'            => array(
                'name'              => __('Hit Statistics', 'wp-statistics'),
                'page_url'          => 'pages',
                'show_on_dashboard' => false,
                'disable_overview'  => true
            ),
            'top-pages-chart' => array(
                'name'              => __('Top 5 Pages Trends', 'wp-statistics'),
                'show_on_dashboard' => false,
                'disable_overview'  => true
            ),
            'pages-chart'     => array(
                'name'              => __('Pages Hits', 'wp-statistics'),
                'show_on_dashboard' => false,
                'disable_overview'  => true
            ),
            'exclusions'      => array(
                'name'              => __('Exclusions', 'wp-statistics'),
                'show_on_dashboard' => false,
                'disable_overview'  => true
            ),
        );

        //Print List of Meta Box
        if ($meta_box === false) {
            return $list;
        } else {
            if (array_key_exists($meta_box, $list)) {
                return $list[$meta_box];
            }
        }

        return array();
    }

    /**
     * Get Meta Box Class name
     *
     * @param $meta_box
     * @return string
     */
    public static function getMetaBoxClass($meta_box)
    {
        return self::$namespace . str_replace("-", "_", $meta_box);
    }

    /**
     * Check Exist Meta Box Class
     *
     * @param $meta_box
     * @return bool
     */
    public static function IsExistMetaBoxClass($meta_box)
    {
        return class_exists(self::getMetaBoxClass($meta_box));
    }

    /**
     * Load MetaBox
     *
     * @param $key
     * @return null
     */
    public static function LoadMetaBox($key)
    {

        // Get MetaBox by Key
        $metaBox = self::getList($key);
        if (count($metaBox) > 0) {

            // Check Load Rest-API or Manually
            if (isset($metaBox['js']) and $metaBox['js'] === false) {
                $class = self::getMetaBoxClass($key);
                return array($class, 'get');
            }
        }

        return function () {
            return null;
        };
    }

}