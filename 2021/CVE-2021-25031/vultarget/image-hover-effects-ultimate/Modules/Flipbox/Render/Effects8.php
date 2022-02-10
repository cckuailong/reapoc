<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Flipbox\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects8 extends Public_Render {

    public function public_css() {
        wp_enqueue_style('oxi-image-hover-flipbox', OXI_IMAGE_HOVER_URL . '/Modules/Flipbox/Files/flipbox.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }

    public function default_render($style, $child, $admin) {

        foreach ($child as $key => $val) {
            $value = json_decode(stripslashes($val['rawdata']), true);
            $frontheading = $backheading = $frontcontent = $backcontent = $fronticon = $backicon = $button = $hr = $ht = '';

            if ($value['image_hover_front_heading'] != ''):
                $frontheading = '<div class="oxi-image-hover-heading ' . $style['oxi-image-flip-front-heading-underline'] . '">' . $this->text_render($value['image_hover_front_heading']) . '</div>';
            endif;
            if ($value['image_hover_back_heading'] != ''):
                $backheading = '<div class="oxi-image-hover-heading ' . $style['oxi-image-flip-back-heading-underline'] . '' . $this->style['oxi-image-flip-back-heading-animation'] . ' ' . $this->style['oxi-image-flip-back-animation-delay'] . '">' . $this->text_render($value['image_hover_back_heading']) . '</div>';
            endif;

//            if ($value['image_hover_front_description'] != ''):
//                $frontcontent = '<div class="oxi-image-hover-content">' . $this->text_render($value['image_hover_front_description']) . '</div>';
//            endif;

            if ($value['image_hover_back_description'] != ''):
                $backcontent = '<div class="oxi-image-hover-content ' . $this->style['oxi-image-flip-back-desc-animation'] . ' ' . $this->style['oxi-image-flip-back-desc-animation-delay'] . '">' . $this->text_render($value['image_hover_back_description']) . '</div>';
            endif;

            if ($value['image_hover_front_icon'] != ''):
                $fronticon = '<div class="oxi-image-hover-icon">' . $this->font_awesome_render($value['image_hover_front_icon']) . '</div>';
            endif;

//            if ($value['image_hover_back_icon'] != ''):
//                $backicon = '<div class="oxi-image-hover-icon ' . $this->style['oxi-image-flip-back-icon-animation'] . ' ' . $this->style['oxi-image-flip-back-icon-animation-delay'] . '">' . $this->font_awesome_render($value['image_hover_back_icon']) . '</div>';
//            endif;
//            if ($this->media_render('image_hover_front_image', $value) != ''):
//                $image = ' <img ' . $this->media_render('image_hover_front_image', $value) . '>';
//            endif;


            if ($value['image_hover_button_text'] != '' && $this->url_render('image_hover_button_link', $value) != ''):
                $button = '<div class="oxi-image-hover-button ' . $this->style['oxi-image-flip-back-button-animation'] . ' ' . $this->style['oxi-image-flip-back-button-animation-delay'] . '">
                            <a ' . $this->url_render('image_hover_button_link', $value) . ' class="oxi-image-btn">' . $this->text_render($value['image_hover_button_text']) . '</a>
                        </div>';
            elseif ($this->url_render('image_hover_button_link', $value) != ''):
                $hr = '<a ' . $this->url_render('image_hover_button_link', $value) . '>';
                $ht = '</a>';
            endif;



            echo '  <div class="oxi-image-hover-style ' . $this->column_render('oxi-image-hover-col', $style) . ' ' . ($admin == "admin" ? 'oxi-addons-admin-edit-list' : '') . '"  ' . $this->animation_render('oxi-image-hover-animation', $style) . '>
                        <div class="oxi-image-hover-style-flipbox">
                            ' . $hr . '
                                <div class="oxi-image-hover oxi-image-flipbox-hover oxi-image-flipbox-hover-style-8 oxi-image-flipbox-hover-' . $this->oxiid . '-' . $val['id'] . '">
                                    <div class="oxi-image-hover-figure ' . $style['image_hover_effects'] . ' ' . $style['image_hover_timing_type'] . '">
                                        <div class="oxi-image-hover-figure-frontend">
                                            <div class="oxi-image-hover-figure-front-section ' . $this->style['oxi-image-flip-front-alignment'] . '">
                                                 ' . $fronticon . $frontheading . '  
                                            </div>
                                        </div>
                                        <div class="oxi-image-hover-figure-backend">
                                            <div class="oxi-image-hover-figure-back-section ' . $this->style['oxi-image-flip-back-content-alignment'] . '">
                                                   ' . $backheading . $backcontent . $button . '
                                            </div>
                                        </div>
                                    </div>
                                </div>
                           ' . $ht . '
                        </div>';
            if ($admin == 'admin') :
                echo $this->oxi_addons_admin_edit_delete_clone($val['id']);
            endif;

            if ($this->media_background_render('image_hover_front_image', $value) != ''):
                $url = $this->media_background_render('image_hover_front_image', $value);
                $this->inline_css .= '.' . $this->WRAPPER . ' .oxi-image-flipbox-hover-' . $this->oxiid . '-' . $val['id'] . ' .oxi-image-hover-figure-frontend:after{background: url(' . $url . ');-moz-background-size: 100% 100% !important;-o-background-size: 100% 100% !important; background-size: 100% 100% !important;}';
            endif;
            if ($this->media_background_render('image_hover_back_image', $value) != ''):
                $url = $this->media_background_render('image_hover_back_image', $value);
                $this->inline_css .= '.' . $this->WRAPPER . ' .oxi-image-flipbox-hover-' . $this->oxiid . '-' . $val['id'] . ' .oxi-image-hover-figure-backend:after{background: url(' . $url . ');-moz-background-size: 100% 100% !important;-o-background-size: 100% 100% !important; background-size: 100% 100% !important;}';
            endif;
            echo ' </div>';
        }
    }

}
