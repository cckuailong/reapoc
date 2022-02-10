<?php


namespace WPDM\Package;


use WPDM\__\__;
use WPDM\__\Template;
use WPDM\__\Crypt;
use WPDM\__\Query;

class Shortcodes
{
    function __construct()
    {
        // Single package shortcode
        add_shortcode("wpdm_package", [$this, 'singlePackage']);

        // Generate direct download link
        add_shortcode('wpdm_direct_link', [$this, 'directLink']);

        // Show all packages
        add_shortcode('wpdm_packages', [$this, 'packages']);

        //Packages by tag
        add_shortcode("wpdm_tag", [$this, 'packagesByTag']);

        // Total Package Count
        add_shortcode('wpdm_download_count', [$this, 'totalDownloads']);

        // Total Package Count
        add_shortcode('wpdm_package_count', [$this, 'totalPackages']);

        // Show all packages in a responsive table
        add_shortcode('wpdm_all_packages', [$this, 'allPackages']);
        add_shortcode('wpdm-all-packages', [$this, 'allPackages']);

        //Search downloads
        add_shortcode('wpdm_search_result', [$this, 'searchResult']);


    }

    /**
     * Callback function for [wpdm_package..]
     * @param array $params
     * @return string
     */
    function singlePackage($params = [])
    {
        $id = wpdm_valueof($params, 'id', [ 'validate' => 'int' ]);

        if(!$id && is_singular('wpdmpro')) $id = get_the_ID();

        //Return if id is invalid
        if (!$id || get_post_type($id) !== 'wpdmpro') return '';

        //Link template
        $template = isset($params['template']) ? $params['template'] : get_post_meta($id, '__wpdm_template', true);
        if ($template == '') $template = 'link-template-default.php';
        $pack = new Package($id);
        $html = "<div class='w3eden'>" . $pack->fetchTemplate($template, $id, 'link') . "</div>";

        return $html;
    }

    /**
     * @usage Generate direct link to download
     * @param $params
     * @param string $content
     * @return string
     */
    function directLink($params, $content = "")
    {
        extract($params);

        global $wpdb;
        $ID = (int)$params['id'];

        if (WPDM()->package->isLocked($ID))
            $linkURL = get_permalink($ID);
        else
            $linkURL = home_url("/?wpdmdl=" . $ID);

        $extras = isset($params['extras']) ? wpdm_sanitize_var($params['extras'], 'txt') : "";
        $target = isset($params['target']) ? "target='" . wpdm_sanitize_var($params['target'], 'txt') . "'" : "";
        $class = isset($params['class']) ? "class='" . wpdm_sanitize_var($params['class'], 'txt') . "'" : "";
        $style = isset($params['style']) ? "style='" . wpdm_sanitize_var($params['style'], 'txt') . "'" : "";
        $rel = isset($params['rel']) ? "rel='" . wpdm_sanitize_var($params['rel'], 'txt') . "'" : "";
        $eid = isset($params['eid']) ? "id='" . wpdm_sanitize_var($params['eid'], 'txt') . "'" : "";
        $linkLabel = isset($params['label']) && !empty($params['label']) ? $params['label'] : get_post_meta($ID, '__wpdm_link_label', true);
        $linkLabel = empty($linkLabel) ? get_the_title($ID) : $linkLabel;
        $linkLabel = wpdm_sanitize_var($linkLabel, 'kses');
        return "<a {$target} {$class} {$eid} {$style} {$rel} {$extras} href='$linkURL'>$linkLabel</a>";

    }

    /**
     * @param array $params
     * @return string
     */
    /**
     * @param array $params
     * @return string
     */
    function packages($params = array('items_per_page' => 10, 'title' => '', 'desc' => '', 'orderby' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => '', 'cols' => 3, 'colspad' => 2, 'colsphone' => 1, 'tags' => '', 'categories' => '', 'year' => '', 'month' => '', 's' => '', 'css_class' => 'wpdm_packages', 'scid' => '', 'async' => 1, 'tax' => '', 'terms' => ''))
    {
        global $current_user, $post;

        static $wpdm_packages = 0;

        // When login=1, show login form for guests/visitors
        if (isset($params['login']) && $params['login'] == 1 && !is_user_logged_in())
            return WPDM()->user->login->form($params);

        // To generate unique sections ID when someone uses multiple shortcode on a page
        $wpdm_packages++;

        // $scparams = Initial unprocessed shortcode parameters
        $scparams = $params;



        $async = __::valueof($params, 'async', ['default' => 0, 'validate' => 'int']);
        $items_per_page = __::valueof($params, 'items_per_page', 10);
        if($items_per_page < 1) $items_per_page = 10;
        $scid = __::valueof($params, 'scid', ['default' => 'wpdm_package_'.$wpdm_packages, 'validate' => 'txt']);
        $cwd_class = "col-lg-" . (int)(12 / __::valueof($params, 'cols', ['default' => 3, 'validate' => 'int']));
        $cwdsm_class = "col-md-" . (int)(12 /  __::valueof($params, 'colspad', ['default' => 2, 'validate' => 'int']));
        $cwdxs_class = "col-" . (int)(12 /  __::valueof($params, 'colsphone', ['default' => 1, 'validate' => 'int']));

        $title = __::valueof($params, 'title', ['validate' => 'txt']);
        $desc = __::valueof($params, 'desc', ['validate' => 'txt']);
        $toolbar = __::valueof($params, 'toolbar', ['default' => 1, 'validate' => 'int']);
        $paging = __::valueof($params, 'paging', ['default' => 1, 'validate' => 'int']);

        $order_field = __::valueof($params, 'orderby', 'date');
        $order_field = isset($_REQUEST['orderby'])? wpdm_query_var('orderby') : $order_field;
        $order = __::valueof($params, 'order', 'desc');
        $order = isset($_REQUEST['order'])? wpdm_query_var('order') : $order;

        $currentPage = __::query_var('cp', 'int');
        if (!$currentPage) $currentPage = 1;

        $query = new Query();
        $query->items_per_page(wpdm_valueof($params, 'items_per_page', 10));
        $query->paged($currentPage);
        $query->sort($order_field, $order);

        foreach ($scparams as $key => $value) {
            if (method_exists($query, $key) && !in_array($key, ['categories', 'tags'])) {
                $query->$key($value);
            }
        }

        /**
         * Process "categories" parameter
         * Usually values are category slug(s), users may use id(s) too
         * If users uses slugs, convert slugs into ids
         **/
        if(wpdm_valueof($scparams, 'categories') !== '') {
            $categories = wpdm_valueof($scparams, 'categories');
            $categories = explode(",", $categories);
            /**
             * Convert slugs to ID
             */
            foreach ($categories as &$cat) {
                if (!is_numeric($cat) && $cat !== '') {
                    $catObj = get_term_by('slug', $cat, 'wpdmcategory');
                    $cat = $catObj->term_id;
                }
            }
            $operator = wpdm_valueof($scparams, 'operator', ['default' => 'IN']);
            $include_children = wpdm_valueof($scparams, 'include_children', ['default' => false]);
            $query->categories($categories, 'term_id', $operator, $include_children);
        }

        if(wpdm_valueof($scparams, 'tags') !== '') {
            $tags = wpdm_valueof($scparams, 'tags');
            $operator = wpdm_valueof($scparams, 'operator', ['default' => 'IN']);
            $query->tags($tags, 'slug', $operator);
        }

        if (wpdm_query_var('skw', 'txt') != '') {
            $query->s(wpdm_query_var('skw', 'txt'));
        }

        if(wpdm_valueof($scparams, 'tax') !== '') {
            $_terms = explode("|", wpdm_valueof($scparams, 'terms'));
            $taxos = explode("|", wpdm_valueof($scparams, 'tax'));
            foreach ($taxos as $index => $_taxo) {
                $terms = wpdm_valueof($_terms, $index);
                $terms = explode(",", $terms);
                if(count($terms) > 0) {

                    $query->taxonomy($_taxo, $terms);
                }
            }
        }

        $query->taxonomy_relation(wpdm_valueof($scparams, 'relation', ['default' => 'AND']));

        if (get_option('_wpdm_hide_all', 0) == 1) {
            $query->meta("__wpdm_access", '"guest"');

            if (is_user_logged_in()) {
                foreach ($current_user->roles as $role) {
                    $query->meta("__wpdm_access", $role);
                }
                $query->meta_relation('OR');
            }
        }

        // Date filter
        if (isset($scparams['year']) || isset($scparams['month']) || isset($scparams['day'])) {

            if (isset($scparams['day'])) {
                $day = ($scparams['day'] == 'today') ? date('d') : $scparams['day'];
                $query->filter('day', $day);
            }

            if (isset($scparams['month'])) {
                $month = ($scparams['month'] == 'this') ? date('Ym') : $scparams['month'];
                $query->filter('m', $month);
            }

            if (isset($scparams['year'])) {
                $year = ($scparams['year'] == 'this') ? date('Y') : $scparams['year'];
                $query->filter('year', $year);
            }

            if (isset($scparams['week'])) {
                $week = ($scparams['week'] == 'this') ? date('W') : $scparams['week'];
                $query->filter('week', $week);
            }
        }

        $query->post_status('publish');
        $query->process();
        $total = $query->count;
        $packages = $query->packages();

        $pages = ceil($total / $items_per_page);
        $page = isset($_GET['cp']) ? (int)$_GET['cp'] : 1;
        $start = ($page - 1) * $items_per_page;


        $html = '';

        foreach ($packages as $pack){
            $pack = (array)$pack;
            //$repeater = "<div class='{$cwd_class} {$cwdsm_class} {$cwdxs_class}'>" . \WPDM\Package::fetchTemplate(wpdm_valueof($scparams, 'template', 'link-template-default.php'), $pack) . "</div>";
            $repeater = "<div class='{$cwd_class} {$cwdsm_class} {$cwdxs_class}'>" . WPDM()->package->fetchTemplate(wpdm_valueof($scparams, 'template', 'link-template-default.php'), $pack) . "</div>";
            $html .= $repeater;

        }


        $html = "<div class='row'>{$html}</div>";

        $_scparams = Crypt::encrypt($scparams);
        if (!isset($paging) || intval($paging) == 1) {
            //sc_params={$_scparams}&
            $pag_links = wpdm_paginate_links($total, $items_per_page, $page, 'cp', array( 'format' => "?cp=%#%",'container' => '#content_' . $scid, 'async' => $async, 'next_text' => ' <i style="display: inline-block;width: 8px;height: 8px;border-right: 2px solid;border-top: 2px solid;transform: rotate(45deg);margin-left: -2px;margin-top: -2px;"></i> ', 'prev_text' => ' <i style="display: inline-block;width: 8px;height: 8px;border-right: 2px solid;border-bottom: 2px solid;transform: rotate(135deg);margin-left: 2px;margin-top: -2px;"></i> '));
            $pagination = "<div style='clear:both'></div>" . $pag_links . "<div style='clear:both'></div>";
        } else
            $pagination = "";

        global $post;

        $burl = get_permalink();
        $sap = get_option('permalink_structure') ? '?' : '&';
        $burl = $burl . $sap;
        if (isset($_GET['p']) && $_GET['p'] != '') $burl .= 'p=' . wpdm_query_var('p', 'txt') . '&';
        if (isset($_GET['src']) && $_GET['src'] != '') $burl .= 'src=' . wpdm_query_var('src', 'txt') . '&';
        $orderby = isset($_GET['orderby']) ? wpdm_query_var('orderby', 'txt') : 'date';
        $order = ucfirst($order);

        $order_field = " " . __(ucwords(str_replace("_", " ", $order_field)), "wpdmpro");
        $ttitle = __("Title", "download-manager");
        $tdls = __("Downloads", "download-manager");
        $tcdate = __("Publish Date", "download-manager");
        $tudate = __("Update Date", "download-manager");
        $tasc = __("Asc", "download-manager");
        $tdsc = __("Desc", "download-manager");
        $tsrc = __("Search", "download-manager");
        $ord = __("Order", "download-manager");
        $order_by_label = __("Order By", "download-manager");

        $css_class = isset($scparams['css_class']) ? sanitize_text_field($scparams['css_class']) : '';
        $desc = isset($scparams['desc']) ? sanitize_text_field($scparams['desc']) : '';

        $toolbar = isset($toolbar) ? $toolbar : 0;

        ob_start();
        include Template::locate("packages-shortcode.php", __DIR__.'/views');
        $content = ob_get_clean();
        return $content;
    }

    /**
     * @usage Show packages by tag
     * @param $params
     * @return string
     */
    function packagesByTag($params)
    {
        if(!$params || !isset($params['id'])) return '';

        $params['tags'] = $params['id'];
        unset($params['id']);
        return $this->packages($params);

    }


    /**
     * @param array $params
     * @return array|null|\WP_Post
     * @usage Shortcode callback function for [wpdm_search_result]
     */
    function searchResult($params = array())
    {
        global $wpdb;

        if (is_array($params))
            @extract($params);
        $template = isset($template) && $template != '' ? $template : 'link-template-calltoaction3';
        $async = isset($async) ? $async : 0;
        $items_per_page = isset($items_per_page) ? $items_per_page : 0;
        update_post_meta(get_the_ID(), "__wpdm_link_template", $template);
        update_post_meta(get_the_ID(), "__wpdm_items_per_page", $items_per_page);
        $strm = wpdm_query_var('search', 'txt');
        if ($strm === '') $strm = wpdm_query_var('s', 'txt');
        $html = '';
        $cols = isset($cols) ? $cols : 1;
        $colspad = isset($colspad) ? $colspad : 1;
        $colsphone = isset($colsphone) ? $colsphone : 1;
        if (($strm == '' && isset($init) && $init == 1) || $strm != '')
            $html = $this->packages(array('items_per_page' => $items_per_page, 'template' => $template, 's' => $strm, 'paging' => true, 'toolbar' => 0, 'cols' => $cols, 'colsphone' => $colsphone, 'colspad' => $colspad, 'async' => $async));
        $html = "<div class='w3eden'><form id='wpdm-search-form' style='margin-bottom: 20px'><div class='input-group input-group-lg'><div class='input-group-addon input-group-prepend'><span class=\"input-group-text\"><i class='fas fa-search'></i></span></div><input type='text' name='search' value='" . $strm . "' class='form-control input-lg' /></div></form>{$html}</div>";
        return str_replace(array("\r", "\n"), "", $html);
    }

    /**
     * @usage Short-code function for total download count
     * @param array $params
     * @return mixed
     */
    function totalDownloads($params = array())
    {
        global $wpdb;
        $download_count = $wpdb->get_var("select sum(meta_value) from {$wpdb->prefix}postmeta where meta_key='__wpdm_download_count'");
        return $download_count;
    }

    /**
     * @usage Short-code function for total package count
     * @param array $params
     * @return mixed
     */
    function totalPackages($params = array())
    {
        if (isset($params['cat'])) {
            $term = get_term_by("slug", $params['cat']);
            if (is_object($term) && isset($term->count)) return $term->count;
            return 0;
        }
        if (isset($params['author'])) {
            $user_post_count = count_user_posts($params['author'], 'wpdmpro');
            return $user_post_count;
        }
        $count_posts = wp_count_posts('wpdmpro');
        $status = isset($params['status']) ? $params['status'] : 'publish';
        if ($status == 'draft') return $count_posts->draft;
        if ($status == 'pending') return $count_posts->pending;
        return $count_posts->publish;
    }

    /**
     * @usage Short-code [wpdm_all_packages] to list all packages in tabular format
     * @param array $params
     * @return string
     */
    function allPackages($params = array())
    {
        global $wpdb, $current_user, $wp_query;
        $items = isset($params['items_per_page']) && $params['items_per_page'] > 0 ? $params['items_per_page'] : 20;
        $offset = $cp = 0;
        if (isset($params['jstable']) && $params['jstable'] == 1) {
            $items = 2000;
        } else {
            $cp = wpdm_query_var('cp', ['validate' => 'int', 'default' => 1]);
            $offset = ($cp - 1) * $items;

        }
        $terms = isset($params['categories']) ? explode(",", $params['categories']) : array();
        $tag = isset($params['tag']) ? $params['tag'] : '';
        if (isset($_GET['wpdmc'])) $terms = array(wpdm_query_var('wpdmc', 'txt'));
        $total_files = wp_count_posts('wpdmpro')->publish;
        if (count($terms) > 0) {
            $tax_query = array(array(
                'taxonomy' => 'wpdmcategory',
                'field' => 'slug',
                'terms' => $terms,
                'operator' => 'IN',
                'include_children' => false
            ));
        }
        if ($tag != '') {
            $params['tag'] = $tag;
        }
        if (isset($params['login']) && $params['login'] == 1 && !is_user_logged_in())
            return WPDM()->user->login->form($params);
        else {
            ob_start();
            //include(wpdm_tpl_path("wpdm-all-downloads.php"));
            include Template::locate("all-packages-shortcode.php", __DIR__.'/views');
            $data = ob_get_clean();
            return $data;
        }
    }

}

