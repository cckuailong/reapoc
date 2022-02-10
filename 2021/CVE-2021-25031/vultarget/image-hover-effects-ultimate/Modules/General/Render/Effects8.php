<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\General\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects8 extends Public_Render {

    public function public_css() {
        wp_enqueue_style('oxi-image-hover-general', OXI_IMAGE_HOVER_URL . '/Modules/General/Files/general.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi-image-hover-general-style-8', OXI_IMAGE_HOVER_URL . '/Modules/General/Files/style-8.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }
public function default_render($style, $child, $admin) {

        foreach ($child as $key => $val) {
            $value = json_decode(stripslashes($val['rawdata']), true);
            $text = $content = $button = $hr = $ht = '';
            if ($value['image_hover_heading'] != ''):
                $text = '<h3 class="oxi-image-hover-heading ' . $this->style['oxi-image-hover-heading-animation'] . ' ' . $this->style['oxi-image-hover-heading-animation-delay'] . ' ' . (isset($this->style['oxi-image-hover-heading-underline']) ? $this->style['oxi-image-hover-heading-underline'] : '') . '">' . $this->text_render($value['image_hover_heading']) . '</h3>';
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
            echo '  <div class="oxi-image-hover-style-general">
                        <div class="oxi-image-hover oxi-image-general-hover oxi-image-general-hover-style-8 oxi-image-general-hover-' . $this->oxiid . '-' . $val['id'] . '">
                            ' . $hr . '
                            <div class="oxi-image-hover-figure ' . $this->style['image_hover_effects'] . '">
                                <div class="oxi-image-hover-image-2">
                                    <div class="oxi-image-hover-image">
                                        <img ' . $this->media_render('image_hover_image', $value) . '>
                                    </div>
                                </div>
                               
                                <div class="oxi-image-hover-figure-caption-2">
                                    <div class="oxi-image-hover-figure-caption">
                                        <div class="oxi-image-hover-caption-tab ' . $this->style['oxi-image-hover-content-alignment'] . '">
                                            ' . $text . ' 
                                            ' . $content . ' 
                                            ' . $button . '
                                        </div>
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
            if ($this->media_background_render('image_hover_feature_image', $value) != ''):
                $url = $this->media_background_render('image_hover_feature_image', $value);
                $this->inline_css .= ' .oxi-image-hover-style-general .oxi-image-general-hover-' . $this->oxiid . '-' . $val['id'] . ' .oxi-image-hover-figure-caption:after{background: url(' . $url . ');-moz-background-size: 100% 100% !important;-o-background-size: 100% 100% !important; background-size: 100% 100% !important;}';
            endif;
        }
    }
   
    public function old_render() {
        $style = $this->dbdata['css'];
        $styledata = explode('|', $style);
        foreach ($this->child as $k => $value) {
            $rowdata = [
                'image_hover_heading' => $value['title'],
                'image_hover_description' => $value['files'],
                'image_hover_image-select' => 'media-library',
                'image_hover_image-image' => $value['image'],
                'image_hover_feature_image-select' => 'media-library',
                'image_hover_feature_image-image' => $value['hoverimage'],
                'image_hover_button_link-url' => $value['link'],
                'image_hover_button_link-target' => $styledata[17] == '_blank' ? 'yes' : '',
                'image_hover_button_text' => $value['buttom_text'],
            ];
            $dd = json_encode($rowdata);
            $this->wpdb->query($this->wpdb->prepare("UPDATE {$this->child_table} SET rawdata = %s WHERE id = %d", $dd, $value['id']));
        }

        $new = [
            'image-hover-style-id' => $this->oxiid,
             'oxi-image-hover-effects-time-size' => '350',
            'oxi-image-hover-effects-time-choices' => 'ms',
            'oxi-addons-elements-template' => $this->dbdata['style_name'],
            'oxi-image-hover-col-lap' => $this->old_column_render($styledata[1], 'lap'),
            'oxi-image-hover-col-tab' => $this->old_column_render($styledata[1], 'tab'),
            'oxi-image-hover-col-mob' => $this->old_column_render($styledata[1], 'mob'),
            'oxi-image-hover-border-radius-lap-top' => $styledata[3],
            'oxi-image-hover-border-radius-lap-right' => $styledata[3],
            'oxi-image-hover-border-radius-lap-bottom' => $styledata[3],
            'oxi-image-hover-border-radius-lap-left' => $styledata[3],
            'oxi-image-hover-border-radius-lap-choices' => '%',
            'oxi-image-hover-width-lap-choices' => 'px',
            'oxi-image-hover-width-lap-size' => $styledata[5],
            'oxi-image-hover-height-lap-choices' => '%',
            'oxi-image-hover-height-lap-size' => ($styledata[7]/ $styledata[5] *100),
            'oxi-image-hover-margin-lap-top' => $styledata[9],
            'oxi-image-hover-margin-lap-right' => $styledata[9],
            'oxi-image-hover-margin-lap-bottom' => $styledata[9],
            'oxi-image-hover-margin-lap-left' => $styledata[9],
            'oxi-image-hover-margin-lap-choices' => 'px',
            /////
            'oxi-image-hover-padding-lap-top' => $styledata[11],
            'oxi-image-hover-padding-lap-right' => $styledata[11],
            'oxi-image-hover-padding-lap-bottom' => $styledata[11],
            'oxi-image-hover-padding-lap-left' => $styledata[11],
            'oxi-image-hover-padding-lap-choices' => 'px',
            //////
            'oxi-image-hover-background-color' => $styledata[13],
            'oxi-image-hover-background-img' => '',
            'oxi-image-hover-content-alignment' => $this->old_alignment_render($styledata[15]),
            'oxi-image-hover-animation-type' => $styledata[19],
            'oxi-image-hover-animation-duration-size' => $styledata[21] * 1000,
            'oxi-image-hover-desc-animation' => $styledata[23],
            'oxi-image-hover-heading-animation' => $styledata[23],
            'oxi-image-hover-button-animation' => $styledata[23],
            'oxi-image-hover-heading-animation-delay' => '',
            'oxi-image-hover-desc-animation-delay' => '',
            'oxi-image-hover-button-animation-delay' => '',
            'oxi-image-hover-boxshadow-shadow' => 'yes',
            'oxi-image-hover-boxshadow-type' => 'inset',
            'oxi-image-hover-boxshadow-horizontal-size' => 0,
            'oxi-image-hover-boxshadow-vertical-size' => 0,
            'oxi-image-hover-boxshadow-blur-size' => 0,
            'oxi-image-hover-boxshadow-spread-size' => $styledata[25],
            'oxi-image-hover-boxshadow-color' => $styledata[27],
            //////
            'oxi-image-hover-heading-typho-size-lap-size' => $styledata[33],
            'oxi-image-hover-heading-typho-size-lap-choices' => 'px',
            'oxi-image-hover-heading-color' => $styledata[35],
            'oxi-image-hover-heading-typho-font' => $styledata[37],
            'oxi-image-hover-heading-typho-weight' => $styledata[39],
            'oxi-image-hover-heading-underline' => ($styledata[41] == 'no' ? '' : 'oxi-image-hover-heading-underline'),
            'oxi-image-hover-heading-margin-lap-top' => 0,
            'oxi-image-hover-heading-margin-lap-right' => 0,
            'oxi-image-hover-heading-margin-lap-bottom' => $styledata[45],
            'oxi-image-hover-heading-margin-lap-left' => 0,
            'oxi-image-hover-heading-margin-lap-choices' => 'px',
            'oxi-image-hover-underline-color' => $styledata[35],
            'oxi-image-hover-underline-distance-lap-choices' => 'px',
            'oxi-image-hover-underline-distance-lap-size' => $styledata[43],
            /////
            'oxi-image-hover-desc-typho-size-lap-size' => $styledata[47],
            'oxi-image-hover-desc-typho-size-lap-choices' => 'px',
            'oxi-image-hover-desc-color' => $styledata[49],
            'oxi-image-hover-desc-typho-font' => $styledata[51],
            'oxi-image-hover-desc-typho-weight' => $styledata[53],
            'oxi-image-hover-desc-margin-lap-top' => 0,
            'oxi-image-hover-desc-margin-lap-right' => 0,
            'oxi-image-hover-desc-margin-lap-bottom' => $styledata[55],
            'oxi-image-hover-desc-margin-lap-left' => 0,
            'oxi-image-hover-desc-margin-lap-choices' => 'px',
            ////
            'oxi-image-hover-button-typho-size-lap-size' => $styledata[57],
            'oxi-image-hover-button-typho-size-lap-choices' => 'px',
            'oxi-image-hover-button-color' => $styledata[59],
            'oxi-image-hover-button-background' => $styledata[61],
            'oxi-image-hover-button-typho-font' => $styledata[63],
            'oxi-image-hover-button-typho-weight' => $styledata[65],
            'oxi-image-hover-button-hover-color' => $styledata[67],
            'oxi-image-hover-button-hover-background' => $styledata[69],
            ///
            'oxi-image-hover-button-radius-lap-top' => $styledata[71],
            'oxi-image-hover-button-radius-lap-right' => $styledata[71],
            'oxi-image-hover-button-radius-lap-bottom' => $styledata[71],
            'oxi-image-hover-button-radius-lap-left' => $styledata[71],
            'oxi-image-hover-button-radius-lap-choices' => 'px',
            ///
            'oxi-image-hover-button-padding-lap-top' => $styledata[73],
            'oxi-image-hover-button-padding-lap-right' => $styledata[75],
            'oxi-image-hover-button-padding-lap-bottom' => $styledata[73],
            'oxi-image-hover-button-padding-lap-left' => $styledata[75],
            'oxi-image-hover-button-padding-lap-choices' => 'px',
            'oxi-image-hover-button-position' => $this->old_button_alignment_render($styledata[77]),
            ///
            'oxi-image-hover-button-margin-lap-top' => $styledata[79],
            'oxi-image-hover-button-margin-lap-right' => $styledata[81],
            'oxi-image-hover-button-margin-lap-bottom' => $styledata[79],
            'oxi-image-hover-button-margin-lap-left' => $styledata[81],
            'oxi-image-hover-button-margin-lap-choices' => 'px',
            ///
            'image-hover-custom-css' => $styledata[83],
            'image_hover_effects' => $styledata[85]
        ];
        $row = json_encode($new);
        $this->wpdb->query($this->wpdb->prepare("UPDATE {$this->parent_table} SET rawdata = %s WHERE id = %d", $row, $this->oxiid));
        $name = explode('-', $this->dbdata['style_name']);
        $cls = '\OXI_IMAGE_HOVER_PLUGINS\Modules\General\Admin\Effects' . $name[1];
        $CLASS = new $cls('admin');
        $CLASS->template_css_render($new);
    }

}
