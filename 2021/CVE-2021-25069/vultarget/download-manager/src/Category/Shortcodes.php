<?php


namespace WPDM\Category;


use WPDM\__\Template;
use WPDM\__\Query;

class Shortcodes
{
    function __construct()
    {
        add_shortcode("wpdm_category", [$this, 'listPackages']);
        add_shortcode('wpdm_category_link', [$this, 'categoryLink']);
    }

    function listPackages($params = array('id' => '', 'operator' => 'IN', 'items_per_page' => 10, 'title' => false, 'desc' => false, 'orderby' => 'create_date', 'order' => 'desc', 'paging' => false, 'toolbar' => 1, 'template' => '', 'cols' => 3, 'colspad' => 2, 'colsphone' => 1, 'morelink' => 1))
    {
        $params['categories'] = $params['id'];
        $params['catsc'] = 1;
        unset($params['id']);

        if(!in_array(wpdm_valueof($params, 'cat_field'), ['id', 'term_id'])) {
            $ids = [];
            foreach (explode(",", $params['categories']) as $slug) {
                $term = get_term_by('slug', $slug, 'wpdmcategory');
                if($term)
                    $ids[] = $term->term_id;
            }
            if(count($ids) > 0) {
                $params['categories'] = implode(",", $ids);
                $params['cat_field'] = 'id';
            }
        }
        return WPDM()->package->shortCodes->packages($params);

    }

    function categoryLink($params)
    {
        $category = new Category(wpdm_valueof($params, 'id'));
        if(!$category->ID) return '';
        $cat = (array)$category;
        $cat['icon'] = $category->icon ? "<img src='{category->icon}' alt='{$category->name}' />" : "";
        $template = isset($params['template']) && $params['template'] != '' ? $params['template'] : 'category-link-shortcode.php';
        return Template::output($template, $cat, __DIR__.'/views');
    }

}
