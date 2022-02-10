<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Magnifier\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects1 extends Public_Render {

    public function public_css() {
        wp_enqueue_style('oxi-image-hover-light-box', OXI_IMAGE_HOVER_URL . '/Modules/Magnifier/Files/Magnifier.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi-image-hover-light-style-1', OXI_IMAGE_HOVER_URL . '/Modules/Magnifier/Files/style-1.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('image_zoom.css', OXI_IMAGE_HOVER_URL . '/Modules/Magnifier/Files/image_zoom.min.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }


    public function public_jquery()
    {
        wp_enqueue_script('image_zoom', OXI_IMAGE_HOVER_URL . '/Modules/Magnifier/Files/image_zoom.min.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        $this->JSHANDLE = 'image_zoom';
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
            $data = json_decode(stripslashes($val['rawdata']), true);       
            $image = ''; 
            if ($this->custom_media_render('oxi_image_magnifier_img', $data) != '') {
                $image = '<img class="oxi_addons__image   oxi__image_' . $this->oxiid . '_'.$key.' ' . $style['oxi_image_magnifier_image_switcher'] . '  ' . $style['oxi_image_magnifier_grayscale_switter'] . '  " src="' . $this->custom_media_render('oxi_image_magnifier_img', $data) . '" alt="slider image"/>';
            }
            echo ' <div class="oxi_addons__image_magnifier_column ' . $this->column_render('oxi-image-hover-col', $style) . ' ' . ($admin == "admin" ? 'oxi-addons-admin-edit-list' : '') . '" > 
                        <div class="oxi_addons__image_magnifier_style_body ' . $style['oxi_image_magnifier_image_switcher'] . ' " >
                            <div class="oxi_addons__image_magnifier_style_1 oxi_addons__image_magnifier  " >
                               ' . $image . '
                           </div>
                       </div>';
                if ($admin == 'admin') :
                    echo $this->oxi_addons_admin_edit_delete_clone($val['id']);
                endif;
            echo '</div>';
        } 
    }

    public function inline_public_jquery()
    {
        $style = $this->style;
        $child = $this->child;
        $width = $height =   '';
        $jquery = '';
        if (array_key_exists('oxi_image_magnifier_magnifi_switcher', $style) && $style['oxi_image_magnifier_magnifi_switcher'] == 'yes') {
            $width = '' . ($style['oxi_image_magnifier_magnifi_width-size'] != '') ? 'width: ' . $style['oxi_image_magnifier_magnifi_width-size'] . ',' : '' . '';
            $height = '' . ($style['oxi_image_magnifier_magnifi_height-size'] != '') ? 'height: ' . $style['oxi_image_magnifier_magnifi_height-size'] . ',' : '' . '';
        } 
       
        $zoom = (array_key_exists('oxi_image_magnifier_magnifi_zoom-size', $style) && $style['oxi_image_magnifier_magnifi_zoom-size'] != '') ? 'maxZoom: ' . $style['oxi_image_magnifier_magnifi_zoom-size'] . '' : 'maxZoom: 2,';
 
        foreach ($child as $key => $val) {
            $data = json_decode(stripslashes($val['rawdata']), true);   
             $position = ''; 
             if (array_key_exists('oxi_image_magnifier_magnifi_position', $data) && $data['oxi_image_magnifier_magnifi_position'] == 'top') {
                $position = '' . ($data['oxi_image_magnifier_magnifi_position_top-size'] != '') ? 'top: ' . $data['oxi_image_magnifier_magnifi_position_top-size'] . ',' : 'top:10,' . '';
            } elseif (array_key_exists('oxi_image_magnifier_magnifi_position', $data) && $data['oxi_image_magnifier_magnifi_position'] == 'right') {
                $position = '' . ($data['oxi_image_magnifier_magnifi_position_right-size'] != '') ? 'right: ' . $data['oxi_image_magnifier_magnifi_position_right-size'] . ',' : 'right:10,' . '';
            } elseif (array_key_exists('oxi_image_magnifier_magnifi_position', $data) && $data['oxi_image_magnifier_magnifi_position'] == 'bottom') {
                $position = '' . ($data['oxi_image_magnifier_magnifi_position_bottom-size'] != '') ? 'bottom: ' . $data['oxi_image_magnifier_magnifi_position_bottom-size'] . ',' : 'bottom:10,' . '';
            } elseif (array_key_exists('oxi_image_magnifier_magnifi_position', $data) && $data['oxi_image_magnifier_magnifi_position'] == 'left') {
                $position = '' . ($data['oxi_image_magnifier_magnifi_position_left-size'] != '') ? 'left: ' . $data['oxi_image_magnifier_magnifi_position_left-size'] . ',' : 'left:10,' . '';
            }
            $jquery .= '    new ImageZoom(".oxi__image_' . $this->oxiid . '_'.$key.'", {
                deadarea: 0.25,
                target: {
                    ' . $position . ' 
                    ' . $width . ' 
                    ' . $height . ' 
                },
                ' . $zoom . '
            });';  
        } 
        return $jquery;

    }

}
