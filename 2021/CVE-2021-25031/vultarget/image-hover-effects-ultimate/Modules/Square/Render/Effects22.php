<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Square\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects22 extends Public_Render
{

    public function public_css()
    {
        wp_enqueue_style('oxi-image-hover-square', OXI_IMAGE_HOVER_URL . '/Modules/Square/Files/square.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi-image-hover-square-style-22', OXI_IMAGE_HOVER_URL . '/Modules/Square/Files/style-22.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }

    public function default_render($style, $child, $admin)
    {

        foreach ($child as $key => $val) {
            $value = json_decode(stripslashes($val['rawdata']), true);
            $text = $content = $button = $hr = $ht = '';
            if ($value['image_hover_heading'] != '') :
                $text = '<div class="oxi-image-hover-figure-heading ' . $this->style['oxi-image-hover-heading-animation'] . ' ' . $this->style['oxi-image-hover-heading-animation-delay'] . '"><h3 class="oxi-image-hover-heading">' . $this->text_render($value['image_hover_heading']) . '</h3></div>';
            endif;

            if ($this->url_render('image_hover_button_link', $value) != '') :
                $hr = '<a ' . $this->url_render('image_hover_button_link', $value) . '>';
                $ht = '</a>';
            endif;

            echo '<div class="oxi-image-hover-style oxi-image-hover-style-22-square ' . $this->column_render('oxi-image-hover-col', $style) . ' ' . ($admin == "admin" ? 'oxi-addons-admin-edit-list' : '') . '" ' . $this->animation_render('oxi-image-hover-animation', $style) . '>';
            echo '  <div class="oxi-image-hover-style-square">
                        <div class="oxi-image-hover oxi-image-square-hover oxi-image-square-hover-style-22 oxi-image-square-hover-' . $this->oxiid . '-' . $val['id'] . '">
                            ' . $hr . '
                            <div class="oxi-image-hover-figure ' . $this->style['image_hover_effects'] . '">
                                <div class="oxi-image-hover-image">
                                    <img ' . $this->media_render('image_hover_image', $value) . '>
                                </div>
                                <div class="oxi-image-hover-figure-caption">
                                    <div class="oxi-image-hover-caption-tab">
                                        ' . $text . '
                                    </div>
                                </div>
                            </div>
                            ' . $ht . '
                        </div>
                    </div>';
            if ($admin == 'admin') :
               echo $this->oxi_addons_admin_edit_delete_clone($val['id']);
            endif;
            echo ' </div>';
        }
    }
}