<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Filter\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects1 extends Public_Render {

    public function public_jquery() {
        wp_enqueue_script('imagesloaded.pkgd.min', OXI_IMAGE_HOVER_URL . '/Modules/Filter/Files/imagesloaded.pkgd.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_script('jquery.isotope.v3.0.2', OXI_IMAGE_HOVER_URL . '/Modules/Filter/Files/jquery.isotope.v3.0.2.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        $this->JSHANDLE = 'jquery.isotope.v3.0.2';
    }

    public function public_css() {
        wp_enqueue_style('oxi-image-hover-filter-style-1', OXI_IMAGE_HOVER_URL . '/Modules/Filter/Files/style-1.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }

    public function inline_public_jquery() {
        $jquery = '';
        $oxiid = $this->oxiid;
        $jquery = ' $(".image-hover-category-data-' . $oxiid . '").imagesLoaded(function(){
                        $(".image-hover-category-data-' . $oxiid . '").isotope({
                            filter: "*",
                            animationOptions: {
                                duration: 750,
                                easing: "linear",
                                queue: false
                            },
                            layoutMode: "masonry",
                        });
                    });
                    $(".image-hover-category-menu-' . $oxiid . ' .image-hover-category-menu-item").on("click", function () {
                        if(!$(this).hasClass("oxi-active")){
                            $(".image-hover-category-menu-' . $oxiid . ' .image-hover-category-menu-item").removeClass("oxi-active");
                            $(this).addClass("oxi-active");
                            var selector = jQuery(this).attr("cat_ref");
                            $(".image-hover-category-data-' . $oxiid . '").isotope({
                                filter: selector, 
                                animationOptions: {
                                    duration: 750, 
                                    easing: "linear",
                                    queue: false
                                }
                            });
                            return false;
                        }
                    });';

        return $jquery;
    }

    public function default_render($style, $child, $admin) {
        $styledata = $this->style;
        $oxiid = $this->oxiid;
        $all_cat_data = (array_key_exists('category_menu_settings', $styledata) && is_array($styledata['category_menu_settings'])) ? $styledata['category_menu_settings'] : [];
        $active_default = '';
        if (array_key_exists('category_parent_cat', $styledata) && $styledata['category_parent_cat'] != '') :
            $active_default = $styledata['category_parent_cat'];
        endif;

        echo '  <div class="image-hover-filter-style image-hover-filter-style-1">
                    <div class="image-hover-category-menu image-hover-category-menu-' . $oxiid . ' ">';
        foreach ($all_cat_data as $value) :
            if ($active_default == $value['category_item_text']) :
                $cat = '*';
                $class = 'oxi_active';
            else :
                $class = '';
                $cat = '.' . $this->CatStringToClassReplacce($value['category_item_text'], $oxiid) . '';
            endif;
            echo '<div class="image-hover-category-menu-item ' . $styledata['category_menu_width_type'] . '  ' . $class . ' " cat_ref="' . $cat . '">
                             ' . $value['category_item_text'] . '
                         </div>
                 ';
        endforeach;
        echo '      </div>';

        echo '      <div class="image-hover-category  image-hover-category-' . $oxiid . '">
                        <div class="image-hover-category-data  image-hover-category-data-' . $oxiid . '">';
        foreach ($child as $value) :
            $childdata = json_decode(stripslashes($value['rawdata']), true);
            $select_cat_data = (array_key_exists('image_hover_category_select', $childdata) && is_array($childdata['image_hover_category_select'])) ? $childdata['image_hover_category_select'] : [];
            $item_cat_list = '';
            foreach ($select_cat_data as $item) :
                $item_cat_list .= $this->CatStringToClassReplacce($item, $oxiid) . ' ';
            endforeach;
            echo '<div class="image-hover-category-item-show  ' . $item_cat_list . '  ' . $this->column_render('category_col', $styledata) . ($admin == "admin" ? '  oxi-addons-admin-edit-list' : '') . '">
                            ' . $this->text_render($childdata['image_hover_info']);
            if ($admin == 'admin'):
                echo $this->oxi_addons_admin_edit_delete_clone($value['id']);
            endif;
            echo '</div>';
        endforeach;
        echo '          </div>
                    </div>
                </div>';
    }

    public function CatStringToClassReplacce($string, $number = '000') {
        $entities = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', "t");
        $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]", " ");
        return 'sa_STCR_' . str_replace($replacements, $entities, urlencode($string)) . $number;
    }

}
