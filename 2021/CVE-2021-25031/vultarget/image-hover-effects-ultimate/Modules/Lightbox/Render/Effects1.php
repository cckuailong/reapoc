<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Lightbox\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects1 extends Public_Render {

    public function public_css() {
        wp_enqueue_style('oxi-image-hover-light-box', OXI_IMAGE_HOVER_URL . '/Modules/Lightbox/Files/Lightbox.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi-image-hover-light-style-1', OXI_IMAGE_HOVER_URL . '/Modules/Lightbox/Files/style-1.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi_addons__light_box_style_1', OXI_IMAGE_HOVER_URL . '/Modules/Lightbox/Files/lightgallery.min.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }

    public function public_jquery() {
        wp_enqueue_script('oxi_addons__light_box_picturefill', OXI_IMAGE_HOVER_URL . '/Modules/Lightbox/Files/picturefill.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        $this->JSHANDLE = 'oxi_addons__light_box_picturefill';
        wp_enqueue_script('oxi_addons__light_box_lightgallery_all', OXI_IMAGE_HOVER_URL . '/Modules/Lightbox/Files/lightgallery_all.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        $this->JSHANDLE = 'oxi_addons__light_box_lightgallery_all';
        wp_enqueue_script('oxi_addons__light_box_mousewheel', OXI_IMAGE_HOVER_URL . '/Modules/Lightbox/Files/jquery.mousewheel.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        $this->JSHANDLE = 'oxi_addons__light_box_mousewheel';
    }

    /*
     * Shortcode Addons Media Render.
     * image
     * @since 2.1.0
     */

    public function custom_media_render($id, $style) {
        $url = '';
        if (array_key_exists($id . '-select', $style)):
            if ($style[$id . '-select'] == 'media-library'):
                return $style[$id . '-image'];
            else:
                return $style[$id . '-url'];
            endif;
        endif;
    }

    public function default_render($style, $child, $admin) {

        foreach ($child as $key => $val) {
            $value = json_decode(stripslashes($val['rawdata']), true);
            $heading = $details = $button = $image = $light_box = $image_or_btn = $icon = '';
            if (array_key_exists('oxi_image_light_box_title', $value) && $value['oxi_image_light_box_title'] != '') {
                $heading = '<' . $style['oxi_image_light_box_tag'] . ' class=\'oxi_addons__heading\'>' . $this->text_render($value['oxi_image_light_box_title']) . '</' . $style['oxi_image_light_box_tag'] . '>';
            }
            if (array_key_exists('oxi_image_light_box_desc', $value) && $value['oxi_image_light_box_desc'] != '') {
                $details = '<div class=\'oxi_addons__details\'>' . $this->text_render($value['oxi_image_light_box_desc']) . ' </div>';
            }
            if (array_key_exists('oxi_image_light_box_button_text', $value) && $value['oxi_image_light_box_button_text'] != '') {
                $button = '<div class="oxi_addons__button_main">
                    <button class="oxi_addons__button">
                        ' . $this->text_render($value['oxi_image_light_box_button_text']) . '
                    </button>
                </div>';
            }
            if ($this->custom_media_render('oxi_image_light_box_image_front', $value) != '') {
                $image = '<div  class="oxi_addons__image_main ' . $style['oxi_image_light_box_custom_width_height_swither'] . '" style="background-image: url(\'' . $this->custom_media_render('oxi_image_light_box_image_front', $value) . '\');" >
                    <div class="oxi_addons__overlay">
                    ' . $this->font_awesome_render($style['oxi_image_light_box_bg_overlay_icon']) . '
                    </div>
                </div>';
            }
            if ($value['oxi_image_light_box_button_icon'] != '') {
                $icon = '<div  class="oxi_addons__icon" >
                    <div class="oxi_addons__overlay">
                        ' . $this->font_awesome_render($style['oxi_image_light_box_bg_overlay_icon_icon']) . '
                    </div>
                        ' . $this->font_awesome_render($value['oxi_image_light_box_button_icon']) . '
                </div>';
            }
            if (array_key_exists('oxi_image_light_box_clickable', $style) && $style['oxi_image_light_box_clickable'] == 'button') {
                $image_or_btn = $button;
            } elseif (array_key_exists('oxi_image_light_box_clickable', $style) && $style['oxi_image_light_box_clickable'] == 'image') {
                $image_or_btn = $image;
            } else {
                $image_or_btn = $icon;
            }
            if ($value['oxi_image_light_box_select_type'] == 'image') {
                if ($this->custom_media_render('oxi_image_light_box_image', $value) != '') {
                    $light_box = '<div class="oxi_addons__light_box_item" ' . ((array_key_exists('oxi_image_light_box_clickable', $style) && $style['oxi_image_light_box_clickable'] == 'image') ? 'style="width: 100%"' : '') . '  data-src="' . $this->custom_media_render('oxi_image_light_box_image', $value) . '"  data-sub-html="' . $heading . ' <br> ' . $details . '">
                          ' . $image_or_btn . '
                    </div>';
                }
            } else {
                $light_box = '<a class="oxi_addons__light_box_item" data-src="' . $value['oxi_image_light_box_video'] . '" data-sub-html="' . $heading . ' <br> ' . $details . '">
                    ' . $image_or_btn . '
                </a>';
            }

            echo '<div class="oxi_addons__light_box_style_1 oxi_addons__light_box ' . $this->column_render('oxi-image-hover-col', $style) . '  ' . ($admin == "admin" ? 'oxi-addons-admin-edit-list' : '') . ' ">
                    <div class="oxi_addons__light_box_parent oxi_addons__light_box_parent-' . $this->oxiid . '-' . $key . '">
                        ' . $light_box . '
                    </div>';
            if ($admin == 'admin'):
                echo $this->oxi_addons_admin_edit_delete_clone($val['id']);
            endif;
            echo '</div>';
        }
    }

    public function inline_public_jquery() {
        $jquery = '';
        $child = $this->child;
        foreach ($child as $key => $val) {
            $value = json_decode(stripslashes($val['rawdata']), true);
            $jquery .= 'jQuery(".' . $this->WRAPPER . ' .oxi_addons__light_box_parent-' . $this->oxiid . '-' . $key . '").lightGallery({
                share: false,
                addClass: "oxi_addons_light_box_overlay_' . $this->oxiid . '"
            });';
        }

        return $jquery;
    }

}
