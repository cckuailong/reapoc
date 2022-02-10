<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Comparison\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects2 extends Public_Render
{

    public function public_css()
    {
        wp_enqueue_style('oxi-image-hover-comparison-box', OXI_IMAGE_HOVER_URL . '/Modules/Comparison/Files/Comparison.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi-image-hover-comparison-style-2', OXI_IMAGE_HOVER_URL . '/Modules/Comparison/Files/style-2.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi-addons-main-wrapper-image-comparison-style-2', OXI_IMAGE_HOVER_URL . '/Modules/Comparison/Files/BeerSlider.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }

    public function public_jquery()
    {
        $this->JSHANDLE = 'jquery-BeerSlider';
        wp_enqueue_script('jquery-BeerSlider', OXI_IMAGE_HOVER_URL . '/Modules/Comparison/Files/BeerSlider.js', true, OXI_IMAGE_HOVER_PLUGIN_VERSION);
     }
  

    public function default_render($style, $child, $admin)
    {
    foreach ($child as $key => $val) {
            $data = json_decode(stripslashes($val['rawdata']), true);  
            $imageone = $imagetwo = $before = $after ='';
            if ($style['oxi_image_compersion_button_controler'] == 'true') {
                $before  = 'data-beer-label="' . $this->text_render($style['oxi_image_comparison_before_text']) . '"';
                $after  = 'data-beer-label="' . $this->text_render($style['oxi_image_comparison_after_text']) . '"';
            }

            if ($this->media_render('oxi_image_comparison_image_one', $data) != '') { 
                $imageone = '<img ' . $this->media_render('oxi_image_comparison_image_one', $data) . '>';
            }
            if ($this->media_render('oxi_image_comparison_image_two', $data) != '') {
                $imagetwo = '<div class="beer-reveal" ' . $after . '>
                    <img ' . $this->media_render('oxi_image_comparison_image_two', $data) . '/>
                </div>';
            }
           
            echo '<div class="oxi-addons-main-wrapper-image-comparison-style-2 oxi-addons-main-wrapper-image-comparison ' . $this->column_render('oxi-image-hover-col', $style) . ' ' . ($admin == "admin" ? 'oxi-addons-admin-edit-list' : '') . '">
                    <div class="oxi-addons-main '.$style['oxi_image_magnifier_image_switcher'].'">
                        <div id="oxi-addons-comparison-' . $this->oxiid . '_' . $key . '" class="beer-slider" ' . $before . '>
                            ' . $imageone . '
                            ' . $imagetwo . '
                         </div>
                    </div>';
            if ($admin == 'admin'):
                echo $this->oxi_addons_admin_edit_delete_clone($val['id']);
            endif;
            echo '</div>';
        } 
    }

    public function inline_public_jquery()
    {
        $styledata = $this->style;
        $child = $this->child;
        $jquery = '';
        foreach ($child as $key => $val) {
            $data = json_decode(stripslashes($val['rawdata']), true);
            $jquery .= ' 
            $.fn.BeerSlider = function ( options ) {
                        options = options || {};
                        return this.each(function() {
                        new BeerSlider(this, options);
                        });
                    };
                   $("#oxi-addons-comparison-' . $this->oxiid . '_' . $key . '").BeerSlider({
                       start: ' . ($data['oxi_image_comparison_body_offset-size'] ? $data['oxi_image_comparison_body_offset-size'] : 50). '
                    });';
        }
        return $jquery;

    }

}
