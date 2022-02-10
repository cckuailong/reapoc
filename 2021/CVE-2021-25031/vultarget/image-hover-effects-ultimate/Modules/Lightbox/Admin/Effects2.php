<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Lightbox\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects1
 *
 * @author biplob
 */
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;
use OXI_IMAGE_HOVER_PLUGINS\Modules\Lightbox\Modules as Modules;

class Effects2 extends Modules {

    public function register_general_tabs() {
        $this->start_section_tabs(
                'oxi-image-hover-start-tabs', [
            'condition' => [
                'oxi-image-hover-start-tabs' => 'general-settings',
            ],
                ]
        );
        $this->start_section_devider();
        $this->register_general_style();
        $this->register_image_settings();
        $this->register_icon_settings();
        $this->register_button_settings();
        $this->end_section_devider();
        $this->start_section_devider();
        $this->start_controls_section(
                'shortcode-addons',
                [
                    'label' => esc_html__('Lightbox Settings ', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => true,
                ]
        );

        $this->add_control(
                'oxi_image_light_z_ind',
                $this->style,
                [
                    'label' => __('Z-index', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::NUMBER,
                    'default' => 9999,
                    'loader' => true,
                    'description' => 'Lightbox Z-index Value, Used for Overlapping Contant.',
                ]
        );
        $this->add_control(
                'oxi_image_light_bg_color',
                $this->style,
                [
                    'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'oparetor' => 'RGB',
                    'default' => 'rgba(68, 161, 86,1.00)',
                    'description' => 'Effect Will be show after Save!',
                    'selector' => [
                        '.Oximfp-bg{{WRAPPER}}' => 'background:{{VALUE}};',
                    ],
                    'description' => 'Customize Lightbox Background Color.',
                ]
        );
        $this->add_control(
                'oxi_image_light_cls_clr',
                $this->style,
                [
                    'label' => __('Closing Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '#ffffff',
                    'description' => 'Lightbox Closing Icon Color, Effect Will be show after Save!',
                ]
        );
        $this->add_control(
                'oxi_image_light_pre_clr',
                $this->style,
                [
                    'label' => __('Preloader Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '#ffffff',
                    'description' => 'Lightbox Preloader Color, Effect Will be show after Save!',
                ]
        );

        $this->end_controls_section();

        $this->register_overlay_icon_settings();
        $this->register_overlay_image_settings();
        $this->end_section_devider();
        $this->end_section_tabs();
    }

    public function modal_form_data() {
        echo '<div class="modal-header">
                    <h4 class="modal-title">Image Hover Form</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">';

        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('General Style', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
                ]
        );

        $this->add_group_control(
                'oxi_image_light_box_image_front', $this->style, [
            'label' => esc_html__('Media Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::MEDIA,
            'default' => [
                'type' => 'media-library',
                'link' => 'https://www.oxilabdemos.com/image-hover/wp-content/uploads/2020/01/placeholder.png',
            ],
            'condition' => [
                'oxi_image_light_box_clickable' => 'image',
            ],
                ]
        );
        $this->add_control(
                'oxi_image_light_box_button_icon', $this->style, [
            'label' => esc_html__('Icon', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::ICON,
            'default' => 'fab fa-accusoft',
            'condition' => [
                'oxi_image_light_box_clickable' => 'icon',
            ],
                ]
        );
        $this->add_control(
                'oxi_image_light_box_button_text', $this->style, [
            'label' => esc_html__('Button Text', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => esc_html__('Show Popup', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'selector' => [
                '{{WRAPPER}} .oxi_addons__light_box_{{KEY}} .oxi_addons__button' => '',
            ],
            'condition' => [
                'oxi_image_light_box_clickable' => 'button',
            ],
                ]
        );

        $this->end_controls_section();
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Popup Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
                ]
        );
        $this->add_control(
                'oxi_image_light_box_select_type', $this->style, [
            'label' => esc_html__('Select Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'default' => 'image',
            'loader' => true,
            'options' => [
                'image' => __('Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'video' => __('Video', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
                ]
        );
        $this->add_group_control(
                'oxi_image_light_box_image', $this->style, [
            'label' => esc_html__('Media Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::MEDIA,
            'default' => [
                'type' => 'media-library',
                'link' => 'https://www.oxilabdemos.com/image-hover/wp-content/uploads/2020/01/placeholder.png',
            ],
            'condition' => [
                'oxi_image_light_box_select_type' => 'image',
            ],
                ]
        );
        $this->add_control(
                'oxi_image_light_box_video', $this->style, [
            'label' => esc_html__('Youtube Link', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'placeholder' => 'https://www.youtube.com/watch?v=sEWx6H8gZH8',
            'default' => 'https://www.youtube.com/watch?v=sEWx6H8gZH8',
            'condition' => [
                'oxi_image_light_box_select_type' => 'video',
            ],
                ]
        );

        $this->end_controls_section();

        echo '</div>';
    }

}
