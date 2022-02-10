<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Filter\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects1
 *
 * @author biplob
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Filter\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects2 extends Modules {

    public function modal_form_data() {
        echo '<div class="modal-header">
                    <h4 class="modal-title">Image Hover Form</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">';
        $this->add_control(
                'image_hover_heading', $this->style, [
            'label' => __('Title', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => 'Title 01',
            'placeholder' => 'Title 01',
            'description' => 'Set Title For repeting Your Category Data.'
                ]
        );

        $this->add_control(
                'image_hover_info', $this->style, [
            'label' => __('Image Hover Shortcode', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXTAREA,
            'description' => 'Add Image Hover Shortcode. After saved kindly reload to loading CSS or JS properly '
                ]
        );
        $this->add_responsive_control(
                'category_item_col', $this->style, [
            'label' => __('Category Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'default' => '',
            'options' => [
                '' => __('Same Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'grid_item_width_2' => __('Width 2', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'grid_item_width_3' => __('Width 3', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'grid_item_width_4' => __('Width 4', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
            'description' => 'Select Width range for this Shortcode. '
                ]
        );
        $this->add_control(
                'image_hover_category_select', $this->style, [
            'label' => __('Category Select', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'multiple' => TRUE,
            'options' => $this->allcatrgory,
            'description' => 'Select Category For your Shortcode. '
                ]
        );

        echo '</div>';
    }

}
