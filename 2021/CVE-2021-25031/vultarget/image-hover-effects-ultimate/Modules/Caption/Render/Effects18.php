<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects18 extends Public_Render {

    public function public_css() {
        wp_enqueue_style('oxi-image-hover-caption', OXI_IMAGE_HOVER_URL . '/Modules/Caption/Files/caption.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi-image-hover-caption-pivot-effects', OXI_IMAGE_HOVER_URL . '/Modules/Caption/Files/pivot-effects.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }

    public function default_render($style, $child, $admin) {

        foreach ($child as $key => $val) {
            $value = json_decode(stripslashes($val['rawdata']), true);
            $button = $content = $text = $hr = $ht='';

            if ($value['image_hover_heading'] != ''):
                $text = '<h3 class="oxi-image-hover-heading ' . $this->style['oxi-image-hover-heading-animation'] . ' ' . $this->style['oxi-image-hover-heading-animation-delay'] . '">' . $this->text_render($value['image_hover_heading']) . '</h3>';
            endif;
            if ($value['image_hover_description'] != ''):
                $content = '<div class="oxi-image-hover-content ' . $this->style['oxi-image-hover-desc-animation'] . ' ' . $this->style['oxi-image-hover-desc-animation-delay'] . '">' . $this->text_render($value['image_hover_description']) . '</div>';
            endif;
            if ($value['image_hover_button_text'] != '' && $this->url_render('image_hover_button_link', $value) != ''):
                $button = '<div class="oxi-image-hover-button ' . $this->style['oxi-image-hover-button-animation'] . ' ' . $this->style['oxi-image-hover-button-animation-delay'] . '">
                            <a ' . $this->url_render('image_hover_button_link', $value) . ' class="oxi-image-btn">' . $this->text_render($value['image_hover_button_text']) . '</a>
                        </div>';
            elseif ($this->url_render('image_hover_button_link', $value) != ''):
                $hr = '<a ' . $this->url_render('image_hover_button_link', $value) . '>';
                $ht = '</a>';
            endif;
            echo '<div class="oxi-image-hover-style ' . $this->column_render('oxi-image-hover-col', $style) . ' ' . ($admin == "admin" ? 'oxi-addons-admin-edit-list' : '') . '" ' . $this->animation_render('oxi-image-hover-animation', $style) . '>';
            echo '  <div class="oxi-image-hover-style-caption">
                        <div class="oxi-image-hover oxi-image-caption-hover oxi-image-caption-hover-style-5 '.$this->style['image_hover_effects'].'">
                            <div class="oxi-image-hover-figure">
                                <div class="oxi-image-hover-image">
                                    <img ' . $this->media_render('image_hover_image', $value) . '>
                                </div>
                                <div class="oxi-image-hover-figure-caption">
                                    <div class="oxi-image-hover-caption-tab ' . $this->style['oxi-image-hover-content-alignment'] . '">
                                        ' . $text . ' 
                                        ' . $content . '
                                        ' . $button . '
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
            if ($admin == 'admin') :
                echo $this->oxi_addons_admin_edit_delete_clone($val['id']);
            endif;
            echo ' </div>';
        }
    }

}
