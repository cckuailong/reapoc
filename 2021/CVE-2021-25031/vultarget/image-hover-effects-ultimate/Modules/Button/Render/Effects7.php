<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Button\Render;

if (!defined('ABSPATH')) {
    exit;
}

use OXI_IMAGE_HOVER_PLUGINS\Page\Public_Render;

class Effects7 extends Public_Render
{

    public function public_css()
    {
        wp_enqueue_style('oxi-image-hover-button', OXI_IMAGE_HOVER_URL . '/Modules/Button/Files/button.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_enqueue_style('oxi-image-hover-button-style-7', OXI_IMAGE_HOVER_URL . '/Modules/Button/Files/style-7.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
    }
    public function default_render($style, $child, $admin)
    {

        foreach ($child as $key => $val) {
            $value = json_decode(stripslashes($val['rawdata']), true);
            $first = $second = '';

            if ($value['image_hover_first_icon'] != '') :
                $first = '<div class="oxi-image-hover-icon">
                            <a ' . $this->url_render('image_hover_first_icon_link', $value) . ' class="oxi-image-icon">' . $this->font_awesome_render($value['image_hover_first_icon']) . '</a>
                        </div>';
            endif;
            if ($value['image_hover_second_icon'] != '') :
                $second = '<div class="oxi-image-hover-icon">
                            <a ' . $this->url_render('image_hover_second_icon_link', $value) . ' class="oxi-image-icon">' . $this->font_awesome_render($value['image_hover_second_icon']) . '</a>
                        </div>';
            endif;
            echo '<div class="oxi-image-hover-style ' . $this->column_render('oxi-image-hover-col', $style) . ' ' . ($admin == "admin" ? 'oxi-addons-admin-edit-list' : '') . '" ' . $this->animation_render('oxi-image-hover-animation', $style) . '>';
            echo '  <div class="oxi-image-hover-style-button">
                        <div class="oxi-image-hover oxi-image-button-hover oxi-image-button-hover-style-7 oxi-image-button-hover-' . $this->oxiid . '-' . $val['id'] . '">
                            <div class="oxi-image-hover-figure ' . $this->style['image_hover_effects'] . '">
                                <div class="oxi-image-hover-image">
                                    <img ' . $this->media_render('image_hover_image', $value) . '>
                                </div>
                                <div class="oxi-image-hover-figure-caption">
                                    <div class="oxi-image-hover-caption-tab ' . $this->style['oxi-image-hover-content-alignment'] . '">
                                        ' . $first . $second . '
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
            if ($admin == 'admin') :
                echo $this->oxi_addons_admin_edit_delete_clone($val['id']);
            endif;
            echo ' </div>';
            if ($this->media_background_render('image_hover_feature_image', $value) != '') :
                $url = $this->media_background_render('image_hover_feature_image', $value);
                $this->inline_css .= '.oxi-image-hover-style-button .oxi-image-button-hover-' . $this->oxiid . '-' . $val['id'] . ' .oxi-image-hover-figure-caption:after{background: url(' . $url . ') !important;-moz-background-size: 100% 100% !important;-o-background-size: 100% 100% !important; background-size: 100% 100% !important;}';
            endif;
        }
    }


    public function old_render()
    {
        $style = $this->dbdata['css'];
        $styledata = explode('|', $style);
        foreach ($this->child as $k => $value) {
            $i = 1;
            $rowdata = [
                'image_hover_title' => 'Image ' . $i,
                'image_hover_first_icon' => $value['data1'],
                'image_hover_first_icon_link-url' => $value['data1link'],
                'image_hover_first_icon_link-target' => $styledata[17] == '_blank' ? 'yes' : '',
                'image_hover_second_icon' => $value['data2'],
                'image_hover_second_icon_link-url' => $value['data2link'],
                'image_hover_second_icon_link-target' => $styledata[17] == '_blank' ? 'yes' : '',
                'image_hover_image-select' => 'media-library',
                'image_hover_image-image' => $value['image'],
                'image_hover_feature_image-select' => 'media-library',
                'image_hover_feature_image-image' => $value['hoverimage'],
            ];
            $dd = json_encode($rowdata);
            $this->wpdb->query($this->wpdb->prepare("UPDATE {$this->child_table} SET rawdata = %s WHERE id = %d", $dd, $value['id']));
            $i++;
        }

        $new = [
            'image-hover-style-id' => $this->oxiid,
            'oxi-image-hover-effects-time-size' => '500',
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
            'oxi-image-hover-height-lap-size' => ($styledata[7] / $styledata[5] * 100),
            'oxi-image-hover-margin-lap-top' => $styledata[9],
            'oxi-image-hover-margin-lap-right' => $styledata[9],
            'oxi-image-hover-margin-lap-bottom' => $styledata[9],
            'oxi-image-hover-margin-lap-left' => $styledata[9],
            'oxi-image-hover-margin-lap-choices' => 'px',
            'oxi-image-hover-content-alignment' =>  'image-hover-align-center-center',
            /////
            'oxi-image-hover-padding-lap-top' => $styledata[11],
            'oxi-image-hover-padding-lap-right' => $styledata[11],
            'oxi-image-hover-padding-lap-bottom' => $styledata[11],
            'oxi-image-hover-padding-lap-left' => $styledata[11],
            'oxi-image-hover-padding-lap-choices' => 'px',
            //////
            'oxi-image-hover-background-color' => $styledata[13],
            'oxi-image-hover-background-img' => '',
            'oxi-image-hover-animation-type' => $styledata[19],
            'oxi-image-hover-animation-duration-size' => $styledata[21] * 1000,
            'oxi-image-hover-boxshadow-shadow' => 'yes',
            'oxi-image-hover-boxshadow-type' => 'inset',
            'oxi-image-hover-boxshadow-horizontal-size' => 0,
            'oxi-image-hover-boxshadow-vertical-size' => 0,
            'oxi-image-hover-boxshadow-blur-size' => 0,
            'oxi-image-hover-boxshadow-spread-size' => $styledata[25],
            'oxi-image-hover-boxshadow-color' => $styledata[27],
            ////
            'oxi-image-hover-icon-width-height-lap-size' => $styledata[97],
            'oxi-image-hover-icon-width-height-lap-choices' => 'px',
            'oxi-image-hover-icon-size-lap-size' => $styledata[85],
            'oxi-image-hover-icon-size-lap-choices' => 'px',
            'oxi-image-hover-icon-color' => $styledata[87],
            'oxi-image-hover-icon-background' => $styledata[89],
            'oxi-image-hover-icon-radius-lap-top' => $styledata[95],
            'oxi-image-hover-icon-radius-lap-right' => $styledata[95],
            'oxi-image-hover-icon-radius-lap-bottom' => $styledata[95],
            'oxi-image-hover-icon-radius-lap-left' => $styledata[95],
            'oxi-image-hover-icon-radius-lap-choices' => 'px',

            'oxi-image-hover-icon-hover-color' => $styledata[91],
            'oxi-image-hover-icon-hover-background' => $styledata[93],
            'oxi-image-hover-icon-margin-lap-top' => 0,
            'oxi-image-hover-icon-margin-lap-right' => $styledata[99],
            'oxi-image-hover-icon-margin-lap-bottom' => 0,
            'oxi-image-hover-icon-margin-lap-left' => 0,
            'oxi-image-hover-icon-margin-lap-choices' => 'px',
            ///
            'image-hover-custom-css' => $styledata[83],
            'image_hover_effects' => $styledata[101],
        ];
        $row = json_encode($new);
        $this->wpdb->query($this->wpdb->prepare("UPDATE {$this->parent_table} SET rawdata = %s WHERE id = %d", $row, $this->oxiid));
        $name = explode('-', $this->dbdata['style_name']);
        $cls = '\OXI_IMAGE_HOVER_PLUGINS\Modules\Button\Admin\Effects' . $name[1];
        $CLASS = new $cls('admin');
        $CLASS->template_css_render($new);
    }
}
