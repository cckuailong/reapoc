<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Magnifier\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects2 extends Public_Render {

    public function public_css() {
        wp_enqueue_style('oxi-image-hover-light-box', OXI_IMAGE_HOVER_URL . '/Modules/Magnifier/Files/Magnifier.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi-image-hover-light-style-1', OXI_IMAGE_HOVER_URL . '/Modules/Magnifier/Files/style-2.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('zoomple.css', OXI_IMAGE_HOVER_URL . '/Modules/Magnifier/Files/styles/zoomple.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }


    public function public_jquery()
    {
        $this->JSHANDLE = 'zoomple.js';
        wp_enqueue_script('zoomple.js', OXI_IMAGE_HOVER_URL . '/Modules/Magnifier/Files/zoomple.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
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
                $image = '<div class="oxi__image_body oxi__image_' . $this->oxiid . '_' . $key . ' " href="' . $this->custom_media_render('oxi_image_magnifier_img', $data ) . '">
                            <img class="oxi_addons__image ' . $style['oxi_image_magnifier_image_switcher'] . '  ' . $style['oxi_image_magnifier_grayscale_switter'] . '  " src="' . $this->custom_media_render('oxi_image_magnifier_img', $data) . '" alt=""/>
                        </div>';
            }

            echo ' <div class="oxi_addons__image_magnifier_column ' . $this->column_render('oxi-image-hover-col', $style) . ' ' . ($admin == "admin" ? 'oxi-addons-admin-edit-list' : '') . '" > 
                        <div class="oxi_addons__image_magnifier_style_body ' . $style['oxi_image_magnifier_image_switcher'] . ' " >
                            <div class="oxi_addons__image_magnifier_style_2 oxi_addons__image_magnifier" >
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
        $width = 'zoomWidth:200,';
        $height = 'zoomHeight:200,';
        $offset = 'offset: {x: -150, y: -150},';
        $rounded = '';
        $jquery = '';
        if (array_key_exists('oxi_image_magnifier_magnifi_switcher', $style) && $style['oxi_image_magnifier_magnifi_switcher'] == 'yes') {
            $width = '' . ($style['oxi_image_magnifier_magnifi_width-size'] != '') ? 'zoomWidth: ' . $style['oxi_image_magnifier_magnifi_width-size'] . ',' : 'zoomWidth:200 ' . ',';
            $height = '' . ($style['oxi_image_magnifier_magnifi_height-size'] != '') ? 'zoomHeight: ' . $style['oxi_image_magnifier_magnifi_height-size'] . ',' : 'zoomHeight:200 ' . ',';
        }
        if (array_key_exists('oxi_image_magnifier_magnifi_offset_switcher', $style) && $style['oxi_image_magnifier_magnifi_offset_switcher'] == 'yes') {
            $offset = 'offset : {x: ' . ($style['oxi_image_magnifier_offset_x-size'] != '' ? $style['oxi_image_magnifier_offset_x-size'] : '-100') . ',y: ' . ($style['oxi_image_magnifier_offset_y-size'] != '' ? $style['oxi_image_magnifier_offset_y-size'] : '-100') . '},';
        }
        if (array_key_exists('oxi_image_magnifier_magnifi_router_switcher', $style) && $style['oxi_image_magnifier_magnifi_router_switcher'] == 'yes') {
            $rounded = 'roundedCorners :true,';
        }
        foreach ($child as $key => $val) {
            $data = json_decode(stripslashes($val['rawdata']), true);   
            $jquery .= ' $(".oxi__image_' . $this->oxiid . '_' . $key . '").zoomple({
                blankURL : "' . OXI_IMAGE_HOVER_URL . '/Modules/Magnifier/Files/images/blank.gif' . '",
                bgColor :"#fff",
                loaderURL : "' . OXI_IMAGE_HOVER_URL . '/Modules/Magnifier/Files/images/loader.gif' . '",
                showCursor:false,
                ' . $width . '
                ' . $height . '
                ' . $offset . '
                ' . $rounded . '
              });  ';
        } 
        $jquery .= '
        jQuery("#zoomple_previewholder").addClass("oxi_addons_magnifier_' . $this->oxiid . '");
        jQuery("#zoomple_previewholder").addClass("oxi_addons_magnifier_previewholder_style_2");
    ';
    return $jquery;
    }

}
