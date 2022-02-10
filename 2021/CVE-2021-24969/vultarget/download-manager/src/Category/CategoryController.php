<?php
/**
* Download manager category controller class
 */
namespace WPDM\Category;

use WPDM\__\Template;

class CategoryController
{

    private $cbTreeInit = 0;
    public $shortcode;

    function __construct()
    {
        add_filter("template_include", [$this, 'wpdmproTemplates'], 9999);
        $this->shortcode = new Shortcodes();
    }

    function hArray($skip_filter = 1, $parent = 0)
    {
        $terms = get_terms(array('taxonomy' => 'wpdmcategory', 'parent' => $parent, 'hide_empty' => false));
        $allterms = _get_term_hierarchy('wpdmcategory');
        $allcats = array();
        foreach ($terms as $term) {
            $allcats[$term->term_id] = array('category' => $term, 'access' => maybe_unserialize(get_term_meta($term->term_id, '__wpdm_access')), 'childs' => array());
            $this->exploreChilds($term, $allcats[$term->term_id]['childs']);
        }

        if ($skip_filter === 0){
            /**
            * @deprecated use WPDM_Category_CategoryController_hArray instead
            */
            $allcats = apply_filters("WPDM_libs_CategoryHandler_hArray", $allcats);
            $allcats = apply_filters("WPDM_Category_CategoryController_hArray", $allcats);
        }

        return $allcats;
    }

    /**
     * Generates WordPress Download Manager category selector wit checkbox and ul/li
     * @param $name
     * @param array $selected
     * @param array $extras
     */
    function checkboxTree($name, $selected = array(), $extras = array())
    {
        echo "<ul class='ptypes m-0 p-0' id='wpdmcat-tree'>";
        $parent = wpdm_valueof($extras, 'parent', ['validate' => 'int']);
        $allcats = WPDM()->categories->hArray(0, $parent);

        /*$cparent = is_array($extras) && isset($extras['base_category']) ? $extras['base_category'] : 0;
        if ($cparent !== 0) {
            $cparent = get_term($cparent);
            $cparent = $cparent->term_id;
            echo "<input type='hidden' value='{$cparent}' name='cats[]' />";
        }*/
        if($parent > 0 && wpdm_valueof($extras, 'hide_parent', ['validate' => 'int', 'default' => 0]) == 0) {
            $term = get_term($parent);
            $value = wpdm_valueof($extras, 'value') === 'slug' ? $term->slug : $term->term_id;
            ?>
            <ul>
            <li class="<?php echo wpdm_valueof($extras, 'liclass'); ?>">
                <label><input type="checkbox" <?php checked(1, in_array($value, $selected)); ?>
                              name="<?php echo $name; ?>[]"
                              class="<?php echo wpdm_valueof($extras, 'cbclass'); ?>"
                              value="<?php echo $value; ?>"> <?php echo $term->name; ?> </label>
                <ul>
            <?php
        }
        $this->checkboxList($name, $allcats, $selected, $extras);
        if($parent > 0 && wpdm_valueof($extras, 'hide_parent', ['validate' => 'int', 'default' => 0]) == 0) echo "</li></ul>";
        echo "</ul>";
    }

    private function checkboxList($name, $allcats, $selected = array(), $extras = array())
    {
        foreach ($allcats as $cat_id => $cat) {

            $value = wpdm_valueof($extras, 'value') === 'slug' ? $cat['category']->slug : $cat_id;
            $category = $cat['category'];
            ?>
            <li class="<?php echo wpdm_valueof($extras, 'liclass'); ?>">
                <label><input type="checkbox" <?php checked(1, in_array($value, $selected)); ?>
                              name="<?php echo $name; ?>[]"
                              class="<?php echo wpdm_valueof($extras, 'cbclass'); ?>"
                              value="<?php echo $value; ?>"> <?php echo $category->name; ?> </label>
                <?php
                if (count($cat['childs']) > 0) {
                    echo "<ul id='wpdmcats-childof-{$cat_id}'>";
                    $this->checkboxList($name, $cat['childs'], $selected, $extras);
                    echo "</ul>";
                }
                ?>
            </li>
            <?php
        }
    }

    function exploreChilds($term, &$allcats)
    {
        $child_ids = get_term_children($term->term_id, 'wpdmcategory');
        if (count($child_ids) > 0) {
            foreach ($child_ids as $child_id) {
                $term = get_term($child_id);
                $allcats[$child_id] = array('category' => $term, 'access' => maybe_unserialize(get_term_meta($child_id, '__wpdm_access')), 'childs' => array());
                $this->exploreChilds($term, $allcats[$child_id]['childs']);
            }
        }
    }

    public function getAllowedRoles($term_id)
    {
        $roles = maybe_unserialize(get_term_meta($term_id, '__wpdm_access', true));
        if (!is_array($roles)) {
            $MetaData = get_option("__wpdmcategory");
            $MetaData = maybe_unserialize($MetaData);

            $roles = maybe_unserialize(get_term_meta($term_id, '__wpdm_access', true));

            if (!is_array($roles))
                $roles = isset($MetaData[$term_id], $MetaData[$term_id]['access']) && is_array($MetaData[$term_id]['access']) ? $MetaData[$term_id]['access'] : array();

            $roles = apply_filters("WPDM_Category_CategoryController_getAllowedRoles", $roles, $term_id);
        }
        foreach ($roles as $index => $role) {
            if (!is_string($roles[$index])) unset($roles[$index]);
        }
        return $roles;
    }

    function parentRoles($cid)
    {
        if (!$cid) return array();
        $roles = array();
        $parents = self::categoryParents($cid, 0);
        $MetaData = get_option("__wpdmcategory");
        $MetaData = maybe_unserialize($MetaData);
        foreach ($parents as $catid) {
            $croles = maybe_unserialize(get_term_meta($catid, '__wpdm_access', true));
            if (!is_array($roles))
                $croles = isset($MetaData[$catid], $MetaData[$catid]['access']) && is_array($MetaData[$catid]['access']) ? $MetaData[$catid]['access'] : array();
            $roles += $croles;
        }
        return array_unique($roles);
    }


    public static function icon($term_id)
    {
        $icon = get_term_meta($term_id, '__wpdm_icon', true);
        if ($icon == '') {
            $MetaData = get_option("__wpdmcategory");
            $MetaData = maybe_unserialize($MetaData);
            $icon = get_term_meta($term_id, '__wpdm_icon', true);
            if ($icon == '')
                $icon = isset($MetaData[$term_id]['icon']) ? $MetaData[$term_id]['icon'] : '';
        }
        return $icon;
    }

    public static function categoryParents($cid, $offset = 1)
    {
        $CategoryBreadcrumb = array();
        if ($cid > 0) {
            $cat = get_term($cid, 'wpdmcategory');
            $parent = $cat->parent;
            $CategoryParents[] = $cat->term_id;
            while ($parent > 0) {
                $cat = get_term($parent, 'wpdmcategory');
                $CategoryParents[] = $cat->term_id;
                $parent = $cat->parent;
            }
            if ($offset)
                array_pop($CategoryBreadcrumb);
            $CategoryParents = array_reverse($CategoryParents);
        }

        return $CategoryParents;

    }

    /**
     * Check if current user has access to the given term
     * @param $term_id
     * @return bool
     */
    public static function userHasAccess($term_id)
    {
        global $current_user;
        $roles = maybe_unserialize(get_term_meta($term_id, '__wpdm_access', true));
        $roles = is_array($roles) ? $roles : array();
        if(in_array('guest', $roles)) return true;
        $user_roles = is_array($current_user->roles) ? $current_user->roles : array();
        $has_role = array_intersect($roles, $user_roles);
        $users = maybe_unserialize(get_term_meta($term_id, '__wpdm_user_access', true));
        $users = is_array($users) ? $users : array();
        if (count($has_role) > 0 || in_array($current_user->user_login, $users)) return true;
        if(count($roles) === 0 && count($users) === 0) return true;
        return false;
    }

    public static function categoryBreadcrumb($cid, $offset = 1)
    {
        $CategoryBreadcrumb = array();
        if ($cid > 0) {
            $cat = get_term($cid, 'wpdmcategory');
            $parent = $cat->parent;
            $CategoryBreadcrumb[] = "<a href='#' class='folder' data-cat='{$cat->term_id}'>{$cat->name}</a>";
            while ($parent > 0) {
                $cat = get_term($parent, 'wpdmcategory');
                $CategoryBreadcrumb[] = "<a href='#' class='folder' data-cat='{$cat->term_id}'>{$cat->name}</a>";
                $parent = $cat->parent;
            }
            if ($offset)
                array_pop($CategoryBreadcrumb);
            $CategoryBreadcrumb = array_reverse($CategoryBreadcrumb);
        }
        echo "<a href='#' class='folder' data-cat='0'>Home</a>&nbsp; <i class='fa fa-angle-right'></i> &nbsp;" . implode("&nbsp; <i class='fa fa-angle-right'></i> &nbsp;", $CategoryBreadcrumb);

    }

    function wpdmproTemplates($template){
        $_template = basename($template);
        $style_global = get_option('__wpdm_cpage_style', 'basic');
        $style = get_term_meta(get_queried_object_id(), '__wpdm_style', true);
        $style = in_array($style, ['basic', 'ltpl']) ? $style : $style_global;
        if($style === 'ltpl' && (is_tax('wpdmcategory') || is_post_type_archive('wpdmpro'))){
            $template = Template::locate("taxonomy-wpdmcategory.php", __DIR__.'/views');
        }
        /*if($_template !== 'single-wpdmpro.php' && is_singular('wpdmpro')){
            $template = Template::locate("single-wpdmpro.php", WPDM_TPL_FALLBACK, WPDM_TPL_FALLBACK);
        }*/
        return $template;
    }



}

